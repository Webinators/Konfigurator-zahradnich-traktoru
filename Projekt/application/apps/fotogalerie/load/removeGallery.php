<?php

include("../../../config.php");

try {

   $fotogalerie = new PhotoGallery();
   echo "0->".$fotogalerie->removeGallery($_POST);

} catch(Exception $e){
   echo "1->".$e->getMessage();
}
?>