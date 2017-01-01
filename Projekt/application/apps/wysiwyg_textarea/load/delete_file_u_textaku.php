<?php
$slozka = $_POST['way'];

if($slozka != '')
{
unlink($slozka);
}
?>