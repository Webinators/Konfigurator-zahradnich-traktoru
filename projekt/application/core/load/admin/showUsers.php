<?php

require_once("../loader.php");

use System\Authentication;

$output = new Output();
$admin = Authentication\Admin::initialize();

try {

    $data = $admin->printUsers();
    $output->Data($data);

} catch (Exception $e){
    $output->Error($e->getMessage());
}

?>