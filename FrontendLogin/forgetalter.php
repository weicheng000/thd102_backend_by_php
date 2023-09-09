<?php
header("Content-Type: application/json");
include("../Conn.php");

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$email = $data['email'];
$set = $data['psd'];

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Email doesn't exist, proceed with the insertion
    $query = "UPDATE `MEMBER` SET `PASSWORD` = :pwd WHERE `EMAIL` = :email;";

    $stmt = $pdo->prepare($query);

    // Bind parameters
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':pwd', $set);

    // Execute the statement
    $stmt->execute();

    // Send a success response
    $response = array('status' => 'success');
    echo json_encode($response);

} catch (PDOException $e) {
    // Handle any exceptions here
    $error = array('error' => $e->getMessage());
    echo json_encode($error);
}
?>