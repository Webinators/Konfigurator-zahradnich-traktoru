<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 23. 4. 2016
 * Time: 19:30
 */

class Output {

    public function Success($message){
        echo '0->'.$message;
    }

    public function Error($message){
        echo '1->'.$message;
    }

    public function Data($data){
        echo '0->'.$data;
    }

}