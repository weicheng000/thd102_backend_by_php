<?php
// 匯入資料庫連接檔案
include '../Conn.php';

$response = array();

try {
    // 設定 PDO 錯誤模式為例外
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 執行 SQL 查詢
    $query = "SELECT ID, PIC FROM STICKERS WHERE `MODE` = 1 ORDER BY ID DESC LIMIT 0, 25";
    $stmt = $pdo->query($query);

    // 抓取查詢結果作為關聯陣列

    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 關閉 PDO 連接
    $pdo = null;

    // 設定成功的 JSON 响應
    $response['status'] = 'success';
    $response['data'] = $list;

} catch (PDOException $e) {
    // 處理連接或查詢錯誤
    $response['status'] = 'error';
    $response['message'] = "Error: " . $e->getMessage();
}

// 將整個回應轉換為 JSON
$jsonResponse = json_encode($response);

// 設置正確的 JSON 頭部
header('Content-Type: application/json');

// 發送 JSON 响應給客戶端
echo $jsonResponse;
?>
