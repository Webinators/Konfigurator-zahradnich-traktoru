<?php

require_once("loader.php");

$icon = new Icons();
$data = $icon->loadIcons();
echo $data;

?>