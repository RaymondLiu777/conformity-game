<?php
    require '../config/config.php';
    require 'group_util.php';

    //Redirect if not logged in
    if(!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
        header('location: ../home/home.php');
        exit();
    }

    //Add groups
    if(isset($_POST["action"]) && !empty($_POST["action"])){
        if($_POST["action"] == "create-group") {
            createGroup($_POST["group_name"], $_POST["user_id"]);
        }
        else {
            $error = "Error, unknown action";
        }
    }

    $groups = getGroups($_SESSION["user_id"]);
    // var_dump($groups);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles.css"/>
    <title>Groups</title>
</head>
<body>

    <?php include '../navbar/nav.php'; ?>

    <div class="main">
        <div class="title text-center">Groups List</div>
        <div id="groups">
            <div class="container-fluid">
                <div class="row">
                    <?php for($i = 0; $i < sizeof($groups["all"]); $i++) : ?>
                        <div class="col col-12 col-sm-6">
                            <div id="accordion">
                                <div class="card text-white text-center blue-bg group-row">
                                    <div class="card-header" id="headingOne">
                                        <h5 class="mb-0">
                                            <button class="btn group-btn collapsed" data-bs-toggle="collapse" data-bs-target="#<?php echo str_replace(' ', '', $groups["all"][$i]["name"]);?>">
                                                <?php echo $groups["all"][$i]["name"]; ?>
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="<?php echo str_replace(' ', '', $groups["all"][$i]["name"]);?>" class="collapse" data-bs-parent="#accordion">
                                        <div class="card-body">
                                            <p class="mb-3">Created By: <?php echo $groups["all"][$i]["owner"]; ?></p>
                                            <?php if(in_array($groups["all"][$i]["id"], $groups["joined"])) : ?>
                                                <a href="leave-group.php?group_id=<?php echo $groups["all"][$i]["id"]; ?>" class="btn btn-danger">Leave Group</a>
                                            <?php else : ?>
                                                <a href="join-group.php?group_id=<?php echo $groups["all"][$i]["id"]; ?>" class="btn btn-primary">Join Group</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>

                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                    <form id="add-group" action="group.php" method="POST">
                        <div class="form-group login-form-group text-center">
                            <label for="form-group" class="blue-color text-center">Group Name:</label>
                            <input type="text" id="form-group" class="form-control" name="group_name"/>
                            <input type="hidden" id="form-user-hidden" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>"/>
                            <div class="invalid-feedback">
                                Field Cannot be Empty
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="form-check col col-auto">
                                <input class="form-check-input" type="checkbox" value="private" name="private" disabled>
                                <label class="form-check-label" for="defaultCheck1">
                                    Private (coming soon)
                                </label>
                            </div>
                        </div>

                        <div class="text-center">
                            <span id="error" class="error-message">
                                <?php 
                                    if(isset($error) && !empty($error)) {
                                        echo $error;
                                    }
                                ?>
                            </span>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="action" value="create-group" class="btn btn-primary btn-block w-50 blue-btn">Create Group</button>
                        </div>
                        
                    </form>
                </div>  
            </div>  
        </div>
        <div class="spacer"></div>
    </div>

    <div class="footer">
        
    </div>
    

    <script src="group.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>