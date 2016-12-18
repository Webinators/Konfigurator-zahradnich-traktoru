<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 28. 10. 2016
 * Time: 16:02
 */

use System\Objects\Collection;

class konfigurator
{

    private $db;
    use Collection\ObjSet;

    private $relPath;
    private $urlPath;

    function __construct()
    {

        $this->loadSystem();

        $this->db = new Database(DB_HOST, DB_NAME);

        $this->relPath = $this->Root->getAppPath(__DIR__);
        $this->urlPath = $this->Root->getAppPath(__DIR__, false, true);

        $this->formE = new FormElements();

    }

    public function index()
    {

        $view = new View($this->Root->getAppPath(__DIR__,true), "index");
        $view->title = "Konfigurátor";

        $view->url = $this->Root->getPathToProject(false, true);
        $view->packages = $this->Root->getAppPath(__DIR__,false,true);

        $this->db->addWherePart("p.ID_parametr","IN","46,45,44,43,42");
        $this->db->selectFromTable("p.ID_parametr AS ID_parametr,Nazev,ID_hodnota,Hodnota_h","Parametr p NATURAL JOIN ParametrHodnota NATURAL JOIN HodnotyList","","p.ID_parametr,Poradi->ASC");
        $view->params = $this->db->getRows();
        $view->icons = $this->Icons;


        return $view->display("main");

    }

    private function getIDToSearch($par, $hod){

        $db = new Database(DB_HOST, DB_NAME);

        $db->addWherePart("ID_parametr","=",$par);
        $db->addWherePart("AND","ID_hodnota","=",$hod);
        $db->selectFromTable("Poradi","ParametrHodnota");
        $poradi = $db->getRows();

        $db->addWherePart("ID_parametr","=",$par);
        $db->addWherePart("AND","Poradi",">",$poradi[0]["Poradi"]);
        $db->selectFromTable("ID_hodnota","ParametrHodnota");

        $data = $db->getRows();

        $arr = array();

        foreach ($data as $par){
            array_push($arr, $par["ID_hodnota"]);
        }

        return $arr;
    }

    public function search($order = "Cena->ASC"){

        $view = new View($this->Root->getAppPath(__DIR__,true), "index");
        $view->title = "Nalezené traktory";

        $view->url = $this->Root->getPathToProject(false, true);
        $view->packages = $this->Root->getAppPath(__DIR__,false,true);        
        
        $data = $_POST;
        $results = array();

        // Pevne hodnoty

        for($i = 0; $i < count($data["parametr"]["id"]); $i++){

            $results[$i] = array();

            $this->db->addWherePart("ID_parametr","=",$data["parametr"]["id"][$i],array(true,false));
            $this->db->addWherePart("AND","ID_hodnota","IN",join(",",$this->getIDToSearch($data["parametr"]["id"][$i],$data["parametr"]["hodnota"][$data["parametr"]["id"][$i]])),array(false,true));

            $this->db->selectFromTable("ID_produkt","ProduktParam","","ID_produkt->ASC");
            $products = $this->db->getRows();

            foreach ($products as $product){
                array_push($results[$i],$product["ID_produkt"]);
            }

        }

        $i = count($results);

        //Parcela

        $results[$i] = array();

        $this->db->addWherePart("ID_parametr","=",42,array(true,false));
        if($data["parcela"] < 61){
            $this->db->addWherePart("AND","Hodnota","<=",$data["parcela"],array(false,true));
        } else {
            $this->db->addWherePart("AND","Hodnota",">=",$data["parcela"],array(false,true));
        }

        $this->db->selectFromTable("ID_produkt","ProduktParam","","ID_produkt->ASC");
        $products = $this->db->getRows();

        foreach ($products as $product){
            array_push($results[$i],$product["ID_produkt"]);
        }

        $i++;

        // Sklon

        $results[$i] = array();

        $this->db->addWherePart("ID_parametr","=",13,array(true,false));
        if($data["sklon"] < 16){
            $this->db->addWherePart("AND","Hodnota","<=",$data["sklon"],array(false,true));
        } else {
            $this->db->addWherePart("AND","Hodnota",">=",$data["sklon"],array(false,true));
        }

        $this->db->selectFromTable("ID_produkt","ProduktParam","","ID_produkt->ASC");
        $products = $this->db->getRows();

        print_r($products);

        foreach ($products as $product){
            array_push($results[$i],$product["ID_produkt"]);
        }

        // Vyfiltrovani

        for ($j = 0; $j < count($results[0]); $j++){

            for($i = 1; $i < count($results); $i++){

                if(!in_array($results[0][$j],$results[$i])){
                    unset($results[0][$j]);
                    continue;
                }

            }

        }

        $this->db->addWherePart("ID_produkt","IN",join(",",$results[0]));
        $this->db->selectFromTable("*","Produkt","",$order);

        $view->products = $this->db->getRows();

        return $view->display("main");
        
    }

}