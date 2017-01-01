<?php

include("../../../config.php");

print_r($_POST);

try{

    $popt = new Poptavka("");
    echo $popt->send($_POST);

} catch (Exception $e){
    echo "1->".$e->getMessage();
}

?>
