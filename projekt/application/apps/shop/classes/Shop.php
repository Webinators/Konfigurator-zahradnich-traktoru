<?php

use System\Objects\Collection;

class Shop {

    use Collection\ObjSet;

    private $database;
    private $form;
    private $formE;
    private $table;
    private $BazosP;
    private $URL;

    function __construct() {
        $this->loadSystem();

        $this->database = new Database(DB_HOST, DB_NAME);
        $this->makeTables();

        $this->BazosP = $this->Root->getAppPath(__DIR__);

        $this->form = Form::getForm("FormTable");
        $this->formE = new FormElements();
        $this->table = new Table();

        $this->URL = $this->Root->getPathToProject(false, true) . $this->BazosP;
    }

    private function makeTables() {

	$adminShop = new ShopAdmin();

    }

    public function printProducts() {

        $output = '';

        $orderby = "ID_produkt->DESC";

        $output .= '<link rel="stylesheet" type="text/css" href="' . $this->URL . 'css/products.css">';

        //$this->form->TableWidth("auto");
        $this->form->Method("GET");

        $output .= '<div id="BazarControlPanel"><div id="BazarControlPanelTitle">Vyhledávací panel</div>';

        $this->formE->setDataFromArray(array("ASC", "DESC"), array("↑", "↓"));
        $this->form->addItem("Cena", $this->formE->RadioField("Cena", $_GET["Cena"]), false);

        if ($_GET["Cena"] != '') {
            $orderby = "Cena->" . $_GET["Cena"];
        }

        $this->formE->setDataFromDb($categories, "Nazev_k", "Nazev_k");
        $this->form->addItem("Kategorie", $this->formE->Select("Nazev_k", $_GET["Nazev_k"], array(true, "Vše"), false), false);

        if ($_GET["Nazev_k"] != '') {
            $this->database->addWherePart("Nazev_k", "=", $_GET["Nazev_k"]);
        }

        $this->form->addButton($this->formE->Button()->Submit("search", "najit"));

        $output .= $this->form . '</div><br />';

        $icon = new Icons();

        $this->database->selectFromTable("*", "Produkt NATURAL JOIN Kategorie", "", $orderby);
        $products = $this->database->getRows();

        $output .= '<div id="productsContainer" style="position: relative;text-align: center;padding-left: 7px;  display: flex; flex-flow: row wrap; ">';

        if ($this->User->userIsAdmin()) {
            $output .= '<div class="productContainer"><a href="' . $this->URL . 'load/addProductForm.php" class="ajaxWin" title="Přidat produkt"><div class="productCategory">Přidat nový</div><div class="productImageContainer">' . $icon->getIcon("add", "50px", "Přidat produkt") . '</div><div class="productSpec" style="background-color: transparent;"><div style="background: none;" class="productName"></div><br /></div></a></div>';
        }

        foreach ($products as $product) {
            $output .= $this->buildProduct($product);
        }

        $output .= '</div> ' . $this->formE->dataPager(5000) . ' <script type="text/javascript" src="' . $this->URL . 'js/bazos.js"></script>';

        return $output;
    }

    private function buildProduct($product) {

        $commander = new FileCommander();
        $commander->setPath($this->BazosP . "images");

        $imagesE = new ImgEdit();

        if ($this->User->userIsAdmin()) {
            $delete = '<a id="bazosRemoveProduct" data-id="' . $product["ID_produkt"] . '" href="' . $this->URL . 'load/removeProduct.php">' . $this->Icons->getIcon("remove", "25px", "", 'class="remove"') . '</a>';
        } else {
            $delete = "";
        }

        $commander->moveToDir($product["ID_produkt"]);
        $file = $commander->getFiles();

        $imagesE->setInputDir($commander->getActualPath());

        $img = '<a class="bazosShowProduct" href="' . $this->URL . 'load/showProduct.php" data-id="' . $product["ID_produkt"] . '" title="Zobrazit produkt" >' . $imagesE->getIMGInHTML($file[0]) . '</a>';
        $img = $imagesE->getImgWithOptions($file[0], $img, "600,700");

        return '<div class="productContainer">' . $delete . '<a class="bazosShowProduct" data-id="' . $product["ID_produkt"] . '" title="Zobrazit produkt" ><div class="productCategory">' . $product["Nazev_k"] . '</div></a><div class="productImageContainer">' . $img . '<div class="productName fade">' . $product["Nazev_p"] . '</div></div><a class="bazosShowProduct" data-id="' . $product["ID_produkt"] . '" title="Zobrazit produkt" ><div class="productSpec"><font color="#ff0000"><b>Cena: ' . number_format($product["Cena"], 2, ',', ' ') . ' Kč</b></font></div> </a>

        ' . $this->formE->Button()->Submit("basket", "vložit do košíku") . '

        </div>';
    }

    public function showProduct($id) {

        $this->database->addWherePart("ID_produkt", "=", $id);
        $this->database->selectFromTable("*", "Produkt p JOIN Kategorie k ON o.Kategorie = k.ID_kategorie");
        $product = $this->database->getRows();

        $this->database->addWherePart("ID_produkt", "=", $id);
        $this->database->selectFromTable("*", "ParametrProdukt NATURAL JOIN Parametr");
        $params = $this->database->getRows();

        usort($params, function ($a, $b) {
            return $a['Poradi'] - $b['Poradi'];
        });

        $commander = new FileCommander();
        $commander->setPath($this->BazosP . "images");
        $commander->moveToDir($product[0]["ID_produkt"]);
        $file = $commander->getFiles();

        $output = '';

        $imagesE = new ImagesEditor();
        $imagesE->setInputDir($commander->getActualPath());
        $img = $imagesE->getIMGInHTML($file[0]);

        $output .= '<h1>' . $product[0]["Nazev_p"] . '</h1>';

        $output .= '<table>
            <tr valign="top">
            <td rowspan="2">
                ' . $img . '
            </td>
            <td>';

        $output .= '
            <b>Kategorie</b><br />' . $product[0]["Nazev_k"] . '<br />
            <b>Název produktu</b><br />' . $product[0]["Nazev_p"] . '<br/>
            <b>Cena</b><br />' . $product[0]["Cena"] . ' Kč<br/>
            <b>Popisek</b><br />' . $product[0]["Popis_p"] . '<br />
        ';

        $output .= '</td></tr></table><br /><h2>Parametry produktu</h2><div id="bazosProductParams">';

        $output .= '<table width="100%">';

        foreach ($params as $param) {
            $output .= $this->buildParam($param, $id);
        }

        $output .= '</table>';

        return $output;
    }

    private function buildParam($param, $prodId) {

        $output = '<tr><td width="40%"><b>' . $param["Nazev_pa"] . '</b></td><td width="50%">' . $param["Hodnota"] . ' ' . $param["Jednotka"] . '</td></tr>';
        return $output;
    }

}

?>