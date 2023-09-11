<?php
include "../Conn.php";

$counts = array();

$regions = array(
    "台北" => "%北%",
    "台中" => "%中%",
    "台南" => "%南%",
    "高雄" => "%雄%",
    "新竹" => "%竹%",
    "花蓮" => "%花%",
    "嘉義" => "%嘉%",
    "外島" => array("%澎%", "%金%", "%江%")
);

foreach ($regions as $region => $prefix) {
    if (is_array($prefix)) {
        $count = 0;
        foreach ($prefix as $p) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM HOTELINFO WHERE LEFT(HOTELADD, 3) LIKE :prefix");
            $stmt->bindParam(':prefix', $p, PDO::PARAM_STR);
            $stmt->execute();
            $count += $stmt->fetchColumn();
        }
        $counts[] = array("name" => $region, "accommodations" => $count);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM HOTELINFO WHERE LEFT(HOTELADD, 3) LIKE :prefix");
        $stmt->bindParam(':prefix', $prefix, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        $counts[] = array("name" => $region, "accommodations" => $count);
    }
}

// 将$counts数组转换为JSON格式
$json_data = json_encode($counts);

// 输出JSON数据
echo $json_data;
?>
