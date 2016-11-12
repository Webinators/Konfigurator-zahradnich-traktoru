<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 29. 10. 2016
 * Time: 15:25
 */

class Redirector
{

    public static function redirect($dest){

        if(!headers_sent()){

            header("Location: ".$dest."");

        } else {

            echo '<script type="text/javascript">

                window.location.href = "'.$dest.'";

            </script>';

        }

    }

}