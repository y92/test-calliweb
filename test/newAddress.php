<?php 

include_once __DIR__.'/template.php';

function echoWindowTitle()
{
    echo "Add a new address";
}

function echoPage()
{
    ?>
    <h1>Add a new address</h1>
    <?php
    
    if (!userConnected()) {
        ?>
        <p>You must be connected to add a new address.</p>
        <?php
    }
    else {
        // Tokens are used to prevent bots or users to insert addresses otherwise than by this form
        $token = generateToken();
        $DBMan = DBManager::getInstance();
        $DBMan->insertToken($token);

        $fields = ["prefix", "lastName", "firstName", "email", "phoneNumber", "addrL1", "addrL2", "postalCode", "city", "country"];
        $labels = ["Prefix (Mr, Mrs ...)", "Last name", "First name", "Email", "Phone number", "Address line 1", "Address line 2", "Postal Code", 
                   "City", "Country"];
        $types = ["text", "text", "text", "email", "tel", "text", "text", "text", "text", "text"];
        ?>
        <div class="form">
            <form method="post" id="newAddressForm">
                <?php foreach ($fields as $k=> $v) {
                ?>
                <div class="row">
                    <div class="label">
                        <label for="<?= $id = $v."Input" ?>"><?= $labels[$k] ?></label>
                    </div>
                    <div class="input">
                        <input type="<?= $types[$k] ?>" name="<?= $fields[$k] ?>" id="<?= $id ?>">
                    </div>
                </div>
                <?php
                }
                ?>
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
                        <input type="submit" value="Insert">
                    </div>
                </div>
            </form>
        </div>
        <script src="js/newAddress.js"></script>
        <?php
    }
}