<?php

$fullPath = realpath(dirname(__FILE__))."/";

require_once(''.$fullPath.'config.php');

$tree = new TreeDirectory();
$url = $tree->getUrlDomain();

$realDir = $url.PATH_TO_PROJECT.PATH_TO_REGISTRATURA;

$icons = new Icons();

echo'
<script type="text/javascript">
var mainVariables = {
  homepath: "'.$url.'",
  pathtoimages: "'.$realDir.'utilities/",
  pathtofiles: "'.$realDir.'"
};
</script>

<script type="text/javascript" src="'.$realDir.'js/plugins/jquery.mobile.min.js"></script>
<script type="text/javascript" src="'.$realDir.'js/plugins/jquery_min.js"></script>

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

<script src="'.$realDir.'apps/imagesEditor/js/changeIMG.js" type="text/javascript"></script>

';

$msg = new Msg();
echo $msg->showMsgs();

if (isset($_GET['form']) && $_GET['form'] == "registrace") {

    echo '<link rel="stylesheet" type="text/css" href="' . $realDir . 'css/users/registrace.css">';

    if ($_GET['admin']) {

        echo '
            <script type="text/javascript">

            $(document).ready(function(){

                sendData({

                    data: {send:true},
                    url: "' . $realDir . 'load/admin/form/getAccessForm.php",
                    method: "POST",
                    progress: "window",
                    alert: false

                },function(data, err){

                    if(data != false){

                        mainWindow.normal({
                            center: true,
                            bar: false,
                            content: data,
                            buttonPointer: false,
                            returnPointer: false,
                            close: false
                        });

                    } else {

                        mainWindow.alert({
                            content: err,
                            close: false
                        });
                     }

                });

            });

                </script></body></html>
                ';

        exit;

    } else {

        echo '
            <script type="text/javascript">

            $(document).ready(function(){

                sendData({

                    data: {send:true},
                    url: "' . $realDir . 'load/users/form/getRegForm.php",
                    method: "POST",
                    progress: "window",
                    alert: false

                },function(data, err){

                    if(data != false){

                        mainWindow.normal({
                            center: true,
                            bar: false,
                            content: data,
                            buttonPointer: false,
                            returnPointer: false,
                            close: false
                        });

                    } else {

                        mainWindow.alert({
                            content: err,
                            close: false
                        });
                     }

                });

            });

                </script></body></html>
                ';

        exit;
    }

} else {

    $user = \System\Authentication\Users::initialize();
    $bar = $user->buildUserBar();
    echo $bar;

}



?>

