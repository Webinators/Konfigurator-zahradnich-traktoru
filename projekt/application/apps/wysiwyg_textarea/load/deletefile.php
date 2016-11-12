<?php

session_start();

$pokracovani = $_SESSION['pokracovani'];

if($pokracovani != '')
{
$pokracovani = $pokracovani."/";
}

$file = $_POST['name'];

if($file != '')
{
$file = "../obrazky/".$pokracovani.$file;

unlink($file);
}

require('vypsaniobrazkuktextaku.php');
?>