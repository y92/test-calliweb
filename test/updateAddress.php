<?php 

include_once __DIR__.'/template.php';

function echoWindowTitle()
{
    echo "Update an address";
}

function echoPage()
{
    ?>
    <h1>Update an address</h1>
    <?php
    if (!userConnected()) {
        ?>
        <p>You must be connected to update your addresses.</p>
        <?php
    }
    else if (!isset($_GET['id'])) {
        ?>
        <p>You must select an address.</p>
        <?php
    }
    else if (!is_numeric($_GET['id'])) {
        ?>
        <p>Invalid argument specified</p>
        <?php
    }
    else {
        $DBMan = DBManager::getInstance();

        $addr = $DBMan->getAddress($_GET['id']);
        $addrData = $addr['address'] ?? [];

        $displayForm = false;

        switch($addr['code']) {
            case DBManager::NOT_EXISTS:
                $msg = "The address you have selected does not exist.";
                break;
            case DBManager::DB_ERROR:
                $msg = "Error DB";
                break;
            case DBManager::QUERY_SUCCESS:
                if ($addrData['owner'] != getUserIdSession()) {
                    $msg = "This address is not yours.";
                }
                else {
                    $msg = "You can update your addresses.";
                    $displayForm = true;
                }
                break;
            default:
                $msg = "Internal error";
                break;
        }

        ?>
        <p><?= $msg ?></p>
        <?php
        if ($displayForm) {

            // Tokens are used to prevent bots or users to update addresses otherwise than by this form
            $token = generateToken();
            $DBMan->insertToken($token);

            $fields = ["prefix", "lastName", "firstName", "email", "phoneNumber", "addrL1", "addrL2", "postalCode", "city", "country"];
            $labels = ["Prefix (Mr, Mrs ...)", "Last name", "First name", "Email", "Phone number", "Address line 1", "Address line 2", "Postal Code", 
                    "City", "Country"];
            $types = ["text", "text", "text", "email", "tel", "text", "text", "text", "text", "text"];
            ?>
            <div class="form">
                <form method="post" id="updateAddressForm">
                    <div class="row" style="display: none;">
                        <div class="input">
                            <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
                        </div>
                    </div>
                    <?php
                    foreach ($fields as $k=> $v) {
                        ?>
                        <div class="row">
                            <div class="label">
                                <label for="<?= $id = $v."Input" ?>"><?= $labels[$k] ?></label>
                            </div>
                            <div class="input">
                                <input type="<?= $types[$k] ?>" name="<?= $fields[$k] ?>" id="<?= $id ?>" value="<?= htmlentities($addrData[$v]) ?>">
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
                            <input type="submit" value="Update">
                        </div>
                    </div>
                </form>
            </div>
            <script src="js/updateAddress.js"></script>
            <?php
        }
    }
}