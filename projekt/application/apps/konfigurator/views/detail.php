<?php echo '

<link rel="stylesheet" href="'.$packages.'css/detail.css" type="text/css" media="all" />

<div id="detail_produkt">

    <div id="detail_produkt_nadpis"><h2 align="center">'.$data["Vyrobce"].' '.$data["Nazev_p"].'</h2></div>
    <br>

    <table>
        <tbody>
        <tr>
            <td>';

$path = $project."apps/shop/images/".$data["ID_produkt"]."";

$mini = new MiniGallery($path);

$cmd = new FileCommander();
$cmd->setPath($path);

$cmd->moveToDir("thumbs");
$thumbs = $cmd->getFiles("",false,false,true);

$cmd->moveUp();
$cmd->moveToDir("foto");
$foto = $cmd->getFiles("",false,false,true);

echo '<div class="pozadi">
             <a href="'.$foto[0].'" data-lightbox="roadtrip"><img src="'.$thumbs[0].'" alt="Sekačka" title="Sekačka"> </a>
            ';

for($i = 1; $i < count($thumbs); $i++){

    echo '
                <a style="display: none;" href="'.$foto[$i].'" data-lightbox="roadtrip"><img src="'.$thumbs[$i].'" alt="Sekačka" title="Sekačka"> </a>
                ';

}

echo '<h5> Celkem '.count($thumbs).' obrázek</h5></div>

           </td>
            <td>
                <div>
                    <table width="370px">
                        <tbody>
                        <tr>
                            <td>Cena s DPH:</td>
                            <td><b>
                                <font color="#ff0000">'.$data["Cena"].'</font>&nbsp;&nbsp;Kč</b></td>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <br />

    <button type="button" class="popis active" id="btn_popis">Popis</button>  <button type="button" class="techn_param" id="btn_params">Technické parametry</button>

    <div class="info" id="popis">
        <b>Popis</b>
        <br />
        <hr>
        '.$data["Popis"].'
    </div>

    <div id="detail_produkt_params" class="info"><b>Technické Parametry</b>
        <br>
        <hr>
        <table class="parametry">
        ';

        foreach($data["params"] as $param){
            echo '<tr><td>'.$param["Nazev"].'</td><td>'.$param["Hodnota"].'</td></tr>';
        }

        echo '
        </table>
    </div>

    <script type="text/javascript">

        $(document).on("click","#btn_popis",function(e){
            e.preventDefault();
	    $(this).addClass("active");
	    $("#btn_params").removeClass("active");
           $("#detail_produkt_params").hide();
           $("#popis").show();
        });

        $(document).on("click","#btn_params",function(e){
            e.preventDefault();
	    $(this).addClass("active");
           $("#popis").hide();
           $("#detail_produkt_params").show();
	    $("#btn_popis").removeClass("active");
        });

    </script>

    <br>
</div>

';

?>