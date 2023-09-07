<?php

include '../Conn.php';

$currentPage = $_GET["currentPage"];
$pageSize = $_GET["pageSize"];
$startIndex = ($currentPage - 1) * $pageSize;
$ID = isset($_GET["ReportId"]) ? $_GET["ReportId"] : '';

$query = "SELECT 
r.REPORTSDATE,
r.REPORTSMEMBER_ID,
s.MEMBER_ID,
m.EMAIL,
s.POSTDATE,
s.MODE
FROM `STICKERS` s
 RIGHT JOIN `REPORTS` r
  ON s.ID = r.STICKERS_ID
 JOIN `MEMBER` m
 	ON r.REPORTSMEMBER_ID = m.ID
WHERE r.REPORTSMEMBER_ID LIKE '%$ID%'
LIMIT $startIndex, $pageSize";

$result = $pdo->query($query);

if ($result) {
    $list = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $list[] = $row;
    }

    // 查詢總筆數
    $countQuery = "SELECT COUNT(*) as total FROM `REPORTS`";
    $countResult = $pdo->query($countQuery);
    $totalRecords = $countResult->fetch(PDO::FETCH_ASSOC)["total"];

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

?>