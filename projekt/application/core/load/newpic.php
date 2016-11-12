<?php

$newpic = imagecreatefrompng("../utilities/vzor.png");

for ($i=0;$i<5;$i++)

{

while(strlen($str)!=1){

$random=rand(48,123);

if( ($random>47 && $random<58) || ($random>96 && $random<123) ||

($random>64 && $random<91) ){

$str.=chr($random);

}

} 

$text .= strtolower($str);

$textcolor = imagecolorallocate($newpic,rand(0,130),rand(0,130),rand(0,130));

imagettftext ($newpic,rand(15,25),rand(-45,45),15+($i*38),35, $textcolor,"../utilities/arial.ttf",$str) ;

$str = NULL;

}

session_start(); 
session_regenerate_id(); 
ini_set('session.cookie_httponly', true); 

$_SESSION['Randomstring'] = $text;
$_SESSION['Randomstring_lifestart'] = time(); 

Header("Content-type: image/png"); 

ImagePNG($newpic);
ImageDestroy($newpic);

?>