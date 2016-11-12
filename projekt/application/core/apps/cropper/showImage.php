<?php

require_once("../../load/loader.php");

$path = $_GET['path'];

$images = new ImagesEditor();
$img = $images->loadImage($path);
$img->show();

?>