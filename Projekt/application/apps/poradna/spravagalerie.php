<div id="odsazeni">

    <?php

    $poradna = new Poradna();

    if($_GET["id_clanek"] != ''){

        $clanek = $poradna->showClanek($_GET["id_clanek"]);

        /* Nastacení pro aplikace */

        /*Nastavení pro DB */
        $cestadb = "data/db_clanky.php";
        $tabulka = "Clanky";
        $nazev_identifikatoru = "ID_clanku";
        $idclanku = $clanek;
        $slozka_na_ukladani_souboru = "apps/wysiwyg_textarea/obrazky_novinek/";  //vychází se ze složky s indexem
        $nazev_atributu_pro_ulozeni_textu_v_tabulce = "Text";   //povinný jen pro typ článku text, u aktualit nechat prázdný
        $slozka_s_fotogaleri = "fotogalerie/";  //vychází se ze složky s indexem

        /*Parametry pro vypsani clanku */
        $typclanku = "text";    //text || aktualita
        $showspravceobrazku = "true";

    require('apps/wysiwyg_textarea/textarea.php');

    } else {

        echo $poradna->insertComponents();
        $poradna->printGallery();
    }
    ?>

</div>
