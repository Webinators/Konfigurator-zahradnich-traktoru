<?php
include('js_pro_pridavani_souboru.php');

$cestadb = $_POST['db'];
$tabulka = $_POST['table'];
$nazev_identifikatoru = $_POST['nazevid'];
$idclanku = $_POST['id'];
$hlavni_slozka = $_POST['slozka'];
$slozka_fotogalerie = $_POST['slozkasfotogalerii'];

$fullpath = $_SERVER["PHP_SELF"];

$pathparts = explode("/",$fullpath);
$wysiwyg_parts = explode("/", $hlavni_slozka);

unset($pathparts[count($pathparts)-1]);
unset($wysiwyg_parts[count($wysiwyg_parts)-1]);
unset($wysiwyg_parts[count($wysiwyg_parts)-1]);

$tecky = "";

for($i=1;$pathparts[$i];$i++)
{
$tecky .= "../"; 
}

$new_wysiwyg_parts = "";

for($i=0;$wysiwyg_parts[$i];$i++)
{
$new_wysiwyg_parts .= $wysiwyg_parts[$i]."/"; 
}

$hlavni_slozka = $tecky.$hlavni_slozka.'';
$real_slozka = $tecky.$new_wysiwyg_parts;
$slozka_fotogalerie = $tecky.$slozka_fotogalerie.'';

$i=1;

require("../../../".$cestadb."");

$query = "SELECT * FROM ".$tabulka." WHERE ".$nazev_identifikatoru." = ".$idclanku."" or die("Problém: " . mysqli_error($link));

$result = $link->query($query);
		
$vysledek = mysqli_fetch_array($result);

$popisek = strtr($vysledek['Popisek'],"|","\"");
$popisek = str_replace("<br />","", $popisek);

$text = strtr($vysledek['Text'],"|","\"");

$text = str_replace("<br />","", $text);

echo '<form id="addnew" method="post" action="" enctype="multipart/form-data">

<input type="hidden" name="id_novinky" value="'.$idclanku.'"/>
<input type="hidden" name="cestadb" value="'.$cestadb.'" />
<input type="hidden" name="tabulka" value="'.$tabulka.'" />
<input type="hidden" name="nazev_identifikatoru" value="'.$nazev_identifikatoru.'" />
<input type="hidden" name="hlavnislozka" value="'.$hlavni_slozka.'" />
';

echo' 
<div id="aktuality_podokno">
<div id="aktuality_lista_podokna">Nadpis:</div>
<div id="aktuality_obsahova_cast_podokna">
<input type="text" id="nadpis" name="nadpis" size="90" value="'.$vysledek['Nadpis'].'"/>
</div></div><br />   

<div id="aktuality_podokno">
<div id="aktuality_lista_podokna">Anotace:</div>
<div id="aktuality_obsahova_cast_podokna">
<div style="position: relative; background-color: #efefef;border-radius: 3px;-moz-border-radius: 3px;-webkit-border-radius: 3px;padding-top: 4px;">   

<a onclick="zobrazobrazky(\'obrazky\',\''.$i.'\',\'#file_popisek\')" style="position: absolute;top: 5px;left: 43%;z-index: 50;"><img src="apps/wysiwyg_textarea/ikonka/insertimage.png" width="20px" style="cursor: pointer;"/></a>

<center><textarea name="popisek" id="pole'.$i.'" class="rte-zone">'.$popisek.'</textarea></center>

<script type="text/javascript">
    $("#pole'.$i.'").rte("apps/wysiwyg_textarea/wysieasy/css/style.css", "apps/wysiwyg_textarea/wysieasy/icon/");
</script>

<style>
#pole'.$i.' {width: 98%;height: 150px;overflow: scroll;resize: none;border-top: 1px #b2b3b2 solid;padding: 3px 4px;}
</style>
</div>

<div id="priloha_popisek">

<input id="file_popisek" type="hidden" name="popisek_file" value=""/>
<input id="icka_popisek" type="hidden" name="popisek_icka" value=""/>

<center>
<div id="popisek_priloha">
';

$q = 1;

$folder = "".$hlavni_slozka.$idclanku."/popisek_pic/pic.TXT";

if(file_exists($folder))
{
$txt = file_get_contents($folder);

if($txt != '')
{
echo'
<div class="obal_fotka_popisek'.$q.'" id="obal_popisek_priloha"><div class="popisek_priloha_lista'.$q.'" id="popisek_priloha_lista">Příložená fotka: <a onclick="removepopisekfotka(\''.$q.'\',(\''.$folder.'\'))"><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" title="Odebrat fotku" alt="odebrat fotku" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div><div class="popisek_img'.$q.'" id="popisek_img"><input type="hidden" name="nazev_promenne" value="popisek"/><input class="popisek_fotka'.$q.'" type="hidden" name="popisek_fotka" value="'.$txt.'" />'.$txt.'</div></div>
';
}
}

$folder = "".$hlavni_slozka.$idclanku."/files/popisek/";

$q++;

$qecka = "";

$slozka = OpenDir($folder);
while ($soubor = ReadDir($slozka))
{ 
  if ($soubor != "." && soubor != "..")
 {

if(!is_dir($folder.$soubor))
{
$pathinfo = pathinfo ($folder.$soubor, PATHINFO_EXTENSION); 
$filesize = (filesize($folder.$soubor))/1000;
$filesize .= "kb";

if($pathinfo == "doc" || $pathinfo == "docx" || $pathinfo == "odt" || $pathinfo == "pdf" || $pathinfo == "rar" || $pathinfo == "xls" || $pathinfo == "zip")
{
$pathinfo = $pathinfo;
}
else
{
$pathinfo = "others";
}

echo'

<div class="obal_popisek_priloha'.$q.'" id="obal_popisek_priloha"><div class="popisek_priloha_lista'.$q.'" id="popisek_priloha_lista">Přiložený soubor:<a onclick="removepopisekprilohafile(\''.$q.'\',\''.$folder.$soubor.'\')"><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" title="Odebrat fotku" alt="odebrat fotku" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div>
<div class="popisek_img'.$q.'" id="popisek_img"><table><tr valign="top"><td rowspan="2"><img src="apps/wysiwyg_textarea/ikonka/pro_prilohy/'.$pathinfo.'.png" alt="'.$pathinfo.'" title="'.$pathinfo.'" width="70px"/></td><td>'.$soubor.'</td></tr><tr><td>'.$filesize.'</td></tr></table></div></div>

';

$q++;
}
}
}

pridanisouboru("popisek", "i", $q);

echo'

</div>
</center>

</div>

</div>
</div></div><br />
';  
$j=$i;
$i++; 
      

     echo '

<center><a class="aktuality_zobrazovaci_tlacitko" onclick="show(\'textarea'.$i.'\')">Editovat článek</a></center><br /> 

<br />
<div id="aktuality_podokno" class="textarea'.$i.'" style="display: none;">
<div id="aktuality_lista_podokna">Článek<a onclick="hide(\'textarea'.$i.'\')" ><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div>
<div id="aktuality_obsahova_cast_podokna">

<div style="position: relative;">
<a onclick="zobrazobrazky(\'obrazky\',\''.$i.'\',\'.wysibb-body\')" style="position: absolute;top: 9px;left: 400px;z-index: 50;"><img src="apps/wysiwyg_textarea/ikonka/insertimage.png" width="20px" style="cursor: pointer;"/></a>

<script type="text/javascript">
$(function() {
  $("#pole'.$i.'").wysibb();
});

var screenheight = $(window).height();

screenheight = (screenheight*60)/100;

$(".wysibb-body:last").css( "max-height", ""+screenheight+"px" );

</script>

<textarea name="textarea" id="pole'.$i.'">'.$text.'</textarea>
</div>

<div id="priloha_clanek">

<input id="file_clanek" type="hidden" name="clanek_file" value=""/>
<input id="icka_clanek" type="hidden" name="clanek_icka" value=""/>

<center>
<div id="clanek_priloha">

';

$folder = "".$hlavni_slozka.$idclanku."/clanek_sparovani/sparovani.TXT";
$txt = file_get_contents($folder);

$txt_parametry = explode("%", $txt);

if($txt_parametry[0] != '')
{
echo'
<div class="obal_pripojene_galerie" id="obal_popisek_priloha"><div id="popisek_priloha_lista">Připojená galerie: <a onclick="zrusit_sparovani_s_galerii(\'obal_pripojene_galerie\')"><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" title="Odebrat fotku" alt="odebrat fotku" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div><div class="pripojena_galerie_img" id="popisek_img"><center><img src="'.$tecky.$slozka_fotogalerie.'/fotky/uvodni/'.$txt_parametry[0].'.jpg" height="100px" border="0"/></center><a class="url_fotogalerie" href="#"><b>'.$txt_parametry[2].'</b><br /><span class="popisek">'.$txt_parametry[3].'</span></a><br />Zobrazit obsah galerie: 
';
if($txt_parametry[1] == "ano")
{
echo'
<input type="checkbox" name="zobrazit_obsah_galerie" value="ano" checked="checked"/>
';
}
else
{
echo'
<input type="checkbox" name="zobrazit_obsah_galerie" value="ano" />
';
}
echo '
<input type="hidden" name="id_pripojene_galerie" value="'.$txt_parametry[0].'"/>
<input type="hidden" name="nadpis_pripojene_galerie" value="'.$txt_parametry[2].'"/>
<input type="hidden" name="popis_pripojene_galerie" value="'.$txt_parametry[3].'"/>
<input type="hidden" name="nazev" value="'.$txt_parametry[4].'"/>

</div></div>';
}
else
{
echo'
<a class="sparovani_s_galerii" id="popisek_priloha_vlozit" onclick="nacti_spravce_galerie(\'sparovani_s_galerii\',\''.$slozka_fotogalerie.'\',\'\')"><div id="popisek_priloha_prilozit_soubor">spárovat s galerií</div></a>
';
}

$folder = "".$hlavni_slozka.$idclanku."/files/clanek/";

$q = 1;
$qecka = "";

$slozka = OpenDir($folder);
while ($soubor = ReadDir($slozka))
{ 
  if ($soubor != "." && soubor != "..")
 {

if(!is_dir($folder.$soubor))
{
$pathinfo = pathinfo ($folder.$soubor, PATHINFO_EXTENSION); 
$filesize = (filesize($folder.$soubor))/1000;
$filesize .= "kb";

if($pathinfo == "doc" || $pathinfo == "docx" || $pathinfo == "odt" || $pathinfo == "pdf" || $pathinfo == "rar" || $pathinfo == "xls" || $pathinfo == "zip")
{
$pathinfo = $pathinfo;
}
else
{
$pathinfo = "others";
}

echo'

<div class="obal_clanek_priloha'.$q.'" id="obal_popisek_priloha"><div class="clanek_priloha_lista'.$q.'" id="popisek_priloha_lista">Přiložený soubor:<a onclick="removeclanekprilohafile(\''.$q.'\',\''.$folder.$soubor.'\')"><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" title="Odebrat fotku" alt="odebrat fotku" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div>
<div class="clanek_img'.$q.'" id="popisek_img"><table><tr valign="top"><td rowspan="2"><img src="apps/wysiwyg_textarea/ikonka/pro_prilohy/'.$pathinfo.'.png" alt="'.$pathinfo.'" title="'.$pathinfo.'" width="70px"/></td><td>'.$soubor.'</td></tr><tr><td>'.$filesize.'</td></tr></table></div></div>

';

$q++;
}
}
}

pridanisouboru("clanek", "q", $q);

echo'

</div>
</center>

</div>

</div></div><br />

<center><input onclick="savenew()" type="submit" name="send" value="Uložit"/></center> <br />

</form>


<div class="okno" id="spravce_galerie">
<div style="position: relative;left: -150px;width: 100%;height: 100%;color: #000000;">
<a style="cursor: pointer;padding: 0px;" onclick="skryj_spravce_galerie(\'spravce_galerie\',\'data_spravce_galerie\')"><div id="editor_textarea_pruhledny_pozadi"></div></a>

<div id="spravce_galerie_obal">

<div class="listaeditorutextu"><a onclick="skryj_spravce_galerie(\'spravce_galerie\',\'data_spravce_galerie\')" ><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div>

<div id="data_spravce_galerie"></div>

</div></div></div>


<script type="text/javascript">

      function hide(id)
      {
      $(""+id+"").hide(100);
      }

      function show(id,skr,skr2)
      {
      $("#"+skr+"").hide(100);
      $("#"+skr2+"").hide(100);
      $("."+id+"").show(100);
      }

function renderfile(div,formid,load)
{

$(document).ready(function()
{

$("#"+formid+"").submit(function(e)
{
		$("#"+load+"").html("<img src=\'apps/wysiwyg_textarea/ikonka/loading.gif\' width=\'30px\' style=\'z-index: 10;background-color: #ffffff;\'/>");

	var formObj = $(this);

		var formData = new FormData(this);
		$.ajax({
        	url: \'apps/wysiwyg_textarea/load/renderuploadedfile.php\',
	       type: \'POST\',
		data:  formData,
		mimeType:"multipart/form-data",
		contentType: false,
    	       cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					$("#"+load+"").html(\'\');$("#"+div+"").hide(100);$("#hlavnitextovepole").show();$("#pic'.$i.'").html(data);
					
		    },
		  	error: function(jqXHR, textStatus, errorThrown) 
	    	{
				$("#"+load+"").html(\'<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus=\'+textStatus+\', errorThrown=\'+errorThrown+\'</code></pre>\');
	    	} 	        
	   });
        e.preventDefault();
        e.unbind();

});

});

}

$("input#docx").on(\'change\', function(){

var filename = $("#docx").val();

var explode = filename.split(\'.\');

if(explode[1] == "docx" || explode[1] == "doc")
{
$("#docxsubmit").show();
}
else
{
alert("vybral jste špatný typ souboru");
$("#docxsubmit").hide();
}

});

$("input#odt").on(\'change\', function(){

var filename = $("#odt").val();

var explode = filename.split(\'.\');

if(explode[1] == "odt")
{
$("#odtsubmit").show();
}
else
{
alert("vybral jste špatný typ souboru");
$("#odtsubmit").hide();
}

});


function savenew()
{

$("#addnew").submit(function(e)
{
	var formObj = $(this);

		var formData = new FormData(this);
		$.ajax({
        	url: \'apps/wysiwyg_textarea/load/upravanovinky.php\',
	       type: \'POST\',
		data:  formData,
              beforeSend: function(xhr, options){

              var nadpis = $(\'#nadpis\').val();
              var popis = $(\'#text'.$j.'\').val();

              if(nadpis == false)
              {
              alert("Prosím vyplňte nadpis");$("#nadpis").css("border","1px #ff0000 solid");return false; 
              }

              if(popis == "<div id=\"pic'.$j.'\"></div>")
              {
              alert("Prosím vyplňte anotaci");$("#text'.$j.'").css("background-color","red");return false; 
              }


              },

		mimeType:"multipart/form-data",
		contentType: false,
    	       cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					alert("novinka úspěšně upravena");$(\'#novinkavysl\').html(data);
					
		    },
		  	error: function(jqXHR, textStatus, errorThrown) 
	    	{
				alert("novinka nebyla úspěšně upravena");
	    	} 	        
	   });
        e.preventDefault();
        e.unbind();

});
}

function nacti_spravce_galerie(id, fotogalerie_cesta, id_hlavni_galerie, nazev)
{

$("#spravce_galerie").show(200);

$("#data_spravce_galerie").html(\'<center><img src="apps/wysiwyg_textarea/ikonka/loadingbig.gif" style="top: 40%;" width="200px" /></center>\');

$("#data_spravce_galerie").load(\'apps/wysiwyg_textarea/load/nacteni_spravce_galerie.php\',{id: \'\'+id+\'\', fotogalerie_cesta: \'\'+fotogalerie_cesta+\'\', id_hlavni_galerie: \'\'+id_hlavni_galerie+\'\', nazev: \'\'+nazev+\'\'},function(responseTxt,statusTxt,xhr){
    if(statusTxt=="success")
    if(statusTxt=="error")
      alert("Error: "+xhr.status+": "+xhr.statusText);
  });

}

function skryj_spravce_galerie(id, clear)
{

$("#"+id+"").hide(200);
$("#"+clear+"").html("");
}

function sparovat_s_novinkou(id, id_galerie, nadpis, popis, nazev)
{

$("#spravce_galerie").hide(200);
$("#spravce_galerie").html(\'\');
$("."+id+"").remove();

var onmouseover = "this.className=\'closeoknopo\'";

var onmouseout = "this.className=\'closeokno\'";

var onclick = "zrusit_sparovani_s_galerii(\'obal_pripojene_galerie\')";

var img = \'<a onclick="\'+onclick+\'"><img class="closeokno" onmouseover="\'+onmouseover+\'" onmouseout="\'+onmouseout+\'" title="Odebrat fotku" alt="odebrat fotku" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a>\';


$("#clanek_priloha").prepend(\'<div class="obal_pripojene_galerie" id="obal_popisek_priloha"><div id="popisek_priloha_lista">Připojená galerie: \' + img + \'</div><div class="pripojena_galerie_img" id="popisek_img"><center><img src="'.$tecky.$slozka_fotogalerie.'/fotky/uvodni/\'+id_galerie+\'.jpg" height="100px" border="0"/></center><a class="url_fotogalerie" href="#"><b>\'+nadpis+\'</b><br /><span class="popisek">\'+popis+\'</span></a><br />Zobrazit obsah galerie: <input type="hidden" name="id_pripojene_galerie" value="\'+id_galerie+\'"/> <input type="hidden" name="nadpis_pripojene_galerie" value="\'+nadpis+\'"/> <input type="hidden" name="popis_pripojene_galerie" value="\'+popis+\'"/> <input type="hidden" name="nazev" value="\'+nazev+\'"/> <input type="checkbox" name="zobrazit_obsah_galerie" value="ano" /></div></div>\');

}

function zrusit_sparovani_s_galerii(id)
{

$("."+id+"").load(\'apps/wysiwyg_textarea/load/vyprazdni_textak_u_sparovani_s_galerii.php\',{way: \''.$hlavni_slozka.''.$idclanku.'/clanek_sparovani/sparovani.TXT\'},function(responseTxt,statusTxt,xhr){
if(statusTxt=="success")
if(statusTxt=="error")
alert("Error: "+xhr.status+": "+xhr.statusText);
});

$("."+id+"").fadeOut(300, function(){ $(this).remove();});

var onclick = "nacti_spravce_galerie(\'sparovani_s_galerii\',\''.$slozka_fotogalerie.'\',\'\')"; 
$("#clanek_priloha").prepend(\'<a class="sparovani_s_galerii" id="popisek_priloha_vlozit" onclick="\'+onclick+\'"><div id="popisek_priloha_prilozit_soubor">spárovat s galerií</div></a>\');

}
</script>
';
?>