<?php

ob_start();

require_once('../../../config.php');

use System\Authentication;

if(isset($_POST['email']))
{

try {

$admin = Authentication\Admin::initialize();
$admin->loginUser($_POST);

echo "0->ok";

} catch(Exception $e){
   echo "1->".$e->getMessage();
}

}

ob_end_flush();

?>