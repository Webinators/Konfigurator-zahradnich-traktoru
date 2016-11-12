<?php

require_once("../../loader.php");

if(isset($_POST['key']))
{

$formChecker = new FormChecker();

$key = $_POST['key'];

try{
$formChecker->checkGeneratedPic($key);
echo "0->ok";
}
catch(Exception $e) {
echo "1->".$e->getMessage(); exit;
}

}
?>