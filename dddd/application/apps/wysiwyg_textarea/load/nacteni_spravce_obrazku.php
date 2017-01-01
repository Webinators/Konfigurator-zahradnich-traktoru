<?php

if($nedelatvewysiwyg_textarea == "")
{

echo'

<style type="text/css">

.submit {padding: 10px 20px;border: 0px;font-size: 18px;background-color: #699c04;border: 1px #000 solid;color: #ffffff;}
.submit2 {padding: 10px 20px;border: 0px;font-size: 18px;background-color: #2e2e2e;border: 1px #000 solid;color: #ffffff;}

</style>

<script type="text/javascript" src="apps/wysiwyg_textarea/js/spravceobrazku.js" charset="utf-8"></script>

<div class="okno" id="obrazky">
<div style="position: fixed;_position: relative;top: 0px;left: 0px;width: 100%;height: 100%;z-index: 99999999999999;color: #000000;">
<a style="cursor: pointer;padding: 0px;" onclick="skryjobrazky(\'obrazky\')"><div class="pruhledny_pozadi_u_movie"></div></a>

<div id="obrazky_vlozeni">
<br />
<form name="multiform" id="multiform" action="apps/wysiwyg_textarea/load/nahraniobrazku.php" method="POST" enctype="multipart/form-data">
<table>
<tr><td><input type="file" id="file" name="file" class="filestyle" data-input="false" data-buttonText="Nový obrázek"/></td><td><input type="submit" onclick="nahraj()" id="multi-post" value="Upload" class="upload" onmouseover="this.className=\'uploadpo\'" onmouseout="this.className=\'upload\'"/></td><td style="width: 500px;text-align: right;"><input id="searching" type="search" name="keyword" oninput="najdi(this.value)" style="border: 1px #000 solid;" value="search" onfocus="if(this.value == \'search\') this.value=\'\';" onblur="if(this.value==\'\')this.value=this.defaultValue" placeholder="search"/></td></tr>
</table>
<center>Zarovnání obrázků:&nbsp;<select id="zarovnaniobrazku"><option value="left">left</option><option value="right">right</option><option value="absolute">před textem</option></select>
<div id="multi-msg"></div>
</center>
</form>

<div id="scrolldiv2" onmousedown="zobrazpravymkliknutim(\'scrolldiv2\',\'pravymenuvne\')">
   <div id="pravymenuvne" class="pravyklikmenu">
        <ol>
            <li><a href="#" onclick="createfolder()">New folder</a> </li>

        </ol>
    </div>

<div id="seznam">

<div id="loading"></div></div>

</div>

<br /></div></div></div>

';
}

?>