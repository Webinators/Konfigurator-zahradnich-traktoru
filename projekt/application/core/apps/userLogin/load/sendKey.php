<?php

require_once('../../../config.php');

use System\Authentication\Components;

try {

$loginForm = new Components\loginForm();
echo "0->".$loginForm->sendKey($_POST["email"]);

} catch(Exception $e){
   echo "1->".$e->getMessage();
}

?>