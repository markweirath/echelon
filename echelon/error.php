<?php
$page = "error";
$page_title = "Error";
$b3_conn = false; // no b3 connection is required
$auth_user_here = false; // allow both logged in and logged out users to see this page
$pagination = false;
require 'inc.php';

require 'inc/header.php';
?>

<h1>Echelon Error!</h1>
	
	<img src="images/error.jpg" alt="Error!" class="float-left" />
	
	<?php if($_GET['t'] == 'locked') {
			echo '<div class="error-msg error"><h3>Locked Out!</h3><p>You have been locked out of Echelon for repeated hacking attempts. 
					This ban is permanent. If you feel that you should not be banned please contact the site admin.</p></div>';
		} elseif($_GET['t'] == 'ie') {
			echo '<div class="error-msg error"><h3>Internet Explorer Banned!</h3><p>Microsoft Internet Explorer is banned from this site due to secuirty concerns. Please choose a modern browser,
					such as Mozilla Firefox, Google Chrome, Apple Safari, or Opera. Thank-you.</p></div>';
		}
	?>
	
	<br class="clear" />
	
<?php require 'inc/footer.php'; ?>