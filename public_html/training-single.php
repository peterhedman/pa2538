<?php require('includes/config.php'); 

//if not logged in redirect to login page

$userid = $_SESSION['userID'];

//define page title
$title = 'Training Session';

if(isset($_GET['id'])){
	$id = $_GET['id'];
	
	foreach($trainings as $training){

		if($training->getID() == $id){
			$address =  $training->getStartAdress();
			
			$title_of_training = 'From '. $address[0];
			$title = "Training " . $title_of_training;
		}
		
	}
	
}

//include header template
require('includes/header.php'); 
?>

<div class="container member-page map-on-single">

	<div class="row">

	    <!--<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">-->
			
				<h2><?php echo $title_of_training ?></h2>
                
                <?php foreach($trainings as $training){
					if($training->getID() == $id){
					
						if($user->is_logged_in()){ 
							$currentUserInfo = "user_logged_in";
							if($training->getUserID() == $userid){
								$currentUserInfo = "current_user_event";
							}  
						} else {
							$currentUserInfo = "guest";
						}
						
						
						
					//echo "Creator: " . $training->getUserID() . " - Satus: " . $currentUserInfo . " - userid: " . $userid;
					?>
					
                	
					
					
					<?php 
					
						//echo "För ID: ". $training->getID() . " har points: " . $pointsArray[0];
					
					?>
                    <div id="location_target" style="display: none;">
					<?php 
					$pointsArray = $training->getAllPoints();
					array_push($pointsArray,$currentUserInfo);
					echo json_encode($pointsArray); //sends the locations to javascript
					?>
               		</div>
                    
                    <div id="map-single"></div>
                    <div id="right-panel">
                      <p>Total Distance: <span id="total"></span></p>
                    </div>
                    
                    <?php if($currentUserInfo == "current_user_event"){ ?>
                    
                    <form id="update-route">
                
                    <label for="date">Date: </label>
                    <input id="date" name="date" type="text" placeholder="MM-DD" value="<?php echo $training->getDateOnly() ?>" />
                    
                    <label for="time">Time: </label>
                    <input id="time" name="time" type="text" placeholder="HH:MM" value="<?php echo $training->getTime() ?>" />
                    
                    
                    <?php $type = $training->getType() ?>
                    <label for="walking">Running: </label>
                    <input type="radio" name="type"
					<?php if (isset($type) && $type=="walking") echo "checked";?>
                    value="walking">
                    <label for="bicycling">Bicycling: </label>
                    <input type="radio" name="type"
                    <?php if (isset($type) && $type=="bicycling") echo "checked";?>
                    value="bicycling">
                
                    <input type="submit" value="Update route" />
                </form>

						
						
				<?php } else { // IF logged in or guest 
					
					echo "Start date: " . $training->getDateOnly() . "</br>Start Time: " . $training->getTime() . "</br>Type: " . $training->getType();
					
				}
					}
					
				}?>
                
				<hr>

		<!--</div>-->
	</div>


</div>

<?php 
//include header template
require('includes/footer.php'); 
?>
