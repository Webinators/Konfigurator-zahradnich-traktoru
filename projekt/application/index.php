<?php

require_once('config.php');
require_once('core/config.php');

$URL = $_GET["url"];

if($URL == ""){

    $view = new View(ROOT.PROJECT,"home");
    echo $view->display("main");

} else {

	$params = explode("/",$URL);

        if(count($params) == 1){ 
    
            $view = new View(ROOT.PROJECT,$params[0]);
            echo $view->display("main");
    
        } elseif(count($params) > 1) {
    
            try {
    
                if (is_dir(ROOT . PROJECT . CORE . "apps/" . $params[0]) OR is_dir(ROOT . PROJECT . "apps/" . $params[0])) {
    
                    unset($params[0]);
    
                    $_class = $params[1]; unset($params[1]);
                    $_method = $params[2]; unset($params[2]);
  
                    $reflectionMethod = new ReflectionMethod($_class, $_method);
                    $res = $reflectionMethod->invokeArgs(new $_class(), $params); 

                    if(!is_null($res)){
                        echo $res;
                    }
    
                } else {
    
                    echo "PAGE NOT FOUND";
    
                }
    
            } catch(Exception $e){
                echo "1->".$e->getMessage();
            }
    
        } else {
    
            echo "PAGE NOT FOUND";
            
        }
    


}


?> 