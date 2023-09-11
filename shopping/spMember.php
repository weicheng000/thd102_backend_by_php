<?php
include("../Conn.php");

// 從前端 JavaScript 獲取 'account'
$email = $_GET['account'];

$sql = "SELECT
            ID,
            `NAME`,
            EMAIL,
            PHONE,
            `STATUS`,
            POINTS
        FROM petpago.MEMBER
        WHERE EMAIL = :EMAIL;";

// 資料庫查詢並取值
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':EMAIL', $email);
$stmt->execute();
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if ($member !== false) {
    $response = array(
        'login' => 'success',
        'id' => $member['ID'],
        'name' => $member['NAME'],
        'email' => $member['EMAIL'],
        'phone' => $member['PHONE'],
        'status' => $member['STATUS'],
        'havePoints' => $member['POINTS']
    );
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = array('login' => 'error');
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
