<?php

include_once __DIR__.'/template.php';

function echoWindowTitle()
{
    echo "Register";
}

function echoPage()
{
    ?>
    <h1>Register</h1>
    <?php
    if (userConnected()) {
        ?>
        <p>You are already connected</p>
        <?php
    }
    else {
        // Token are used to prevent bots or users to register otherwise than by this form
        $token = generateToken();
        $DBMan = DBManager::getInstance();
        $DBMan->insertToken($token);
        ?>
        <div class="form">
            <form method="post" id="registerForm">
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
                        <label for="pwdInput1">Your password</label>
                    </div>
                    <div class="input">
                        <input type="password" name="pwd1" id="pwdInput1" oncut="return false;" oncopy="return false;" onpaste="return false;" required>
                    </div>
                </div>
                <div class="row">
                    <div class="label">
                        <label for="pwdInput2">Confirm your password</label>
                    </div>
                    <div class="input">
                        <input type="password" name="pwd2" id="pwdInput2" oncut="return false;" oncopy="return false;" onpaste="return false;" required>
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
                        <input type="submit" value="Register">
                    </div>
                </div>
            </form>
        </div>
        <script src="js/register.js"></script>
        <?php
    }

}