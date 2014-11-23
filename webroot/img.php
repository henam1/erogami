<?php 
/**
 * This is a PHP skript to process images using PHP GD.
 *
 */

include(__DIR__.'/config.php');

//
// Define some constant values, append slash
// Use DIRECTORY_SEPARATOR to make it work on both windows and unix.
//
$dir = array(
    'imgDir' => __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR,
    'cacheDir' => __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR
);

$img = new CImage($dir);

$img->displayImage();
