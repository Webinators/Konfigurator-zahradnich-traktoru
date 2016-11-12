<?php

ob_start();

require_once("../../../config.php");

use System\Authentication;

$user = Authentication\Users::initialize();

if($user->userIsLogged())
{

    if(isset($_GET['id'])){
        $id = $_GET['id'];
    } else {
        $id = $user->getUserSession("user_id");
    }

    $profil = $user->getProfil($id);

ob_end_clean();

$pathA = explode("/",$profil);
$file = array_pop($pathA);
$path = implode("/", $pathA);

$imagesE = new ImgEdit();
$imagesE->setInputDir($path);
$imagesE->setOutputDir($path);

$image = $imagesE->loadImage($file);
$image->show();

}
?>