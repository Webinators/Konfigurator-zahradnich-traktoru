<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 14.8.2016
 * Time: 18:20
 */

use System\Objects\Collection;

class ImagesEditor
{
    use Collection\ObjSet;

    private $origPic;
    private $origPicParams;

    private $path;

    private $params;
    private $options;
    private $img;

    private $imgEdit;

    function __construct($path, $imgNameExt, $img, $uploadResizeParams = "",$options = array(true,true,true,true,false))
    {
        $this->imgEdit = new ImgEdit();

        $this->loadSystem();

        $this->Permissions->addNewCategory("Správa obrázků");
        $this->Permissions->addNewPermission("Úprava obrázků","Správa obrázků");
        $this->Permissions->addNewPermission("Změnit obrázek","Správa obrázků");
        $this->Permissions->addNewPermission("Odebrat obrázek","Správa obrázků");

        $icons = new Icons();
        $this->img = $img;

        if($this->Permissions->userHasAuthorization("Úprava obrázků")) {

            $this->params = $uploadResizeParams;
            $this->path = $path."/".$imgNameExt;

            $URL = $this->Root->getAppPath(__DIR__,false,true)."load/";

            $btns = array(
                (($this->Permissions->userHasAuthorization("Změnit obrázek") && $options[0]) ? '<a href="'.$URL.'changeIMG.php" id="MainIMGEditOption1">'.$icons->getIcon("camera").'</a>' : ""),
                (($this->Permissions->userHasAuthorization("Změnit obrázek") && $options[1]) ? '<a id="MainIMGEditOption2">'.$icons->getIcon("crop").'</a>' : ""),
                (($this->Permissions->userHasAuthorization("Změnit obrázek") && $options[2])? '<a href="'.$URL.'rotate.php" id="MainIMGEditOption3">'.$icons->getIcon("turnleft").'</a>' : ""),
                (($this->Permissions->userHasAuthorization("Změnit obrázek") && $options[3]) ? '<a href="'.$URL.'rotate.php" id="MainIMGEditOption4">'.$icons->getIcon("turnright").'</a>' : ""),
                (($this->Permissions->userHasAuthorization("Odebrat obrázek")  && $options[4]) ? '<a href="'.$URL.'remove.php" id="MainIMGEditOption5">'.$icons->getIcon("remove").'</a>' : ""));

            $this->options = join(" ",$btns);

        }
    }

    public function origImage($path, $imgNameExt, $uploadResizeParams = ""){

        $commander = new FileCommander();
        $commander->setPath($path);
        $this->origPic = $commander->getActualPath()."/".$imgNameExt;
        $this->origPicParams = $uploadResizeParams;

    }

    public function changeImg($data)
    {

        if ($this->Permissions->userHasAuthorization("Změnit obrázek")) {

            $widths = explode(",",$data["width"]);
            $heights = explode(",",$data["height"]);
            $resizes = explode(",",$data["resize"]);
            $paths = explode(",",$data["path"]);

            if ($paths[0] != '') {

                $dest = "";
                $time = time();

                $commander = new FileCommander();
                $uploader = new FileUploader();

                if(count($paths) == 2){

                    $filename = pathinfo($paths[0], PATHINFO_FILENAME);

                    $desP = explode("/", $paths[0]);
                    unset($desP[count($desP) - 1]);
                    $paths[0] = implode("/", $desP);

                    $commander->setPath($paths[0]);
                    $commander->removeFile($filename);

                    $uploader->thumbPath($paths[0]);
                    $uploader->thumbParams($widths[0].",".$heights[0].",".$resizes[0]);

                    $filename = pathinfo($paths[1], PATHINFO_FILENAME);

                    $desP = explode("/", $paths[1]);
                    unset($desP[count($desP) - 1]);
                    $paths[1] = implode("/", $desP);

                    $commander->setPath($paths[1]);
                    $commander->removeFile($filename);

                    $uploader->setFolder($paths[1]);
                    $uploader->UploadNewFile($_FILES['image'][0], $filename, true, true, true, "" . $widths[1] . "," . $heights[1] . "," . $resizes[1] . "");

                    $commander->setPath($paths[0]);
                    
                    $file = $commander->searchFile($filename);
                    $dest = $paths[0]."/".$file[0]."?" . $time . ",".$paths[0]."/".$file[0]."?" . $time . "";

                } else {

                    $filename = pathinfo($paths[0], PATHINFO_FILENAME);

                    $desP = explode("/", $paths[0]);
                    unset($desP[count($desP) - 1]);
                    $paths[0] = implode("/", $desP);

                    $commander->setPath($paths[0]);
                    $commander->removeFile($filename);

                    $uploader->setFolder($paths[0]);
                    $uploader->UploadNewFile($_FILES['image'][0], $filename, true, true, true, "" . $widths[0] . "," . $heights[0] . "," . $resizes[0] . "");

                    if($dest == '') {
                        $file = $commander->searchFile($filename);
                        $dest = $paths[0]."/".$file[0]."?" . $time . "";
                    }

                }

                return $dest;

            } else {
                throw new Exception("1->Není definovaná cesta!");
            }

        } else {
            throw new Exception("1->Nemáte patřičná práva!");
        }

    }

    public function rotateImg($data)
    {
        if ($this->Permissions->userHasAuthorization("Změnit obrázek")) {

            $destinations = explode(",",$data["path"]);
            $degrees = $data["degrees"];

            $commander = new FileCommander();
            $dest = "";
            $time = time();

            foreach($destinations as $destination) {

                $desP = explode("/", $destination);
                $filename = array_pop($desP);
                $path = implode("/", $desP);

                $this->imgEdit->setInputDir($path);
                $this->imgEdit->setOutputDir($path);

                $image = $this->imgEdit->loadImage($filename);
                $image->rotate($degrees);
                $image->save();

                $commander->setPath($path);
                
                $file = $commander->searchFile($filename);
                $dest .= ",".$path."/".$file[0]."?".$time;

            }
            
            return ltrim($dest,",");

        } else {
            throw new Exception("Nemáte patřičná práva!");
        }

    }

    public function removeImg($data){

        if($this->Permissions->userHasAuthorization("Změnit obrázek")) {

            $destinations = explode(",",$data["path"]);

            foreach($destinations as $destination) {

                $destination = explode("/", $destination);
                $file = array_pop($destination);
                $file = explode(".", $file);
                $destination = implode("/", $destination);

                try {

                    $commander = new FileCommander();
                    $commander->setPath($destination);
                    $commander->removeFile($file[0], $file[1]);

                } catch (Exception $e) {
                    echo $e->getMessage();
                }

            }

            return "ok";

        } else {
            throw new Exception("1->Nemáte patřičná práva!");
        }

    }

    public function __toString()
    {

        if($this->Permissions->userHasAuthorization("Úprava obrázků")) {

            $params = explode(",",$this->params);
            $paramsO = explode(",", $this->origPicParams);

            if(count($params) > count($paramsO)){
                $count = count($params);
            } else {
                $count = count($paramsO);
            }

            $paramsSet = array();

            for($i = 0; $i < $count; $i++){

                $data = array();

                if($params[$i] != ''){
                    array_push($data, $params[$i]);
                }

                if($paramsO[$i] != ''){
                    array_push($data, $paramsO[$i]);
                }

                $paramsSet[$i] = $data;

            }

            $paths = array();
            array_push($paths, $this->path);
            if($this->origPic != ''){array_push($paths, $this->origPic);}

            $params = '
             <input type="hidden" name="width" value="'.join(",",$paramsSet[0]).'"/>
             <input type="hidden" name="height" value="'.join(",",$paramsSet[1]).'"/>
             <input type="hidden" name="resize" value="'.join(",",$paramsSet[2]).'"/>
             <input type="hidden" name="path" value="'.join(",", $paths).'"/>
           ';

            $editor = new Editor();
            return "".$editor->build($params.$this->img, $this->options);

        } else {
            return "".$this->img;
        }

    }

}