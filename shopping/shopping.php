<?php
include("./Conn.php");

// 處理前端發送的訂單數據（orderData 和 orderDetailsData）
$orderData = json_decode(file_get_contents("php://input"), true);

// 執行資料庫操作
// 假設 $db 是你的資料庫連接對象
// 假設 ORDER 表和 ORDERDETAILS 表是你的資料庫表

// 首先插入訂單數據到 ORDER 表，獲取自動生成的訂單ID
$sql = "INSERT INTO `ORDER` (ORDERSTATUS, ORDERDATE, BEFORETOTAL, USEPOINTS, MEMBER_ID)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
// $stmt->bind_param("ssdii", $orderData['ORDERSTATUS'], $orderData['ORDERDATE'], $orderData['BEFORETOTAL'], $orderData['USEPOINTS'], $orderData['MEMBER_ID']);
$stmt -> bindParam(1, $orderData['ORDERSTAUS']);
$stmt -> bindParam(2, $orderData['ORDERDATE']);
$stmt -> bindParam(3, $orderData['BEFORETOTAL']);
$stmt -> bindParam(4, $orderData['USEPOINTS']);
$stmt -> bindParam(5, $orderData['MEMBER_ID']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // 獲取剛插入的訂單ID
    $orderId = $stmt->insert_id;

    // 然後插入訂單詳情數據到 ORDERDETAILS 表
    foreach ($orderData['orderDetailsData'] as $orderDetail) {
        $sql = "INSERT INTO ORDERDETAILS (NOWPRICE, QUANTITY, AMOUNT, SIZE, START, END, STARTDATE, ENDDATE, ORDER_ID, PRODUCT_ID, HOTELINFO_ID)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("dddsdssssss", $orderDetail['NOWPRICE'], $orderDetail['QUANTITY'], $orderDetail['AMOUNT'], $orderDetail['SIZE'], $orderDetail['START'], $orderDetail['END'], $orderDetail['STARTDATE'], $orderDetail['ENDDATE'], $orderId, $orderDetail['PRODUCT_ID'], $orderDetail['HOTELINFO_ID']);
        $stmt->execute();
    }

    // 如果訂單詳情也插入成功，向前端發送成功響應
    echo json_encode(array("success" => true));
} else {
    // 如果訂單插入失敗，向前端發送失敗響應
    echo json_encode(array("success" => false));
}

// 确保PHP文件没有其他输出或HTML内容
exit;
?>
