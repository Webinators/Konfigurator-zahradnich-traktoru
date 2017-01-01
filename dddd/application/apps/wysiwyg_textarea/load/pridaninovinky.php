<?php

require("../../../config.php");

$nadpis = $_POST['nadpis'];

$text1 = $_POST['popisek'];

$text2 = $_POST['textarea'];
$text2 = addslashes($text2);
$text2 = nl2br($text2);

$text1 = preg_replace('~<....if gte mso .*.>.*<..endif...>~Usi', "", $text1);
$text1 = preg_replace('~<span.*>~Usi', "", $text1);
$text1 = preg_replace('~<\/span>~Usi', "", $text1);
$text1 = addslashes($text1);

$cestadb = $_POST['cestadb'];
$cestadb = "../../../".$cestadb;
$tabulka = $_POST['tabulka'];

$popisek_icka = $_POST['popisek_icka'];
$clanek_icka = $_POST['clanek_icka'];

$obrazek = $_POST['popisek_fotka'];

$hlavnislozka = $_POST['hlavnislozka'];

$nazev_identifikatoru = $_POST['nazev_identifikatoru'];

require($cestadb);

$datum = date("Y-m-d",time());
$cas = date("G:i:S",time());

use System\Authentication;
$user = Authentication\Users::initialize();
$idU = $user->getUserSession("user_id");

$link->query ("INSERT INTO ".$tabulka." VALUES(\"\",\"".$nadpis."\",\"".$text1."\",\"".$text2."\",\"".$datum."\",\"".$cas."\",\"".$idU."\")");

$query = "SELECT * FROM ".$tabulka." ORDER BY ".$nazev_identifikatoru." DESC LIMIT 1" or die("Problém: " . mysqli_error($link));$result = $link->query($query);

$result = $link->query($query);

$vysledek = mysqli_fetch_array($result);

$lastid = $vysledek[$nazev_identifikatoru];

$lastid = $lastid;

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


$directory = "../predimage/word/media/";

if (glob("$directory*.jpeg") != false || glob("$directory*.png") != false || glob("$directory*.gif") != false)
{

$filecountjpeg = count(glob("$directory*.jpeg"));
$pocetjpeg = $filecount;

$filecountpng = count(glob("$directory*.png"));
$pocetpng = $filecount;

$filecountgif = count(glob("$directory*.gif"));
$pocetgif = $filecount;

if(is_dir("$hlavnislozka$lastid"))
{
}
else
{
umask(0000);
mkdir("$hlavnislozka$lastid", 0777);
}

umask(0000);
mkdir("$hlavnislozka$lastid/word", 0777);

for($i=1;$i<=$pocetjpeg;$i++)
{

$from = $directory.'image'.$i.'.jpeg';
$to = ''.$hlavnislozka.$lastid.'/word/image'.$i.'.jpeg';

copy($from, $to);

}

for($i=1;$i<=$pocetpng;$i++)
{

$from = $directory.'image'.$i.'.jpeg';
$to = ''.$hlavnislozka.$lastid.'/word/image'.$i.'.jpeg';

copy($from, $to);

}

for($i=1;$i<=$pocetgif;$i++)
{

$from = $directory.'image'.$i.'.jpeg';
$to = ''.$hlavnislozka.$lastid.'/word/image'.$i.'.jpeg';

copy($from, $to);

}


$zmenit = "predimage/word/media";
$v = "obrazky_novinek/$lastid/word";

$text2 = str_replace($zmenit,$v,$text2);

$link->query ("UPDATE ".$tabulka." SET Text=\"".$text2."\" WHERE ".$nazev_identifikatoru."=".$lastid."");

}

?>