<?php

namespace System\Authentication;

use System\Authentication\Components;
use System\Authentication;

class Users extends \FormConstants
{

    private $database = null;
    private static $User = null;

    private function __construct(){

        $this->database = $this->database = new \Database(DB_HOST, DB_NAME);

        $permission = Permissions::initialize();

        $permission->addNewCategory("Správa uživatelů");
        $permission->addNewPermission("Nastavení práv","Správa uživatelů");
        $permission->addNewPermission("Mazání uživatelů","Správa uživatelů");

        $permission->addNewCategory("Správa obrázků");
        $permission->addNewPermission("Úprava obrázků","Správa obrázků");
        $permission->addNewPermission("Změnit obrázek","Správa obrázků");
        $permission->addNewPermission("Odebrat obrázek","Správa obrázků");
    }

    public static function initialize()
    {
        if (!isset(self::$User)) {
            self::$User = new Users();
        }

        return self::$User;
    }

    public function getUserSession($name = ""){

        $session = \Sessions::initialize();

        if($name != '') {
            $data = $session->getSession($name, true);
        } else {
            $data = array($session->getSession("logged", false), $session->getSession("user_id", false), $session->getSession("post", false));
        }

        return $data;
    }

    public function getUserFromId($id){


        $this->database->addWherePart("ID_uzivatele", "=", $id);
        $this->database->selectFromTable("Jmeno,Prijmeni", TAB_USERS);
        $data = $this->database->getRows();

        return $data[0]["Jmeno"] . " " . $data[0]["Prijmeni"];

    }

    public function getLoggedUserName()
    {

        $userID = $this->getUserSession("user_id");

        $this->database->addWherePart("ID_uzivatele", "=", $userID);
        $this->database->selectFromTable("Jmeno,Prijmeni", TAB_USERS);
        $data = $this->database->getRows();

        return $data[0]["Jmeno"] . " " . $data[0]["Prijmeni"];
    }

    public function userIsLogged(){

        $session = $this->getUserSession();

        if (is_array($session)) {
            if ($session[0] == true && $session[1] != '' && $session[2] != '') {
                if ($session[2] == "User" || $session[2] == "Admin") {
                    return true;
                }
            }
        }

        return false;
    }

    public function userIsAdmin($id = 0){

        if($id < 1){

            $session = $this->getUserSession();

            if ($this->userIsLogged()) {
                if ($session[2] == "Admin") {
                    return true;
                }
            }

        } else {

            $this->database->addHavingPart("ID_uzivatele","=",$id);
            $this->database->selectFromTable("Post",TAB_USERS);
            $data = $this->database->getRows();

            if($data[0]["Post"] == "Admin"){
                return true;
            }
        }

        return false;
    }

    public function buildUserBar(){

        if($this->userIsLogged()){

            $userId = $this->getUserSession("user_id");

            $bar = new Components\loggedBar(true,$userId);
            $userBar = $bar->getLoggedBar();
            return $userBar;

        } else {

            $formChecker = new \FormChecker();
            $formChecker->newAlphabet();

            $bar = new Components\loggedBar(false,null);
            $userBar = $bar->getLoggedBar();
            return $userBar;

        }

    }

    public function generatePassword($email){

        $formc = new \FormChecker();
        $pass = $formc->randString();

        $attributes = "Jmeno";
        $this->database->addWherePart("Email", "=", $email);
        $this->database->selectFromTable($attributes, TAB_USERS);
        $data = $this->database->getRows();
        $count = $this->database->countRows();

        if($count == 1){

            $admin = Admin::initialize();
            $passN = $admin->hashPass($data[0]["Jmeno"], $pass);

            $this->database->addWherePart("Email", "=", $email);
            $this->database->updateTable(TAB_USERS,"Heslo",array($passN));

            $predmet = "Nové heslo";
            $from = SERVER_MAIL;

            $body = '
                Dobrý den, zde je nově vygenerované heslo: '.$pass.' <br />
                Prosíme, hned po přihlášení si jej změňte na jiné.
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

    public function searchUsers($data)
    {

        $attributes = "ID_uzivatele,Jmeno,Prijmeni,Email,Profilovka";
        $this->database->addWherePart("Jmeno", "LIKE", "" . $data . "%");
        $this->database->addWherePart("OR", "Prijmeni", "LIKE", "" . $data . "%");
        $this->database->selectFromTable($attributes, TAB_USERS, "", "Jmeno->ASC");
        $data = $this->database->getRows();
        $number = $this->database->countRows();

        $result = "";

        if ($number > 0) {
            for ($i = 0; $i < $number; $i++) {

                $result .= '
                    <a id="showLoggedUserProfil" data-user-id="' . $data[$i]['ID_uzivatele'] . '" title="Zobrazit profil" alt="Zobrazit profil">
                    <div class="LoggedBarsSearchedUserBox">
                    <table border="0">
                    <tr><td class="LoggedBarsSearchedUserImgBackground" rowspan="2"><img src="' . $this->getProfil($data[$i]['ID_uzivatele']) . '" height="20px" alt="Profilový obrázek" title="Profilový obrázek"/></td><td>' . $data[$i]['Jmeno'] . ' ' . $data[$i]['Prijmeni'] . '</td></tr>
                    <tr><td>' . $data[$i]['Email'] . '</td></tr>
                    </table>
                    </div>
                    </a>
                ';

            }
        } else {
            $result .= '
                <div class="LoggedBarsSearchedUserBox" style="width: 100%;">
                Nebyl nalezen žádný výsledek.
                </div>
                ';
        }

        return $result;
    }

    public function buildUserProfil($userId = "")
    {
        if($this->userIsLogged()) {

            $profil = new Components\Profil($userId);
            return $profil->getUserProfil();

        }else{
            throw new \Exception("Nejste přihlášený");
        }

    }

    public function changeUserName($name, $secondName)
    {

        $formChecker = new \FormChecker();

        $userId = $this->getUserSession("user_id");

        $userName = $formChecker->checkName($name);
        $secondName = $formChecker->checkSecondName($secondName);

        $this->database->addWherePart("ID_uzivatele", "=", $userId);
        $this->database->updateTable(TAB_USERS, "Jmeno, Prijmeni", array($userName, $secondName));
    }

    public function changeUserDetails($data)
    {

        if ($this->userIsLogged()) {

            $userID = $this->getUserSession("user_id");
            $this->database->addWherePart("ID_uzivatele", "=", $userID);

            $attributes = "";
            $values = array();

            foreach ($data as $key => $value) {

                if ($attributes != '') {
                    $attributes .= ',' . $key;
                } else {
                    $attributes .= $key;
                }

                array_push($values, $value);

            }

            $this->database->updateTable(TAB_USERS, $attributes, $values);
        }
    }

    public function changeProfilPic($data){

        if($this->userIsLogged())
        {

            $tree = new \TreeDirectory();

            $autocrop = $data['autocrop'];
            $userSession = $this->getUserSession("user_id");
            $profil = $tree->getPathToRegistratura(true,false).self::users_destination.$userSession."/".self::users_profil;

            $uploader = new \FileUploader($profil,true,false);
            $uploader->setLimits("3MB",1,"jpg,png,gif,jpeg");
            $uploader->UploadNewFile($_FILES['newprofil'],"profil",true,true,false,"");

            $filename = $uploader->getFilesNames();
            $pathtoprofil = $profil.$filename[0];
            $result = $uploader->getResult();

            if(empty($result[1]))
            {

                if($autocrop == true){

                    $filepath = $pathtoprofil;

                    $editor = new \ImagesEditor($filepath,$filepath,false);
                    $editor->resizeImage(384,526,"cropp");

                    return "0|".$result[0][0]."";

                } else {

                    $filepath = $tree->getPathToRegistratura(false,true).$pathtoprofil;
                    return "1|".$filepath."";

                }

            } else {

                return "2|".$result[1][0]."";

            }

        }

    }

    public function getProfil($id){

        $tree = new \TreeDirectory();

        $path = $tree->getPathToRegistratura().self::users_destination.$id."/".self::users_profil;

        $commander = new \FileCommander();
        $commander->setPath($path);

        $file = $commander->getFiles();

        return $path.$file[0];

    }

    public function logoutUser(){

        $session = \Sessions::initialize();

        $session->removeSession("logged");
        $session->removeSession("user_id");
        $session->removeSession("post");

    }
}