<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 28.3.2016
 * Time: 20:38
 */
class Icons
{

    private $icons = array();

    function __construct(){

        $tree = new TreeDirectory();
        $URL = $tree->getPathToRegistratura(false,true)."utilities/icons/";

        $this->icons["add"] = array($URL."add/add.png","Přidat");
        $this->icons["addrow"] = array($URL."add/row_add_after.png","Přidat řádek pod");

        $this->icons["close"] = array($URL."close/close.png","Zavřít");

        $this->icons["basicedit"] = array($URL."edit/basicedit.png","Editovat");
        $this->icons["edit"] = array($URL."edit/edit.png","Editovat");

        $this->icons["doc"] = array($URL."filesTypes/doc.png","doc");
        $this->icons["docx"] = array($URL."filesTypes/docx.png","docx");
        $this->icons["odt"] = array($URL."filesTypes/odt.png","odt");
        $this->icons["others"] = array($URL."filesTypes/others.png","Neznámý formát");
        $this->icons["pdf"] = array($URL."filesTypes/pdf.png","pdf");
        $this->icons["rar"] = array($URL."filesTypes/rar.png","rar");
        $this->icons["xls"] = array($URL."filesTypes/xls.png","xls");
        $this->icons["zip"] = array($URL."filesTypes/zip.png","zip");
        $this->icons["help"] = array($URL."help/help.png","help");

        $this->icons["crop"] = array($URL."img/crop.png","crop");
        $this->icons["turnleft"] = array($URL."img/turnleft.png","Otočit doleva");
        $this->icons["turnright"] = array($URL."img/turnright.png","Otočit doprava");

        $this->icons["move"] = array($URL."move/move.png","Posunout/Přesunout");

        $this->icons["delete"] = array($URL."remove/remove.png","Odebrat");
        $this->icons["remove"] = array($URL."remove/remove.png","Odebrat");
        $this->icons["removeall"] = array($URL."remove/removeall.png","Odebrat vše");

        $this->icons["save"] = array($URL."save/save.png","Uložit");

        $this->icons["update"] = array($URL."update/update.png","Aktualizovat");

        $this->icons["right"] = array($URL."validation/right.png","Ok");
        $this->icons["wrong"] = array($URL."validation/wrong.png","Špatný údaj");

        $this->icons["camera"] = array($URL."camera.png","Vybrat novou fotku");

        $this->icons["info"] = array($URL."info.png","Zobrazit podrobnosti");

        $this->icons["arrow_bottom"] = array($URL."arrows/arrow_bottom.png","Rozrolovat");
        $this->icons["arrow_right"] = array($URL."arrows/arrow_right.png","Vpravo");
        $this->icons["arrow_left"] = array($URL."arrows/arrow_left.png","Vlevo");
        
        $this->icons["search"] = array($URL."search/search.png","Najít");

        $this->icons["basket"] = array($URL."basket.png","Vložit do košíku");
        $this->icons["back"] = array($URL."back/back.png","Zpět");
        $this->icons["upload"] = array($URL."upload/upload.png","Nahrát");
    }

    public function getIcon($name,$width = "25px",$title="",$advanced = ""){

        if($title == ""){
            $title = $this->icons[$name][1];
        }

        return '<img src='.$this->icons[$name][0].' width="'.$width.'" title="'.$title.'" alt="'.$title.'" style="cursor: pointer;" '.$advanced.'/>';
    }

    public function loadIcons(){

        $icons = array();
        $names = array();

        foreach($this->icons as $key => $value){

            array_push($names, $key);
            array_push($icons, '<img src='.$value[0].' width="25px" title="'.$value[1].'" alt="'.$value[1].'"/>');
        }

        return json_encode(array($names,$icons));

    }

}