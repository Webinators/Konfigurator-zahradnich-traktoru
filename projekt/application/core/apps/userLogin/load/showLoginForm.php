<?php

ob_start();

require_once("../../../config.php");

use System\Authentication\Components;

$output = new Output();

$login = new Components\loginForm();
$data = $login->getLoggedForm();

$output->Data($data);

ob_end_flush();

?>