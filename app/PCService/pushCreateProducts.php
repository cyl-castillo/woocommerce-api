<?php
require_once "config.php";

try {

} catch (Exception $exception){
    echo json_encode("Error: ".$exception->getMessage());
}


$stmt = $conn->query("SELECT productos.id, productos.title, productos.price, productos.body, productos.description, categorias.id_woo, productos.sku, productos.stock, productos.images FROM productos JOIN categorias ON productos.id_categoria = categorias.id WHERE productos.id_woo IS NULL AND productos.stock IS NOT NULL");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$consultas = 0;
$total = count($productos);

echo $total." productos seran cargados a woocomerce...";
function fsize($path) { 
      $fp = fopen($path,"r"); 
      $inf = stream_get_meta_data($fp); 
      fclose($fp); 
      foreach($inf["wrapper_data"] as $v) { 
        if (stristr($v, "content-length")) { 
          $v = explode(":", $v); 
          return trim($v[1]); 
        } 
      } 
      return 0;
    } 

foreach($productos as $prod){

//print_r($prod);    
    $data = [
        'name' => $prod['title'],
        'type' => 'simple',
        'regular_price' => $prod['price'],
        'description' => $prod['body'],
        'short_description' => $prod['description'],
        'categories' => [
            [
                'id' => $prod['id_woo']
                ]
            ], 
            'sku' => $prod['sku'],
            'manage_stock' => true,
            'stock_quantity' => $prod['stock']
        ];

        $imagenes = [];

        if(isset($prod['images'])){
            $imgs = json_decode($prod['images']);
            foreach($imgs as $img){
if($img != ""){
if(fsize($img) > 0)
                $imagenes [] = ["src" => $img];
}
            }
        }

	if($imagenes != []){
        $data['images'] = $imagenes;
}
        $registrada = $woocommerce->post('products', $data);
        
        $datas = [
            'id_woo' => $registrada->id,
            'id' => $prod['id']
        ];
        
        $sql = "UPDATE productos SET id_woo = :id_woo, stock_anterior = stock WHERE id = :id";
        $stmt= $conn->prepare($sql);
        $stmt->execute($datas);
        
        $consultas++;
        echo "\nSe han registrado $consultas productos";
	
    }
    
    
    echo "\nSe cargaron $consultas productos";
    
    
    ?>
