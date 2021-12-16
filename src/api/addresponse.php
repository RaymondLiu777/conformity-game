<?php
    require '../config/config.php';

    //Check to see if a user is logged in
    if(!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
        echo "No current user session";
        exit();
    }

    //response=?&question_id=?
    //Require a response(tinyint), questions_id, users_id
    if(!isset($_POST["response"]) || empty($_POST["response"]) ||
    !isset($_POST["question_id"]) || empty($_POST["question_id"])) {
        echo "Error, missing argument";
        exit();
    }

    $response = $_POST["response"];
    $question_id = $_POST["question_id"];
    $user_id = $_SESSION["user_id"];

    //Connect to database and add/edit response
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
    if($mysqli->connect_errno) {
       echo $mysqli->connect_error;
       exit();
    }

    //Check is question exists
    $question_statement = $mysqli->prepare("SELECT * FROM questions 
                                        WHERE questions.id = ?;");
    $question_statement->bind_param("i", $question_id);
    $executed = $question_statement->execute();
    if(!$executed) {
        echo $mysqli->error;
        exit();
    }

    $question_result = $question_statement->get_result();
    if($question_result->num_rows != 1) {
        echo "Could not find question id";
        exit();
    }
    $question_statement->close();

    //Check if response exists
    $response_statement = $mysqli->prepare("SELECT * FROM responses 
                                        WHERE responses.users_id = ?
                                        AND responses.questions_id = ?;");
    $response_statement->bind_param("ii", $user_id, $question_id);
    $executed = $response_statement->execute();
    if(!$executed) {
        echo $mysqli->error;
        exit();
    }

    $response_result = $response_statement->get_result();
    if($response_result->num_rows == 1) {
        //Already exists response, update it
        $row = $response_result->fetch_assoc();
        if($row["response"] != $response) {
            $change_response_statement = $mysqli->prepare("UPDATE responses
                                                            SET responses.response = ?
                                                            WHERE responses.users_id = ?
                                                            AND responses.questions_id = ?;");
            $change_response_statement->bind_param("iii", $response, $user_id, $question_id);
            $executed = $change_response_statement->execute();
            if(!$executed) {
                echo $mysqli->error;
                exit();
            }
            $change_response_statement->close();
        }
    }
    else {
        //Response doesn't exist, add it
        $add_response_statement = $mysqli->prepare("INSERT INTO responses(response, users_id, questions_id)
                                                    VALUES(?,?,?);");
        $add_response_statement->bind_param("iii", $response, $user_id, $question_id);
        $executed = $add_response_statement->execute();
        if(!$executed) {
            echo $mysqli->error;
            exit();
        }
        $add_response_statement->close();
    }

    $response_statement->close();
    $mysqli->close();

    echo "Success!";

?>