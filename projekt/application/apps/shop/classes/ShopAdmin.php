<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 28. 10. 2016
 * Time: 16:02
 */

use System\Objects\Collection;

class ShopAdmin
{

    private $db;
    use Collection\ObjSet;

    private $relPath;
    private $urlPath;

    private $form;
    private $formE;

    function __construct()
    {

        $this->loadSystem();

        $this->db = new Database(DB_HOST, DB_NAME);
        $this->makeTables();

        $this->relPath = $this->Root->getAppPath(__DIR__);
        $this->urlPath = $this->Root->getAppPath(__DIR__, false, true);

        $this->formE = new FormElements();

    }

    private function makeTables()
    {

        $this->db->createTable("Kategorie", "
            ID_kategorie INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Nazev_k VARCHAR(100) NOT NULL
        ");

        $this->db->createTable("Produkt", "
            ID_produkt INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Nazev_p VARCHAR(100) NOT NULL,
            Popis TEXT,
            Cena DOUBLE NOT NULL,
            K_cene VARCHAR(150),
            Kategorie INT NOT NULL REfERENCES Kategorie(ID_kategorie)
        ");

        $this->db->createTable("Parametr", "
            ID_parametr INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Nazev VARCHAR(100) NOT NULL,
            Popisek VARCHAR(100),
            Pevne_h TINYINT NOT NULL DEFAULT(0),
            Typ VARCHAR(10)
        ");

        $this->db->createTable("KategorieParam", "
            ID_kategorie INT NOT NULL REFERENCES Kategorie(ID_kategorie),
            ID_parametr INT NOT NULL REFERENCES Parametr(ID_parametr)
        ");

        $this->db->createTable("ProduktParam", "
            ID_produkt INT NOT NULL REFERENCES Produkt(ID_produkt),
            ID_parametr INT NOT NULL REFERENCES Parametr(ID_parametr),
            Hodnota TEXT
        ");

        $this->db->createTable("HodnotyList", "
            ID_hodnota INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Hodnota_h TEXT NOT NULL
        ");

        $this->db->createTable("ParametrHodnota", "
            ID_parametr INT NOT NULL REFERENCES Parametr(ID_parametr),
            ID_hodnota INT NOT NULL REFERENCES HodnotyList(ID_hodnota)
        ");

    }

    public function CategoryList()
    {

        $order = $_GET["order"];

        $this->db->addWherePart("Visible","=","1");
        $this->db->selectFromTable("*", "Kategorie", "", $order);
        $categories = $this->db->getRows();

        $data = '<table width="100%" border="1">';

        foreach ($categories as $category) {

            $data .= '<tr><td>' . $category["Nazev_k"] . '</td><td><a href="index.php?page=shop/kategorie/edit&ID_kategorie=' . $category["ID_kategorie"] . '" data-target="tr">' . $this->Icons->getIcon("edit") . '</a></td><td><a class="ajaxDel" data-destination="tr" href="' . $this->urlPath . 'load/kategorie/remove.php?id=' . $category["ID_kategorie"] . '">' . $this->Icons->getIcon("remove") . '</a></td></tr>';

        }

        $data .= '</table>';

        $options = '
            <a href="index.php?page=shop/kategorie/add">' . $this->Icons->getIcon("add", "25px", "Přidat kategorii") . '</a>
        ';


        $editor = new Editor();
        $output = $editor->build($data, $options, "relative");

        return $output;

    }

    public function categoryForm($data = array())
    {

        $this->form = Form::getForm("FormTable", $this->urlPath . "load/kategorie/save.php");

        $this->form->addHiddenItem($this->formE->Input()->Hidden("ID_kategorie")->Value($data["ID_kategorie"]));
        $this->form->addItem("Název kategorie", $this->formE->Input()->Text("Nazev_k")->Value($data["Nazev_k"]));

        $this->db->selectFromTable("*", "Parametr");
        $params = $this->db->getRows();

        if(!empty($data)) {
            $this->db->addWherePart("ID_kategorie", "=", $data["ID_kategorie"]);
            $this->db->selectFromTable("ID_parametr", "KategorieParam");
            $chosenP = $this->db->getRows();
        } else {
            $chosenP = array();
        }

        $data = '<br /><br /><label>Zvolte parametry pro tuto kategorii: </label><br /><br />';
        $this->formE->setDataFromDb($params, "ID_parametr", "Nazev");
        $data .= $this->formE->CheckboxField("parametr", $chosenP);

        $this->form->addItem("", $data);

        if (!empty($data)) {
            $this->form->addButton($this->formE->Button()->Submit("save", "Uložit kategorii"));
        } else {
            $this->form->addButton($this->formE->Button()->Submit("add", "Přidat kategorii"));
        }

        return $this->form;
    }

    public function editCategory($data)
    {

        $this->db->addWherePart("ID_kategorie", "=", $data["ID_kategorie"]);
        $this->db->selectFromTable("*", "Kategorie");

        $data = $this->db->getRows();

        return $this->categoryForm($data[0]);
    }

    public function saveCategory($data)
    {

        if ($data["ID_kategorie"] != '') {

            $this->db->addWherePart("ID_kategorie", "=", $data["ID_kategorie"]);
            $this->db->updateTable("Kategorie", "Nazev_k", array($data["Nazev_k"]));

        } else {

            $this->db->insertIntoTable("Nazev_k", "Kategorie", array($data["Nazev_k"]));
            $data["ID_kategorie"] = $this->db->getLasInsertedId();

        }

        $this->db->addWherePart("parametr","NOT IN",join(",",$data["parametr"]));
        $this->db->deleteFromTable("KategorieParam");


        foreach ($data["parametr"] as $param) {

            $this->db->addWherePart("ID_kategorie","=",$data["ID_kategorie"]);
            $this->db->addWherePart("AND","ID_parametr","=",$param);
            $this->db->selectFromTable("COUNT(*) AS pocet","KategorieParam");
            $count = $this->db->getRows();

            if($count[0]["pocet"] == 0){
                $this->db->insertIntoTable("ID_kategorie,ID_parametr", "KategorieParam", array($data["ID_kategorie"], $param));
            }
        }

        Redirector::redirect('' . $this->Root->getPathToProject(false, true) . 'index.php?page=shop/sprava-kategorii');

    }

    public function removeCategory($id)
    {

        $this->db->addWherePart("ID_kategorie", "=", $id);
        $this->db->deleteFromTable("KategorieParam");

        $this->db->addWherePart("ID_kategorie", "=", $id);
        $this->db->updateTable("Kategorie","Visible",array(0));

        echo "0->Kategorie úspěšně smazána";

    }

    public function parametrList()
    {

        $order = $_GET["order"];

        $this->db->selectFromTable("*", "Parametr", "", $order);
        $params = $this->db->getRows();

        $data = '<table id="parametersTable" width="100%" border="1">';

        foreach ($params as $param) {

            $data .= $this->renderParam($param);

        }

        $data .= '</table>';

        $options = '
            <a class="ajaxWin" href="' . $this->urlPath . 'pages/parametr/add.php">' . $this->Icons->getIcon("add", "25px", "Přidat parametr") . '</a>
        ';


        $editor = new Editor();
        $output = $editor->build($data, $options, "relative");
        $output .= $editor->build("", $options, "relative");

        return $output;

    }

    public function renderParam($data)
    {

        return '<tr><td>' . $data["Nazev"] . ' (' . $data["Jednotka"] . ') <span style="font-size: 0.8em;">' . $data["Popisek"] . '</span></td><td><a class="ajaxWin" data-win-title="Úprava parametru" href="' . $this->urlPath . 'pages/parametr/edit.php?ID_parametr=' . $data["ID_parametr"] . '">' . $this->Icons->getIcon("edit") . '</a></td><td><a class="ajaxDel" data-destination="tr" href="' . $this->urlPath . 'load/parametr/remove.php?id=' . $data["ID_parametr"] . '">' . $this->Icons->getIcon("remove") . '</a></td></tr>';

    }

    public function parametrForm($data = array())
    {

        $this->form = Form::getForm("FormTable", $this->urlPath . "load/parametr/save.php");

        $this->form->addHiddenItem($this->formE->Input()->Hidden("ID_parametr")->Value($data["ID_parametr"]));
        $this->form->addItem("Název parametru", $this->formE->Input()->Text("Nazev")->Value($data["Nazev"]));
        $this->form->addItem("Jednotka parametru", $this->formE->Input()->Text("Jednotka")->Value($data["Jednotka"]));
        $this->form->addItem("Popisek", $this->formE->Input()->Text("Popisek")->Value($data["Popisek"]));

        $values = array(array("select","radio","cehckbox"), array("selectbox (výběrové pole)", "radio (přepínač)", "checkbox (zatrhávací políčka)"));
        $this->formE->setDataFromArray($values[0],$values[1]);

        if(!empty($data)) {
            $this->db->addWherePart("ID_parametr", "=", $data["ID_parametr"]);
            $this->db->selectFromTable("*", "Parametr NATURAL JOIN ParametrHodnota NATURAL JOIN HodnotyList");
            $params = $this->db->getRows();
        } else {
            $params = array();
        }

        $paramsData = ''.$this->formE->Input()->CheckBox("Pevne_h")->Checked($data["Pevne_h"])->Value("1")->Rest('data-handler="ProductParamsEnable1"').'

            <div id="ProductParamsEnable1Target" style="margin-top: 10px;display: '.(($data["Pevne_h"] == 1 && $data["Pevne_h"] != '') ? "block" : "none" ).';">

                Vyberte způsob vybírání hodnot: '.$this->formE->Select("Pevne_h_type",$data["Typ"],array(true,"")).'

               <br /><br /> Definujte hodnoty: <table style="display: inline-block;">';

        $paramsData .= '<tr style="display: none;"><td>'.$this->formE->Input()->Hidden("Pevne_h_id[]").' '.$this->formE->Input()->Text("Pevne_h_val[]")->_Class("productParam").'</td><td> <a class="removeProductParam" style="cursor: pointer;">&nbsp;&nbsp;'.$this->Icons->getIcon("remove","20px").'</a>&nbsp;&nbsp;<a class="productParamAddAfter" style="cursor: pointer;">'.$this->Icons->getIcon("addrow","20px").'</a> </td></tr>';

        foreach($params as $param){
            $paramsData .= '<tr><td>'.$this->formE->Input()->Hidden("Pevne_h_id[]")->Value($param["ID_hodnota"]).' '.$this->formE->Input()->Text("Pevne_h_val[]")->Value($param["Hodnota_h"])->_Class("productParam").'</td><td> <a class="removeProductParam" style="cursor: pointer;">&nbsp;&nbsp;'.$this->Icons->getIcon("remove","20px").'</a>&nbsp;&nbsp;<a class="productParamAddAfter" style="cursor: pointer;">'.$this->Icons->getIcon("addrow","20px").'</a> </td></tr>';
        }

        $this->form->addItem("Definování pevných hodnot", $paramsData.'

                <tr><td>'.$this->formE->Input()->Hidden("Pevne_h_id[]").' '.$this->formE->Input()->Text("Pevne_h_val[]").' </td><td>&nbsp;&nbsp; '.$this->Icons->getIcon("add","20px","Přidat další parametr",'class="productParam"').'</td></tr>

                </table>

            </div>

        ');

        if (!empty($data)) {
            $this->form->addButton($this->formE->Button()->Submit("save", "Uložit parametr")->_class("ajaxSave")->Rest('data-destination="tr" data-win-closeParent="true"'));
        } else {
            $this->form->addButton($this->formE->Button()->Submit("add", "Přidat parametr")->_class("ajaxAdd")->Rest('data-destination="#parametersTable" data-win-closeParent="true"'));
        }

        return $this->form;
    }

    public function editParametr($data)
    {

        $this->db->addWherePart("ID_parametr", "=", $data["ID_parametr"]);
        $this->db->selectFromTable("*", "Parametr");

        $data = $this->db->getRows();

        return $this->parametrForm($data[0]);

    }

    public function saveParametr($data)
    {

        if ($data["ID_parametr"] != '') {

            $this->db->addWherePart("ID_parametr", "=", $data["ID_parametr"]);
            $this->db->updateTable("Parametr", "Nazev, Jednotka, Popisek", array($data["Nazev"], $data["Jednotka"], $data["Popisek"]));

        } else {

            $this->db->insertIntoTable("Nazev, Jednotka, Popisek", "Parametr", array($data["Nazev"], $data["Jednotka"], $data["Popisek"]));
            $data["ID_parametr"] = $this->db->getLasInsertedId();

        }


        $this->db->addWherePart("ID_parametr", "=", $data["ID_parametr"]);
        $this->db->updateTable("Parametr", "Pevne_h, Typ", array($data["Pevne_h"], $data["Pevne_h_type"]));


        if($data["Pevne_h"] == 1) {

            $ids = join(",", $data["Pevne_h_id"]);
            $this->db->addWherePart("ID_hodnota", "NOT IN", $ids);
            $this->db->addWherePart("AND", "ID_parametr", "=", $data["ID_parametr"]);
            $this->db->deleteFromTable("ParametrHodnota");

            for ($i = 0; $i < count($data["Pevne_h_id"]); $i++) {

                if ($data["Pevne_h_val"][$i] != '') {

                    $this->db->addWherePart("Hodnota_h", "LIKE", $data["Pevne_h_val"][$i]);
                    $this->db->selectFromTable("COUNT(ID_hodnota) AS pocet, ID_hodnota", "HodnotyList");
                    $count = $this->db->getRows();

                    if ($count[0]["pocet"] == 0) {
                        $this->db->insertIntoTable("Hodnota_h", "HodnotyList", array($data["Pevne_h_val"][$i]));
                        $data["Pevne_h_new_id"][$i] = $this->db->getLasInsertedId();
                    } else {
                        $data["Pevne_h_new_id"][$i] = $count[0]["ID_hodnota"];
                    }

                    if(($data["Pevne_h_id"][$i] != "")) {

                        if(($data["Pevne_h_id"][$i] != $data["Pevne_h_new_id"][$i]) && $data["Pevne_h_new_id"][$i] != ''){

                            $this->db->addWherePart("ID_hodnota", "=", $data["Pevne_h_id"][$i]);
                            $this->db->addWherePart("AND", "ID_parametr", "=", $data["ID_parametr"]);
                            $this->db->updateTable("ParametrHodnota", "ID_hodnota", array($data["Pevne_h_new_id"][$i]));

                        }

                    } else {

                        if ($data["Pevne_h_new_id"][$i] != '') {
                            $this->db->insertIntoTable("ID_hodnota,ID_parametr", "ParametrHodnota", array($data["Pevne_h_new_id"][$i], $data["ID_parametr"]));
                        }
                    }

                }

            }

        }

        echo '0->' . $this->renderParam($data);

    }

    public function removeParametr($id)
    {

        $this->db->addWherePart("ID_parametr","=",$id);
        $this->db->deleteFromTable("KategorieParam");

        $this->db->addWherePart("ID_parametr","=",$id);
        $this->db->deleteFromTable("ProduktParam");

        $this->db->addWherePart("ID_parametr", "=", $id);
        $this->db->deleteFromTable("Parametr");

    }


    public function productList()
    {

        $order = $_GET["order"];

        $this->db->selectFromTable("*", "Produkt", "", $order);
        $products = $this->db->getRows();

        $data = '<table width="100%" border="1">';

        foreach ($products as $product) {

            $data .= '<tr><td>' . $product["Nazev_p"] . '</td><td><a href="index.php?page=shop/produkt/edit&ID_produkt=' . $product["ID_produkt"] . '" data-target="tr">' . $this->Icons->getIcon("edit") . '</a></td><td><a class="ajaxDel" data-destination="tr" href="' . $this->urlPath . 'load/produkt/remove.php?id=' . $product["ID_produkt"] . '">' . $this->Icons->getIcon("remove") . '</a></td></tr>';

        }

        $data .= '</table>';

        $options = '
            <a href="index.php?page=shop/produkt/add">' . $this->Icons->getIcon("add", "25px", "Přidat produkt") . '</a>
        ';


        $editor = new Editor();
        $output = $editor->build($data, $options, "relative");

        return $output;

    }

    public function productForm($data = array())
    {

        $this->form = Form::getForm("FormTable", $this->urlPath . "load/produkt/save.php");
        //$this->form->Enctype("multipart/form-data");

        $this->form->addHiddenItem($this->formE->Input()->Hidden("ID_produkt")->Value($data["ID_produkt"]));
        $this->form->addHiddenItem($this->formE->Input()->Hidden("Old_kategorie")->Value($data["ID_produkt"]));

        $this->form->addItem("<b>Název produktu</b>", $this->formE->Input()->Text("Nazev_p")->Value($data["Nazev_p"]));
        $this->form->addItem("<b>Výrobce</b>", $this->formE->Input()->Text("Vyrobce")->Value($data["Vyrobce"]));

        $this->db->addWherePart("Visible","=","1");
        $this->db->selectFromTable("ID_kategorie, Nazev_k", "Kategorie");
        $this->formE->setDataFromDb($this->db->getRows(), "ID_kategorie", "Nazev_k");
        $this->form->addItem("<b>Kategorie</b>", $this->formE->Select("ID_kategorie", $data["Kategorie"], array(true, "vyberte kategorii"), true, 'class="productCategory" data-url="' . $this->urlPath . 'load/produkt/getParameters.php"'));

        $this->formE->clearData();

        $this->form->addItem("<b>Cena</b>", $this->formE->Input()->Text("Cena")->Value($data["Cena"]));
        $this->form->addItem("<b>K ceně</b>", $this->formE->Input()->Text("K_cene")->Value($data["K_cene"]));

        $this->form->addItem("<b>Popis</b>", $this->formE->TextAreaEditor("Popis",$data["Popis"]));

        $this->form->addItem("", "");
        $this->form->addItem("", '<label><b>Parametry produktu</b></label><br /><br /><div id="productCategoryDest" style="position: relative;">' . $this->renderProductParams($data) . '</div>');

        if (!empty($data)) {
            $this->form->addButton($this->formE->Button()->Submit("save", "Uložit produkt"));
        }

        $minig = null;

        if ($data["ID_produkt"] != '') {
            $minig = new MiniGallery($this->Root->getAppPath(__DIR__, true) . "images/" . $data["ID_produkt"] . "");
            $minig->thumbResize("700,500")->allowedExtensions("jpg,png,gif,jpeg");
        } else {
            $minig = new MiniGallery("");
            $minig->thumbResize("700,500")->allowedExtensions("jpg,png,gif,jpeg");
        }

        if (empty($data)) {
            $this->form->addItem("", $minig);
            $this->form->addButton($this->formE->Button()->Submit("add", "Přidat produkt")->_class("ajaxAdd"));
            $minig = "";
        }


        return $this->form . $minig;
    }

    public function getParameterValue($prodID, $paramID)
    {
        $this->db->addWherePart("ID_produkt", "=", $prodID);
        $this->db->addWherePart("AND", "ID_parametr", "=", $paramID);
        $this->db->selectFromTable("Hodnota", "ProduktParam");
        $val = $this->db->getRows();

        $this->db->addWherePart("ID_parametr", "=", $paramID);
        $this->db->addWherePart("AND", "ID_hodnota", "=", $val[0]["Hodnota"]);
        $this->db->selectFromTable("Hodnota_h", "Parametr NATURAL JOIN ParametrHodnota NATURAL JOIN HodnotyList");

        $data = $this->db->getRows();

        if($this->db->countRows() > 0) {
            return $data[0]["Hodnota_h"];
        } else {
            return "";
        }
    }

    public function renderProductParams($data)
    {

        $output = "";

        if ($data["Kategorie"] != '') {

            $this->db->addWherePart("ID_kategorie", "=", $data["Kategorie"]);
            $this->db->selectFromTable("*", "KategorieParam kp JOIN Parametr p ON kp.ID_parametr = p.ID_parametr", "", "p.ID_parametr->ASC");
            $params = $this->db->getRows();

            $output .= '<table class="FlexTable" style="width: 100%;">';

            foreach($params as $param) {

                if ($data["ID_produkt"] != '') {
                    $this->db->addWherePart("ID_produkt", "=", $data["ID_produkt"]);
                    $this->db->addWherePart("AND", "ID_parametr", "=", $param["ID_parametr"]);
                    $this->db->selectFromTable("*", "ProduktParam NATURAL JOIN Parametr");
                    $param = $this->db->getRows();
                } else {
                    $pom = $param;
                    $param = array();
                    $param[0] = $pom;
                }

                $output .= '<tr><td>' . $param[0]["Nazev"] . ' ' . $param[0]["Popisek"] . '</td><td>' . $this->formE->Input()->Hidden("paramsID[]")->Value($param[0]["ID_parametr"]) . '';

                if ($param[0]["Pevne_h"] == 1) {

                    $this->db->addWherePart("ID_parametr", "=", $param[0]["ID_parametr"]);
                    $this->db->selectFromTable("*", "Parametr NATURAL JOIN ParametrHodnota NATURAL JOIN HodnotyList");
                    $paramVals = $this->db->getRows();

                    $this->formE->setDataFromDb($paramVals, "ID_hodnota", "Hodnota_h");

                    switch ($param[0]["Typ"]) {

                        case "select":
                            $output .= $this->formE->Select("paramsVal[]", $param[0]["Hodnota"], array(true, ""));
                            break;
                        case "radio":
                            $output .= $this->formE->RadioField("paramsVal[]", $param[0]["Hodnota"]);
                            break;
                        case "checkbox":
                            $output .= $this->formE->CheckboxField("paramsVal[]", $param[0]["Hodnota"]);
                            break;
                        default:
                            break;
                    }

                    $this->formE->clearData();

                } else {
                    $output .= $this->formE->Input()->Text("paramsVal[]")->Value($param[0]["Hodnota"]);
                }

                $output .= '' . $param[0]["Jednotka"] . '</td></tr>';


            }

            $output .= '</table>';


        } else {
            $output = "Načtou se až po vybrání kategorie";
        }

        return $output;

    }

    public function editProduct($data)
    {

        $this->db->addWherePart("ID_produkt", "=", $data["ID_produkt"]);
        $this->db->selectFromTable("*", "Produkt");

        $data = $this->db->getRows();

        return $this->productForm($data[0]);

    }

    /**
     * @param $data
     * @throws Exception
     */
    public function saveProduct($data)
    {

        if ($data["ID_produkt"] != '') {


            if($data["Old_kategorie"] != ''){

                if($data["ID_kategorie"] != $data["Old_kategorie"]){

                    $this->db->addWherePart("ID_produkt","=",$data["ID_produkt"]);
                    $this->db->deleteFromTable("ProduktParam");

                }
            }

            $this->db->addWherePart("ID_produkt", "=", $data["ID_produkt"]);
            $this->db->updateTable("Produkt", "Nazev_p, Popis, Cena, K_cene, Kategorie, Vyrobce", array($data["Nazev_p"], $data["Popis"], $data["Cena"], $data["K_cene"], $data["ID_kategorie"],$data["Vyrobce"]));

            for ($i = 0; $i < count($data["paramsID"]); $i++) {

                $this->db->addWherePart("ID_produkt", "=", $data["ID_produkt"]);
                $this->db->addWherePart("AND", "ID_parametr", "=", $data["paramsID"][$i]);

                $this->db->updateTable("ProduktParam", "Hodnota", array($data["paramsVal"][$i]));

            }

        } else {

            $this->db->insertIntoTable("Nazev_p, Popis, Cena, K_cene, Kategorie, Vyrobce", "Produkt", array($data["Nazev_p"], $data["Popis"], $data["Cena"],$data["K_cene"] , $data["ID_kategorie"], $data["Vyrobce"]));
            $data["ID_produkt"] = $this->db->getLasInsertedId();

            for ($i = 0; $i < count($data["paramsID"]); $i++) {
                $this->db->insertIntoTable("ID_produkt, ID_parametr, Hodnota", "ProduktParam", array($data["ID_produkt"], $data["paramsID"][$i], $data["paramsVal"][$i]));
            }

        }


        for ($i = 0; $i < count($data["paramsID"]); $i++) {

            $this->db->addWherePart("ID_parametr", "=", $data["paramsID"][$i]);
            $this->db->selectFromTable("COUNT(ID_parametr) AS pocet", "ProduktParam");
            $count = $this->db->getRows();

            if ($count[0]["pocet"] > 0) {

                $this->db->addWherePart("ID_parametr", "=", $data["paramsID"][$i]);
                $this->db->addWherePart("AND", "ID_produkt", "=", $data["ID_produkt"]);

                $this->db->updateTable("ProduktParam", "Hodnota", array($data["paramsVal"][$i]));

            } else {
                $this->db->insertIntoTable("ID_produkt, ID_parametr, Hodnota", "ProduktParam", array($data["ID_produkt"], $data["paramsID"][$i], $data["paramsVal"][$i]));
            }

        }

        $commander = new FileCommander();
        $commander->setPath($this->Root->getAppPath(__DIR__, true) . "images");
        $commander->addDir($data["ID_produkt"]);
        $commander->moveToDir($data["ID_produkt"]);

        $mini = new MiniGallery($commander->getActualPath());
        $mini->thumbResize("700,500,cropp")->allowedExtensions("jpg,png,gif,jpeg");
        $mini->uploadFiles();

        $msg = new Msg();
        $msg->setMsg("Proudkt byl úspěšně uložen", "success");

        Redirector::redirect(''.$this->Root->getPathToProject(false, true).'index.php?page=shop/sprava-produktu');

    }

    public function removeProduct($id)
    {

        $this->db->addWherePart("ID_produkt", "=", $id);
        $this->db->deleteFromTable("ProduktParam");

        $this->db->addWherePart("ID_produkt", "=", $id);
        $this->db->deleteFromTable("Produkt");

        echo "0->Produkt úspěšně smazán";

    }

}