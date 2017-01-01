<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 31.7.2016
 * Time: 20:27
 */

use System\Objects\Collection;

class Poptavka
{

    use System\Objects\Collection\ObjSet;

    private $URL;
    private $title;

    function __construct($title)
    {
        $this->loadSystem();
        $this->URL = $this->Root->getPathToProject(false, true).$this->Root->getAppPath(__DIR__);
        $this->title = $title;
    }

    public function send($data){

        if(filter_var($data["odesilatel"],FILTER_VALIDATE_EMAIL) && ($data["dotaz"] != '') && (strlen($data["jmeno"]) > 7)) {

            $body = '
                Dobrý den, byl odeslán poptávkový formulář na: ' . $data["title"] . ' z www.kambra.cz <br /><br />

                <table>
                <tr><td>Jméno/firma:</td><td>' . $data["jmeno"] . '</td></tr>
                <tr><td>Email:</td><td>' . $data["odesilatel"] . '</td></tr>
                <tr><td>Dotaz:</td><td>' . $data["dotaz"] . '</td></tr>
                </table>

            ';

            $uploader = new FileUploader();

            $predmet = 'Poptávka z Kambry';

            $mail = new HtmlMimeMail("X-Mailer: Html Mime Mail Class");
            $mail->add_html($body, "");

            if($uploader->isPostFile()){

                for($i = 0; $i <  count($_FILES["soubor"]); $i++){

                    $mail->add_attachment($mail->get_file($_FILES["soubor"][$i]["tmp_name"]), $_FILES["soubor"][$i]["name"]);
                }

            }

            $mail->set_charset('utf-8', TRUE);
            $mail->build_message();

            if ($mail->send($data["prijemce1"], $data["prijemce1"], $data["odesilatel"], $data["odesilatel"], $predmet, "Return-Path: " . $data["odesilatel"] . "") && $mail->send($data["prijemce2"], $data["prijemce2"], $data["odesilatel"], $data["odesilatel"], $predmet, "Return-Path: " . $data["odesilatel"] . "")) {
                return "0->ok";
            } else {
                return "1->Email nebyl úspěšně odeslán, zkuste to znovu";
            }

        }

    }

    public function __toString(){

        $form = Form::getForm("FormTable",$this->URL."load/send.php");
        $formE = new FormElements();

        $form->Enctype("multipart/form-data");

        $form->addHiddenItem($formE->Input()->Hidden("prijemce1")->Value("info@kambra.cz"));
        $form->addHiddenItem($formE->Input()->Hidden("prijemce2")->Value("vydra567@seznam.cz"));
        $form->addHiddenItem($formE->Input()->Hidden("title")->Value($this->title));

        $form->addItem("Jméno/firma", $formE->Input()->Text("jmeno")->Required(true),true);
        $form->addItem("Email", $formE->Input()->Email("odesilatel")->Required(true),true);
        $form->addItem("Dotaz", $formE->TextArea("dotaz","",true));
        $form->addItem("Příloha", $formE->Input()->FileOld("soubor"),true);
        $form->addButton($formE->Button()->Submit("upload","Odeslat")->ID("poptavkaSendBtn"));

        return '<script src="'.$this->URL.'js/poptavka.js"></script>'.$form.'';
    }

}