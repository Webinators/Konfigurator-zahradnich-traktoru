<?php

require_once('../../../config.php');

use System\Authentication\Components;

try {

$loginForm = new Components\loginForm();
echo "0->".$loginForm->forgottenPassword();

} catch(Exception $e){
   echo "1->".$e->getMessage();
}

?>