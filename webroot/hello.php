<?php 
/**
 * This is a Erogami pagecontroller.
 *
 */
// Include the essential config-file which also creates the $erogami variable with its defaults.
include(__DIR__.'/config.php'); 
 
 
// Do it and store it all in variables in the Erogami container.
$erogami['title'] = "Hello World";
 
$erogami['main'] = <<<EOD
<h1>Hej Världen</h1>
<p>Detta är en exempelsida som visar hur Erogami ser ut och fungerar.</p>
EOD;
 
 
// Finally, leave it all to the rendering phase of Erogami.
include(EROGAMI_THEME_PATH);