<?php
include("../Conn.php");

// 接收來自前端的JSON數據
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['account'];
$newData = $data['userData'];

try {
    $sql = "UPDATE MEMBER SET 
                `NAME` = :NAME,
                PHONE = :PHONE,
                BRD = :BRD,
                `ADDRESS` = :ADDRESS
            WHERE EMAIL = :EMAIL;";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':NAME', $newData['NAME'], PDO::PARAM_STR);
    $stmt->bindParam(':PHONE', $newData['PHONE'], PDO::PARAM_STR);
    $stmt->bindParam(':BRD', $newData['BRD'], PDO::PARAM_STR);
    $stmt->bindParam(':ADDRESS', $newData['ADDRESS'], PDO::PARAM_STR);
    $stmt->bindParam(':EMAIL', $token, PDO::PARAM_STR);

    $stmt->execute();

    echo json_encode(['message' => "更新成功"]);
} catch (PDOException $e) {
    echo json_encode(['error' => "發生數據庫錯誤" . $e->getMessage()]);
}
?>
