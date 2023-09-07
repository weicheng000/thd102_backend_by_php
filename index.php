<?php
header("Content-Type: application/json");

$mysqli = new mysqli("localhost", "root", "80122010", "G2BK");

$currentPage = $_GET["currentPage"];
$pageSize = $_GET["pageSize"];

$startIndex = ($currentPage - 1) * $pageSize;

// 查詢分頁數據
$query = "SELECT * FROM reports LIMIT $startIndex, $pageSize";
$result = $mysqli->query($query);

if ($result) {
    $list = array();
    while ($row = $result->fetch_assoc()) {
        $list[] = $row;
    }

    // 查詢總筆數
    $countQuery = "SELECT COUNT(*) as total FROM reports";
    $countResult = $mysqli->query($countQuery);
    $totalRecords = $countResult->fetch_assoc()["total"];

    $totalPages = ceil($totalRecords / $pageSize);

    $response = array(
        "page" => array(
            "currentPage" => $currentPage,
            "totalPages" => $totalRecords,
        ),
        "result" => $list
    );

    echo json_encode($response);
} else {
    echo json_encode(array("error" => "没有找到数据"));
}

$mysqli->close();
?>
