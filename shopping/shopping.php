<?php
header("Content-Type: application/json");
include("../Conn.php");


try {
    $input = file_get_contents('php://input');
    echo 'Received JSON data: ' . $input; // 调试输出接收到的 JSON 数据
    $data = json_decode($input, true);
    echo 'Decoded data: ' . print_r($data, true); // 调试输出解析后的数据

    if ($data === null) {
        echo json_encode(['error' => '无效的 JSON 数据: ' . json_last_error_msg()]);
        exit;
    }  
    // 開啟交易
    $pdo->beginTransaction();


    // 新增資料到 ORDER 表格
    $orderStatus = "無異動";
    $orderDate = $data['orderDate'];

    // 在这里添加检查 'totalPrice' 是否已定义和不为空的代码
    if (!isset($data['totalPrice']) || empty($data['totalPrice'])) {
        echo json_encode(['error' => 'totalPrice 未定义或为空']);
        exit;
    }


    $beforeTotal = $data['totalPrice'];
    $usePoints = $data['usePoints']; 
    $memberId = $data['memberId'];
    $items = $data['shoppingItems'];

    $insertOrderSql = "INSERT INTO `ORDER` (ORDERSTAUS, ORDERDATE, BEFORETOTAL, USEPOINTS, MEMBER_ID) 
    VALUES (:orderStatus, :orderDate, :beforeTotal, :usePoints, :memberId)";

    $stmt = $pdo->prepare($insertOrderSql);
    $stmt->bindParam(':orderStatus', $orderStatus, PDO::PARAM_STR);
    $stmt->bindParam(':orderDate', $orderDate, PDO::PARAM_STR);
    $stmt->bindParam(':beforeTotal', $beforeTotal, PDO::PARAM_INT);
    $stmt->bindParam(':usePoints', $usePoints, PDO::PARAM_INT);
    $stmt->bindParam(':memberId', $memberId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // 插入成功
        $orderId = $pdo->lastInsertId();
        // 继续执行其他插入操作和提交事务
        // 你可以在这里执行订单详情表的插入操作，然后提交事务
    } else {
        // 插入失败，处理错误
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['error' => '订单建立失败：' . $errorInfo[2]]);
        $pdo->rollback();
        exit;
    }



    // $stmt->execute();

    // 取剛建立的ORDER ID
    $orderId = $pdo->lastInsertId();

    foreach ($items as $item) {
        // 先取與 $item['product'] 匹配的飯店信息的 ID
        $hotelName = $item['product'];
        $getHotelInfoIdSql = "SELECT ID FROM hotelinfo WHERE HOTELNAME = :hotelName";
        $stmt2 = $pdo->prepare($getHotelInfoIdSql);
        $stmt2->bindParam(':hotelName', $hotelName, PDO::PARAM_STR);
        $stmt2->execute();
        $hotelInfoRow = $stmt2->fetch(PDO::FETCH_ASSOC);
    
        if ($hotelInfoRow) {
            $hotelInfoId = $hotelInfoRow['ID'];
        } else {
            // 如果未找到匹配的飯店信息，則將 $hotelInfoId 設置為 null
            $hotelInfoId = null;
        }
    
        // 然後執行插入，將 $hotelInfoId 用於 HOTELINFO_ID 列
        $nowPrice = $item['spPrice'];
        $quantity = isset($item['listDistance']) ? $item['listDistance'] : $item['listDate_D'];
        $amount = $item['BuyNum'];
        $size = $item['dogSizeValue'];
        $start = $item['startadd'];
        $end = $item['endadd'];
        $startDate = $item['listDate_S'];
        $endDate = $item['listDate_E'];
        $productId = $item['listTypeValue'];
    
        $insertOrderDetailsSql = "INSERT INTO ORDERDETAILS (NOWPRICE, QUANTITY, AMOUNT, `SIZE`, `START`, `END`, STARTDATE, ENDDATE, ORDER_ID, PRODUCT_ID, HOTELINFO_ID) 
        VALUES (:nowPrice, :quantity, :amount, :size , :start, :end, :startDate, :endDate, :orderId, :productId, :hotelInfoId)";
        
        $stmt1 = $pdo->prepare($insertOrderDetailsSql);
    
        $stmt1->bindParam(':nowPrice', $nowPrice, PDO::PARAM_INT);
        $stmt1->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt1->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt1->bindParam(':size', $size, PDO::PARAM_STR);
        $stmt1->bindParam(':start', $start, PDO::PARAM_STR);
        $stmt1->bindParam(':end', $end, PDO::PARAM_STR);
        $stmt1->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt1->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt1->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt1->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt1->bindParam(':hotelInfoId', $hotelInfoId, PDO::PARAM_INT);
    
        $stmt1->execute();
    }
    // 更新會員點數
    $currentPoints = 0;
    $newPoints = $currentPoints - $usePoints;

    $updatePointsSql = "UPDATE MEMBER SET POINTS = :newPoints WHERE ID = :memberId";
    $stmt3 = $pdo->prepare($updatePointsSql);
    $stmt3->bindParam(':newPoints', $newPoints, PDO::PARAM_INT);
    $stmt3->bindParam(':memberId', $memberId, PDO::PARAM_INT);
    $stmt3->execute();

    // 提交交易
    $pdo->commit();

    // echo json_encode(['message' => '訂單建立成功']);
    echo json_encode(['success' => true, 'message' => '訂單建立成功']);

} catch (PDOException $e) {
    // 回溯交易
    $pdo->rollback();
    echo json_encode(['error' => '訂單建立失敗：' . $e->getMessage()]);
}

?>
