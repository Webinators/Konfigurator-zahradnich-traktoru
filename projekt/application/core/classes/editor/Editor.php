<?php

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 28.3.2016
 * Time: 19:40
 */
class Editor
{

    function __construct(){

    }

    public function build($inside, $options, $position = "absolute")
    {

        $output = '

            <div class="MainContentEditContainer '.(($position == "relative") ? "rel" : "").'">

                <div class="mainContentEditorOptions '.(($position == "absolute") ? mainContentEditorOptionsSlide : "").' '.(($position == "relative") ? "rel" : "").' alignElemsLeft" style="position: '.$position.';">' . $options . '<br style="clear: both;"/></div>
                <div class="MainContentEditBody flexElem alignElemsCenter">' . $inside . '</div>

            </div>

        ';

        return $output;
    }

}