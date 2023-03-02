<?php
require_once "config.php";

try {

} catch (Exception $exception){
    echo json_encode("Error: ".$exception->getMessage());
}

$stmt = $conn->query("SELECT * FROM categorias WHERE id_woo IS NOT NULL");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$consultas = 0;
$total = count($categorias);

$update = [];

foreach($categorias as $cat){
    
    $data = ["id"=>$cat['id_woo']];

    if(isset($cat["image"])){
        $data["image"] = [
            'src' => $cat["image"]
        ];
    }

    $update [] = $data;

    if(count($update) == 100){
        $datas = ["update"=>$update];
        $woocommerce->post('products/categories/batch', $datas);
        $update = [];
    }

    $consultas++;

}

$datas = ["update"=>$update];
print_r($woocommerce->post('products/categories/batch', $datas));
$update = [];


?>