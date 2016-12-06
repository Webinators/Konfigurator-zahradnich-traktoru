<?php

        $form->addHiddenItem($this->formE->Input()->Hidden("ID_kategorie")->Value($data["ID_kategorie"]));
        $form->addItem("Název kategorie", $this->formE->Input()->Text("Nazev_k")->Value($data["Nazev_k"]));

        $data = '<br /><br /><label>Zvolte parametry pro tuto kategorii: </label><br /><br />';

        $this->formE->setDataFromDb($params, "ID_parametr", "Nazev");

        $data .= '<table><tr><td></td><td>Pořadí</td></tr>'.$this->formE->CheckboxField("parametr", $chosenP, function($input, &$data){

                return '<tr><td>'.$input.'</td><td><input type="text" name="order[]" value="'.$data["Poradi"].'"/></td></tr>';

            }).'</table>';

        $form->addItem("", $data);

        if (!empty($data)) {
            $form->addButton($this->formE->Button()->Submit("save", "Uložit kategorii"));
        } else {
            $form->addButton($this->formE->Button()->Submit("add", "Přidat kategorii"));
        }

	echo $form;

?>