<?php session_start(); ?>

<?php 
require_once __DIR__.'/functions.php'; 
require_once __DIR__.'/classes/DBManager.php';

$DBMan = DBManager::getInstance();

$userId = getUserIdSession();

if ($userId !== null)
{
    if (!$DBMan->existsMemberId($userId)) {
        disconnectUser();
    }
}

unset($DBMan);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php if (function_exists("echoWindowTitle")) { echoWindowTitle(); } ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <script src="js/jquery-3.4.1.js"></script>
    </head>
    <body>
        <header class="topMenu">
            <nav class="topMenuLinks">
                <a href=".">Index</a>
                <?php
                    if (userConnected()) {
                        echo "<a href=\"myAddresses.php\">My addresses</a> ";
                        echo "<a href=\"logout.php\">Logout</a>";
                    }
                    else {
                        echo "<a href=\"register.php\">Register</a> ";
                        echo "<a href=\"login.php\">Login</a>";
                    }
                ?>
            </nav>
        </header>
        <?php
            if (function_exists("echoPage")) {
                echoPage();
            }
        ?>
    </body>
</html>