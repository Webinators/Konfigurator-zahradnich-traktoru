<?php

include("../../../config.php");

try {

$fotogalerie = new Poradna();
echo "0->".$fotogalerie->addNewGallery($_POST);

} catch(Exception $e){
  echo "1->".$e->getMessage();
}
?>