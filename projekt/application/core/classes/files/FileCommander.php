<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 25.1.2016
 * Time: 15:55
 */

class FileCommander
{

    private $path;

    private $dirs = array();
    private $files = array();

    private $actualPath = "";

    function __construct()
    {


    }

    private function checkPath($path){

        $tree = new TreeDirectory();
        $absolute = $tree->getRoot().$tree->getPathToProject();

        if(realpath($path)){

            if(strpos($path, $absolute) !== false){
                return $path;
            } else {
                return $absolute.$path;
            }

        } else {

            if(realpath($absolute.$path)){

                return $absolute.$path;

            } else {
                throw new Exception("Složka nenalezena, zadávejte cestu ke složkám od kořenovýho odresáře projektu!!!");
            }

        }

    }

    public function setPath($path)
    {
        $path = $this->checkPath($path);

        $this->path = $path;
        $this->actualPath = $path;

        $this->getDirContent();
    }

    public function getActualPath($relative = false, $url = false){
    
        $tree = new TreeDirectory();       
        $rel = str_replace($tree->getRoot(),"",$this->actualPath);
        
        if($relative ){          
            return $rel;         
        }
    
        if($url){        
            return $tree->getUrlDomain().$rel;        
        }
    
        return $this->actualPath;
        
    }

    private function getDirContent()
    {

        $this->dirs = array();
        $this->files = array();

        $cdir = scandir($this->actualPath);

        foreach ($cdir as $key => $value) {

            if ($value != "." && $value != "..") {

                if (is_dir($this->actualPath . "/" . $value)) {
                    array_push($this->dirs, $value);
                } else {
                    array_push($this->files,$value);
                }
            }
        }

        $this->sort();
    }

    private function sort($files = true, $dirs = true){

        if($files) {
            usort($this->files, "strnatcasecmp");
        }
        if($dirs) {
            usort($this->dirs, "strnatcasecmp");
        }
    }

    public function moveUp(){

        if($this->path != $this->actualPath) {
            $parts = explode("/", $this->actualPath);
            unset($parts[count($parts) - 1]);
            $this->actualPath = implode("/",$parts);

            $this->getDirContent();
        }
    }

    public function dirExists($name)
    {

        foreach ($this->dirs as $value) {
            if ($value == $name) {
                return true;
            }
        }

        return false;
    }

    public function fileExists($name){

        foreach($this->files as $value) {
            if ($value == $name) {
                return true;
            }
        }

        return false;
    }

    public function moveToDir($name)
    {

        $name = rtrim($name, "/");

        if ($this->dirExists($name)) {

            $this->actualPath .= "/" . $name;
            $this->getDirContent();
            return true;

        }
        return false;
    }

    public function getDirs()
    {
        return $this->dirs;
    }

    public function countDirs(){
        return count($this->dirs);
    }

    public function countFiles(){
        return count($this->files);
    }

    public function getLastFile()
    {
        if(!empty($this->files)){
            return $this->files[count($this->files)-1];
        } else {
            return "";
        }
    }

    public function getFiles($extensions = "",$withPath = false)
    {

        $searched = array();

        if ($extensions != '') {

            $extensions = explode(",", strtolower($extensions));

            foreach ($this->files as $value) {
                if (in_array(strtolower(pathinfo($this->actualPath . "/" . $value, PATHINFO_EXTENSION)), $extensions)) {

                    if ($withPath) {
                        array_push($searched, $this->actualPath . "/" . $value);
                    } else {
                        array_push($searched, $value);
                    }
                }

            }

        } else {

            foreach ($this->files as $value) {

                if ($withPath) {
                    array_push($searched, $this->actualPath . "/" . $value);
                } else {
                    array_push($searched, $value);
                }

            }

        }

        return $searched;

    }

    public function searchFile($name, $extension = ""){

        $found = array();

        foreach ($this->files as $value) {

            if($extension != '') {

                if ($name . "." . $extension == $value){
                    return $value;
                }

            } else {

                $value = explode(".",$value);

                if ($name == $value[0]){
                    array_push($found,implode(".",$value));
                }

            }
        }

        if(!empty($found)){
            return $found;
        }

        return false;

    }

    public function addDir($dirName, $chmod = 0755){

        $dirName = rtrim($dirName, "/");

        if(!$this->dirExists($dirName)) {
            umask(0000);
            if (!mkdir($this->actualPath . "/" . $dirName, $chmod)) {
                throw new Exception("Vyskytl se problém při vytváření složky.");
            }

            array_push($this->dirs, $dirName);
            $this->sort(false,true);

        } else {
            return false;
        }
    }

    public function createFile($name){

        if(preg_match("/^.*\..*/",$name)) {
            if (!$this->fileExists($name)) {

                $f = fopen($this->actualPath ."/". $name, "w+");
                $f = fwrite($f, "\n");
                fclose($f);

                array_push($this->files, $name);

            } else {
                return false;
            }
        } else {
            throw new Exception("Špatný název souboru");
        }
    }

    public function rewriteFile($name, $data){

        if($this->fileExists($name)) {

            file_put_contents($this->actualPath."/".$name, $data);

        } else {
            throw new Exception("Soubor neeexistuje");
        }
    }

    public function appendToFile($name, $data){

        if($this->fileExists($name)) {

            $old = file_get_contents($this->actualPath."/".$name);
            file_put_contents($this->actualPath."/".$name, $old . $data);

        } else {
            throw new Exception("Soubor neeexistuje");
        }
    }

    public function copyFileFrom($fileNameExt, $filePath, $newName = ""){

        $path = $this->checkPath($filePath);
        copy($path."/".$fileNameExt, $this->actualPath."/".$fileNameExt);

        if($newName != ''){
            $ext = pathinfo($this->actualPath."/".$fileNameExt, PATHINFO_EXTENSION);
            rename($this->actualPath."/".$fileNameExt, $this->actualPath."/".$newName.".".$ext);
        }

    }

    public function removeFile($name, $extension = "")
    {

        $files = $this->searchFile($name,$extension);

        if(is_array($files) && !empty($files)){

            foreach($files as $value){
                unlink($this->actualPath . "/" . $value);
            }

            return true;

        } else {

            if($files != ''){
                unlink($this->actualPath . "/" . $files);
                return true;
            }
        }

        return false;
    }

    public function removeDir($name){

        if($this->dirExists($name)){

            $this->DeleteDir($this->actualPath . "/" . $name);
            rmdir($this->actualPath . "/" . $name);

        } else {
            throw new Exception("Složka nenalezena");
        }

    }

    public function clearDir($name){

        if($this->dirExists($name)){
            $this->DeleteDir($this->actualPath . "/" . $name);
        } else {
            throw new Exception("Složka nenalezena");
        }

    }

    private function DeleteDir($path)
    {

        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            if(is_dir($path . '/' . $file)) {
                $this->DeleteDir($path . '/' . $file);
                rmdir($path . '/' . $file);
            } else {
                unlink($path . '/' . $file);
            }
        }

    }

}
?>