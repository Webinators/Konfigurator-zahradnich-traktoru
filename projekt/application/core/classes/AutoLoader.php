<?php

class AutoLoader {

    private $projRoot;
    private $classN;

    private $path = array();
    private static $autoLoader = null;

    public static function initialize(){

        if (!isset(self::$autoLoader)) {
            self::$autoLoader = new AutoLoader();
        }

        return self::$autoLoader;
    }

    private function __construct(){

        $classesDestination = AUTOLOADER_PATHS;
        $this->projRoot = ROOT.PATH_TO_PROJECT;

        $data = file_get_contents($classesDestination);
        $paths = explode(",", $data);

        foreach ($paths as $val) {
            if(!in_array($val,$this->path)){
                array_push($this->path, $val);
            }
        }
    }

    public function addPath($path)
    {

        if(is_dir(rtrim($this->projRoot.$path,"/"))) {

            if(!in_array($path,$this->path)){
                file_put_contents(AUTOLOADER_PATHS, join(",",$this->path).','.$path);
            }

        } else {
            throw new Exception("Špatně uvedená cesta nebo uvedná složka neexistuje.");
        }
    }

    private function readDir($Hpath,$dir){

        $Hpath = rtrim($Hpath,"/");

        if($dir != ''){
            $dir = "/".$dir;
        } else {
            $dir = "";
        }

        $filesArr = array();

        if ($open = opendir($Hpath . $dir)) {

            while (($file = readdir($open)) !== false) {

                if ($file != '..' && $file != '.') {

                    if (is_dir($Hpath . $dir . "/" . $file)) {

                        if ($dir != '') {
                            array_push($filesArr, $dir . "/" . $file);
                        } else {
                            array_push($filesArr, $file);
                        }

                    } else {

                        if ($file == $this->classN . ".php") {

                            return $Hpath . $dir . "/" . $this->classN . ".php";

                        }

                    }

                }
            }

            return $filesArr;
        }
    }

    public function getClassFile($name)
    {

        $this->classN = $name;

        foreach ($this->path as $Hpath) {

            if(is_dir($this->projRoot.$Hpath)) {

                $dirArray = $this->readDir($this->projRoot . $Hpath, "");

                if (!empty($dirArray)) {

                    while (is_array($dirArray)) {

                        foreach ($dirArray as $value) {

                            $deepPath = $value;

                            $data = $this->readDir($this->projRoot . $Hpath, $deepPath);

                            if (is_array($data)) {
                                $dirArray = array_merge($dirArray, $data);
                            } else {
                                return $data;
                            }

                            $index = array_search($value, $dirArray);

                            unset($dirArray[$index]);

                            if(empty($dirArray)){
                                $dirArray = "";
                            }

                        }
                    }

                    if ($dirArray) {
                        return $dirArray;
                    } else {
                        continue;
                    }

                }
            }
        }
    }
}