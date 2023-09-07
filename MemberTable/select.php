<?php 

include '../Conn.php';

$currentPage = $_GET["currentPage"];
$pageSize = $_GET["pageSize"];
$startIndex = ($currentPage - 1) * $pageSize;
$ID = isset($_GET["MemberId"])? $_GET["MemberId"]: '';
$query = "SELECT * FROM `MEMBER` WHERE ID LIKE '%$ID%' LIMIT $startIndex, $pageSize";

$result = $pdo->query($query);

if ($result) {
    $list = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $list[] = $row;
    }

    // 查詢總筆數
    $countQuery = "SELECT COUNT(*) as total FROM `MEMBER`";
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
    echo json_encode(array("error" => "沒有找到資料"));
}

?>