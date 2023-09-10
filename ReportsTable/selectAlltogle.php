<?php
header("Content-Type: application/json");
include '../Conn.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$token = $data['token'];
$key = $data['key'];

if(isset($token) && isset($key)){
    try{
        // 使用參數化查詢，避免SQL注入攻擊
        $sql = "UPDATE `STICKERS` SET `MODE` = :key WHERE (`ID` = :token);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_INT);

        // 執行更新操作
        $stmt->execute();

        // 檢查受影響的行數，以確認更新是否成功
        $affected_rows = $stmt->rowCount();
        if ($affected_rows > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    } catch(PDOException $e){
        echo json_encode(['status' => 'error', 'message' => '數據庫錯誤：' . $e->getMessage()]);
    } finally {
        // 關閉PDO連接
        $pdo = null;
    }
}
?>