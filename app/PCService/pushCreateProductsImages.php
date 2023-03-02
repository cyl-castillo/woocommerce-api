<?php
require_once "config.php";

try {
    echo json_encode("Success: Datos Cargados Correctamente");
} catch (Exception $exception){
    echo json_encode("Error: ".$exception->getMessage());
}


$stmt = $conn->query("SELECT * FROM productos WHERE id_woo IS NOT NULL AND images IS NOT NULL");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$consultas = 0;
$total = count($productos);

echo $total." productos seran actualizados en woocomerce...";

foreach($productos as $prod){
    


        $data = [];
        

        $imagenes = [];

        if(isset($prod['images'])){
            $imgs = json_decode($prod['images']);
            foreach($imgs as $img){
                $imagenes [] = ["src" => $img];
            }
        }

        $data['images'] = $imagenes;
        
        $woocommerce->put('products/'.$prod['id_woo'], $data);
        
        $consultas++;
        echo "\nSe han registrado $consultas productos";

    }
    
    
    echo "\nSe cargaron $consultas productos";
    
    
    ?>