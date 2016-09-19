<?php

require_once 'includes/main.php';

$thisPage = "Calendar";
$page_title = "DoYouWannaJoin - " . $thisPage;
$page_description = "En Sida för den aktiva";

$run_circuts = get_all_run_circuts();
$requests = get_all_requests();

?>

<?php include("includes/header.php"); ?>

		<div id="calendar" class="container">
        
        	<div id="protected-page">
			<h1>Running calendar</h1>
            <?php 
			
			if (is_array($requests)) {
			
				foreach($requests as $request){
					
					echo "<h2>" . $request['title'] . "</h2>";
					echo "<h4>Time: " . $request['start_time'] . "</h4>";
					
					foreach($run_circuts as $circut){
						
						if($circut['id'] == $request['run_circut_id']){
							echo "<p>Where: " . $circut['title'] . "</p>"; 
						}
						
					}
					
					echo "</br>";
				}
			
			}
			
			?>
            

			</div>
        
        
        
        </div> <!-- END #index.container -->
        
<?php include("includes/footer.php"); ?>