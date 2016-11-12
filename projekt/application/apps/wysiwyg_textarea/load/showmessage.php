
<?php

$id = $_POST['id'];
$hlavni_slozka = $_POST['slozka'];
$galerie_slozka = $_POST['galerie_slozka'];

$fullpath = $_SERVER["PHP_SELF"];

$pathparts = explode("/",$fullpath);

unset($pathparts[count($pathparts)-1]);

$tecky = "";

for($i=1;$pathparts[$i];$i++)
{
$tecky .= "../"; 
}

$hlavni_slozka = $tecky.$hlavni_slozka.'';
$galerie_slozka = $tecky.$galerie_slozka;

echo 
'
<link rel="stylesheet" href="'.$galerie_slozka.'css/galerie.css" type="text/css" media="all" />
';

require(''.$tecky.'data/db_clanky.php');

$query = "SELECT * FROM Aktuality WHERE ID_clanku=".$id."" or die("Problém: " . mysqli_error($link));

$result = $link->query($query);
		
$vysledek = mysqli_fetch_array($result);

$text = strtr($vysledek['Text'],"|","\"");

$text = stripslashes($text);

$text = preg_replace('/\[b](.*?)\[\/b]/si', '<b>$1</b>', $text);
$text = preg_replace('/\[u](.*?)\[\/u]/si', '<u>$1</u>', $text);
$text = preg_replace('/\[i](.*?)\[\/i]/si', '<i>$1</i>', $text);
$text = preg_replace('/\[s](.*?)\[\/s]/si', '<s>$1</s>', $text);
$text = preg_replace('/\[sp](.*?)\[\/sp]/si', '<sp>$1</sp>', $text);
$text = preg_replace('/\[sb](.*?)\[\/sb]/si', '<sb>$1</sb>', $text);

$text = preg_replace('/\[img width=(.*?),height=(.*?)](.*?)\[\/img]/si', '<img src="$3" width="$1" height=$2 />', $text);
$text = preg_replace('/\[img](.*?)\[\/img]/si', '<img src="$1"/>', $text);

$text = preg_replace('/\[video](.*?)\[\/video]/si', '<iframe width="560px" height="315px" src="http://www.yotbe.com/embed/$1" frameborder="0" allowfullscreen></iframe>', $text);
$text = preg_replace('/\[url=(.*?)](.*?)\[\/url]/si', '<a href="$1">$2</a>', $text);
$text = preg_replace('/\[list](.*?)\[\/list]/si', '<ul>$1</ul>', $text);
$text = preg_replace('/\[list=1](.*?)\[\/list]/si', '<ol>$1</ol>', $text);
$text = preg_replace('/\[*](.*?)\[\/*]/si', '<li>$1</li>', $text);

$text = preg_replace('/\[color=(.*?)](.*?)\[\/color]/si', '<font color="$1">$2</font>', $text);
$text = preg_replace('/\[font=(.*?)](.*?)\[\/font]/si', '<font family="$1">$2</font>', $text);

$text = preg_replace('/\[left](.*?)\[\/left]/si', '<p align="left">$1</p>', $text);
$text = preg_replace('/\[center](.*?)\[\/center]/si', '<p align="center">$1</p>', $text);
$text = preg_replace('/\[right](.*?)\[\/right]/si', '<p align="right">$1</p>', $text);
$text = preg_replace('/\[quote](.*?)\[\/quote]/si', '<blockquote>$1</blockquote>', $text);
$text = preg_replace('/\[code](.*?)\[\/code]/si', '<code>$1</code>', $text);

$text = preg_replace('/\[td](.*?)\[\/td]/i', '<td>$1</td>', $text);
$text = preg_replace('/\[tr](.*?)\[\/tr]/i', '<tr>$1</tr>', $text);

$text = preg_replace('/\[table](.*?)\[\/table]/si', '<table border="1" style="border-collapse: collapse;">$1</table>', $text);

$text = preg_replace('/\[size=50](.*?)\[\/size]/si', '<font size="1">$1</font>', $text);
$text = preg_replace('/\[size=85](.*?)\[\/size]/si',  '<font size="2">$1</font>', $text);
$text = preg_replace('/\[size=100](.*?)\[\/size]/si', '<font size="3">$1</font>', $text);
$text = preg_replace('/\[size=150](.*?)\[\/size]/si', '<font size="4">$1</font>', $text);
$text = preg_replace('/\[size=200](.*?)\[\/size]/si', '<font size="5">$1</font>', $text);


echo'

'.$text.'<br /><br />

';

$txt = "".$hlavni_slozka.$vysledek['ID_clanku']."/clanek_sparovani/sparovani.TXT";
$txt_data = file_get_contents($txt);

$txt_parametry = explode("%", $txt_data);

if($txt_parametry[0] != '')
{
if($txt_parametry[1] == "ano")
{

$galerie_folder = "".$galerie_slozka."fotky/fotky/".$txt_parametry[0]."/";

if (glob("$galerie_folder*.jpg") != false)
{
$filecount = count(glob("$galerie_folder*.jpg"));
$pocet = $filecount;

$i = 1;

while($i <= $pocet)
{
			echo"
			<a href=\"".$galerie_folder."".$i.".jpg\" data-lightbox=\"roadtrip\">
			<img class=\"obr\" src=\"".$galerie_folder."".$i.".jpg\" height=\"80px\" style=\"border: 1px #000 solid;\" border=\"0\" />
			</a>
			";
$i++;
}

}
}
else
{

$galerie_folder = "".$galerie_slozka."fotky/uvodni/".$txt_parametry[0].".jpg";

echo'
<center>
<div class="obal_fotogalerie" style="margin-top: 10px;float: none;"> 
<a href="index.php?page=galerie&id_galerie='.$txt_parametry[0].'&galerie='.$txt_parametry[2].'&info=Fotogalerie - '.$txt_parametry[4].' - '.$txt_parametry[2].'"><center><img class="imgpruhledny" onmouseover="this.className=\'imgcely\'" onmouseout="this.className=\'imgpruhledny\'" src="'.$galerie_folder.'" height="130px" border="0"/></center></a>
<a class="url_fotogalerie" href="index.php?page=galerie&id_galerie='.$txt_parametry[0].'&galerie='.$txt_parametry[2].'&info=Fotogalerie - '.$txt_parametry[4].' - '.$txt_parametry[2].'"><b>'.$txt_parametry[2].'</b><br />
<span class="popisek">'.$txt_parametry[3].'</span></a>
</div>
</center>
';

}
}


$folder = "".$hlavni_slozka.$vysledek['ID_clanku']."/files/clanek/";

$slozka = OpenDir($folder);
while ($soubor = ReadDir($slozka))
{ 
if ($soubor != "." && soubor != "..")
{

if(!is_dir($folder.$soubor))
{

$nazev = strtr($soubor,"-_","  ");

$pathinfo = pathinfo ($folder.$soubor, PATHINFO_EXTENSION);

$server = $_SERVER["SCRIPT_URI"];

$real_file = "/".ltrim($folder,"../").$soubor;
$file = str_replace("/","%2F",$real_file);

if($pathinfo == "docx")
{

$googleurl = 'https://docs.google.com/viewer?url=http%3A%2F%2F'.$_SERVER['HTTP_HOST'].$file;

echo'
<br /><div class="aktaulity_seznam_odkazu"><a class="aktaulity_link" href="'.$googleurl.'" target="_blank">'.$nazev.'</a></div>
';
}

else if($pathinfo == "doc")
{

$googleurl = 'https://docs.google.com/viewer?url=http%3A%2F%2F'.$_SERVER['HTTP_HOST'].$file;

echo'
<br /><div class="aktaulity_seznam_odkazu"><a class="aktaulity_link" href="'.$googleurl.'" target="_blank">'.$nazev.'</a></div>
';
}

else if($pathinfo == "pdf")
{

$googleurl = 'https://docs.google.com/viewer?url=http%3A%2F%2F'.$_SERVER['HTTP_HOST'].$file;

echo'
<br /><div class="aktaulity_seznam_odkazu"><a class="aktaulity_link" href="'.$googleurl.'" target="_blank">'.$nazev.'</a></div>
';
}

else if($pathinfo == "odt")
{

$googleurl = 'https://docs.google.com/viewer?url=http%3A%2F%2F'.$_SERVER['HTTP_HOST'].$file;

echo'
<br /><div class="aktaulity_seznam_odkazu"><a class="aktaulity_link" href="'.$googleurl.'" target="_blank">'.$nazev.'</a></div>
';
}

else if($pathinfo == "xls")
{

$googleurl = 'https://docs.google.com/viewer?url=http%3A%2F%2F'.$_SERVER['HTTP_HOST'].$file;

echo'
<br /><div class="aktaulity_seznam_odkazu"><a class="aktaulity_link" href="'.$googleurl.'" target="_blank">'.$nazev.'</a></div>
';
}

else
{
echo'
<br /><div class="aktaulity_seznam_odkazu"><a class="aktaulity_link" href="'.$real_file.'" target="_blank">'.$nazev.'</a></div>
';
}

}
}
}

echo'
<br /><br /><br />
';
?>