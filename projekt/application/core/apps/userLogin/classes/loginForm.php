<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 22.2.2015
 * Time: 10:52
 */

namespace System\Authentication\Components;

use System\Objects\Collection;

class loginForm extends \FormConstants {

    use Collection\ObjSet;
    private $data;

    function __construct(){

        $this->loadSystem();

        $checker = new \FormChecker();
        $checker->newAlphabet();

        $newAlphabet = $this->Sessions->getSession('reg_new_alfabet',true);
        $normalAlphabet = $this->Sessions->getSession('reg_correct_alfabet',true);

        $tree = new \TreeDirectory();

        $formE = new \FormElements();

        $form = \Form::getForm("formTable", $this->Root->getAppPath(__DIR__, false, true).'load/loginuser.php');
        $form->addItem("Email", $formE->Input()->Email("email")->Required(true)->ID("userLogEmail")->Rest('data-url="'.$this->Root->getPathToRegistratura(false, true).'load/users/form/checkemail.php"'));
        $form->addItem("Heslo", $formE->Input()->Password("password")->Required(true)->ID("userLogPassword"));
        $form->addButton($formE->Button()->Submit("","Přihlásit se")->ID("LoggedBarUserLoginFormSubmit"));

        $this->data = '

            <script type="text/javascript">
               var log_normalalphabet = "'.$normalAlphabet.'";
               var log_newalphabet = "'.$newAlphabet.'";
            </script>

            <div id="LoggedBarLoginFormObal">
            '.$form.'

            <link rel="stylesheet" type="text/css" href="'.$this->Root->getAppPath(__DIR__, false, true).'css/login.css">
            <script src="'.$this->Root->getAppPath(__DIR__, false, true).'js/login.js" type="text/javascript"></script>

            <div><a href="'.$this->Root->getAppPath(__DIR__, false, true).'load/forgetPassForm.php" class="ajaxWin">Zapomenuté heslo</a></div>
            </div>
        ';
    }

    public function forgottenPassword(){

        $form = \Form::getForm("FormTable", $this->Root->getAppPath(__DIR__, false, true)."load/generatePass.php");
        $formE = new \FormElements();

        $form->addItem("Zadejte váš email", $formE->Input()->Email("email")->Required(true)->Id("ForgottenPasswordEmail"), false);
        $form->addItem("", $formE->Button()->Submit("", "Poslat kód")->ID("ForgottenPasswordBtn")->_class("ajax")->ContainerStyle("display: none")->formAction($this->Root->getAppPath(__DIR__, false, true)."load/sendKey.php")->formMethod("POST"));
        $form->addItem("Zadejte klíč", $formE->Input()->Text("text")->Required(true));
        $form->addButton($formE->Button()->Submit("upload","Poslat vygenerované heslo")->_class("ajax")->Rest('data-win-message="Byl vám poslán úspěšně email" data-win-closeParent="true"'));

        return $form;

    }

    public function sendKey($email){

        $formC = new \FormChecker();
        $str = $formC->randString();

        if($this->Admin->checkIfEmailExists($email)){

            $this->Sessions->createSession("ForgottenPassKey", $str);

            $predmet = "Kód (zapomenuté heslo)";
            $from = SERVER_MAIL;

            $body = '
                Dobrý den, zde je vygenerovaný kód: '.$str.' <br />
            ';

            $mail = new \HtmlMimeMail("X-Mailer: Html Mime Mail Class");
            $mail->add_html($body, "");
            $mail->set_charset('utf-8', TRUE);
            $mail->build_message();

            if($mail->send($email, $email, $from, $from, $predmet, "Return-Path: $from")){
                return "ok";
            } else {
                throw new \Exception("Email nebyl úspěšně odeslán.");
            }

        } else {
            throw new \Exception("Takový email není registrovaný.");
        }

    }

    public function sendGeneratedPassword($data){

        $key = $this->Sessions->getSession("ForgottenPassKey", true);

        if($key != $data["text"]){
            return $this->User->generatePassword($data["email"]);
        } else {
            throw new \Exception("Zadný klíč se neshoduje s vygenerovaným");
        }

    }

    public function getLoggedForm(){

        return $this->data;

    }

} 