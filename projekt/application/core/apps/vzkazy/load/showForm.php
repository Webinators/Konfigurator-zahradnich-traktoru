<?php

require_once("../../../config.php");

try {

    $vzkazy = new Vzkazy();
    echo "0->".$vzkazy->showAddVzkazForm();

} catch(Exception $e){
    echo "1->".$e->getMessage();
}
?>