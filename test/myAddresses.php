<?php

include_once __DIR__.'/template.php';

function echoWindowTitle() 
{
    echo "My addresses";
}

function echoPage()
{
    ?>
    <h1>My addresses</h1>
    <?php
    if (!userConnected()) {
        ?>
        <p>You are not connected.</p>
        <?php
    }
    else {
        $DBMan = DBManager::getInstance();
        $userId = getUserIdSession();

        $addresses = $DBMan->getAddressesFromUser($userId);
        $addrArray = $addresses['addresses'] ?? [];


        switch ($addresses['code']) {
            case DBManager::FK_NOT_EXISTS:
                $msg = "Your account has been deleted or is temprarily inaccessible.";
                break;
            case DBManager::DB_ERROR:
                $msg = "Error DB";
                break;
            case DBManager::QUERY_SUCCESS:
                $msg = "These are your addresses";
                break;
            default:
                break;
        }
        ?>
        <p><?= $msg ?></p>
        <div class="buttons"><a class="button" href="newAddress.php">Add a new address</a></div>
        <ul class="addresses">
            <?php
            foreach($addrArray as $v) {
                ?>
                <li class="address">
                    <div class="addressName"><span class="prefix"><?= htmlentities($v['prefix']) ?></span> <span class="lastName"><?= htmlentities($v['lastName']) ?></span> <span class="firstName"><?= htmlentities($v['firstName']) ?></span></div>
                    <div class="addressL1"><?= htmlentities($v['addrL1']) ?></div>
                    <div class="addressL2"><?= htmlentities($v['addrL2']) ?></div>
                    <div class="addressLocation"><span class="postalCode"><?= htmlentities($v['postalCode']) ?></span> <span class="city"><?= htmlentities($v['city']) ?></span></div>
                    <div class="addressLocation"><span class="country"><?= htmlentities($v['country']) ?></span></div>
                    <div class="contact">
                        <div class="phoneNumber"><?= htmlentities($v['phoneNumber']) ?></div>
                        <div class="email"><a href="mailto:<?= $mail=htmlentities($v['email']) ?>"><?= $mail ?></a></div>
                    </div>

                    <div class="addressButtons"><a class="addressButton" href="updateAddress.php?id=<?= $v['id'] ?>">Update</a> <a data-address="<?= $v['id'] ?>" data-name="<?= htmlentities($v['prefix']." ".$v['lastName']." ".$v['firstName'])?>" data-action="deleteAddress" class="addressButton">Delete</a></div>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
        if (!empty($addrArray)) {
            ?>
            <div class="buttons"><a class="button" href="newAddress.php">Add a new address</a></div>
            <div class="buttons"><a class="button" data-action="createCSV">Export as CSV</a> <a id="downloadCSV" class="button" data-action="downloadCSV" style="display: none;">Download CSV</a></div>
            <?php
        }
        ?>
        <script src="js/myAddresses.js"></script>
        <?php
    }
}