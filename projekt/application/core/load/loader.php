<?php
$absolutP = dirname( realpath( __FILE__ ) );
$absolutP = explode("/", $absolutP);
unset($absolutP[count($absolutP)-1]);
$absolutP = implode("/", $absolutP).'/';

require("".$absolutP."config.php");

?>