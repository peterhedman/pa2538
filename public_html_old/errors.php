<?php

$page_title = "DoYouWannaJoin - 404";
$page_description = "En Sida för den aktiva";


$status = $_SERVER['REDIRECT_STATUS'];
$codes = array(
400 => array('400 Bad Request', 'The request cannot be fulfilled due to bad syntax.'),
403 => array('403 Forbidden', 'The server has refused to fulfil your request.'),
404 => array('404 Not Found', 'The page you requested was not found on this server.'),
405 => array('405 Method Not Allowed', 'The method specified in the request is not allowed for the specified resource.'),
408 => array('408 Request Timeout', 'Your browser failed to send a request in the time allowed by the server.'),
500 => array('500 Internal Server Error', 'The request was unsuccessful due to an unexpected condition encountered by the server.'),
502 => array('502 Bad Gateway', 'The server received an invalid response while trying to carry out the request.'),
504 => array('504 Gateway Timeout', 'The upstream server failed to send a request in the time allowed by the server.'),
);

$title = $codes[$status][0];
$message = $codes[$status][1];

if ($title == false || strlen($status) != 3) {
$message = 'Please supply a valid HTTP status code.';
}
?>

<?php include("includes/header.php"); ?>

		<div id="index" class="container" style="text-align:center;">
        	<?php
					echo '<h1>Hold up! '.$title.' detected</h1>
					<p>'.$message.'</p>';
				?>
        
        	<div id="container-1">
            	
                
            </div> <!-- END #container-1 -->
        
        
        </div> <!-- END #index.container -->
        
<?php include("includes/footer.php"); ?>
		