<?php
    require '../config/config.php';
    require './userauth.php';
    
    //User is already logged in
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        header('Location: ../home/home.php');
        exit();
    }

    //Check if everything is set
    if(isset($_POST['action']) && !empty($_POST['action'])) 
    {
        // var_dump($_POST); //DEBUG
        if($_POST['action'] == 'login') {
            loginUser($_POST['username'], $_POST['password']);
        }
        else if($_POST['action'] == 'register') {
            registerUser($_POST['username'], $_POST['password']);
            if(!isset($error) || empty($error)) {
                loginUser($_POST['username'], $_POST['password']);
            }
        }
        else {
            $error = "Invalid action";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles.css"/>
    <title>Conformity Quiz Login</title>
</head>
<body>
    
    <?php include '../navbar/nav.php'; ?>

    <div class="main">
        <div class="spacer"></div>

        <div id="login-box" class="col-11 col-auto text-center">
            <p id="login-text">Login</p>
            <form id="login-form" action="login.php" method="POST" >
                <div class="form-group login-form-group">
                    <label for="form-username" class="blue-color">Username</label>
                    <input type="text" id="form-username" class="form-control" name="username"/>
                    <div id="username-error" class="invalid-feedback">
                        Please Enter Username
                    </div>
                </div>
                <div class="form-group login-form-group">
                    <label for="form-password" class="blue-color">Password</label>
                    <input type="password" id="form-password" class="form-control" name="password"/>
                    <div class="invalid-feedback">
                        Please Enter Password
                    </div>
                </div>
                <div class="error-message"> 
                    <?php 
                        if(isset($error) && !empty($error)) {
                            echo $error;
                        }
                    ?>
                </div>
                <div class="success-message"> 
                    <?php 
                        if(isset($success) && !empty($success)) {
                            echo $success;
                        }
                    ?>
                </div>
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-12 col-sm-6 form-btn-1 js-remove">
                            <button id="login-btn" type="submit" name="action" value="login" class="btn btn-primary btn-block w-100 blue-btn">Login</button>
                        </div>
                        <div class="col col-12 col-sm-6 form-btn-2">   
                            <button id="register-btn" type="submit" name="action" value="register" class="btn btn-primary btn-block w-100 blue-btn">Register</button>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
        
        <div class="spacer"></div>
    </div>


    <div class="footer">

    </div>

    <script src="login.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>