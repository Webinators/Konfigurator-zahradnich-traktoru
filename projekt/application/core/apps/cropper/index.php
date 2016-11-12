<!DOCTYPE html>
<html lang="cs">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Cropper</title>

    <style>body{margin: 0px; padding: 5px;background-color: #282828;color: #282828;}</style>

</head>
<body>

<?php

$pic = $_GET['destination'];

include("../../load/loader.php");

$tree = new TreeDirectory();
$project = $tree->getPathToProject(true);
$registratura = $tree->getPathToRegistratura(false,true);

$picP = explode("/",$pic);
$img = array_pop($picP);
$pic = implode("/",$picP);

$editor = new ImgEdit();
$editor->setInputDir($pic);

$size = getimagesize($pic."/".$img);

echo '
<script type="text/javascript" src="'.$registratura.'js/plugins/jquery_min.js"></script>
<link rel="stylesheet" type="text/css" href="'.$registratura.'apps/cropper/css/imgareaselect-default.css" />

<script type="text/javascript" src="'.$registratura.'apps/cropper/scripts/jquery.imgareaselect.pack.js"></script>

Vyberte oblast pro ořez:<br />
<div style="text-align: center;">

'.$editor->getIMGInHTML($img)->height("100%")->ID("ImageToCrop").'

<form id="imagesCropper" method="post" action="'.$registratura.'apps/cropper/cropp.php">
';

echo'
<input type="hidden" name="fileP" value="'.$pic.'" />
<input type="hidden" name="fileN" value="'.$img.'" />

<input id="defaultWidth" type="hidden" value="'.$size[0].'"/>
<input id="defaultHeight" type="hidden" value="'.$size[1].'"/>

<input name="x1" type="hidden" id="x1" value="-" />
<input name="y1" type="hidden" id="y1" value="-" />
<input name="width" type="hidden" value="-" id="w" />
<input name="height" type="hidden" id="h" value="-" />
  
<input style="position: absolute;bottom: 0px; left: 50%;z-index: 9999999;" type="submit" id="imagesCropperBtn" name="cropp" value="Oříznnout"/>

</form>
</div>

<script type="text/javascript">
function preview(img, selection) {
    if (!selection.width || !selection.height)
        return;
    
    $(\'#x1\').val(selection.x1);
    $(\'#y1\').val(selection.y1);
    $(\'#x2\').val(selection.x2);
    $(\'#y2\').val(selection.y2);
    $(\'#w\').val(selection.width);
    $(\'#h\').val(selection.height);    
}

$(document).ready(function() {

$("#ImageToCrop").load(function() {

$(this).height(($(window).height() * 90) / 100);

var height = $("#ImageToCrop").height();
var DefaultHeight = $("#defaultHeight").val();
var DefaultWidth = $("#defaultWidth").val();

var width = $("#ImageToCrop").width();

var AratioX = DefaultWidth/width;
var AratioY = DefaultHeight/height;

AratioX = AratioX.toFixed(5);
AratioY = AratioY.toFixed(5);

$("#imagesCropper").prepend(\'<input type="hidden" name="multipleX" value="\'+AratioX+\'" />\');
$("#imagesCropper").prepend(\'<input type="hidden" name="multipleY" value="\'+AratioY+\'" />\');

    $(\'#ImageToCrop\').imgAreaSelect({fadeSpeed: 200, onSelectChange: preview});

    });

});
</script>

';

?>
</body></html>