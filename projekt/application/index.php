<?php
ob_start();

$info = $_GET['info'];
?>


<!DOCTYPE html>
<html lang="cs">
<head>

    <meta http-equiv="Content-type" content="application/html; charset=utf-8" />

    <link rel="stylesheet" href="apps/fotogalerie/css/lightbox.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/styly.css" type="text/css" media="all" />

    <title>Konfigurator app</title>

</head>

<body>

<div style="margin: 0px auto;width: 900px;">

    <?php

    session_start();

    include("config.php");
    require("".FULL_PATH_TO_REGISTRATURA."Uzivatele.php");

    ?>

    <?php

    if(isset($_GET["page"])) {

        $page = $_GET["page"];

        if (file_exists("pages/".$page.".php")){

            include_once("pages/" . $page . ".php");

        } else {

            $fileC = new FileCommander();
            $fileC->setPath("apps");

            $apps = $fileC->getDirs();

            foreach ($apps as $app){

                if(strpos($page,$app) !== false){

                    if(str_replace($app,"",$page) != '') {
                        $url = explode("/", str_replace($app . "/", "", $page));
                        define("PATH",$path = $fileC->getActualPath() . "/" . $app . "/pages/" . (count($url) > 1 ? join("/", $url) : $app . "/" . join("/", $url)) . ".php");
                        define("URL",$fileC->getActualPath(false,true). "/" . $app ."/");
                    } else {
                        define("PATH",false);
                    }
                    require_once($fileC->getActualPath()."/".$app."/pages/layout.php");


                    $file = $fileC->getActualPath()."/".$app."/pages/".str_replace($app."/","",$page).".php";

                    if(file_exists($file)){
                        include_once($file);
                        break;
                    }

                }

            }

        }

    } else {
        include("pages/uvod.php");
    }
    ?>

    <script type="text/javascript" src="apps/fotogalerie/js/lightbox.min.js"></script>

</div>

</body>
</html>