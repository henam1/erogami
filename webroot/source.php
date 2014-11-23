<?php 
/**
 * This is a Erogami pagecontroller.
 *
 */
// Include the essential config-file which also creates the $erogami variable with its defaults.
include(__DIR__.'/config.php'); 


// Add style for csource
$erogami['stylesheets'][] = 'css/source.css';


// Create the object to display sourcecode
//$source = new CSource();
$source = new CSource(array('secure_dir' => '..', 'base_dir' => '..'));


// Do it and store it all in variables in the Erogami container.
$erogami['title'] = "Visa källkod";

$erogami['main'] = "<h1>Visa källkod</h1>\n" . $source->View();


// Finally, leave it all to the rendering phase of Erogami.
include(EROGAMI_THEME_PATH);

