<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 19.2.2015
 * Time: 18:12
 */

class URL{

    private $main;
    private $gets;

    private function joinGets(){
        $output = "";

        foreach($this->gets as $key => $value){

            if($output != ''){
                $output .= "&".$key."=".$value;
            } else {
                $output .= "?".$key."=".$value;
            }

        }

        return $output;
    }

    function __construct($fullUrl){

        $parts = explode("?", $fullUrl);

        $this->main = $parts[0];

        if($parts[1] != ''){

            $getP = explode("&", $parts[1]);

            foreach($getP as $getI){

                $getIP = explode("=", $getI);
                $this->gets[$getIP[0]] = $getIP[1];

            }

        }
    }

    public function dropGet()
    {
        $this->gets = array();
        return $this;
    }

    public function addGetPart($part, $val){

        if(!array_key_exists($part, $this->gets)) {
            $this->gets[$part] = $val;
        } else {
            $this->changeGetPart($part, $val);
        }

        return $this;
    }

    public function dropGetPart($part){

        if(array_key_exists($part, $this->gets)) {
            unset($this->gets[$part]);
        }

        return $this;
    }

    public function changeGetPart($part, $val){

        if(array_key_exists($part, $this->gets)) {
            $this->gets[$part] = $val;
        }

        return $this;
    }

    public function __toString()
    {
        return $this->main.$this->joinGets();
    }

}

class TreeDirectory {

    private $root;
    private $project;

    function __construct()
    {
        $this->root = ROOT;
        $this->project = PATH_TO_PROJECT;
        $this->registratura = PATH_TO_REGISTRATURA;
    }

    public function getRoot(){
        return $this->root;
    }

    public function getPathToProject($absolute = false, $withUrl = false){

        if($absolute){
            return $this->root.$this->project;
        }

        if($withUrl){
            return $this->getUrlDomain().$this->project;
        }

        return $this->project;
    }

    public function getPathToRegistratura($absolute = false, $withUrl = false)
    {

        if ($absolute) {
            return $this->getPathToProject(true) . $this->registratura;
        }

        if ($withUrl) {
            return $this->getPathToProject(false,true).$this->registratura;
        }

        return $this->registratura;
    }

    private function urlOrigin($s, $use_forwarded_host=false)
    {
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
        $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    private function fullUrl($s, $use_forwarded_host=false)
    {
        return $this->urlOrigin($s, $use_forwarded_host) . $s['REQUEST_URI'];
    }

    public function getUrlDomain()
    {

        $url = $this->fullUrl($_SERVER);
        $url = str_replace("//", "*", $url);
        $url = explode("/", $url);

        $needed = str_replace("*", "//", $url[0]);

        return $needed . "/~vidlak/";

    }

    public function getFullUrl(){

        $full = $this->fullUrl($_SERVER);
        return new URL($full);

    }

    public function getAppPath($dir, $absolute = false, $withURL = false){

        $dirP = explode("/", $dir);
        unset($dirP[count($dirP) - 1]);
        $dir = join("/",$dirP);

        $projP =  str_replace($this->getPathToProject(true,false),"",$dir)."/";

        if($absolute){
            return $this->getPathToProject(true,false).$projP;
        } elseif($withURL){
            return $this->getPathToProject(false,true).$projP;
        } else {
            return $projP;
        }


    }

}