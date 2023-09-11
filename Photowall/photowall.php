<?php
header("Content-type: application/json");

$file = $_FILES["profile"]["name"];
$token = $_POST["name"];

if (isset($memberID)) {
    return;
}

$hzm = substr($file, strpos($file, "."));
$newfile = date("Y-m-d") . "-" . rand(100, 999);
$allowedExts = array("gif", "jpeg", "jpg", "png");
$temp = explode(".", $file);
$extension = end($temp);

if (
    (($_FILES["profile"]["type"] == "image/gif")
        || ($_FILES["profile"]["type"] == "image/jpeg")
        || ($_FILES["profile"]["type"] == "image/jpg")
        || ($_FILES["profile"]["type"] == "image/pjpeg")
        || ($_FILES["profile"]["type"] == "image/x-png")
        || ($_FILES["profile"]["type"] == "image/png"))
    && ($_FILES["profile"]["size"] < 10485760)
    && in_array($extension, $allowedExts)
) {
    // 判断上传结果
    if ($_FILES["profile"]["error"] > 0) {
        $result = array(
            'status' => 'error',
        );
    } else {
        $ServerRoot = $_SERVER["DOCUMENT_ROOT"];
        $filePath = $ServerRoot . "/thd102/g2/images/photowall/" . $newfile . $hzm;

        // 上传文件
        if (move_uploaded_file($_FILES["profile"]["tmp_name"], $filePath)) {
            $file_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];

            try {
                include '../Conn.php';

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = "INSERT INTO STICKERS (POSTDATE, PIC, `MODE`, MEMBER_ID)
                SELECT NOW(), '/thd102/g2/images/photowall/$newfile$hzm', 1, ID
                FROM `MEMBER`
                WHERE EMAIL = :token";

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->execute();

                $result = array(
                    "status" => "success",
                    "filePath" => "/thd102/g2/images/photowall/".$newfile.$hzm
                );
            } catch (PDOException $e) {
                // 写入失败，删除上传的图片
                unlink($filePath);

                $result = array(
                    'status' => 'error',
                    'error_message' => $e->getMessage()
                );
            }

            $pdo = null;
        } else {
            $result = array(
                'status' => 'error',
                'error_message' => '文件上传失败'
            );
        }
    }
} else {
    $result = array(
        'status' => 'error'
    );
}

// 输出JSON
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
