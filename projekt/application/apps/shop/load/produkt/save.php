<?php

require("../../../../config.php");

$shop = new ShopAdmin();
echo $shop->saveProduct($_POST);

?>