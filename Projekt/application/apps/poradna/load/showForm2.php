<?php

include("../../../config.php");

try {

$fotogalerie = new Poradna();
echo "0->".$fotogalerie->showAddClanekForm($_POST);

} catch(Exception $e){
  echo "1->".$e->getMessage();
}
?>