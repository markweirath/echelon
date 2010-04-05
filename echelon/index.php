<?php
$page = "home";
$page_title = "Home";
$auth_name = 'login';
$auth_user_here = true;
$b3_conn = false;
$pagination = false;
require 'inc.php';

require 'inc/header.php';
?>
	<h1>Welcome to Echelon <small>v.2.0a</small></h1>
	 
	<div class="error-msg error" style="float: none;margin:15px auto;width:90%;padding:5px;">
		<p><strong>Note:</strong> This site uses a backup of a B3 Database that is more than 6 months old.</p>
	</div>

	<div id="change-log">	   
		<h3>Changelog v.2.0a</h3>
		
		<ul>
			<li>Better user management, allowing more flexible permissions for Echelon users and managers</li>
			<li>IP Blacklist, allows troublesome people to be banned from accessing the Echelon site</li>
			<li>Echelon connect, lets people connect their B3 client with their Echelon user</li>
			<li>Editable settings lets Echelon managers keep things simple</li>
			<li>Regular visitors page shows a list of people who are not clan members and who connect regularly.</li>
			<li>In-active admins page shows a list of admins who have not connected to the servers in a while</li>
			<li>Multi server for a single DB support. This allows you to associate mutliple servers to one DB. This gives people who use this technique when setting up B3 more flexibility for chat logs and banning and the like</li>
			<li>Ability to change a client's mask, greeting, login details, edit a ban deatils</li>
			<li>Security: Anti-session hijacking and fixation, tokens to stop CSRF attacks, prepared statments to prevent SQL injection. Making your Echelon expirence more secure allowing you to protect both you and your clients</li>
		</ul>
	</div
	
	<br class="clear" />

	<p class="last-seen">You were last seen with this <?php $ip = ipLink($_SESSION['last_ip']); echo $ip; ?> IP address,<br />
		<?php $mem->lastSeen('l, jS F Y (H:i)'); ?>
	</p>
	
<?php require 'inc/footer.php'; ?>