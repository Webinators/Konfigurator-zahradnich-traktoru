<?php
$pocet_textovych_poli = $_POST['pocet_textovych_poli'];
$tabulka = $_POST['tabulka'];
$nazev_identifikatoru = $_POST['nazev_identifikatoru'];
$i = $_POST['i'];

$cestadb = $_POST['cestadb'];
$cestadb = "../../../".$cestadb;

$textove_pole_popisek = $_POST['textove_pole_popisek'];
$nadpis_k_textu = $_POST['nadpis_k_textu'];

$id = $_POST['id'];

require($cestadb);

$query = "SELECT * FROM ".$tabulka." WHERE ".$nazev_identifikatoru."=".$id."" or die("Problém: " . mysqli_error($link));$result = $link->query($query);

$vysledek = mysqli_fetch_array($result);

echo '<div id="textlabel'.$i.'">';

 if($textove_pole_popisek == "true")
      {
      $popisek = strtr($vysledek['Popisek'],"|","\"");
      $popisek = $popisek.'<div id="pic"></div>';
      }

      $text = strtr($vysledek['Text'],"|","\"");

      $text = $text.'<div id="pic'.$i.'"></div>'; 

      $nadpis = $vysledek['Nadpis'];

      $id = $vysledek[$nazev_identifikatoru];
       
      if($pocet_textovych_poli > 1)
      {
      echo '<div class="krajnidiv">';
      }

      if($nadpis_k_textu == "true") 
      {
      if($pocet_textovych_poli > 1)
      {
      echo'<div class="nadpis4">';
      }

      echo''.$nadpis.'<a style="float: right;margin-right: 5px;cursor: pointer;" onclick="editovatclanek(\''.$id.'\',\'textlabel'.$i.'\')">edit</a>';

      if($pocet_textovych_poli > 1)
      {
      echo'</div>';
      }
      }


      if($textove_pole_popisek == "true")
      {

      if($pocet_textovych_poli > 1)
      {
      echo'<div class="vnitrek">';
      }

      echo'
'.$popisek.'
           
      ';  

      if($pocet_textovych_poli > 1)
      {
      echo'</div></div><br />';
      }

      $i++;$pocet_textovych_poli++; 
      }

      echo '</div>';

echo '</div>';

?>