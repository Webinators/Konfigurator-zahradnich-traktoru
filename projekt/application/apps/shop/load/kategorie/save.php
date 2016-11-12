<?php

require("../../../../config.php");

$shop = new ShopAdmin();
echo $shop->saveCategory($_POST);

?>