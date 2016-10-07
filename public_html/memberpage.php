<?php require('includes/config.php'); 

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); } 

//define page title
$title = 'Members Page';

$userid = $_SESSION['userID'];
$remove_user = isset($_POST['remove_user']) ? $_POST['remove_user'] : 0;
$rank = isset($_POST['rank']) ? $_POST['rank'] : 0;
$custom = isset($_POST['custom']) ? $_POST['custom'] : 0;
$update_rank = isset($_POST['update_rank']) ? $_POST['update_rank'] : 0;

error_log("rank: " . $rank);

if (!empty($remove_user)) {
	// IF is parent needs to fix new owner before deletion
	try {
		//gets all trainingsessions of user
		$stmt = $db->prepare('SELECT * FROM trainingsession WHERE user_id = :userID');
		$stmt->execute(array(
		':userID' => $userid
		));
		$all_this_user_sessions = $stmt->fetchAll();
		
		
		foreach($all_this_user_sessions as $session_single){
		
			if($session_single["parent_session"] == 0){
				
				$counter = 0;
				//Gets all participant of this session
				$stmt = $db->prepare('SELECT u.email, u.userID FROM users AS u INNER JOIN trainingkeeper AS t ON u.userID = t.users WHERE t.trainingsession = :trainingID');
				$stmt->execute(array(
						':trainingID' => $session_single["id"]
						));	
				$single_participants = $stmt->fetchAll();
				
				//gets id of latest participant
				foreach ($single_participants as $participants ) {
					if($participants["userID"] != $userid){
						$secundUserID = $participants["userID"];	
					}
					
					$counter++;
				}
				
				if($counter > 1){
				
					//deletes the current user from trainingsession and training keeper
					$stmt = $db->prepare("DELETE FROM trainingsession WHERE id = :session_id AND user_id = :user_id");
					$stmt->execute(array(
					':user_id' => $userid,
					':session_id' => $session_single["id"]
					));
					
					//gets the row id of the latest participant if this trainingsession
					$stmt = $db->prepare('SELECT id FROM trainingsession WHERE user_id = :userID AND parent_session = :parentIDCurrent');
					$stmt->execute(array(
					':parentIDCurrent' => $session_single["id"],
					':userID' => $secundUserID
					));
					$single_new_user_row = $stmt->fetch();
					$single_new_user_id = $single_new_user_row['id'];
					
					//Insert the new owenr into traininh keeper
					$stmt = $db->prepare('INSERT INTO trainingkeeper (users,trainingsession) VALUES (:users, :trainingsession)');
					$stmt->execute(array(
					':users' => $secundUserID,
					':trainingsession' => $single_new_user_id
					));
					
					//Updates the latest participant to session owenr
					$stmt = $db->prepare('UPDATE trainingsession SET parent_session = :parentID WHERE user_id = :userID AND parent_session = :parentIDCurrent');
					$stmt->execute(array(
					':parentID' => 0,
					':parentIDCurrent' => $session_single["id"],
					':userID' => $secundUserID
					));
					
					//Sets new parent on the children
					$stmt = $db->prepare('UPDATE trainingsession SET parent_session = :parentID WHERE parent_session = :parentIDCurrent');
					$stmt->execute(array(
					':parentID' => $single_new_user_id,
					':parentIDCurrent' => $session_single["id"]
					));
					
					//inserts the children in trainingkeeper again
					foreach ($single_participants as $participants ) {
						if($participants["userID"] != $userid && $participants["userID"] != $single_new_user_id){
							$stmt = $db->prepare('INSERT INTO trainingkeeper (users,trainingsession) VALUES (:users, :trainingsession)');
							$stmt->execute(array(
							':users' => $participants["userID"],
							':trainingsession' => $single_new_user_id
							));
						}
					}
				}
			
			}
			
		}
	
	} catch(PDOException $e) {
		echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		error_log("error: " . $e->getMessage());
	}
	
	try {
		//AND finaly delete from users
		$stmt = $db->prepare("DELETE FROM users WHERE userID = :userID");
		$stmt->execute(array(':userID' => $userid));
		
	} catch(PDOException $e) {
		echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		error_log("error: " . $e->getMessage());
	}
	
	$user->logout();
	header('Location: index.php');
}

/*
if (isset($_POST['update_rank'])) {
	
	$beginner = isset($_POST['beginner']) ? $_POST['beginner'] : 0;
	$medium = isset($_POST['medium']) ? $_POST['medium'] : 0;
	$advanced = isset($_POST['advanced']) ? $_POST['advanced'] : 0;
	$custom = isset($_POST['custom']) ? $_POST['custom'] : 0;
*/


if(!empty($update_rank))
{
	error_log("rank: " . $rank);
	error_log("userid: " . $userid);

	if(!empty($rank) && empty($custom))
	{
		if($rank == "2"){
			$pace = 1.6;
			
		} else if($rank == "1"){
			$pace = 1.3;
			
		} else {
			$pace = 1;
			
		}
		
		try {
			$stmt = $db->prepare("UPDATE users SET rank = :rank, pace = :pace WHERE userID = :userID");
			$stmt->execute(array(
			':userID' => $userid,
			':rank' => $rank,
			':pace' => $pace
			));
		
		} catch(PDOException $e) {
			echo '<p class="bg-danger">'.$e->getMessage().'</p>';
			error_log("error: " . $e->getMessage());
		}
	}
	
	
	if(!empty($custom))
	{
		$stmt = $db->prepare("UPDATE users SET rank = :rank, pace = :pace WHERE userID = :userID");
		$stmt->execute(array(
		':userID' => $userid,
		':rank' => 3,
		':pace' => $custom
		));
	}
	
}
	
	
//}

/*
$stmt = $db->prepare("UPDATE trainingsession SET start_address = :start_address WHERE start_location = :start_location");
	$stmt->execute(array(
		':start_address' => $map_adress,
		':start_location' => $start_adress_pos
	));

*/
$counter_own_sessions = 0;
$counter_join_sessions = 0;
$total_participation_distance = 0;

foreach($trainings as $training){
	if($training->getUserID() == $userid){
		
		if($training->getParent() == 0){
			$counter_own_sessions++;
		}else{
			$counter_join_sessions++;
		}
		
		$total_participation_distance = $total_participation_distance + $training->getDistance();
		
	}
}



//include header template
require('includes/header.php'); 
?>

<div class="container member-page">

	<div class="row">

	    
			
				<h2>Welcome <?php echo $_SESSION['username']; ?></h2>
                
                <hr>
				
                <h2>Statistics</h2>
                <?php
                echo "Created sessions: " . $counter_own_sessions . "</br>";
				echo "Joined Sessions: " . $counter_join_sessions . 	"</br>";
				echo "Total participation distance: " . $total_participation_distance . " m</br>";	
                
				?>
				<hr>
				
                <h2>Update pace</h2>
                <p>The pace is multiplied with the defualt speed of the activities </p>
                <p>	Walking: 2 m/s</br>
                	Running: 4 m/s</br>
                    Bicycling : 7 m/s</p>
                
                <?php $rank = $user->getRank(); ?>
                <form id="pace-editor" method='post'>
					
                    <label for="beginner">Beginner (*1): </label>
                    <input type="radio" name="rank"
					<?php if (isset($rank) && $rank=="0") echo "checked";?>
                    value="0">
                    </br>
                    <label for="medium">Medium (*1.3): </label>
                    <input type="radio" name="rank"
                    <?php if (isset($rank) && $rank=="1") echo "checked";?>
                    value="1">
                    </br>
                    <label for="advanced">Advanced (*1.6): </label>
                    <input type="radio" name="rank"
                    <?php if (isset($rank) && $rank=="2") echo "checked";?>
                    value="2">
                    </br>
                    <label for="custom">Custom: </label>
                    <input id="custom" name="custom" type="text" placeholder="<?php echo $user->getPace(); ?>" value="" />
                	</br>
                    <input type="submit" name="update_rank" value="Update" />
                </form>
                </br>
                <hr>
            <h2>Delete Your account</h2>
            <p>Delete your account by clicking the button below</p>
            <form name='delete_user' method='post'>
			<input type='submit' class='confirm' name='remove_user' value='Delete'</br>
			</form>
            
            <br />
            <hr>
            <h2>Upload GMX</h2>
            <p>Remember the filename</p>
            <!-- Button to select & upload files -->
            <span class="btn btn-success fileinput-button">
            <span>Select files...</span>
            <!-- The file input field used as target for the file upload widget -->
            <input id="fileupload" type="file" name="files[]" multiple>
            </span>
            
            <!-- The global progress bar -->
            <p>Upload progress</p>
            <div id="progress" class="progress progress-success progress-striped">
            <div class="bar"></div>
            </div>
            
            <!-- The list of files uploaded -->
            <p>Files uploaded:</p>
            <ul id="files"></ul>
            
            
            <h4>All uploaded GPX </h4>
            <?php $files = scandir('files'); 
            
			$counter = 0;
            foreach($files as $file)
            {
				$counter++;
				if($counter > 2){
					echo $file;
            		echo "</br>";
				}
            
            }
			
			
			
			?>
	
    
    </div>
	


</div>

<?php 
//include header template
require('includes/footer.php'); 
?>
