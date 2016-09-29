<?php require('includes/config.php'); 

//if not logged in redirect to login page

$userid = $_SESSION['userID'];

//define page title
$title = 'Training Session';


$id = "";

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

//checks if session is a parent, if not redirect to parent
foreach($trainings as $training){
	
	if($training->getParent() != 0 && $training->getID() == $id){
		header('Location: training-single.php?id='.$training->getParent());
	}

}

//Removes the participation
$remove_participant = isset($_POST['remove_participation']) ? $_POST['remove_participation'] : 0;
if (!empty($remove_participant)) {
   	
	$stmt = $db->prepare("DELETE FROM trainingsession WHERE user_id = :user_id");
	$stmt->execute(array(':user_id' => $userid));
	$stmt = $db->prepare("DELETE FROM trainingkeeper WHERE users  = :user_id ");
	$stmt->execute(array(':user_id' => $userid));
	header('Location: training-sessions.php');
}


//gets the ajax request
$data = isset($_POST['waypoints']) ? $_POST['waypoints'] : 0;
$tot_distance = isset($_POST['totalDistance']) ? $_POST['totalDistance'] : 0;
$form_data = isset($_POST['FormData']) ? $_POST['FormData'] : 0;
$map_adress = isset($_POST['Adress']) ? $_POST['Adress'] : 0;
$start_adress_pos = isset($_POST['latlng']) ? $_POST['latlng'] : 0;
$idIn = isset($_POST['id']) ? $_POST['id'] : 0;

$startLoc = isset($_POST['startLoc']) ? $_POST['startLoc'] : 0;
$stopLoc = isset($_POST['stopLoc']) ? $_POST['stopLoc'] : 0;

//gets the form values seperatl
$form_data = $form_data . "&";
preg_match_all('~=(.*?)&~', $form_data, $matches);
$match_array = $matches[1];
$form_date = $match_array[0];
$form_time = $match_array[1];
$form_type = $match_array[2];

$form_time = str_replace("%3A",":",$form_time);
$tot_distance = str_replace(".","",$tot_distance);

//saves all the waypoint inc start and end pos
$counter = 0;
$waypoints = array();
$waypoint_string = "";


//Updates the tabel with the address
if(!empty($map_adress))
{
	//fix for decimals in long and lat
	$start_adress_pos = json_decode($start_adress_pos, true);
	$start_adress_pos = json_encode($start_adress_pos);
	
	error_log("startin: " . $start_adress_pos);
	//update users record set the active column to Yes where the usersID and active value match the ones provided in the array
	$stmt = $db->prepare("UPDATE trainingsession SET start_address = :start_address WHERE start_location = :start_location");
	$stmt->execute(array(
		':start_address' => $map_adress,
		':start_location' => $start_adress_pos
	));
}

if(!empty($form_data))
{	 
	$object_array = json_decode($data, true);
	if(is_array($object_array)){
		foreach($object_array as $object)
		{
			$counter++;
			array_push($waypoints, '{"lat":'.$object["lat"].',"lng":'.$object["lng"].'}');
		}
	
		$first_pos = json_encode($object_array[0]);
		$end_pos = json_encode(end($object_array));
	}
	//error_log("first_pos: " . $first_pos);
	
	//makes a only waypoint string
	$waypoints = array_slice($waypoints, 1, -1);
	foreach($waypoints as $waypoint){
		
		$waypoint_string = $waypoint_string . "," . $waypoint;
	}
	
	$waypoint_string = substr($waypoint_string, 1);
	
	//fixup for mysql format
	$time = $form_time.":00";
	$date = $form_date;
	$userid = $_SESSION['userID'];
	$type = $form_type;
	
	if($type == "bicycling"){
		$defualtSpeed = 7;
	} else if($type == "running"){
		$defualtSpeed = 4;
	} else {
		$defualtSpeed = 2;
	}
	
	//error_log("user: " . $userid . " - start: " . $first_pos . " - end: " . $end_pos . " - Waypoints: " . $waypoint_string . " - Discanxe: " . $tot_distance . " - Date: " . $date . " - Time: " . $time . " - Type: " . $type . " - map_adress: " . $map_adress);
	try {
		
		//error_log("startLoc: " . $startLoc . " stopLoc:" . $stopLoc);
		
		if(!empty($startLoc) && !empty($stopLoc)){
			
			$stmt = $db->prepare('INSERT INTO trainingsession (user_id,date,time,parent_session,join_location,stop_location,start_location,end_location,waypoints,type,distance,default_speed) VALUES (:user_id, :date, :time, :parent_session, :join_location, :stop_location, :start_location,:end_location, :waypoints, :type, :distance, :default_speed) ON DUPLICATE KEY UPDATE join_location = :new_join_location, stop_location = :new_stop_location');
			$stmt->execute(array(
			':user_id' => $userid,
			':date' => $date,
			':time' => $time,
			':parent_session' => $idIn,
			':join_location' => $startLoc,
			':stop_location' => $stopLoc,
			':start_location' => $first_pos,
			':end_location' => $end_pos,
			':waypoints' => $waypoint_string,
			':distance' => $tot_distance,
			':type' => $type,
			':default_speed' => $defualtSpeed,
			':new_join_location' => $startLoc,
			':new_stop_location' => $stopLoc
			));
			
			$stmt = $db->prepare('INSERT INTO trainingkeeper (users,trainingsession) VALUES (:users, :trainingsession)');
			$stmt->execute(array(
			':users' => $userid,
			':trainingsession' => $idIn
		));
			
		}else{
			
			//error_log("id: " . $idIn);
			$stmt = $db->prepare('UPDATE trainingsession SET date = :date, time = :time ,start_location = :start_location, end_location = :end_location, waypoints = :waypoints, type = :type, distance = :distance, default_speed = :default_speed WHERE id = :id');
			$stmt->execute(array(
			':id' => $idIn,
			':date' => $date,
			':time' => $time,
			':start_location' => $first_pos,
			':end_location' => $end_pos,
			':waypoints' => $waypoint_string,
			':distance' => $tot_distance,
			':type' => $type,
			':default_speed' => $defualtSpeed
			));
		
		}
	
		} catch(PDOException $e) {
			echo '<p class="bg-danger">'.$e->getMessage().'</p>';
			error_log("error: " . $e->getMessage());
		}
			
}

$email_invite = isset($_POST['email_invite']) ? $_POST['email_invite'] : 0;
$email_invite_not_reg = isset($_POST['email_invite_not_reg']) ? $_POST['email_invite_not_reg'] : 0;

if(!empty($email_invite)){
	//send email
	$to = $email_invite;
	$subject = "Invatation";
	$body = "<p>Hello you have been invited to participate in a trainingsession.</p>
	<p>Please click to link to join: <a href='".DIR."training-single.php?id=$id'>".DIR."training-single.php?id=$id</a></p>
	<p>Best Regards</p>";

	$mail = new Mail();
	$mail->setFrom(SITEEMAIL);
	$mail->addAddress($to);
	$mail->subject($subject);
	$mail->body($body);
	$mail->send();
}

if(!empty($email_invite_not_reg)){
	//send email
	$to = $email_invite_not_reg;
	$subject = "Invatation of registration";
	$body = "<p>Hello you have been invited to participate in a trainingsession.</p>
	<p>To join the session please register at: <a href='".DIR."register.php'>".DIR."register.php</a></p>
	<p>Best Regards</p>";

	$mail = new Mail();
	$mail->setFrom(SITEEMAIL);
	$mail->addAddress($to);
	$mail->subject($subject);
	$mail->body($body);
	$mail->send();
}

$children;


try {
	$stmt = $db->prepare('SELECT email, userID FROM users');
	$stmt->execute();	
	$user_emails = $stmt->fetchAll();
		
} catch(PDOException $e) {
	echo '<p class="bg-danger">'.$e->getMessage().'</p>';
	error_log("error: " . $e->getMessage());
}
//include header template
require('includes/header.php'); 
?>

<div class="container member-page map-on-single">

	<div class="row">

	    <!--<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">-->
			
				<h2><?php echo $title_of_training ?></h2>
                
                <?php foreach($trainings as $training){
					//gets the current_user start and endlocation
					if($training->getParent() == $id && $training->getUserID() == $userid){
						$joinLoc = $training->getJoinLocation();
						$stopLoc = $training->getStopLocation();
						$children = $training;
					}	
                
				}
                ?>
                
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
						
					?>
                    
                    <div id="location_target" style="display: none;">
					<?php 
					$pointsArray = $training->getAllPoints();
					array_push($pointsArray,$currentUserInfo,  $training->getDate(), $user->getRank(), $training->getDistance(), $training->getType(), $id, $user->getPace(), $training->getDefaultSpeed(), $joinLoc, $stopLoc);
					echo json_encode($pointsArray); //sends the locations to javascript
					?>
               		</div>
                    
                    <div id="map-single"></div>
                    
                    <?php if($currentUserInfo == "current_user_event"){ ?>
                    
                    <form id="update-route">
                
                    <label for="date">Date: </label>
                    <input id="date" name="date" type="text" placeholder="MM-DD" value="<?php echo $training->getDateOnly() ?>" />
                    
                    <label for="time">Time: </label>
                    <input id="time" name="time" type="text" placeholder="HH:MM" value="<?php echo $training->getTime() ?>" />
                    
                    
                    <?php $type = $training->getType() ?>
                    <label for="walking">Walking: </label>
                    <input type="radio" name="type"
					<?php if (isset($type) && $type=="walking") echo "checked";?>
                    value="walking">
                    <label for="running">Running: </label>
                    <input type="radio" name="type"
					<?php if (isset($type) && $type=="running") echo "checked";?>
                    value="running">
                    <label for="bicycling">Bicycling: </label>
                    <input type="radio" name="type"
                    <?php if (isset($type) && $type=="bicycling") echo "checked";?>
                    value="bicycling">
                
                    <input type="submit" value="Update route" />
                </form>
                
                
                
                <table width="100%">
                  <tr>
                    <th>Training Start Time</th>
                    <th>Your End Time</th>
                  </tr>
                  <tr>
                    <td>Date: <?php echo $training->getDateOnly() ?></td>
                    <td>Distance: <span id="distanceEnd"></span></td>
                  </tr>
                  <tr>
                    <td>Time: <?php echo $training->getTime() ?></td>
                    <td>Time: <span id="timeEnd"></span></td>
                  </tr>
                  <tr>
                    <td>Type: <?php echo $training->getType() ?></td>
                    <td>Total time: <span id="totalTime"></span></td>
                    <!-- <td>$100</td> -->
                  </tr>
                </table> 

						
						
				<?php } else if ($currentUserInfo == "user_logged_in") { // IF logged in or guest ?> 
                
                 <table width="100%">
                  <tr>
                    <th>Training Start Time</th>
                    <th>Your Start Time</th>
                    <th>Your End Time</th>
                  </tr>
                  <tr>
                    <td>Date: <?php echo $training->getDateOnly() ?></td>
                    <td>Distance from start: <span id="distanceStart"></span></td>
                    <td>Distance from start: <span id="distanceEnd"></span></td>
                  </tr>
                  <tr>
                    <td>Time: <?php echo $training->getTime() ?></td>
                    <td>Time: <span id="timeStart"></span></td>
                    <td>Time: <span id="timeEnd"></span></td>
                  </tr>
                  <tr>
                    <td>Type: <?php echo $training->getType() ?></td>
                    <td></td>
                    <td>Total time: <span id="totalTime"></span></td>
                  </tr>
                </table> 
					 
                    <?php if(!empty($children)){ ?>
                    </br>
					
                    
                    <h3>Send invatation</h3>
                    <form name='email_invite_not_registred' method='post'>
                    <?php foreach ( $user_emails as $var ) { 
						if($var["userID"] != $userid){ ?>
                    <label for="email_invite"><?php echo $var["email"]; ?> </label>
                    
						<input type="radio" name="email_invite"
						<?php if (isset($email_invite) && $email_invite==$var["email"]) echo "checked";?>
						value="<?php echo $var["email"]; ?>">

					<?php 
						}
					} ?>
                    </br>
                    <input type='submit' class='confirm' name='submit' value='Send'</br>
                    
                    </br>

                    <h4>Inviate non registered user</h4>
                    <form name='email_invite_not_registred' method='post'>
                    <input id="email" name="email_invite_not_reg" type="email" placeholder="Email" value="" />
					<input type='submit' class='confirm' name='submit' value='Send'</br>
					</form>
                    
                    </br>
                    <h3>Delete your participation</h3>
					<form name='delete_participation' method='post'>
					<input type='submit' class='confirm' name='remove_participation' value='Remove Participation'</br>
					</form>

					  
				<?php } else {?>
                
                <form id="save-join-route">
                     
                     <div style="display: none;">
                    <input id="date" name="date" type="text" value="<?php echo $training->getDateOnly() ?>" />
                    <input id="time" name="time" type="text" value="<?php echo $training->getTime() ?>" />	
                    <input id="time" name="time" type="text" value="<?php echo $training->getType() ?>" />	
                     </div>
                     
                     	<input type="submit" value="Join this session" />
                     </form>
                
                
                <?php } ?>
					
				<?php 
				//error_log(print_r($user->getRank()));
				} else {
						echo 'Distance from start: <span id="distanceEnd"></span></br>';
						echo 'Date: ' . $training->getDateOnly() . '</br>';
						echo 'Start time: ' . $training->getTime() . '</br>';
						echo 'Aprox end time: <span id="timeEnd"></span></br>';
						echo 'Total time: <span id="totalTime"></span></br>';
						echo 'Registor or login to see details.';
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
