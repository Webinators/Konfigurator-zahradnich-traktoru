<?php

require("../../../../config.php");

$shop = new ShopAdmin();
echo $shop->removeParametr($_GET["id"]);

?>