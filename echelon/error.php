<?php
error_reporting(E_ALL ^ E_NOTICE);
$page = "error";
$page_title = "Error";
$b3_conn = false;
$pagination = false;
require 'inc/ctracker.php';
require 'inc/functions.php';
require 'inc/header.php'; 
?>

<h1>Echelon Error!</h1>
	
	<img src="images/error.jpg" alt="Error!" class="float-left" />
	
	<div class="error-msg error">
	<?php if($_GET['t'] == 'locked')
		echo '<h3>Locked Out!</h3><p>You have been locked out of Echelon for repeated hacking attempts. This ban is permanent. If you feel that you should not be banned please contact the site admin.</p>';
	?>
	</div>
	
	<br class="clear" />
	
<?php require 'inc/footer.php'; ?>