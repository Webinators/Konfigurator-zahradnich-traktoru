<?php

$path = dirname( realpath( __FILE__ ) )."/";
$project = "rsp/";
$registratura = "core/";

define("PATH_TO_PROJECT", $project);
define("ROOT", trim(str_replace(PATH_TO_PROJECT," ",$path)));
define("PATH_TO_REGISTRATURA", $registratura);
define("FULL_PATH_TO_REGISTRATURA", ROOT.PATH_TO_PROJECT.$registratura);

$r = file_get_contents(FULL_PATH_TO_REGISTRATURA."classes/root/root.txt");

if($r != ROOT.",".PATH_TO_PROJECT."," . PATH_TO_REGISTRATURA) {
    $f = fopen(FULL_PATH_TO_REGISTRATURA . "classes/root/root.txt", "w");
    $f = fwrite($f, ROOT . "," . PATH_TO_PROJECT . "," . PATH_TO_REGISTRATURA);
    fclose($f);
}

require_once(FULL_PATH_TO_REGISTRATURA."config.php");

$Loader->addPath("apps/");

?>