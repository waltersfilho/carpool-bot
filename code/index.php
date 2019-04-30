<?php
    require_once "Classes/config/Config.php";
    require_once "Classes/controller/Roteador.php";
    require_once "Classes/config/Connection.php";


    $update = file_get_contents('php://input');
    $updateArray = json_decode($update, TRUE);
    
    Roteador::direcionar($updateArray);
?>

<h2>CaronasBot</h2>
<h4>More @ <a href="https://github.com/waltersfilho/CaronasBot">https://github.com/waltersfilho/CaronasBot</a></h4>
