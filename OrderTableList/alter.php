<?php
header("Content-Type: application/json");
include '../Conn.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$token = $data['token'];
$key = $data['key'];

if (isset($token)) {
    if (isset($key)){
        try {
            $sql = "UPDATE `ORDER` SET `ORDERSTAUS` = :key WHERE `ID` = :token";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response['status'] = 'success';
                echo json_encode($response);
            } else {
                $response['status'] = 'noChange';
                echo json_encode($response);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => "error" . $e->getMessage()]);
        }
    }
}
?>