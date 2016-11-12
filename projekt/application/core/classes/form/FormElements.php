<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 7. 2. 2016
 * Time: 9:39
 */

use System\Objects\Collection;

class Button{

    use Collection\ObjSet;

    protected $id;
    protected $class;
    protected $value;
    protected $icon;
    protected $rest;
    protected $style = [];
    protected $containerStyle = [];

    protected function _ID($id){
        $this->id = $id;
        return $this;
    }

    protected function __class($class){
        $this->class = $class;
        return $this;
    }

    protected function _Rest($rest){
        $this->rest = $rest;
        return $this;
    }

    protected function _Style($style){
        array_push($this->style, $style);
    }

    protected function _ContainerStyle($style){
        array_push($this->containerStyle, $style);
    }

    public function Submit($icon = "", $value = ""){
        return new ButtonSubmit($icon,$value);
    }

    public function Link($icon = "", $url = "", $value = ""){
        return new ButtonLink($icon,$url,$value);
    }

    function __construct(){
        $this->loadSystem();
    }

    public function __toString()
    {
        if($this->value == "") {

            switch ($this->icon) {
                case "add": $this->value = "Přidat"; break;
                case "delete": $this->value = "Smazat"; break;
                case "info": $this->value = "Info"; break;
                case "search": $this->value = "Vyhledat"; break;
                case "save": $this->value = "Uložit"; break;
                case "basket": $this->value = "Vložit do košíku"; break;
                default: $this->value = "Odeslat data"; break;
            }

        }

        return "";
    }

}

class ButtonSubmit extends Button{

    private $action;
    private $enctype;
    private $method;
    private $obj;

    function __construct($icon = "", $value = ""){
        parent::__construct();
        $this->icon = $icon;
        $this->value = $value;
        $this->obj = $this;
    }

    public function formAction($action){
        $this->action = $action; return $this->obj;
    }

    public function formMethod($method){
        if(strtoupper($method) == "POST" || strtoupper($method) == "GET") {
            $this->method = $method;
        } else {
            throw new Exception("Špatná metoda");
        }
        return $this->obj;
    }

    public function formEncType($enc){
        $this->enctype = $enc;
        return $this->obj;
    }

    public function ID($id){
        parent::_ID($id);
        return $this->obj;
    }

    public function _class($class){
        parent::__class($class);
        return $this->obj;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function Style($style){
        parent::_Style($style);
        return $this->obj;
    }

    public function ContainerStyle($style){
        parent::_ContainerStyle($style);
        return $this->obj;
    }

    protected function _ContainerStyle($style){
        array_push($this->containerStyle, $style);
    }


    function __toString()
    {

        parent::__toString();
        if($this->icon != ''){$this->Style("padding-left: 2.5em;");}

        return '<div class="flexElem alignElemsCenter" '.($this->containerStyle != '' ? 'style="'.join(";", $this->containerStyle).'"' : "").'><div class="FormButtonContainer">
            <input type="submit" class="Button FormButton '.($this->class != '' ? ''.$this->class.'':'').'" '.($this->id != '' ? 'id="'.$this->id.'"':'').' '.($this->style != '' ? 'style="'.join(";", $this->style).'"' : "").' '.($this->value != '' ? 'value="'.$this->value.'"':'').' '.($this->action != '' ? 'formaction="'.$this->action.'"':'').' '.($this->enctype != '' ? 'formenctype="'.$this->enctype.'':'').' '.($this->method != '' ? 'formmethod="'.$this->method.'"':'').' '.$this->rest.'/>
            '.(($this->icon != '') ? $this->Icons->getIcon($this->icon, "20px", ($this->value != '' ? '' . $this->value . '' : ''), 'class="FormButtonIcon"') : "").'
        </div></div>';
    }
}

class ButtonLink extends Button{

    private $href;
    private $obj;

    public function ID($id){
        parent::_ID($id);
        return $this->obj;
    }

    public function _class($class){
        parent::__class($class);
        return $this->obj;
    }

    public function href($href){
        $this->href = $href;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function Style($style){
        parent::_Style($style);
        return $this->obj;
    }

    public function ContainerStyle($style){
        parent::_ContainerStyle($style);
        return $this->obj;
    }

    function __construct($icon = "", $url = "", $value = ""){
        parent::__construct();
        $this->icon = $icon;
        $this->value = $value;
        $this->obj = $this;
        $this->href = $url;
    }

    function __toString()
    {

        parent::__toString();

        return '<a href="'.$this->href.'" class="'.($this->class != '' ? ''.$this->class.'':'').'" '.($this->id != '' ? 'id="'.$this->id.'"':'').' title="'.$this->value.'" '.($this->style != '' ? 'style="'.join(";", $this->style).'"' : "").' '.$this->rest.'>
            <div class="flexElem alignElemsCenter" '.($this->containerStyle != '' ? 'style="'.join(";", $this->containerStyle).'"' : "").'><div class="ButtonContainer">
              <div class="Button">'.(($this->icon != '') ? $this->Icons->getIcon($this->icon, "20px", ($this->value != '' ? '' . $this->value . '' : ''), 'class="ButtonIcon"') : "").' '.$this->value.'</div>
            </div></div></a>';
    }

}

class Input {

    protected $name;
    protected $id;
    protected $class;
    protected $disabled;
    protected $readOnly;
    protected $size;
    protected $required;
    protected $value;
    protected $rest;

    protected function __Name($name){
        $this->name = $name;
    }

    protected function __Id($id){
        $this->id = $id;
    }

    protected function __Class($class)
    {
        $this->class = $class;
    }

    protected function __Disabled($disabled){
        if(is_bool($disabled)){
            $this->disabled = "disabled";
        } else {
            throw new Exception("disabled musí být boolean");
        }
    }

    protected function __ReadOnly($read){

        if(is_bool($read)){
            if($read) {
                $this->readOnly = "readonly";
            }
        } else {
            throw new Exception("readonly musí být boolean");
        }

    }

    protected function __Size($size){
        $this->size = $size;
    }

    protected function __Required($required){

        if(is_bool($required)){
            if($required) {
                $this->required = "required";
            }
        } else {
            throw new Exception("readonly musí být boolean");
        }

    }

    protected function _Rest($rest){
        $this->rest = $rest;
        return $this;
    }

    function __construct($name){
        $this->name = $name;
    }

    public function __toString() {
        return 'name="'.$this->name.'" value="'.$this->value.'" '.($this->id != '' ? 'id='.$this->id.'':'').' '.($this->class != '' ? 'class='.$this->class.'':'').' '.($this->disabled ? "disabled" : "").' '.($this->readOnly ? "readonly" : "").'  '.($this->size != '' ? 'size='.$this->size.'':'').' '.$this->required.'';
    }
}

class InputText extends Input
{

    private $maxLength;
    private $pattern;
    private $placeHolder;
    private $autoComplete;
    private $list;
    private $listD;
    private $obj;

    function __construct($name, $list = "", $listD = ""){
        $this->list = $list;
        $this->listD = $listD;
        parent::__construct($name);
        $this->obj = $this;
    }

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function MaxLength($max)
    {
        if (is_int($max) && $max > 0) {
            $this->pattern = $max;
        } else {
            throw new Exception("Maximální délka musí být celé číslo > 0");
        }
        return $this->obj;

    }

    public function Pattern($pattern)
    {
        $this->pattern = $pattern;
        return $this->obj;
    }

    public function Value($value)
    {
        $this->value = $value;
        return $this->obj;
    }

    public function PlaceHolder($plh)
    {
        $this->placeHolder = $plh;
        return $this->obj;
    }

    public function AutoComplete($auto)
    {
        if (is_bool($auto)) {
            if ($auto) {
                $this->autoComplete = 'autocomplete="on"';
            } else {
                $this->autoComplete = 'autocomplete="off"';
            }
        } else {
            throw new Exception("AutoComplete musí být boolean");
        }
        return $this->obj;
    }

    public function _List($list)
    {
        $this->list = $list;
        return $this->obj;
    }

    public function ListD($listD)
    {
        $this->listD = $listD;
        return $this->obj;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function __toString()
    {
        return '<input type="text" '. parent::__toString() . ' ' . ($this->maxLength != '' ? 'maxlength=' . $this->maxLength . '' : '') . ' ' . $this->autoComplete . ' ' . ($this->pattern != '' ? 'pattern=' . $this->pattern . '' : '') . ' ' . ($this->placeHolder != '' ? 'placeholder=' . $this->placeHolder . '' : '') . ' ' . ($this->list != '' ? 'list=' . $this->list . '' : '') . ' '.$this->rest.'/>
        '.$this->listD;
    }

}

class InputNum extends Input{

    private $max;
    private $min;
    private $step;
    private $obj;

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Max($max)
    {
        if (is_numeric($max)) {
            $this->max = $max;
        } else {
            throw new Exception("Max musí být číslo");
        }
        return $this->obj;

    }

    public function Min($min)
    {
        if (is_numeric($min)) {
            $this->min = $min;
        } else {
            throw new Exception("Min musí být číslo");
        }
        return $this->obj;

    }

    public function Step($step)
    {
        if (is_numeric($step)) {
            $this->step = $step;
        } else {
            throw new Exception("Min musí být číslo");
        }
        return $this->obj;

    }

    public function Value($value)
    {
        if(is_numeric($value)) {
            $this->value = $value;
        } else {
            throw new Exception("Input number musí mít hodnotu číslo");
        }
        return $this->obj;
    }

    function __construct($name)
    {
        parent::__construct($name);
        $this->obj = $this;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function __toString() {
        return '<input type="number"'. parent::__toString().' '.($this->min != '' ? 'min='.$this->min.'':'').' '.($this->max != '' ? 'max='.$this->max.'':'').' '.($this->step != '' ? 'step='.$this->step.'':'').' '.$this->rest.'/>';
    }

}

final class InputRange extends Input{

    private $max;
    private $min;
    private $step;
    private $obj;

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Max($max)
    {
        if (is_numeric($max)) {
            $this->max = $max;
        } else {
            throw new Exception("Max musí být číslo");
        }
        return $this->obj;

    }

    public function Min($min)
    {
        if (is_numeric($min)) {
            $this->min = $min;
        } else {
            throw new Exception("Min musí být číslo");
        }
        return $this->obj;

    }

    public function Step($step)
    {
        if (is_numeric($step)) {
            $this->step = $step;
        } else {
            throw new Exception("Min musí být číslo");
        }
        return $this->obj;

    }

    public function Value($value)
    {
        if(is_numeric($value)) {
            $this->value = $value;
        } else {
            throw new Exception("Input number musí mít hodnotu číslo");
        }
        return $this->obj;
    }

    function __construct($name)
    {
        parent::__construct($name);
        $this->obj = $this;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function __toString() {
        return '<input type="range"'. parent::__toString().' '.($this->min != '' ? 'min='.$this->min.'':'').' '.($this->max != '' ? 'max='.$this->max.'':'').' '.($this->step != '' ? 'step='.$this->step.'':'').' '.$this->rest.'/>';
    }

}

final class InputCheck extends Input{

    private $checked;
    private $text;
    private $obj;

    function __construct($name)
    {
        parent::__construct($name);
        $this->obj = $this;
    }

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Checked($ch)
    {
        if (is_bool($ch)) {
            if($ch) {
                $this->checked = "checked";
            }
        } elseif(intval($ch) == 1){

	     if(intval($ch) == 1){$this->checked = "checked";}

	 } else {}

        return $this->obj;
    }

    public function Value($value)
    {
        $this->value = $value;
        return $this->obj;
    }

    public function Text($text)
    {
        $this->text = $text;
        return $this->obj;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function __toString() {

        $uniq = uniqid();
        $this->Id($uniq);

        return '<input type="checkbox" '.parent::__toString().' '.$this->checked.' '.$this->rest.'/> '.$this->text .'
        <script type="text/javascript">iCheck($("#'.$uniq.'"));</script>';
    }

}

final class InputColor extends Input{

    private $obj;

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Value($value)
    {
        $this->value = $value;
        return $this->obj;
    }

    function __construct($name){
        parent::__construct($name);
        $this->obj = $this;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function __toString() {
        return '<input type="color" '. parent::__toString() .' '.$this->rest.'/>';
    }

}

final class InputDate extends Input{

    private $obj;

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Value($value)
    {
        $this->value = $value;
        return $this->obj;
    }

    function __construct($name){
        parent::__construct($name);
        $this->obj = $this;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function __toString() {
        return '<input type="date" '. parent::__toString().' '.$this->rest.'/>';
    }

}

final class InputEmail extends Input{

    private $obj;

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Value($value)
    {
        $this->value = $value;
        return $this->obj;
    }

    function __construct($name){
        parent::__construct($name);
        $this->obj = $this;

    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function __toString() {
        return '<input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" '.parent::__toString(). ' '.$this->rest.'/>';
    }

}

final class InputHidden extends Input{

    private $obj;

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Value($value)
    {
        $this->value = $value;
        return $this->obj;
    }

    function __construct($name){
        parent::__construct($name);
        $this->readOnly = true;
        $this->obj = $this;
    }

    public function __toString() {
        return '<input type="hidden" '. parent::__toString().' '.$this->rest.'/>';
    }

}

final class InputPassword extends Input{

    private $obj;

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Value($value)
    {
        $this->value = $value;
        return $this->obj;
    }

    function __construct($name){
        parent::__construct($name);
        $this->obj = $this;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function __toString() {
        return '<input type="password" '.parent::__toString().' '.$this->rest.'/>';
    }

}

final class InputImage extends Input{

    private $src;
    private $alt;
    private $obj;

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Value($value)
    {
        $this->value = $value;
        return $this->obj;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    function __construct($name){
        parent::__construct($name);
        $this->obj = $this;
    }

    public function __toString() {
        return '<input type="image" '.parent::__toString().' '.($this->src != '' ? 'src='.$this->src.'':'').' '.($this->alt != '' ? 'alt='.$this->alt.'':'').' '.$this->rest.'/>';
    }

}

class InputFile extends Input{

    protected $maxFiles = 20;
    protected $maxFileSize = "5Mb";
    protected $allowedFormats = "jpg,png,gif";
    protected $obj;

    function __construct($name){
        parent::__construct($name);
        $this->obj = $this;
    }

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function MaxFiles($max){
        if(is_int($max)) {
            $this->maxFiles = $max;
        } else {
            throw new Exception("Maximální počet souborů musí být celé číslo");
        }
        return $this->obj;
    }

    public function MaxFileSize($max){
        $this->maxFileSize = $max;
        return $this->obj;
    }

    public function AllowedFormats($formats){
        $this->allowedFormats = $formats;
        return $this->obj;
    }

    public  function __toString(){
        return '<input type="file" '.parent::__toString().'/>';
    }

}

final class InputFileBasic extends InputFile{

    function __construct($name){
        parent::__construct($name);
        $this->obj = $this;
    }

    public function __toString(){
        $input = new FileUploaderInput($this->maxFiles,$this->maxFileSize,$this->allowedFormats);
        return $input->Classic($this->name,$this->required);
    }

}

final class InputFileExtended extends InputFile{

    function __construct($name){
        parent::__construct($name);
        $this->obj = $this;
    }

    public  function __toString(){

        $input = new FileUploaderInput($this->maxFiles,$this->maxFileSize,$this->allowedFormats);
        return $input->Extended($this->name, $this->required);

    }

}

final class InputRadio extends Input{

    private $checked;
    private $text;
    private $obj;

    public function Name($name){
        parent::__Name($name); return $this->obj;
    }

    public function Id($id){
        parent::__Id($id); return $this->obj;
    }

    public function _Class($class)
    {
        parent::__Class($class); return $this->obj;
    }

    public function Disabled($disabled){
        parent::__Disabled($disabled); return $this->obj;
    }

    public function ReadOnly($read){
        parent::__ReadOnly($read); return $this->obj;
    }

    public function Size($size){
        parent::__Size($size); return $this->obj;
    }

    public function Required($required){
        parent::__Required($required);  return $this->obj;
    }

    public function Value($value)
    {
        $this->value = $value;
        return $this->obj;
    }

    public function Text($text)
    {
        $this->text = $text;
        return $this->obj;
    }

    public function Checked($ch)
    {
        if (is_bool($ch)) {
            if($ch) {
                $this->checked = "checked";
            }
        } else {
            throw new Exception("Checked musí být bool");
        }
        return $this->obj;

    }

    function __construct($name){
        parent::__construct($name);
        $this->obj = $this;
    }

    public function Rest($rest){
        parent::_Rest($rest);
        return $this->obj;
    }

    public function __toString() {

        $uniq = uniqid();
        $this->Id($uniq);

        return '<input type="radio" '.parent::__toString().' '.$this->checked.' '.$this->rest.'/> '.$this->text .' <script type="text/javascript">iCheck($("#'.$uniq.'"));</script>';
    }

}

final class InputSet{

    function __construct()
    {

    }

    public function Hidden($name){
        return new InputHidden($name);
    }

    public function Text($name){
        return new InputText($name);
    }

    public function Password($name){
        return new InputPassword($name);
    }

    public function Email($name){
        return new InputEmail($name);
    }

    public function Number($name){
        return new InputNum($name);
    }

    public function Range($name){
        return new InputRange($name);
    }

    public function FileBasic($name){
        return new InputFileBasic($name);
    }

    public function FileExtendet($name){
        return new InputFileExtended($name);
    }

    public function FileOld($name){
        return new InputFile($name);
    }

    public function CheckBox($name){
        return new InputCheck($name);
    }

    public function switchBox($name){

        $uniq = uniqid();

        $check = new InputCheck($name);
        $check->_Class("switchBtn")->Id("in".$uniq);

        return '<div id="to'.$uniq.'" class="toggle toggle-modern"></div>'.$check.'

        <script type="text/javascript">
        $(document).ready(function(){
            toggles($("#to'.$uniq.'"),$("#in'.$uniq.'"));
        });
        </script>

        ';

    }

    public function Radio($name){
        return new InputRadio($name);
    }

    public function Date($name){
        return new InputDate($name);
    }

    public function Color($name){
        return new InputColor($name);
    }

}

final class FormElements {

    use Collection\ObjSet;

    private $data = array();
    private $inputs;

    function __construct()
    {
        $this->loadSystem();
        $this->inputs = new InputSet();
    }

    public function Button($icon = "", $title = ""){
        return new Button($icon, $title);
    }

    public function TextAreaEditor($name, $value = "", $required = false)
    {

        if($value == ""){$value = "\r\n\r\n\r\n\r\n\r\n\r\n";}

        $id = uniqid();

        return '
        <textarea id="' . $id . '" ' . ($required ? "required" : "") . ' name="' . $name . '">' . $value . '</textarea>  <script type="text/javascript">

        $(function() {
            $("#' . $id . '").wysibb();
        });

        </script> ';
    }

    public function TextArea($name, $value, $required = false)
    {
        if($value == ""){$value = "<br /><br />";}
        return '<textarea ' . ($required ? "required" : "") . ' name="' . $name . '">' . $value . '</textarea>';
    }

    public function Input(){
        return $this->inputs;
    }

    public function InputTextWithList($name){

        $output = '<datalist id="'.$name.'_list">';

        foreach ($this->data as $record) {
            $output .= '<option value="' . $record[1] . '">';
        }

        $output .= '</datalist>';

        return $this->Input()->Text($name)->_List($name."_list")->ListD($output);
    }

    private function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    public function CheckboxField($name,$checked = ""){

        $output = '<div class="required">';
        $uniq = uniqid();

        if (is_array($checked)) {

            for ($i = 0; $i < count($this->data); $i++) {
                if ($this->in_array_r($this->data[$i][0],$checked)) {
                    $output .= $this->Input()->CheckBox($name."[".$i."]")->Checked(true)->Value($this->data[$i][0])->Text($this->data[$i][1]).'<br />';
                } else {
                    $output .= $this->Input()->CheckBox($name."[".$i."]")->Checked(false)->Value($this->data[$i][0])->Text($this->data[$i][1]).'<br />';
                }
            }

        } else {

            for ($i = 0; $i < count($this->data); $i++) {
                if ($checked == $this->data[$i][0]) {
                    $output .= $this->Input()->CheckBox($name."[".$i."]")->Checked(true)->Value($this->data[$i][0])->Text($this->data[$i][1]).'<br />';
                } else {
                    $output .= $this->Input()->CheckBox($name."[".$i."]")->Checked(false)->Value($this->data[$i][0])->Text($this->data[$i][1]).'<br />';
                }
            }

        }

        $output .= '</div>';

        $output .= '</select>';

        return $output;

    }

    public function RadioField($name,$checked = ""){

        $output = '';
        $uniq = uniqid();

        if (is_array($checked)) {

            for ($i = 0; $i < count($this->data); $i++) {

                if ($this->in_array_r($this->data[$i][0], $checked)) {
                    $output .= $this->Input()->Radio($name)->Required(true)->Checked(true)->Text("")->Value($this->data[$i][0]).'<br />';
                } else {
                    $output .= $this->Input()->Radio($name)->Required(true)->Checked(false)->Value($this->data[$i][0])->Text($this->data[$i][1]).'<br />';
                }
            }

        } else {

            for ($i = 0; $i < count($this->data); $i++) {

                if ($checked == $this->data[$i][0]) {
                    $output .= $this->Input()->Radio($name)->Required(true)->Checked(true)->Value($this->data[$i][0])->Text($this->data[$i][1]).'<br />';
                } else {
                    $output .= $this->Input()->Radio($name)->Required(true)->Checked(false)->Value($this->data[$i][0])->Text($this->data[$i][1]).'<br />';
                }
            }

        }

        $output .= '</select>';

        return $output;

    }

    public function Select($name, $defaultV = "", $noVal = array(false,""), $required = false, $addiction = "")
    {

        $uniq = uniqid();

        $select = '<select id="'.$uniq.'" name="' . $name . '" '.($required ? "required" : "").'" ' . $addiction . '>'.(($noVal[0]) ? '<option value="">'.$noVal[1].'</option>' : '').'';

        for ($i = 0; $i < count($this->data); $i++) {

            if ($defaultV == $this->data[$i][0]) {
                $select .= '<option value="' . $this->data[$i][0] . '" selected>' . $this->data[$i][1] . '</option>';
            } else {
                $select .= '<option value="' . $this->data[$i][0] . '">' . $this->data[$i][1] . '</option>';
            }
        }

        $select .= '</select>

        <script type="text/javascript">
            niceSelect($("#'.$uniq.'"));
        </script>

        ';

        return $select;
    }

    //
    // two methods used for load select, checkboxes, radios, inputwithlist data

    private function sortData(&$array){

        setlocale(LC_ALL,"cs_CZ.UTF-8");
        usort($array, function ($item1,$item2)
        {
            if ($item1[1] == $item2[1]) return 0;
            return ($item1[1] > $item2[1]) ? 1 : -1;
        });

    }

    public function clearData(){
    
        $this->data = array();
    
    }


    public function setDataFromArray($values, $texts)
    {
        if (is_array($values) && is_array($texts)) {

            $vals = array();

            for ($i = 0; $values[$i]; $i++) {
                array_push($vals,array($values[$i],$texts[$i]));
            }

            $this->sortData($vals);
            $this->data = $vals;
        }
    }

    public function setDataFromDb($array, $valueAttr, $textAttr){

        if(!empty($array)) {

            if (is_array($array[0])) {

                foreach ($array as $value) {

                    $text = $textAttr;

                    if (preg_match_all("/{(.*?)}/s", $textAttr, $matches)) {

                        for ($j = 0; $j < count($matches[0]); $j++) {

                            $pom = trim($matches[0][$j],"{");
                            $pom = trim($pom,"}");

                            $text = str_replace($matches[0][$j],$value[$pom],$text);

                        }
                    } else {
                        $text = str_replace($text,$value[$text],$text);
                    }

                    array_push($this->data, array($value[$valueAttr], $text));

                }

            }
        }

        $this->sortData($this->data);
    }

    public function dataPager($count, $visItPerSlide = 20, $steps = array()){

        $detect = new MobileDetect();

        if($detect->isMobile()){
            $visItPerSlide = 10;
        }

        $visItPerSlide--;


        $itemsVisibleCount = (int)$visItPerSlide/2;

        if($detect->isMobile()) {
            $itemsVisibleCount = 1;
        }

        if(empty($steps)) {
            $steps = array(20, 40, 60, 80, 100);
        }

        $this->setDataFromArray($steps, $steps);

        if(!$_GET["strankovani"]) {
            $selectedStep = 20;
        } else {
            $selectedStep = $_GET["strankovani"];
        }

        $pagesNum = intval($count / $selectedStep) + 1;

        $output = "";

        if(!$_GET["active"]) {
            $active = 1;
        } else {
            $active = $_GET["active"];
        }

        $step = 1;
        $arrowsL = "";
        $arrowsR = "";

        $level = 0;

        /*while(($step * 10) < $pagesNum){

            $step *= 10;*/

        $arrowsL = '<a class="dataPagerArrow" data-properties="{\'direction\': \'left\', \'level\': '.$level.', \'visItPerSlide\': '.$visItPerSlide.', \'visItPerSlideOrig\': '.$visItPerSlide.', mobile: '.($detect->isMobile() ? 'true' : 'false' ).'}">'.$this->Icons->getIcon("arrow_left").'</a>' . $arrowsL;
        $arrowsR .= '<a class="dataPagerArrow" data-properties="{\'direction\': \'right\', \'level\': '.$level.', \'visItPerSlide\': '.$visItPerSlide.', \'visItPerSlideOrig\': '.$visItPerSlide.', mobile: '.($detect->isMobile() ? 'true' : 'false' ).'}">'.$this->Icons->getIcon("arrow_right").'</a>';

        /*    $level++;
        }*/

        //$data = $this->dataPagerCount($active,0,$step,$pagesNum,$start,$selectedStep);

        $output .= '<div class="dataPager"><div class="dataPagerHeader flexElem flexWrap alignElemsCenter">
        <div class="flex flexElem alignElemsCenter"><div class="dataPagerLeft flexElem valignCenter" '.((($active - 10) > 0) ? "" : 'style="display: none;"').'>'.$arrowsL.'</div><div class="dataPagerBody">';

        $products = 1;

        if(($active - $itemsVisibleCount) < 0){
            $minLeft = 0;
            $maxLeft = $visItPerSlide;
        } elseif (($active + $itemsVisibleCount) > $pagesNum){
            $maxLeft = $pagesNum;
            $minLeft = $pagesNum - $visItPerSlide;
        } else {
            $minLeft = $active - $itemsVisibleCount;
            $maxLeft = $active + $itemsVisibleCount;
        }

        for($i = 1; $i <= $pagesNum; $i++){

            $output .= '<div class="dataPagerItem ' . (($i == $active) ? "dataPagerActive dataPagerChosen" : "") . '" style="'.(($i > $minLeft) && ($i < $maxLeft) ? '' : 'display: none;').'"><a href="' . $this->Root->getFullUrl()->addGetPart("products", $products . "-" . ($products + $selectedStep))->addGetPart("active", $i) . '">' . $i . '</a></div>';
            $products += $selectedStep + 1;
        }


        $output .= '</div><div class="dataPagerRight flexElem valignCenter" '.((($active + 10) < $pagesNum) ? "" : 'style="display: none;"').'>'.$arrowsR.'</div></div><div class="zobrazovatPO"> Zobrazovat po: '.$this->Select("pocP",$selectedStep,false,false,'class="dataPagerCountP"').'</div></div></div>

        <script type="text/javascript">

            var dataPagerLastW = 0;

            function dataPagerCheck(pager){

                function Check(elem){

                    var all = elem.find(".dataPagerBody .dataPagerItem");
                    var visible = all.filter(":visible");

                    var count = visible.length - 1;
                    var itemWidth = visible.first().outerWidth(true) + parseInt(visible.first().css("margin-left"));

                    var winWidth = $(window).width();

                    var arrows = elem.find(".dataPagerArrow");

                    var proprt =  eval(\'(\' + arrows.first().attr("data-properties") + \')\');
                    var proprt2 =  eval(\'(\' + arrows.last().attr("data-properties") + \')\');

                    if((winWidth > dataPagerLastW) && (dataPagerLastW > 0)){

                        var direction = "right";
                        var pom = visible.last().index();

                        while(((itemWidth * (count + 1)) + (arrows.first().outerWidth(true) * 2)) < elem.parent().width()){

                            if(count == proprt.visItPerSlideOrig){
                                break;
                            }

                            if(pom == all.last().index()){
                                direction = "left";
                                pom = visible.first().index();
                            }

                            if(direction == "right"){
                                count++;
                                pom++;
                                all.eq(pom).fadeIn();
                            } else {
                                count++;
                                pom--;
                                all.eq(pom).fadeIn();
                            }

                        }

                    } else {

                        while((itemWidth * (count + 1) + (arrows.first().outerWidth(true) * 2)) > winWidth){
                            visible.eq(count).fadeOut();
                            count--;
                        }

                        if(count < visible.length){
                            arrows.last().parent().show();
                        }

                    }

                    proprt.visItPerSlide = count;
                    proprt2.visItPerSlide = count;

                    arrows.first().attr("data-properties", JSON.stringify(proprt));
                    arrows.last().attr("data-properties", JSON.stringify(proprt2));

                    dataPagerLastW = winWidth;

                }

                if($(window).width() != dataPagerLastW){

                    if(isDefined(pager)){
                        Check(pager);
                    } else {
                        $(document).find(".dataPager").each(function(){
                            Check($(this));
                        });
                    }

                }

            }

            $(document).ready(function(){
                dataPagerCheck();
            });

            $(window).resize(function(){
                dataPagerCheck();
            });

            $(document).on("click",".dataPagerArrow",function(e){

                var properties = eval(\'(\' + $(this).attr("data-properties") + \')\');

                var direction = properties.direction;

                var arrow = $(this);
                var container = arrow.closest(".dataPager");

                var all = container.find(".dataPagerBody .dataPagerItem");
                var visible = all.filter(":visible");

                var left = visible.first().index();
                var right = visible.last().index();
                var count = all.length;

                if(properties.mobile){
                    var step = parseInt(properties.visItPerSlide/2);
                } else {
                    var step = properties.visItPerSlide;
                }

                var hide = true;

                if(direction == "right"){

                    var mover = right;
                    var last = right + step;
                    mover++;

                    for(mover; mover <= last; mover++){

                        if(mover > count-1){break;}

                        all.eq(mover).fadeIn(0);

                        if(left < right){
                            all.eq(left).fadeOut(0);
                        }
                        left++;

                        if(mover == count-1){
                            arrow.parent().hide();
                            break;
                        }

                    }

                    container.find(".dataPagerLeft").show();

                } else {

                   var mover = left;
                   var last = left - step;
                   mover--;

                   for(mover; mover >= last ; mover--){

                        if(mover < 0){break;}

                        all.eq(mover).fadeIn(0);

                        if(right > left){
                            all.eq(right).fadeOut(0);
                        }

                        right--;

                        if(mover == 0){
                            arrow.parent().hide();
                            break;
                        }

                   }

                   container.find(".dataPagerRight").show();

                }

                dataPagerCheck(container);

            });

            $(document).on("change",".dataPagerCountP",function(){

                var val = $(this).val();
                var url = new URL();
                url.addParameter("strankovani",val);
                url.useThisUrl("",false);

            });

        </script>

        <style>

        .dataPager{position: relative;display: inline-block;background-color: #ccc;color: #000;margin-top: 5px; border: 1px #ccc solid;}
        .dataPagerHeader {width: 100%; padding: 3px;color: #000; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;white-space: nowrap;}
        .dataPagerHeader .zobrazovatPO {padding-left: 8px; line-height: 30px;}
        .dataPagerBody{padding-top: 5px;display: inline-block;overflow: hidden;height: 29px;}
        .dataPagerLeft a, .dataPagerRight a {cursor: pointer;}
        .dataPagerItem {margin-left: 5px;padding: 4px; background-color: #2e2e2e;color: #fff;display: inline-block;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;}
        .dataPagerItem:hover {background-color: #ffff00;}
        .dataPagerItem a {color: #fff;text-decoration: none;}
        .dataPagerItem:hover a {color: #000;}
        .dataPagerVisible { display: inline-block;}
        .dataPagerActive a {color: #ff0000 !important;}
        .dataPagerChosen {background-color: #ffff00;}
        .dataPagerChosen a {color: #000;}

        /* Landscape phone to portrait tablet */
        @media (max-width: 979px) {

            .dataPagerBody{height: 50px;}
            .dataPagerItem {padding: 8px;}
            .dataPagerArrow img{width: 40px;}

        }

        </style>

                                        ';

        return $output;

    }

}