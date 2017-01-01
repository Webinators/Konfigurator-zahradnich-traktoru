<?php

session_start();

$pokracovani = $_SESSION['pokracovani'];

if($pokracovani != '')
{
$pokracovani = $pokracovani."/";
}

$file = $_FILES['file']['name'];
$folder = "../obrazky/$pokracovani";

$id = $_POST['id_sekacky'];

$picture = $file;   
$rename = strtr($picture, " ešcržýáíéntduúýóöäüóôEŠCRŽÝÁÍÉNTDUÚÝÓÖÄÜÓÔ", "_escrzyaientduuyooauooESCRZYAIENTDUUYOOAUOO");

if(file_exists($folder.$rename))
{
echo'<script type="text/javascript">alert("Soubor v této složce s tímto názvem už existuje");</script>'; $preskocit="ano";
}

if($preskocit == "ano")
{

}
else
{
include ('../SimpleImage.php');

move_uploaded_file($_FILES['file']['tmp_name'], "../predimage/".$rename);

  $file = "../predimage/".$rename."";
  $soubor= pathinfo($file, PATHINFO_EXTENSION);
  if($soubor == "jpg" OR $soubor == "JPG" OR $soubor == "png" OR $soubor == "gif")
{

$rozmer = getimagesize('../predimage/'.$rename.'');	
	$width = $rozmer[0];
	$height = $rozmer[1];

       if($width >= $height)
	{ 
		if($width <= 350)
		{
		 $image = new SimpleImage();
       	 $image->load('../predimage/'.$rename.'');
               $image->save($folder.$rename);

		} 
		else
		{		
        	 $image = new SimpleImage();
       	 $image->load('../predimage/'.$rename.'');
       	 $image->resizeToWidth(350);
        	 $image->save($folder.$rename);

     		 }
	} 	
	if ($height > $width)
	{
		if($height <= 200)
		{
	              $image = new SimpleImage();
       	       $image->load('../predimage/'.$rename.'');
                     $image->save($folder.$rename);
		}
		 else
		{
			$image = new SimpleImage();
    			$image->load('../predimage/'.$rename.'');
    			$image->resizeToHeight(200);
    			$image->save($folder.$rename);
		}

	}

}

			if ($rename != '')
	{
                $way = "../predimage/".$rename;	
        	 unlink ($way);
	}

}
require('vypsaniobrazkuktextaku.php');
?>