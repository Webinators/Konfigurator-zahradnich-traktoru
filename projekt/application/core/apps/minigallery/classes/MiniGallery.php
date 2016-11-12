<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 30.10.2016
 * Time: 21:19
 */

use System\Objects\Collection;

class MiniGallery
{

    use Collection\ObjSet;

    private $resizeParams = ["",""];
    private $commander = null;
    private $params = ["3Mb",""];

    function __construct($destination = "")
    {

        $this->loadSystem();

        if($destination != '') {

            $this->commander = new FileCommander();
            $this->checkFolder($destination);

        }

    }

    public function thumbResize($params = ""){

        if($params != ''){
            $paramsA = explode(",",$params);
            $params = $paramsA[0].",".$paramsA[1].",".(($paramsA[2] != "") ? $paramsA[2] : "cropp")."";
        }

        $this->resizeParams[0] = $params;
        return $this;
    }

    public function photoResize($params = ""){

        if($params != ''){
            $paramsA = explode(",",$params);
            $params = $paramsA[0].",".$paramsA[1].",".(($paramsA[2] != "") ? $paramsA[2] : "cropp")."";
        }

        $this->resizeParams[1] = $params;
        return $this;
    }

    public function maxFileSize($val){
        $this->params[0] = $val;
        return $this;
    }

    public function allowedExtensions($extensions = "")
    {
        $this->params[1] = $extensions;
        return $this;
    }

    public function __toString()
    {

        $output = '<div class="miniGallery" style="">';

        try{

            $output .= '<div class="miniGalleryFiles flexElem alignElemsCenter flexWrap">'.$this->loadFiles().'</div>';

            $forme = new FormElements();

            if($this->commander != null) {

                $form = Form::getForm("FormTable", $this->Root->getAppPath(__DIR__, false, true) . 'load/upload.php');

                $form->addHiddenItem($forme->Input()->Hidden("resizeP[]")->Value($this->resizeParams[0]));
                $form->addHiddenItem($forme->Input()->Hidden("resizeP[]")->Value($this->resizeParams[1]));
                $form->addHiddenItem($forme->Input()->Hidden("destination")->Value($this->commander->getActualPath()));
                $form->addHiddenItem($forme->Input()->Hidden("allowed")->Value($this->params[1]));

                $form->addItem("", $forme->Input()->FileExtendet("files")->MaxFileSize($this->params[0])->AllowedFormats($this->params[1]));
                $form->addButton($forme->Button()->Submit("upload", "Nahrát soubory")->_class("miniGalleryBtn"));

                $output .= $form;

            } else {


                $output .= $forme->Input()->FileExtendet("files")->MaxFileSize($this->params[0])->AllowedFormats($this->params[1]);

            }

        } catch (Exception $e){

            $output .= $e->getMessage();

        }

        $output .= '</div><link <link href="'.$this->Root->getAppPath(__DIR__,false,true).'css/mini.css" media="all" rel="stylesheet" type="text/css" /><script type="text/javascript" src="'.$this->Root->getAppPath(__DIR__,false,true).'js/mini.js"></script>';

        return $output;

    }

    private function loadFiles(){

        $data = '';

        if($this->commander != null) {

            $this->commander->moveToDir("thumbs");

            $thumbDir = $this->commander->getActualPath();
            $thumbs = $this->commander->getFiles();

            $this->commander->moveUp();
            $this->commander->moveToDir("foto");

            $fotoDir = $this->commander->getActualPath();
	          $fotoUrl = $this->commander->getActualPath(false,true);
            $fotos = $this->commander->getFiles();

            $this->commander->moveUp();
            $this->commander->moveToDir("files");

            $filesDir = $this->commander->getActualPath();
            $files = $this->commander->getFiles();

            $images = new ImgEdit();
            $images->setInputDir($thumbDir);

            for ($i = 0; $i < count($thumbs); $i++) {

                    $img = $images->getIMGInHTML($thumbs[$i]);
                    $img->width("150px");

		                $img = '<a href="'.$fotoUrl.'/'.$fotos[$i].'" data-lightbox="photoes">'.$img.'</a>';

                    $editor = $images->getImgWithOptions($thumbs[$i], $img, $this->resizeParams[0], array(true, true, true, true, true));
                    $editor->origImage($fotoDir, $fotos[$i], $this->resizeParams[1]);

                    $data .= $editor;

            }

            for ($i = 0; $i < count($files); $i++) {

                $data .= '';

                // todo: editor pro obyč soubory

            }

            $this->commander->moveUp();

        }

        return $data;

    }

    private function checkFolder($dir){

        $this->commander->setPath($dir);

        $this->commander->addDir("foto");
        $this->commander->addDir("thumbs");
        $this->commander->addDir("files");

    }

    public function uploadFiles(){

        $path = $this->commander->getActualPath();

        $this->commander->moveToDir("foto");

        if($this->commander->countFiles() > 0) {
            $lastfoto = intval(pathinfo($this->commander->getActualPath() ."/". $this->commander->getLastFile(), PATHINFO_FILENAME));
        } else {
            $lastfoto = 1;
        }

        $this->commander->moveUp();
        $this->commander->moveToDir("files");

        if($this->commander->countFiles() > 0) {
            $lastfile = intval(pathinfo($this->commander->getActualPath() ."/". $this->commander->getLastFile(), PATHINFO_FILENAME));
        } else {
            $lastfile = 1;
        }

        $this->commander->moveUp();

        if($lastfoto > $lastfile){
            $newname = $lastfoto;
        } else {
            $newname = $lastfile;
        }

        if($newname != 1) {
            $newname++;
        }
        
        $fileUploader = new FileUploader();
        $fileUploader->thumbPath($path."/thumbs");
        $fileUploader->thumbParams($this->resizeParams[0]);

        $image = array("jpg","png","gif","jpeg");

        for($i = 0; $_FILES["files"][$i];$i++){

            $extension = strtolower(pathinfo($_FILES['files'][$i]["name"],PATHINFO_EXTENSION));

            if(in_array($extension,$image)){

                $fileUploader->setFolder($path."/foto");
                $fileUploader->uploadNewFile($_FILES['files'][$i], $newname, false, true, true, $this->resizeParams[1]);

            } else {

                $fileUploader->setFolder($path."/files");
                $fileUploader->uploadNewFile($_FILES['files'][$i], $newname, false);

            }

            $newname++;
        }

        echo "0->".$this->loadFiles();

    }

}