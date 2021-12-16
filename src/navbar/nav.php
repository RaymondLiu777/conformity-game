<div id="navbar" class="navbar navbar-dark navbar-expand-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="../home/home.php">Conformity Quiz</a>
        
        
        <?php if(!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) : ?>

            <!-- Not logged in -->
            <div class="navbar-nav">
                <a class="navbar-link nav-link" href="../login/login.php">Login</a>
            </div>

        <?php else : ?>

            <!-- Logged in -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <div class="navbar-nav container-fluid">
                    <div class="col-12 col-sm-4">
                        <a class="navbar-link nav-link" href="../friend/friend.php">Friends</a>
                    </div>
                    <div class="col-12 col-sm-4">
                        <a class="navbar-link nav-link" href="../group/group.php">Groups</a>
                    </div>
                    <div class="col-12 col-sm-4">
                        <a class="navbar-link nav-link" href="../login/logout.php">Logout</a>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) : ?>
    <div id="welcome-message" class="container-fluid">
        <div class="row text-center">
            <span>Welcome Back <?php echo $_SESSION["username"]; ?>!</span>
        </div>
    </div>
<?php endif; ?>