<?php

ob_start();

require_once("../../loader.php");

$admin = new Admin();

try{
    $admin->registerAdmin($_POST);
}
catch(Exception $e) {
    echo $e->getMessage();
}

echo 0;

ob_end_flush();

?>