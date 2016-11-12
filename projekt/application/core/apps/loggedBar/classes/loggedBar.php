<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 22.2.2015
 * Time: 11:17
 */

namespace System\Authentication\Components;

use System\Objects\Collection;

class loggedBar {

    use Collection\ObjSet;
    private $data;

    function __construct($logged,$userId){

        $this->loadSystem();

        try {

            $database = new \Database(DB_HOST, DB_NAME);
            $database->addWherePart("ID_uzivatele", "=", $userId);
            $database->selectFromTable("ID_uzivatele,Jmeno,Prijmeni", TAB_USERS);
            $data = $database->getRows();

        } catch (\Exception $e) {
            $e->getMessage();
        }

        $tree = new \TreeDirectory();

        $this->data = '
           <link rel="stylesheet" type="text/css" href="'.$tree->getAppPath(__DIR__, false, true).'css/loggedBar.css">
           <script src="'.$tree->getAppPath(__DIR__, false, true).'js/loggedBar.js" type="text/javascript"></script>
           <script src="'.$tree->getAppPath(__DIR__, false, true).'js/others.js" type="text/javascript"></script>
        ';

        if($logged) {

            $this->data .= '

                <style type="text/css">body{padding-top: 35px;}</style>

                <div id="loggedBar">
                <div id="loggedbarobal">
                <div id="userInLoggedBar">              

                <input type="search" id="LoggedBarSearcherUsers" placeholder="Najít uživatele"/>

                <div id="LoggedBarsearchedUsers"><div id="LoggedBarsearchedUsersData"></div></div>';

            if($this->Permissions->userHasAuthorization(("Mazání uživatelů"))){

                $this->data .= '&nbsp;&nbsp;&nbsp;<a id="showLoggedUser" title="Zobrazit uživatele"><div id="loggedBarContentObal">Uživatelé</div></a>';

            }

            $this->data .= '&nbsp;&nbsp;&nbsp;<a><div id="loggedBarContentObal"><a href="index.php?page=vzkazy" title="zobrazit vzkazy">Vzkazy</a></div></a>&nbsp;&nbsp;&nbsp;<a id="showLoggedUserProfil" title="zobrazit profil"><div id="loggedBarContentObal">' . $data[0]["Jmeno"] . ' ' . $data[0]["Prijmeni"] . ' &nbsp;<img src="' . $tree->getPathToRegistratura(false,true) .'apps/userProfil/load/userProfil.php"/></div></a>&nbsp;&nbsp;&nbsp;<a id="loggedBarlogout" onclick="logoutUser()" title="odhlásit se" ><div id="loggedBarContentObal">logout</div></a></div>

                </div>
                </div><div id="userProfil"></div>
            ';

        } else {

            $this->data .= '
                <a class="LoggedBarShowUserLoginForm"><div id="loginbutton">Login</div></a>
            ';

        }

    }

    public function getLoggedBar(){

        return $this->data;

    }

} 