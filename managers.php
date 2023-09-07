<?php
function connectToDatabase() {
    $conn = new mysqli("localhost", "root", "80122010", "G2BK");

    if ($conn->connect_error) {
        die("連接資料庫失敗: " . $conn->connect_error);
    }

    return $conn;
}

function sendResponse($success, $message) {
    $response = array("success" => $success, "message" => $message);
    header("Content-Type: application/json");
    echo json_encode($response);
}

$conn = connectToDatabase();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["adminID"]) && isset($data["password"])) {
        $adminID = $data["adminID"];
        $password = $data["password"];

        // 使用预处理语句来防止SQL注入
        $query = "SELECT * FROM Managers WHERE AdminID = ? AND Password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $adminID, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            sendResponse(true, "登入成功");
        } else {
            sendResponse(false, "登入失敗 $adminID $password");
        }
        
        $stmt->close(); // 关闭预处理语句
    } else {
        sendResponse(false, "請提供管理者ID和密碼");
    }
}

$conn->close();
?>