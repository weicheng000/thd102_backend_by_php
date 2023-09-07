<?php
session_start();
if (isset($_GET['account'])) {
    $account = $_GET['account'];
    unset($_SESSION[$account]);
    $res = array('logout' => "success");
    header('Content-Type: application/json');
    echo json_encode($res);
} else {
    $res = array('logout' => "error");
    header('Content-Type: application/json');
    echo json_encode($res);
}
?>
