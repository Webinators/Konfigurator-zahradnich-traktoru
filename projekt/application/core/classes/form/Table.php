<?php

class Table
{

    private $data = array();

    public $id;
    public $class;
    public $maxWidth;


    private $cols = 0;
    private $colsPom = 0;


    function __construct($maxWidth = "100%", $id = "", $class = "")
    {
        $this->id = $id;
        $this->class = $class;
        $this->maxWidth = $maxWidth;
    }

    public function newCell($value, $class = "", $id = "")
    {
        array_push($this->data[count($this->data) - 1], array($class,$id,$value));
        $this->colsPom++;
    }

    public function newRow()
    {
        if($this->colsPom > $this->cols){
            $this->cols = $this->colsPom;
        }

        $this->colsPom = 0;
        array_push($this->data, array());
    }

    public function __toString()
    {

        if($this->colsPom > $this->cols) {
            $this->cols = $this->colsPom;
        }

        $output = '<table class="FlexTable ' . ($this->class != "" ? '' . $this->class . '' : '') . '" ' . ($this->id != "" ? 'id="' . $this->id . '"' : '') .' style="width: '.$this->maxWidth.';position: relative; margin: 0px auto;">';

        foreach ($this->data as $rows) {

            $output .= '<tr>';

            for($i = 0; $i < count($rows); $i++){

                if((($i + 1) == count($rows)) && (($i + 1) < $this->cols)){
                    $colspan = 'colspan="'.$this->cols.'"';
                }

                $output .= '<td align="left" '.$colspan.' class="' . ($rows[$i][0] != "" ? '' . $rows[$i][0] . '' : '') . '" ' . ($rows[$i][1] != "" ? 'id="' . $rows[$i][1] . '"' : '') . '>' . $rows[$i][2] . '</td>';
            }

            $output .= '</tr>';

        }

        $output .= '</table>';

        $this->data = array();

        return $output;
    }

}

?>