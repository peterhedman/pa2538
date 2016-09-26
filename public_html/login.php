<?php
//include config
require_once('includes/config.php');

//check if already logged in move to home page
if( $user->is_logged_in() ){ header('Location: memberpage.php'); } 

define('login', TRUE);

//define page title
$title = 'Login';

//include header template
require('includes/header.php'); 
?>

	
<div class="container">

	<?php include('includes/login-form.php');
	
	 ?>



</div>


<?php 
//include header template
require('includes/footer.php'); 
?>
