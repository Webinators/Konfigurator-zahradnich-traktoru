<?php

use System\Authentication;

$user = Authentication\Users::initialize();

if($user->userIsLogged())
{
    echo "0->ok";
}
else
{
    echo "1->not logged";
}

?>