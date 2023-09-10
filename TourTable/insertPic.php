<?php
include '../Conn.php';
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data === null) {
    $response = ['status' => 'error', 'message' => '無效的數據'];
    echo json_encode($response);
    exit;
}
try {

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 开始事务
    $pdo->beginTransaction();

    foreach ($data['url'] as $index => $value) {
        // 如果当前值为空，则跳过 SQL 语句的执行
        if ($value === null) {
            continue;
        }

        // 构建 SQL 语句并执行
        $sql = "SELECT 1 FROM `HOTELPIC` WHERE `HPIC` = " . ($index + 1) . " AND `HOTELINFO_ID` = '{$data['hotelid']}'";
        $checkResult = $pdo->query($sql);

        if ($checkResult->rowCount() > 0) {
            // 如果存在相同记录，执行更新操作
            $updateSql = "UPDATE `HOTELPIC` SET `SEQ` = '$value' WHERE `HPIC` = " . ($index + 1) . " AND `HOTELINFO_ID` = '{$data['hotelid']}'";
            $pdo->exec($updateSql);
        } else {
            $insertSql = "INSERT INTO `HOTELPIC` (`SEQ`, `HPIC`, `HOTELINFO_ID`)
          VALUES ('$value', " . ($index + 1) . ", '{$data['hotelid']}')";
            $pdo->exec($insertSql);
        }


    }

    // 提交事务
    $pdo->commit();

    // 返回成功消息
    $response = ['status' => 'success', 'message' => '所有 SQL 语句执行成功'];
    echo json_encode($response);
} catch (PDOException $e) {
    // 如果发生异常，回滚事务并返回错误消息
    $pdo->rollBack();
    $response = ['status' => 'error', 'message' => 'SQL 语句执行失败：' . $e->getMessage()];
    echo json_encode($response);
}

// 关闭数据库连接
$pdo = null;
?>