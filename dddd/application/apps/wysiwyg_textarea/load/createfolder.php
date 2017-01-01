<?php
session_start();

$pokracovani = $_SESSION['pokracovani'];

if($pokracovani != '')
{
$pokracovani = $pokracovani."/";
}

$directoryname = "new folder";
$i = 1;

while(file_exists("../obrazky/$pokracovani$directoryname"))
{
$directoryname = "new folder(".$i.")";
$i++;
}

umask(0000);
mkdir("../obrazky/$pokracovani$directoryname", 0777);

require('vypsaniobrazkuktextaku.php');
?>