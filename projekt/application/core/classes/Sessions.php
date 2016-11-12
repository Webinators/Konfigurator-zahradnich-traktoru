<?php

ob_start();

/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 21.3.2015
 * Time: 14:53
 */

class Sessions {

    private static $instance = null;

    private function Sessions()
    {

    }

    public static function initialize()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Sessions();
        }

        return self::$instance;
    }


    public function getSession($name = "",$required = true){

        if ($name != '') {

            ini_set('session.cookie_httponly', true);
            ini_set('session.session.use_only_cookies', true);

            session_start();
            session_regenerate_id();

            if (isset($_SESSION[$name]) && $_SESSION[$name] != '') {
                return $_SESSION[$name];
            } else {
                if($required) {
                    throw new Exception("Tato session neexistuje nebo nemá žádnou hodnotu!!!");
                } else {
                    return "";
                }
            }
        } else {
            throw new Exception("Neni uveden název session u zmeny session!!!");
        }
    }

    public function sessionExists($name){

        ini_set('session.cookie_httponly', true);
        ini_set('session.session.use_only_cookies', true);

        session_start();
        session_regenerate_id();

        if(isset($_SESSION[$name])){
            return true;
        } else {
            return false;
        }

    }

    public function createSession($name = "",$value = "",$changeIfExists = true)
    {

        if ($name != '') {
            if ($value != '') {

                ini_set('session.cookie_httponly', true);
                ini_set('session.session.use_only_cookies', true);
                ini_set('session.cookie_lifetime', 60 * 60 * 3);

                session_start();
                session_regenerate_id();

                if (!isset($_SESSION[$name])) {

                    $_SESSION[$name] = $value;

                } else {

                    if ($_SESSION[$name] != $value) {
                        if ($changeIfExists) {
                            $this->changeSession($name, $value);
                        } else {
                            throw new Exception("Tato session už byla vytvořená!!!");
                        }
                    }

                }

            } else {
                throw new Exception("Neni uvedená žádná hodnota u vytvoření session!!!");
            }
        } else {
            throw new Exception("Neni uveden název session u vytvoření session!!!");
        }
    }

    public function changeSession($name = "", $value = "")
    {
        if ($name != '') {
            if ($value != '') {

                ini_set('session.cookie_httponly', true);
                ini_set('session.session.use_only_cookies', true);
                ini_set('session.cookie_lifetime', 60*60*3);

                session_start();
                session_regenerate_id();

                if (isset($_SESSION[$name]) && $_SESSION[$name] != '') {

                    $_SESSION[$name] = $value;

                } else {
                    throw new Exception("Tato session neexistuje nebo nemá žádnou hodnotu!!!");
                }
            } else {
                throw new Exception("Neni uvedená žádná hodnota u zmene session!!!");
            }} else {
            throw new Exception("Neni uveden nazev session u zmeny session!!!");
        }
    }

    public function removeSession($name = ""){

        if($name != '') {

            ini_set('session.cookie_httponly', true);
            ini_set('session.session.use_only_cookies', true);

            session_start();
            session_regenerate_id();

            if (isset($_SESSION[$name])) {

                unset($_SESSION[$name]);

            }
        } else {
            throw new Exception("Neni uveden nazev session u mazani session!!!");
        }
    }

    public function destroySessions(){

        ini_set('session.cookie_httponly', true);
        ini_set('session.session.use_only_cookies', true);

        session_start();
        session_regenerate_id();

        session_destroy();

    }

} 

ob_end_flush();

?>