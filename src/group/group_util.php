<?php

    function createGroup($group_name, $user_id) {
        //Validate info
        if(!isset($group_name) || empty($group_name)) {
            $GLOBALS['error'] = "Missing Input";
            return;
        }
        if(!isset($user_id) ||empty($user_id)) {
            $GLOBALS['error'] = "Error in creating group";
            return;
        }
 
        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }
 
        //Check if group exists
        $group_statement = $mysqli->prepare("SELECT * FROM groups WHERE groups.name = ?;");
        $group_statement->bind_param("s", $group_name);
        $executed = $group_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $result = $group_statement->get_result();
        if($result->num_rows > 0) {
            $GLOBALS['error'] = "Group already exists";
            return;
        }

        $group_statement->close();

        //Create Group
        $create_group_statement = $mysqli->prepare("INSERT INTO groups(name, owner_id, private) VALUES(?,?,FALSE)");
        $create_group_statement->bind_param("si", $group_name, $user_id);
        $executed = $create_group_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $create_group_statement->store_result();
        if($create_group_statement->affected_rows != 1) {
            $GLOBALS['error'] = "Updated " . $create_group_statement->affected_rows . " rows";
        }
        $create_group_statement->close();

        //Get group id
        $get_group_statement = $mysqli->prepare("SELECT * FROM groups WHERE groups.name = ?;");
        $get_group_statement->bind_param("s", $group_name);
        $executed = $get_group_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $result = $get_group_statement->get_result();
        if($result->num_rows != 1) {
            $GLOBALS['error'] = "Database Error :(";
            return;
        }

        //Get group's id
        $row = $result->fetch_assoc();
        $group_id = $row["id"];
        $get_group_statement->close();

        //Add self to group
        $add_self_statement = $mysqli->prepare("INSERT INTO groups_has_users(groups_id, users_id) VALUES(?,?)");
        $add_self_statement->bind_param("ii", $group_id, $user_id);
        $executed = $add_self_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }
        $add_self_statement->close();

        $mysqli->close();
    }


    function getGroups($user_id) {

        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }

        //Get groups that user is a part of
        $group_joined_statement = $mysqli->prepare("SELECT groups_has_users.groups_id FROM groups_has_users
                                                    WHERE groups_has_users.users_id = ?;");
        $group_joined_statement->bind_param("i", $user_id);
        $executed = $group_joined_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $results = $group_joined_statement->get_result();

        $results_array_joined = [];
        while($row = $results->fetch_assoc()) {
            array_push($results_array_joined, $row["groups_id"]);
        }

        $group_joined_statement->close();

        //Get all groups
        $group_statement = $mysqli->prepare("SELECT groups.id AS id, groups.name, users.username AS owner, groups.private FROM groups
                                            JOIN users ON groups.owner_id = users.id
                                            WHERE groups.private = FALSE;");
        $executed = $group_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $results = $group_statement->get_result();

        $results_array_all = [];
        while($row = $results->fetch_assoc()) {
            array_push($results_array_all, $row);
        }

        $group_statement->close();
        $mysqli->close();

        return ["joined" => $results_array_joined, "all" => $results_array_all];

    }

    function joinGroup($user_id, $group_id) {
        if(!isset($user_id) || empty($user_id) || !isset($group_id) || empty($group_id) ) {
            return;
        }

        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }

        //Add user to group
        $join_statement = $mysqli->prepare("INSERT INTO groups_has_users(groups_id, users_id)
                                                    VALUES(?,?);");
        $join_statement->bind_param("ii", $group_id, $user_id);
        $executed = $join_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $join_statement->close();
        $mysqli->close();
    }

    function leaveGroup($user_id, $group_id) {
        if(!isset($user_id) || empty($user_id) || !isset($group_id) || empty($group_id) ) {
            return;
        }

        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }

        //Add user to group
        $join_statement = $mysqli->prepare("DELETE FROM groups_has_users
                                            WHERE groups_id = ? AND users_id = ?;");
        $join_statement->bind_param("ii", $group_id, $user_id);
        $executed = $join_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        $join_statement->close();
        $mysqli->close();
    }
?>