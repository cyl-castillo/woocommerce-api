<?php
require_once "config.php";

$stmt = $conn->query("SELECT * FROM productos WHERE id_woo IS NOT NULL");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

$consultas = 0;
$total = count($productos);

try {
    echo json_encode("Success: Datos Cargados Correctamente");
} catch (Exception $exception){
    echo json_encode("Error: ".$exception->getMessage());
}

$delete = [];
foreach($productos as $prod){
	
	$dt = array(
		"id" => $prod['id_woo'],
		"regular_price" => $prod['price_original'] * $tipocambio * $margen * $impuesto
	);
    
    $update [] = $dt;
    
    if(count($update) == 100){
        $data = ["update"=>$update];
        $woocommerce->post('products/batch', $data);
        $update = [];
    }

}    
$data = ["update"=>$update];
$woocommerce->post('products/batch', $data);

echo '["'.$total.' productos actualizados"]';

?>