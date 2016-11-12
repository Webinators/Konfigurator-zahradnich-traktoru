<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 17.3.2015
 * Time: 19:18
 */

use System\Objects\Collection;

class Image
{
    private $name;
    private $extension;

    private $newPath;
    private $oldPath;

    private $origWidth;
    private $origHeight;
    private $origExtension;

    private $width;
    private $height;

    private $type;

    private $GDImage;

    private $commander;
    private $imagesEP;

    function __construct($pic, ImgEdit $im, $GD)
    {

        $this->commander = new FileCommander();
        $this->imagesEP = $im;

        if ($this->imagesEP->isValidIMG($GD)) {
            $this->GDImage = $GD;
        } else {
            throw new Exception("Obrázek, se kterým se má pracovat, musí být otevřený v GD knihovně!");
        }

        $this->name = pathinfo($pic, PATHINFO_FILENAME);
        $this->extension = pathinfo($pic, PATHINFO_EXTENSION);

        list($width, $height) = getimagesize($pic);

        $this->width = $width;
        $this->height = $height;

        $this->origWidth = $width;
        $this->origHeight = $height;
        $this->origExtension = $this->extension;

        $this->type = exif_imagetype($pic);

        $this->newPath = $this->imagesEP->newDestination();
        $this->oldPath = $this->imagesEP->destination();

    }

    public function isJPG(){
        if($this->type == 2){
            return true;
        }
        return false;
    }

    public function isPNG(){
        if($this->type == 3){
            return true;
        }
        return false;
    }

    public function isGIF(){
        if($this->type == 1 || $this->type == 0){
            return true;
        }
        return false;
    }

    public function name(){
        return $this->name;
    }

    public function extension(){
        return $this->extension;
    }

    public function origExtension(){
        return $this->origExtension;
    }

    public function newPath(){
        return $this->newPath;
    }

    public function GDImage(){
        return $this->GDImage;
    }

    public function width(){
        return $this->width;
    }

    public function height(){
        return $this->height;
    }

    public function origWidth(){
        return $this->origWidth;
    }

    public function origHeight(){
        return $this->origHeight;
    }

    public function removeOriginal(){

        if($this->oldPath != $this->newPath) {
            $this->commander->setPath($this->oldPath);
            $this->commander->removeFile($this->name, $this->origExtension);
        }

    }

    public function rotate($degrees)
    {
        if (isset($degrees)) {

            $rotate = imagerotate($this->GDImage, $degrees, 0);
            $this->GDImage = $rotate;

        } else {
            throw new Exception("Není zadán úhel otočení obrázku!!!");
        }
    }

    public function autoRotate()
    {

        $exif = exif_read_data($this->oldPath . "/" . $this->name . "." . $this->extension);
        $orientation = $exif['THUMBNAIL']['Orientation'];

        switch ($orientation) {
            case 3:
                $rotate = imagerotate($this->GDImage, 180, 0);
                break;
            case 6:
                $rotate = imagerotate($this->GDImage, -90, 0);
                break;
            case 8:
                $rotate = imagerotate($this->GDImage, 90, 0);
                break;
            default:
                $rotate = $this->GDImage;
                break;
        }

        $this->GDImage = $rotate;

    }

    private function resampleImg($width, $height, $x = 0, $y = 0)
    {

        if ($x > 0 || $y > 0) {

            $widthO = $width;
            $heightO = $height;

        } else {

            $widthO = $this->width;
            $heightO = $this->height;
        }

        $newImg = $this->imagesEP->createImage("truecolor", $width, $height);

        if ($this->isPNG()) {

            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);

        } elseif ($this->isGIF()) {

            $transparent = imageColorTransparent($this->GDImage);

            if ($transparent != -1) {
                $transparent_color = imageColorsForIndex($this->GDImage, $transparent);

                $transparent_new = imageColorAllocate($newImg, $transparent_color["red"], $transparent_color["green"], $transparent_color["blue"]);
                $transparent_new_index = imageColorTransparent($newImg, $transparent_new);
                imageFill($newImg, 0, 0, $transparent_new_index);
            }

            imageCopyResized($newImg, $this->GDImage, 0, 0, $x, $y, $width, $height, $widthO, $heightO);
        }

        if (!$this->isGIF()) {
            imagecopyresampled($newImg, $this->GDImage, 0, 0, $x, $y, $width, $height, $widthO, $heightO);
        }

        $this->GDImage = $newImg;

        $this->width = $width;
        $this->height = $height;

    }

    public function cropImage($x, $y, $width, $height)
    {
        $this->resampleImg($width, $height, $x, $y);
    }

    private function imageResize($new_width = 0, $new_height = 0)
    {

        if ($new_height != 0) {

            $prc = (100 * $new_height) / $this->height;
            $height = $new_height;
            $width = ($this->width * $prc) / 100;

        } else {

            $prc = (100 * $new_width) / $this->width;
            $width = $new_width;
            $height = ($this->height * $prc) / 100;
        }

        $this->resampleImg($width, $height);

    }

    public function transparentBackground($width, $height)
    {
        $resultPic = $this->imagesEP->createImage("truecolor", $width, $height);
        $black = imagecolorallocate($resultPic, 0, 0, 0);
        imagecolortransparent($resultPic, $black);

        $x = ($width / 2) - ($this->width / 2);
        $y = ($height / 2) - ($this->height / 2);

        imagecopymerge($resultPic, $this->GDImage, $x, $y, 0, 0, $this->width, $this->height, 100);

        if($this->isJPG()) {
            $this->type = 3;
            $this->extension = "png";
        }

        $this->GDImage = $resultPic;
    }

    public function resize($width = 0, $height = 0, $method = "cropp")
    {

        /* method: is used when both width and height are defined.

            supplement - will create transparent background with defined dimensions, if is height or width of image larger
                         than defined width or height, it will change larger dimension to defined dimension and then position
                         the picture to the center od background.
            cropp       - will change larger dimension the defined dimension and then will crop image with second dimension
                         from the middle of picture. (looses data)
        */

        if($width != 0 && $height != 0) {

            $widthO = $this->width;
            $heightO = $this->height;

            if (($widthO / $heightO) == ($width / $height)) {

                $this->imageResize($width);

            } else {

                if(($widthO == $heightO) && ($widthO > $width)){

                    if($width > $height){
                        $this->imageResize(0,$height);
                    } else {
                        $this->imageResize($width);
                    }

                } else if(($widthO > $heightO) && ($widthO > $width)){

                    if($method == "cropp") {
                        if ($heightO > $height) {
                            $this->imageResize(0, $height);
                        }
                    } else {
                        if ($heightO > $height) {
                            $this->imageResize($width);
                        }
                    }

                } else {

                    if($method == "cropp") {
                        if ($widthO > $width) {
                            $this->imageResize($width);
                        }
                    } else {
                        if ($widthO > $width) {
                            $this->imageResize(0, $height);
                        }
                    }

                }

                $widthO = $this->width;
                $heightO = $this->height;

                switch($method){

                    case "cropp":

                        if($widthO > $heightO){

                            if($widthO > $width){

                                $x = ($widthO / 2) - ($width / 2);
                                $y = 0;

                                $this->cropImage($x, $y, $width, $heightO);
                            }

                            if($heightO > $height){

                                $x = 0;
                                $y = ($heightO / 2) - ($height / 2);

                                $this->cropImage($x, $y, $widthO, $height);
                            }

                        } else {

                            if($heightO > $height){

                                $x = 0;
                                $y = ($heightO / 2) - ($height / 2);

                                $this->cropImage($x, $y, $widthO, $height);
                            }

                            if($widthO > $width){

                                $x = ($widthO / 2) - ($width / 2);
                                $y = 0;

                                $this->cropImage($x, $y, $width, $heightO);
                            }

                        }

                        if(($widthO < $width) || ($heightO < $height)) {
                            $this->transparentBackground($width, $height);
                        }

                        break;
                    case "supplement":

                        $this->transparentBackground($width,$height);

                        break;
                    default:
                        throw new Exception("Špatná vlastnost u metody u změně velikosti obrázku");
                        break;
                }

            }

        } else {

            if($width > 0){

                $this->imageResize($width);

            } elseif ($height > 0) {

                $this->imageResize(0,$height);

            } else {

                $widthO = $this->width;
                $heightO = $this->height;

                if($width >= $widthO || $height >= $heightO)
                {
                    if($widthO >= $heightO){
                        $this->imageResize(1280);
                    } else {
                        $this->imageResize(0,720);
                    }

                }
            }
        }

    }

    public function cropTransparentBorders()
    {

        // Get the width and height
        $width = $this->width;
        $height = $this->height;
        // Find the size of the borders
        $top = 0;
        $bottom = 0;
        $left = 0;
        $right = 0;
        $bgcolor = 0xFFFFFF; // Use this if you only want to crop out white space
        $bgcolor = imagecolorat($this->GDImage, $top, $left); // This works with any color, including transparent backgrounds
        //top
        for (; $top < $height; ++$top) {
            for ($x = 0; $x < $width; ++$x) {
                if (imagecolorat($this->GDImage, $x, $top) != $bgcolor) {
                    break 2; //out of the 'top' loop
                }
            }
        }
        //bottom
        for (; $bottom < $height; ++$bottom) {
            for ($x = 0; $x < $width; ++$x) {
                if (imagecolorat($this->GDImage, $x, $height - $bottom - 1) != $bgcolor) {
                    break 2; //out of the 'bottom' loop
                }
            }
        }
        //left
        for (; $left < $width; ++$left) {
            for ($y = 0; $y < $height; ++$y) {
                if (imagecolorat($this->GDImage, $left, $y) != $bgcolor) {
                    break 2; //out of the 'left' loop
                }
            }
        }
        //right
        for (; $right < $width; ++$right) {
            for ($y = 0; $y < $height; ++$y) {
                if (imagecolorat($this->GDImage, $width - $right - 1, $y) != $bgcolor) {
                    break 2; //out of the 'right' loop
                }
            }
        }
        //copy the contents, excluding the border

        $this->cropImage($left, $top, $width - ($left + $right), $height - ($top + $bottom));
    }

    public function show()
    {

        switch (strtolower($this->extension)) {

            case "jpg":
            case "jpeg":
                header("Content-Type: image/jpeg");
                imagejpeg($this->GDImage);
                break;
            case "png":
                header("Content-Type: image/png");
                imagepng($this->GDImage);
                break;
            case "gif":
                header("Content-Type: image/gif");
                imagegif($this->GDImage
                );
        }

    }

    public function save()
    {
        $this->imagesEP->SaveImage($this);
    }

}

final class Img{

    private $src;
    private $width;
    private $height;
    private $alt;
    private $title;
    private $id;
    private $class;

    private $obj;

    function __construct($dir, $img)
    {
        $this->src($dir, $img);
        $this->obj = $this;
    }

    public function src($dir, $img){

        if($dir != '') {

            $time = time();
            $tree = new TreeDirectory();

            $URL = $tree->getUrlDomain();
            $reg = $tree->getPathToRegistratura(false, true);
            $root = $tree->getRoot();

            $relative = str_replace($root, "", $dir);

            $imgPath = $URL . $relative."/".$img."?time=".$time;

            $commander = new FileCommander();
            $commander->setPath($dir);

            if ($commander->fileExists($img)) {
                $this->src = $imgPath;
            } else {
                $this->src = $reg . "utilities/undefined.png";
            }

            return $this->obj;

        } else {
            throw new Exception("Není uvedná cesta k obrázku");
        }

    }

    public function width($width){
        $this->width = $width;
        return $this->obj;
    }

    public function height($height){
        $this->height = $height;
        return $this->obj;
    }

    public function alt($alt){
        $this->alt = $alt;
        return $this->obj;
    }

    public function title($title){
        $this->title = $title;
        return $this->obj;
    }

    public function id($id){
        $this->id = $id;
        return $this->obj;
    }

    public function _class($class){
        $this->class = $class;
        return $this->obj;
    }

    public function __toString()
    {
        if($this->src != "") {
            return '<img src="' . $this->src . '" 
            '.(($this->id != "") ? 'id="'.$this->id.'"' : "" ).'
            '.(($this->class != "") ? 'id="'.$this->class.'"' : "" ).'
            '.(($this->width != "") ? 'width="'.$this->width.'"' : "" ).'
            '.(($this->height != "") ? 'height="'.$this->height.'"' : "" ).'
            '.(($this->title != "") ? 'title="'.$this->title.'"' : "image" ).'
            '.(($this->alt != "") ? 'alt="'.$this->alt.'"' : "image" ).'
             />';
        } else {
            throw new Exception("Není uvedná cesta k obrázku");
        }
    }

}

class ImgEdit
{

    use Collection\ObjSet;

    private $destination;
    private $newDestination;
    private $commander;

    public function destination(){
        return $this->destination;
    }

    public function newDestination(){
        return $this->newDestination;
    }

    function __construct()
    {
        $this->loadSystem();
        $this->commander = new FileCommander();
    }

    public function setInputDir($path)
    {
        $this->destination = $this->checkPath($path);
    }

    public function setOutputDir($path)
    {
        $this->newDestination = $this->checkPath($path);
        $this->commander->setPath($this->destination);
    }

    public function isValidIMG($source)
    {

        if (gettype($source) == "resource") {
            if (get_resource_type($source) == "gd") {
                return true;
            }
        }

        return false;
    }

    private function checkPath($destination)
    {

        if ($destination != '') {

            $this->commander->setPath($destination);
            return $this->commander->getActualPath();

        } else {
            throw new Exception("Není uvedená cesta k obrázkům!");
        }

    }

    public function loadImage($imgNameEx)
    {

        if ($this->destination != '' && $this->newDestination != '') {
            if ($imgNameEx != '') {

                $image = $this->createImageFrom($imgNameEx);
                $image = new Image($this->destination . "/" . $imgNameEx, $this, $image);

                return $image;

            } else {
                throw new Exception("Není uveden obrázek");
            }
        } else {
            throw new Exception("Není uvedená cesta zdroje, nebo cesta cíle!");
        }

    }

    private function createImageFrom($imgName)
    {

        $image = false;

        switch (pathinfo($this->destination . "/" . $imgName, PATHINFO_EXTENSION)) {

            case "jpg":
            case "jpeg":
                $image = imagecreatefromjpeg($this->destination . "/" . $imgName);
                break;
            case "png":
                $image = imagecreatefrompng($this->destination . "/" . $imgName);
                break;
            case "gif":
                $image = imagecreatefromgif($this->destination . "/" . $imgName);
        }

        if(!$this->isValidIMG($image)){
            throw new Exception("Načtení obrázku se nepovedlo...");
        }

        return $image;
    }

    public function createImage($type = "truecolor", $width, $height)
    {

        $image = false;

        switch ($type) {

            case "truecolor":

                if (is_numeric($width) && is_numeric($height)) {
                    $image = imagecreatetruecolor($width, $height);
                } else {
                    throw new Exception("Rozměry obrázku musí být číslo!");
                }

                break;

        }

        if(!$this->isValidIMG($image)){
            throw new Exception("Načtení obrázku se nepovedlo...");
        }

        return $image;

    }

    public function SaveImage(Image $img)
    {

        if($this->destination == $this->newDestination){
            $pom = "_";
        } else {
            $pom = "";
        }

        $image = null;

        switch ($img->extension()) {

            case "jpg":
            case "jpeg":
                imagejpeg($img->GDImage(), $img->newPath()."/".$img->name().$pom.".".$img->extension());
                break;
            case "png":
                imagepng($img->GDImage(), $img->newPath()."/".$img->name().$pom.".".$img->extension());
                break;
            case "gif":
                imagegif($img->GDImage(), $img->newPath()."/".$img->name().$pom.".".$img->extension());
        }


        if($this->destination == $this->newDestination) {
            $this->commander->setPath($this->newDestination);
            $this->commander->removeFile($img->name(), $img->origExtension());
            rename($this->newDestination."/".$img->name().$pom.".".$img->extension(), $this->newDestination."/".$img->name().".".$img->extension());
        }


    }

    public function isImage($imgNameExt){

        $ext = strtolower(pathinfo($this->destination()."/".$imgNameExt, PATHINFO_EXTENSION));
        $img = array("jpg,png,gif,jpeg");

        if(in_array($ext, $img)){
            return true;
        }

        return false;

    }

    public function getIMGInHTML($imgNameExt){

        if($this->destination != '') {

            $img = new Img($this->destination, $imgNameExt);
            $img->title($imgNameExt);
            $img->alt($imgNameExt);

            return $img;

        } else {
            throw new Exception("Není uvedná složka s obrázky");
        }
    }

    public function getImgWithOptions($imgNameExt, $img, $uploadResizeParams = "",$options = array(true,true,true,true,false)){
        return new ImagesEditor($this->destination, $imgNameExt, $img, $uploadResizeParams, $options);
    }

}

