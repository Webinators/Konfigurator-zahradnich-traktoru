<?php
session_start();

$name = $_POST['name'];

$_SESSION['pokracovani'] = $name;

require('vypsaniobrazkuktextaku.php');

?>