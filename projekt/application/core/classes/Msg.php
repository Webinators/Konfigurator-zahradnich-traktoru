<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 10.11.2016
 * Time: 22:34
 */

use System\Objects\Collection;


class Msg
{
    use Collection\ObjSet;

    public function setMsg($msg, $type = "success"){

        if($this->Sessions->getSession("messages",false) != ''){
            $all = json_decode($this->Sessions->getSession("messages", false));
        } else {
            $all = array();
        }

        array_push($all, array("icon" => $type, "data" => $msg));

        $this->Sessions->createSession("messages", json_encode($all));

    }

    public function showMsgs(){

        $all = json_decode($this->Sessions->getSession("messages",false));
        if(empty($all)){$all = array();}

        $output = '<script type="text/javascript">$(document).ready(function(){';

        foreach($all as $msg){

            $output .= '
                mainWindow.alert({
                    buttonPointer: undefined,
                    content: '.json_encode($msg).',
                    type: "message"
                }, function(){

                });
            ';
        }

        $output .= '});</script>';

        $this->Sessions->removeSession("messages");

        return $output;

    }
}