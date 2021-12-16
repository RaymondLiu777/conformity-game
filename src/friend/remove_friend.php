<?php
    require '../config/config.php';
    require 'friend_util.php';
    if(isset($_GET["friend_id"]) && !empty($_GET["friend_id"])) {
            removeFriend($_SESSION["user_id"], $_GET["friend_id"]);
    }
    header('location: ./friend.php');
?>