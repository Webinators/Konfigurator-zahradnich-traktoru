<?php

include("../../../config.php");

try {
	$fotogalerie = new PhotoGallery();
	echo "0->".$fotogalerie->saveGallery($_POST);
} catch(Exception $e){
	echo "1->".$e->getMessage();
}
?>