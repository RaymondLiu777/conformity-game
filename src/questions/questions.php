<?php
    require '../config/config.php';
    require './questions_util.php';

    //Load comparison groups if logged in
    if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) {
        $groups = getGroups($_SESSION["user_id"]);
        // var_dump($groups);
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
    <link rel="stylesheet" href="./transitions.css"/>

    <title>Conformity Quiz</title>
</head>
<body>
    <!-- Navbar -->
    <?php include '../navbar/nav.php'; ?>

    <!-- Questions -->
    <div id="questions" class="main fluid-container">
        <div id="transition-box">
            <div class="row gx-0 justify-content-center text-center">
                <span id="question">Are you a dog person or a cat person?</span>
            </div>
            <div class="spacer"></div> 
            <div id="answers">
                <div class="row gx-0 justify-content-center">
                    <div class="col-12 col-sm-5 col-lg-4 text-center">
                        <button id="btn-choice-1" type="button" class="btn btn-lg btn-primary con-btn blue-btn w-100 h-100">Cat</button>
                    </div>
                    <div class="col col-auto vertical-align px-3">
                        <span id="or">
                            OR
                        </span>
                    </div>
                    <div class="col-12 col-sm-5 col-lg-4 text-center">
                        <button id="btn-choice-2" type="button" class="btn btn-lg btn-warning con-btn yellow-btn w-100 h-100">Dog</button>
                    </div>
                </div>
            </div>
            <div class="spacer"></div> 
            <div class="row gx-0 justify-content-center">
                <div id="conformity-bar">
                    <div class="progress">
                        <div id="option-1-bar" class="progress-bar text-size-20 blue-bar" role="progressbar" style="width: 50%">50%</div>
                        <div id="option-2-bar" class="progress-bar text-size-20 yellow-bar" role="progressbar" style="width: 50%">50%</div>
                    </div>
                </div>
            </div>
            <div class="row gx-0 justify-content-center"> 
                <div class="col col-auto text-white"> 
                    Data from <span id="response-count">1</span> response(s)
                </div>
            </div>
        </div>

        <?php if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) : ?>
            <div class="row gx-0 justify-content-center">
                <div class="col col-12 col-sm-3 vertical-align">
                    <span id="comparison-group">Comparison Group:</span>
                </div>
                <div class="col col-12 col-sm-3">
                    <div id="group-selection">
                        <select id="form-group-selection" class="form-select">
                            <option value="-1" selected>World</option>
                            <option value="0">Friends</option>
                            <?php for($i = 0; $i < sizeof($groups); $i++) :?>
                                <option value="<?php echo $groups[$i]["group_id"]; ?>">
                                    <?php echo $groups[$i]["name"]; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="row gx-0 justify-content-center text-white mt-2">
                Login to save your results!
            </div>
        <?php endif; ?>
    </div>
    <div class="footer">
        <div class="spacer"></div>
        <div class="fluid-container overflow-hidden">
            <div class="row justify-content-center">
                <div class="col col-auto text-center">
                    <button id="btn-prev-question" type="button" class="btn btn-lg btn-primary">Prev</button>
                </div>
                <div class="col col-auto text-center">
                    <button id="btn-random-question" type="button" class="btn btn-lg btn-primary">Random</button>
                </div>
                <div class="col col-auto text-center">
                    <button id="btn-next-question" type="button" class="btn btn-lg btn-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <p>
            Want to add your own questions? <a href="#" onclick="alert('Feature Coming Soon');">Click Here</a>
        </p>
    </div>
    
    <script src="questions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>

    </script>
</body>
</html>