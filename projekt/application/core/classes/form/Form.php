<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 7.6.2016
 * Time: 20:50
 */

class Form
{

    public static function getForm($type = "FormTable", $action = ""){
        return new $type($action);
    }

}

class FormBasic
{
    protected $action;
    protected $method;
    protected $enctype;
    protected $class;
    protected $id;

    public function Action($action)
    {
        $this->action = $action;
        return $this;
    }

    public function Method($method)
    {
        if ($method == "POST" || $method == "GET") {
            $this->method = $method;
        } else {
            throw new Exception("Špatná metoda pro FORM");
        }
        return $this;
    }

    public function Enctype($enctype)
    {
        $this->enctype = $enctype;
        return $this;
    }

    public function _Class($class)
    {
        $this->class = $class;
        return $this;
    }

    public function Id($id)
    {
        $this->id = $id;
        return $this;
    }

    protected $table = null;

    protected $data = array();
    protected $btns = array();
    protected $hidden = array();

    function __construct($action = "")
    {
        $this->method = "POST";
        $this->action = $action;
    }

    public function addHiddenItem($elem)
    {
        array_push($this->hidden, $elem);
    }

    public function addItem($description, $elem, $break = true)
    {
        array_push($this->data, array($description, $elem, $break));
    }

    public function addButton($elem)
    {
        array_push($this->btns, $elem);
    }

    protected function clear(){
        $this->data = array();
        $this->btns = array();
        $this->hidden = array();
    }

    public function __toString()
    {

        $output = '<form method="' . $this->method . '" action="' . $this->action . '" ' . ($this->id != "" ? 'ID="' . $this->id . '"' : '') . ' ' . ($this->class != "" ? 'class="' . $this->class . '"' : '') . ' ' . ($this->enctype != "" ? 'enctype="' . $this->enctype . '"' : '') . '>';

        foreach ($this->hidden as $el) {
            $output .= $el;
        }

        for ($i = 0; $i < count($this->data); $i++) {

            $output .= '' . $this->data[$i][0] . '' . $this->data[$i][1] . '';
            $output .= '&nbsp;<div class="validatorOutput"></div>';

            if ($this->data[$i][3]) {
                $output .= '<br />';
            }
        }

        foreach ($this->btns as $btn) {
            $output .= $btn;
        }

        return $output."</form>";
    }

}

class FormTable extends FormBasic{

    protected $table;

    function __construct($action)
    {
        parent::__construct($action);
        $this->table = new Table();
    }

    public function TableWidth($width){
        $this->table->maxWidth = $width;
    }

    public function __toString()
    {

        $output = '<form method="' . $this->method . '" action="' . $this->action . '" ' . ($this->id != "" ? 'ID="' . $this->id . '"' : '') . ' ' . ($this->class != "" ? 'class="' . $this->class . '"' : '') . ' ' . ($this->enctype != "" ? 'enctype="' . $this->enctype . '"' : '') . '>';

        foreach ($this->hidden as $el) {
            $output .= $el;
        }

        $this->table->newRow();

        for ($i = 0; $i < count($this->data); $i++) {

            if($this->data[$i][0] != '') {
                $this->table->newCell($this->data[$i][0]);
            }

            $this->table->newCell(''.$this->data[$i][1].'&nbsp;<div class="validatorOutput"></div>');

            if($this->data[$i][2]){
                $this->table->newRow();
            }

        }

        $this->table->newRow();

        $btns = '<div style="text-align: center;">';

        foreach ($this->btns as $btn) {
            $btns .= ' '.$btn;
        }

        $btns .= '</div>';

        $this->table->newCell($btns);

        $output .= '<div>'.$this->table.'</div></form>';

        return $output;
    }

}

final class FormTableCols extends FormTable{

    private $cols = array();

    public function addCol($title){
        array_push($this->cols,$title);

    }

    public function addItem($elem){
        array_push($this->data[count($this->data) - 1], $elem);

    }

    public function addRow(){
        array_push($this->data, array());

    }

    public function __toString()
    {

        $this->addCol("Action");

        $output = '<form method="' . $this->method . '" action="' . $this->action . '" ' . ($this->id != "" ? 'ID="' . $this->id . '"' : '') . ' ' . ($this->class != "" ? 'class="' . $this->class . '"' : '') . ' ' . ($this->enctype != "" ? 'enctype="' . $this->enctype . '"' : '') . '>';

        foreach ($this->hidden as $el) {
            $output .= $el;
        }

        $this->table->newRow();

        foreach($this->cols as $col){
            $this->table->newCell($col);
        }

        $this->table->newRow();

        foreach($this->data as $row){

            for ($i = 0; $i < count($this->cols); $i++) {

                if(isset($row[$i])){
                    $this->table->newCell($row[$i]);
                } else {

                    if($i == (count($this->cols) - 1)){

                        $btns = '';

                        foreach ($this->btns as $btn) {
                            $btns .= ($btn);
                        }

                        $this->table->newCell($btns);

                    } else {
                        $this->table->newCell("&nbsp;");
                    }

                }

            }

            $this->table->newRow();

        }

        $output .= '<div>'.$this->table.'</div></form>';

        return $output;
    }

}


