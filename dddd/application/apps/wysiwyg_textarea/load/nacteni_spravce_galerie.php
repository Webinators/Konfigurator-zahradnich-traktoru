<?php

$fotogalerie_cesta = $_POST['fotogalerie_cesta'];
$id = $_POST['id'];
$id_hlavni_galerie = $_POST['id_hlavni_galerie'];
$nazev = $_POST['nazev'];

$fullpath = $_SERVER["PHP_SELF"];

$pathparts = explode("/",$fullpath);

unset($pathparts[count($pathparts)-1]);

$tecky = "";

for($i=1;$pathparts[$i];$i++)
{
$tecky .= "../"; 
}

echo 
'
<link rel="stylesheet" href="'.$fotogalerie_cesta.'css/galerie.css" type="text/css" media="all" />
';

require ("".$fotogalerie_cesta."/db_galerie.php");

if($id_hlavni_galerie != '')
{

$galerie = "SELECT * FROM ".$tabulka." WHERE ".$hlidentifikator."=".$id_hlavni_galerie." ORDER BY ".$podlecehoradit." ".$razeni."" or die("Problém: " . mysqli_error($link));  

$result = $link->query($galerie);

$pocet = mysqli_num_rows($result);

echo '<center><a class="sparovani_s_galerii" style="background-color: #000000; color: #ffffff; font-weight: bold; padding: 3px 5px;" id="popisek_priloha_vlozit" onclick="nacti_spravce_galerie(\''.$id.'\',\''.$fotogalerie_cesta.'\',\'\')">Zpět</a></center><br />';

if($pocet == 0)
{
echo 
"
<div style=\"background-color: #2e2e2e;padding: 10px 15px;font-size: 19px;color: #ffffff;\">
Ještě zde nejsou vložené žádné galerie.
</div>
<br />
";
}
else
{

$i = 1;
while($Vysledek = mysqli_fetch_array($result))
{

if($i > 3)
{
$style = "margin-top: -44px;";
}
else
{
$style = "";
}
			
			$explode = explode("-",$Vysledek['Datum_pridani']);
			$Vysledek['Datum_pridani'] = $explode[2].".".$explode[1].".".$explode[0];	
			echo'
			<div class="obal_fotogalerie" style="'.$style.'"> 
			<a onclick="sparovat_s_novinkou(\''.$id.'\',\''.$Vysledek[$identifikator].'\',\''.$Vysledek['Nadpis'].'\',\''.$Vysledek['Popis'].'\', \''.$nazev.'\')"><center><img class="imgpruhledny" onmouseover="this.className=\'imgcely\'" onmouseout="this.className=\'imgpruhledny\'" src="'.$tecky.$Vysledek['Miniatura'].'" height="100px" border="0"/></center></a>
			<a class="url_fotogalerie" style="top: 80px;" href="#" onclick="sparovat_s_novinkou(\''.$id.'\',\''.$Vysledek[$identifikator].'\',\''.$Vysledek['Nadpis'].'\',\''.$nazev.'\')"><b>'.$Vysledek['Nadpis'].'</b><br />
			<span class="popisek">'.$Vysledek['Popis'].'</span></a>
			<span class="pridano">'.$Vysledek['Datum_pridani'].'</span>
			</div>';
$i++;
}

}

}
else
{

$rok = "SELECT * FROM ".$hltabulka." ORDER BY ".$hlpodlecehoradit." ".$hlrazeni."" or die("Problém: " . mysqli_error($link));  

$result = $link->query($rok);

$pocet = mysqli_num_rows($result);

if($pocet == 0)
{
echo 
"
<div style=\"background-color: #2e2e2e;padding: 10px 15px;font-size: 19px;color: #ffffff;\">
V galerii ještě nejsou vložené žádné fotky.
</div>
<br />
";
}
else
{

while($Vysledek = mysqli_fetch_array($result))
{
			echo'
			<div class="obal_fotogalerie" margin-top: -19px;>
			<a onclick="nacti_spravce_galerie(\''.$id.'\',\''.$fotogalerie_cesta.'\',\''.$Vysledek[$hlidentifikator].'\', \''.$Vysledek['Rok'].'\')"><img class="imgpruhledny" onmouseover="this.className=\'imgcely\'" onmouseout="this.className=\'imgpruhledny\'" src="'.$tecky.$Vysledek['Miniatura'].'" height="100px" border="0"/></a>
	              <a class="url_hlavni_galerie" style="font-weight: bold;" href="#" onclick="nacti_spravce_galerie(\''.$id.'\',\''.$fotogalerie_cesta.'\',\''.$Vysledek[$hlidentifikator].'\')"><b>'.$Vysledek['Rok'].'</b></a>		
			</div>';
}

}
}
?>
<br style="clear: both" />