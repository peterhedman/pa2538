<?php require('includes/config.php'); 

//if not logged in redirect to login page
//if(!$user->is_logged_in()){ header('Location: login.php'); }
$userid = $_SESSION['userID'];

//define page title
$title = 'Training Sessions';


try {
	
	$stmt = $db->prepare('SELECT userID, rank, pace FROM users WHERE active="Yes"');
	$stmt->execute();	
	$all_users = $stmt->fetchAll();
		
} catch(PDOException $e) {
	echo '<p class="bg-danger">'.$e->getMessage().'</p>';
	error_log("error: " . $e->getMessage());
}


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
				
					if($training->getDate() >= date("Y-m-d H:i:s") && $training->getParent() == 0){
						$address =  $training->getStartAdress();
						echo '<a href="training-single.php?id='.$training->getID().'">From '. $address[0] . ', ' . $address[1] . '</a>';
						echo '</br>When: ' . $training->getDateOnly();
						echo '</br>At: ' . $training->getTime();
						echo '</br>Type: ' . $training->getType();
						echo '</br>Distance: ' . $training->getDistance() . ' meters';
						echo '</br>';
						
						foreach($all_users as $users){
							if($training->getUserID() == $users["userID"]){
								
								$rankOutput = "";
								
								if($users["rank"] == 0){
									$rankOutput = "Beginner";
								} else if($users["rank"] == 1){
									$rankOutput = "Medium";
								} else if($users["rank"] == 2){
									$rankOutput = "Advanced";
								} else {
									$rankOutput = "Costum - Pace: " . $users["pace"];
								}
								
								echo 'Rank: ' . $rankOutput;
								echo '</br>';
							}
						}
						
						
						if($training->getUserID() == $userid && $training->getParent() == "0"){
						echo '(You are the session creator)';
						echo '</br>';
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
