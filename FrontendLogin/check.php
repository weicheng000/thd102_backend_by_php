<?php
session_start(); // 會話,啟動!

if (isset($_GET['account'])) {
    $account = $_GET['account'];
    if (isset($_SESSION[$account]) && $_SESSION[$account] === 'success') {
        $res = array('login' => "success");
        header('Content-Type: application/json');
        echo json_encode($res);
    } else {
        $res = array('login' => "error");
        header('Content-Type: application/json');
        echo json_encode($res);
    }
} else {
    $res = array('login' => "error");
    header('Content-Type: application/json');
    echo json_encode($res);
}

?>