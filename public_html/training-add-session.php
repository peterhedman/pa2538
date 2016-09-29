<?php require('includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); } 

//define page title
$title = 'Add New Training';


//gets the ajax request
$data = isset($_POST['waypoints']) ? $_POST['waypoints'] : 0;
$tot_distance = isset($_POST['totalDistance']) ? $_POST['totalDistance'] : 0;
$form_data = isset($_POST['FormData']) ? $_POST['FormData'] : 0;
$map_adress = isset($_POST['Adress']) ? $_POST['Adress'] : 0;
$start_adress_pos = isset($_POST['latlng']) ? $_POST['latlng'] : 0;

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

if(!empty($data))
{	 
	$object_array = json_decode($data, true);
	foreach($object_array as $object)
	{
		$counter++;
		array_push($waypoints, '{"lat":'.$object["lat"].',"lng":'.$object["lng"].'}');
	}
	
	$first_pos = json_encode($object_array[0]);
	$end_pos = json_encode(end($object_array));
	
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
		
		$stmt = $db->prepare('INSERT INTO trainingsession (user_id,date,time,start_location,end_location,waypoints,type,distance,default_speed) VALUES (:user_id, :date, :time, :start_location,:end_location, :waypoints, :type, :distance, :default_speed)');
		$stmt->execute(array(
		':user_id' => $userid,
		':date' => $date,
		':time' => $time,
		':start_location' => $first_pos,
		':end_location' => $end_pos,
		':waypoints' => $waypoint_string,
		':distance' => $tot_distance,
		':type' => $type,
		':default_speed' => $defualtSpeed
		));
		
		$id = $db->lastInsertId('id');
		$stmt = $db->prepare('INSERT INTO trainingkeeper (users,trainingsession) VALUES (:users, :trainingsession)');
		$stmt->execute(array(
		':users' => $userid,
		':trainingsession' => $id
		));
		
	
	} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
			error_log("error: " . $e->getMessage());
		}
		
}

$target_path = "uploads/";

$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
    " has been uploaded";
} else{
    echo "There was an error uploading the file, please try again!";
}



/*
//HEADER DONT WORK...... REDIRECT VIA JAVASCRIPT INSTEAD
if(isset($_POST['date']) && !empty($_POST['date'])){
	header('location:training-single.php?id='.$id); 
	exit(); 
}
*/



//include header template
require('includes/header.php'); 
?>

<div class="container member-page add-session-page">

	<div class="row">

	    <!-- <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3"> -->
			
				<h2><?php echo $title ?></h2>
				<hr>
                
                <!-- <div id="map"></div> -->
				<div id="map"></div>
                
                  <p>Total Distance: <span id="total"></span></p>
               
                
                
				
                <form id="done-with-route">
                
                    <label for="date">Date: </label>
                    <input id="date" name="date" type="text" placeholder="MM-DD" value="" />
                    
                    <label for="time">Time: </label>
                    <input id="time" name="time" type="text" placeholder="HH:MM" value="" />
                    
                    
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
                
                    <input type="submit" value="I'm done with the route" />
                </form>
                 </br>
                 </br>
                  
                  <h3>Add GMX path to map </h3>
                  <p>You have to first upload the GMX file att your memberpage, and then copy the filename in the box below</p>
                      
                 <form id="include-gpx-file">
                    
                    <input type="text" name="gmx_path" value="" />
 
                    <input type="submit" value="Get GMX" />
                 </form>
                
    			<?php
				
				  // here i would like use foreach:
				  /*
				
				
				
				
                //$details = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=41.43206,-81.38992&destinations=San+Francisco&mode=walking&sensor=false";
				$details = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=41.43206,-81.38992&destinations=San+Francisco&mode=bicycling&sensor=false";

				$json = file_get_contents($details);
			
				$details = json_decode($json, TRUE);
			
				echo "<pre>"; print_r($details); echo "</pre>";
				*/
                ?>

		<!-- </div> -->
	</div>


</div>

<?php 
//include header template
require('includes/footer.php'); 
?>
