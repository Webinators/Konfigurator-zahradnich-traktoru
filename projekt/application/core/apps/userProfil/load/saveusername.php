<?php

require_once("../../../config.php");

use System\Authentication;

$user = Authentication\Users::initialize();

if(isset($_POST['username']) && $_POST['username'] != '' && isset($_POST['usersecondname']) && $_POST['usersecondname'] != '' && $userSession[1] != '')
{

$username = $_POST['username'];
$usersecondname = $_POST['usersecondname'];

try{
  $user->changeUserName($username,$usersecondname);
}catch(Exception $e){
  echo "1->".$e->getMessage();
}

echo "0->Data uložena.";

}

?>