<?php

require("../../../../config.php");

$shop = new ShopAdmin();
echo $shop->saveParametr($_POST);

?>