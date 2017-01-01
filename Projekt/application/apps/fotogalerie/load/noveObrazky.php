<?php

include("../../../config.php");

try{

    $fotogalerie = new PhotoGallery();
    $fotogalerie->uploadNewImages($_POST);

    echo "0->ok";

} catch (Exception $e){
    echo "1->".$e->getMessage();
}

?>
