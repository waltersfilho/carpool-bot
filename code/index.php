<?php
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
    
    require_once "Classes/config/Config.php";
    require_once "Classes/controller/Roteador.php";
    require_once "Classes/config/Connection.php";


    $update = file_get_contents('php://input');
    $updateArray = json_decode($update, TRUE);
    
    $texto = Roteador::direcionar($updateArray);

    echo $texto
?>

<h2>CaronasBot</h2>
<h4>More @ <a href="https://github.com/waltersfilho/CaronasBot">https://github.com/waltersfilho/CaronasBot</a></h4>
