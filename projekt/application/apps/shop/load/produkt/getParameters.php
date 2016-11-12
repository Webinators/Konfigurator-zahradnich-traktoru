<?php

require("../../../../config.php");

$shop = new ShopAdmin();
echo "0->".$shop->renderProductParams($_GET);

?>