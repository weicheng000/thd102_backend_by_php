<?php
header("Content-Type: application/json");
include '../Conn.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$email = $data['email'];
$account = $data['account'];
$pwd = $data['password'];

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the email already exists in the database
    $checkQuery = "SELECT COUNT(*) FROM `MEMBER` WHERE `EMAIL` = :email";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();

    $emailExists = $checkStmt->fetchColumn();

    if ($emailExists > 0) {
        // Email already exists, send a response indicating the duplication
        $response = array('error' => 'Email already exists');
        echo json_encode($response);
    } else {
        // Email doesn't exist, proceed with the insertion
        $query = "INSERT INTO `MEMBER` (`NAME`, `EMAIL`, `PASSWORD`, `BRD`, `PHONE`, `ADDRESS`, `STATUS`, `POINTS`) 
                  VALUES (:name, :email, :pwd, '1990-01-01', '000000000', '請設定你的地址', '正常', '0');";

        $stmt = $pdo->prepare($query);

        // Bind parameters
        $stmt->bindParam(':name', $account);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':pwd', $pwd);

        // Execute the statement
        $stmt->execute();

        // Send a success response
        $response = array('message' => 'success');
        echo json_encode($response);
    }
} catch (PDOException $e) {
    // Handle any exceptions here
    $error = array('error' => $e->getMessage());
    echo json_encode($error);
}
?>

