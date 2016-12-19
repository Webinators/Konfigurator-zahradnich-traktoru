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

        if($this->Sessions->sessionExists("konfigurator-products")){
            $this->Sessions->removeSession("konfigurator-products");
        }

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
        $db->addWherePart("AND","Poradi",">=",$poradi[0]["Poradi"]);
        $db->selectFromTable("ID_hodnota","ParametrHodnota");

        $data = $db->getRows();

        $arr = array();

        foreach ($data as $par){
            array_push($arr, $par["ID_hodnota"]);
        }

        return $arr;
    }

    public function search($cena = "ASC", $vyrobce = "vse", $typ = "standart"){

        $view = new View($this->Root->getAppPath(__DIR__,true), "filter");
        $view->title = "Nalezené traktory";

        if($this->Sessions->sessionExists("konfigurator-cena")){
            if($this->Sessions->getSession("konfigurator-cena") != $cena){
                $this->Sessions->changeSession("konfigurator-cena",$cena);
            }
            $view->acena = $this->Sessions->getSession("konfigurator-cena");
        } else {
            $view->acena = $cena;
            $this->Sessions->createSession("konfigurator-cena",$cena);
        }

        if($this->Sessions->sessionExists("konfigurator-vyrobce")){
            if($this->Sessions->getSession("konfigurator-vyrobce") != $vyrobce){
                $this->Sessions->changeSession("konfigurator-vyrobce",$vyrobce);
            }
            $view->avyrobce = $this->Sessions->getSession("konfigurator-vyrobce");
        } else {
            $view->avyrobce = $vyrobce;
            $this->Sessions->createSession("konfigurator-vyrobce",$vyrobce);
        }

        //$view->typ = $typ;

        $view->url = $this->Root->getPathToProject(false, true);
        $view->packages = $this->Root->getAppPath(__DIR__,false,true);
        $view->project = $this->Root->getPathToProject(true);

        if(!$this->Sessions->sessionExists("konfigurator-products")) {

            $results = array();
            $data = $_POST;

            // Pevne hodnoty

            for ($i = 0; $i < count($data["parametr"]["id"]); $i++) {

                $results[$i] = array();

                $this->db->addWherePart("ID_parametr", "=", $data["parametr"]["id"][$i], array(true, false));
                $this->db->addWherePart("AND", "ID_hodnota", "IN", join(",", $this->getIDToSearch($data["parametr"]["id"][$i], $data["parametr"]["hodnota"][$data["parametr"]["id"][$i]])), array(false, true));

                $this->db->selectFromTable("ID_produkt", "ProduktParam", "", "ID_produkt->ASC");
                $products = $this->db->getRows();

                foreach ($products as $product) {
                    array_push($results[$i], $product["ID_produkt"]);
                }

            }

            $i = count($results);

            //Parcela

            $results[$i] = array();

            $this->db->addWherePart("ID_parametr", "=", 42, array(true, false));
            if ($data["parcela"] < 61) {
                $this->db->addWherePart("AND", "Hodnota", "<=", $data["parcela"], array(false, true));
            } else {
                $this->db->addWherePart("AND", "Hodnota", ">=", $data["parcela"], array(false, true));
            }

            $this->db->selectFromTable("ID_produkt", "ProduktParam", "", "ID_produkt->ASC");
            $products = $this->db->getRows();

            foreach ($products as $product) {
                array_push($results[$i], $product["ID_produkt"]);
            }

            $i++;

            // Sklon

            $results[$i] = array();

            $this->db->addWherePart("ID_parametr", "=", 13, array(true, false));
            if ($data["sklon"] < 16) {
                $this->db->addWherePart("AND", "Hodnota", "<=", $data["sklon"], array(false, true));
            } else {
                $this->db->addWherePart("AND", "Hodnota", ">=", $data["sklon"], array(false, true));
            }

            $this->db->selectFromTable("ID_produkt", "ProduktParam", "", "ID_produkt->ASC");
            $products = $this->db->getRows();

            foreach ($products as $product) {
                array_push($results[$i], $product["ID_produkt"]);
            }

            // Vyfiltrovani

            for ($j = 0; $j < count($results[0]); $j++) {

                for ($i = 1; $i < count($results); $i++) {

                    if (!in_array($results[0][$j], $results[$i])) {
                        unset($results[0][$j]);
                        continue;
                    }

                }

            }

            $this->Sessions->createSession("konfigurator-products",json_encode($results[0]));
            $result = join(",",$results[0]);

        } else {
            $data = json_decode($this->Sessions->getSession("konfigurator-products"), true);
            $result = join(",",$data);
        }

        // select traktoru
        $this->db->addWherePart("ID_produkt","IN", $result);

        if($vyrobce != "vse"){
            $this->db->addWherePart("AND","Vyrobce","LIKE",$vyrobce);
        }

        if($_GET["products"] != ''){
            $range = explode("-",$_GET["products"]);
            $limit = $range[0].",".$range[1];
        } else {
            if($_GET["strankovani"] != ''){
                $limit = $_GET["strankovani"];
            } else {
                $limit = 12;
            }
        }

        $this->db->selectFromTable("*","Produkt","","Cena->".$cena."",$limit);
        $view->products = $this->db->getRows();

        // select vyrobcu
        $this->db->addWherePart("ID_produkt","IN",$result);
        $this->db->selectFromTable("DISTINCT(Vyrobce)","Produkt");
        $view->vyrobci = $this->db->getRows();

        // zjisteni poctu traktoru

        $this->db->addWherePart("ID_produkt","IN", $result);

        if($vyrobce != "vse"){
            $this->db->addWherePart("AND","Vyrobce","LIKE",$vyrobce);
        }

        $this->db->selectFromTable("COUNT(*) AS pocet","Produkt");
        $count = $this->db->getRows();

        $view->pager = $this->formE->dataPager($count[0]["pocet"], 12, array(12=>12,36=>36,76=>76,152=>152));

        return $view->display("main");

    }

    public function detail($id){

        $view = new View($this->Root->getAppPath(__DIR__,true), "detail");

        $view->url = $this->Root->getPathToProject(false, true);
        $view->packages = $this->Root->getAppPath(__DIR__,false,true);
        $view->project = $this->Root->getPathToProject(true);

        $this->db->addWherePart("ID_produkt","=",$id);
        $this->db->selectFromTable("*", "Produkt");
        $data = $this->db->getRows();
        $data = $data[0];

        $view->title = "Detail produktu";

        $this->db->addWherePart("ID_kategorie","=",$data["Kategorie"]);
        $this->db->addWherePart("AND","ID_produkt","=",$id);
        $this->db->selectFromTable("p.Nazev as Nazev, ID_hodnota, Hodnota","KategorieParam kp JOIN Parametr p ON kp.ID_parametr = p.ID_parametr LEFT JOIN ProduktParam pp ON p.ID_parametr = pp.ID_parametr","","Poradi->ASC");
        $params = $this->db->getRows();

        $i = 0;
        foreach ($params as $param) {

            if($param["ID_hodnota"] != NULL){

                $this->db->addWherePart("ID_hodnota", "=", $param["ID_hodnota"]);
                $this->db->selectFromTable("Hodnota_h", "HodnotyList");
                $paramVals = $this->db->getRows();

                $params[$i]["Hodnota"] = $paramVals[0]["Hodnota_h"];

            }
            $i++;
        }

        $data["params"] = $params;

        $view->data = $data;

        return $view->display("main");
    }

}