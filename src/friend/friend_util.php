<?php

    function addFriend($friend_username, $user_id) {
        //Validate info
        if(!isset($friend_username) || empty($friend_username)) {
            $GLOBALS['error'] = "Missing Input";
            return;
        }
        if(!isset($user_id) ||empty($user_id)) {
            $GLOBALS['error'] = "Error in adding friend";
            return;
        }
 
        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }
 
        //Check if friend exists
        $user_statement = $mysqli->prepare("SELECT * FROM users WHERE users.username = ?;");
        $user_statement->bind_param("s", $friend_username);
        $executed = $user_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $result = $user_statement->get_result();
        if($result->num_rows > 1) {
            $GLOBALS['error'] = "Database Error";
            return;
        }
        else if($result->num_rows < 1) {
            $GLOBALS['error'] = "Could not find friend";
            return;
        }

        //Get friend's id
        $row = $result->fetch_assoc();
        $friend_id = $row["id"];
        $user_statement->close();

        if($friend_id == $user_id) {
            return;
        }

        //Check if you are already friends
        $check_friend_statement = $mysqli->prepare("SELECT * FROM friends 
                                                    WHERE friends.users_id = ? AND friends.friend_id = ?;");
        $check_friend_statement->bind_param("ii", $user_id, $friend_id);
        $executed = $check_friend_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $check_friend_statement->store_result();
        if($check_friend_statement->num_rows > 0) {
            $GLOBALS['error'] = "Already added friend";
            return;
        }
        $check_friend_statement->close();

        //Add friend
        $add_friend_statement = $mysqli->prepare("INSERT INTO friends(users_id, friend_id) VALUES(?,?)");
        $add_friend_statement->bind_param("ii", $user_id, $friend_id);
        $executed = $add_friend_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $add_friend_statement->store_result();
        if($add_friend_statement->affected_rows != 1) {
            $GLOBALS['error'] = "Updated " . $add_friend_statement->affected_rows . " rows";
        }
        $add_friend_statement->close();
        $mysqli->close();
    }

    function getFriends($user_id) {
        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }

        //Get all people
        $friend_statement = $mysqli->prepare("SELECT users.id, users.username FROM friends
                                            JOIN users ON friends.friend_id = users.id
                                            WHERE friends.users_id = ?;");
        $friend_statement->bind_param("i", $user_id);
        $executed = $friend_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $results = $friend_statement->get_result();

        $results_array = [];
        while($row = $results->fetch_assoc()) {
            array_push($results_array, $row);
        }

        $friend_statement->close();
        $mysqli->close();

        return $results_array;
    }

    function removeFriend($user_id, $friend_id) {
        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }
 
        //Remove friend
        $friend_statement = $mysqli->prepare("DELETE FROM friends
                                                WHERE friends.users_id = ? && friends.friend_id = ?;");
        $friend_statement->bind_param("ii", $user_id, $friend_id);
        $executed = $friend_statement->execute();

        $friend_statement->close();
        $mysqli->close();
    }
?>