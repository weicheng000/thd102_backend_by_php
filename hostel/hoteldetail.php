<?php
include '../Conn.php';

$token = $_GET['hotelid'];

$query = "SELECT HOTELNAME AS 'hostelName', HOTELADD AS 'hostelAddress', HOTELINTRO AS 'hostelDescription' FROM HOTELINFO WHERE ID = :token";

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':token', $token, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response = array(
            'status' => 'success',
            'data' => $result
        );
    } else {
        $response = array(
            'status' => 'error',
        );
    }
} catch (PDOException $e) {
    $response = array(
        'status' => 'error',
    );
}

// 将响应数据转换为JSON格式并发送给前端
header('Content-Type: application/json');
echo json_encode($response);

// 关闭PDO连接
$pdo = null;
?>
