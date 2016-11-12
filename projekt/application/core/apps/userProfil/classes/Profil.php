<?php

namespace System\Authentication\Components;

use System\Authentication;
use System\Objects\Collection;

class Profil extends \FormConstants
{
    use  Collection\ObjSet;
    private $profil;

    function __construct($id = ''){

        $this->loadSystem();

        $userOpenedHisProfil = false;

        $user = $this->User;

        if($id == '') {
            $id = $user->getUserSession("user_id");
            $userOpenedHisProfil = true;
        }

        try {

            $database = new \Database(DB_HOST, DB_NAME);
            $database->addWherePart("ID_uzivatele", "LIKE", $id);
            $database->selectFromTable("*", TAB_USERS);
            $result = $database->countRows();

        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $tree = new \TreeDirectory();

        if($result != 0){

            $data = $database->getRows();
            $explode = explode("-",$data[0]['Datum_registrace']);
            $date = $explode[2].".".$explode[1].".".$explode[0];

            $this->profil .= '

            <link rel="stylesheet" type="text/css" href="'.$tree->getAppPath(__DIR__, false, true).'css/profil.css">

            <div id="userProfilProfilovka">
            ';

            if($userOpenedHisProfil){

                $imagesE = new \ImgEdit();
                $profil = $this->User->getProfil($id);

                $pathA = explode("/",$profil);
                $file = array_pop($pathA);
                $path = implode("/", $pathA);

                $imagesE->setInputDir($path);
                $img = $imagesE->getImgWithOptions($file,$imagesE->getIMGInHTML($file,"",'class="userProfilPic"'),"768,1052,cropp",array(true,true,false,false,false));

                $this->profil .= '
                '.$img.'<a id="adderuserProfilName" title="Upravit jméno a příjmení"><div id="userProfilName" class="userProfilName" >'.$data[0]['Jmeno'].' '.$data[0]['Prijmeni'].'</div></a>
                ';

            } else {

                $this->profil .= '
                <div id="userProfilName" class="userProfilName" onmouseover="newstyle(\'userProfilName\')" onmouseout="defaultstyle(\'userProfilName\')">'.$data[0]['Jmeno'].' '.$data[0]['Prijmeni'].'</div><img class="userProfilPic" src="'.$user->getProfil($data[0]['ID_uzivatele']).'" title="Profilový obrázek" alt="Profilový obrázek"/>
                ';

            }

            $this->profil .= '</div><div id="userProfilInfo"><div class="userProfilInsideBar">Detail</div>';

            if($userOpenedHisProfil) {

                $this->profil .= '

                    <form id="userChangeUserDetails" method="post" action="'.$tree->getAppPath(__DIR__, false, true).'load/changedetails.php">
                        <table border="1px">
                        <tr><td>Email</td><td>' . $data[0]['Email'] . '</td></tr>
                        <tr><td>Datum registrace</td><td>' . $date . '</td></tr>
                        <tr><td>Datum narození</td><td><input type="text" name="Datum_narozeni" value="'.$data[0]['Datum_narozeni'].'" /></td></tr>
                        <tr><td>Přezdívka</td><td><input type="text" name="Prezdivka" value="'.$data[0]['Prezdivka'].'" /></td></tr>
                        <tr><td>Postavení</td><td>' . $data[0]['Post'] . '</td></tr>                     
                        </table><br />
			   <input id="userChangeUserDetailsBtn" type="submit" value="Uložit" />

                    </form>
                    ';

            } else {

                $this->profil .= '

                        <table border="1px">
                        <tr><td>Email</td><td>' . $data[0]['Email'] . '</td></tr>
                        <tr><td>Datum registrace</td><td>' . $date . '</td></tr>
                        <tr><td>Datum narození</td><td>'.$data[0]['Datum_narozeni'].'</td></tr>
                        <tr><td>Přezdívka</td><td>'.$data[0]['Prezdivka'].'</td></tr>
                        <tr><td>Postavení</td><td>' . $data[0]['Post'] . '</td></tr>
                        </table>
                    ';

            }

            $this->profil .= '</div><script type="text/javascript" src="'.$tree->getAppPath(__DIR__, false, true).'js/profil.js"></script><br style="clear: both;"/><div id="RegisteredUsersPermitionsDiv"><div class="userProfilInsideBar">Práva</div><div id="usersPermitionsContent">';

            $permEx = new Authentication\PermissionsEx();

            $this->profil .= $permEx->printPermissions($id,$user->getUserSession("user_id"));

        } else {
            throw new \Exception("Takový uživatel není zaregistrovaný");
        }

    }

    public function getUserProfil()
    {
        return $this->profil;
    }

}