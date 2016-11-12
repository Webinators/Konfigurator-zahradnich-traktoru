<?php

require_once("../../loader.php");

use System\Authentication;

$output = new Output();
$permEx = new Authentication\PermissionsEx();

try {

    $permEx->joinPermissionsToUser($_POST);
    $output->Success("Práva úspěšně změněna");
} catch (Exception $e){
    $output->Error($e->getMessage());
}
?>