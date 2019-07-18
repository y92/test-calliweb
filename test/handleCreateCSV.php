<?php session_start(); ?>

<?php

require_once __DIR__.'/functions.php';
require_once __DIR__.'/classes/DBManager.php';

$res = ['msg' => str_replace(",", ", ",json_encode($_POST)), 'result' => "info"];

if (!userConnected()) {
    $res['msg'] = "You are not connected.";
    $res['result'] = "error";
}

else {

    $userId = getUserIdSession();
    $DBMan = DBManager::getInstance();

    $addr = $DBMan->getAddressesFromUser($userId);
    $addrData = $addr['addresses'] ?? [];

    switch($addr['code']) {
        case DBManager::FK_NOT_EXISTS:
            $res['msg'] = "Your accout has been deleted or is temporarily inaccessible.";
            $res['result'] = "error";
            break;
        case DBManager::DB_ERROR:
            $res['msg'] = "Error DB";
            $res['result'] = "error";
            break;
        case DBManager::QUERY_SUCCESS:
            $csvFileContent = "Prefix;Last name;First name;Email;Phone number;Address line 1;Address line 2;Postal Code;City;Country\n";
            foreach($addrData as $row) {
                $csvFileContent .= $row['prefix'].";".$row['lastName'].";".$row['firstName'].";".$row['email'].";".$row['phoneNumber'].";".$row['addrL1'].";".$row['addrL2'].";";
                $csvFileContent .= $row['postalCode'].";".$row['city'].";".$row['country'];
                $csvFileContent .= "\n";
            }

            $filePath = "tmp/user_$userId"."_addresses_".generateToken().".csv";

            $put = file_put_contents(__DIR__."/$filePath", $csvFileContent);

            if ($put === false) {
                $res['msg'] = "CSV file cannot be created.";
                $res['result'] = "error";
            }
            else {
                $res['msg'] = "CSV file successfully created";
                $res['result'] = "success";
                $res['href'] = $filePath;
            }
            break;
        default:
            $res['msg'] = "Internal error";
            $res['result'] = "error";
            break;
    }

}


echo json_encode($res);

?>