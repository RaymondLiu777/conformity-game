<?php
    require '../config/config.php';
    require 'friend_util.php';
    // var_dump($_SESSION);
    // echo "<br>";
    // var_dump($_POST);

    //Redirect if not logged in
    if(!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
        header('location: ../home/home.php');
        exit();
    }

    //Add friends
    if(isset($_POST["action"]) && !empty($_POST["action"])){
        if($_POST["action"] == "add-friend") {
            addFriend($_POST["friend_username"], $_POST["user_id"]);
        }
        else {
            $error = "Error, unknown action";
        }
    }

    //Get Friends
    $friends = getFriends($_SESSION["user_id"]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles.css"/>
    <title>Friends</title>
</head>
<body>
    
    <?php include '../navbar/nav.php'; ?>

    <div class="main">
        <div class="title text-center">Friends List</div>
        <div id="friends">
            <div class="container-fluid">
                <div class="row">

                    <?php for($i = 0; $i < sizeof($friends); $i++) : ?>
                        <div class="col col-12 col-sm-6 col-md-4">
                            <div class="friend-row text-center">
                                
                                <p><?php echo $friends[$i]["username"]; ?></p>
                                <div>
                                    <a class="btn btn-primary blue-btn" href="remove_friend.php?friend_id=<?php echo $friends[$i]["id"]; ?>">Remove Friend</a>
                                </div>
                            </div>
                        </div>
                    <?php endfor ?>
                </div>
            </div>
        </div>
        <div class="spacer-small"></div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                    <form id="add-friend" action="friend.php" method="POST">
                        <div class="form-group login-form-group text-center">
                            <label for="form-friend" class="blue-color text-center">Username:</label>
                            <input type="text" id="form-friend" class="form-control" name="friend_username"/>
                            <div class="invalid-feedback">
                                Field Cannot be Empty
                            </div>
                            <input type="hidden" id="form-user-hidden" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>"/>
                        </div>
                        <div class="text-center mb-3">
                            <span id="error" class="error-message">
                                <?php 
                                    if(isset($error) && !empty($error)) {
                                        echo $error;
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="action" value="add-friend" class="btn btn-primary btn-block w-50 blue-btn">Add Friend</button>
                        </div>
                    </form>
                </div>  
            </div>  
        </div>
    </div>

    <div class="footer">

    </div>

    <script src="friend.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>