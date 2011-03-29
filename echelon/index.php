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
	<h1>Welcome to Echelon <small><?php echo ECH_VER; ?></small></h1>
	
	<?php if($_SESSION['last_seen'] == '' && $_SESSION['username'] == 'admin') : /* Show this message to the admin user (the first user create) only on their first visit */ ?> 
	
		<div class="msg success">
			<p>Welcome to Echelon for the first time, now all you need to do is good to the 'Echelon' tab in the navigation up above. It is suggested that you change the settings, and setup game and server information for Echelon.</p>
		</div>
		
	<?php endif; ?>
	 
	<p class="welcome">Welcome <?php echo $mem->displayName();  if(!$no_games) : ?> You are logged into the &ldquo;<?php echo $game_name; ?>&rdquo; database.<br />
		<small>You can change what game information you would like to see under the 'game' dropdown above.</small><?php endif; ?></p>
	
	<ul class="padd">
		<?php if(!$no_games) : ?><li><a href="clients.php" title="Enter the repositorty and start exploring Echelon">Enter the Respository</a></li><?php endif; ?>
		<li><a href="<?php echo $path; ?>actions/logout.php" class="logout" title="Sign out of Echelon">Log Out</a></li>
	</ul>
	
	<div id="change-log" class="index-block">	   
		<h3>Changelog <?php echo ECH_VER; ?></h3>
		
		<ul>
			<li>A new look: Echelon has gotten a face lift; giving a much cleaner interface for the end user.</li>
			<li>Easy Management: Echelon admins can now edit the majority of all Echelon settings from the Echelon control panel, no more shall admins need to hand out ftp/file access permissions so that admin can edit a setting or add a new game to expand Echelon.</li>
			<li>Multiverse: Echelon now supports multi everything. Many B3 users run multiple B3 instances off the same DB. You can access multiple games or multiple servers from one Echelon.</li>
			<li>New Pages: we have added some more pages to the default Echelon install: Active Admins, see what admins haven't logged on in a while; Regular Users see what users frequent your servers regularly and recently; Admin List, just like the clients page but only for admins.</li>
			<li>IP Blacklist: easily and simply ban people from accessing your Echelon.</li>
			<li>More Things to Do: Admins now have the ability to change a client's mask information, greeting, and edit ban details shortening or lengthening a ban or change the reason.</li>
			<li>Security: anti-session hijacking and fixation, tokens to stop CSRF attacks, prepared statements to prevent SQL injection. Making your Echelon experience more secure allowing you to protect both you and your users.</li>
			<li>Granular Permissions: from the permissions page you can now decide what people can perform what actions.</li>
			<li>Gravatars: select a profile picture for your user with the Gravatar system (Globally Recognised Avatar)</li>
		</ul>
	</div>
	
	<?php
		## External Links Section ##
		$links = $dbl->getLinks();
		
		$num_links = $links['num_rows'];
		
		if($num_links > 0) :
			
			echo '<div id="links-table" class="index-block">
					<h3>External Links</h3>
					<ul class="links-list">';
		
				foreach($links['data'] as $link) :
				
					echo '<li><a href="'. $link['url'] .'" class="external" title="'. $link['title'] .'">' . $link['name'] . '</a></li>';
				
				endforeach;
			
			echo '</ul></div>';
			
		else:
			echo 'no results';
			
		endif;
		## End External Links Section ##
	?>
	
	<br class="clear" />

	<p class="last-seen"><?php if($_SESSION['last_ip'] != '') { ?>You were last seen with this <?php $ip = ipLink($_SESSION['last_ip']); echo $ip; ?> IP address,<br /><?php } ?>
		<?php $mem->lastSeen('l, jS F Y (H:i)'); ?>
	</p>
	
<?php require 'inc/footer.php'; ?>