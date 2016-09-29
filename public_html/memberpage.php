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
   	
	$stmt = $db->prepare("DELETE FROM users WHERE userID = :userID");
	$stmt->execute(array(':userID' => $userid));
	/*$stmt = $db->prepare("DELETE FROM trainingsession WHERE user_id  = :user_id ");
	$stmt->execute(array(':user_id ' => $userid));*/
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


//include header template
require('includes/header.php'); 
?>

<div class="container member-page">

	<div class="row">

	    
			
				<h2>Member only page - Welcome <?php echo $_SESSION['username']; ?></h2>
				
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
            <form enctype="multipart/form-data" action="uploader.php" method="POST">
            <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
            Choose a file to upload: <input name="uploadedfile" type="file" />
            <input type="submit" value="Upload File" />
            </form>
            <h4>All uploaded GMX </h4>
            <?php $files = scandir('uploads'); 
			
			foreach($files as $file)
			{
				echo $file;
				echo "</br>";
			}
			
			
			
			?>
	
    
    </div>
	


</div>

<?php 
//include header template
require('includes/footer.php'); 
?>
