<?php

require_once('../../../config.php');

try {

$mini = new MiniGallery($_POST["destination"]);
$mini->thumbResize($_POST["resizeP"][0])->photoResize($_POST["resizeP"][1])->allowedExtensions($_POST["allowed"]);

$mini->uploadFiles();

} catch(Exception $e){
   echo "1->".$e->getMessage();
}

?>