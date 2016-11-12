<?php

require_once("../../../config.php");

use System\Authentication;

$user = Authentication\Users::initialize();

$user->changeUserDetails($_POST);

echo "0->Údaje úspěšně změněny";

?>