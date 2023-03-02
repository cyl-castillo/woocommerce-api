<?php
require_once "config.php";

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $endpoints["categorias"]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $token"));
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

$data = json_decode(curl_exec($curl));
$consultas = 0;
foreach ($data as $dat) {
    $image = null;
    if(count($dat->images) > 0){
        $image = $dat->images[0]->variations[0]->url;
    }
    $data = [
        'id' => $dat->id,
        'title' => $dat->title,
        'description' => $dat->description,
        'type' => $dat->type,
        'image' => $image,
    ];

    try {
        $sql = "INSERT INTO categorias (id, title, description, type, image) VALUES (:id, :title, :description, :type, :image)";
        $stmt= $conn->prepare($sql);
        $stmt->execute($data);
        $consultas++;
    } catch (Exception $exception){
        echo $exception->getMessage();
    }



       
}
echo json_encode("PCSERVICE: Carga realizada correctamente");

?>
