<?php
require_once "config.php";
$nuevos = 0;
$actualizados = 0;
$txtfile = "/wp-content/plugins/pcservice/include/tipocambio.txt";

$fp = fopen($txtfile, "r");
$tipocambio = "";
while (!feof($fp)){
    $tipocambio .= fgets($fp);
}
fclose($fp);
$tipocambio = floatval($tipocambio);

$txtfile = "/wp-content/plugins/pcservice/include/margen.txt";

$fp = fopen($txtfile, "r");
$margen = "";
while (!feof($fp)){
    $margen .= fgets($fp);
}
fclose($fp);
$margen = (floatval($margen)/100) + 1;

$txtfile = "/wp-content/plugins/pcservice/include/impuesto.txt";

$fp = fopen($txtfile, "r");
$impuesto = "";
while (!feof($fp)){
    $impuesto .= fgets($fp);
}
fclose($fp);
$impuesto = ((floatval($impuesto))/100) + 1;

try {
    echo json_encode("Success: Datos Cargados Correctamente");
} catch (Exception $exception){
    echo json_encode("Error: ".$exception->getMessage());
}
$curl = curl_init();
$fecha = date("Y-m-d H:i:s");
curl_setopt($curl, CURLOPT_URL, "https://www.pcservice.com.uy/rest/products/bydate/?from=".date( "YmdHis", strtotime( "$fecha -1 hour")),);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $token"));
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
$data = json_decode(curl_exec($curl));

$consultas = 0;
$cproductos = 0;
$contador = 0;

$nuevos = 0;
$actualizados = 0;
foreach($data as $prod){
    
    if($contador < 106){
		$contador++;
		continue;
	}

    
    $images = [];
    if(count($prod->images) > 0){
        foreach($prod->images as $img){
            $imgReuturn = '';
            foreach($img->variations as $var){
                $imgReuturn = $var->url;
            }
            $images [] = $imgReuturn;
        }
    }

    
    $datas = [
        'id' => $prod->id,
        'title' => $prod->title,
        'description' => $prod->description,
        'body' => $prod->body,
        'type' => "product",
        'sku' => $prod->sku,
        'images' => json_encode($images),
        'price_original' => $prod->price->price,
        'price' => strval($prod->price->price * $tipocambio * $margen * $impuesto),
        'stock' => $prod->availability->stock,
        'sync' => 0
    ];   
    
    $sql = "SELECT COUNT(id) FROM productos WHERE id = '{$datas['id']}' ";
    $res = $conn->query($sql);
    $count = $res->fetchColumn();

    $sql2 = "SELECT COUNT(id) FROM productos WHERE id = '{$datas['id']}' AND id_woo IS NULL";
    $res2 = $conn->query($sql2);
    $count2 = $res2->fetchColumn();

    if($count == 1 && $count2 == 1){
        $sql = "DELETE FROM productos WHERE id = '{$datas['id']}'";
        $conn->query($sql);
        $count = 0;
    }

    if($count == 0){
        
        $sql = "INSERT INTO productos (id, title, description, body, type, sku, images, price_original, price, stock, sync) VALUES (:id, :title, :description, :body, :type, :sku, :images, :price_original, :price, :stock, :sync)";
        $stmt= $conn->prepare($sql);
        $stmt->execute($datas);
        $cproductos++;
        
        $data = [
            'name' => $datas['title'],
            'type' => 'simple',
            'regular_price' => $datas['price'],
            'description' => $datas['body'],
            'short_description' => $datas['description'],
            'sku' => $datas['sku'],
            'manage_stock' => true,
            'stock_quantity' => $datas['stock']
        ];
        
        $imagenes = [];
        
        if(isset($datas['images'])){
            $imgs = json_decode($datas['images']);
            foreach($imgs as $img){
                $id = rand(99999,99999999);
                $url = $img;
                $imga = "../../../uploads/cdrimgs/$id.jpg";
                file_put_contents($imga, file_get_contents($url));
                if(filesize($imga) > 0){
                    $imagenes [] = ["src" => "http://35.198.19.156/wp-content/uploads/cdrimgs/$id.jpg?_t=1668833276"];
                }
            }
        }
        $data['images'] = $imagenes;
        $sku = $data['sku'];
        $params = [
            'sku' => $sku
        ];
        
        $skupro = $woocommerce->get("products", $params);
        $id_woo = 0;
        foreach($skupro as $pro){
            if($pro->sku == $sku){
                $id_woo = $pro->id;
            }
        }
        if($id_woo == 0){
            $registrada = $woocommerce->post('products', $data);
            $datass = [
                'id_woo' => $registrada->id,
                'id' => $prod->id
            ];
            
            $sql = "UPDATE productos SET id_woo = :id_woo, stock_anterior = stock WHERE id = :id";
            $stmt= $conn->prepare($sql);
            $stmt->execute($datass);    
        }else{

            $woocommerce->put('products/'.$id_woo, ["regular_price"=>$datas['price'], "stock_quantity" => $datas['stock']]);

            $datass = [
                'id_woo' => $id_woo,
                'id' => $prod->id
            ];

            $sql = "UPDATE productos SET id_woo = :id_woo, stock_anterior = stock WHERE id = :id";
            $stmt= $conn->prepare($sql);
            $stmt->execute($datass);    

        }        
        $nuevos++;

    }elseif($count == 1){
        
        $stmt = $conn->query("SELECT * FROM productos WHERE id = '{$datas['id']}'");
        $pviejo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pviejo = $pviejo[0];
        $pnueov = $woocommerce->get("products/{$pviejo['id_woo']}");
        if($pnueov->stock_quantity > $pviejo['stock']){
            $stock = $pnueov['stock_quantity'] - $pviejo['stock'] + $datas['stock'];
        }else{
            $stock = $datas['stock'];
        }
        
        $sql = 'UPDATE productos SET price_original = "'.$datas['price_original'].'", stock = '.$datas['stock'].' WHERE id = "'.$datas['id'].'"';
        $conn->query($sql);
        
        $woocommerce->put('products/'.$pviejo['id_woo'], ["regular_price"=>$datas['price'], "stock_quantity" => $stock]);
        $actualizados++;
    }
    
    
    $consultas++;
    
}

echo '["Se actualizaron '.$actualizados.' y se agregaron '.$nuevos.'"]';

?>