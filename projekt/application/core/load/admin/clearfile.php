<?php

if(isset($_POST['unlink']) && $_POST['unlink'] == "true")
{
session_start();
$createfile = "../../utilities/pass.TXT";
unlink($createfile);
unset($_SESSION['created_code']);
}
?>