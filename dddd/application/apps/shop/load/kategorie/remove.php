<?php

require("../../../../config.php");

$shop = new ShopAdmin();
echo $shop->removeCategory($_GET["id"]);

?>