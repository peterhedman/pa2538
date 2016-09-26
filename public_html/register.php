<?php require('includes/config.php');

//if logged in redirect to members page
if( $user->is_logged_in() ){ header('Location: memberpage.php'); }

define('register', TRUE);

//define page title
$title = 'Register';

//include header template
require('includes/header.php');
?>


<div class="container">
	
	<?php include('includes/register-form.php'); ?>

</div>

<?php
//include header template
require('includes/footer.php');
?>
