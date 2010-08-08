<?php
$page = 'error';
$page_title = "Error";
$b3_conn = false; // no b3 connection is required
$auth_user_here = false; // allow both logged in and logged out users to see this page
$pagination = false;
require 'inc.php';

require 'inc/header.php';
?>

<h1>Echelon Error!</h1>
	
	<img src="images/error.jpg" alt="Error!" class="float-left" />
	
	<div class="error-msg error">
	<?php 
		if($_GET['t'] == 'locked') {
			echo '<h3>Locked Out!</h3><p>You have been locked out of Echelon for repeated hacking attempts. 
					This ban is permanent. If you feel that you should not be banned please contact the site admin.</p>';
		} elseif($_GET['t'] == 'ie') {
			echo '<h3>Internet Explorer Banned!</h3><p>Microsoft Internet Explorer is banned from this site due to secuirty concerns. Please choose a modern browser,
					such as Mozilla Firefox, Google Chrome, Apple Safari, or Opera. Thank-you.</p>';
		} elseif($_GET['t'] == 'plug') {
			echo '<h3>Plugin Page Failure</h3><p>The last page you requested requires that a plugin name be sent in the request. You did not sent one.</p>';
		} elseif($_GET['t'] == 'plugpage') {
			echo '<h3>Plugin Page Failure</h3><p>That plugin does not have a stand-alone page.';
		} elseif($_GET['t'] == 'ssl') {
			echo '<h3>SSL Connection Required</h3><p>An SSL connection is required for this site, and you did not seem to have one.</p>';
		} else {
			echo '<h3>Error! Error!</h3><p>Something seems to have gone wrong! A team of highly trained monkeys have been dispatched, in an attempt to fix the problem.</p>';
		}
	?>
	</div>
	
	<br class="clear" />
	
<?php require 'inc/footer.php'; ?>