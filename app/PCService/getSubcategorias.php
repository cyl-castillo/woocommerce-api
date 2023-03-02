<?php
require_once "config.php";


$stmt = $conn->query("SELECT * FROM categorias");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$consultas = 0;

foreach($categorias as $cat){
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $endpoints["subcategorias"].$cat['id']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $token"));
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $data = json_decode(curl_exec($curl));
    
    if(isset($data->childs)){
        foreach($data->childs as $dat){
            
            $image = null;
            if(count($dat->images) > 0){
                $image = $dat->images[0]->variations[0]->url;
            }
            $datas = [
                'id' => $dat->id,
                'title' => $dat->title,
                'description' => $dat->description,
                'type' => $dat->type,
                'image' => $image,
                'od_categoria' => $cat['id']
            ];
            
            
            $sql = "SELECT COUNT(id) FROM categorias WHERE id = $dat->id";
            $res = $conn->query($sql);
            $count = $res->fetchColumn();
            
            if($count == 0){
                
                $sql = "INSERT INTO categorias (id, title, description, type, image, od_categoria) VALUES (:id, :title, :description, :type, :image, :od_categoria)";
                $stmt= $conn->prepare($sql);
                $stmt->execute($datas);
                $consultas++;
                
            }
        }
        
    }

       

}
echo json_encode("PCSERVICE: Carga realizada correctamente");

?>
