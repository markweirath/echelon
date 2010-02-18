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
	       
	<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>

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

	<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>

	<p class="last-seen">You were last seen with this <?php $ip = ipLink($_SESSION['last_ip']); echo $ip; ?> IP address,<br />
		<?php echo time_duration($time_last_seen, 'Mwdhm'); ?> ago. 
	</p>
	
<?php require 'inc/footer.php'; ?>