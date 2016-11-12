<?php

require_once("../../../config.php");

use System\Authentication;

$user = Authentication\Users::initialize();

$show = $_POST['showprofil'];
$id = $_POST['id'];

if($show == "true")
{

if($id != ''){
  $data = $user->buildUserProfil($id);
} else {
  $data = $user->buildUserProfil();
}


echo "0->".$data;   

}

?>