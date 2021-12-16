<?php
    require '../config/config.php';
    require './question_util.php';



    

    $results = [];
    $logged_in = false;
    //Validate User
    if(isset($_SESSION["logged_in"]) && !empty($_SESSION["logged_in"])) {
        $logged_in = true;
        $user_id = $_SESSION["user_id"];
    }
    $results["logged_in"] = $logged_in;


    //Connect to database
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
    if($mysqli->connect_errno) {
        echo $mysqli->connect_error;
        exit();
    }

    //Get all questions
    $questions_array = getQuestions($mysqli);

    //Collect the distributions of the world
    getGlobalStats($mysqli, $questions_array);


    if($logged_in) {
        //Collect information about specific user
       getUserResponses($mysqli, $user_id, $questions_array);

        //Collect information about user's friends
        getFriendsStats($mysqli, $user_id, $questions_array);

        //Get which groups the user is a part of
        getGroupStats($mysqli, $user_id, $questions_array);
    }
    else {
        //Set all responses to -1
        for($i = 0; $i < sizeof($questions_array); $i++) {
            $questions_array[$i]["response"] = -1;
        }
    }
    $mysqli->close();

    $results["questions"] = $questions_array;
    //Send all questions in JSON format
    echo json_encode($results);
?>