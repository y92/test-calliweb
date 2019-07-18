<?php
include_once __DIR__.'/template.php';

function echoWindowTitle()
{
    echo "Login";
}

function echoPage()
{
    ?>
    <h1>Login</h1>
    <?php
    if (userConnected()) {
        ?>
        <p>You are already connected</p>
        <?php
    }
    else {
        // Token are used to prevent bots or users to login otherwise than by this form
        $token = generateToken();
        $DBMan = DBManager::getInstance();
        $DBMan->insertToken($token);
        ?>
        <div class="form">
            <form method="post" id="loginForm">
                <div class="row">
                    <div class="label">
                        <label for="emailInput">Your email</label>
                    </div>
                    <div class="input">
                        <input type="email" name="email" id="emailInput" required>
                    </div>
                </div>
                <div class="row">
                    <div class="label">
                        <label for="pwdInput">Your password</label>
                    </div>
                    <div class="input">
                        <input type="password" name="pwd" id="pwdInput" oncut="return false;" oncopy="return false;" onpaste="return false;" required>
                    </div>
                </div>
                <div class="row tokenInput">
                    <div class="input">
                        <input type="hidden" name="token" value="<?= $token ?>">
                    </div>
                </div>
                <div class="row result">
                    <div class="formResult info" id="message"></div>
                </div>
                <div class="row">
                    <div class="input">
                        <input type="submit" value="Login">
                    </div>
                </div>
            </form>
        </div>
        <script src="js/login.js"></script>
        <?php
    }

}