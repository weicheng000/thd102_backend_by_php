<?php
// 编码
header("Content-type:application/json");
 
// 获取文件
$file = $_FILES["image"]["name"];
 
$hzm = substr($file,strpos($file,"."));
 
$newfile = date("Y-m-d")."-".rand(100,999);
 
// 允许上传的后缀
$allowedExts = array("gif", "jpeg", "jpg", "png");
$temp = explode(".", $file);
$extension = end($temp);

if ((($_FILES["image"]["type"] == "image/gif")
|| ($_FILES["image"]["type"] == "image/jpeg")
|| ($_FILES["image"]["type"] == "image/jpg")
|| ($_FILES["image"]["type"] == "image/pjpeg")
|| ($_FILES["image"]["type"] == "image/x-png")
|| ($_FILES["image"]["type"] == "image/png"))
&& ($_FILES["image"]["size"] < 10485760)
&& in_array($extension, $allowedExts)){
    
    // 判断上传结果
    if ($_FILES["image"]["error"] > 0){
        
        $result = array(
            'status' => 'error',
        );
    }else{

        $ServerRoot = $_SERVER["DOCUMENT_ROOT"];
        $filePath = $ServerRoot."/thd102/g2/images/hostel/".$newfile.$hzm;
        // 上传文件
        move_uploaded_file($_FILES["image"]["tmp_name"], $filePath);
        $file_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
        $result = array(
            "status" => "success",
            "filePath" => "/thd102/g2/images/hostel/".$newfile.$hzm
        );
    }
}else{
    
    $result = array(
        'status' => 'error'
    );
}

// 输出JSON
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>