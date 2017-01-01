<?php
function rotate($jpgFile, $thumbFile, $orientation) {
  

 
    $source = imagecreatefromjpeg($jpgFile);

    // Fix Orientation
    switch($orientation) {
        case 3:
             $rotate = imagerotate($source,180,0);
             imagejpeg($rotate,$jpgFile);
            break;
        case 6:
             $rotate = imagerotate($source,-90,0);
             imagejpeg($rotate,$jpgFile);
            break;
        case 8:
             $rotate = imagerotate($source,90,0);
             imagejpeg($rotate,$jpgFile);
            break;
    }
    // Output

 imagedestroy($source); 
 imagedestroy($rotate); 
}

?>