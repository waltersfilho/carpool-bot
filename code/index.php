<?php
    require_once "Classes/Config.php";
    require_once "Classes/Roteador.php";
    require_once "Classes/Connection.php";


    $update = file_get_contents('php://input');
    $updateArray = json_decode($update, TRUE);
    
    Roteador::route($updateArray);
?>

<h2>CaronasBot</h2>
<h4>More @ <a href="https://github.com/filipebarretto/php-carpool-bot">https://github.com/filipebarretto/php-carpool-bot</a></h4>
