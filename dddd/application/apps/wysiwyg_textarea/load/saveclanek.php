<?php

$id = $_POST['id_clanku'];
$nadpis = $_POST['nadpis'];
$text = $_POST['textarea'];
$text = nl2br($text);
$text = mysqli_real_escape_string($text);

$tabulka = $_POST['tabulka'];
$nazev_identifikatoru = $_POST['nazev_identifikatoru'];
$cestadb = $_POST['cestadb'];

require($cestadb);

$link->query ("UPDATE ".$tabulka." SET Nadpis=\"".$nadpis."\", Popisek=\"".$text."\" WHERE ID_clanku LIKE(\"".$id."\")");

?>