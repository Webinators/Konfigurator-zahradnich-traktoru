<?php

ob_start();

require_once("../../loader.php");

use System\Authentication\Components;

try {
    $regForm = new Components\RegistrationForm();
    echo "0->".$regForm->getRegistrationForm();
} catch (Exception $e){
    echo "1->".$e->getMessage();
}

ob_end_flush();

?>