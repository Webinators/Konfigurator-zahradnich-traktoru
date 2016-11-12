<?php

require("../../../../config.php");

$shop = new ShopAdmin();
echo $shop->removeProduct($_GET["id"]);

?>