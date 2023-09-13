<?php
include '../Conn.php';

$key = $_GET['key'];

if($key === '台北'){
    $key = '北';
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = "SELECT ID AS 'id', HOTELNAME AS 'name' FROM HOTELINFO WHERE HOTELADD LIKE CONCAT('%', :key, '%') LIMIT 0, 9";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':key', $key, PDO::PARAM_STR);
    $stmt->execute();

    $results = array();

    // 从查询结果中逐行提取数据
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = $row;
    }

    $count = count($results);

    if ($results) {
        $response = array(
            'status' => 'success',
            'count' => $count,
            'data' => $results
        );
    } else {
        $response = array(
            'status' => 'success',
            'data' => array()
        );
    }
} catch (PDOException $e) {
    $response = array(
        'status' => 'error',
    );
}

// 将响应数据转换为JSON格式并发送给前端
header('Content-Type: application/json');
echo json_encode($response);

// 关闭PDO连接
$pdo = null;
?>
