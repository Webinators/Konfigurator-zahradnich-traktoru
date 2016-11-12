<?php
namespace System\Authentication;

class Admin extends \FormConstants
{
    private static $Admin = null;

    private $extendetSalt = "Zo4rU5Z1YyKJAASY0PT6EUg7BBYdlEhPaNLuxAwU8lqu1ElzHv0Ri7EM6irpx5w";
    private $database;

    private function __construct()
    {
        $this->database = new \Database(DB_HOST, DB_NAME);
        $this->checkTables();
    }

    public static function initialize()
    {
        if (!isset(self::$Admin)) {
            self::$Admin = new Admin();
        }

        return self::$Admin;
    }

    private function checkTables()
    {

        if (!$this->database->tableExists(TAB_USERS)) {

            $columns = '
            ID_uzivatele INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Datum_registrace DATE NOT NULL,
            Jmeno VARCHAR(70) NOT NULL,
            Prijmeni VARCHAR(70) NOT NULL,
            Email VARCHAR(40) NOT NULL,
            Heslo VARCHAR(100) NOT NULL,
            Profilovka VARCHAR(50) NOT NULL,
            Post VARCHAR(10) NOT NULL,
            Prava TEXT,
            Datum_narozeni VARCHAR(10),
            Prezdivka VARCHAR(30)
            ';

            $this->database->createTable(TAB_USERS, $columns);

        }

        if (!$this->database->tableExists(TAB_PERMITIONS)) {

            $columns = '
            ID_prava INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Pravo VARCHAR(50) NOT NULL
            ';

            $this->database->createTable(TAB_PERMITIONS, $columns);

        }
    }

    public function checkIfAdminExists()
    {

        $this->database->addWherePart("Post", "LIKE", "Admin");
        $this->database->selectFromTable("ID_uzivatele", TAB_USERS);
        $result = $this->database->countRows();

        if ($result > 0) {
            return true;
        }

        return false;
    }

    public function checkIfEmailExists($email){

        $attributes = "ID_uzivatele";
        $this->database->addWherePart("Email","LIKE",$email);
        $this->database->selectFromTable($attributes, TAB_USERS);

        if($this->database->countRows() > 0){
            return true;
        }

        return false;
    }

    public function hashPass($name, $pass){

        $salt = $name . $this->extendetSalt;
        return hash('sha256', $salt . $pass . $salt);

    }

    private function checkUserBeforeRegistration($data)
    {

        $formChecker = new \FormChecker();

        $name = $formChecker->checkName($data[self::form_username]);
        $secondName = $formChecker->checkSecondName($data[self::form_user_second_name]);
        $email = $formChecker->checkEmail($data[self::form_email],true);
        $password = $formChecker->checkPassword($data[self::form_password], $data[self::form_passwordrepeat]);
        $formChecker->checkConfirmCode($data[self::confirm_code]);
        $formChecker->checkGeneratedPic($data[self::generated_pic]);

        $password = $this->hashPass($email, $password);

        return (array($name, $secondName, $email, $password));

    }

    public function registerUser($data)
    {

        $userType = "User";
        $session = \Sessions::initialize();

        if (!$this->checkIfAdminExists()) {

            if ($session->getSession("AdminAccessCode", false) == "true") {
                $userType = "Admin";
            } else {
                throw new \Exception("Admin ještě není zaregistrovaný");
            }
        }

        $data = $this->checkUserBeforeRegistration($data);

        $check = new \FormChecker();

        if ($check->emailExists($data[2])) {
            throw new \Exception("Uživatel s tímto emailem už je zaregistrovaný");
        }

        $values = array(date("Y-m-d H:i:s"), $data[0], $data[1], $data[2], $data[3], $userType);

        $this->database->insertIntoTable("Datum_registrace, Jmeno, Prijmeni, Email, Heslo, Post", TAB_USERS, $values);
        $userId = $this->database->getLasInsertedId();

        $treeDir = new \TreeDirectory();
        $commander = new \FileCommander();
        $commander->setPath($treeDir->getPathToRegistratura(true).self::users_destination,false);

        $commander->addDir($userId);
        $commander->moveToDir($userId);

        $commander->addDir(self::users_profil);
        $commander->moveToDir(self::users_profil);

        $commander->copyFileFrom(self::default_profil_pic, $treeDir->getPathToRegistratura(true,false)."utilities");

        $this->database->addWherePart("ID_uzivatele", "LIKE", $userId);
        $this->database->updateTable(TAB_USERS, "Profilovka", array("profil.jpg"));

        $session->removeSession("reg_new_alfabet");
        $session->removeSession("reg_correct_alfabet");

    }

    private function checkUserBeforeLogin($data)
    {
        $formChecker = new \FormChecker();

        $email = $formChecker->checkEmail($data[self::form_email]);
        $password = $formChecker->checkPassword($data[self::form_password], false);

        if($this->checkIfEmailExists($email)){

            $this->database->addWherePart("Email","=",$email);
            $this->database->selectFromTable("Email",TAB_USERS);
            $data = $this->database->getRows();

            $password = $this->hashPass($data[0]["Email"], $password);

            return (array($email,$password));

        } else {
            throw new \Exception("Takový uživatel s tímto emailem není zaregistrován!!!");
        }
    }

    public function loginUser($data)
    {
        $values = $this->checkUserBeforeLogin($data);

        $this->database->addWherePart("Email", "=", $values[0]);
        $this->database->addWherePart("AND", "Heslo", "=", $values[1]);
        $this->database->selectFromTable("ID_uzivatele,Post", TAB_USERS);

        if ($this->database->countRows() > 0) {

            $data = $this->database->getRows();

            $session = \Sessions::initialize();

            $session->createSession("logged", "true");
            $session->createSession("user_id", $data[0]['ID_uzivatele']);
            $session->createSession("post", $data[0]['Post']);

            $session->createSession("logged", "true");
            $session->createSession("user_id", $data[0]['ID_uzivatele']);
            $session->createSession("post", $data[0]['Post']);

            $session->removeSession("reg_new_alfabet");
            $session->removeSession("reg_correct_alfabet");

        } else {
            throw new \Exception("Špatné heslo nebo email");
        }

        $this->database->clear();
    }

    public function printUsers(){

        $permission = Permissions::initialize();
        $user = Users::initialize();

        if($permission->userHasAuthorization("Správa uživatelů")) {

            $tree = new \TreeDirectory();

            $this->database->selectFromTable("ID_uzivatele,Jmeno,Prijmeni,Profilovka", TAB_USERS, "", "", "", "Jmeno->ASC");
            $data = $this->database->getRows();

            $output = '<b>Seznam uživatelů:</b> <br /> <input type="search" id="usersSearchUser" placeholder="najít uživatele"/> <br /><br /> <form method="post" action="' . $tree->getPathToRegistratura(false,true). 'load/admin/removeUsers.php" id="usersRemoveUser"><table class="basicTable">';

            for ($i = 0; $data[$i]; $i++) {
                $output .= '<tr title="Zobrazit profil uživatele" id="showLoggedUserProfil" style="cursor: pointer;" data-user-id="'.$data[$i]['ID_uzivatele'].'" class="usersListRow"><td style="width: 30px;"><input type="checkbox" name="value[' . $i . ']" value="' . $data[$i]["ID_uzivatele"] . '"></td><td style="width: 30px;"><img width="30px" src="'.$user->getProfil($data[$i]["ID_uzivatele"]).'" title="Profilový obrázek" alt="Profilový obrázek"/></td><td>' . $data[$i]["Jmeno"] . ' ' . $data[$i]["Prijmeni"] . '</td></tr>';
            }

            $output .= '</table><br /><br /><input style="margin: 0px auto;" id="RemoveUsersBtn" type="submit" value="Odebrat uživatele"/></form>

             <script type="text/javascript">

                $(document).on("click","#RemoveUsersBtn",function(e){
                    mainWindow.confirm($(this),"Opravdu chcete odebrat zvolené uživatele?",e,function(data){
                      mainWindow.alert(undefined,{content: "fsdfdsfsd"});
                    });
                });

                $(document).on("keyup","#usersSearchUser",function(e){

                    var data = $(this).val();

                    $("#usersRemoveUser").find("tr").each(function(){

                        var user = $(this).find("td:nth-child(3)").text(); $(this).show();

                        var reg = new RegExp("^"+data,"gi");

                        if(!reg.test(user)){
                            $(this).hide();
                        }

                    });

                });

                $(document).on("mouseover",".usersListRow",function(){
                    $(this).css({"background-color":"#2e2e2e"});
                });

                $(document).on("mouseout",".usersListRow",function(){
                    $(this).css({"background-color":"#ffffff"});
                });


            </script>
            ';

            return $output;


        } else {
            throw new \Exception("Nemáte práva pro správu uživatelů");
        }
    }

}

?>