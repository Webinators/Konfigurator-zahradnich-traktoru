<?php   
     
$data = '<table id="parametersTable" width="100%" border="1">';

foreach ($params as $param) {
 $data .= $renderer->renderParam($param);
}

$data .= '</table>';

$options = '
   <a class="ajaxWin" href="' . $url . 'shop/ShopAdmin/parametrForm">' . $icons->getIcon("add", "25px", "Přidat parametr") . '</a>
';

$editor = new Editor();
$output = $editor->build($data, $options, "relative");
$output .= $editor->build("", $options, "relative");

echo $output;

?>