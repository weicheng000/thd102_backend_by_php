<?php
header("Content-Type: application/json");
include '../Conn.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (isset($data['Events']))

    if ($data['Events'] === true && isset($data['HotelId'])) {

        try {
            // 检查数据库连接
            if ($pdo === null) {
                throw new Exception("Database connection failed");
            }
            $HotelId = $data['HotelId'];
            // 检查记录是否存在
            $checkQuery = "SELECT COUNT(*) FROM `HOTELINFO` WHERE `ID` = :HotelId";
            $stmtCheck = $pdo->prepare($checkQuery);
            $stmtCheck->bindParam(':HotelId', $HotelId, PDO::PARAM_INT);
            $stmtCheck->execute();
            $rowCount = $stmtCheck->fetchColumn();

            if ($rowCount === 0) {
                // 记录不存在，返回错误消息
                http_response_code(404);
                echo json_encode(["status" => "error1"]);
            } else {
                // 更新记录的逻辑（与之前的代码相同）
                $HotelId = $data['HotelId'];
                $HotelName = $pdo->quote($data['HotelName']); // 使用pdo->quote()來處理字串
                $Address = $pdo->quote($data['Address']); // 使用pdo->quote()來處理字串
                $DOGROOM = $data['DOGROOM'];
                $CATROOM = $data['CATROOM'];
                $SAN = $data['SAN'];
                $AC = $data['AC'];
                $CCTV = $data['CCTV'];
                $HUM = $data['HUM'];
                $WF = $data['WF'];
                $Comment = $pdo->quote($data['Comment']); // 使用pdo->quote()來處理字串

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // 使用參數化來更新數據
                $updateQuery = "UPDATE `HOTELINFO` 
            SET `HOTELNAME` = $HotelName, 
                `HOTELADD` = $Address, 
                `HOTELINTRO` = $Comment, 
                `DOGROOM` = :DOGROOM, 
                `CATROOM` = :CATROOM, 
                `SAN` = :SAN, 
                `AC` = :AC, 
                `CCTV` = :CCTV, 
                `HUM` = :HUM, 
                `WF` = :WF 
            WHERE `ID` = :HotelId";

                $stmt = $pdo->prepare($updateQuery);
                $stmt->bindParam(':DOGROOM', $DOGROOM, PDO::PARAM_INT);
                $stmt->bindParam(':CATROOM', $CATROOM, PDO::PARAM_INT);
                $stmt->bindParam(':SAN', $SAN, PDO::PARAM_INT);
                $stmt->bindParam(':AC', $AC, PDO::PARAM_INT);
                $stmt->bindParam(':CCTV', $CCTV, PDO::PARAM_INT);
                $stmt->bindParam(':HUM', $HUM, PDO::PARAM_INT);
                $stmt->bindParam(':WF', $WF, PDO::PARAM_INT);
                $stmt->bindParam(':HotelId', $HotelId, PDO::PARAM_INT);

                $stmt->execute();
                // 设置HTTP状态码为200
                http_response_code(200);
                // 返回JSON响应
                echo json_encode(["status" => "success"]);
            }
        } catch (Exception $e) {
            // 错误处理
            http_response_code(500);
            echo json_encode(["status" => "Error: " . $e->getMessage()]);
        }

    } else if ($data['Events'] === false) {
        // 插入新记录的逻辑（与之前的代码相同

        $HotelName = $pdo->quote($data['HotelName']);
        $Address = $pdo->quote($data['Address']);
        $Info = $data['Info'];
        $DOGROOM = $data['DOGROOM'];
        $CATROOM = $data['CATROOM'];
        $SAN = $data['SAN'];
        $AC = $data['AC'];
        $CCTV = $data['CCTV'];
        $HUM = $data['HUM'];
        $WF = $data['WF'];
        $Comment = $pdo->quote($data['Comment']);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $insertQuery = "INSERT INTO `HOTELINFO` 
            (`HOTELNAME`, `HOTELADD`, `HOTELINTRO`, `MODE`, `DOGROOM`, `CATROOM`, `SAN`, `AC`, `CCTV`, `HUM`, `WF`) 
            VALUES ($HotelName, $Address, $Comment, $Info, $DOGROOM, $CATROOM, $SAN, $AC, $CCTV, $HUM, $WF)";

        $pdo->exec($insertQuery);

        $newID = $pdo->lastInsertId();

        http_response_code(200);
        // 返回JSON响应（可以根据需要设置响应内容）
        echo json_encode(["status" => "success", "newID" => $newID]);;
    }

?>