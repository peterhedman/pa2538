<?php require('includes/config.php');

//if logged in redirect to members page
//if( $user->is_logged_in() ){ header('Location: memberpage.php'); }

// register-form active
define('register', TRUE);

//define page title
$title = 'Home';

//include header template
require('includes/header.php');
?>


<div id="index" class="container">

	<div id="container-1">
            	
                <div id="text-area-index">
            		<p>EN bra jäkla text hamnar i detta området</p>
                </div>
                
            </div> <!-- END #container-1 -->
        
        <?php if( !$user->is_logged_in() ){include('includes/register-form.php');} ?>
        
        </div> <!-- END #index.container -->

</div>

<?php
//include header template
require('includes/footer.php');
?>
