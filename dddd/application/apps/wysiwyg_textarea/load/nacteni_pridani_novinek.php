<?php
include('js_pro_pridavani_souboru.php');

$cestadb = $_POST['db'];
$tabulka = $_POST['table'];
$nazev_identifikatoru = $_POST['nazevid'];
$hlavni_slozka = $_POST['slozka'];

$fullpath = $_SERVER["PHP_SELF"];

$pathparts = explode("/",$fullpath);

unset($pathparts[count($pathparts)-1]);

$tecky = "";

for($i=1;$pathparts[$i];$i++)
{
$tecky .= "../"; 
}

$hlavni_slozka = $tecky.$hlavni_slozka.'';

echo $hlavni_slozka;

$i=1;

require("".$tecky.$cestadb."");

echo '<form id="addnew" method="post" action="" enctype="multipart/form-data">

<input type="hidden" name="cestadb" value="'.$cestadb.'" />
<input type="hidden" name="tabulka" value="'.$tabulka.'" />
<input type="hidden" name="nazev_identifikatoru" value="'.$nazev_identifikatoru.'" />
<input type="hidden" name="hlavnislozka" value="'.$hlavni_slozka.'" />
';

echo'
<div id="aktuality_podokno">
<div id="aktuality_lista_podokna">Nadpis:</div>
<div id="aktuality_obsahova_cast_podokna">
<input type="text" id="nadpis" name="nadpis" size="90"/>
</div>
</div><br />

<div id="aktuality_podokno">
<div id="aktuality_lista_podokna">Anotace:</div>
<div id="aktuality_obsahova_cast_podokna">
<div style="position: relative; background-color: #efefef;border-radius: 3px;-moz-border-radius: 3px;-webkit-border-radius: 3px;padding-top: 4px;">   

<a onclick="zobrazobrazky(\'obrazky\',\''.$i.'\',\'#file_popisek\')" style="position: absolute;top: 5px;left: 43%;z-index: 50;"><img src="apps/wysiwyg_textarea/ikonka/insertimage.png" width="20px" style="cursor: pointer;"/></a>

<center><textarea name="popisek" id="pole'.$i.'" class="rte-zone"></textarea></center>

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

pridanisouboru("popisek", "i", 1);

echo'
</div>
</center>

</div>

';

echo'

<br />


</div>
</div></div><br />

      ';  
      $j=$i;
      $i++; 
      

     echo '

<center><a id="napsat_clanek" class="aktuality_zobrazovaci_tlacitko" onclick="showtextarea(\'textarea'.$i.'\')">Napsat článek</a>&nbsp;&nbsp;&nbsp;</center><br /> 

<br />

<script>
$(function() {
  $("#pole'.$i.'").wysibb();
});
</script>

<div class="textarea'.$i.'" style="display: none;" id="aktuality_podokno">
<div id="aktuality_lista_podokna">Článek<a onclick="hidetextarea(\'textarea'.$i.'\')" ><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div>
<div id="aktuality_obsahova_cast_podokna">
<div style="position: relative;">
<a onclick="zobrazobrazky(\'obrazky\',\''.$i.'\',\'.wysibb-body\')" style="position: absolute;top: 9px;left: 400px;z-index: 50;"><img src="apps/wysiwyg_textarea/ikonka/insertimage.png" width="20px" style="cursor: pointer;"/></a>
<textarea name="textarea" id="pole'.$i.'" style="height: 250px;"></textarea>
</div>

<center>
<div id="clanek_priloha">

<input id="icka_clanek" type="hidden" name="clanek_icka" value=""/>

';

pridanisouboru("clanek", "q", 1);

echo'

</div>
</center>

</div></div><br />

<center><input class="submitarea" onmouseover="this.className=\'submitareapo\'" onmouseout="this.className=\'submitarea\'" onclick="pridatnovinku()" type="submit" name="send" value="Přidat novinku"/></center> <br />

</form>
';

echo'
<div class="okno" id="uploaddocx">
<div style="position: fixed;_position: relative;top: 0px;left: 0px;width: 100%;height: 100%;z-index: 99999999999999;color: #000000;">
<a style="cursor: pointer;padding: 0px;" onclick="hide(\'uploaddocx\')"><div class="pruhledny_pozadi_okynko"></div></a>
 
<div id="obrazky_vlozeni" style="background-color: transparent;top: 40%;">

<form id="docxform" method="post" action="" enctype="multipart/form-data">

<center><div id="uploaddocx" style="width: 250px;" class="pridaniaktuality"><div class="labelaktuality">Nahrání docx<a onclick="hide(\'uploaddocx\')" ><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div><div class="popisekaktuality">

<center><input type="file" id="docx" name="docx" class="filestyle" data-input="false" data-buttonText="Nový soubor"/></center>

<input style="display: none;" type="submit" id="docxsubmit" value="nahrát" onclick="renderfile(\'uploaddocx\',\'docxform\',\'renderingdocxprocess\')"/>

</form>

<div id="renderingdocxprocess"></div>

</div></div></center>
</div>

</div></div>


<div class="okno" id="uploadodt">
<div style="position: fixed;_position: relative;top: 0px;left: 0px;width: 100%;height: 100%;z-index: 99999999999999;color: #000000;">
<a style="cursor: pointer;padding: 0px;" onclick="hide(\'uploadodt\')"><div class="pruhledny_pozadi_okynko"></div></a>
 
<div id="obrazky_vlozeni" style="background-color: transparent;top: 40%;">

<form id="odtform" method="post" action="" enctype="multipart/form-data">

<center><div style="width: 200px;" class="pridaniaktuality"><div class="labelaktuality">Načtení odt<a onclick="hide(\'uploadodt\')" ><img class="closeokno" onmouseover="this.className=\'closeoknopo\'" onmouseout="this.className=\'closeokno\'" src="apps/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div><div class="popisekaktuality">

<center><input type="file" id="odt" name="odt" class="filestyle" data-input="false" data-buttonText="Nový soubor"/></center>

<input style="display: none;" type="submit" id="odtsubmit" value="nahrát" onclick="renderfile(\'uploadodt\',\'odtform\',\'renderingodtprocess\')"/>

</form>

<div id="renderingodtprocess"></div>

</div></div></center>

</div></div></div>


<script type="text/javascript">


function showfileinput(id)
{

$("#"+id+"").show(100);

}

      function hide(id)
      {
      $("#"+id+"").hide(100);
      }

      function show(id,skr,skr2)
      {
      $("#"+skr+"").hide(100);
      $("#"+skr2+"").hide(100);
      $("#"+id+"").show(100);
      }

      function showtextarea(id)
      {
      $("."+id+"").show(100);
      }

      function hidetextarea(id)
      {
      $("."+id+"").hide(100);
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
					$("#"+load+"").html(\'\');$("#"+div+"").hide(100);$("#textarea'.$i.'").show();$(".wysibb-body:last").html(data);
					
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

function pridatnovinku()
{
$(document).ready(function()
{

$("#addnew").submit(function(e)
{
	var formObj = $(this);

		var formData = new FormData(this);
		$.ajax({
        	url: \'apps/wysiwyg_textarea/load/pridaninovinky.php\',
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
					alert("Novinka úspěšně přidána");$(\'#novinkavysl\').html(data);
					
		    },
		  	error: function(jqXHR, textStatus, errorThrown) 
	    	{
				alert("novinka nebyla úspěšně přidána");
	    	} 	        
	   });
        e.preventDefault();
        e.unbind();

});

});
}

</script>
      ';  

?>