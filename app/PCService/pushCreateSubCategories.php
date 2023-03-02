<?php
require_once "config.php";

try {
    echo json_encode("Success: Datos Cargados Correctamente");
} catch (Exception $exception){
    echo json_encode("Error: ".$exception->getMessage());
}

$stmt = $conn->query("SELECT * FROM categorias WHERE id_woo IS NULL AND od_categoria IS NOT NULL");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$consultas = 0;
$total = count($categorias);

$stmt = $conn->query("SELECT * FROM categorias WHERE id_woo IS NOT NULL AND od_categoria IS NULL");
$topCategorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo $total." categorias seran cargados a woocomerce...";


foreach($categorias as $cat){
    
    $parent = 0;

    foreach($topCategorias as $tc){
        if($tc['id'] == $cat['od_categoria']){
            $parent = $tc['id_woo'];
        }
    }

    if($parent == 0){
        print_r($cat);
    }

    $data = [
        'name' => $cat['title'],
        'parent' => $parent
    ];

    if(isset($cat["description"])){
        $data['description'] = $cat["description"];
    }
    
    $registrada = $woocommerce->post('products/categories', $data);

    $datas = [
        'id_woo' => $registrada->id,
        'id' => $cat['id']
    ];

    $sql = "UPDATE categorias SET id_woo = :id_woo WHERE id = :id";
    $stmt= $conn->prepare($sql);
    $stmt->execute($datas);

    $consultas++;

}


echo "\nSe cargaron $consultas categorias";


?>