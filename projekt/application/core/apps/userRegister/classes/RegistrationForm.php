<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 27.2.2015
 * Time: 8:53
 */

namespace System\Authentication\Components;

use System\Objects\Collection;

class registrationForm extends \FormConstants {

    use Collection\ObjSet;

    function __construct($access = false){

        $this->loadSystem();

        if(!$this->Admin->checkIfAdminExists()){
            if(!$access){throw new \Exception("Ještě se nelze registrovat.");}
        }

    }

    public function getRegistrationForm(){

        $formChecker = new \FormChecker();
        $formChecker->newAlphabet();
        $session = $this->Sessions;

        $newAlphabet = $session->getSession('reg_new_alfabet', true);
        $normalAlphabet = $session->getSession('reg_correct_alfabet', true);

        $tree = new \TreeDirectory();
        $treePath = $tree->getAppPath(__DIR__, false, true);

        return '

        <script type="text/javascript">
            var reg_normalalphabet = "' . $normalAlphabet . '";
            var reg_newalphabet = "' . $newAlphabet . '";
        </script>

	 <div id="registration_obal">
        <form method="post" action="' . $tree->getAppPath(__DIR__, false, true) . 'load/registeruser.php" id="usersRegistrationForm">
        <table>
        <tr><td>Jméno</td><td><input type="text" name="' . self::form_username . '" placeholder="Jméno" id="regUsername"/></td><td><div id="regUsernameerr"></div></td></tr>
        <tr><td>Příjmení</td><td><input type="text" name="' . self::form_user_second_name . '" placeholder="Příjmení" id="regUsersecondname"/></td><td><div id="regUsersecondNameErr"></div></td></tr>
        <tr><td>Email</td><td><input type="email" name="' . self::form_email . '" placeholder="Email" id="regEmail" autocomplete="off"/></td><td><div id="regEmailerr"></div></td></tr>
        <tr><td>Heslo</td><td><input type="password" name="' . self::form_password . '" placeholder="Heslo" id="regPassword1" autocomplete="off"/></td><td><div id="regPassword1err"></div></td></tr>
        <tr><td>Heslo znovu</td><td><input type="password" name="' . self::form_passwordrepeat . '" placeholder="Heslo znovu" id="regPassword2" autocomplete="off"/></td><td><div id="regPassword2err"></div></td></tr>
        <tr><td colspan="3" align="center"><img src="' . $tree->getPathToRegistratura(false, true) . 'load/newpic.php"/></td></tr>
        <tr><td>Text z obrázku</td><td><input type="text" name="' . self::generated_pic . '" placeholder="Opište text z obrázku" id="regGeneratedpic"/></td><td><div id="regGeneratedpicerr"></div></td></tr>
        <tr><td colspan="3" align="center"><input type="button" name="check" id="regPostmail" value="Pokračovat v registraci"/></td></tr>
        <tr class="registrationNextPart" style="display: none"><td>Potvrzovací kód</td><td><input type="text" name="' . self::confirm_code . '" placeholder="Vložte kód z emailu" id="regConfirmcode"/></td><td><div id="regConfirmErr"></div></td></tr>
        <tr class="registrationNextPart" style="display: none"><td colspan="3" align="center"><input type="submit" name="check" id="usersRegistrationFormBtn"/></td></tr>

        </table>

        </form> <div id="regallerr"></div>
	 </div>

	 <script src="' . $treePath . 'js/registration.js" type="text/javascript"></script>
        ';

    }

} 