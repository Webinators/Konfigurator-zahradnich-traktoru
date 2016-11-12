<?php
require_once("../../loader.php");

if(isset($_POST['name']))
{

$formChecker = new FormChecker();

$name = $_POST['name'];

try{

if($formChecker->checkName($name))
{
echo "0->ok";
}

}
catch(Exception $e) {
echo "1->".$e->getMessage(); exit;
}

}
?>