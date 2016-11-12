<?php
if(isset($_POST['fileP']))
{

    include("../../load/loader.php");

    $fileP = $_POST['fileP'];
    $fileN = $_POST['fileN'];

    $multipleX = $_POST["multipleX"];
    $multipleY = $_POST["multipleY"];
    $x = (int) ($_POST["x1"] * $multipleX);
    $y = (int) ($_POST["y1"] * $multipleY);
    $width = (int) ($_POST["width"] * $multipleX);
    $height = (int) ($_POST["height"] * $multipleY);

    $images = new ImgEdit();
    $images->setInputDir($fileP);
    $images->setOutputDir($fileP);
    $img = $images->loadImage($fileN);
    $img->cropImage($x,$y,$width,$height);
    $img->save();

}
?>

