<?php

$data = '<table width="100%" border="1">';

foreach ($categories as $category) {
    $data .= '<tr><td>' . $category["Nazev_k"] . '</td><td><a href="index.php?page=shop/kategorie/edit&ID_kategorie=' . $category["ID_kategorie"] . '" data-target="tr">' . $icons->getIcon("edit") . '</a></td><td><a class="ajaxDel" data-destination="tr" href="' . $this->urlPath . 'load/kategorie/remove.php?id=' . $category["ID_kategorie"] . '">' . $icons->getIcon("remove") . '</a></td></tr>';
}

$data .= '</table>';

$options = '<a href="index.php?page=shop/kategorie/add">' . $icons->getIcon("add", "25px", "Přidat kategorii") . '</a>';

$editor = new Editor();
echo $editor->build($data, $options, "relative");

?>