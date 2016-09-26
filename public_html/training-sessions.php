<?php require('includes/config.php'); 

//if not logged in redirect to login page
//if(!$user->is_logged_in()){ header('Location: login.php'); }


//define page title
$title = 'Training Sessions';

//include header template
require('includes/header.php'); 
?>

<div class="container member-page">

	<div class="row">

	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			
				<h2><?php echo $title ?></h2>
                <hr>
                <?php
				
				
				
				foreach($trainings as $training){
				
					if($training->getDate() >= date("Y-m-d H:i:s")){
						$address =  $training->getStartAdress();
						echo '<a href="training-single.php?id='.$training->getID().'">From '. $address[0] . ', ' . $address[1] . '</a>';
					}
				
				}
				
				
				?>
                
				

		</div>
	</div>


</div>

<?php 
//include header template
require('includes/footer.php'); 
?>
