<?php

ob_start();

require_once("../../loader.php");

use System\Authentication\Components;
$output = new Output();

if(isset($_POST["code"]) && $_POST["code"] != '') {

    $code = $_POST["code"];

    $form = new Components\AdminRegistrationForm();

    try {
        $data = $form->getRegForm($code);
        $output->Data($data);
    } catch (Exception $e) {
        $output->Error($e->getMessage());
    }

} else {
    $output->Error("Nebyl odeslán žádný kód");
}

ob_end_flush();

?>