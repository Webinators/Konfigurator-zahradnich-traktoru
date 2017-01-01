<?php

$cestadb = "data/db_clanky.php";

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 18.3.2016
 * Time: 12:01
 */

use System\Objects\Collection;

class Poradna
{
    use Collection\ObjSet;

    private $database;
    private $fileCommander;

    private $URLReg;
    private $URLPho;
    private $path;

    private $navigate = "";

    function __construct()
    {

        if ($_GET["navigate"] != '') {
            $this->navigate = $_GET["navigate"];
        }

        $this->loadSystem();

        $this->database = new Database(DB_HOST, DB_NAME);
        $this->createTables();

        $this->fileCommander = new FileCommander();

        $this->path = $this->Root->getAppPath(__DIR__);
        $this->URLReg = $this->Root->getPathToRegistratura(false, true);
        $this->URLPho = $this->Root->getPathToProject(false, true) . $this->path;


        $this->Permissions->addNewCategory("Správa poradny");
        $this->Permissions->addNewPermission("Úprava poradny", "Správa poradny");
    }

    private function createTables()
    {
        $this->database->createTable("Poradna", "
            ID_poradny INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            Nadpis VARCHAR(70) NOT NULL,
            Popis VARCHAR(120),
            Datum_pridani DATETIME
        ");

        $this->database->createTable("PoradnaTree", "
            Parent INT NOT NULL REFERENCES Poradna(ID_poradny) ON DELETE CASCADE,
            ID_galerie INT NOT NULL REFERENCES Poradna(ID_poradny) ON DELETE CASCADE,
            path VARCHAR(255) NOT NULL,
            pathlen INT NOT NULL,
            UNIQUE(ID_galerie)
        ");

        $this->database->createTable("PoradnaClanky", "
            ID_clanek INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Galerie INT NOT NULL REFERENCES Poradna(ID_poradny) ON DELETE CASCADE,
            Nazev VARCHAR(255) NOT NULL,
            Popis VARCHAR(255),
            Clanek INT NOT NULL,
            Datum_pridani DATETIME
        ");

    }

    public function insertComponents()
    {
        return '
            <script type="text/javascript" src="' . $this->URLPho . 'js/poradna.js"></script>
            <link rel="stylesheet" href="' . $this->URLPho . 'css/galerie.css" type="text/css" media="all" />
        ';
    }

    public function addNewGallery($data)
    {

        if ($data["nadpis"] != '') {

            $data["date"] = date("Y-m-d h:i:s", time());

            $this->database->insertIntoTable("Nadpis, Popis, Datum_pridani", "Poradna", array($data["nadpis"], $data["popis"], $data["date"]));
            $id = $this->database->getLasInsertedId();

            if ($data["path"] == 0) {
                $path = $this->path . "/fotky";
            } else {
                $path = $this->path . "/fotky/" . str_replace("/", "/subgallery/", $data["path"]);
            }

            if ($data["path"] != 0) {
                $path .= "/subgallery";
            } else {
                $data["path"] = "";
            }

            if ($data["path"] != '') {
                $data["path"] .= "/" . $id;
            } else {
                $data["path"] = $id;
            }

            if ($data["parent"] == 0) {
                $data["parent"] = $id;
            }

            $this->database->insertIntoTable("Parent, ID_galerie, path, pathlen", "PoradnaTree", array($data["parent"], $id, $data["path"], $data["pathlen"]));

            $this->fileCommander->setPath($path);
            $this->fileCommander->addDir($id);
            $this->fileCommander->moveToDir($id);

            $this->fileCommander->addDir("uvodni");
            $this->fileCommander->addDir("subgallery");
            $this->fileCommander->addDir("upoutavky");

            $fileuploader = new FileUploader();

            if ($fileuploader->isPostFile("miniatura")) {

                $this->fileCommander->moveToDir("uvodni");
                $fileuploader->setFolder($this->fileCommander->getActualPath());
                $fileuploader->uploadNewFile($_FILES['miniatura'][0], "" . $id . "", false, true, true, "700,500,cropp");

            }

            throw new Exception($fileuploader->printMessage());

        } else {
            throw new Exception("Není vyplněný název galerie");
        }
    }

    private function customerLogged(){

        if($this->Sessions->sessionExists("customerLogged")){
            return true;
        } else {
            return false;
        }

    }

    public function loginCustomer($data){

        if($data["passw"] == "3KaM9"){

            $this->Sessions->createSession("customerLogged",true);

        } else {
            throw new Exception("Špatné heslo pro přístup do poradny.");
        }

        echo "ok";

    }

    public function printGallery()
    {

        $formE = new FormElements();

        if($this->customerLogged() || $this->User->userIsLogged()) {

            $editor = new ImgEdit();

            $parent = "";

            if ($_GET["id_galerie"] != '') {
                $parent = $_GET["id_galerie"];
            } else {
                $this->database->addWherePart("ft.pathlen", "=", 0);
            }

            if ($parent) {

                $navi = explode("-", $this->navigate);
                unset($navi[count($navi) - 1]);

                $this->database->addWherePart("ID_galerie", "=", $_GET["parent"]);
                $this->database->addWherePart("AND", "Parent", "!=", $_GET["parent"]);
                $this->database->selectFromTable("Parent", "FotogalerieTree");
                $data = $this->database->getRows();

                $odkaz = '?page=poradna&parent=' . $data[0]["Parent"] . '&id_galerie=' . $_GET["parent"] . '&info=Poradna&navigate=' . join(" - ", $navi) . '';

                echo $formE->Button()->Link("back",$odkaz,"zpět");

                $this->database->addWherePart("ft.Parent", "=", $parent);
                $this->database->addWherePart("AND", "ft.ID_galerie", "!=", $parent);

            }

            echo '<div class="flexElem flexWrap">';

            $this->database->selectFromTable("*", "Poradna f JOIN PoradnaTree ft ON f.ID_poradny = ft.ID_galerie", "", "Datum_pridani->DESC");
            $data = $this->database->getRows();
            $dataC = $this->database->countRows();

            $form = Form::getForm("FormBasic");

            if ($parent != '') {

                $this->database->addWherePart("ID_galerie", "=", $parent);
                $this->database->selectFromTable("*", "PoradnaTree", "", "", 1);
                $datad = $this->database->getRows();

                $params[0] = $datad[0]["pathlen"];
                $params[1] = $datad[0]["path"];
                $params[2] = $parent;

            } else {
                $params[0] = 0;
                $params[1] = 0;
                $params[2] = 0;
            }

            if ($this->Permissions->userHasAuthorization("Úprava poradny")) {

                $form->Action($this->URLPho . "load/showForm.php");
                $form->Method("POST");

                $form->addHiddenItem($formE->Input()->Hidden("pathlen")->Value($params[0]));
                $form->addHiddenItem($formE->Input()->Hidden("path")->Value($params[1]));
                $form->addHiddenItem($formE->Input()->Hidden("parent")->Value($params[2]));
                $form->addButton($formE->Button()->Submit("add", "Přidat téma")->ID("mainGalerieAddNewTheme"));

                echo '
              <div class="obal_fotogalerie flex flexElem valignCenter alignElemsCenter">
              <div style="background-color: #747474; padding: 10px; margin-top: -10px">' . $form . '</div>
              </div>
            ';

            }

            foreach ($data as $gallery) {

                $odkaz = '?page=poradna&parent=' . $parent . '&id_galerie=' . $gallery["ID_poradny"] . '&info=Poradna&navigate=' . $this->navigate . ' - ' . $gallery["Nadpis"];

                $path = $this->path . "fotky/" . str_replace("/", "/subgallery/", $gallery["path"]) . "/uvodni";

                if ($this->Permissions->userHasAuthorization("Úprava poradny")) {

                    echo '<div class="obal_fotogalerie flex">';

                    $editor->setInputDir($path);
                    $this->fileCommander->setPath($path);
                    $file = $this->fileCommander->getFiles();

                    if (!$file) {
                        $file[0] = $gallery["ID_fotogalerie"];
                    }

                    $img = '<a href="' . $odkaz . '">' . $editor->getIMGInHTML($file[0])->height("130px") . '</a>';

                    $form = Form::getForm("FormBasic");
                    $form->Action($this->URLPho . "load/saveGallery.php");
                    $form->addHiddenItem($formE->Input()->Hidden("ID_poradny")->Value($gallery["ID_poradny"]));
                    $form->addItem("", $formE->Input()->Text("nadpis")->Value($gallery["Nadpis"]));
                    $form->addItem("", $formE->Input()->Text("popis")->Value($gallery["Popis"]));
                    $form->addButton($formE->Button()->Submit("save")->ID("mainGalerieSaveGallery"));

                    echo '' . $editor->getImgWithOptions($file[0], $img, "700,500,cropp") . '<br />
                <a href="' . $this->URLPho . 'load/removeGallery.php" id="mainGalerieRemoveGallery" data-id-g="' . $gallery["ID_poradny"] . '" style="position: absolute;top: 0px;right: 0px;background-color: #2e2e2e;padding: 3px;">' . $this->Icons->getIcon("delete", "30px", "Smazat téma") . '</a>
                 ' . $form . '
                 </div>';


                } else {

                    echo '<div class="obal_fotogalerie flex" >';

                    $editor->setInputDir($path);
                    $this->fileCommander->setPath($path);
                    $file = $this->fileCommander->getFiles();
                    if (!$file) {
                        $file[0] = $gallery["ID_fotogalerie"];
                    }

                    echo '<a href="' . $odkaz . '">' . $editor->getIMGInHTML($file[0])->height("130px") . '<br />
                    <div class="fotogalerie_nadpis">' . $gallery["Nadpis"] . '</div><div class="fotogalerie_popis">' . $gallery["Popis"] . '</div></a>
                     </div>';
                }

            }

            if ($parent != "") {

                $this->database->addWherePart("Galerie", "=", $parent);
                $this->database->selectFromTable("*", "PoradnaClanky");
                $poradna = $this->database->getRows();
                $poradnaC = $this->database->countRows();

                $this->database->addWherePart("ID_galerie", "=", $parent);
                $this->database->selectFromTable("path","PoradnaTree");
                $path = $this->database->getRows();
                $path = "" . $this->path . "fotky/".str_replace("/","/subgallery/", $path[0]["path"]).'/upoutavky';

                foreach ($poradna as $clanek) {

                    echo '<div class="obal_fotogalerie flex">';

                    $odkaz = '?page=poradna&parent=' . $parent . '&id_clanek=' . $clanek["ID_clanek"] . '&info=Poradna&navigate=' . $this->navigate . ' - ' . $clanek["Nazev"] .'';

                    $editor->setInputDir($path);
                    $this->fileCommander->setPath($path);
                    $file = $this->fileCommander->searchFile($clanek["ID_clanek"]);
                    if (!$file) {
                        $file[0] = $clanek["ID_clanek"];
                    }

                    $img = '<a href="' . $odkaz . '">' . $editor->getIMGInHTML($file[0])->height("130px") . '</a>';

                    echo '' . $editor->getImgWithOptions($file[0], $img, "700,500,cropp") . '<br />';

                    if($this->Permissions->userHasAuthorization("Úprava poradny")){

                        $form = Form::getForm("FormBasic");
                        $form->Action($this->URLPho . "load/saveClanek.php");
                        $form->addHiddenItem($formE->Input()->Hidden("ID_clanek")->Value($clanek["ID_clanek"]));
                        $form->addItem("", $formE->Input()->Text("nazev")->Value($clanek["Nazev"]));
                        $form->addItem("", $formE->Input()->Text("popis")->Value($clanek["Popis"]));
                        $form->addButton($formE->Button()->Submit("save")->ID("mainGalerieSaveClanek"));

                        echo $form.'
                                <a href="' . $this->URLPho . 'load/removeClanek.php" id="mainGalerieRemoveClanek" data-id-g="' . $clanek["ID_clanek"] . '" style="position: absolute;top: 0px;right: 0px;background-color: #2e2e2e;padding: 3px;">' . $this->Icons->getIcon("delete", "30px", "Smazat článek") . '</a>
                             ';

                    } else {

                        echo'
                                <div class="fotogalerie_nadpis">' . $clanek["Nazev"] . '</div><div class="fotogalerie_popis">' . $clanek["Popis"] . '</div></a>
                            ';
                    }


                    echo '</div>';

                }


                if ($dataC == 0 && $poradnaC == 0) {
                    echo '<div class="obal_fotogalerie flex">Poradna je prázdná</div>';
                }

                if ($this->Permissions->userHasAuthorization("Úprava poradny")) {

                    $form = Form::getForm("FormBasic", $this->URLPho . 'load/showForm2.php');
                    $form->addHiddenItem($formE->Input()->Hidden("ID_poradny")->Value($parent));
                    $form->addButton($formE->Button()->Submit("add", "Přidat článek")->ID("mainGalerieAddNewClanek"));

                    echo '<div class="obal_fotogalerie flex flexElem valignCenter alignElemsCenter">
                     <div style="background-color: #747474; padding: 10px; margin-top: -10px">' . $form . '</div>
                     </div>
                ';

                }

            } else {

                if ($dataC == 0) {
                    echo '<div class="obal_fotogalerie flex">Ve poradně nejsou zatím vložené žádné příspěvky</div>';
                }

            }

            echo '</div>';

        } else {

            echo ' 
            <script type="text/javascript">

                $(document).ready(function(){
    
                    sendData({
    
                        data: {send:true},
                        url: "' . $this->URLPho . 'load/getAccessForm.php",
                        method: "POST",
                        progress: "window",
    
                    },function(data){
    
                        if(data != false){
    
                            mainWindow.normal({
                                center: true,
                                bar: false,
                                content: data,
                                buttonPointer: false,
                                returnPointer: false,
                                close: false
                            });
    
                        }
    
                    });
    
                });
                </script>
            ';

        }

    }

    public function getAccessForm(){

        $form = Form::getForm("FormTable", $this->URLPho."load/loginCustomer.php");
        $formE = new FormElements();

        $form->Method("POST");
        $form->addItem("Zadejte vstupní kód", $formE->Input()->Password("passw")->Required(true));
        $form->addButton($formE->Button()->Submit("upload","Vstoupit")->ID("mainPoradnaLoginCustomer"));

        return ''.$form.'';
    }

    public function showAddGalleryForm($data)
    {
        $form = Form::getForm("FormTable");
        $form->Action($this->URLPho."load/addGallery.php");
        $form->Method("POST");
        $form->Enctype("multipart/form-data");

        $formE = new FormElements();

        if($data["path"] != 0){
            $data["pathlen"] += 1;
        }

        $form->addHiddenItem($formE->Input()->Hidden("pathlen")->Value($data["pathlen"]));
        $form->addHiddenItem($formE->Input()->Hidden("path")->Value($data["path"]));
        $form->addHiddenItem($formE->Input()->Hidden("parent")->Value($data["parent"]));

        $form->addItem("Nadpis",$formE->Input()->Text("nadpis")->Required(true));
        $form->addItem("Popis",$formE->Input()->Text("popis")->Required(true));
        $form->addItem("Upotávací obrázek", $formE->Input()->FileBasic("miniatura")->AllowedFormats("jpg,png,gif,jpeg")->MaxFiles(1)->MaxFileSize("3MB"));

        $form->addButton($formE->Button()->Submit("add")->ID("mainGalerieAddTheme"));

        return $form;
    }

    public function showAddClanekForm($data)
    {
        $form = Form::getForm("FormTable");
        $form->Action($this->URLPho."load/addClanek.php");
        $form->Method("POST");
        $form->Enctype("multipart/form-data");

        $formE = new FormElements();

        $form->addHiddenItem($formE->Input()->Hidden("ID_poradny")->Value($data["ID_poradny"]));
        $form->addItem("Nadpis",$formE->Input()->Text("nazev")->Required(true));
        $form->addItem("Popis",$formE->Input()->Text("popis")->Required(true));
        $form->addItem("Upoutávací obrázek",$formE->Input()->FileBasic("upoutavka")->AllowedFormats("jpg,png,gif,jpeg")->MaxFiles(1)->MaxFileSize("3MB"));
        $form->addItem("","Psaní článku Vám bude umožněno až po přidání článku");
        $form->addButton($formE->Button()->Submit("add")->ID("mainGalerieAddClanek"));

        return $form;
    }

    public function saveGallery($data)
    {

        if ($data["nadpis"] != '') {

            $this->database->addWherePart("ID_poradny", "=", $data["ID_poradny"]);
            $this->database->updateTable("Poradna", "Nadpis, Popis", array($data["nadpis"], $data["popis"]));

            echo "ok";

        } else {
            throw new Exception("Není vyplněný název galerie");
        }
    }

    public function saveClanek($data)
    {

        if ($data["nazev"] != '') {

            $this->database->addWherePart("ID_clanek", "=", $data["ID_clanek"]);
            $this->database->updateTable("PoradnaClanky", "Nazev, Popis", array($data["nazev"], $data["popis"]));

            echo "ok";

        } else {
            throw new Exception("Není vyplněný název článku");
        }
    }

    public function removeGallery($data)
    {

        $this->database->addWherePart("ID_galerie", "=", $data["id_galerie"]);
        $this->database->selectFromTable("path","PoradnaTree");
        $path = $this->database->getRows();
        $path = "" . $this->path . "fotky/".str_replace("/","/subgallery/", $path[0]["path"]);

        $pathP = explode("/",$path);
        $toDel = array_pop($pathP);
        $path = join("/", $pathP);

        $this->database->addWherePart("ID_poradny", "=", $data["id_galerie"]);
        $this->database->deleteFromTable("Poradna");

        $this->database->addWherePart("ID_galerie", "=", $data["id_galerie"]);
        $this->database->addWherePart("OR", "Parent", "=", $data["id_galerie"]);
        $this->database->deleteFromTable("PoradnaTree");

        $commander = new FileCommander();
        $commander->setPath($path);
        $commander->removeDir($toDel);

        return "ok";
    }

    public function removeClanek($data)
    {
        $this->database->addWherePart("ID_clanek", "=", $data["ID_clanek"]);
        $this->database->deleteFromTable("PoradnaClanky");

        $this->database->addWherePart("ID_galerie", "=", $data["ID_poradny"]);
        $this->database->selectFromTable("path","PoradnaTree");
        $path = $this->database->getRows();
        $path = "" . $this->path . "fotky/".str_replace("/","/subgallery/", $path[0]["path"]);

        $this->fileCommander->setPath($path);
        $this->fileCommander->moveToDir("upoutavky");
        $this->fileCommander->removeFile($data["ID_clanek"]);

        return "ok";
    }


    public function addNewClanek($data){

        $this->database->insertIntoTable("Text","Clanky",array("Nový článek"));
        $id = $this->database->getLasInsertedId();

        $data["date"] = date("Y-m-d h:i:s", time());
        $this->database->insertIntoTable("Galerie,Nazev,Popis,Clanek,Datum_pridani", "PoradnaClanky", array($data["ID_poradny"], $data["nazev"], $data["popis"], $id, $data["date"]));
        $id = $this->database->getLasInsertedId();

        $this->database->addWherePart("ID_galerie", "=", $data["ID_poradny"]);
        $this->database->selectFromTable("path","PoradnaTree");
        $path = $this->database->getRows();
        $path = "" . $this->path . "fotky/".str_replace("/","/subgallery/", $path[0]["path"]);

        $this->fileCommander->setPath($path);
        $this->fileCommander->moveToDir("upoutavky");

        $uploader = new FileUploader();
        $uploader->setFolder($this->fileCommander->getActualPath());

        $uploader->uploadNewFile($_FILES['upoutavka'][0], "" . $id . "", false, true, true, "700,500,cropp");

        return "ok";
    }


    public function showClanek($id){

        $this->database->addWherePart("ID_clanek", "=", $id);
        $this->database->selectFromTable("Clanek", "PoradnaClanky");
        $clanek = $this->database->getRows();

        return $clanek[0]["Clanek"];
    }


}