<?php

ob_start();

require_once("../../loader.php");

use System\Authentication\Components;

$output = new Output();

try {

    $regF = new Components\AdminRegistrationForm();
    $data = $regF->getAccessForm();

    $output->Data($data);

} catch (Exception $e){
    $output->Error($e->getMessage());
}

ob_end_flush();
    
?>