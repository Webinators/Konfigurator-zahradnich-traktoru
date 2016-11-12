<?php

require_once("../../loader.php");

use System\Authentication\Components;
$output = new Output();

$code = $_POST["code"];

if(isset($code)){

    $form = new Components\AdminRegistrationForm();

    if($form->checkCode($code))
    {
        $output->Success("ok");
    } else {
        $output->Error("err");
    }

}

?>