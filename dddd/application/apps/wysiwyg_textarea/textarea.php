<?php

use System\Authentication;

$user = Authentication\Users::initialize();

$i = 1;

include_once("apps/wysiwyg_textarea/load/bbreplace.php");

$checkdir = rtrim($slozka_na_ukladani_souboru);

if(!is_dir($checkdir))
{
    umask(0000);
    mkdir("$checkdir", 0775);
}

if($nedelatvewysiwyg_textarea == "")
{

    echo'
<link type="text/css" rel="stylesheet" href="apps/wysiwyg_textarea/css/jquery-te-1.4.0.css">
<link rel="stylesheet" href="apps/wysiwyg_textarea/css/editor.css" type="text/css" media="all" />
';

    if($user->userIsLogged())
    {

        echo'

<script type="text/javascript" src="apps/wysiwyg_textarea/wysieasy/js/jquery.rte.js"></script>

<script src="apps/wysiwyg_textarea/wysibb/jquery.wysibb.min.js"></script>
<link rel="stylesheet" href="apps/wysiwyg_textarea/wysibb/theme/default/wbbtheme.css" />
';
        if($showspravceobrazku == "true")
        {
            require('apps/wysiwyg_textarea/load/nacteni_spravce_obrazku.php');
        }
    }
}

if($user->userIsLogged())
{

    $pic = 'apps/wysiwyg_textarea/ikonka/loadingbig.gif';
    $imageData = base64_encode(file_get_contents($pic));
    $src = 'data: '.mime_content_type($image).';base64,'.$imageData;

    echo
        '

<div class="okno" id="editortextarea">
<div style="position: relative;width: 100%;height: 100%;color: #000000;">
<a style="cursor: pointer;padding: 0px;" onclick="skryjeditornovinek()"><div id="editor_textarea_pruhledny_pozadi"></div></a>

<div id="textareaeditorobal">

<div class="listaeditorutextu"><a onclick="skryjeditornovinek()" ><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div>

<div id="dataprotextareueditor"></div>

</div></div></div>

<script type="text/javascript">

function nactieditornovinek(id,db,table,nazevid,slozka,slozka_fotogalerie)
{
$("#editortextarea").show(100);

$("#dataprotextareueditor").html(\'<center><img src="'.$src.'" style="top: 40%;" width="200px" /></center>\');

$("#dataprotextareueditor").load(\'apps/wysiwyg_textarea/load/nacteni_editoru_novinek.php\',{id: \'\'+id+\'\', db: \'\'+db+\'\', table: \'\'+table+\'\', nazevid: \'\'+nazevid+\'\', slozka: \'\'+slozka+\'\', slozkasfotogalerii: \'\'+slozka_fotogalerie+\'\'},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
}

function nactieditorclanku(id,db,table,nazevid,slozka,atribut,slozka_fotogalerie)
{
$("#editortextarea").show(100);

$("#dataprotextareueditor").html(\'<center><img src="'.$src.'" style="top: 40%;" width="200px" /></center>\');

$("#dataprotextareueditor").load(\'apps/wysiwyg_textarea/load/nacteni_editoru_clanku.php\',{id: \'\'+id+\'\', db: \'\'+db+\'\', table: \'\'+table+\'\', nazevid: \'\'+nazevid+\'\', slozka: \'\'+slozka+\'\', atribut: \'\'+atribut+\'\', slozkasfotogalerii: \'\'+slozka_fotogalerie+\'\'},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });

}

function pridejnovinku(db,table,nazevid,slozka,slozka_fotogalerie)
{

$("#editortextarea").show(100);

$("#dataprotextareueditor").html(\'<center><img src="'.$src.'" style="top: 40%;" width="200px" /></center>\');

$("#dataprotextareueditor").load(\'apps/wysiwyg_textarea/load/nacteni_pridani_novinek.php\',{db: \'\'+db+\'\', table: \'\'+table+\'\', nazevid: \'\'+nazevid+\'\', slozka: \'\'+slozka+\'\', slozkasfotogalerii: \'\'+slozka_fotogalerie+\'\'},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });
}

function skryjeditornovinek()
{
$("#dataprotextareueditor").html(\'\');
$("#editortextarea").hide(100);
}

</script>
';
}

if($typclanku == "aktualita")
{

    $divoriginalid = 1;

    echo '
<div style="display: inline-block;position: relative;">

<div id="AktualitySlideBtnContainer">
<a onclick="prevAktualita()" class="AktualitySlideBtn" id="AktualitySlideBtnL"><img src="img/control_left2.png" title="předchozí novinky" alt="předchozí novinky"/></a>
<a onclick="nextAktualita()" class="AktualitySlideBtn" id="AktualitySlideBtnR"><img src="img/control_right2.png" title="následující novinky" alt="následující novinky"/></a>
</div>

<div id="obal_aktualit'.$divoriginalid.'" class="obal_aktualit">
';

    $divoriginalid += 1;

    $q = 1;

    if($user->userIsLogged())
    {
        echo'

<div class="krajnidivaktualit" id="krajnidivaktualit">
<div id="nadpisproaktuality">Přidání novinky</div><br />
<div class="obsahovacastaktualit">
<a id="editaktuality" onmouseover="this.idName=\'editaktualitypo\'" onmouseout="this.idName=\'editaktuality\'" style="float: left;" onclick="pridejnovinku(\''.$cestadb.'\',\''.$tabulka.'\',\''.$nazev_identifikatoru.'\',\''.$slozka_na_ukladani_souboru.'\',\''.$slozka_s_fotogaleri.'\')">Přidat</a>
</div>
</div>
';

        $limit = 3;

        $q++;
    }
    else
    {
        $limit = 4;
    }

    require($cestadb);

    $IDD = 1;

    $query = "SELECT * FROM ".$tabulka." ORDER BY ".$nazev_identifikatoru." DESC LIMIT ".$limit."" or die("Problém: " . mysqli_error($link));

    $result = $link->query($query);

    while($vysledek = mysqli_fetch_array($result)){

        $nadpis = stripslashes($vysledek['Nadpis']);

        $text = stripslashes($vysledek['Popisek']);

        $text = strtr($text,"|","\"");

        $text = bbreplace($text);

        if($user->userIsLogged())
        {

            $url = '<a id="editaktuality" onmouseover="this.idName=\'editaktualitypo\'" onmouseout="this.idName=\'editaktuality\'" onclick="nactieditornovinek(\''.$vysledek['ID_clanku'].'\',\''.$cestadb.'\',\''.$tabulka.'\',\''.$nazev_identifikatoru.'\',\''.$slozka_na_ukladani_souboru.'\',\''.$slozka_s_fotogaleri.'\')">edit</a>';

        } else {
            $url = '';
        }

        if($vysledek['Text'] != '')
        {
            $url2 = '<div id="odkaz_aktualit" onmouseover="this.idName=\'odkaz_aktualitpo\'" onmouseout="this.idName=\'odkaz_aktualit\'" onclick="nacistclanek(\''.$vysledek['ID_clanku'].'\',\''.$slozka_na_ukladani_souboru.'\',\''.$slozka_s_fotogaleri.'\')">Zobrazit článek</div>';
        }
        else
        {
            $url2 = '';
        }

        echo'
<div class="krajnidivaktualit" id="krajnidivaktualit">
<div id="nadpisproaktuality">'.$nadpis.'</div><br />
<div id="obsahovacastaktualit'.$IDD.'" class="obsahovacastaktualit">
'.$text.'
';

        $folder = "".$slozka_na_ukladani_souboru.$vysledek['ID_clanku']."/popisek_pic/pic.TXT";

        if(file_exists($folder))
        {
            $txt = file_get_contents($folder);

            echo $txt;
        }

        $folder = "".$slozka_na_ukladani_souboru.$vysledek['ID_clanku']."/files/popisek/";

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

                    $real_file = "/".$folder.$soubor;
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

        $explode = explode("-",$vysledek['Datum_pridani']);
        $vysledek['Datum_pridani'] = $explode[2].".".$explode[1].".".$explode[0];

        $explode = explode(":",$vysledek['Cas']);
        $vysledek['Cas'] = $explode[0].":".$explode[1];

        unset($explode);


        echo'
</div>
<br />Přidáno dne: '.$vysledek['Datum_pridani'].' '.$vysledek['Cas'].', <br /> autor: '.$user->getUserFromId($vysledek['Autor']).'<br />

'.$url2.' '.$url.'

</div>
';

        $url = '';
        $q++;


        $lastid = $vysledek['ID_clanku'];
        $IDD++;
    }

    echo '</div>';

    for($po = 1; $po <= 4;$po++)
    {

        echo '
<div id="obal_aktualit'.$divoriginalid.'" class="obal_aktualit">';

        $divoriginalid += 1;

        $query = "SELECT * FROM ".$tabulka."  WHERE ".$nazev_identifikatoru." < ".$lastid." ORDER BY ".$nazev_identifikatoru." DESC LIMIT 4" or die("Problém: " . mysqli_error($link));

        $result = $link->query($query);

        for($w=1;$w <= 4; $w++){

            $vysledek = mysqli_fetch_array($result);

            $nadpis = stripslashes($vysledek['Nadpis']);

            $text = stripslashes($vysledek['Popisek']);

            $text = strtr($text,"|","\"");

            $text = bbreplace($text);

            if($user->userIsLogged())
            {

                $url = '<a id="editaktuality" onmouseover="this.idName=\'editaktualitypo\'" onmouseout="this.idName=\'editaktuality\'" onclick="nactieditornovinek(\''.$vysledek['ID_clanku'].'\',\''.$cestadb.'\',\''.$tabulka.'\',\''.$nazev_identifikatoru.'\',\''.$slozka_na_ukladani_souboru.'\',\''.$slozka_s_fotogaleri.'\')">edit</a>';

            } else {

                $url = "";

            }

            if($vysledek['Text'] != '')
            {
                $url2 = '<div id="odkaz_aktualit" onmouseover="this.idName=\'odkaz_aktualitpo\'" onmouseout="this.idName=\'odkaz_aktualit\'" onclick="nacistclanek(\''.$vysledek['ID_clanku'].'\',\''.$slozka_na_ukladani_souboru.'\',\''.$slozka_s_fotogaleri.'\')">Zobrazit článek</div>';
            }
            else
            {
                $url2 = '';
            }

            echo'
<div class="krajnidivaktualit" id="krajnidivaktualit">
<div id="nadpisproaktuality">'.$nadpis.'</div><br />
<div id="obsahovacastaktualit'.$IDD.'" class="obsahovacastaktualit">
'.$text.'
';

            $folder = "".$slozka_na_ukladani_souboru.$vysledek['ID_clanku']."/popisek_pic/pic.TXT";

            if(file_exists($folder))
            {
                $txt = file_get_contents($folder);

                echo $txt;
            }

            $folder = "".$slozka_na_ukladani_souboru.$vysledek['ID_clanku']."/files/popisek/";

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

                        $real_file = "/".$folder.$soubor;
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

            $explode = explode("-",$vysledek['Datum_pridani']);
            $vysledek['Datum_pridani'] = $explode[2].".".$explode[1].".".$explode[0];

            $explode = explode(":",$vysledek['Cas']);
            $vysledek['Cas'] = $explode[0].":".$explode[1];

            unset($explode);

            echo'
</div>
<br />Přidáno dne: '.$vysledek['Datum_pridani'].' '.$vysledek['Cas'].', <br /> autor: '.$user->getUserFromId($vysledek['Autor']).'<br />

'.$url2.' '.$url.'

</div>
';

            $url = '';
            $q++;

            $lastid = $vysledek['ID_clanku'];
            $IDD++;
        }

        echo '</div>';

    }

    echo
        '

<script type="text/javascript">

var NumberofDivs = 4;
var Divcursor = 1;

$(document).ready(function(){
 $("#obal_aktualit"+Divcursor+"").show();

$(document).find(".obal_aktualit").not(":first").each(function(){  $(this).hide();	})

});

function prevAktualita()
{
  if(Divcursor > 1)
  {
   $("AktualitySlideBtnR").show();
   $("#obal_aktualit"+Divcursor+"").toggle( "slide" );
   Divcursor = Divcursor - 1;
   $("#obal_aktualit"+Divcursor+"").toggle( "slide" );

   if(Divcursor == 1)
   {
      $("#AktualitySlideBtnL").hide();
      $("#AktualitySlideBtnR").show();
   }

  } else {
   $("#AktualitySlideBtnL").hide();
  }
}

function nextAktualita()
{

  if(Divcursor < 4)
  {
  $("#AktualitySlideBtnL").show();

   $("#obal_aktualit"+Divcursor+"").toggle( "slide" );
   Divcursor = Divcursor + 1;
   $("#obal_aktualit"+Divcursor+"").toggle( "slide" );

   if(Divcursor == 4)
   {
      $("#AktualitySlideBtnR").hide();
   }
  } else {
   $("#AktualitySlideBtnR").hide();
   $("#AktualitySlideBtnL").show();
  }
}

function  nacistclanek (id, slozka, galerie_slozka)
{

sendData({

    data: {id:id,slozka: slozka, galerie_slozka: galerie_slozka},
    url: "apps/wysiwyg_textarea/load/showmessage.php",
    progress: "window",
    method: "POST"

}, function (data) {

    if(data != false) {

        mainWindow.normal({
            
            content: data,
            center: true,
            
        });
        
    }

});

}

function skrytclanek()
{
$("#oknovypsaniaktuality").hide(300);$("#newsdata").html(\'\');
}
</script>

</center>

<div class="oknovypsaniaktuality" id="oknovypsaniaktuality">
<div style="position: fixed;_position: relative;top: 0px;left: 0px;width: 100%;height: 100%;z-index: 99999999999999;color: #000000;">
<a style="cursor: pointer;padding: 0px;" onclick="skrytclanek()"><div id="editor_textarea_pruhledny_pozadi"></div></a>

<div id="aktuality_clanek_obsah">

<div class="listaeditorutextu"><a onclick="skrytclanek()" ><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div>

<div id="newsdata"></div>

</div></div></div></center>
';

}

if($typclanku == "text")
{

    if($user->userIsLogged())
    {
        echo '<div class="tlacitkaaktuality"><a id="editaktuality" onmouseover="this.idName=\'editaktualitypo\'" onmouseout="this.idName=\'editaktuality\'" onclick="nactieditorclanku(\''.$idclanku.'\',\''.$cestadb.'\',\''.$tabulka.'\',\''.$nazev_identifikatoru.'\',\''.$slozka_na_ukladani_souboru.'\',\''.$nazev_atributu_pro_ulozeni_textu_v_tabulce.'\',\''.$slozka_s_fotogaleri.'\')">editovat článek</a></div>';
    }

    $url = "";

    require($cestadb);

    $query = "SELECT * FROM ".$tabulka." WHERE ".$nazev_identifikatoru."=".$idclanku."" or die("Problém: " . mysqli_error($link));

    $result = $link->query($query);

    $vysledek = mysqli_fetch_array($result);

    $text = stripslashes($vysledek[$nazev_atributu_pro_ulozeni_textu_v_tabulce]);

    $text = strtr($text,"|","\"");

    $text = bbreplace($text);

    echo $text."<br /><br />";

    $fullpath = $_SERVER["PHP_SELF"];

    $pathparts = explode("/",$fullpath);

    unset($pathparts[count($pathparts)-1]);

    $tecky = "";

    for($i=1;$pathparts[$i];$i++)
    {
        $tecky .= "../";
    }

    $hlavni_slozka = $tecky.$slozka_na_ukladani_souboru.'';
    $galerie_slozka = $tecky.$slozka_s_fotogaleri;

    $txt = "".$hlavni_slozka.$vysledek[$nazev_identifikatoru]."/clanek_sparovani/sparovani.TXT";
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
			<a href=\"".$galerie_folder."".$i.".jpg\"><img class=\"obr\" src=\"".$galerie_folder."".$i.".jpg\" height=\"120px\" style=\"border: 1px #000 solid;\" border=\"0px\" /></a>
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


    $folder = "".$hlavni_slozka.$vysledek[$nazev_identifikatoru]."/files/clanek/";

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

                $real_file = "/".$folder.$soubor;
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



}

$nedelatvewysiwyg_textarea = "ano";
?>