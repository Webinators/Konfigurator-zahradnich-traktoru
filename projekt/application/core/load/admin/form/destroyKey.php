<?php

require_once("../../loader.php");

if(isset($_POST['unlink']))
{

$session = Sessions::initialize();

if($session->sessionExists("AdminAccessCode")) {
   $session->changeSession("AdminAccessCode","");
}

$output = new Output();
$output->Success("0->ok");

}
?>