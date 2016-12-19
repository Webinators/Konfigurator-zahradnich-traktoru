<?php echo '

<link rel="stylesheet" href="'.$packages.'css/searched.css" type="text/css" media="all" />

<div id="vypis_produktu">
<div class="filtr">Parametry</div>
<div class="options">
    <b>Cena:</b>

    <a href="'.$url.'konfigurator/konfigurator/search/DESC/'.urlencode($avyrobce).'"><img src="'.$packages.'icons/'.(($acena == "DESC") ? "dolu1" : "dolu").'.png" alt="Cena dolu" title="cena dolu" border="0"></a>

    <a href="'.$url.'konfigurator/konfigurator/search/ASC/'.urlencode($avyrobce).'"><img src="'.$packages.'icons/'.(($acena == "ASC") ? "nahoru1" : "nahoru").'.png" alt="Cena nahoru" title="cena nahoru" border="0"></a><br>

    <b>Výrobce:</b>
    ';

foreach($vyrobci as $vyrobce){
    echo '<a class="'.(($avyrobce == $vyrobce["Vyrobce"]) ? "active" : "").'" href="'.$url.'konfigurator/konfigurator/search/'.$acena.'/'.urlencode($vyrobce["Vyrobce"]).'">'.$vyrobce["Vyrobce"].'</a> - ';
}

echo '<a class="'.(($avyrobce == "vse") ? "active" : "").'" href="'.$url.'konfigurator/konfigurator/search/'.$acena.'/vse">vše</a>

    <br>
    <!--<b>Režim:</b>
    <a href="'.$url.'konfigurator/konfigurator/search/'.$acena.'/'.$avyrobce.'/compare">Porovnávací</a>
    <span style="'.$url.'konfigurator/konfigurator/search/'.$acena.'/'.$avyrobce.'/standart">Standart</span>-->

</div>
</div>
<br>

<div id="polozky_najite" class="flexElem flexWrap">
';

$cmd = new FileCommander();
$editor = new ImgEdit();

foreach($products as $product) {

    $path = $project."apps/shop/images/".$product["ID_produkt"]."";
    $cmd->setPath($path);

    if($cmd->dirExists("thumbs")){
        $cmd->moveToDir("thumbs");
    }

    $editor->setInputDir($cmd->getActualPath());

    $thumbs = $cmd->getFiles();

    echo '
            <div class="produkt flex flexElem"><div>
                <div class="nazev">
                    '.$product["Vyrobce"].' - '.$product["Nazev_p"].'
                </div>
                <br>
                <table style="color: #ffffff;">
                    <tbody>
                    <tr valign="middle">
                        <td rowspan="5" style="width: 170px;">
                            <div class="miniatura"><a href="">'.$editor->getIMGInHTML($thumbs[0])->width("120px")->alt("")->title("").'</a></div></td>
                    </tr>
                    <tr>
                        <td> Cena: <font color="#8acb08"><b>'.$product["Cena"].'</b></font> Kč</td>
                    </tr>

                    <tr>
                        <td>
                            <a href="'.$url.'konfigurator/konfigurator/detail/'.$product["ID_produkt"].'" class="submitt" style="text-decoration: none;">Zobrazit&nbsp;více</a>
                        </td>
                    </tr>
                    </tbody>
                </table>


                <div class="popis">
                    '.$product["Popis"].'
                </div>
            </div></div>
    ';

}

echo '
</div>
';

echo '<div class="flexElem alignElemsRight" style="width: 100%;">'.$pager.'</div>';

?>