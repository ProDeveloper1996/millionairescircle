<?php

require_once ("config.php");
require_once ("xpage_public.php");


$session_id = "";
if (array_key_exists ('PHPSESSID', $_GET)) {
    $session_id = $_GET['PHPSESSID'];
}


if (strlen ($session_id) > 0) @session_id ($session_id);

@session_start ();

$text = "ERROR";
if (array_key_exists ('Log_Turing_ID', $_SESSION)) {
    $text = $_SESSION['Log_Turing_ID'];
}

$sitePath = $db->GetOne ("Select value From settings Where keyname='PathSite'");


$bgpath = $sitePath."images/fon.jpg";
$image = imagecreatefromjpeg ($bgpath);



$sizes = getimagesize ($bgpath);
$iw = $sizes[0];
$ih = $sizes[1];

$tpx = round ($iw/strlen ($text));
$sizeY = $ih;

$font = 4;
$fw = imagefontwidth ($font)*strlen ($text);
$fh = imagefontheight ($font);

$x = ($iw-$fw)/2;
$y = ($ih-$fh)/2;

for ($i = 0; $i < strlen ($text); $i++)
{
    $newNumber = imageCreate ($tpx, $tpx);
    $background_color = imagecolorallocate ($newNumber, 255, 255, 255);

    $red = rand(0, 230);
    $green = rand(0, 230);
    $blue = rand(0, 230);
    $tc = imagecolorallocate ($newNumber, $red, $green, $blue);

    $font = rand (4, 5);
    imagestring (
        $newNumber,
        $font,
        0,
        0,
        $text[$i],
        $tc
    );

    $angle = rand (-10, 10);
    if ($angle < 0) $angle = 360 + $angle;
//    $newNumber = imagerotate ($newNumber, $angle, 0);

    $w = imageSX ($newNumber);
    $h = imageSY ($newNumber);

    $w_new = round($w*(rand(10, 15)/10));
    $h_new = round($h*(rand(10, 15)/10));

    $newImage = imageCreate ($w_new, $h_new);
    $background_color = imagecolorallocate ($newImage, 255, 255, 255);
    imageCopyResized($newImage, $newNumber, 0, 0, 0, 0, $w_new, $h_new, $w, $h);

    $wtpx = $tpx*$i;
    $w = imageSX ($newImage);
    $h = imageSY ($newImage);
    if ($h < $sizeY) $sizeY = $h;
    imageCopy ($image, $newImage, $wtpx, 0, 0, 0, $w, $h);

    imageDestroy ($newImage);
    imageDestroy ($newNumber);
}

$w = imageSX ($image);
$turing = imagecreatefromjpeg ($bgpath);
imageCopyResized($turing, $image, 0, 0, 0, 0, $w, $ih, $w, $sizeY);
imageDestroy ($image);

header ('Content-type: image/jpeg');
return imagejpeg ($turing, '', 70);
imageDestroy ($turing);

?>