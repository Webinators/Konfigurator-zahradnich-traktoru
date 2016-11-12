<?php

require_once("../../loader.php");

if(isset($_POST['email']))
{

$formChecker = new FormChecker();

$mail = $_POST['email'];

if(isset($_POST['noduplicate'])){
   $noDuplicate = $_POST['noduplicate'];
} else {
   $noDuplicate = false;
}

try{

$formChecker->checkEmail($mail,$noDuplicate,true);
echo "0->ok";

} catch(Exception $e) {
echo "1->".$e->getMessage(); exit;
}

}
?>