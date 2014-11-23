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

if(isset($_POST['login'])) 
{ 
    $user->login($_POST['acronym'], $_POST['password']);
    header('Location: login.php');  
} 

// Do it and store it all in variables in the Erogami container.
$erogami['title'] = "Login";

$erogami['main'] = <<<EOD
<article class = 'justify readable'>
<h1>{$erogami['title']}</h1>

<form method=post>
  <fieldset>
  <legend>Login</legend>
  <p><em>Du kan logga in med doe:doe eller admin:admin.</em></p>
  <p><label>Användare:<br/><input type='text' name='acronym' value=''/></label></p>
  <p><label>Lösenord:<br/><input type='text' name='password' value=''/></label></p>
  <p><input type='submit' name='login' value='Login'/></p>
  <output><b>{$output}</b></output>
  <p><a href='logout.php'>Logout</a> <a href='status.php'>Status</a></p>
  </fieldset>
</form>
</article>
EOD;



// Finally, leave it all to the rendering phase of Erogami.
include(EROGAMI_THEME_PATH);