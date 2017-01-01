<?php

$filedocx = $_FILES['docx']['name'];
$fileodt = $_FILES['odt']['name'];

$files = glob('../predimage/word/media/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}

if($filedocx != '')
{
$picture = $filedocx;   
$rename = strtr($picture, " ěščřžýáíéňťďůúýóöäüóôEŠČŘŽÝÁÍÉŇŤĎŮÚÝÓÖÄÜÓÔ", "_escrzyaientduuyooauooESCRZYAIENTDUUYOOAUOO");

move_uploaded_file($_FILES['docx']['tmp_name'], "../predimage/".$rename);

$file = "../predimage/".$rename; 

require('../docxtohtml/CreateHTML.php');

unlink($file);
}


if($fileodt != '')
{

$picture = $fileodt;   
$rename = strtr($picture, " ěščřžýáíéňťďůúýóöäüóôEŠČŘŽÝÁÍÉŇŤĎŮÚÝÓÖÄÜÓÔ", "_escrzyaientduuyooauooESCRZYAIENTDUUYOOAUOO");

move_uploaded_file($_FILES['odt']['tmp_name'], "../predimage/".$rename);

$file = "../predimage/".$rename; 

require('../odttohtml/odt.php');

unlink($file);

}

?>