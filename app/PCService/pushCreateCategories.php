<?php

use Codexshaper\WooCommerce\Models\Category;

require_once "config.php";

try {
    $stmt = $conn->query("SELECT * FROM categorias WHERE id_woo IS NULL AND od_categoria IS NULL Limit 10");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $consultas = 0;
    $total = count($categorias);


    foreach($categorias as $cat){

        $data = [
            'name' => $cat['title']
        ];

        $registrada = Category::create($data);
//        $registrada = $woocommerce->post('product/categories', $data);

        $datas = [
            'id_woo' => $registrada->id,
            'id' => $cat['id']
        ];

        $sql = "UPDATE categorias SET id_woo = :id_woo WHERE id = :id";
        $stmt= $conn->prepare($sql);
        $stmt->execute($datas);

        $consultas++;

    }

    echo json_encode("PCSERVICE: Carga realizada correctamente");

} catch (Exception $exception){
    echo json_encode("ERROR: ".$exception->getMessage() );
}


?>
