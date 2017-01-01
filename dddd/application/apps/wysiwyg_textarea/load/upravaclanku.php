<?php
$text = $_POST['textarea'];

$text = addslashes($text);
$text = nl2br($text);

$cestadb = $_POST['cestadb'];
$cestadb = "../../../".$cestadb;
$tabulka = $_POST['tabulka'];

$clanek_icka = $_POST['clanek_icka'];

$hlavnislozka = $_POST['hlavnislozka'];

$nazev_identifikatoru = $_POST['nazev_identifikatoru'];
$atribut = $_POST['atribut'];

$id = $_POST['id_clanku'];

require($cestadb);

$query = "SELECT * FROM ".$tabulka." WHERE ".$nazev_identifikatoru."=".$id."" or die("Problém: " . mysqli_error($link));   

$result = $link->query($query);

$vysledek = mysqli_fetch_array($result);
		
if($vysledek[$nazev_identifikatoru] != '')
{
$link->query ("UPDATE ".$tabulka." SET ".$atribut." = \"".$text."\" WHERE ".$nazev_identifikatoru."=".$id."");
} 
else
{
$link->query ("INSERT INTO ".$tabulka." VALUES(\"\",\"".$text."\")");
}

$lastid = $id;


if($clanek_icka != '')
{

$clanek_icka = str_replace("(","",$clanek_icka);
$clanek_icka = str_replace(")","",$clanek_icka);

$parts = explode(",",$clanek_icka);

foreach ($parts as $value)
{

if(is_dir("$hlavnislozka$lastid"))
{}
else
{
umask(0000);
mkdir("$hlavnislozka$lastid", 0777);
}

if(is_dir("$hlavnislozka$lastid/files/"))
{}
else
{
umask(0000);
mkdir("$hlavnislozka$lastid/files", 0777);
}

if(is_dir("$hlavnislozka$lastid/files/clanek"))
{}
else
{
umask(0000);
mkdir("$hlavnislozka$lastid/files/clanek", 0777);
}

$clanek_file = $_FILES['clanek_input_file'.$value.'']['name'];
move_uploaded_file($_FILES['clanek_input_file'.$value.'']['tmp_name'], "".$hlavnislozka.$lastid."/files/clanek/".$clanek_file);
}
}

$promenna = $_POST['nazev_promenne'];
$fotka = $_POST[''.$promenna.'_fotka'];

if($fotka != '')
{

if(is_dir("$hlavnislozka$lastid"))
{}
else
{
umask(0000);
mkdir("$hlavnislozka$lastid", 0777);
}

if(is_dir("$hlavnislozka$lastid/popisek_pic"))
{}
else
{
umask(0000);
mkdir("$hlavnislozka$lastid/popisek_pic", 0777);
}

$txt = fopen("$hlavnislozka$lastid/popisek_pic/pic.TXT", "w+");
fwrite($txt, $fotka);
fclose($txt);
}

$id_galerie = $_POST['id_pripojene_galerie'];
$nacist_obah_galerie = $_POST['zobrazit_obsah_galerie'];
$nadpis_galerie = $_POST['nadpis_pripojene_galerie'];
$popis_galerie = $_POST['popis_pripojene_galerie'];
$nazev = $_POST['nazev'];

if($id_galerie != '')
{

if(is_dir("$hlavnislozka$lastid"))
{}
else
{
umask(0000);
mkdir("$hlavnislozka$lastid", 0777);
}

if(is_dir("$hlavnislozka$lastid/clanek_sparovani"))
{}
else
{
umask(0000);
mkdir("$hlavnislozka$lastid/clanek_sparovani", 0777);
}

$txt = fopen("$hlavnislozka$lastid/clanek_sparovani/sparovani.TXT", "w+");
fwrite($txt, $id_galerie."%".$nacist_obah_galerie."%".$nadpis_galerie."%".$popis_galerie."%".$nazev);
fclose($txt);

}


?>
