<?php
echo '
<a href="'.URL.'shop/ShopAdmin/parametrList">Správa parametrů</a>
<a href="'.URL.'shop/ShopAdmin/CategoryList">Správa kategorií</a>
<a href="'.URL.'shop/ShopAdmin/productList">Správa produktů</a>    
';
?>   

<form method="GET" action="" enctype="multipart/form-data">

<table class="zarovnani" cellspacing="10px">
<tr valign="top"><td>
<h3 style="width: 350px;"><center>Typ úpravy</center></h3>
<div class="bunka1">
<table class="table">
</table>
<table>

<tr><td><input name="uprava" type="radio" value="zadní výhoz (deflektorem), mulčování, sběr do koše" required/></td><td>&nbsp;&nbsp;&nbsp;zadní výhoz (deflektorem)</td></tr>

<tr><td><input name="uprava" type="radio" value="sběr do koše, mulčování, zadní výhoz (deflektorem)" required/></td><td>&nbsp;&nbsp;&nbsp;sběr do koše</td></tr>

<tr><td><input name="uprava" type="radio" value="mulčování, mulčování (vysoké trávy do 1,2 m.), mulčování (náletových dřevin do průmě" required/></td><td>&nbsp;&nbsp;&nbsp;mulčování</td></tr>

<tr><td><input name="uprava" type="radio" value="mulčování (vysoké trávy do 1,2 m.), mulčování (náletových dřevin do průměru 4cm)" required/></td><td>&nbsp;&nbsp;&nbsp;mulčování (vysoké trávy do 1,2 m.)</td></tr>

<tr><td><input name="uprava" type="radio" value="mulčování (náletových dřevin do průměru 4cm)" required/></td><td>&nbsp;&nbsp;&nbsp;mulčování (náletových dřevin do &Oslash; 4cm)</td></tr>

<tr><td><input name="uprava" type="radio" value="sběr do koše, mulčování, zadní výhoz (deflektorem)" required/></td><td>&nbsp;&nbsp;&nbsp;sběr do koše, mulčování, zadní výhoz (deflektorem)</td></tr>

</table>
</div>
</td><td>
<h3 style="width: 220px;"><center>Parcela</center></h3>
<div class="bunka2">
<table class="table">
</table>
<table>

<tr><td><input name="parcela" type="radio" value="5" required/></td><td>&nbsp;&nbsp;do&nbsp;5&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td></tr>

<tr><td><input name="parcela" type="radio" value="10" required/></td><td>&nbsp;&nbsp;do&nbsp;10&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td></tr>

<tr><td><input name="parcela" type="radio" value="15" required/></td><td>&nbsp;&nbsp;do&nbsp;15&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td></tr>

<tr><td><input name="parcela" type="radio" value="30" required/></td><td>&nbsp;&nbsp;do&nbsp;30&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td></tr>

<tr><td><input name="parcela" type="radio" value="60" required/></td><td>&nbsp;&nbsp;do&nbsp;60&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td></tr>

<tr><td><input name="parcela" type="radio" value="61" required/></td><td>&nbsp;&nbsp;nad&nbsp;61&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td></tr>

</table>
</div>
</td></tr>
<tr valign="top"><td>
<h3 style="width: 350px;"><center>Tráva</center></h3>
<div class="bunka4">
<table class="table">
</table>
<table>

<tr><td><input name="trava" type="radio" value="suchá, vlhká, mokrá" required/></td><td>&nbsp;&nbsp;&nbsp;suchá</td></tr>

<tr><td><input name="trava" type="radio" value="vlhká, mokrá" required/></td><td>&nbsp;&nbsp;&nbsp;vlhká</td></tr>

<tr><td><input name="trava" type="radio" value="mokrá" required/></td><td>&nbsp;&nbsp;&nbsp;mokrá</td></tr>

<tr><td><input name="trava" type="radio" value="náletové dřeviny" required/></td><td>&nbsp;&nbsp;&nbsp;náletové dřeviny</td></tr>

<tr><td><input name="trava" type="radio" value="vše" required/></td><td>&nbsp;&nbsp;&nbsp;vše</td></tr>


</table>
</div>
</td><td>
<h3 style="width: 220px;"><center>Sklon</center></h3>
<div class="bunka2">
<table class="table">
</table>
<table>

<tr><td><input name="sklon" type="radio" value="5" required/></td><td>&nbsp;&nbsp;do&nbsp;5<sup>°</sup></td></tr>

<tr><td><input name="sklon" type="radio" value="10" required/></td><td>&nbsp;&nbsp;do&nbsp;10<sup>°</sup></td></tr>

<tr><td><input name="sklon" type="radio" value="15" required/></td><td>&nbsp;&nbsp;do&nbsp;15<sup>°</sup></td></tr>

<tr><td><input name="sklon" type="radio" value="16" required/></td><td>&nbsp;&nbsp;nad&nbsp;16<sup>°</sup></td></tr>


</table>

</div>
</td></tr>
<tr valign="top"><td>
<h3 style="width: 350px;"><center>Sečení</center></h3>
<div class="bunka4">
<table class="table">
</table>
<table>

<tr><td><input name="seceni" type="radio" value="občasné, pravidelné 1* za týden, pravidelné 1* za měsíc, pravidelné 1* za rok" required/></td><td>&nbsp;&nbsp;&nbsp;občasné</td></tr>

<tr><td><input name="seceni" type="radio" value="pravidelné 1* za týden, pravidelné 1* za měsíc, pravidelné 1* za rok" required/></td><td>&nbsp;&nbsp;&nbsp;pravidelné 1* za týden</td></tr>

<tr><td><input name="seceni" type="radio" value="pravidelné 1* za měsíc, pravidelné 1* za rok" required/></td><td>&nbsp;&nbsp;&nbsp;pravidelné 1* za měsíc</td></tr>

<tr><td><input name="seceni" type="radio" value="pravidelné 1* za rok" required/></td><td>&nbsp;&nbsp;&nbsp;pravidelné 1* za rok</td></tr>

<tr><td><input name="seceni" type="radio" value="vše" required/></td><td>&nbsp;&nbsp;&nbsp;vše</td></tr>

</table>
</div>
</td><td>
<h3 style="width: 220px;"><center>Terén</center></h3>
<div class="bunka2">
<table class="table">
</table>
<table>

<tr><td><input name="teren" type="radio" value="suchý, podmáčený" required/></td><td>&nbsp;&nbsp;&nbsp;suchý</td></tr>

<tr><td><input name="teren" type="radio" value="podmáčený" required/></td><td>&nbsp;&nbsp;&nbsp;podmáčený</td></tr>

<tr><td><input name="teren" type="radio" value="vše" required/></td><td>&nbsp;&nbsp;&nbsp;vše</td></tr>

</table>
</div>
</td></tr>
</table>

Je nutno dodržovat svahovou dostupnost uvedenou v návodu k obsluze stroje. Zde uvádíme svahovou dostupnost motoru a převodovky z hlediska životnosti. 

<h3 style="width: 65%"><center>Osobní informace</center></h3>
<div class="bunka3">
<table class="table">
</table>
<table>
<tr><td style="text-align: right;width:115px;">Jméno:*</td><td>&nbsp;&nbsp;<input type="text" name="jmeno" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td></tr>
<tr><td style="text-align: right;width:115px;">Přijmení:*</td><td>&nbsp;&nbsp;<input type="text" name="prijmeni" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td></tr>
<tr><td style="text-align: right;width:115px;">Ulice a č. popisné:*</td><td>&nbsp;&nbsp;<input type="text" name="ulice" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td></tr>
<tr><td style="text-align: right;width:115px;">Město:*</td><td>&nbsp;&nbsp;<input type="text" name="mesto" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td></tr>
<tr><td style="text-align: right;width:115px;">PSČ:*</td><td>&nbsp;&nbsp;<input type="text" name="psc" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required size="5" maxlength="6"/></td></tr>
<tr><td style="text-align: right;width:115px;">Email:*</td><td>&nbsp;&nbsp;<input type="email" name="email" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td></tr>
<tr><td style="text-align: right;width:115px;">Telefon:*</td><td>&nbsp;&nbsp;<input type="tel" name="telefon" onfocus="if(this.value == '+420') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="+420" value="+420" maxlength="17" required size="15"/></td></tr>

</table>
</div>
<br />
<div style="clear: both;position:relative;margin-top: 30px;">
<center>
<input class="submit" onmouseover="this.className='submit2'" onmouseout="this.className='submit'" style="cursor: pointer;" type="submit" name="najit" value="Najít traktor"/>
</center>
</div>
</form>