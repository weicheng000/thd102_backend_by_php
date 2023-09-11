<?php
include '../Conn.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "UPDATE `STICKERS`
              SET
                `POSTDATE` = '2023-01-01',
                `PIC` = '\\thd102\\g2\\images\\photowall\\1.png', 
                `MODE` = '1', 
                `MEMBER_ID` = '3' 
              WHERE `ID` = 1";


$stmt = $pdo->prepare($query);


$stmt->execute();

echo "成功更新";
?>