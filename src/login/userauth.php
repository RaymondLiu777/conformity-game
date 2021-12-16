<?php

    function registerUser($username, $password) {
        //Validate info
        if(!isset($username) || empty($username)) {
           $GLOBALS['error'] = "Missing Username";
           return;
        }
        if(!isset($password) || empty($password)) {
            $GLOBALS['error'] = "Missing Password";
            return;
        }
        

        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }

        //Check if username already exists
        $user_statement = $mysqli->prepare("SELECT * FROM users WHERE users.username = ?;");
        $user_statement->bind_param("s", $username);
        $executed = $user_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        //Already exists a user with that name
        $user_statement->store_result();
        if($user_statement->num_rows > 0) {
            $GLOBALS['error'] = "There is already an account with that username";
            return;
        }
        $user_statement->close();

        //Create new user
        $register_statement = $mysqli->prepare("INSERT INTO users (username, password, salt, is_admin) VALUES(?, ?, ?, FALSE);");
        $salt = rand(0, 4000000000);
        $saltedpassword = $password . $salt;
        $passwordhash = hash("sha256", $saltedpassword);
        $register_statement->bind_param("ssi", $username, $passwordhash, $salt);
        $executed = $register_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }
        $register_statement->store_result();
        if($register_statement->affected_rows != 1) {
            $GLOBALS['error'] = "Updated " . $register_statement->affected_rows . " rows";
        }
        else {
            $GLOBALS['success'] = "Successfully Registered User: " . $username;
        }
        $register_statement->close();
        $mysqli->close();
    }

    function loginUser($username, $password) {
        //Validate info
        if(!isset($username) || empty($username)) {
           $GLOBALS['error'] = "Missing Username";
           return;
        }
        if(!isset($password) ||empty($password)) {
            $GLOBALS['error'] = "Missing Password";
            return;
        }

        //Connect to database
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($mysqli->connect_errno) {
            echo $mysqli->connect_error;
            exit();
        }

        //Look for user entry
        $user_statement = $mysqli->prepare("SELECT * FROM users WHERE users.username = ?;");
        $user_statement->bind_param("s", $username);
        $executed = $user_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }

        //Should be 1 result
        $result = $user_statement->get_result();
        if($result->num_rows < 1) {
            $GLOBALS['error'] = "There is no account with that username, please double check capitalization";
            return;
        }
        else if($result->num_rows > 1) {
            $GLOBALS['error'] = "Database Error";
            return;
        }

        //Check password
        $row = $result->fetch_assoc();
        $saltedpassword = $password . $row["salt"];
        $passwordhash = hash("sha256", $saltedpassword);
        if($passwordhash != $row["password"]) {
            $GLOBALS['error'] = "Incorrect Password";
            return;
        }

        //Session Variables
        $_SESSION["logged_in"] = true;
        $_SESSION["username"] = $username;
        $_SESSION["password"] = $passwordhash;
        $_SESSION["is_admin"] = $row['is_admin'];
        $_SESSION["user_id"] = $row['id'];

        header('Location: ../home/home.php');

        $user_statement->close();
    }
?>