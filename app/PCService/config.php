<?php

require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'https://shopit.uy/',
    'ck_77b0de9cb77d071a89670ec47093f9f39b28c085',
    'cs_a3457f2ffffa821cdde8c1b4edaffce2dca9d151',
    [
'timeout' => 3600,
'wp_api' => true,
'query_string_auth' => true,
'verify_ssl' => false,
        'version' => 'wc/v3',
        ]
    );


    $host = 'localhost';
    $dbname = 'shopitu1_wp_ipe05';
    $username = 'shopitu1_wp_x9pal';
    $password = '5X3#j0vRQFKgBUs_';


    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
        return 0;
    }

    $url = 'https://www.pcservice.com.uy/rest/';

    $endpoints = array(
        "login" => $url."auth/login",
        "categorias" => $url."categories/",
        "subcategorias" => $url."categories/",
        "producto" => $url."products/"
    );

    function getToken(){

        $userPCServie = "deniel.sanchez@goencodetech.com";
        $passPCService = "ShopitApi.123";

        $login = array(
            "username" => $userPCServie,
            "password" => $passPCService
        );

        $json_string = json_encode($login);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://www.pcservice.com.uy/rest/auth/login/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $data = json_decode(curl_exec($curl));

        if(curl_errno($curl)){
            echo 'Curl error: ' . curl_error($curl);
        }

        $token = $data->token;

        curl_close($curl);
        return $token;
    }
    $token = getToken();

    ?>
