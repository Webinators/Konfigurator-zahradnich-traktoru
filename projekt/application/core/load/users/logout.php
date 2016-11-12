<?php

require_once('../loader.php');

use System\Authentication;

if(isset($_GET['logout']) && $_GET['logout'] != '')
{

$user = Authentication\Users::initialize();
$user->logoutUser();

}

?>