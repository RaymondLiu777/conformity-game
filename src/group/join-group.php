<?php
    require '../config/config.php';
    require 'group_util.php';
    if(isset($_GET["group_id"]) && !empty($_GET["group_id"])) {
        joinGroup($_SESSION["user_id"], $_GET["group_id"]);
    }
    header('location: ./group.php');
?>