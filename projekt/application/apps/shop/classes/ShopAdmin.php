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
            Nazev_k VARCHAR(100) NOT NULL,
            Visible tinyint(4) NOT NULL DEFAULT(1)
        ");

        $this->db->createTable("Produkt", "
            ID_produkt INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Nazev_p VARCHAR(100) NOT NULL,
            Popis TEXT,
            Cena DOUBLE NOT NULL,
            K_cene VARCHAR(150),
            Vyrobce VARCHAR(100) NOT NULL,
            Kategorie INT NOT NULL REfERENCES Kategorie(ID_kategorie)
        ");

        $this->db->createTable("Parametr", "
            ID_parametr INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Nazev VARCHAR(100) NOT NULL,
            Popisek VARCHAR(100),
            Jednotka VARCHAR(30),
            Pevne_h tinyint NOT NULL DEFAULT(0),
            Typ VARCHAR(15)
        ");

        $this->db->createTable("KategorieParam", "
            ID_kategorie INT NOT NULL REFERENCES Kategorie(ID_kategorie),
            ID_parametr INT NOT NULL REFERENCES Parametr(ID_parametr),
            Order INT NOT NULL DEFAULT(0)
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

    public function CategoryList($order = "ID_kategorie->ASC")
    {

        $view = new View($this->Root->getAppPath(__DIR__,true), "kategorie/kategorie");
        $view->title = "Správa kategorií";

        $this->db->addWherePart("Visible","=","1");
        $this->db->selectFromTable("*", "Kategorie","",$order);

        $view->categories = $this->db->getRows();
        $view->icons = $this->Icons;

        return $view->display("main");

    }

    public function categoryForm($data = array())
    {

        $view = new View($this->Root->getAppPath(__DIR__,true), "kategorie/form");
        $view->title = "Editace kategorie";

        $view->form = Form::getForm("FormTable", $this->urlPath . "shop/ShopAdmin/saveCategory");
        $view->formE = new FormElements();

        $this->db->addWherePart("ID_kategorie", "=", $data["ID_kategorie"]);
        $this->db->selectFromTable("*", "Parametr p RIGHT JOIN KategorieParam kp ON p.ID_parametr = kp.ID_parametr","","kp.Order->ASC");
        $view->params = $this->db->getRows();

        if(!empty($data)) {
            $this->db->addWherePart("ID_kategorie", "=", $data["ID_kategorie"]);
            $this->db->selectFromTable("ID_parametr", "KategorieParam");
            $view->chosenP = $this->db->getRows();
        } else {
            $view->chosenP = array();
        }

        return $view->display("main");

    }

    public function editCategory($id = null)
    {

        if($id != null) {

            $this->db->addWherePart("ID_kategorie", "=", $id);
            $this->db->selectFromTable("*", "Kategorie");

            $data = $this->db->getRows();

            return $this->categoryForm($data[0]);

        } else {
            throw new Exception("Není uvedené ID produktu");
        }
    }

    public function saveCategory()
    {

        $data = $_POST;

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

        Redirector::redirect('' . $this->Root->getPathToProject(false, true) . 'shop/ShopAdmin/CategoryList');

    }

    public function removeCategory($id = null)
    {

        if ($id != null) {
            $this->db->addWherePart("ID_kategorie", "=", $id);
            $this->db->deleteFromTable("KategorieParam");

            $this->db->addWherePart("ID_kategorie", "=", $id);
            $this->db->updateTable("Kategorie", "Visible", array(0));

            return "0->Kategorie úspěšně smazána";
        } else {
            throw new Exception("Není uvedené ID produktu");
        }

    }

    public function parametrList($order = "ID_parametr->ASC")
    {

        $view = new View($this->Root->getAppPath(__DIR__,true), "parametr/parametr");
        $view->title = "Správa paramterů";

        $this->db->selectFromTable("*", "Parametr","",$order);
        $view->params = $this->db->getRows();
        $view->icons = $this->Icons;
        $view->renderer = $this;

        $view->url = $this->Root->getPathToProject(false,true);

        return $view->display("main");

    }

    public function renderParam($data)
    {
        $url = $this->Root->getPathToProject(false,true);
        return '<tr><td>' . $data["Nazev"] . ' (' . $data["Jednotka"] . ') <span style="font-size: 0.8em;">' . $data["Popisek"] . '</span></td><td><a class="ajaxWin" data-win-title="Úprava parametru" href="' . $url . 'shop/ShopAdmin/editParametr/' . $data["ID_parametr"] . '">' . $this->Icons->getIcon("edit") . '</a></td><td><a class="ajaxDel" data-destination="tr" href="' . $url . 'shop/ShopAdmin/removeParametr/' . $data["ID_parametr"] . '">' . $this->Icons->getIcon("remove") . '</a></td></tr>';
    }

    public function parametrForm($data = array())
    {

        $view = new View($this->Root->getAppPath(__DIR__,true), "parametr/form");
        $view->title = "Správa parametrů";

        $view->form = Form::getForm("FormTable", $this->Root->getPathToProject(false, true) . "shop/ShopAdmin/load/parametr/saveParametr");
        $view->formE = new FormElements();
        $view->data = $data;

        $view->icons = $this->Icons;

        $view->packages = $this->Root->getAppPath(__DIR__, false, true);
        $view->url = $this->Root->getPathToProject(false, true);

        if(!empty($data)) {
            $this->db->addWherePart("ID_parametr", "=", $data["ID_parametr"]);
            $this->db->selectFromTable("*", "Parametr NATURAL JOIN ParametrHodnota NATURAL JOIN HodnotyList");
            $view->params = $this->db->getRows();
        } else {
            $view->params = array();
        }

        return $view->display("main");

    }

    public function editParametr($id = null)
    {

        if ($id != null) {

            $this->db->addWherePart("ID_parametr", "=", $id);
            $this->db->selectFromTable("*", "Parametr");

            $data = $this->db->getRows();

            return $this->parametrForm($data[0]);

        } else {
            throw new Exception("Není uvedené ID produktu");
        }
    }

    public function saveParametr()
    {

        $data = $_POST;

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

    public function removeParametr($id = null)
    {

        if ($id != null) {
            $this->db->addWherePart("ID_parametr", "=", $id);
            $this->db->deleteFromTable("KategorieParam");

            $this->db->addWherePart("ID_parametr", "=", $id);
            $this->db->deleteFromTable("ProduktParam");

            $this->db->addWherePart("ID_parametr", "=", $id);
            $this->db->deleteFromTable("Parametr");
        } else {
            throw new Exception("Není uvedené ID produktu");
        }
    }


    public function productList($order = "ID_produkt->ASC")
    {

        $view = new View($this->Root->getAppPath(__DIR__,true), "produkt/produkt");
        $view->title = "Správa produktů";
        $view->icons = $this->Icons;

        $this->db->selectFromTable("*", "Produkt", "", $order);
        $view->products = $this->db->getRows();

        return $view->display("main");
    }

    public function productForm($data = array())
    {

        $form = Form::getForm("FormTable", $this->urlPath . "load/produkt/save.php");
        //$form->Enctype("multipart/form-data");

        $form->addHiddenItem($this->formE->Input()->Hidden("ID_produkt")->Value($data["ID_produkt"]));
        $form->addHiddenItem($this->formE->Input()->Hidden("Old_kategorie")->Value($data["Kategorie"]));

        $form->addItem("<b>Název produktu</b>", $this->formE->Input()->Text("Nazev_p")->Value($data["Nazev_p"]));
        $form->addItem("<b>Výrobce</b>", $this->formE->Input()->Text("Vyrobce")->Value($data["Vyrobce"]));

        $this->db->addWherePart("Visible","=","1");
        $this->db->selectFromTable("ID_kategorie, Nazev_k", "Kategorie");
        $this->formE->setDataFromDb($this->db->getRows(), "ID_kategorie", "Nazev_k");
        $form->addItem("<b>Kategorie</b>", $this->formE->Select("ID_kategorie", $data["Kategorie"], array(true, "vyberte kategorii"), true, 'class="productCategory" data-url="' . $this->urlPath . 'load/produkt/getParameters.php"'));

        $this->formE->clearData();

        $form->addItem("<b>Cena</b>", $this->formE->Input()->Text("Cena")->Value($data["Cena"]));
        $form->addItem("<b>K ceně</b>", $this->formE->Input()->Text("K_cene")->Value($data["K_cene"]));

        $form->addItem("<b>Popis</b>", $this->formE->TextAreaEditor("Popis",$data["Popis"]));

        $form->addItem("", "");
        $form->addItem("", '<label><b>Parametry produktu</b></label><br /><br /><div id="productCategoryDest" style="position: relative;">' . $this->renderProductParams($data) . '</div>');

        if (!empty($data)) {
            $form->addButton($this->formE->Button()->Submit("save", "Uložit produkt"));
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
            $form->addItem("", $minig);
            $form->addButton($this->formE->Button()->Submit("add", "Přidat produkt"));
            $minig = "";
        }


        return $form . $minig;
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
            $this->db->selectFromTable("*", "KategorieParam kp LEFT JOIN Parametr p ON kp.ID_parametr = p.ID_parametr", "", "p.Nazev->ASC");
            $params = $this->db->getRows();

            $output .= '<table class="FlexTable" style="width: 100%;">';

            if($data["ID_produkt"] != '') {
                for ($i = 0; $i < count($params); $i++) {

                    $this->db->addWherePart("ID_produkt", "=", $data["ID_produkt"]);
                    $this->db->addWherePart("AND", "ID_parametr", "=", $params[$i]["ID_parametr"]);
                    $this->db->selectFromTable("Hodnota", "ProduktParam pp NATURAL JOIN Parametr");

                    if ($this->db->countRows() > 0) {
                        $param = $this->db->getRows();
                        $params[$i]["Hodnota"] = $param[0]["Hodnota"];
                    } else {
                        $params[$i]["Hodnota"] = "";
                    }

                }
            }

            foreach($params as $param) {

                $output .= '<tr><td>' . $param["Nazev"] . ' ' . $param["Popisek"] . '</td><td>' . $this->formE->Input()->Hidden("paramsID[]")->Value($param["ID_parametr"]) . '';

                if ($param["Pevne_h"] == 1) {

                    $this->db->addWherePart("ID_parametr", "=", $param["ID_parametr"]);
                    $this->db->selectFromTable("*", "Parametr NATURAL JOIN ParametrHodnota NATURAL JOIN HodnotyList");
                    $paramVals = $this->db->getRows();

                    $this->formE->setDataFromDb($paramVals, "ID_hodnota", "Hodnota_h");

                    switch ($param["Typ"]) {

                        case "select":
                            $output .= $this->formE->Select("paramsVal[]", $param["Hodnota"], array(true, ""));
                            break;
                        case "radio":
                            $output .= $this->formE->RadioField("paramsVal[]", $param["Hodnota"]);
                            break;
                        case "checkbox":
                            $output .= $this->formE->CheckboxField("paramsVal[]", $param["Hodnota"]);
                            break;
                        default:
                            break;
                    }

                    $this->formE->clearData();

                } else {
                    $output .= $this->formE->Input()->Text("paramsVal[]")->Value($param["Hodnota"]);
                }

                $output .= '' . $param["Jednotka"] . '</td></tr>';


            }

            $output .= '</table>';


        } else {
            $output = "Načtou se až po vybrání kategorie";
        }

        return $output;

    }

    public function editProduct($id = null)
    {

        if($id != null) {

            $this->db->addWherePart("ID_produkt", "=", $id);
            $this->db->selectFromTable("*", "Produkt");

            $data = $this->db->getRows();

            return $this->productForm($data[0]);

        } else {
            throw new Exception("Není uvedené ID produktu");
        }

    }

    public function saveProduct($data)
    {

        $data = $_POST;

        if ($data["ID_produkt"] != '') {


            if($data["Old_kategorie"] != ''){

                if($data["ID_kategorie"] != $data["Old_kategorie"]){

                    $this->db->addWherePart("ID_produkt","=",$data["ID_produkt"]);
                    $this->db->deleteFromTable("ProduktParam");

                }
            }

            $this->db->addWherePart("ID_produkt", "=", $data["ID_produkt"]);
            $this->db->updateTable("Produkt", "Nazev_p, Popis, Cena, K_cene, Kategorie, Vyrobce", array($data["Nazev_p"], $data["Popis"], $data["Cena"], $data["K_cene"], $data["ID_kategorie"],$data["Vyrobce"]));

        } else {

            $this->db->insertIntoTable("Nazev_p, Popis, Cena, K_cene, Kategorie, Vyrobce", "Produkt", array($data["Nazev_p"], $data["Popis"], $data["Cena"],$data["K_cene"] , $data["ID_kategorie"], $data["Vyrobce"]));
            $data["ID_produkt"] = $this->db->getLasInsertedId();

        }

        for ($i = 0; $i < count($data["paramsID"]); $i++) {

            $this->db->addWherePart("ID_produkt","=",$data["ID_produkt"]);
            $this->db->addWherePart("AND", "ID_parametr", "=", $data["paramsID"][$i]);
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
        $mini->thumbResize("700,500,cropp")->allowedExtensions(FileUploader::IMAGES);
        $mini->uploadFiles();

        $msg = new Msg();
        $msg->setMsg("Proudkt byl úspěšně uložen", "success");

        Redirector::redirect(''.$this->Root->getPathToProject(false, true).'index.php?page=shop/sprava-produktu');

    }

    public function removeProduct($id = null)
    {

        if ($id != null) {

            $this->db->addWherePart("ID_produkt", "=", $id);
            $this->db->deleteFromTable("ProduktParam");

            $this->db->addWherePart("ID_produkt", "=", $id);
            $this->db->deleteFromTable("Produkt");

            return  "0->Produkt úspěšně smazán";

        } else {
            throw new Exception("Není uvedené ID produktu");
        }

    }

}