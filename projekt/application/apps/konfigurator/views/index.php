<?php
echo '
<link rel="stylesheet" href="'.$packages.'css/konfigurator.css" type="text/css" media="all" />
<div id="konfigurator">
    <form method="POST" action="'.$url.'konfigurator/konfigurator/search">

<div class="flexElem flexWrap alignElemsCenter">
';

        $last = "";

        $i = count($params);

        foreach ($params as $param){

            if($last != $param["Nazev"]){

                if($last != ''){
                    echo '</table></div></div></div>';
                }

                echo '
                    <div class="konfigurator_polozka flex flexElem"><div>

                    <div class="konfigurator_zalozka">
                        <div class="top" style=""><h3>'.$param["Nazev"].'</h3>
                        </div>
                        <div class="bottom">
                        </div>
                    </div>
        
                    <div class="konfigurator_bunka"><input type="hidden" name="parametr[id][]" value="'.$param["ID_parametr"].'"/><table border="0" margin="0" cellpadding="0">
                    ';

            }

	     
            echo '<tr><td><input name="parametr[hodnota]['.$param["ID_parametr"].']" type="radio" value="'.$param["ID_hodnota"].'" required/></td><td>&nbsp;&nbsp;&nbsp;'.$param["Hodnota_h"].'</td></tr>';

            $last = $param["Nazev"];
            $i--;

            if($i == 0){
                echo '</table></div></div></div>';
            }

        }


        ?>


        <div class="konfigurator_polozka flex flexElem"><div>

            <div class="konfigurator_zalozka">
                <div class="top" style=""><h3>Parcela</h3>
                </div>
                <div class="bottom">
                </div>
            </div>

            <div class="konfigurator_bunka">

                <table>
                    <tr><td>
                            <input name="parcela" type="radio"   value="5" required/></td><td>&nbsp;&nbsp;do&nbsp;5&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td>
                    </tr>
                    <tr><td>
                            <input name="parcela" type="radio"   value="10" required/></td><td>&nbsp;&nbsp;do&nbsp;10&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td>
                    </tr>
                    <tr><td>
                            <input name="parcela" type="radio"   value="15" required/></td><td>&nbsp;&nbsp;do&nbsp;15&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td>
                    </tr>
                    <tr><td>
                            <input name="parcela" type="radio"   value="30" required/></td><td>&nbsp;&nbsp;do&nbsp;30&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td>
                    </tr>
                    <tr><td>
                            <input name="parcela" type="radio"   value="60" required/></td><td>&nbsp;&nbsp;do&nbsp;60&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td>
                    </tr>
                    <tr><td>
                            <input name="parcela" type="radio"   value="61" required/></td><td>&nbsp;&nbsp;nad&nbsp;61&nbsp;000&nbsp;&nbsp;m<sup>2</sup></td>
                    </tr>
                </table>

            </div>

        </div></div>

        <div class="konfigurator_polozka flex flexElem"><div>

            <div class="konfigurator_zalozka">
                <div class="top" style=""><h3>Sklon</h3>
                </div>
                <div class="bottom">
                </div>
            </div>

            <div class="konfigurator_bunka">

                <table>
                    <tr><td>
                            <input name="sklon" type="radio"   value="5" required/></td><td>&nbsp;&nbsp;do&nbsp;5<sup>°</sup></td>
                    </tr>
                    <tr><td>
                            <input name="sklon" type="radio"   value="10" required/></td><td>&nbsp;&nbsp;do&nbsp;10<sup>°</sup></td>
                    </tr>
                    <tr><td>
                            <input name="sklon" type="radio"   value="15" required/></td><td>&nbsp;&nbsp;do&nbsp;15<sup>°</sup></td>
                    </tr>
                    <tr><td>
                            <input name="sklon" type="radio"   value="16" required/></td><td>&nbsp;&nbsp;nad&nbsp;16<sup>°</sup></td>
                    </tr>
                </table>
            </div>

        </div></div>

</div>

        <br />
        Je nutno dodržovat svahovou dostupnost uvedenou v návodu k obsluze stroje. Zde uvádíme svahovou dostupnost motoru a převodovky z hlediska životnosti.


        <!--
        <h3 style="width: 65%">
            <center>Osobní informace
            </center></h3>
        <div class="bunka3">

            <table class="FlexTable">
                <tr>
                    <td style="text-align: right;width:115px;">Jméno:*</td><td>&nbsp;&nbsp;
                        <input type="text" name="jmeno" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td>
                </tr>
                <tr>
                    <td style="text-align: right;width:115px;">Přijmení:*</td><td>&nbsp;&nbsp;
                        <input type="text" name="prijmeni" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td>
                </tr>
                <tr>
                    <td style="text-align: right;width:115px;">Ulice a č. popisné:*</td><td>&nbsp;&nbsp;
                        <input type="text" name="ulice" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td>
                </tr>
                <tr>
                    <td style="text-align: right;width:115px;">Město:*</td><td>&nbsp;&nbsp;
                        <input type="text" name="mesto" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td>
                </tr>
                <tr>
                    <td style="text-align: right;width:115px;">PSČ:*</td><td>&nbsp;&nbsp;
                        <input type="text" name="psc" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required size="5" maxlength="6"/></td>
                </tr>
                <tr>
                    <td style="text-align: right;width:115px;">Email:*</td><td>&nbsp;&nbsp;
                        <input type="email" name="email" onfocus="if(this.value == '') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="" value="" required/></td>
                </tr>
                <tr>
                    <td style="text-align: right;width:115px;">Telefon:*</td><td>&nbsp;&nbsp;
                        <input type="tel" name="telefon" onfocus="if(this.value == '+420') this.value='';" onblur="if(this.value=='')this.value=this.defaultValue" placeholder="+420" value="+420" maxlength="17" required size="15"/></td>
                </tr>
            </table>
        </div><br />

        -->

        <div style="clear: both;position:relative;margin-top: 30px;">
            <center>
                <input class="submit" onmouseover="this.className='submit2'" onmouseout="this.className='submit'" style="cursor: pointer;" type="submit" name="najit" value="Najít traktor"/>
            </center>
        </div>
    </form>
</div>