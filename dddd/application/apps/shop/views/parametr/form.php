<?php

echo '<script type="text/javascript" src="' . $packages . 'js/parameters.js"></script>';

        $form->addHiddenItem($this->formE->Input()->Hidden("ID_parametr")->Value($data["ID_parametr"]));
        $form->addItem("Název parametru", $this->formE->Input()->Text("Nazev")->Value($data["Nazev"]));
        $form->addItem("Jednotka parametru", $this->formE->Input()->Text("Jednotka")->Value($data["Jednotka"]));
        $form->addItem("Popisek", $this->formE->Input()->Text("Popisek")->Value($data["Popisek"]));

        $values = array(array("select" => "selectbox (výběrové pole)","radio" => "radio (přepínač)","cehckbox" => "checkbox (zatrhávací políčka)"));
        $this->formE->setDataFromArray($values[0],$values[1]);

        $paramsData = ''.$this->formE->Input()->CheckBox("Pevne_h")->Checked($data["Pevne_h"])->Value("1")->Rest('data-handler="ProductParamsEnable1"').'

            <div id="ProductParamsEnable1Target" style="margin-top: 10px;display: '.(($data["Pevne_h"] == 1 && $data["Pevne_h"] != '') ? "block" : "none" ).';">

                Vyberte způsob vybírání hodnot: '.$this->formE->Select("Pevne_h_type",$data["Typ"],array(true,"")).'

               <br /><br /> Definujte hodnoty: <table style="display: inline-block;">';

        $paramsData .= '<tr style="display: none;"><td>'.$this->formE->Input()->Hidden("Pevne_h_id[]").' '.$this->formE->Input()->Text("Pevne_h_val[]")->_Class("productParam").'</td><td> <a class="removeProductParam" style="cursor: pointer;">&nbsp;&nbsp;'.$icons->getIcon("remove","20px").'</a>&nbsp;&nbsp;<a class="productParamAddAfter" style="cursor: pointer;">'.$icons->getIcon("addrow","20px").'</a> </td></tr>';

        foreach($params as $param){
            $paramsData .= '<tr><td>'.$this->formE->Input()->Hidden("Pevne_h_id[]")->Value($param["ID_hodnota"]).' '.$this->formE->Input()->Text("Pevne_h_val[]")->Value($param["Hodnota_h"])->_Class("productParam").'</td><td> <a class="removeProductParam" style="cursor: pointer;">&nbsp;&nbsp;'.$icons->getIcon("remove","20px").'</a>&nbsp;&nbsp;<a class="productParamAddAfter" style="cursor: pointer;">'.$icons->getIcon("addrow","20px").'</a> </td></tr>';
        }

        $form->addItem("Definování pevných hodnot", $paramsData.'

                <tr><td>'.$this->formE->Input()->Hidden("Pevne_h_id[]").' '.$this->formE->Input()->Text("Pevne_h_val[]").' </td><td>&nbsp;&nbsp; '.$icons->getIcon("add","20px","Přidat další parametr",'class="productParam"').'</td></tr>

                </table>

            </div>

        ');

        if (!empty($data)) {
            $form->addButton($this->formE->Button()->Submit("save", "Uložit parametr")->_class("ajaxSave")->Rest('data-destination="tr" data-win-closeParent="true"'));
        } else {
            $form->addButton($this->formE->Button()->Submit("add", "Přidat parametr")->_class("ajaxAdd")->Rest('data-destination="#parametersTable" data-win-closeParent="true"'));
        }

	echo $form;

?>