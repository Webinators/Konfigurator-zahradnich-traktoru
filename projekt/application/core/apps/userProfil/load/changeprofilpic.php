<?php

require_once("../../../config.php");

use System\Authentication;

$user = Authentication\Users::initialize();

try {
   echo "0->".$user->changeProfilPic($_POST);
} catch(Exception $e){
   echo "1->".$e->getMessage();
}

?>