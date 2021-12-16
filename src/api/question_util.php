<?php

function getQuestions(&$mysqli) {
    $questions_result = $mysqli->query("SELECT * FROM questions ORDER BY questions.id;");
    if(!$questions_result) {
        echo $mysqli->error;
        exit();
    }

    $questions_array = [];
    while($row = $questions_result->fetch_assoc()) {
        array_push($questions_array, $row);
    }
    return $questions_array;
}

function getGlobalStats(&$mysqli, &$questions_array) {
    //I found this SQL statement off google, it works even though I have no idea why
    $global_result = $mysqli->query("SELECT questions_id, sum(Response1) AS response1, sum(Response2) AS response2
                                    FROM (
                                        SELECT questions_id, 
                                            case response when 1 then 1 else 0 end as Response1,
                                            case response when 2 then 1 else 0 end as Response2
                                        FROM responses
                                    ) E
                                    GROUP BY questions_id
                                    ORDER BY questions_id;");
    if(!$global_result) {
        echo $mysqli->error;
        exit();
    }

    $globals_array = [];
    while($row = $global_result->fetch_assoc()) {
        array_push($globals_array, $row);
    }

    //Merge questions array and global results
    $global_index = 0;
    for($i = 0; $i < sizeof($questions_array); $i++) {
        if($global_index < sizeof($globals_array) && $questions_array[$i]["id"] == $globals_array[$global_index]["questions_id"]) {
            // unset($globals_array[$global_index]["questions_id"]);
            $questions_array[$i]["groups"]["global"]["response1"] = (int)$globals_array[$global_index]["response1"];
            $questions_array[$i]["groups"]["global"]["response2"] = (int)$globals_array[$global_index]["response2"];
            $global_index++;
        }
        else {
            $questions_array[$i]["groups"]["global"]["response1"] = 0;
            $questions_array[$i]["groups"]["global"]["response2"] = 0;
        }
    }
}

function getUserResponses(&$mysqli, $user_id, &$questions_array) {
    $user_responses_statement = $mysqli->prepare("SELECT responses.questions_id, responses.response FROM responses
        WHERE responses.users_id = ?
        ORDER BY responses.questions_id;");
    $user_responses_statement->bind_param("i", $user_id);
    $executed = $user_responses_statement->execute();
    if(!$executed) {
        echo $mysqli->error;
        exit();
    }

    //Get results
    $result = $user_responses_statement->get_result();
    $user_responses_array = [];
    while($row = $result->fetch_assoc()) {
        array_push($user_responses_array, $row);
    }
    $user_responses_statement->close();

    //Merge questions array and user responses
    $responses_index = 0;
    for($i = 0; $i < sizeof($questions_array); $i++) {
        if($responses_index < sizeof($user_responses_array) && $questions_array[$i]["id"] == $user_responses_array[$responses_index]["questions_id"]) {
            $questions_array[$i]["response"] = $user_responses_array[$responses_index]["response"];
            //Reduce global index by 1 if user has already responded
            if($user_responses_array[$responses_index]["response"] == 1) {
                $questions_array[$i]["groups"]["global"]["response1"] -= 1;
            }
            else if($user_responses_array[$responses_index]["response"] == 2) {
                $questions_array[$i]["groups"]["global"]["response2"] -= 1;
            }
            $responses_index++;
        }
        else {
            $questions_array[$i]["response"] = -1;
        }
    }
}


function getFriendsStats(&$mysqli, $user_id, &$questions_array) {
    $friends_statement = $mysqli->prepare("SELECT responses.questions_id, COUNT(*) AS responses FROM responses
                                                JOIN friends ON friends.friend_id = responses.users_id
                                                WHERE friends.users_id = ? AND responses.response = ?
                                                GROUP BY responses.questions_id
                                                ORDER BY responses.questions_id;");

    for($i = 1; $i < 3; $i++) {
        //Bind parameters
        $friends_statement->bind_param("ii", $user_id, $i);
        $executed = $friends_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        //Get results
        $result = $friends_statement->get_result();
        $friend_responses_array = [];
        while($row = $result->fetch_assoc()) {
            array_push($friend_responses_array, $row);
        }

        //Merge with questions array
        $friend_index = 0;
        for($j = 0; $j < sizeof($questions_array); $j++) {
            if($friend_index < sizeof($friend_responses_array) && $questions_array[$j]["id"] == $friend_responses_array[$friend_index]["questions_id"]) {
                $questions_array[$j]["groups"]["friends"]["response" . strval($i)] = $friend_responses_array[$friend_index]["responses"];
                $friend_index++;
            }
            else {
                $questions_array[$j]["groups"]["friends"]["response" . strval($i)] = 0;
            }
        }
    }

    $friends_statement->close();
}

function getGroupStats(&$mysqli, $user_id, &$questions_array) {
    $group_joined_statement = $mysqli->prepare("SELECT groups_has_users.groups_id FROM groups_has_users
                                                    WHERE groups_has_users.users_id = ?;");
    $group_joined_statement->bind_param("i", $user_id);
    $executed = $group_joined_statement->execute();
    if(!$executed) {
        echo $mysqli->error;
        exit();
    }

    $results = $group_joined_statement->get_result();

    $groups_joined = [];
    while($row = $results->fetch_assoc()) {
        array_push($groups_joined, $row["groups_id"]);
    }
    $group_joined_statement->close();
    
    //Collect information about user's groups
    $groups_statement = $mysqli->prepare("SELECT responses.questions_id, COUNT(*) AS responses FROM responses
                                            JOIN groups_has_users ON groups_has_users.users_id = responses.users_id
                                            WHERE groups_has_users.groups_id = ? AND responses.response = ?
                                            AND responses.users_id != ?
                                            GROUP BY responses.questions_id
                                            ORDER BY responses.questions_id;");

    for($group_index = 0; $group_index < sizeof($groups_joined); $group_index++) {
        for($i = 1; $i < 3; $i++) {
            $group_id = $groups_joined[$group_index];
            //Bind parameters
            $groups_statement->bind_param("iii", $group_id, $i, $user_id);
            $executed = $groups_statement->execute();
            if(!$executed) {
                echo $mysqli->error;
                exit();
            }

            //Get results
            $result = $groups_statement->get_result();
            $groups_array = [];
            while($row = $result->fetch_assoc()) {
                array_push($groups_array, $row);
            }

            //Merge with questions array
            $index = 0;
            for($j = 0; $j < sizeof($questions_array); $j++) {
                if($index < sizeof($groups_array) && $questions_array[$j]["id"] == $groups_array[$index]["questions_id"]) {
                    $questions_array[$j]["groups"]["group" . strval($group_id)]["response" . strval($i)] = $groups_array[$index]["responses"];
                    $index++;
                }
                else {
                    $questions_array[$j]["groups"]["group" . strval($group_id)]["response" . strval($i)] = 0;
                }
            }
        }
    }

    $groups_statement->close();
}

?>