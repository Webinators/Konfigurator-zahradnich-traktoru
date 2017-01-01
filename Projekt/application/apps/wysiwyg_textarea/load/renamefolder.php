<?php
session_start();

$pokracovani = $_SESSION['pokracovani'];

if($pokracovani != '')
{
$pokracovani = $pokracovani."/";
}

$starynazev = $_POST['starynazev'];
$novynazev = $_POST['novynazev'];

if(is_dir("../obrazky/$pokracovani$novynazev"))
{
echo '<script>alert("Tato složka už existuje!");</script>';
}
else
{
rename("../obrazky/$pokracovani$starynazev","../obrazky/$pokracovani$novynazev");
}

require('vypsaniobrazkuktextaku.php');

?>