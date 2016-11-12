<?php

require_once('../../../config.php');

use System\Authentication\Components;

try {

$loginForm = new Components\loginForm();
   echo "0->".$loginForm->sendGeneratedPassword($_POST);
} catch(Exception $e){
   echo "1->".$e->getMessage();
}

?>