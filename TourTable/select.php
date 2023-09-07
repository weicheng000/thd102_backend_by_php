<?php 

include '../Conn.php';

$currentPage = $_GET["currentPage"];
$pageSize = $_GET["pageSize"];
$startIndex = ($currentPage - 1) * $pageSize;
$ID = isset($_GET["HotelId"])? $_GET["HotelId"]: '';

$query = "SELECT * FROM `HOTELINFO` WHERE ID LIKE '%$ID%' AND HOTELNAME != '寵物接送' LIMIT $startIndex, $pageSize";
$result = $pdo->query($query);

if ($result) {
    $list = array();

    // print_r($result);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $list[] = $row;
    }

    // 查詢總筆數
    $countQuery = "SELECT COUNT(*) as total FROM `HOTELINFO`";
    $countResult = $pdo->query($countQuery);
    $totalRecords = $countResult->fetch(PDO::FETCH_ASSOC)["total"];

    $totalPages = ceil($totalRecords / $pageSize);

    // 目前使用線下的資料庫環境 BIT 格式用 AJAX 前端跟 PHP 都會正確拿到 0 跟 1
    // 但線上的資料庫環境不管PHP 或前端完竟 0 跟 1 都會回傳成 /U0000 跟 /U0001

    $response = array(
        "page" => array(
            "currentPage" => $currentPage,
            "totalPages" => $totalRecords,
        ),
        "result" => $list
    );

    echo json_encode($response);
} else {
    echo json_encode(array("error" => "沒有找到資料"));
}

?>