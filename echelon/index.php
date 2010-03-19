<?php
$page = "home";
$page_title = "Home";
$auth_name = 'login';
require 'inc.php';

require 'inc/header.php';

$time = time();
$time_last_seen = ($time - $_SESSION['last_seen']);
?>
	<h1>Welcome to Echelon <small>v.2.0a</small></h1>
	 
	<div class="error-msg error" style="float: none;margin:15px;width:90%;padding:5px;">
		<p><strong>Note:</strong> This site uses a backup of a B3 Database that is more than 6 months old.</p>
	</div>

	<div id="change-log">	   
		<h3>Changelog v2.0</h3>
		
		<ul>
			<li>Better user management</li>
			<li>IP Blacklist</li>
			<li>Echelon connect</li>
			<li>Editable settings</li>
			<li>Regular visitors page</li>
			<li>In-active admins page</li>
			<li>Multi server for a single DB support</li>
			<li>Ability to change a client's mask, greeting, login details, edit a ban</li>
			<li>Security: Anti-session hijacking and fixation, tokens to stop CSRF attacks, prepared statments to prevent SQL injection.</li>
		</ul>
	</div>

	<p class="last-seen">You were last seen with this <?php $ip = ipLink($_SESSION['last_ip']); echo $ip; ?> IP address,<br />
		<?php $mem->lastSeen('l, jS F Y (H:i)'); ?>
	</p>
	
<?php require 'inc/footer.php'; ?>