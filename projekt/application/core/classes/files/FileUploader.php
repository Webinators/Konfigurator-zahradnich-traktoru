<?php
class FileUploader
{

    private $paths = array();

    private $limits = array();
    private $disabled = 'bin|cgi|exe|pl|py|sh|bat|html|xhtml|css|ico|inc|hphp|module|json';

    private $file = array();

    private $errors = array();
    private $success = array();

    private $newFileNames = array();
    private $origFileNames = array();

    private $commander;

    function __construct($folder = "", $createFolder = false)
    {
        $this->getIniConfig();
        $this->adjustFilesArray();
        $this->commander = new FileCommander();
        $this->createPreloadFolder();
    }

    private function getIniConfig()
    {

        $max_upload_filesize = ini_get('upload_max_filesize');
        $max_post_size = ini_get('post_max_size');

        switch (substr ($max_post_size, -1))
        {
            case 'K': case 'k': $max_post_size *= 1024; $this->limits['max_post_size_default'] = $max_post_size; break;
            case 'M': case 'm': $max_post_size *= pow(1024,2); $this->limits['max_post_size_default'] = $max_post_size; break;
            case 'G': case 'g': $max_post_size *= pow(1024,3); $this->limits['max_post_size_default'] = $max_post_size; break;
            default: throw new Exception("Nepodporovaná jednotka nebo nebyla nalezena max. velikost odeslaných dat v php.ini."); exit;
        }

        $this->limits['max_post_size'] = $this->limits['max_post_size_default'];

        switch (substr ($max_upload_filesize, -1)) {
            case 'K':
            case 'k':
                $max_upload_filesize *= 1024;
                $this->limits['max_upload_size_default'] = $max_upload_filesize;
                break;
            case 'M':
            case 'm':
                $max_upload_filesize *= pow(1024, 2);
                $this->limits['max_upload_size_default'] = $max_upload_filesize;
                break;
            case 'G':
            case 'g':
                $max_upload_filesize *= pow(1024, 3);
                $this->limits['max_upload_size_default'] = $max_upload_filesize;
                break;
            default:
                throw new Exception("Nepodporovaná jednotka nebo nebyla nalezena max. velikost jednoho souboru dat v php.ini.");

        }

        $this->limits['max_upload_size'] =  $this->limits['max_upload_size_default'];
        $this->limits['max_num_files_default'] = ini_get('max_file_uploads');
        $this->limits['max_num_files'] = $this->limits['max_num_files_default'];
    }

    public function setFolder($folder = ""){
        $this->commander->setPath($folder);
        $this->paths['dest_path'] = $this->commander->getActualPath();
    }

    public function setLimits($maxFileSize = "3Mb", $maxNumFiles = 20, $allowedFormats = ""){

        $this->setAllowedTypes($allowedFormats);
        $this->setConfiguration($maxFileSize,$this->limits['max_upload_size']);

        if(($maxNumFiles * $this->limits['max_upload_size']) <= $this->limits['max_post_size_default']){

            if(($maxNumFiles <= $this->limits['max_num_files_default']) && ($maxNumFiles > 0)) {
                $this->limits['max_num_files'] = $maxNumFiles;
            } else {
                $this->limits['max_num_files'] = $maxNumFiles;
            }
        } else {
            $this->limits['max_num_files'] = (int) ($this->limits['max_post_size_default'] / $this->limits['max_upload_size']);
        }

        if(($this->limits['max_upload_size'] * $this->limits['max_num_files']) < $this->limits['max_post_size_default']){
            $this->limits['max_post_size'] = $this->limits['max_upload_size'] * $this->limits['max_num_files'];
        }

    }

    public function restoreLimits(){
        $this->getIniConfig();
        $this->setAllowedTypes("");
    }

    public function getMaxPostSize(){
        return $this->limits['max_post_size'];
    }

    public function getMaxUploadSize(){
        return $this->limits['max_upload_size'];
    }

    public function getMaxNumOfFiles(){
        return $this->limits['max_num_files'];
    }

    public function getDisabledExtensions(){
        return $this->disabled;
    }

    public function getAllowedExtensions(){
        return $this->limits['allowed_formats'];
    }

    public function thumbPath($path){
        $this->commander->setPath($path);
        $this->paths['thumb_path'] = $this->commander->getActualPath();
    }

    public function thumbParams($resizeParams = ""){
        $this->file['thumb_resize_params'] = $resizeParams;
    }

    public function uploadNewFile($file, $newFileName = "", $overwrite = true, $autoRotate = true, $autoResize = true ,$resizeParams = "")
    {
        if($this->paths['dest_path'] != '') {

                if ($newFileName != '') {
                    $this->file['new_name'] = $newFileName;
                }

                $this->file['overwrite'] = $overwrite;
                $this->file['auto_rotate'] = $autoRotate;
                $this->file['auto_resize'] = $autoResize;
                $this->file['resize_params'] = $resizeParams;

                $this->upload($file);

        } else {
            throw new Exception("Nelze nahrát soubor, protože není uvedená cílová složka");
        }
    }

    public function isPostFile($name = "")
    {
        $files = $_FILES;

        if (empty($files)) {
            return false;
        }

        reset($files);

        list($key, $value) = each($files);

        if($name != ""){
            $key = $name;
        }

        if (is_array($value['error'])) {
            if ($files[$key]['error'][0] == 4) {
                return false;
            }
        } else {
            if ($files[$key]['error'] == 4) {
                return false;
            }
        }

        return true;
    }

    private function adjustFilesArray()
    {
        $files = $_FILES;
        $_FILES = array();

        $mainName = array();
        $params = array();
        $keys = array();
        $names = array();
        $types = array();
        $TmpNames = array();
        $errors = array();
        $size = array();

        foreach ($files as $key1 => $value1) {

            array_push($mainName, $key1);

            $keys[$key1] = array();
            $params[$key1] = array();
            $names[$key1] = array();
            $types[$key1] = array();
            $TmpNames[$key1] = array();
            $errors[$key1] = array();
            $size[$key1] = array();

            if (is_array($files[$key1]['name'])) {

                foreach ($value1 as $key2 => $value2) {

                    foreach ($value2 as $key3 => $value4) {

                        if(!in_array($key3,$keys[$key1])) {
                            array_push($keys[$key1], $key3);
                        }
                        if(!in_array($key2,$params[$key1])) {
                            array_push($params[$key1], $key2);
                        }

                        switch ($key2) {

                            case "name":

                                array_push($names[$key1], $value4);

                                break;

                            case "type":

                                array_push($types[$key1], $value4);

                                break;

                            case "tmp_name":

                                array_push($TmpNames[$key1], $value4);

                                break;

                            case "error":

                                array_push($errors[$key1], $value4);

                                break;

                            case "size":
                                array_push($size[$key1], $value4);
                                break;
                        }

                    }
                }

            } else {

                array_push($keys[$key1], 0);

                foreach($value1 as $key2 => $value2) {

                    array_push($params[$key1], $key2);

                    switch ($key2) {

                        case "name":

                            array_push($names[$key1], $value2);

                            break;

                        case "type":

                            array_push($types[$key1], $value2);

                            break;

                        case "tmp_name":

                            array_push($TmpNames[$key1], $value2);

                            break;

                        case "error":

                            array_push($errors[$key1], $value2);

                            break;

                        case "size":
                            array_push($size[$key1], $value2);
                            break;
                    }
                }

            }

        }

        if (!empty($keys)) {

            if(count($keys) <= $this->limits['max_num_files']) {
                if(array_sum($size) <= $this->limits['max_post_size']) {

                    foreach($mainName as $key1) {

                        $fileParam = array();
                        $fileParams = array();

                        $_FILES[$key1] = array();

                        foreach ($keys[$key1] as $value1) {

                            $fileParam[$value1] = array($names[$key1][$value1], $types[$key1][$value1], $TmpNames[$key1][$value1], $errors[$key1][$value1], $size[$key1][$value1]);
                            $fileParams[$value1] = array();

                            foreach ($params[$key1] as $key2 => $value2) {
                                $fileParams[$value1][$params[$key1][$key2]] = $fileParam[$value1][$key2];
                            }

                        }

                        $_FILES[$key1] = $fileParams;
                    }

                } else {
                    throw new Exception("Byl překročen limit celkové velikosti odelaných souborů");
                }
            } else {
                throw new Exception("Byl překročen limit počtu odeslaných soborů. Maximum je: ".$this->limits['max_num_files']."");
            }

        }

    }

    private function createPreLoadFolder()
    {

        $tree = new TreeDirectory();

        if(is_dir($tree->getPathToRegistratura(true,false)."predimage")) {
            $this->commander->setPath($tree->getPathToRegistratura(true, false) . "predimage",false);
        } else {
            $this->commander->setPath($tree->getPathToRegistratura(true, false),false);
            $this->commander->addDir("predimage");
            $this->commander->moveToDir("predimage");
        }

        $this->paths['preload'] = $this->commander->getActualPath()."/";

    }

    private function setConfiguration($limit, &$dest)
    {

        $limit = trim($limit);
        $limit = str_replace(",",".",$limit);


        if (preg_match("/[0-9]*.[0-9]*?KB/i", $limit))
        {
            (int)$limit = str_replace("KB","",$limit);
            $limit *= 1024;
            if($limit <= $this->limits['max_post_size']){
                $dest = $limit;
            }
        }
        elseif (preg_match("/[0-9]*.[0-9]*?MB/i", $limit))
        {
            (int)$limit = str_replace("MB","",$limit);
            $limit *= pow(1024,2);
            if($limit <= $this->limits['max_post_size']){
                $dest = $limit;
            }
        }
        elseif(preg_match("/[0-9]*.[0-9]*?GB/i", $limit))
        {
            (int)$limit = str_replace("GB","",$limit);
            $limit *= pow(1024,3);
            if($limit <= $this->limits['max_post_size']){
                $dest = $limit;
            }
        }
        else
        {
        }

    }

    private function setAllowedTypes($allowedFormats)
    {
        if($allowedFormats != "") {
            $allowedFormats = trim($allowedFormats);
            $this->limits['allowed_formats'] = str_replace(",", "|", $allowedFormats);
        } else {
            $this->limits['allowed_formats'] = "";
        }
    }

    private function upload($file)
    {
        $this->checkFile($file);
    }

    private function checkFile($file)
    {

        switch($file['error'])
        {
            case 0: break;
            case 1:
            case 2:
                array_push($this->errors, "".$file['name']." je příliš velký");  return;
                break;
            case 3:
                array_push($this->errors, "".$file['name']." byl pouze částečně nahrán"); return;
                break;
            default:
                array_push($this->errors, "Vyskytl se nějaký problém při nahrání souboru"); return;
                break;
        }

        $suffix = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name = pathinfo($file['name'], PATHINFO_FILENAME);

        if(!preg_match("/php|phtml[0-9]*?/i", $suffix))
        {

            if(!preg_match("/".$this->disabled."/i", $suffix))
            {
                if($this->limits['allowed_formats'] != '')
                {
                    $pattern = $this->limits['allowed_formats'];

                    if(preg_match("/$pattern/i", $suffix))
                    {
                        $this->checkSize($file);
                        $this->checkName($name, $suffix);
                        array_push($this->origFileNames, $name.".".$suffix);
                        $this->moveFile($file);
                    }
                    else
                    {
                        array_push($this->errors, "Tento soubor nepatří mezi povolené"); return;
                    }
                }
                else
                {
                    $this->checkSize($file);
                    $this->checkName($name, $suffix);
                    array_push($this->origFileNames, $name.".".$suffix);
                    $this->moveFile($file);
                }
            }
            else
            {
                array_push($this->errors, "Je zakázáno nahrát soubor s touto příponou!!!"); return;
            }
        }
        else
        {
            array_push($this->errors, "Je zakázáno nahrát php soubor!!!"); return;
        }
    }

    private function checkSize($file)
    {

        if ($file['size'] == 0)
        {
            array_push($this->errors, "Soubor je prázdný!!!"); return;
        }
        elseif ($file['size'] > $this->limits['max_upload_size'])
        {
            array_push($this->errors, "Soubor je větší než je povoleno!!!"); return;
        }

    }

    private function checkName($name, $suffix)
    {

        if(!preg_match("/\/|\\|&|\||\?|\*/i", $name))
        {
            if($this->file["new_name"] != '')
            {
                $newName = $this->file["new_name"];
                $newFile = $newName.".".$suffix;
            } else {
                $newName = uniqid();
                $newFile = $newName.".".$suffix;
            }

            if($this->file['overwrite'] == false)
            {
                if(!file_exists($this->paths['dest_path'].$newFile))
                {
                    $this->file["new_name"] = $newFile;
                }
                else
                {
                    $i = 1;

                    while(file_exists($this->paths['dest_path'].$newName."_".$i.".".$suffix))
                    {
                        $i++;
                    }

                    $this->file["new_name"] = $newName."_".$i.".".$suffix;

                }
            } else {
                $this->file["new_name"] = $newFile;
            }

        }
        else
        {
            array_push($this->errors, "Soubor obsahuje podezřelé znaky!!!"); return;
        }

    }

    private function moveFile($file)
    {

        $success = move_uploaded_file($file['tmp_name'], $this->paths['preload'] . $this->file["new_name"]);

        array_push($this->errors, "move_uploaded_file(".$file['tmp_name'].", ".$this->paths['preload'] . $this->file["new_name"].")");

        if ($success) {

            $lastFile = end($this->origFileNames);
            array_push($this->success, "Soubor " . $lastFile . " byl úspěšně nahrán ");

        } else {

            $lastFile = end($this->origFileNames);
            array_push($this->errors, "Soubor " . $lastFile . " nebyl úspěšně nahrán, pravděpodobně chyba serveru nebo špatná práva!!!");
            return;

        }

        $suffix = pathinfo($this->paths['preload'] . $this->file["new_name"], PATHINFO_EXTENSION);
        $suffix = strtolower($suffix);

        $preLoadFile = $this->paths['preload'] . $this->file["new_name"];
        $newFile = $this->paths['dest_path']."/". $this->file["new_name"];

        if (preg_match("/jpg|jpeg|png|gif/i", $suffix)) {

            $ImgEdit = new ImgEdit();

            // thumb IMG
            
            if($this->paths["thumb_path"] != ''){

                $ImgEdit->setInputDir($this->paths['preload']);
                $ImgEdit->setOutputDir($this->paths['thumb_path']);
                
                $image = $ImgEdit->loadImage($this->file["new_name"]);

                if ($this->file['auto_rotate']) {
                    $image->autoRotate();
                }
                
                if($this->file["thumb_resize_params"]){

                    $sizes = explode(",", $this->file['thumb_resize_params']);

                    $newWidth = $sizes[0];
                    $newHeight = $sizes[1];

                    $image->resize($newWidth, $newHeight, $sizes[2]);

                    $image->save();
                    
                }
                
            }
            
            // full IMG
            
            $ImgEdit->setInputDir($this->paths['preload']);
            $ImgEdit->setOutputDir($this->paths['dest_path']);

            $image = $ImgEdit->loadImage($this->file["new_name"]);

            if ($this->file['auto_rotate']) {
                $image->autoRotate();
            }

            if ($this->file['resize_params'] != "") {

                $sizes = explode(",", $this->file['resize_params']);

                $newWidth = $sizes[0];
                $newHeight = $sizes[1];

                $image->resize($newWidth, $newHeight, $sizes[2]);

            } else {

                if ($this->file['auto_resize']) {
                    $image->resize();
                }

            }
            
            $image->save();
            $image->removeOriginal();

        } else {

            copy($preLoadFile, $newFile);

        }

        array_push($this->newFileNames, $this->file["new_name"]);

    }

    public function getFilesNames()
    {
        return $this->newFileNames;
    }

    public function getOriginalFilesNames()
    {
        return $this->origFileNames;
    }

    public function printMessage(){

        $message = "";

        foreach($this->errors as $value){
            $message .= $value."<br />";
        }

        foreach($this->success as $value){
            $message .= $value."<br />";
        }

        return $message;

    }

    public function countErrors(){
        return count($this->errors);
    }

    public function getResult()
    {
        $result = array(0 => $this->success, 1 => $this->errors);

        return $result;
    }

}
?>