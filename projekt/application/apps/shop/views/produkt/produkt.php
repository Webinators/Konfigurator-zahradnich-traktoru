<?php
      
  $data = '<table width="100%" border="1">';

        foreach ($products as $product) {

            $data .= '<tr><td>' . $product["Nazev_p"] . '</td><td><a href="index.php?page=shop/produkt/edit&ID_produkt=' . $product["ID_produkt"] . '" data-target="tr">' . $icons->getIcon("edit") . '</a></td><td><a class="ajaxDel" data-destination="tr" href="' . $this->urlPath . 'load/produkt/remove.php?id=' . $product["ID_produkt"] . '">' . $icons->getIcon("remove") . '</a></td></tr>';

        }

        $data .= '</table>';

        $options = '
            <a href="index.php?page=shop/produkt/add">' . $icons->getIcon("add", "25px", "Přidat produkt") . '</a>
        ';


        $editor = new Editor();
        $output = $editor->build($data, $options, "relative");

	echo $output;

?>