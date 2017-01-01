<?php

session_start();

$pokracovani = $_SESSION['pokracovani'];

if($pokracovani != '')
{
$pokracovani = $pokracovani."/";
}

$folder = $_POST['name'];
$folder = "../obrazky/".$pokracovani.$folder;
$folder2 = "../obrazky/".$pokracovani.$folder."/";

if ($handle = opendir($folder)) 
{ 
for (;false !== ($file = readdir($handle));) 
{ 
if($file != "." && $file != "..") 
{ 
unlink($folder2.$file);
} 
} 
closedir($folder); 
}

rmdir($folder);


require('vypsaniobrazkuktextaku.php');
?>