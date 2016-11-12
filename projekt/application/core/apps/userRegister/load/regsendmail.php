<?php

require_once("../../../config.php");

if(isset($_POST['email']))
{

$formChecker = new FormChecker();

$key = $formChecker->randString(10);

$email = $_POST['email'];
$predmet = "Registrace";
$from = SERVER_MAIL;

session_start(); 
session_regenerate_id(); 
ini_set('session.cookie_httponly', true); 

$_SESSION['Confirmcode'] = $key;

$body =
'
Dobrý den, zde je potvrzovací kód pro registraci: '.$key.'.
';

$mail = new HtmlMimeMail("X-Mailer: Html Mime Mail Class");
$mail->add_html($body, "");
$mail->set_charset('utf-8', TRUE);
$mail->build_message();
if($mail->send($email, $email, $from, $from, $predmet, "Return-Path: $from")){
echo "0->ok";
} else {
echo "1->Email nebyl úspěšně odeslán";
}

}
?>