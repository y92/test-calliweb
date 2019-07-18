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

    if (!isset($_POST['token'])) {
        $res['msg'] = "Token is not specified";
        $res['result'] = "error";
    }

    else if (!$DBMan->isValidToken($_POST['token'])) {
        $res['msg'] = "Token is invalid or has expired";
        $res['result'] = "error";
    }

    else {
        $insert = $DBMan->insertAddress($userId, $_POST['prefix'] ?? null, $_POST['lastName'] ?? null, $_POST['firstName'] ?? null, $_POST['email'] ?? null, $_POST['phoneNumber'] ?? null, $_POST['addrL1'] ?? null, $_POST['addrL2'] ?? null, $_POST['postalCode'] ?? null, $_POST['city'] ?? null, $_POST['country'] ?? null);

        switch($insert) {
            case DBManager::FK_NOT_EXISTS:
                $res['msg'] = "Your account has been removed or is temporarily inaccessible.";
                $res['result'] = "error";
                break;
            case DBManager::INVALID_ARGUMENT:
                $res['msg'] = "Email is invalid.";
                $res['result'] = "error";
                break;
            case DBManager::QUERY_SUCCESS:
                $res['msg'] = "Address inserted successfully";
                $res['result'] = "success";
                $DBMan->deleteToken($_POST['token']);
                break;
            case DBManager::DB_ERROR:
                $res['msg'] = "Error DB";
                $res['result'] = "error";
                break;
            default:
                $res['msg'] = "Internal error";
                $res['result'] = "error";
                break;
        }
    }

}
echo json_encode($res);

?>