<?php

ob_start();

require_once("../../loader.php");

use System\Authentication;

$output = new Output();

$admin = Authentication\Admin::initialize();

try{
  $admin->registerUser($_POST);
  $output->Success("ok");
}
catch(Exception $e) {
  $output->Error($e->getMessage());
}

ob_end_flush();

?>