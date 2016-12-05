<?php

$path = dirname( realpath( __FILE__ ) )."/";
$project = "rsp/";
$registratura = "core/";

define("PROJECT", $project);
define("ROOT", trim(str_replace(PROJECT," ",$path)));
define("CORE", $registratura);
define("ABS_CORE", ROOT.PROJECT.$registratura);

require_once(ABS_CORE."config.php");

$tree = new TreeDirectory();

define("URL", $tree->getPathToProject(false, true));

$Loader->addPath("apps/");

?>