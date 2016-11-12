<?php
$nadpis = $_POST['nadpis'];

$text1 = $_POST['popisek'];
$text2 = $_POST['textarea'];

$text1 = preg_replace('~<....if gte mso .*.>.*<..endif...>~Usi', "", $text1);
$text1 = preg_replace('~<span.*>~Usi', "", $text1);
$text1 = preg_replace('~<\/span>~Usi', "", $text1);
$text1 = addslashes($text1);

$text2 = addslashes($text2);
$text2 = nl2br($text2);

$cestadb = $_POST['cestadb'];
$cestadb = "../../../".$cestadb;
$tabulka = $_POST['tabulka'];

$popisek_icka = $_POST['popisek_icka'];
$clanek_icka = $_POST['clanek_icka'];

$obrazek = $_POST['popisek_fotka'];

$hlavnislozka = $_POST['hlavnislozka'];

$nazev_identifikatoru = $_POST['nazev_identifikatoru'];

$id_novinky = $_POST['id_novinky'];

require($cestadb);

$link->query ("UPDATE ".$tabulka." SET Nadpis=\"".$nadpis."\", Popisek = \"".$text1."\", Text = \"".$text2."\" WHERE ".$nazev_identifikatoru."=".$id_novinky."");

$lastid = $id_novinky;

if($popisek_icka != '')
{

$popisek_icka = str_replace("(","",$popisek_icka);
$popisek_icka = str_replace(")","",$popisek_icka);

$parts = explode(",",$popisek_icka);

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
mkdir("$hlavnislozka$lastid/files/popisek", 0777);
}


$popisek_file = $_FILES['popisek_input_file'.$value.'']['name'];
move_uploaded_file($_FILES['popisek_input_file'.$value.'']['tmp_name'], "".$hlavnislozka.$lastid."/files/popisek/".$popisek_file);
}
}

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

$directory = "../predimage/word/media/";

if (glob("$directory*.jpeg") != false || glob("$directory*.png") != false || glob("$directory*.gif") != false)
{

$filecountjpeg = count(glob("$directory*.jpeg"));
$pocetjpeg = $filecount;

$filecountpng = count(glob("$directory*.png"));
$pocetpng = $filecount;

$filecountgif = count(glob("$directory*.gif"));
$pocetgif = $filecount;

if(is_dir("../obrazky_novinek/$lastid"))
{
}
else
{
umask(0000);
mkdir("../obrazky_novinek/$lastid", 0777);
}

for($i=1;$i<=$pocetjpeg;$i++)
{

$from = $directory.'image'.$i.'.jpeg';
$to = '../obrazky_novinek/'.$lastid.'/image'.$i.'.jpeg';

copy($from, $to);

}

for($i=1;$i<=$pocetpng;$i++)
{

$from = $directory.'image'.$i.'.jpeg';
$to = '../obrazky_novinek/'.$lastid.'/image'.$i.'.jpeg';

copy($from, $to);

}

for($i=1;$i<=$pocetgif;$i++)
{

$from = $directory.'image'.$i.'.jpeg';
$to = '../obrazky_novinek/'.$lastid.'/image'.$i.'.jpeg';

copy($from, $to);

}


$zmenit = "predimage/word/media";
$v = "obrazky_novinek/$lastid";

$text2 = str_replace($zmenit,$v,$text2);
$text2 = nl2br($text2);
$text2 = mysqli_real_escape_string($text2);

$link->query ("UPDATE ".$tabulka." SET Text=\"".$text2."\" WHERE ".$nazev_identifikatoru."=".$lastid."");

}

?>