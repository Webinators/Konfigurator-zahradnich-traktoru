<?php

$path = $_POST['way'];

$txt = fopen($path, "w+");
fwrite($txt, "");
fclose($txt);

?>