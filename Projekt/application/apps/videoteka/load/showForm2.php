<?php

include("../../../config.php");

try {

$fotogalerie = new VideoGallery();
echo "0->".$fotogalerie->showAddVideoForm($_POST);

} catch(Exception $e){
  echo "1->".$e->getMessage();
}
?>