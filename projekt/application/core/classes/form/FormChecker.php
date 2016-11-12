<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 26.2.2015
 * Time: 17:43
 */

use System\Authentication;

class FormChecker extends FormConstants {

    public function checkName($name=""){

        $name = trim($name);

        if($name != ''){
            if(strlen($name) >= self::form_username_min_lenght){
                if(strlen($name) <= self::form_username_max_lenght) {
                    return stripslashes($name);
                } else {
                    throw new Exception("Vaše jméno je podezřele dlouhé");
                }
            } else {
                throw new Exception("Vaše jméno je podezřele krátké");
            }
        } else {
            throw new Exception("Nevyplnil/a jste jméno");
        }

    }

    public function checkSecondName($name=""){

        $name = trim($name);

        if($name != ''){
            if(strlen($name) >= self::form_username_min_lenght){
                if(strlen($name) <= self::form_username_max_lenght) {
                    return stripslashes($name);
                } else {
                    throw new Exception("Vaše příjmení je podezřele dlouhé");
                }
            } else {
                throw new Exception("Vaše příjmení je podezřele krátké");
            }
        } else {
            throw new Exception("Nevyplnil/a jste příjmení");
        }

    }

    public function checkEmail($email,$noDuplicates = false,$verify = true)
    {

        $email = trim($email);

        if($email != ''){

            if(filter_var(trim($email), FILTER_VALIDATE_EMAIL)){

                if($noDuplicates){

                    if($this->emailExists($email)) {
                        throw new Exception("Takový email už je zaregistrovaný");
                    }

                } else {

			if(!$this->emailExists($email)){
                          throw new Exception("Tento email není zaregistrovaný");
			} 
                }

                return $email;

            } else {
                throw new Exception("Nesprávný email!!!");
            }

        } else {
            throw new Exception("Nevyplnil/a jste email!!!");
        }

    }

    public function emailExists($email)
    {

	 $admin = Authentication\Admin::initialize();

        if($admin->checkIfEmailExists($email)){
            return true;
        }
        
        return false;
    }

    public function checkPassword($password, $passwordN)
    {

        $password = trim($password);

        $session = Sessions::initialize();

        $newAlphabet = explode(",", $session->getSession("reg_new_alfabet", true));
        $correctAlphabet = explode(",", $session->getSession("reg_correct_alfabet", true));

        if ($newAlphabet != '' && $correctAlphabet != '') {

            if ($password != '') {

                if ($passwordN == false) {

                    $password = $this->restorePassword($password, $correctAlphabet, $newAlphabet);
                    return $password;

                } else {

                    if ($this->checkPasswordRepeat($password, $passwordN)) {

                        $password = $this->restorePassword($password, $correctAlphabet, $newAlphabet);
                        return $password;

                    }
                }

            } else {
                throw new Exception("Nevyplnil/a jste heslo!!!");
            }
        } else {
            throw new Exception("Vyskytl se problém při kontrole formuláře!!!");
        }
    }

    public function checkPasswordRepeat($password,$passwordN){

        $password = trim($password);
        $passwordN = trim($passwordN);

        if($passwordN != ''){
            if($passwordN == $password) {
                return true;
            } else {
                throw new Exception("Hesla se neshodují");
            }
        } else {
            throw new Exception("Nevyplnil/a jste heslo!!!");
        }

    }

    public function checkGeneratedPic($code){

        $code = trim($code);

        $session = Sessions::initialize();

        $generated = $session->getSession("Randomstring");
        if($generated != ''){
            if($code != '') {
                if(strtolower($code) != $generated) {
                    throw new Exception("Neopsal/a jste text z obrázku správně!!");
                }
            } else {
                throw new Exception("Neopsal/a jste text z obrázku!!");
            }
        } else {
            throw new Exception("Vyskytl se problém při ověření textu z obrázku, obraťte se na admina!!!");
        }

    }

    public function checkConfirmCode($code){

        $code = trim($code);

$session = Sessions::initialize();

        $generated = $session->getSession("Confirmcode");

        if($generated != ''){
            if($code != '') {
                if($code != $generated) {
                    throw new Exception("Neopsal/a jste potvrzovací kód správně!!");
                }
            } else {
                throw new Exception("Špatně opsaný potvrzovací kód!!");
            }
        } else {
            throw new Exception("Vyskytl se problém při ověření potvrzovacího kódu, obraťte se na admina!!!");
        }

    }

    private function restorePassword($password,$correctAlphabet,$newAlphabet)
    {

        function search_array($key,$array)
        {

            $length = count($array) - 1;

            for($i=0;$i<=$length;$i++)
            {

                if($array[$i] == $key){
                    return $i;
                }else{
                    if($i == $length)
                    {
                        return -1;
                    }
                }
            }
        }

        $passwordArray = str_split(trim($password));
        $passLength = count($passwordArray) - 1;

        $restoredPassword = "";

        for($i=0;$i<=$passLength;$i++)
        {
            $key = search_array($passwordArray[$i],$newAlphabet);

            if($key > -1){
                $restoredPassword .= $correctAlphabet[$key];
            }else{
                $restoredPassword .= $passwordArray[$i];
            }
        }

        return $restoredPassword;

    }

    public function newAlphabet(){

        $letters = array_merge(range('a','z'),range('A','Z'),range('0','9'));

        $arrayLength = count($letters);
        $arrayLength -= 1;

        $newLetters = "";
        $disabled = array();

        for($i=0; $i<=$arrayLength; $i++)
        {

            $newRand = rand(0,$arrayLength);

            while(in_array($newRand, $disabled))
            {
                $newRand = rand(0,$arrayLength);
            }
            if($newLetters == ''){
                $newLetters .= ''.$letters[$newRand].'';
            } else {
                $newLetters .= ','.$letters[$newRand].'';
            }
            $disabled[$i] = $newRand;
        }

        for($i=0;$i<=$arrayLength;$i++)
        {
            $letters[$i] = $letters[$i];
        }

        $session = Sessions::initialize();

        if($session->sessionExists("reg_new_alfabet") && $session->sessionExists("reg_correct_alfabet"))
        {
            $session->changeSession("reg_new_alfabet", $newLetters);
            $session->changeSession("reg_correct_alfabet", join(",", $letters));
        } else {
            $session->createSession("reg_new_alfabet", $newLetters);
            $session->createSession("reg_correct_alfabet", join(",", $letters));
        }

    }

    public function randString($length = 10)
    {

        $str = "";
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }


} 