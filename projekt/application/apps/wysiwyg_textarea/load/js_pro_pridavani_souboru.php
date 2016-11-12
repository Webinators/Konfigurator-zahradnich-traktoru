<?php

function pridanisouboru($idecko,$promenna,$i)
{

echo '<a class="'.$idecko.'_priloha_vlozit'.$i.'" id="popisek_priloha_vlozit" onclick="'.$idecko.'_priloha_vlozit_input(\''.$idecko.'_priloha_prilozit_soubor'.$i.'\',\''.$i.'\')"><div class="'.$idecko.'_priloha_prilozit_soubor'.$i.'" id="popisek_priloha_prilozit_soubor">Přiložit soubor</div></a>';

$i++;

$code = '

<div id="del'.$idecko.'"></div>

<script type="text/javascript">

var '.$promenna.' = '.$i.';

$("#file_'.$idecko.'").change(function()
{
var inputval = $("#file_'.$idecko.'").val();

$("#file_'.$idecko.'").val("");

var onmouseover = "this.className=\'closeoknopo\'";

var onmouseout = "this.className=\'closeokno\'";

var onclick = "remove'.$idecko.'fotka(\'"+'.$promenna.'+"\')";

var img = \'<a onclick="\'+onclick+\'"><img class="closeokno" onmouseover="\'+onmouseover+\'" onmouseout="\'+onmouseout+\'" title="Odebrat fotku" alt="odebrat fotku" src="logged/wysiwyg_textarea/ikonka/close.png" width="20px"/></a>\';

$("#'.$idecko.'_priloha").append(\'<div class="obal_fotka_'.$idecko.'\'+'.$promenna.'+\'" id="obal_popisek_priloha"><div class="'.$idecko.'_priloha_lista\'+'.$promenna.'+\'" id="popisek_priloha_lista">Příložená fotka: \' + img + \'</div><div class="'.$idecko.'_img\'+'.$promenna.'+\'" id="popisek_img"><input type="hidden" name="nazev_promenne" value="'.$idecko.'"/><input class="'.$idecko.'_fotka\'+'.$promenna.'+\'" type="hidden" name="'.$idecko.'_fotka" value="\'+inputval+\'" />\'+inputval+\'</div></div>\');

'.$promenna.'++;
});

function remove'.$idecko.'fotka(id, pathtofile)
{

if(pathtofile != \'\')
{
$("#del'.$idecko.'").load(\'logged/wysiwyg_textarea/load/vyprazdni_textak_u_popisku.php\',{way: \'\'+pathtofile+\'\'},function(responseTxt,statusTxt,xhr){
if(statusTxt=="success")
if(statusTxt=="error")
alert("Error: "+xhr.status+": "+xhr.statusText);
});
}

$(".'.$idecko.'_fotka"+id+"").val("");
$("#file_'.$idecko.'").val("");

$(".obal_fotka_'.$idecko.'"+id+"").fadeOut(300, function(){ $(this).remove();});
}

function remove'.$idecko.'prilohafile(id, pathtofile)
{

if(pathtofile != \'\')
{
$("#del'.$idecko.'").load(\'logged/wysiwyg_textarea/load/delete_file_u_textaku.php\',{way: \'\'+pathtofile+\'\'},function(responseTxt,statusTxt,xhr){
if(statusTxt=="success")
if(statusTxt=="error")
alert("Error: "+xhr.status+": "+xhr.statusText);
});
}

var input_icka = $("#icka_'.$idecko.'").val();

input_icka = input_icka.replace(\'(\'+id+\'),\', \'\');

$("#icka_'.$idecko.'").val(input_icka);

$(".obal_'.$idecko.'_priloha"+id+"").fadeOut(300, function(){ $(this).remove();});
$(".'.$idecko.'_priloha_vlozit"+id+"").fadeOut(300, function(){ $(this).remove();});
}

function '.$idecko.'_priloha_vlozit_input(id, icko)
{

$(".'.$idecko.'_priloha_vlozit"+icko+"").removeAttr("onclick");
$("."+id+"").html(\'<input class="'.$idecko.'_priloha_file_input\'+'.$promenna.'+\'" type="file" name="'.$idecko.'_input_file\'+'.$promenna.'+\'"/>\');

$(".'.$idecko.'_priloha_file_input"+'.$promenna.'+"").on(\'change\', function(){

var file = $(this).val();
var filesize = (this.files[0].size)/1000 + " Kb";

var onmouseover = "this.className=\'closeoknopo\'";

var onmouseout = "this.className=\'closeokno\'";

var onclick = "remove'.$idecko.'prilohafile(\'"+'.$promenna.'+"\')";

var pathextension = file.substring(file.lastIndexOf(\'.\') + 1).toLowerCase();
 
if(pathextension == "doc" || pathextension == "docx" || pathextension == "odt" || pathextension == "pdf" || pathextension == "rar" || pathextension == "xls" || pathextension == "zip")
{
pathextension = pathextension;
}
else
{

if(pathextension == "jpg" || pathextension == "jpeg" || pathextension == "png" || pathextension == "gif")
{} else { alert("nepodporovaný formát obrázku"); $(".'.$idecko.'_priloha_file_input"+'.$promenna.'+"").val(""); return false; }

if(pathextension == "exe" || pathextension == "icon" || pathextension == "bat" || pathextension == "dll")
{ alert("nepodporovaný soubor"); $(".'.$idecko.'_priloha_file_input"+'.$promenna.'+"").val(""); return false; }

pathextension = "others";

}

$("#'.$idecko.'_priloha").append(\'<div class="obal_'.$idecko.'_priloha\'+'.$promenna.'+\'" id="obal_popisek_priloha"><div class="'.$idecko.'_priloha_lista\'+'.$promenna.'+\'" id="popisek_priloha_lista">Přiložený soubor:<a onclick="\'+onclick+\'"><img class="closeokno" onmouseover="\'+onmouseover+\'" onmouseout="\'+onmouseout+\'" title="Odebrat fotku" alt="odebrat fotku" src="logged/wysiwyg_textarea/ikonka/close.png" width="20px"/></a></div>\
                               <div class="'.$idecko.'_img\'+'.$promenna.'+\'" id="popisek_img"><table><tr valign="top"><td rowspan="2"><img src="logged/wysiwyg_textarea/ikonka/pro_prilohy/\'+pathextension+\'.png" alt="\'+pathextension+\'" title="\'+pathextension+\'" width="70px"/></td><td>\'+file+\'</td></tr><tr><td>\'+filesize+\'</td></tr></table></div></div>\');


$("#icka_'.$idecko.'").val($("#icka_'.$idecko.'").val() + "("+'.$promenna.'+"),");

$(".'.$idecko.'_priloha_prilozit_soubor"+icko+"").hide();

'.$promenna.'++;

onclick = "'.$idecko.'_priloha_vlozit_input(\''.$idecko.'_priloha_prilozit_soubor\'+'.$promenna.'+\'\', \'\'+'.$promenna.'+\'\')";
$("#'.$idecko.'_priloha").append(\'<a class="'.$idecko.'_priloha_vlozit\'+'.$promenna.'+\'" id="popisek_priloha_vlozit" onclick="\'+onclick+\'"><div class="'.$idecko.'_priloha_prilozit_soubor\'+'.$promenna.'+\'" id="popisek_priloha_prilozit_soubor">Přiložit soubor</div></a>\');

});
}

</script>
';

echo $code;
}
?>