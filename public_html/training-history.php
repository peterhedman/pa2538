<?php require('includes/config.php'); 

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); } 

$userid = $_SESSION['userID'];

//define page title
$title = 'My Trainings';

//include header template
require('includes/header.php'); 
?>

<div class="container member-page">

	<div class="row">

	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			
				<h2><?php echo $title ?></h2>
				<hr>
                <?php
				
				
				echo "<h3>Upcoming Sessions</h3>";
				foreach($trainings as $training){
				
					if($training->getUserID() == $userid){
						
						if($training->getDate() >= date("Y-m-d H:i:s")){
							$address =  $training->getStartAdress();
							$message = "Creator";
							if($training->getParent() != "0"){
								$message = "Participant";
							}
							
							echo $message . " - ";
							echo '<a href="training-single.php?id='.$training->getID().'">From '. $address[0] . ', ' . $address[1] . '</a>';
							echo "</br>";
						} 
					}
				}
				echo "</br><hr>";
				echo "<h3>Previous Sessions</h3>";
				foreach($trainings as $training){
				
					if($training->getUserID() == $userid){
						
						if($training->getDate() < date("Y-m-d H:i:s")){
							$address =  $training->getStartAdress();
							$message = "Creator";
							if($training->getParent() != "0"){
								$message = "Participant";
							}
							
							echo $message . " - ";
							echo '<a href="training-single.php?id='.$training->getID().'">From '. $address[0] . ', ' . $address[1] . '</a>';
							echo "</br>";
						} 
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
