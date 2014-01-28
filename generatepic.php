<?
$number = $_GET[number];
$my_img = imagecreatefrompng ( "2002.png" );
$background = imagecolorallocate($my_img, 0, 0, 0);
imagecolortransparent($my_img, $background);
imagealphablending($my_img, false);
imagesavealpha($my_img, true);
//$background = imagecolorallocate( $my_img, 0, 0, 255 );
$text_colour = imagecolorallocate( $my_img, 0, 0, 0 );
//$line_colour = imagecolorallocate( $my_img, 128, 255, 0 );
$font = imageloadfont('./langdon.gdf');
imagestring( $my_img, $font, 115, 38, $number,  $text_colour );

//$item=preg_replace( '/\r\n\x0B\0\t/', ' ', $item);
header( "Content-type: image/png" );
imagepng( $my_img );
imagecolordeallocate( $line_color );
imagecolordeallocate( $text_color );
imagecolordeallocate( $background );
imagedestroy( $my_img );
?>