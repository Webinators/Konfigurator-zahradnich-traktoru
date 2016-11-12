<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 27.2.2015
 * Time: 8:53
 */

namespace System\Authentication\Components;

use System\Objects\Collection;

class AdminRegistrationForm extends \FormConstants
{
    use Collection\ObjSet;

    function __construct()
    {
        $this->loadSystem();
        if($this->Admin->checkIfAdminExists()){throw new \Exception('Admin už existuje');}
    }

    public function getAccessForm()
    {

        $checker = new \FormChecker();
        $code = $checker->randString(15);

        $this->Sessions->createSession("AdminAccessCode", $code, true);

        $body = ' Dobrý den, zde je přístupový kód pro admin registraci: ' . $code . '';

        $mail = new \HtmlMimeMail("X-Mailer: Html Mime Mail Class");
        $mail->add_html($body, "");
        $mail->set_charset('utf-8', TRUE);
        $mail->build_message();
        $mail->send(self::admin_mail,self::admin_mail, SERVER_MAIL, SERVER_MAIL, "Sokol admin registrace", "Return-Path: " . SERVER_MAIL . "");
        
        return '
		<div id="registration_obal">
        	<form method="post" action="' . $this->Root->getPathToRegistratura(false,true) . 'load/admin/form/access.php" id="adminRegistrationForm">
                <table>
                <tr><td>Zadejte kód: <input type="text" name="code" id="adminRegistrationAccessInput"/></td><td><div id="adminRegistrationAccessErr"></div></td></tr>
	            <tr><td cols="2" style="text-align: center;"><input type="submit" name="tryAccess" id="adminRegistrationAccess"/></td></tr>
                </table>
		</form>
		</div><script src="' . $this->Root->getPathToRegistratura(false,true) . 'js/admin/form/access.js" type="text/javascript"></script>
        ';

    }

    public function getRegForm($code){

        if($this->checkCode($code, true)) {
            $regForm = new RegistrationForm(true);
            return $regForm->getRegistrationForm();
        } else {
            throw new \Exception('Špatně opsaný přístupový kód!');
        }
    }

    public function checkCode($code = "", $changeToBool = false)
    {

        if ($this->Sessions->sessionExists("AdminAccessCode")) {

            $rightCode = $this->Sessions->getSession("AdminAccessCode");

            if ($rightCode == $code) {

                if($changeToBool) {
                    $this->Sessions->changeSession("AdminAccessCode", "true");
                }
                return true;
            }

        }

        return false;
    }
}

?>