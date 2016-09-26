<?php

require_once 'includes/main.php';

$thisPage = "Login";
$page_title = "DoYouWannaJoin - " . $thisPage;
$page_description = "En Sida för den aktiva";

/*--------------------------------------------------
	Don't show the login page to already logged-in users.
---------------------------------------------------*/
$user = new User();

if($user->loggedIn()){
	redirect('protected.php');
}


?>

<?php include("includes/header.php"); ?>

		<div id="index" class="container">
        
        <?php include("login-form.php"); ?>
        
        </div> <!-- END #index.container -->
        
<?php include("includes/footer.php"); ?>
		