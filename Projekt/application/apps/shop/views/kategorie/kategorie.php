<?php

$data = '<table width="100%" border="1">';

foreach ($categories as $category) {
    $data .= '<tr><td>' . $category["Nazev_k"] . '</td><td><a href="'.$url.'shop/ShopAdmin/editCategory/' . $category["ID_kategorie"] . '" data-target="tr">' . $icons->getIcon("edit") . '</a></td><td><a class="ajaxDel" data-destination="tr" href="'.$url.'shop/ShopAdmin/removeCategory/' . $category["ID_kategorie"] . '">' . $icons->getIcon("remove") . '</a></td></tr>';
}

$data .= '</table>';

$options = '<a href="'.$url.'shop/ShopAdmin/categoryForm">' . $icons->getIcon("add", "25px", "Přidat kategorii") . '</a>';

$editor = new Editor();
echo $editor->build($data, $options, "relative");

?>