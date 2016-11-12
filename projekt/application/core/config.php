<?php

error_reporting(E_ERROR | E_PARSE);

$path = dirname( realpath( __FILE__ ) )."/";

// Get paths (root path,project path)
$r = file_get_contents($path."classes/root/root.txt");

$paths = explode(",",$r);

if(!defined('ROOT')){
    define("ROOT", $paths[0]);
}
if(!defined('PATH_TO_PROJECT')){
    define("PATH_TO_PROJECT", $paths[1]);
}
if(!defined('PATH_TO_REGISTRATURA')){
    define("PATH_TO_REGISTRATURA", $paths[2]);
}

/* DB Connecting settings */

define( "DB_HOST", "localhost,vidlak,vidlak" ); //host
define( "DB_NAME", "vidlak" ); //database name
define( "TAB_USERS", "regUsers"); // Users table name
define( "TAB_PERMITIONS", "Permitions"); // Permitions table name

$fullPath = ROOT.PATH_TO_PROJECT.PATH_TO_REGISTRATURA;

define("AUTOLOADER_PATHS", $fullPath."/classes/root/paths.txt");

require_once($fullPath . "classes/AutoLoader.php");

require_once($fullPath . "classes/admin/Admin.php");
require_once($fullPath . "classes/admin/Permissions.php");
require_once($fullPath . "classes/users/Users.php");

require_once($fullPath . "classes/ObjSet.php");

function __autoload($class_name)
{

   $Loader = AutoLoader::initialize();

   $parts = explode('\\', $class_name);

   if (!class_exists(end($parts))) {

     $classN = $Loader->getClassFile(end($parts));
     require_once($classN);
   }

}

$Loader = AutoLoader::initialize();

$Loader->addPath(PATH_TO_REGISTRATURA."classes/");
$Loader->addPath(PATH_TO_REGISTRATURA."apps/");

//define constants for Users

define( "SERVER_MAIL", "info@kamra.cz" );

//define constants for Database Users

class FormConstants
{

    const default_profil_pic = "default.jpg";
    const users_destination = "Users/";
    const users_profil = "profil/";

    // Constants for users login and registration form
    const form_username = "username";
    const form_username_min_lenght = 3;
    const form_username_max_lenght = 50;
    const form_user_second_name = "usersecondname";
    const form_user_second_name_min_lenght = 3;
    const form_user_second_name_max_lenght = 50;
    const form_password = "password";
    const form_passwordrepeat = "passwordrepeat";
    const form_email = "email";
    const generated_pic = "generated_pic";
    const confirm_code = "confirm_code";

    const admin_mail = "vydra567@seznam.cz";

}

?>