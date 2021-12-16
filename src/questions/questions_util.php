<?php

    function getGroups($user_id) {
        if(!isset($user_id) || empty($user_id)) {
            return;
        }

        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }

        //Get groups that user is a part of
        $group_joined_statement = $mysqli->prepare("SELECT groups.id AS group_id, groups.name FROM groups_has_users
                                                    JOIN groups ON groups_has_users.groups_id = groups.id
                                                    WHERE groups_has_users.users_id = ?;");
        $group_joined_statement->bind_param("i", $user_id);
        $executed = $group_joined_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $results = $group_joined_statement->get_result();

        $results_array = [];
        while($row = $results->fetch_assoc()) {
            array_push($results_array, $row);
        }

        $group_joined_statement->close();
        $mysqli->close();

        return $results_array;
    }





?>