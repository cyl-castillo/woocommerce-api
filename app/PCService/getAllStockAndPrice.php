<?php
require_once "config.php";

$stmt = $conn->query("SELECT id, sku, sync FROM productos WHERE id_woo IS NULL AND sync = 0");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$consultas = 0;
$total = count($productos);
$fallos = 0;

foreach($productos as $producto){
    
    if($consultas%250 == 0){
        $token = getToken();
    }
    
    echo "\nProcesando {$producto['id']} - ";
    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $endpoints["producto"].$producto['id']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $token"));
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $data = json_decode(curl_exec($curl));
    
    if(isset($data->price)){
        if(isset($data->price->price)){
            if($producto["sync"] == 0){
                $datas = [
                    'price_original' => $data->price->price,
                    'price' => $data->price->price * 1.22 * 1.25,
                    'stock' => $data->availability->stock,
                    'body' => $data->body,
                    'id' => $producto['id'],
                    'sync' => 1
                ];    
                $sql = "UPDATE productos SET price_original = :price_original, price = :price, stock = :stock, body = :body, sync = :sync WHERE id = :id";
                $stmt= $conn->prepare($sql);
                $stmt->execute($datas);
                $consultas++;
                
            }else{
                $datas = [
                    'price_original' => $data->price->price,
                    'price' => $data->price->price * 1.22 * 1.25,
                    'stock' => $data->availability->stock,
                    'sync' => 1,
                    'id' => $producto['id'],
                ];   
                $sql = "UPDATE productos SET price_original = :price_original, price = :price, stock = :stock, sync = :sync WHERE id = :id";
                $stmt= $conn->prepare($sql);
                $stmt->execute($datas);
                $consultas++;
                
            }
            
            curl_close($curl);
        }else{
            echo "Producto sin precio, no subido";
            $fallos++;
        }
    }else{
        echo "Producto sin precio, no subido";
        $fallos++;
    }
    echo "Procesado... $consultas/$total";
}

echo "\nSe actualizaron $consultas productos";
echo "\nFallaron $fallos productos";


?>