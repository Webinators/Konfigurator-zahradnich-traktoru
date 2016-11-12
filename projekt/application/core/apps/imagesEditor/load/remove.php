<?php

require_once("../../../config.php");

try {

    $editor = new ImagesEditor();

    echo "0->" . $editor->removeImg($_POST);

} catch(Exception $e){
    echo "1->".$e->getMessage();
}

?>