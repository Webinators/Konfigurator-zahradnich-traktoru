<?php

if(PATH != false){
  include(PATH);
} else {
  $shop = new Shop(); 
  echo $shop->printProducts();
}

echo '<script type="text/javascript" src="' . URL . 'js/product.js"></script>';


?>