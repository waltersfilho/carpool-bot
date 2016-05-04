<?php
    require_once "Classes/Config.php";
    require_once "Classes/Roteador.php";
    require_once "Classes/Connection.php";


    $update = file_get_contents('php://input');
    $updateArray = json_decode($update, TRUE);
    
    Roteador::direcionar($updateArray);
?>

<h2>CaronasBot</h2>
<h4>More @ <a href="https://github.com/filipebarretto/CaronasBot">https://github.com/filipebarretto/CaronasBot</a></h4>
