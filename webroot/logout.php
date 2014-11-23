<?php 
/**
 * This is a Erogami pagecontroller.
 *
 */
// Include the essential config-file which also creates the $erogami variable with its defaults.
include(__DIR__.'/config.php'); 

// Connect to a MySQL database using PHP PDO
$db = new CDatabase($erogami['database']);

$user = new CUser($db);

if($user->IsAuthenticated()){
    
    $output = "Du är inloggad som: {$user->GetName()}";
}
        else {
          $output = "Du är INTE inloggad.";
}

// Logout the user
if(isset($_POST['logout'])) {
  $user->Logout();  
  header('Location: logout.php');
}

// Do it and store it all in variables in the container.
$erogami['title'] = "Logout";

$erogami['main'] = <<<EOD
<article class = 'justify readable'>
<h1>{$erogami['title']}</h1>

<form method=post>
  <fieldset>
  <legend>Logout</legend>
  <p><input type='submit' name='logout' value='Logout'/></p>
  <output><b>{$output}</b></output>
  <p><a href='login.php'>Login</a> <p><a href='status.php'>Status</a></p>
  </fieldset>
</form>
</article>
EOD;



// Finally, leave it all to the rendering phase of Erogami.
include(EROGAMI_THEME_PATH);