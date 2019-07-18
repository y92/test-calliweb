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

    if (!isset($_POST['id'])) {
        $res['msg'] = "No address specified";
        $res['result'] = "error";
    }

    else if (!is_numeric($_POST['id'])) {
        $res['msg'] = "Invalid argument";
        $res['result'] = "error";
    }

    else {

        $addr = $DBMan->getAddress($_POST['id']);
        $addrData = $addr['address'] ?? [];

        if ($addr['code'] == DBManager::NOT_EXISTS) {
            $res['msg'] = "The address you have specified does not exists";
            $res['result'] = "error";
        }
        else if ($addr['code'] == DBManager::QUERY_SUCCESS) {
            if ($addrData['owner'] != $userId) {
                $res['msg'] = "This address is not yours.";
                $res['result'] = "error";
            }
            else {
                $delete = $DBMan->deleteAddress($_POST['id']);

                switch($delete) {
                    case DBManager::QUERY_SUCCESS:
                        $res['msg'] = "Address deleted successfully";
                        $res['result'] = "success";
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
        else {
            $res['msg'] = "Internal error";
            $res['result'] = "error";
        }
    }

}
echo json_encode($res);

?>