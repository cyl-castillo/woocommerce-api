<?php
if(!isset($wpdb))
{
    include_once(ABSPATH.'wp-config.php');
    include_once(ABSPATH.'wp-includes/class-wpdb.php');
}
global $wpdb;


$sql = "CREATE TABLE IF NOT EXISTS `categorias` ( `id` int(11) NOT NULL, `title` varchar(64) NOT NULL, `type` varchar(40) NOT NULL, `image` varchar(256) DEFAULT NULL, `description` varchar(256) DEFAULT NULL, `od_categoria` int(11) DEFAULT NULL, `id_woo` int(11) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$wpdb->get_results($sql);

$sql = "CREATE TABLE IF NOT EXISTS `productos` ( `id` int(11) NOT NULL, `title` varchar(128) NOT NULL, `description` varchar(512) NOT NULL, `type` varchar(32) NOT NULL, `images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL, `sku` varchar(32) NOT NULL, `price_original` decimal(10,2) DEFAULT NULL, `stock` int(11) DEFAULT NULL, `price` decimal(10,2) DEFAULT NULL, `sync` tinyint(1) NOT NULL DEFAULT 0, `body` text DEFAULT NULL, `stock_anterior` int(11) NOT NULL DEFAULT 0, `id_woo` int(11) DEFAULT NULL, `id_categoria` int(11) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$wpdb->get_results($sql);

$sql = "CREATE TABLE IF NOT EXISTS `config` (`username` varchar(64) NOT NULL, `password` varchar(64) NOT NULL, `api` varchar(64) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$wpdb->get_results($sql);

$results = $wpdb->get_results( 'SELECT * FROM config WHERE api = "pcservice"', OBJECT );

if(isset($_POST['action'])){
    if($_POST['action'] == "saveform"){
        echo 'Guardando...';

        if(count($results) == 0){

            $wpdb->insert( 'config',

            array(
                "username" => $_POST['username'],
                "password" => $_POST['password'],
                "api" => 'pcservice'
                )
            );

        }else{

            $wpdb->update( 'config',
            array(
                "username" => $_POST['username'],
                "password" => $_POST['password']
            ),
            array( 'api' => 'pcservice' )
        );

    }
}else if($_POST['action'] == "uptcambio"){
		$txtfile = plugin_dir_path(__FILE__) . "tipocambio.txt";
        $texto = $_POST['username'];
		$file = fopen($txtfile, "w+");
		fwrite($file, $texto);
		fclose($file);
	}else if($_POST['action'] == "uptimpuesto"){
		$txtfile = plugin_dir_path(__FILE__) . "impuesto.txt";
        $texto = $_POST['username'];
		$file = fopen($txtfile, "w+");
		fwrite($file, $texto);
		fclose($file);
	}else if($_POST['action'] == "uptmargen"){
		$txtfile = plugin_dir_path(__FILE__) . "margen.txt";
        $texto = $_POST['username'];
		$file = fopen($txtfile, "w+");
		fwrite($file, $texto);
		fclose($file);
	}
}

$results = $wpdb->get_results( 'SELECT * FROM config WHERE api = "pcservice"', OBJECT );

function getFile($name){
	$txtfile = plugin_dir_path(__FILE__) . "$name.txt";
	$fp = (file_exists($txtfile))? fopen($txtfile, "a+") : fopen($txtfile, "w+");

	//$fp = fopen($txtfile, "r");
	$texto = "";
	while (!feof($fp)){
    	$texto .= fgets($fp);
	}
	fclose($fp);
	return $texto;
}

?>

<h3>API PC Service</h3>

<?php if(count($results) > 0){ ?>

    <form action="" method="post">
    <input type="hidden" name="action" value="saveform">
    <label for="username">Usuario</label>
    <input type="text" name="username" value="<?php echo $results[0]->username; ?>">

    <label for="password">Password</label>
    <input type="text" name="password" value="<?php echo $results[0]->password; ?>">
    <button type="submit" class="button button-primary button-large">Guardar Cambios</button>
    </form>


    <?php }else{
        ?>
        <form action="" method="post">
        <input type="hidden" name="action" value="saveform">
        <label for="username">Usuario</label>
        <input type="text" name="username">

        <label for="password">Password</label>
        <input type="text" name="password">
        <button type="submit" class="button button-primary button-large">Guardar Cambios</button>
        </form>
        <?php } ?>

    <form action="" method="post" style="margin-top:15px;">
    	<input type="hidden" name="action" value="uptcambio">
    	<label for="username">Tipo de Cambio</label>
    	<input type="text" name="username" value="<?php echo getFile("tipocambio"); ?>">
    	<button type="submit" id="uptcambio" class="button button-primary button-large">Guardar Cambios</button>
    </form>

    <form action="" method="post" style="margin-top:15px;">
    	<input type="hidden" name="action" value="uptimpuesto">
    	<label for="username">Impuesto</label>
    	<input type="text" name="username" value="<?php echo getFile("impuesto"); ?>">
    	<button type="submit" id="uptcambio" class="button button-primary button-large">Guardar Cambios</button>
    </form>

    <form action="" method="post" style="margin-top:15px;">
    	<input type="hidden" name="action" value="uptmargen">
    	<label for="username">Margen de Ganancia</label>
    	<input type="text" name="username" value="<?php echo getFile("margen"); ?>">
    	<button type="submit" id="uptcambio" class="button button-primary button-large">Guardar Cambios</button>
    </form>

        <button class="button button-secundary button-large" onclick="primera()">PRIMERA CARGA</button>
        <br>
        <button class="button button-secundary button-large" onclick="segunda()">SEGUNDA CARGA</button>
        <br>
        <button class="button button-secundary button-large" onclick="tercera()">TERCERA CARGA</button>
        <br>
        <button class="button button-secundary button-large" onclick="cuarta()">CUARTA CARGA</button>
        <br>

        <button class="button button-danger button-large" onclick="borrar()">BORRAR PRODUCTOS DE PCSERVICE</button>

        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>

			function actualizar(){

            Swal.fire({
                title: '¿Desea actualizar los productos?',
				text: "Se traeran los cambios de las ultimas 24 horas desde la API",

                showCancelButton: true,
                confirmButtonText: 'SI',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch("<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/updateProductsFunction.php")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: result.value,
                        })
                    }
                })

                /*$.get( "<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php", function( result ) {
                    console.log(result)
                });*/

            }

			function updateprice(){

            Swal.fire({
                title: '¿Desea actualizar los productos?',

                showCancelButton: true,
                confirmButtonText: 'SI',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch("<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: result.value,
                        })
                    }
                })

                /*$.get( "<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php", function( result ) {
                    console.log(result)
                });*/

            }

			function updateprice(){

            Swal.fire({
                title: '¿Desea actualizar los precios de los productos?',

                showCancelButton: true,
                confirmButtonText: 'SI',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch("<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: result.value,
                        })
                    }
                })

                /*$.get( "<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php", function( result ) {
                    console.log(result)
                });*/

            }


        function primera(){

            Swal.fire({
                title: '¿Desea realizar la primera carga?',

                showCancelButton: true,
                confirmButtonText: 'SI',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch("<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/getCategorias.php")
                    .then(response => {
                        return response
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: result.value,
                        })
                    }
                })

                /*$.get( "<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php", function( result ) {
                    console.log(result)
                });*/

            }


        function segunda(){

            Swal.fire({
                title: '¿Desea realizar la segunda carga?',

                showCancelButton: true,
                confirmButtonText: 'SI',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch("<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/getSubcategorias.php")
                    .then(response => {
                        return response
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: result.value,
                        })
                    }
                })

                /*$.get( "<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php", function( result ) {
                    console.log(result)
                });*/

            }

        function tercera(){

            Swal.fire({
                title: '¿Desea realizar la tercera carga?',

                showCancelButton: true,
                confirmButtonText: 'SI',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch("<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/pushCreateCategories.php")
                    .then(response => {
                        return response
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: result.value,
                        })
                    }
                })

                /*$.get( "<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php", function( result ) {
                    console.log(result)
                });*/

            }
        function cuarta(){

            Swal.fire({
                title: '¿Desea Cargar las subcategorias?',

                showCancelButton: true,
                confirmButtonText: 'SI',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch("<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/pushCreateSubCategories.php")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: result.value,
                        })
                    }
                })

                /*$.get( "<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php", function( result ) {
                    console.log(result)
                });*/

            }
        function quinta(){

            Swal.fire({
                title: '¿Desea Cargar las imagenes de las categorias?',

                showCancelButton: true,
                confirmButtonText: 'SI',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch("<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/pushCreateCategoriesImages.php")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: result.value,
                        })
                    }
                })

                /*$.get( "<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php", function( result ) {
                    console.log(result)
                });*/

            }
        function borrar(){

            Swal.fire({
                title: '¿Desea Eliminar los prodcutos?',

                showCancelButton: true,
                confirmButtonText: 'SI',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return fetch("<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/deleteAll.php")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: result.value,
                        })
                    }
                })

                /*$.get( "<?php echo get_site_url();?>/wp-content/plugins/pcservice/include/primera.php", function( result ) {
                    console.log(result)
                });*/

            }

            </script>
