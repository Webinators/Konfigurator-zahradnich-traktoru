<?php
require_once('../loader.php');

use System\Authentication;

$user = Authentication\Users::initialize();

if(isset($_POST['text']) && $user->userIsLogged())
{

$text = $_POST['text'];
$text = ucfirst($text);

$data = $user->searchUsers($text);

echo "0->".$data;

}
?>