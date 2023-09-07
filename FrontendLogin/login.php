<?php 
header("Content-Type: application/json");
include '../Conn.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$account = $data['account'];
$pwd = $data['password'];

try{
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM `MEMBER` WHERE EMAIL = ? AND `PASSWORD` = ?";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(1,$account);
    $stmt->bindParam(2,$pwd);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result){
        $res = array('login' => "success");
        session_start();
        $_SESSION[$account] = 'success';

        header('Content-Type: application/json');
        echo json_encode($res);
    }else{
        $res = array('login' => "error");
        header('Content-Type: application/json');
        echo json_encode($res);
    }
} catch(PDOException $e) {
    die("資料庫連接失敗: " . $e->getMessage());
}
$pdo = null;
?>