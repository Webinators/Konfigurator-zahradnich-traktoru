<?php

class Database
{
    private $allowedOperators = array("=","!=","<>",">","<",">=","<=","!<","!>","ALL","ANY","SOME","BETWEEN","IN","LIKE","IS NULL","NOT ALL","NOT BETWEEN","NOT IN","NOT LIKE","IS NOT NULL");

    private $where = array();
    private $having = array();

    private $pdo = null;
    private $stmt;
    private $count;

    private $execute;

    private $info = array();

    private function connectDB($connStr,$name)
    {

        if (isset($connStr)) {
            if (isset($name)) {

                $str = explode(",", $connStr);

                if (count($localhost) == 4) {
                    $this->pdo = new PDO('mysql:host=' . $str[0] . ';dbname=' . $str[3] . ';charset=utf8', $str[1], $str[2]);
                } else {
                    $this->pdo = new PDO('mysql:host=' . $str[0] . ';dbname=' . $name . ';charset=utf8', $str[1], $str[2]);
                }

                $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } else {
                throw new Exception("Není uvedené hostname pro připojení k DB v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
            }
        } else {
            throw new Exception("Není uvedený host pro připojení k DB v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
        }
    }

    function __construct($connStr,$dbName)
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if (isset($trace[0])) {
            $this->info["file"] = $trace[0]["file"];
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        try {
            $this->connectDB($connStr, $dbName);
        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function begin()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollback(){
        return $this->pdo->rollBack();
    }

    public function getLasInsertedId(){
        return $this->pdo->lastInsertId();
    }

    public function tableExists($table)
    {
        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        if(isset($table)) {
            try {
                $result = $this->pdo->query("SELECT 1 FROM ".$table." LIMIT 1");
            } catch (Exception $e) {
                return false;
            }

            if ($result !== false) {
                return true;
            }
        } else {
            throw new Exception("Není uvedená žádná tabulka pro ověření, zda existuje v ".$this->info['file']." na řádku ".$this->info['line']."");
        }
    }

    public function tableIsEmpty($table)
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        if(isset($table)) {

            $stmt = $this->pdo->prepare('SELECT * FROM '.$table.' LIMIT 1');

            $data = $stmt->fetchAll();

            if($this->countTableRows($data) == 0){
                return true;
            } else {
                return false;
            }

        } else {
            throw new Exception("Není uvedená žádná tabulka pro ověření, zda je prázdná v ".$this->info['file']." na řádku ".$this->info['line']."");
        }
    }

    public function getTableColumns($table)
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        if (isset($table)) {

            if ($this->tableExists($table)) {

                $rs = $this->pdo->query('SELECT * FROM ".$table." LIMIT 0');

                $columns = array();

                for ($i = 0; $i < $rs->columnCount(); $i++) {
                    $col = $rs->getColumnMeta($i);
                    array_push($columns, $col);
                }

                return $columns;

            } else {
                throw new Exception("Tabulka " . $table . " neexistuje v ".$this->info['file']." na řádku ".$this->info['line']."!!!");
            }

        } else {
            throw new Exception("Není uvedená žádná tabulka pro získání sloupců v ".$this->info['file']." na řádku ".$this->info['line']."");
        }
    }

    public function addWherePart($continue = "",$attribute = "",$operator = "",$value = "",&$destination = "")
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        if($destination == ''){
            $destination = &$this->where;
        }

        if(!is_array($destination)){
            $destination = array();
        }

        if (is_array($value))
        {
            $this->buildSelect(false, $value);
        }

        if (empty($destination)) {

            if($this->checkRequiredTermValues($continue,$attribute,$operator)) {

                $attribute0 = $continue;
                $operator0 = strtoupper($attribute);
                $value0 = $operator;

                array_push($destination,array("attribute" => $attribute0,"operator" => $operator0, "value" => $value0));

            }

        } else {

            if($this->checkRequiredTermValues($attribute,$operator,$value,$continue)) {

                $attribute = $attribute;
                $operator = strtoupper($operator);

                array_push($destination,array("attribute" => $attribute,"continue" => $continue, "operator" => $operator, "value" => $value));

            }
        }
    }

    public function addHavingPart($continue = "",$attribute = "",$operator = "",$value = "",&$destination = "")
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        if($destination == ''){
            $destination = &$this->having;
        }

        if(!is_array($destination)){
            $destination = array();
        }

        if (is_array($value))
        {
            $value = $this->buildSelect($value);
        }

        if (empty($destination)) {

            if($this->checkRequiredTermValues($continue,$attribute,$operator)) {

                $attribute0 = $continue;
                $operator0 = strtoupper($attribute);
                $value0 = $operator;

                array_push($destination, array("attribute"=>$attribute0,"operator" => $operator0, "value" => $value0));

            }

        } else {

            if($this->checkRequiredTermValues($attribute,$operator,$value,$continue)) {

                $attribute = $attribute;
                $operator = strtoupper($operator);

                array_push($destination, array("attribute"=>$attribute,"continue" => $continue, "operator" => $operator, "value" => $value));

            }
        }

    }

    private function checkRequiredTermValues($attribute,&$operator,&$value,&$continue = "")
    {

        if (isset($attribute)) {
            if (isset($operator)) {

                if($operator == "IS NULL" || $operator == "IS NOT NULL"){

                    return true;

                } else {

                    if (isset($value)) {

                        if ($this->checkOperator(strtoupper($operator))) {

                            $operator = strtoupper($operator);

                            if ($continue != "") {

                                if (strtoupper($continue) == "AND" || strtoupper($continue) == "OR") {

                                    $continue = strtoupper($continue);

                                    return true;

                                } else {

                                    throw new Exception("Mezi podmínkami může být pouze AND nebo OR v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");

                                }
                            } else {
                                return true;
                            }

                        } else {

                            $operators = "";

                            foreach ($this->allowedOperators as $value1) {
                                if ($operators == '') {
                                    $operators .= $value1;
                                } else {
                                    $operators .= ", " . $value1;
                                }
                            }

                            throw new Exception("Takový operátor není definovaný v " . $this->info['file'] . " na řádku " . $this->info['line'] . ". Použijte jeden z následujících: " . $operators . "");
                        }

                    } else {
                        throw new Exception("Není uvedena hodnota v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
                    }
                }

            } else {
                throw new Exception("Není uveden operátor v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
            }
        } else {
            throw new Exception("Není uveden název sloupce v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
        }
    }

    private function checkOperator($operator)
    {
        if(in_array($operator,$this->allowedOperators)){
            return true;
        } else {
            return false;
        }

    }

    private function buildSelect($parameters = array())
    {

        if(!empty($parameters)) {

            $attributes = $this->prepareAttributes($parameters[0]);

            $tables = $this->prepareTables($parameters[1]);

            if($this->termPartsAreChecked($parameters[2])){
                $where = $this->prepareTerms($parameters[2],true,false,false);
            } else {
                $where = $this->prepareTerms($parameters[2],true,false,true);
            }

            $groupBy = $this->prepareGroupBy($parameters[3]);

            if($this->termPartsAreChecked($parameters[2])){
                $having = $this->prepareTerms($parameters[4],false,true,false);
            } else {
                $having = $this->prepareTerms($parameters[4],false,true,true);
            }

            $orderByParts = $this->prepareOrderBy($parameters[5]);

            $limit = $this->prepareLimit($parameters[6]);

            $select = "SELECT ".$attributes." FROM ".$tables." ".$where." ".$groupBy." ".$having." ".$orderByParts." ".$limit."";

            return $select;

        }
    }

    private function termPartsAreChecked($term){

        $i=0;

        foreach($term as $key => $value){

            if($i == 0){

                if($value["operator"] == '' &&  $value["value"] == ''){
                    return false;
                }

            } else {

                if($value["continue"] == '' && $value["operator"] == '' && $value["value"] == ''){
                    return false;
                }

            }

            $i++;
        }

        return true;

    }

    private function prepareAttributes($attr)
    {

        if (isset($attr)) {
            return $attr;
        } else {
            throw new Exception("Není definovaný žádný atribut v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
        }

    }

    private function prepareTables($tables)
    {

        if ($tables != '') {
            return $tables;
        } else {
            throw new Exception("Není definována žádná tabulka v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
        }

    }

    private function prepareTerms($data,$where = false, $having = false, $processData = true)
    {
        if($where == true || $having == true) {

            if (!empty($data)) {

                $terms = array();

                if ($processData == true) {

                    foreach ($data as $key => $value) {
                        foreach ($value as $values) {

                            if (empty($term)) {

                                $this->addWherePart($key, $values[0], $values[1], "", $terms);

                            } else {

                                $this->addWherePart($values[0], $key, $values[1], $values[2], $terms);

                            }

                        }
                    }
                }

                if ($processData == false) {
                    $terms = $data;
                }

                if($where){
                    $prefix = "_W";
                } else {
                    $prefix = "_H";
                }

                $term = "";

                foreach($terms as $value){

                    if($term == ''){

                        $term .= $this->makeTerm($value["attribute"],$value["operator"],$value["value"],"",$prefix);

                    } else {

                        $term .= $this->makeTerm($value["attribute"],$value["operator"],$value["value"],$value["continue"],$prefix);

                    }

                }

                $finalTerm = "WHERE ".$term;

                return $finalTerm;

            } else {
                return "";
            }
        }

        return "";
    }

    private function makeTerm($attribute,$operator,$value,$continue = "",$prefix)
    {

        $back = debug_backtrace();

        switch($operator){

            case "=":
            case "!=":
            case "<>":
            case ">":
            case "<":
            case ">=":
            case "<=":
            case "!<":
            case "!>":

                $execute = $this->newExecute($attribute,$value,$prefix);

                return " ".$continue." ".$attribute." ".$operator.":".$execute."";
                break;

            case "LIKE":
            case "NOT LIKE":

                $execute = $this->newExecute($attribute,$value,$prefix);

                return " ".$continue." ".$attribute." ".$operator."(:".$execute.")";
                break;

            case "IN":
            case "NOT IN":

                $output = " ".$continue." ".$attribute." ".$operator."(";
                $inParts = explode(",",$value);
                $parts = array();

                foreach($inParts as $key => $value){
                    $execute = $this->newExecute($attribute,$value,$prefix);
                    array_push($parts, ":".$execute."");
                }

                $output .= implode(",",$parts).") ";

                return $output;
                break;

            case "BETWEEN":
            case "NOT BETWEEN":

                $output = " ".$continue." ".$attribute." ".$operator."";
                $betweenParts = explode(",",$value);

                if($betweenParts[0] != '' && $betweenParts[1] != ''){

                    $execute0 = $this->newExecute($attribute,$betweenParts[0],$prefix);
                    $execute1 = $this->newExecute($attribute,$betweenParts[1],$prefix);

                    $output .= ":".$execute0." AND :".$execute1."";
                } else {
                    throw new Exception("Pro BETWEEN musí být uvedeny dvě hodnoty oddělené čárkou v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
                }

                return $output;

                break;

            case "ALL":
            case "ANY":
            case "EXISTS":
            case "NOT EXISTS":

                return " ".$continue." ".$attribute." ".$operator."(".$value.")";
                break;

            case "IS NULL":
            case "IS NOT NULL":

                return " ".$continue." ".$attribute." ".$operator."";

                break;
        }

    }

    private function bindvalues(&$array, &$stmt){

        if(!empty($array)) {
            foreach ($array as $key => $value) {
                $stmt->bindValue($key, $value[0], $value[1]);
            }
        }

    }

    private function newExecute($target,$value,$prefix)
    {

        $target = str_replace(".", "_", $target);

        $i = 0;

        if(!empty($this->execute)) {
            foreach ($this->execute as $key => $val) {
                if ($key == ":" . $target . $prefix . $i . "") {
                    $i++;
                }
            }
        }

        $this->execute[":" . $target . $prefix . $i . ""] = array($value, $this->checkType($value));

        return $target . $prefix . $i;

    }

    private function checkType($prom){

        switch(gettype($prom)){

            case "boolean":
                return PDO::PARAM_BOOL;
                break;
            case "NULL":
                return PDO::PARAM_NULL;
                break;
            default:
                return PDO::PARAM_STR;
                break;
        }

    }

    private function prepareGroupBy($group)
    {
        if ($group != '') {
            return "GROUP BY " . $group . "";

        } else {
            return "";
        }
    }

    private function prepareOrderBy($order)
    {
        if ($order != "") {

            $orderByParts = explode("->", $order);

            if ($orderByParts[0] != '' && $orderByParts[1] == 'DESC' || $orderByParts[0] != '' && $orderByParts[1] == 'ASC') {
                $orderBy = "ORDER BY " . $orderByParts[0] . " " . $orderByParts[1] . "";

            } else {
                throw new Exception("Špatně uvedený ORDER BY v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
            }

            return $orderBy;
        } else {
            return "";
        }
    }

    private function prepareLimit($limit)
    {
        if($limit != '') {

            $newLimit = "";

            if (is_numeric($limit)) {
                $newLimit = "LIMIT " . $limit . "";
            } else {
                throw new Exception("Limit musí být číslo ORDER BY v " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
            }

            return $newLimit;
        } else {
            return "";
        }
    }

    public function clear()
    {
        $this->where = array();
        $this->having = array();
        $this->execute = array();
        $this->join = array();
    }

    private function countTableRows($data)
    {
        $rows = $data;

        $col = 0;

        if (count($rows) > 0) {
            foreach ($rows as $key => $value) {
                $col++;
            }

            return $col;

        } else {

            return 0;
        }
    }

    public function selectFromTable($attributes, $table, $groupBy = "", $order = "", $limit = "")
    {
        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        $select = $this->buildSelect(array(str_replace("(AGGR)","",$attributes),$table,$this->where,$groupBy,$this->having,$order,$limit));
        $stmt = $this->pdo->prepare($select);

        $this->bindvalues($this->execute,$stmt);

        if($stmt->execute()) {

            if(preg_match("/(AGGR)/",$attributes)){
                $this->stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->count = $this->countTableRows($this->stmt);
            } else {
                $this->stmt = $stmt->fetchAll();
                $this->count = $this->countTableRows($this->stmt);
            }

        } else {
            throw new Exception("SELECT neproběhl správně v souboru ".$this->info['file']." na řádku ".$this->info['line']."");
        }

        $this->clear();

    }

    public function getRows()
    {
        return $this->stmt;
    }

    public function countRows()
    {
        return $this->count;
    }

    public function insertIntoTableOnce($attributes = "", $table , $values = array(), $check = ""){

        if($check != '') {

            $attrArr = explode(",",$check);

            for($i = 0; $i < count($attrArr); $i++) {

                if($i == 0) {
                    if (is_numeric($values[$i])) {
                        $this->addWherePart($attrArr[$i], "=", "".$values[$i]."");
                    } else {
                        $this->addWherePart($attrArr[$i], "LIKE", "".$values[$i]."");
                    }
                } else {
                    if (is_numeric($values[$i])) {
                        $this->addWherePart("AND",$attrArr[$i], "=", "".$values[$i]."");
                    } else {
                        $this->addWherePart("AND",$attrArr[$i], "LIKE", "".$values[$i]."");
                    }
                }
            }

            $this->selectFromTable($attributes, $table);
            $count = $this->countRows();

            $this->clear();

            if($count == 0){

                $this->insertIntoTable($attributes, $table , $values);

            } else {
                throw new Exception("Tyto data už jsou zaznamenána!");
            }

        } else {
            throw new Exception("Není uvedeno jaké hodnoty se mají kontrolovat v souboru ".$this->info['file']." na řádku ".$this->info['line']."!");
        }

    }

    public function insertIntoTable($attributes = "", $table , $values = array())
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        reset($values);

        if(!empty( $values ) && $table != '')
        {

            $inserted = "";
            $execute = array();

            foreach ($values as $key => $value)
            {

                if($inserted == "")
                {
                    $inserted .= ":value".$key."";
                }
                else
                {
                    $inserted .= ",:value".$key."";
                }

                $execute[':value'.$key.''] = array($value, $this->checkType($value));

            }

            if($attributes != '')
            {
                $attributes = "(".$attributes.")";
            }

            $stmt = $this->pdo->prepare('INSERT INTO '.$table.' '.$attributes.' VALUES ('.$inserted.')');
            $this->bindvalues($execute,$stmt);

            if($stmt->execute()){} else {throw new Exception("INSERT neproběhl správně v souboru ".$this->info['file']." na řádku ".$this->info['line']."");  exit;}

        }
        else
        {
            throw new Exception("Musíte zadat hodnoty a tabulku v INSERT v souboru ".$this->info['file']." na řádku ".$this->info['line']."");  exit;
        }
    }

    public function updateTable($table, $attributes = "", $values = array())
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        if($attributes != '' && $table != '' && !empty($values))
        {

            if(!empty($this->where)) {

                $where = $this->where;
                $where = $this->prepareTerms($where, "", true, false, false);

                $attributesParts = explode(",", $attributes);
                $i = 0;
                $updateParts = "";
                $execute = array();

                foreach ($values as $key => $value) {
                    if ($updateParts == "") {
                        $updateParts .= $attributesParts[$i] . "=:val" . trim($attributesParts[$i]) . $key . $i;
                        $execute[':val' . trim($attributesParts[$i]) . $key . $i . ''] = array($value, $this->checkType($value));
                    } else {
                        $updateParts .= ', ' . $attributesParts[$i] . "=:val" . trim($attributesParts[$i]) . $key . $i;
                        $execute[':val' . trim($attributesParts[$i]) . $key . $i . ''] = array($value, $this->checkType($value));
                    }

                    $i++;
                }

                $stmt = $this->pdo->prepare('UPDATE ' . $table . ' SET ' . $updateParts . ' ' . $where . '');

                $this->bindvalues($this->execute,$stmt);
                $this->bindvalues($execute,$stmt);

                if (!$stmt->execute()) {
                    throw new Exception("UPDATE neproběhl správně v souboru " . $this->info['file'] . " na řádku " . $this->info['line'] . "");
                }
            } else {
                throw new Exception("Musíte zadat podmínku u UPDATE TABLE v souboru ".$this->info['file']." na řádku ".$this->info['line']."");
            }

        } else {
            throw new Exception("Musíte zadat atributy, hodnoty a tabulku u UPDATE TABLE v souboru ".$this->info['file']." na řádku ".$this->info['line']."");
        }

        $this->clear();

    }

    public function createTable($table, $columns = "")
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        if($table != '' && $columns != '')
        {
            if(!$this->tableExists($table)) {
                $stmt = $this->pdo->exec('CREATE TABLE IF NOT EXISTS ' . $table . ' (' . $columns . ') DEFAULT CHARSET=utf8;');
            }
        }
        else
        {
            throw new Exception("Musíte zadat tabulku a sloupce u CREATE TABLE v souboru ".$this->info['file']." na řádku ".$this->info['line']."");  exit;
        }
    }

    public function deleteFromTable($table)
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[0])){
            $this->info["line"] = $trace[0]["line"];
        }

        unset($trace);

        if(isset($table))
        {

            if(!empty($this->where)){

                $where = $this->where;
                $where = $this->prepareTerms($where,"",true,false,false);

                $stmt = $this->pdo->prepare('DELETE FROM '.$table.' '.$where.'');
                $this->bindvalues($this->execute,$stmt);

                if($stmt->execute()) {} else {throw new Exception("Smazání neproběhlo správně u DELETE v souboru ".$this->info['file']." na řádku ".$this->info['line'].""); }

            } else {
                throw new Exception("Musíte zadat podmínku u DELETE v souboru ".$this->info['file']." na řádku ".$this->info['line']."");
            }

        } else {
            throw new Exception("Musíte zadat tabulku u DELETE v souboru ".$this->info['file']." na řádku ".$this->info['line']."");
        }

        $this->clear();
    }

    public function deleteAllFromTable($table)
    {

        $e = new Exception();
        $trace = $e->getTrace();

        if(isset($trace[1])){
            $this->info["line"] = $trace[1]["line"];
        }

        unset($trace);

        if(isset($table))
        {

            $stmt = $this->pdo->prepare('DELETE FROM '.$table.'');
            if($stmt->execute()) {} else {throw new Exception("Smazání neproběhlo správně u DELETE v souboru ".$this->info['file']." na řádku ".$this->info['line']."");}

        } else {
            throw new Exception("Musíte zadat tabulku v DELETE ALL v souboru ".$this->info['file']." na řádku ".$this->info['line']."");
        }
    }

    public function close()
    {
        $this->clear();

        try
        {
            $this->pdo = null;
            $this->stmt = null;
            $this->count = "";
            $this->execute = "";
            $this->where = array();
            $this->having = array();
            $this->join = array();
            return $this->pdo;

        }
        catch (Exception $e){
            $this->throw_Error($e->getMessage());
        }
    }

}

?>

