<?php
$i = 1;
session_start();

$idarey = $_POST['id'];
$celeid = $_POST['id_textarey'];

$_SESSION['id_textarey'] = $celeid;

$idhlavnitextarey = $_SESSION['id_textarey'];

$pokracovani = $_SESSION['pokracovani'];

$explode = explode("/",$pokracovani);

echo '<div class="navigation"><a style="cursor: pointer;" class="linknavigation" onmouseover="this.className=\'linknavigationpo\'" onmouseout="this.className=\'linknavigation\'" onclick="movetofolder(\'\')">obrazky</a>';

for($i=0;$explode[$i];$i++)
{

if($link != '')
{
$link = $link."/".$explode[$i];
}
else
{
$link = $explode[$i];
}

echo ' <img src="apps/wysiwyg_textarea/ikonka/sipka.png"/> <a style="cursor: pointer;" class="linknavigation" onmouseover="this.className=\'linknavigationpo\'" onmouseout="this.className=\'linknavigation\'" onclick="movetofolder(\''.$link.'\')">'.$explode[$i].'</a>';
}

echo '</div>';

if($pokracovani != '')
{
$cestakdir = "/".$pokracovani;
$cestakpolozkam = $pokracovani."/";
}
else
{
$cestakdir = "";
}

if($pokracovani != '')
{
$cestakobr = "/".$pokracovani."/";
}
else
{
$cestakobr = "/";
}

$adresar = opendir("../obrazky$cestakdir");
while($soubor = readdir($adresar))
{

if(is_dir("../obrazky/".$cestakpolozkam.$soubor.""))
{
if($soubor != "." AND $soubor != "..")
{
echo'
<div class="seznamsouboru" id="'.$i.'" onmousedown="zobrazpravymkliknutim(\''.$i.'\',\'pravyklikmenu'.$i.'\',\''.$soubor.'\')" ondblclick="zobrazobsahslozky(\''.$soubor.'\')" onmouseover="nastavstyldivu(\''.$i.'\')" onmouseout="zrusstyldivu(\''.$i.'\')"> <a style="cursor: pointer;"><img src="apps/wysiwyg_textarea/ikonka/folder.png" height="60px"/></a><div class="nazevobr"><div id="foldername'.$i.'"><a onclick="zobrazobsahslozky(\''.$soubor.'\')" style="cursor: pointer;">'.$soubor.'</a></div></div></div>

<script>
document.getElementById(\''.$i.'\').onclick = function() {

zvyraznidiv("'.$i.'","'.$soubor.'");

}
 
</script>

<div id="pravyklikmenu'.$i.'" class="pravyklikmenu">
   <ol>
      <li><a onclick="renamefolder(\'foldername'.$i.'\',\''.$soubor.'\',\''.$i.'\')">Rename</a> </li>
      <li><a id="deletefolder'.$i.'" onclick="deletefolder(\''.$i.'\',\''.$soubor.'\')">Delete</a> </li>
   </ol>
</div>
';

$i++;
}
}

}

$n = 0;
$c = 1;

$adresar = opendir("../obrazky$cestakdir");

$data = "../obrazky/nazev.txt";
$load = file_get_contents($data);

$search = explode(",",$load);

while($soubor = readdir($adresar))
{
if(!is_dir("../obrazky/".$cestakpolozkam.$soubor.""))
{

$pripona = pathinfo($soubor, PATHINFO_EXTENSION);

if($pripona == "jpg" OR $pripona == "JPG" OR $pripona == "png" OR $pripona == "gif")
{

echo'
                         
<div class="seznamsouboru" id="'.$i.'" onmousedown="zobrazpravymkliknutim(\''.$i.'\',\'pravyklikmenu'.$i.'\',\''.$soubor.'\')" onmouseover="nastavstyldivu(\''.$i.'\')" onmouseout="zrusstyldivu(\''.$i.'\')"><a style="cursor: pointer;"><center><img src="apps/wysiwyg_textarea/obrazky'.$cestakobr.''.$soubor.'" height="60px"/></center></a><a id="insert'.$i.'" class="insert" style="display: none;" onclick="vlozobrdotextarey(\''.$idhlavnitextarey.'\',\'apps/wysiwyg_textarea/obrazky'.$cestakobr.''.$soubor.'\',\''.$idarey.'\')">vložit</a><div class="nazevobr"><div id="filename'.$i.'"><a style="cursor: pointer;">'.$soubor.'</a></div></div></div>
    
<script>
document.getElementById(\''.$i.'\').onclick = function() {

zvyraznisoubor("'.$i.'","'.$soubor.'");

}
 
</script>
                        
<div id="pravyklikmenu'.$i.'" class="pravyklikmenu">
   <ol>
      <li><a onclick="renamefile(\'filename'.$i.'\',\''.$soubor.'\',\''.$i.'\')">Rename</a> </li>
      <li><a id="deletefile'.$i.'" onclick="deletefile(\''.$i.'\',\''.$soubor.'\')">Delete</a> </li>
   </ol>
</div>
			   ';
$i++;$n++;$c++;
}
}
}


?>