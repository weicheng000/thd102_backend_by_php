<?php

include("../Conn.php");

$token = $_GET['account'];

try {
    // 讀取資料庫中的會員資料
    $sql = "SELECT * FROM MEMBER WHERE EMAIL = :EMAIL;";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':EMAIL', $token, PDO::PARAM_STR);
    $stmt->execute();

    $resultArray = '';

    // 檢查是否只有一條紀錄
    if ($stmt->rowCount() == 1) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(['status' => "error"]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => "發生數據庫錯誤" . $e->getMessage()]);
}

?>
