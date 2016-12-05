<?php
      
  $data = '<table width="100%" border="1">';

        foreach ($products as $product) {

            $data .= '<tr><td>' . $product["Nazev_p"] . '</td><td><a href="'.$url.'shop/ShopAdmin/editProduct/' . $product["ID_produkt"] . '" data-target="tr">' . $icons->getIcon("edit") . '</a></td><td><a class="ajaxDel" data-destination="tr" href="'.$url.'shop/ShopAdmin/removeProduct/' . $product["ID_produkt"] . '">' . $icons->getIcon("remove") . '</a></td></tr>';

        }

        $data .= '</table>';

        $options = '
            <a href="'.$url.'shop/ShopAdmin/productForm">' . $icons->getIcon("add", "25px", "Přidat produkt") . '</a>
        ';


        $editor = new Editor();
        $output = $editor->build($data, $options, "relative");

	echo $output;

?>