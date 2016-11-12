<?php

require("../../../../config.php");

$shop = new ShopAdmin();
echo $shop->editParametr($_GET);

?>