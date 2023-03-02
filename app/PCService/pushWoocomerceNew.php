<?php
require_once "config.php";

try {
    echo json_encode("Success: Datos Cargados Correctamente");
} catch (Exception $exception){
    echo json_encode("Error: ".$exception->getMessage());
}

$stmt = $conn->query("SELECT * FROM productos WHERE id_woo IS NULL AND price_original > 0 AND stock_anterior = 0 AND sync = 1");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$consultas = 0;
$total = count($productos);

echo $total." productos seran cargados a woocomerce...";


foreach($productos as $producto){
    
    echo "\nProcesando {$producto['id']} - ";
   
    $data = [
        'name' => $producto['title'],
        'type' => 'simple',
        'regular_price' => $producto['price'],
        'description' => $producto['body'],
        'short_description' => $producto['description'],
        'categories' => [
            [
                'id' => 9
            ],
            [
                'id' => 14
            ]
        ],
        'images' => [
            [
                'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg'
            ],
            [
                'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg'
            ]
        ]
    ];
    

    curl_close($curl);

    echo "Procesado... $consultas/$total";
}

echo "\nSe cargaron $consultas productos";


?>