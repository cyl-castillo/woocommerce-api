<?php
require_once "config.php";

$stmt = $conn->query("SELECT * FROM categorias WHERE od_categoria IS NOT NULL");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$consultas = 0;
$cproductos = 0;

foreach($categorias as $cat){

    if($consultas%500 == 0){
        $token = getToken();
    }
    
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $endpoints["categorias"].$cat['od_categoria']."/".$cat['id']."/products");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $token"));
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $data = json_decode(curl_exec($curl));

    if(isset($data->childs)){
        
        if(isset($data->childs[0]->products)){
            
            $productos = $data->childs[0]->products;
            
            foreach($productos as $product){
                
                $images = [];
                if(count($product->images) > 0){
                    foreach($product->images as $img){
                        $imgReuturn = '';
                        foreach($img->variations as $var){
                            $imgReuturn = $var->url;
                        }
                        $images [] = $imgReuturn;
                    }
                }
                
                $datas = [
                    'id' => $product->id,
                    'title' => $product->title,
                    'description' => $product->description,
                    'type' => $product->type,
                    'sku' => $product->sku,
                    'id_categoria' => $cat['id'],
                    'images' => json_encode($images),
                ];
                
                $sql = "SELECT COUNT(id) FROM productos WHERE id = $product->id";
                $res = $conn->query($sql);
                $count = $res->fetchColumn();
                
                if($count == 0){
                    
                    $sql = "INSERT INTO productos (id, title, description, type, sku, id_categoria, images) VALUES (:id, :title, :description, :type, :sku, :id_categoria, :images)";
                    $stmt= $conn->prepare($sql);
                    $stmt->execute($datas);
                    $cproductos++;
                    
                }
                
            }
            
            
        }
    }
    curl_close($curl);
    echo "\nProcesado... $consultas";
    $consultas++;

}

echo "\nSe registraron $cproductos productos nuevos";

?>