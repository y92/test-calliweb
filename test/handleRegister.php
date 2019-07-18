<?php session_start(); ?>

<?php 

require_once __DIR__.'/functions.php'; 
require_once __DIR__.'/classes/DBManager.php';

$res = ['result' => "info"];

if (userConnected()) {
    $res['msg'] = "You are already connected.";
    $res['result'] = "error";
}

else {
    try {
        $DBMan = DBManager::getInstance();

        if (!isset($_POST['token'])) {
            $res['msg'] = "Token is not specified";
            $res['result'] = "error";
        }

        else if (!$DBMan->isValidToken($_POST['token'])) {
            $res['msg'] = "Token is invalid or has expired";
            $res['result'] = "error";
        }

        else if (!isset($_POST['email'])) {
            $res['msg'] = "Email is not specified";
            $res['result'] = "error";
        }

        else if ($DBMan->existsMember($_POST['email'])) {
            $res['msg'] = "Email already used";
            $res['result'] = "error";
        }

        else if (!isset($_POST['pwd1']) || !isset($_POST['pwd2']) || strlen($_POST['pwd1']) < 1) {
            $res['msg'] = "Password is not specified";
            $res['result'] = "error";
        }

        else if ($_POST['pwd1'] != $_POST['pwd2']) {
            $res['msg'] = "Passwords must be identical.";
            $res['result'] = "error";
        }

        else {
            $insert = $DBMan->insertMember($_POST['email'], $_POST['pwd1']);

            switch($insert) {
                case DBManager::INVALID_ARGUMENT:
                    $res['msg'] = "Invalid email";
                    $res['result'] = "error";
                    break;
                case DBManager::DB_ERROR:
                    $res['msg'] = "Error DB";
                    $res['result'] = "error";
                    break;
                case DBManager::QUERY_SUCCESS:
                    $res['msg'] = "Registration done successfully";
                    $res['result'] = "success";
                    $DBMan->deleteToken($_POST['token']);
                    break;
                default:
                    $res['msg'] = "Internal error";
                    $res['result'] = "error";
                    break;
            }
        }

    }
    catch(PDOException $e) {
        $res['msg'] = "Cannot connect to DB";
        $res['result'] = "error";
    }
}

echo json_encode($res);
?>
