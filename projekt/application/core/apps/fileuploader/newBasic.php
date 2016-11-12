<?php

require("../../load/loader.php");

$name = $_POST['name'];
$maxFiles = $_POST['maxFiles'];
$maxFileSize = $_POST['maxFileSize'];
$allowedFormats = $_POST['allowedFormats'];

$fileuploader = new FileUploaderInput($maxFiles,$maxFileSize,$allowedFormats);
echo $fileuploader->classic($name);

?>