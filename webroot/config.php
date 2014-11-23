<?php
/**
 * Config-file for Erogami. Change settings here to affect installation.
 *
 */

/**
 * Set the error reporting.
 *
 */
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly


/**
 * Define Erogami paths.
 *
 */
define('EROGAMI_INSTALL_PATH', __DIR__ . '/..');
define('EROGAMI_THEME_PATH', EROGAMI_INSTALL_PATH . '/theme/render.php');


/**
 * Include bootstrapping functions.
 *
 */
include(EROGAMI_INSTALL_PATH . '/src/bootstrap.php');


/**
 * Start the session.
 *
 */
session_name(preg_replace('/[^a-z\d]/i', '', __DIR__));
session_start();


/**
 * Create the Erogami variable.
 *
 */
$erogami = array();


/**
 * Site wide settings.
 *
 */
$erogami['lang']         = 'sv';
$erogami['title_append'] = ' | Erogami en webbtemplate';

$erogami['header'] = <<<EOD
<img class='sitelogo' src='img/oophp.png' alt='Erogami Logo'/>
<span class='sitetitle'>Erogami webbtemplate</span>
<span class='siteslogan'>Återanvändbara moduler för webbutveckling med PHP</span>
EOD;

$erogami['footer'] = <<<EOD
<footer><span class='sitefooter'>Copyright (c) Alexander Chau Nguyen | <a href='https://github.com/mosbth/Anax-base'>Erogami på GitHub</a> | <a href='http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance'>Unicorn</a></span></footer>
EOD;


/**
 * The navbar
 *
 */
//$erogami['navbar'] = null; // To skip the navbar
$erogami['navbar'] = array(
  'class' => 'nb-plain',
  'items' => array(
    'hem'         => array('text'=>'Hem',         'url'=>'hello.php',          'title' => 'Min presentation om mig själv'),
    'redovisning' => array('text'=>'Redovisning', 'url'=>'redovisning.php', 'title' => 'Redovisningar för kursmomenten'),
    'kallkod'     => array('text'=>'Källkod',     'url'=>'source.php',      'title' => 'Se källkoden'),
  ),
  'callback_selected' => function($url) {
    if(basename($_SERVER['SCRIPT_FILENAME']) == $url) {
      return true;
    }
  }
);



/**
 * Theme related settings.
 *
 */
//$erogami['stylesheet'] = 'css/style.css';
$erogami['stylesheets'] = array('css/style.css');
$erogami['favicon']    = 'favicon.ico';



/**
 * Settings for JavaScript.
 *
 */
$erogami['modernizr'] = 'js/modernizr.js';
$erogami['jquery'] = '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js';
//$erogami['jquery'] = null; // To disable jQuery
$erogami['javascript_include'] = array();
//$erogami['javascript_include'] = array('js/main.js'); // To add extra javascript files



/**
 * Google analytics.
 *
 */
$erogami['google_analytics'] = 'UA-22093351-1'; // Set to null to disable google analytics
