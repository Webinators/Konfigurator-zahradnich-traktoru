<?php

namespace System\Authentication;

class Permissions
{

    private static $Permission = null;
    protected $database = null;

    protected $permissions = array();
    protected $countP = 0;
    protected $categories = array();
    protected $countK = 0;

    protected $userP = array();

    protected function __construct(){

        try {
            $this->database = $this->database = new \Database(DB_HOST, DB_NAME);
            $this->checkTables();
        } catch(\Exception $e) {
            Throw new \Exception($e->getMessage());
        }

    }

    public static function initialize()
    {
        if (!isset(self::$Permission)) {
            self::$Permission = new Permissions();
        }

        return self::$Permission;
    }

    private function checkTables()
    {

        if (!$this->database->tableExists("PermissionsK")) {

            $this->database->createTable("PermissionsK", '
                ID_kategorie INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                Nazev_k varchar(50) NOT NULL
            ');
        }

        if (!$this->database->tableExists("PermissionsTree")) {

            $this->database->createTable("PermissionsTree", '
                Parent INT,
                ID_kategorie INT NOT NULL,
                path_length int NOT NULL,
                FOREIGN KEY (Parent) REFERENCES PermissionsK(ID_kategorie),
                FOREIGN KEY (ID_kategorie) REFERENCES PermissionsK(ID_kategorie)
            ');
        }

        if (!$this->database->tableExists("Permissions")) {

            $this->database->createTable("Permissions", '
                ID_prava int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                Nazev_p varchar(50) NOT NULL,
                ID_kategorie int(11) NOT NULL,
                FOREIGN KEY (ID_kategorie) REFERENCES PermissionsK(ID_kategorie)
            ');
        }
    }

    protected function loadPermissions()
    {

        if(empty($this->categories) && empty($this->permissions)){

            $this->database->selectFromTable("ID_kategorie AS ID_prava,Nazev_k as Nazev_p,path_length", "PermissionsK NATURAL JOIN PermissionsTree");
            $this->categories = $this->database->getRows();
            $this->countK = $this->database->countRows();

            $this->database->selectFromTable("ID_prava, Nazev_p", "Permissions");
            $this->permissions = $this->database->getRows();
            $this->countP = $this->database->countRows();
        }

    }

    public function addNewCategory($category, $parent = "")
    {

        $this->loadPermissions();

        if ($category != '') {

            if (!$this->categoryExists($category)) {

                if($parent != ''){
                    if($this->categoryExists($parent) && !$this->categoryExists($category)) {

                        $id = $this->getCategoryParams($parent);
                        $this->database->insertIntoTable("Nazev_k", "PermissionsK", array($category));

                        $this->database->addWherePart("Nazev_k","=",$category);
                        $this->database->selectFromTable("ID_kategorie","PermissionsK");
                        $data = $this->database->getRows();

                        $this->database->insertIntoTable("Parent,ID_kategorie,path_length", "PermissionsTree", array($id[0],$data[0]["ID_kategorie"],$id[1] + 1));

                    } else {
                        throw new \Exception("Kategorie ".$parent." neexistuje! nebo kategorie ".$category." už existuje");
                    }
                } else {

                    $this->database->insertIntoTable("Nazev_k", "PermissionsK", array($category));
                    $this->database->addWherePart("Nazev_k", "=", $category);

                    $this->database->selectFromTable("ID_kategorie", "PermissionsK");
                    $data = $this->database->getRows();

                    $this->database->insertIntoTable("Parent,ID_kategorie,path_length", "PermissionsTree", array($data[0]["ID_kategorie"], $data[0]["ID_kategorie"], 0));
                }

                if($parent == "" || !$this->categoryExists($category)) {

                    $this->database->addWherePart("Nazev_k", "=", $category);
                    $this->database->selectFromTable("ID_kategorie AS ID_prava, Nazev_k AS Nazev_p, path_length", "PermissionsK NATURAL JOIN PermissionsTree", "", "", 1);

                    $data = $this->database->getRows();
                    array_push($this->categories, $data[0]);

                    $this->countK++;

                    return $data[0]["ID_kategorie"];

                }
            }

        } else {
            throw new \Exception("Není uvedená žádná kategorie k přidání");
        }
    }

    public function addNewPermission($permission,$category){

        $this->loadPermissions();

        if($permission != '') {

            if (!$this->permissionExists($permission)) {

                if ($category != '') {

                    if($this->categoryExists($category)) {

                        $id = $this->getCategoryParams($category);

                        $this->database->insertIntoTable("Nazev_p,ID_kategorie", "Permissions", array($permission,$id[0]));

                        $this->database->addWherePart("ID_kategorie","=",$id);
                        $this->database->addWherePart("AND","Nazev_p","=",$permission);
                        $this->database->selectFromTable("ID_prava, Nazev_p", "Permissions");
                        $data = $this->database->getRows();

                        array_push($this->permissions,$data[0]);
                        $this->countP++;

                        return $data[0]["ID_prava"];

                    } else {
                        throw new \Exception("Kategorie ".$category." neexistuje!");
                    }

                } else {
                    throw new \Exception("Právo není přidělené do žádné kategorie!");
                }
            }

        } else {
            throw new \Exception("Není žádné právo k přidání!");
        }

    }

    protected function categoryExists($permission){

        for($i = 0; $i < $this->countK; $i++){

            if(is_numeric($permission)){
                if ($this->categories[$i]["ID_prava"] == $permission) {
                    return true;
                }
            } else {
                if ($this->categories[$i]["Nazev_p"] == $permission) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function permissionExists($permission, &$dest = array()){

        if(empty($dest)){
            $dest = $this->permissions;
        }

        foreach($dest as $value){

            if(is_numeric($permission)) {
                if ($value["ID_prava"] == $permission) {
                    return true;
                }
            } else {
                if ($value["Nazev_p"] == $permission) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function getCategoryParams($permission){

        for($i = 0; $i < $this->countK; $i++){

            if(is_numeric($permission)) {
                if ($this->categories[$i]["ID_prava"] == $permission) {
                    return array($this->categories[$i]["ID_prava"], $this->categories[$i]["path_length"]);
                }
            } else {
                if ($this->categories[$i]["Nazev_p"] == $permission) {
                    return array($this->categories[$i]["ID_prava"], $this->categories[$i]["path_length"]);
                }
            }
        }

        return NULL;
    }

    public function getPermissionName($id, &$array = array()){

        if(empty($array)){
            $array = $this->permissions;
        }

        foreach($array as $value){

            if($value["ID_prava"] == $id){
                return $value["Nazev_p"];
            }
        }

        return NULL;
    }

    protected function getPermissionID($name, &$array = array()){

        if($name != '') {
            if (!is_numeric($name)) {
                if (empty($array)) {
                    $array = $this->permissions;
                }

                foreach ($array as $value) {

                    if ($value["Nazev_p"] == $name) {
                        return $value["ID_prava"];
                    }
                }
            } else {
                return $name;
            }
        }

        return NULL;
    }

    protected function getUserPermissions($userID)
    {

        if ($userID != '' && empty($this->userP)) {

            $this->database->addWherePart("ID_uzivatele", "=", $userID);
            $this->database->selectFromTable("Prava", TAB_USERS);
            $userData = $this->database->getRows();

            $this->userP = explode(",", $userData[0]["Prava"]);

        }
    }

    protected function joinPermissions(){
        return array_merge($this->permissions, $this->categories);
    }

    public function userHasAuthorization($permission, $userId = 0)
    {

        $user = Users::initialize();

        if($userId == 0 && $user->userIsLogged()){
            $userId = $user->getUserSession("user_id");
        }

        $authorization = array("id" => $userId, "logged" => $user->userIsLogged(), "admin" => $user->userIsAdmin());

        $this->getUserPermissions($authorization["id"]);
        $this->loadPermissions();
        $all = $this->joinPermissions();

        if ($this->permissionExists($permission, $all)) {

            if ($authorization["logged"]) {

                if ($authorization["admin"]) {
                    return true;
                } else {

                    if (in_array($this->getPermissionID($permission, $all), $this->userP)) {
                        return true;
                    }
                }
            }

        } else {
            throw new \Exception("Toto právo neexistuje!");
        }

        return false;
    }

}

class PermissionsEx extends Permissions{

    public function __construct(){
        parent::__construct();
    }

    private function selectedUserHasPermission($permission, $authorization)
    {

        $this->loadPermissions();
        $all = $this->joinPermissions();

        $exist = $this->getPermissionName($permission, $all);

        if ($this->permissionExists($exist, $all)) {

            if ($authorization["logged"]) {

                if ($authorization["adminProfile"]) {
                    return true;
                } else {

                    if (in_array($permission, $this->userP)) {
                        return true;
                    }

                }
            }

        } else {
            throw new \Exception("Toto právo neexistuje!");
        }


        return false;

    }

    private function printPermissionsInCategory(&$i, $category, $authorization, $enableEdit, &$array, $enabled = false){

        $output = "";

        if($array[$i]["Nazev_p"] != '') {

            $output .= '<li><table class="userPermissionsPadding">';
            $icon = new \Icons();

            for (; $i < count($array); $i++) {

                if ($array[$i]["Nazev_k"] == $category) {

                    if ($enableEdit) {

                        if (!$enabled) {

                            $output .= '<tr><td>' . $array[$i]["Nazev_p"] . '</td><td align="right"><input disabled name="permission[]" type="checkbox" class="userPermissionsCategory" value="' . $array[$i]["ID_prava"] . '"/></td></tr>';

                        } else {

                            if ($this->selectedUserHasPermission($array[$i]["ID_prava"], $authorization)) {
                                $output .= '<tr><td>' . $array[$i]["Nazev_p"] . '</td><td align="right"><input type="checkbox" name="permission[]" checked value="' . $array[$i]["ID_prava"] . '"/></td></tr>';
                            } else {
                                $output .= '<tr><td>' . $array[$i]["Nazev_p"] . '</td><td align="right"><input type="checkbox" name="permission[]" value="' . $array[$i]["ID_prava"] . '"/></td></tr>';
                            }

                        }

                    } else {

                        if (!$enabled) {

                            $output .= '<tr><td>' . $array[$i]["Nazev_p"] . '</td><td align="right">' . $icon->getIcon("wrong", "15px", "Nemá právo") . '</td></tr>';

                        } else {

                            if ($this->selectedUserHasPermission($array[$i]["ID_prava"], $authorization)) {
                                $output .= '<tr><td>' . $array[$i]["Nazev_p"] . '</td><td align="right">' . $icon->getIcon("right", "15px", "Má právo") . '</td></tr>';
                            } else {
                                $output .= '<tr><td>' . $array[$i]["Nazev_p"] . '</td><td align="right">' . $icon->getIcon("wrong", "15px", "Nemá právo") . '</td></tr>';
                            }
                        }
                    }

                } else {
                    break;
                }
            }

            $output .= '</table></li>';
            $i--;

        }

        return $output;

    }

    private function printNewLevel($level, &$i, $authorization, $enableEdit, &$array, $enabled = false){

        $icon = new \Icons();
        $output = "";

        if ($level > 0) {
            $output .= '<li><ul>';
        }

        while($level == $array[$i]["length"]) {

            if($array[$i]["Nazev_k"] == ""){break;}

            $output .= '<li><table width="100%"><tr><td>' . $array[$i]["Nazev_k"] . '</td>';

            if ($enableEdit) {

                if (($level > 0) && !$enabled) {

                    $output .= '<td align="right"><input disabled name="permission[]" type="checkbox" class="userPermissionsCategoryKK" value="' . $array[$i]["ID_kategorie"] . '"/></td>';

                } else {

                    if ($this->selectedUserHasPermission($array[$i]["ID_kategorie"], $authorization)) {
                        $output .= '<td align="right"><input name="permission[]" type="checkbox" class="userPermissionsCategoryKK" checked value="' . $array[$i]["ID_kategorie"] . '"/></td>';
                        $enabled = true;
                    } else {
                        $output .= '<td align="right"><input name="permission[]" type="checkbox" class="userPermissionsCategoryKK" value="' . $array[$i]["ID_kategorie"] . '"/></td>';
                        $enabled = false;
                    }
                }

            } else {

                if (($level > 0) && !$enabled) {

                    $output .= '<td align="right">' . $icon->getIcon("wrong", "15px", "Nemá právo") . '</td>';

                } else {

                    if ($this->selectedUserHasPermission($array[$i]["ID_kategorie"], $authorization)) {
                        $output .= '<td align="right">' . $icon->getIcon("right", "15px", "Má právo") . '</td>';
                        $enabled = true;
                    } else {
                        $output .= '<td align="right">' . $icon->getIcon("wrong", "15px", "Nemá právo") . '</td>';
                        $enabled = false;
                    }
                }
            }

            $output .= '</tr></table></li>';

            $output .= $this->printPermissionsInCategory($i, $array[$i]["Nazev_k"], $authorization, $enableEdit, $array, $enabled);

            if (($array[$i + 1]["length"] > $level)) {
                $i++;
                $output .= $this->printNewLevel($array[$i + 1]["length"], $i, $authorization, $enableEdit, $array, $enabled);
            }

            if($i <= (count($array) - 1)) {
                $i++;
            } else {
                break;
            }
        }

        if ($level > 0) {
            $output .= '</ul></li>';
        }

        return $output;

    }

    public function printPermissions($userId, $userSessionId){

        $user = Users::initialize();

        $authorization = array("logged" => $user->userIsLogged(),"admin" => $user->userIsAdmin(),"adminProfile" => $user->userIsAdmin($userId));

        $this->addNewPermission("Nastavení práv","Správa uživatelů");
        $this->getUserPermissions($userId);

        $this->database->selectFromTable("f.Nazev_k as Nazev_k, a.ID_kategorie as ID_kategorie, Nazev_p, ID_prava, b.path_length AS length","Permissions p RIGHT JOIN( PermissionsK f JOIN PermissionsTree a ON (f.ID_kategorie = a.ID_kategorie) JOIN PermissionsTree b ON (b.ID_kategorie = a.ID_kategorie)) ON (p.ID_kategorie = f.ID_kategorie)","","f.Nazev_k->ASC");
        $data = $this->database->getRows();
        $count = $this->database->countRows();

        $output = '<style>#usersPermissions,#usersPermissions ul{margin: 0px; padding: 0px;} #usersPermissions li {list-style-type: none; margin-left: 12px;} #usersPermissions table {margin: 0px;padding: 0px;text-align: left;} #usersPermissions .userPermissionsPadding {width: 100%;margin-left: 12px;}.userPermissionsRollDown{cursor: pointer;}</style>';

        $enableEdit = ($userSessionId != $userId) && ($authorization["admin"] || $this->userHasAuthorization("Nastavení práv"));
        $tree = new \TreeDirectory();

        if($enableEdit) {
            $output .= '<form id="usersPermitionsForm" method="post" action="' . $tree->getPathToRegistratura(false,true) . 'load/admin/updatePermitions.php"><input type="hidden" name="userId" value="' . $userId . '" /><ul id="usersPermissions">';
        } else {
            $output .= '<ul id="usersPermissions">';
        }

        for($i=0; $i<$count; $i++) {
            $output .= $this->printNewLevel(0,$i,$authorization,$enableEdit,$data);
        }

        if ($enableEdit) {
            $output .= '</ul><input type="submit" name="sendData" id="usersPermitionsFormButton" value="Změnit práva"/></form>';
        } else {
            $output .= '</ul>';
        }

        $output .= '<script type="text/javascript" src="' .$tree->getPathToRegistratura(false, true) . 'js/admin/permissions.js"></script>';

        return $output;
    }

    public function joinPermissionsToUser($data){

        if(isset($data) && is_array($data["permission"] && $this->userHasAuthorization("Nastavení práv"))) {

            $userId = $data["userId"];
            $permissions = join(",", $data["permission"]);

            $this->database->addWherePart("ID_uzivatele", "=", $userId);
            $this->database->updateTable(TAB_USERS, "Prava", array($permissions));

            return "Práva úspěšně uložena";
        } else {
            throw new \Exception("Odeslaná data nejsou validní");
        }
    }

}
