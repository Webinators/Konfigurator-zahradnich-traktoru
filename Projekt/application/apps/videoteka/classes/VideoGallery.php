<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 18.3.2016
 * Time: 12:01
 */

use System\Objects\Collection;

class VideoGallery
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

        $this->path = $this->Root->getAppPath(__DIR__);
        $this->URLReg = $this->Root->getPathToRegistratura(false, true);
        $this->URLPho = $this->Root->getPathToProject(false, true) . $this->path;

        $this->Permissions->addNewCategory("Správa videotéky");
        $this->Permissions->addNewPermission("Úprava videotéky", "Správa videotéky");
    }

    private function createTables()
    {
        $this->database->createTable("Videogalerie", "
            ID_videogalerie INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            Nadpis VARCHAR(70) NOT NULL,
            Popis VARCHAR(120),
            Datum_pridani DATETIME
        ");

        $this->database->createTable("VideogalerieTree", "
            Parent INT NOT NULL REFERENCES Videogalerie(ID_videogalerie) ON DELETE CASCADE,
            ID_galerie INT NOT NULL REFERENCES Videogalerie(ID_videogalerie) ON DELETE CASCADE,
            path VARCHAR(255) NOT NULL,
            pathlen INT NOT NULL,
            UNIQUE(ID_galerie)
        ");

        $this->database->createTable("VideogalerieVidea", "
            ID_video INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Galerie INT NOT NULL REFERENCES Videogalerie(ID_videogalerie) ON DELETE CASCADE,
            url VARCHAR(255) NOT NULL,
            Datum_pridani DATETIME
        ");

    }

    public function insertComponents()
    {
        return '
            <script type="text/javascript" src="' . $this->URLPho . 'js/videogalerie.js"></script>
            <link rel="stylesheet" href="' . $this->URLPho . 'css/galerie.css" type="text/css" media="all" />
        ';
    }

    public function addNewGallery($data)
    {

        if ($data["nadpis"] != '') {

            $data["date"] = date("Y-m-d h:i:s", time());

            $this->database->insertIntoTable("Nadpis, Popis, Datum_pridani", "Videogalerie", array($data["nadpis"], $data["popis"], $data["date"]));
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

            $this->database->insertIntoTable("Parent, ID_galerie, path, pathlen", "VideogalerieTree", array($data["parent"], $id, $data["path"], $data["pathlen"]));

            $this->fileCommander->setPath($path);
            $this->fileCommander->addDir($id);
            $this->fileCommander->moveToDir($id);

            $this->fileCommander->addDir("uvodni");
            $this->fileCommander->addDir("subgallery");

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

    public function saveGallery($data)
    {

        if ($data["nadpis"] != '') {

            $this->database->addWherePart("ID_videogalerie", "=", $data["id_videogalerie"]);
            $this->database->updateTable("Videogalerie", "Nadpis, Popis", array($data["nadpis"], $data["popis"]));

            echo "ok";

        } else {
            throw new Exception("Není vyplněný název galerie");
        }
    }


    private function youtube_title($id) {

        $youtube = "http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=".$id."&format=json";

        $curl = curl_init($youtube);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($return, true);

        return $data["title"];

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

            $odkaz = '?page=videa&parent='.$data[0]["Parent"].'&id_galerie=' . $_GET["parent"] . '&info=VIDEOGALERIE&navigate=' . join(" - ", $navi). '';

            echo '<a href="'.$odkaz.'" title="zpět">Zpět</a>';

            $this->database->addWherePart("ft.Parent", "=", $parent);
            $this->database->addWherePart("AND", "ft.ID_galerie", "!=", $parent);

        }

        echo '<div class="flexElem flexWrap">';

        $this->database->selectFromTable("*", "Videogalerie f JOIN VideogalerieTree ft ON f.ID_videogalerie = ft.ID_galerie", "", "Datum_pridani->DESC");
        $data = $this->database->getRows();
        $dataC = $this->database->countRows();

        $form = Form::getForm("FormBasic");
        $formE = new FormElements();

        if ($parent != '') {

            $this->database->addWherePart("ID_galerie", "=", $parent);
            $this->database->selectFromTable("*", "VideogalerieTree", "", "", 1);
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
            $form->addButton($formE->Button()->Submit("add", "Přidat videogalerii")->ID("mainGalerieAddNewGallery"));

            echo '
              <div class="obal_fotogalerie flex flexElem valignCenter alignElemsCenter">
              <div style="background-color: #747474; padding: 10px; margin-top: -10px">' . $form . '</div>
              </div>
            ';

        }

        foreach ($data as $gallery) {

            $odkaz = '?page=videa&parent='.$parent.'&id_galerie=' . $gallery["ID_videogalerie"] . '&info=VIDEOGALERIE&navigate='.$this->navigate.' - '.$gallery["Nadpis"];

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
                $form->addHiddenItem($formE->Input()->Hidden("id_videogalerie")->Value($gallery["ID_videogalerie"]));
                $form->addItem("", $formE->Input()->Text("nadpis")->Value($gallery["Nadpis"]));
                $form->addItem("", $formE->Input()->Text("popis")->Value($gallery["Popis"]));
                $form->addButton($formE->Button()->Submit("save")->ID("mainGalerieSaveMainGallery"));

                echo '' . $editor->getImgWithOptions($file[0], $img, "700,500,cropp") . '<br />
                <a href="' . $this->URLPho . 'load/removeGallery.php" id="mainGalerieRemoveGallery" data-id-g="' . $gallery["ID_videogalerie"] . '" style="position: absolute;top: 0px;right: 0px;background-color: #2e2e2e;padding: 3px;">' . $this->Icons->getIcon("delete", "30px", "Smazat galerii") . '</a>
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

            $this->database->addWherePart("Galerie","=",$parent);
            $this->database->selectFromTable("*","VideogalerieVidea");
            $videa = $this->database->getRows();
            $videaC = $this->database->countRows();

            foreach ($videa as $video){
                $item = $this->youtube_title($video["url"]);
                echo '<div class="obal_fotogalerie flex">
                <a href="' . $this->URLPho . 'load/removeVideo.php" id="mainGalerieRemoveVideo" data-id-v="' . $video["ID_video"] . '" style="position: absolute;top: 0px;right: 0px;z-index: 999;background-color: #fff;padding: 3px;">' . $this->Icons->getIcon("delete", "30px", "Smazat galerii") . '</a>
                <a class="galleryShowVideo" href="" data-url="'.$video["url"].'"><div class="fotogalerie_nadpis">' . $item . '</div><img src="http://i1.ytimg.com/vi/'.$video["url"].'/mqdefault.jpg" title="video" alt="video"/></a></div>';
            }

            if($dataC == 0 && $videaC == 0){
                echo '<div class="obal_fotogalerie flex">Videotéka je prázdná</div>';
            }

            if ($this->Permissions->userHasAuthorization("Úprava fotogalerie")) {

                $form = Form::getForm("FormBasic", $this->URLPho . 'load/showForm2.php');
                $form->addHiddenItem($formE->Input()->Hidden("id_videogalerie")->Value($parent));
                $form->addButton($formE->Button()->Submit("add", "Přidat video")->ID("mainGalerieAddNewVideo"));

                echo '<div class="obal_fotogalerie flex flexElem valignCenter alignElemsCenter">
                     <div style="background-color: #747474; padding: 10px; margin-top: -10px">' . $form . '</div>
                     </div>
                ';

            }

        } else {

            if($dataC == 0){
                echo '<div class="obal_fotogalerie flex">Ve videogalerii nejsou zatím vložené žádné galerie</div>';
            }

        }

        echo '</div>';
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

        $form->addButton($formE->Button()->Submit("add")->ID("mainGalerieAddGallery"));

        return $form;
    }

    public function showAddVideoForm($data)
    {
        $form = Form::getForm("FormTable");
        $form->Action($this->URLPho."load/addVideo.php");
        $form->Method("POST");

        $formE = new FormElements();

        $form->addHiddenItem($formE->Input()->Hidden("id_videogalerie")->Value($data["id_videogalerie"]));
        $form->addItem("URL",$formE->Input()->Text("url")->Required(true)->Size(13));
        $form->addButton($formE->Button()->Submit("add")->ID("mainGalerieAddVideo"));

        return $form;
    }

    public function removeGallery($data)
    {

        $this->database->addWherePart("ID_galerie", "=", $data["id_galerie"]);
        $this->database->selectFromTable("path","VideogalerieTree");
        $path = $this->database->getRows();
        $path = "" . $this->path . "fotky/".str_replace("/","/subgallery/", $path[0]["path"]);

        $pathP = explode("/",$path);
        $toDel = array_pop($pathP);
        $path = join("/", $pathP);

        $this->database->addWherePart("ID_videogalerie", "=", $data["id_galerie"]);
        $this->database->deleteFromTable("Videogalerie");

        $this->database->addWherePart("ID_galerie", "=", $data["id_galerie"]);
        $this->database->addWherePart("OR", "Parent", "=", $data["id_galerie"]);
        $this->database->deleteFromTable("VideogalerieTree");

        $commander = new FileCommander();
        $commander->setPath($path);
        $commander->removeDir($toDel);

        return "ok";
    }

    public function removeVideo($data)
    {
        $this->database->addWherePart("ID_video", "=", $data["id_video"]);
        $this->database->deleteFromTable("VideogalerieVidea");

        return "ok";
    }


    public function addNewVideo($data){

        if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $data['url'], $match))
        {
            $data['url'] = $match[1];
        }
        else
        {
            $data['url'] = "";
        }

        $data["date"] = date("Y-m-d h:i:s", time());
        $this->database->insertIntoTable("Galerie,url,Datum_pridani", "VideogalerieVidea", array($data["id_videogalerie"], $data["url"], $data["date"]));

        return "ok";
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
           <div style="position: relative;"><a href="?page=videa&ID_nadgalerie='.$ID_h.'&ID_galerie=' . $data[0]['ID_galerie'] . '"><img id="galleryChangeImg" style="border-radius: 4px;border: 8px #fff solid;" src="'.$this->path.'fotky/uvodni/' . $ID_h . '/' . $files[0] . '" width="95%" border="0"/></a>
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