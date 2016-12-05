<?php

echo '<script type="text/javascript" src="' . $packages . 'js/parameters.js"></script>';

        $form->addHiddenItem($formE->Input()->Hidden("ID_produkt")->Value($data["ID_produkt"]));
        $form->addHiddenItem($formE->Input()->Hidden("Old_kategorie")->Value($data["Kategorie"]));

        $form->addItem("<b>Název produktu</b>", $formE->Input()->Text("Nazev_p")->Value($data["Nazev_p"]));
        $form->addItem("<b>Výrobce</b>", $formE->Input()->Text("Vyrobce")->Value($data["Vyrobce"]));

        $formE->setDataFromDb($kategorie, "ID_kategorie", "Nazev_k");
        $form->addItem("<b>Kategorie</b>", $formE->Select("ID_kategorie", $data["Kategorie"], array(true, "vyberte kategorii"), true, 'class="productCategory" data-url="' . $url . 'shop/ShopAdmin/renderProductParams"'));

        $formE->clearData();

        $form->addItem("<b>Cena</b>", $formE->Input()->Text("Cena")->Value($data["Cena"]));
        $form->addItem("<b>K ceně</b>", $formE->Input()->Text("K_cene")->Value($data["K_cene"]));

        $form->addItem("<b>Popis</b>", $formE->TextAreaEditor("Popis",$data["Popis"]));

        $form->addItem("", "");
        $form->addItem("", '<label><b>Parametry produktu</b></label><br /><br /><div id="productCategoryDest" style="position: relative;">' . $params . '</div>');

        if (!empty($data)) {
            $form->addButton($formE->Button()->Submit("save", "Uložit produkt"));
        }

        if (empty($data)) {
            $form->addItem("", $minig);
            $form->addButton($formE->Button()->Submit("add", "Přidat produkt"));
            $minig = "";
        }

	echo $form . $minig;

?>