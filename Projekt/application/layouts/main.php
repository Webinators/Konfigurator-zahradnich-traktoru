<?php

$tree = new TreeDirectory();
$projUrl = $tree->getPathToProject(false,true);

?>

<!DOCTYPE html>
<html lang="cs">
<head>

    <meta http-equiv="Content-type" content="application/html; charset=utf-8" />

    <link rel="stylesheet" href="<?php echo $projUrl; ?>apps/fotogalerie/css/lightbox.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo $projUrl; ?>css/styly.css" type="text/css" media="all" />

    <title>Konfigurator app</title>

<?php

$tree = new TreeDirectory();
$url = $tree->getPathToRegistratura(false,true);

$realDir = $url;

$icons = new Icons();

echo'
<script type="text/javascript">
var mainVariables = {
  homepath: "'.$tree->getPathToProject(false,true).'",
  pathtoimages: "'.$realDir.'utilities/",
  pathtofiles: "'.$tree->getPathToProject(false,true).'"
};
</script>

<script type="text/javascript" src="'.$realDir.'js/plugins/jquery_min.js"></script>
<script type="text/javascript" src="'.$realDir.'js/plugins/jquery.mobile.min.js"></script>


<script src="'.$realDir.'js/plugins/ResizeSensor.js"></script>
<script src="'.$realDir.'js/plugins/ElementQueries.js"></script>

<script type="text/javascript" src="'.$realDir.'js/functions.js"></script>
<link rel="stylesheet" type="text/css" href="'.$realDir.'css/functions.css">
<link rel="stylesheet" type="text/css" href="'.$realDir.'css/Uzivatele.css">
<link rel="stylesheet" type="text/css" href="'.$realDir.'css/responzive.css">


<script src="'.$realDir.'apps/fileuploader/js/extendet.js"></script>
<script src="'.$realDir.'apps/fileuploader/js/basic.js"></script>

<link rel="stylesheet" type="text/css" href="'.$realDir.'apps/fileuploader/css/extendet.css">
<link rel="stylesheet" type="text/css" href="'.$realDir.'apps/fileuploader/css/basic.css">

<script src="'.$realDir.'js/formelem/jquery.wysibb.min.js"></script>
<link rel="stylesheet" type="text/css" href="'.$realDir.'css/formelem/wbbtheme.css">

<script src="'.$realDir.'js/formelem/jquery.nice-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="'.$realDir.'css/formelem/nice-select.css">

<script src="'.$realDir.'js/formelem/icheck.min.js"></script>
<link rel="stylesheet" type="text/css" href="'.$realDir.'css/formelem/icheck/flat.css">

<script src="'.$realDir.'js/formelem/toggles.min.js"></script>
<link rel="stylesheet" type="text/css" href="'.$realDir.'css/formelem/toggles/toggles.css">
<link rel="stylesheet" type="text/css" href="'.$realDir.'css/formelem/toggles/toggles-modern.css">

           <link rel="stylesheet" type="text/css" href="http://trida-tl.8u.cz/rsp/core/apps/bar/css/loggedBar.css">
           <script src="http://trida-tl.8u.cz/rsp/core/apps/bar/js/loggedBar.js" type="text/javascript"></script>

<script type="text/javascript">

  function niceSelect(el){console.log(el);
    el.niceSelect();
  }

  function iCheck(el){
    el.not(".switchBtn").iCheck({
      checkboxClass: "icheckbox_flat",
      radioClass: "iradio_flat"
    });
  }

  function toggles(el, checkbox){

    if(!isDefined(checkbox)){checkbox = null;} else {checkbox.hide();}

    el.toggles({
    drag: true,
    click: true,
    text: {
        on: "ON",
        off: "OFF"
    },
     on: true,
    animate: 250,
    width: 70, // width used if not set in css
     height: 20, // height if not set in css
    easing: "swing",
    checkbox: checkbox,
    type: "select"
    });
    
  }

</script>

<script src="'.$realDir.'apps/imageseditor/js/changeIMG.js" type="text/javascript"></script>

';
?>


</head>

<body>

<div style="margin: 0px auto;width: 900px;">

<?php
require(CORE."Uzivatele.php");

echo $view;
?>


<script type="text/javascript" src="<?php echo $projUrl; ?>apps/fotogalerie/js/lightbox.min.js"></script>

</div>

<div style="position: absolute;bottom: -20px;"><endora /></div>

</body>
</html>