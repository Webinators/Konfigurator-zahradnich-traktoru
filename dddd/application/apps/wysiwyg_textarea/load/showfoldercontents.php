<?php
session_start();

$name = $_POST['name'];

$pokracovani = $_SESSION['pokracovani'];

if($pokracovani != '')
{
$_SESSION['pokracovani'] = $pokracovani."/".$name;
}
else
{
$_SESSION['pokracovani'] = $name;
}

require('vypsaniobrazkuktextaku.php');

?>