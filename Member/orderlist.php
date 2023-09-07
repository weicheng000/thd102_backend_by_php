<?php
include("../Conn.php");

$ID = $_GET['MEMBER_ID'];

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 使用 JOIN 語句來選擇該會員的所有訂單
    $sql_pre = "SELECT m.ID AS MEMBER_ID, m.EMAIL, o.ID AS ORDER_ID, o.ORDERDATE, o.USEPOINTS
                FROM `MEMBER` m
                LEFT JOIN `ORDER` o ON m.ID = o.MEMBER_ID
                WHERE m.EMAIL = :id";

    $stmt = $pdo->prepare($sql_pre);
    $stmt->bindParam(':id', $ID, PDO::PARAM_STR);
    $stmt->execute();

    $result = []; // 初始化最終結果的陣列

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $order = [
            'MemberId' => $row['MEMBER_ID'],
            'OrderId' => $row['ORDER_ID'],
            'OrderDate' => $row['ORDERDATE'],
            'reduce' => $row['USEPOINTS'],
            'OrderList' => [],
        ];

        $orderId = $row['ORDER_ID'];
        $queryDetail = "SELECT
                        od.NOWPRICE,
                        od.QUANTITY,
                        od.AMOUNT,
                        od.SIZE,
                        od.START,
                        od.END,
                        IF(
                        DATE_FORMAT(od.STARTDATE, '%Y-%m-%d') = DATE_FORMAT(od.ENDDATE, '%Y-%m-%d'),
                        CONCAT(DATE_FORMAT(od.STARTDATE, '%m - %d'), ' | ', DATE_FORMAT(od.STARTDATE, '%H:%i')),
                        CONCAT(DATE_FORMAT(od.STARTDATE, '%m-%d'), ' - ', DATE_FORMAT(od.ENDDATE, '%m-%d'))
                        ) AS display_date,
                        pr.PRODUCTNAME,
                        IFNULL(hi.HOTELNAME, '寵物接送') AS display_hotelname
                        FROM ORDERDETAILS od
                        LEFT JOIN `ORDER` o ON od.ORDER_ID = o.ID
                        LEFT JOIN HOTELINFO hi ON od.HOTELINFO_ID = hi.ID
                        LEFT JOIN PRODUCT pr ON od.PRODUCT_ID = pr.ID
                        WHERE o.ID = $orderId";

        // 建立一個新的 PDO 語句來查詢訂單細項
        $stmtDetail = $pdo->query($queryDetail);
        
        while ($detailRow = $stmtDetail->fetch(PDO::FETCH_ASSOC)) {
            $order['OrderList'][] = $detailRow;
        }

        $result[] = $order;
    }
    
    echo json_encode($result);

} catch (PDOException $e) {
    echo "資料庫錯誤：" . $e->getMessage();
}
?>
