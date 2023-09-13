<?php
$MemberId = $_GET["MemberId"];
if (!isset($MemberId)) {
    $res = array('login' => "error");
    header('Content-Type: application/json');
    echo json_encode($res);
} else {
    include '../Conn.php';
    $query1 = "SELECT 
        od.HOTELINFO_ID,
      h.HOTELNAME,
      p.PRODUCTNAME,
      od.AMOUNT * od.QUANTITY AS `AMOUNT`,
      od.NOWPRICE
    FROM ORDERDETAILS od
    LEFT JOIN  PRODUCT p
        ON od.PRODUCT_ID = p.ID
    JOIN HOTELINFO h
      ON od.HOTELINFO_ID = h.ID
    WHERE od.ORDER_ID = $MemberId AND od.START IS NULL";

    $query2 = "SELECT 
        p.PRODUCTNAME,
        od.START,
        od.END,
        od.QUANTITY,
        od.NOWPRICE
      FROM ORDERDETAILS od
      LEFT JOIN  PRODUCT p
          ON od.PRODUCT_ID = p.ID
      WHERE od.ORDER_ID = $MemberId AND od.START IS NOT NULL";

    $hotel_list = $pdo->query($query1);
    $driver_list = $pdo->query($query2);

    if($hotel_list){
        $hotelData = array();
        while($item = $hotel_list->fetch(PDO::FETCH_ASSOC)){
            $hotelData[] = $item;
        }  
    }else{
        $hotelData[] = array();
    }

    if($driver_list){
        $driverData = array();
        while($item = $driver_list->fetch(PDO::FETCH_ASSOC)){
            $driverData[] = $item;
        }  
    }else{
        $driverData[] = array();
    }


    $res = array(
        "hotel" => $hotelData,
        "driver" => $driverData
    );

    echo json_encode($res);
}

?>