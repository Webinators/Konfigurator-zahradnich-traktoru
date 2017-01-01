<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 18.3.2016
 * Time: 12:01
 */

use System\Objects\Collection;

class PhotoGallery
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

        if($_GET["navigate"] != ''){
            $this->navigate = $_GET["navigate"];
        }

        $this->loadSystem();

        $this->database = new Database(DB_HOST, DB_NAME);
        $this->createTables();

        $this->fileCommander = new FileCommander();

        $this->path = $this->Root->getAppPath(__DIR__, false, false);
        $this->URLReg = $this->Root->getPathToRegistratura(false, true);
        $this->URLPho = $this->Root->getPathToProject(false, true) . $this->path;

        $this->Permissions->addNewCategory("Správa fotogalerie");
        $this->Permissions->addNewPermission("Úprava fotogalerie", "Správa fotogalerie");
    }

    private function createTables()
    {
        $this->database->createTable("Fotogalerie", "
            ID_fotogalerie INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            Nadpis VARCHAR(70) NOT NULL,
            Popis VARCHAR(120),
            Datum_pridani DATETIME
        ");

        $this->database->createTable("FotogalerieTree", "
            Parent INT NOT NULL REFERENCES Fotogalerie(ID_fotogalerie) ON DELETE CASCADE,
            ID_galerie INT NOT NULL REFERENCES Fotogalerie(ID_fotogalerie) ON DELETE CASCADE,
            path VARCHAR(255) NOT NULL,
            pathlen INT NOT NULL,
            UNIQUE(ID_galerie)
        ");
    }

    public function insertComponents()
    {
        return '
            <script type="text/javascript" src="' . $this->URLPho . 'js/fotogalerie.js"></script>
            <link rel="stylesheet" href="' . $this->URLPho . 'css/galerie.css" type="text/css" media="all" />
        ';
    }

    public function addNewGallery($data)
    {

        if ($data["nadpis"] != '') {

            $data["date"] = date("Y-m-d h:i:s", time());

            $this->database->insertIntoTable("Nadpis, Popis, Datum_pridani", "Fotogalerie", array($data["nadpis"], $data["popis"], $data["date"]));
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

            $this->database->insertIntoTable("Parent, ID_galerie, path, pathlen", "FotogalerieTree", array($data["parent"], $id, $data["path"], $data["pathlen"]));

            $this->fileCommander->setPath($path);
            $this->fileCommander->addDir($id);
            $this->fileCommander->moveToDir($id);

            $this->fileCommander->addDir("uvodni");
            $this->fileCommander->addDir("fotky");
            $this->fileCommander->addDir("thumbs");
            $this->fileCommander->addDir("subgallery");

            $fileuploader = new FileUploader();

            if ($fileuploader->isPostFile("miniatura")) {

                $this->fileCommander->moveToDir("uvodni");
                $fileuploader->setFolder($this->fileCommander->getActualPath());
                $fileuploader->uploadNewFile($_FILES['miniatura'][0], "" . $id . "", false, true, true, "700,500,cropp");

            }

            $this->fileCommander->moveUp();

            if ($fileuploader->isPostFile("files")) {

                $this->fileCommander->moveToDir("fotky");
                $fileuploader->setFolder($this->fileCommander->getActualPath());

                $this->fileCommander->moveUp();
                $this->fileCommander->moveToDir("thumbs");
                $fileuploader->thumbPath($this->fileCommander->getActualPath());
                $fileuploader->thumbParams("700,500,cropp");

                for ($i = 0; $i < count($_FILES["files"]); $i++) {
                    $fileuploader->uploadNewFile($_FILES['files'][$i], $i + 1, false, true, true, "");
                }

            }

            throw new Exception($fileuploader->printMessage());

        } else {
            throw new Exception("Není vyplněný název galerie");
        }
    }

    public function saveGallery($data)
    {

        if ($data["nadpis"] != '') {

            $this->database->addWherePart("ID_fotogalerie", "=", $data["id_fotogalerie"]);
            $this->database->updateTable("Fotogalerie", "Nadpis, Popis", array($data["nadpis"], $data["popis"]));

            echo "ok";

        } else {
            throw new Exception("Není vyplněný název galerie");
        }
    }

    public function printGallery()
    {

        $editor = new ImgEdit();

        $parent = "";

        if ($_GET["id_galerie"] != '') {
            $parent = $_GET["id_galerie"];
        } else {
            $this->database->addWherePart("ft.pathlen", "=", 0);
        }

        if($parent) {

            $navi = explode("-", $this->navigate);
            unset($navi[count($navi) - 1]);

            $this->database->addWherePart("ID_galerie","=",$_GET["parent"]);
            $this->database->addWherePart("AND","Parent","!=",$_GET["parent"]);
            $this->database->selectFromTable("Parent","FotogalerieTree");
            $data = $this->database->getRows();

            $odkaz = '?page=galerie&parent='.$data[0]["Parent"].'&id_galerie=' . $_GET["parent"] . '&info=FOTOGALERIE&navigate=' . join(" - ", $navi). '';

            echo '<a href="'.$odkaz.'" title="zpět">Zpět</a>';

            $this->database->addWherePart("ft.Parent", "=", $parent);
            $this->database->addWherePart("AND", "ft.ID_galerie", "!=", $parent);

        }

        echo '<div class="flexElem flexWrap">';

        $this->database->selectFromTable("*", "Fotogalerie f JOIN FotogalerieTree ft ON f.ID_fotogalerie = ft.ID_galerie", "", "Datum_pridani->DESC");
        $data = $this->database->getRows();
        $dataC = $this->database->countRows();

        $form = Form::getForm("FormBasic");
        $formE = new FormElements();

        if ($parent != '') {

            $this->database->addWherePart("ID_galerie", "=", $parent);
            $this->database->selectFromTable("*", "FotogalerieTree", "", "", 1);
            $datad = $this->database->getRows();

            $params[0] = $datad[0]["pathlen"];
            $params[1] = $datad[0]["path"];
            $params[2] = $parent;

        } else {
            $params[0] = 0;
            $params[1] = 0;
            $params[2] = 0;
        }

        if ($this->Permissions->userHasAuthorization("Úprava fotogalerie")) {

            $form->Action($this->URLPho . "load/showForm.php");
            $form->Method("POST");

            $form->addHiddenItem($formE->Input()->Hidden("pathlen")->Value($params[0]));
            $form->addHiddenItem($formE->Input()->Hidden("path")->Value($params[1]));
            $form->addHiddenItem($formE->Input()->Hidden("parent")->Value($params[2]));
            $form->addButton($formE->Button()->Submit("add", "Přidat galerii")->ID("mainGalerieAddNewGallery"));

            echo '
              <div class="obal_fotogalerie flex flexElem valignCenter alignElemsCenter">
              <div style="background-color: #747474; padding: 10px; margin-top: -10px">' . $form . '</div>
              </div>
            ';

        }

        foreach ($data as $gallery) {

            $odkaz = '?page=galerie&parent='.$parent.'&id_galerie=' . $gallery["ID_fotogalerie"] . '&info=FOTOGALERIE&navigate='.$this->navigate.' - '.$gallery["Nadpis"];

            $path = $this->path . "fotky/" . str_replace("/", "/subgallery/", $gallery["path"]) . "/uvodni";

            if ($this->Permissions->userHasAuthorization("Úprava fotogalerie")) {

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
                $form->addHiddenItem($formE->Input()->Hidden("id_fotogalerie")->Value($gallery["ID_fotogalerie"]));
                $form->addItem("", $formE->Input()->Text("nadpis")->Value($gallery["Nadpis"]));
                $form->addItem("", $formE->Input()->Text("popis")->Value($gallery["Popis"]));
                $form->addButton($formE->Button()->Submit("save")->ID("mainGalerieSaveMainGallery"));



                echo '' . $editor->getImgWithOptions($file[0], $img, "700,500,cropp") . '<br />
                <a href="' . $this->URLPho . 'load/removeGallery.php" id="mainGalerieRemoveGallery" data-id-g="' . $gallery["ID_fotogalerie"] . '" style="position: absolute;top: 0px;right: 0px;background-color: #2e2e2e;padding: 3px;">' . $this->Icons->getIcon("delete", "30px", "Smazat galerii") . '</a>
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

                echo '<a href="' . $odkaz . '">'.$editor->getIMGInHTML($file[0])->height("130px").'<br />
                 <div class="fotogalerie_nadpis">' . $gallery["Nadpis"] . '</div><div class="fotogalerie_popis">' . $gallery["Popis"] . '</div></a></div>';
            }

        }

        if ($parent != "") {

            $foto = "" . $this->path . "fotky/" . str_replace("/", "/subgallery/", $params[1]) . "";

            $fotoOrig = $foto."/fotky";
            $fotoThumb = $foto."/thumbs";

            $editor = new ImgEdit();
            $editor->setInputDir($fotoThumb);

            $this->fileCommander->setPath($fotoOrig);

            $count = $this->fileCommander->countFiles();
            $origFoto = $this->fileCommander->getFiles();

            $this->fileCommander->setPath($fotoThumb);
            $thumbFoto = $this->fileCommander->getFiles();

            for ($i = 0; $i < $count; $i++) {

                $img = '<a href="' . $this->Root->getUrlDomain() . $fotoOrig . '/' . $origFoto[$i] . '" data-lightbox="photoes">' . $editor->getIMGInHTML($thumbFoto[$i])->height("130px") . '</a>';

                $pic = $editor->getImgWithOptions($thumbFoto[$i], $img, "700,500,cropp", array(true, true, true, true, true));
                $pic->origImage($fotoOrig, $origFoto[$i], "");

                echo '<div class="obal_fotogalerie flex">' . $pic . '</div>';

            }

            if($dataC == 0 && $count == 0){
                echo 'Galerie je prázdná';
            }

            echo '</div>';

            if ($this->Permissions->userHasAuthorization("Úprava fotogalerie")) {

                echo '<hr /><b>Nahrání nových fotek</b>';

                $form = Form::getForm("FormBasic", $this->URLPho . 'load/noveObrazky.php');

                $form->addHiddenItem($formE->Input()->Hidden("pathO")->Value($fotoOrig));
                $form->addHiddenItem($formE->Input()->Hidden("pathT")->Value($fotoThumb));

                $form->addHiddenItem($formE->Input()->Hidden("id_fotogalerie")->Value($parent));

                $form->addItem("", $formE->Input()->FileExtendet("files")->AllowedFormats("jpg,png,gif,jpeg")->MaxFiles(20)->MaxFileSize("3MB"));
                $form->addButton($formE->Button()->Submit("upload", "Nahrát fotky")->ID("uploadMoreFiles"));

                echo $form;

            }

        } else {

            if($dataC == 0){
                echo 'Ve fotogalerii nejsou zatím vložené žádné galerie';
            }

        }
    }

    public function showAddGalleryForm($data)
    {
        $form = Form::getForm("FormTable");
        $form->Action($this->URLPho."load/addGallery.php");
        $form->Method("POST");

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

        $form->addItem("Fotky", $formE->Input()->FileExtendet("files")->AllowedFormats("jpg,png,gif,jpeg")->MaxFiles(20)->MaxFileSize("3MB"));

        $form->addButton($formE->Button()->Submit("add")->ID("mainGalerieAddGallery"));

        return $form;
    }

    public function removeGallery($data)
    {

        $this->database->addWherePart("ID_galerie", "=", $data["id_galerie"]);
        $this->database->selectFromTable("path","FotogalerieTree");
        $path = $this->database->getRows();
        $path = "" . $this->path . "fotky/".str_replace("/","/subgallery/", $path[0]["path"]);

        $pathP = explode("/",$path);
        $toDel = array_pop($pathP);
        $path = join("/", $pathP);

        $this->database->addWherePart("ID_fotogalerie", "=", $data["id_galerie"]);
        $this->database->deleteFromTable("Fotogalerie");

        $this->database->addWherePart("ID_galerie", "=", $data["id_galerie"]);
        $this->database->addWherePart("OR", "Parent", "=", $data["id_galerie"]);
        $this->database->deleteFromTable("FotogalerieTree");

        $commander = new FileCommander();
        $commander->setPath($path);
        $commander->removeDir($toDel);

        return "ok";
    }

    public function uploadNewImages($data){

        $pathO = $data["pathO"];
        $pathT = $data["pathT"];

        $id = $data["id_fotogalerie"];

        $fileUploader = new FileUploader();
        $fileUploader->setFolder($pathO);
        $fileUploader->thumbPath($pathT);
        $fileUploader->thumbParams("700,500,cropp");

        $this->fileCommander->setPath($pathO);
        $count = $this->fileCommander->countFiles() + 1;

        for($i = 0; $_FILES["files"][$i];$i++){
            $fileUploader->uploadNewFile($_FILES['files'][$i], "" . $count . "", false, true, true, "");
            $count++;
        }

    }

    public function lastGallery()
    {

        $this->database->selectFromTable("ID_nadgalerie", "Hlavnigalerie");
        $data = $this->database->getRows();
        $count = $this->database->countRows();

        $selected = array(0, 0);

        for ($i = 0; $i < $count; $i++) {

            $this->database->addWherePart("ID_nadgalerie", "=", $data[$i]["ID_nadgalerie"]);
            $this->database->selectFromTable("Datum_pridani,ID_galerie", "Galerie", "", "Datum_pridani->DESC", 1);
            $data2 = $this->database->getRows();

            if (strtotime($selected[1]) < strtotime($data2[0]["Datum_pridani"])) {
                $selected[0] = $data2[0]["ID_galerie"];
                $selected[1] = $data2[0]["Datum_pridani"];
            }

        }

        $this->database->addWherePart("ID_galerie", "=", $selected[0]);
        $this->database->selectFromTable("*", "Galerie");
        $data = $this->database->getRows();

        $ID_h = $data[0]['ID_nadgalerie'];

        $this->fileCommander->setPath("" . $this->path . "fotky/uvodni/" . $ID_h . "");
        $files = $this->fileCommander->searchFile($data[0]['ID_galerie']);
        $this->fileCommander->setPath("" . $this->path . "fotky/fotky/" . $ID_h . "/" . $data[0]['ID_galerie'] . "");
        $num = $this->fileCommander->countFiles();

        echo '
           <div style="position: relative;"><a href="?page=galerie&ID_nadgalerie='.$ID_h.'&ID_galerie=' . $data[0]['ID_galerie'] . '"><img id="galleryChangeImg" style="border-radius: 4px;border: 8px #fff solid;" src="'.$this->path.'fotky/uvodni/' . $ID_h . '/' . $files[0] . '" width="95%" border="0"/></a>
                <div class="fotogalerie_nadpis fotogalerie_nadpis_last">' . $data[0]['Nadpis'] . '</div><div class="fotogalerie_popis fotogalerie_popis_last">' . $data[0]['Popis'] . '</div>
           </div>

           <script type="text/javascript">

                function getRandomInt(min, max) {
                    return Math.floor(Math.random() * (max - min + 1)) + min;
                }

                function swapImages(){

                    var ID = ' . $data[0]['ID_galerie'] . ';
                    var rok = ' . $ID_h . ';
                    var num = getRandomInt(1, ' . $num . ');

                    var $clone = $("#galleryChangeImg").clone();
                    $clone.attr("src","'.$this->URLPho.'fotky/fotky/"+rok+"/"+ID+"/"+num+".jpg");

                    if(num > 1){
                        $("#galleryChangeImg").fadeOut(function(){
                            $(this).replaceWith($clone);
                        });
                    }
                }

                $(document).ready(function(){
                    // Run our swapImages() function every 5secs
                    setInterval(function(){swapImages();}, 5000);
                });

          </script>
                ';
    }

}