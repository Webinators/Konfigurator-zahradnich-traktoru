<?php

require_once("../../loader.php");

if(isset($_POST['confirmkey']))
{

$formChecker = new FormChecker();

$key = $_POST['confirmkey'];

try{

$formChecker->checkConfirmCode($key);
echo "0->ok";

}
catch(Exception $e) {
echo "1->".$e->getMessage(); exit;
}
}
?>