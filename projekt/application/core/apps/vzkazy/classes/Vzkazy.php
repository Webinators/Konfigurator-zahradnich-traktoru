<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 18.3.2016
 * Time: 12:01
 */

use System\Objects\Collection;

class Vzkazy
{
    use Collection\ObjSet;

    private $database;

    private $URLReg;
    private $URLPho;
    private $path;

    function __construct()
    {
        $this->loadSystem();

        $this->database = new Database(DB_HOST, DB_NAME);
        $this->createTables();

        $this->path = $this->Root->getAppPath(__DIR__);
        $this->URLReg = $this->Root->getPathToRegistratura(false, true);
        $this->URLPho = $this->Root->getAppPath(__DIR__, false, true);

        $this->Permissions->addNewCategory("Správa vzkazy");
        $this->Permissions->addNewPermission("vzkazy", "Správa vzkazy");
    }

    private function createTables()
    {
        $this->database->createTable("Vzkazy", "
            ID_vzkaz INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            Typ VARCHAR(50) NOT NULL,
            Nadpis VARCHAR(70) NOT NULL,
            Text TEXT NOT NULL,
            Autor INT NOT NULL,
            Datum_pridani DATETIME
        ");

    }

    public function insertComponents()
    {
        return '
            <link rel="stylesheet" href="' . $this->URLPho . 'css/vzkazy.css" Type="text/css" media="all" />
        ';
    }

    public function renderVzkaz($data){

        $content = '<div id="vzkazContainer'.$data["ID_vzkaz"].'" class="vzkazContainer">
                <div class="title">
                <div class="flexElem">
                    <div class="flexElem flex alignElemsLeft"><span>Vložil: ' .$this->User->getUserFromId($data["Autor"]). '</span></div>
                    <div class="flexElem flex alignElemsRight" style="padding-right: 70px;"><span>Datum přidání: '.date("d. m. Y. H: i: s", strtotime($data["Datum_pridani"])).'</span></div>
                </div>
                <h2>'.$data["Nadpis"].'</h2>

               </div>
               <div class="content">
                '.$data["Text"].'
               </div>
                 </div>
        ';

        $options = ($this->Permissions->userHasAuthorization("vzkazy") ? '<a class="ajaxWin" data-win-title="Editace vzkazu" href="'.$this->URLPho . 'load/showForm.php?ID_vzkaz='.$data["ID_vzkaz"].'">' . $this->Icons->getIcon("edit", "25px", "Upravit vzkaz") . '</a>' : "");
        $options .= ($this->Permissions->userHasAuthorization("vzkazy") ? '<a class="ajaxDel" data-win-title="Opravdu chcete odebrat vzkaz?" data-win-message="Vzkaz úspěšně odebrán" data-destination="#vzkazContainer'.$data["ID_vzkaz"].'" href="' . $this->URLPho . 'load/removeVzkaz.php?ID_vzkaz='.$data["ID_vzkaz"].'">' . $this->Icons->getIcon("delete", "25px", "Smazat vzkaz") . '</a>' : "");

        $editor = new Editor();
        return "".$editor->build($content, $options);

    }

    public function printVzkazy()
    {

        $output = '<div>';

        $this->database->selectFromTable("*", "Vzkazy", "", "Datum_pridani->DESC", 15);
        $data = $this->database->getRows();

        $form = Form::getForm("FormBasic");
        $formE = new FormElements();

        if ($this->Permissions->userHasAuthorization("vzkazy")) {

            $options = '
              <a class="ajaxWin" href="'.$this->URLPho . 'load/showForm.php">Přidat vzkaz</a>
            ';

        }

        $output .= '<div id="vzkazyContainer">';

        foreach ($data as $vzkaz) {
            $output .= $this->renderVzkaz($vzkaz);
        }

        $output .= '</div>';

        $form = Form::getForm("FormTable","");
      
        $editor = new Editor();
        return $form."<br /><br /><br /><br />".$editor->build($output, $options, "relative");

    }

    public function showAddVzkazForm()
    {
        $form = Form::getForm("FormTable");

        $id = $_GET["ID_vzkaz"];

        if($id != ''){
            $form->Action($this->URLPho."load/saveVzkaz.php");
        } else {
            $form->Action($this->URLPho."load/addVzkaz.php");
        }

        $form->Method("POST");
        $formE = new FormElements();

        if($id != ''){
            $this->database->addWherePart("ID_vzkaz","=", $id);
            $this->database->selectFromTable("*", "Vzkazy", "", "Datum_pridani->DESC", 15);
            $data = $this->database->getRows();

            $form->addHiddenItem($formE->Input()->Hidden("ID_vzkaz")->Value($id));
            $form->addHiddenItem($formE->Input()->Hidden("Autor")->Value($data[0]["Autor"]));
        }

        $form->addItem("Typ",$formE->Input()->Text("Typ")->Required(true)->Value($data[0]["Typ"]));
        $form->addItem("Nadpis",$formE->Input()->Text("Nadpis")->Required(true)->Value($data[0]["Nadpis"]));
        $form->addItem("Text",$formE->TextAreaEditor("Text",$data[0]["Text"],true));

        if($id == ''){$form->addItem("Poslat emaily",$formE->Input()->CheckBox("mail")->Value(true));}

        if($id != ''){
            $form->addButton($formE->Button()->Submit("save")->_class("ajaxSave")->Rest('data-win-message="Vzkaz úspěšně upraven" data-win-closeParent="true" data-destination="#vzkazyContainer #vzkazContainer'.$id.'"'));
        } else {
            $form->addButton($formE->Button()->Submit("add")->_class("ajaxAdd")->Rest('data-win-message="Vzkaz úspěšně přidán" data-win-closeParent="true" data-destination="#vzkazyContainer,top"'));
        }

        return $form;
    }


    public function addNewVzkaz($data)
    {

        if ($data["Nadpis"] != '') {

            $data["Datum_pridani"] = date("Y-m-d h:i:s", time());
            $data["Autor"] = $this->User->getUserSession("user_id");

            $this->database->insertIntoTable("Nadpis, Text, Autor, Typ", "Vzkazy", array($data["Nadpis"], $data["Text"], $data["Autor"], $data["Typ"]));
            $data["ID_vzkaz"] = $this->database->getLasInsertedId();

            if($data["mail"]) {

                $this->database->addWherePart("Post", "=", "Admin");
                $this->database->selectFromTable("Email", "regUsers");
                $users = $this->database->getRows();

                $mail = new HtmlMimeMail("X-Mailer: Html Mime Mail Class");

                foreach ($users as $user) {

                    $email = $user["Email"];
                    $predmet = "Vzkaz: " . $data["Nadpis"] . "";
                    $from = "viceucelovehristekouty@8u.cz";

                    $body = $data["Text"];

                    $mail->add_html($body, "");
                    $mail->set_charset('utf-8', TRUE);
                    $mail->build_message();
                    if ($mail->send($email, $email, $from, $from, $predmet, "Return-Path: $from")) {
                        return $this->renderVzkaz($data);
                    } else {
                        throw new Exception("Vzkaz nebyl úspěšně odeslán");
                    }
                }

            } else {
                return $this->renderVzkaz($data);
            }

        } else {
            throw new Exception("Není vyplněný Nadpis vzkazu");
        }
    }

    public function saveVzkaz($data)
    {

        if ($data["Nadpis"] != '') {

            $this->database->addWherePart("ID_vzkaz", "=", $data["ID_vzkaz"]);
            $this->database->updateTable("Vzkazy", "Nadpis, Text, Typ", array($data["Nadpis"], $data["Text"],$data["Typ"]));

            return $this->renderVzkaz($data);

        } else {
            throw new Exception("Není vyplněný Nadpis vzkazu");
        }
    }

    public function removeVzkaz($data)
    {

        $this->database->addWherePart("ID_vzkaz", "=", $data["ID_vzkaz"]);
        $this->database->deleteFromTable("Vzkazy");

        return "ok";
    }

}