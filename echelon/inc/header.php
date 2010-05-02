<?php
## Pagination for pages with tables ##
if($pagination == true) : // if pagination is needed on the page
	## Find total rows ##
	$result_rows = $db->mysql->query($query);
	$total_rows = $result_rows->num_rows;
	$result_rows->close();
	
	// create query_string
	$query_string_page = queryStringPage();
	$total_pages = totalPages($total_rows, $limit_rows);
endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<title><?php echo $site_name; ?> Echelon - <?php echo $page_title; ?></title>
	
	<!-- Load CSS Stylesheet -->
	<link href="<?php echo $path; ?>css/style.css" rel="stylesheet" media="screen" type="text/css" />
	
	<?php 
	## Include CSS For pages ##
	if(isLogin($page))
		css_file('login');

	if(isCD($page))
		css_file('cd');
		
	if(isSettings($page))
		css_file('settings');
		
	if(isHome($page))
		css_file('home');
	
	## Header JS for Map Page ##
	if(isMap($page))
		echo $map_js;
		
	?>
	
	<!-- ALL JS TO BE LOADED INTO THE FOOTER -->
</head>

<body id="<?php echo $page; ?>">
		
<div id="page-wrap">
	
<div id="header">
	<a name="t"></a>
	<h1 id="title"><a href="<?php echo $path; ?>" title="Go to the home page">Echelon</a></h1>
	<h2 id="subtitle">B3 repository and investigation tool</h2>		
</div><!-- end #header -->
		
						
<div id="mc">

	<div id="menu">
	
		<ul id="nav">
			<?php if($mem->loggedIn()) { ?>
			
				<li class="home<?php if(isHome($page)) echo ' selected'; ?>"><a href="<?php echo $path; ?>" title="Home Page">Home</a></li>
				
				
				<li class="cdd">
					<a href="#">Games</a>
					<ul class="dd games-list">
						<?php
							$this_cur_page = basename($_SERVER['SCRIPT_NAME']);						
							$games_list = $dbl->getGamesList();
							$i = 0;
							$count = count($games_list);
							$count--; // minus 1
							while($i <= $count) :
	
								if($game == $games_list[$i]['id'])
									echo '<li class="selected">';
								else
									echo '<li>';
								echo '<a href="'.$path . $this_cur_page .'?game='.$games_list[$i]['id'].'" title="Switch to this game">'.$games_list[$i]['name'].'</a></li>';
								
								$i++;
							endwhile;
						?>	
					</ul>
				</li>
				
				
				<?php if($mem->reqLevel('clients')) : ?>
				<li class="cdd">
					<a href="#">Clients</a>
					<ul class="dd">
						<li class="n-clients<?php if(isClients($page)) echo ' selected'; ?>"><a href="<?php echo $path; ?>clients.php" title="Clients Listing">Clients</a></li>
						<li class="n-active<?php if($page == 'active') echo ' selected'; ?>"><a href="<?php echo $path; ?>active.php" title="In-active admins">In-active Admins</a></li>
						<li class="n-regular<?php if($page == 'regular') echo ' selected'; ?>"><a href="<?php echo $path; ?>regular.php" title="Regular non admin visitors to your servers">Regular Visitors</a></li>
						<li class="n-admins<?php if($page == 'admins') echo ' selected'; ?>"><a href="<?php echo $path; ?>admins.php" title="A list of all admins">Admin Listing</a></li>
						<li class="n-world<?php if(isMap($page)) echo ' selected'; ?>"><a href="<?php echo $path; ?>map.php" title="Player map">World Player Map</a></li>
					</ul>
				</li>
				<?php
					endif; // reqLevel clients DD
					
					
					if($mem->reqLevel('penalties')) :
				?>
				<li class="cdd">
					<a href="#">Penalties</a>
					<ul class="dd">
						<li class="n-adminkicks<?php if($page == 'adminkicks') echo ' selected'; ?>"><a href="<?php echo $path; ?>adminkicks.php">Admin Kicks</a></li>
						<li class="n-adminbans<?php if($page == 'adminbans') echo ' selected'; ?>"><a href="<?php echo $path; ?>bans.php?t=a">Admin Bans</a></li>
						<li class="n-b3pen<?php if($page == 'b3pen') echo ' selected'; ?>"><a href="<?php echo $path; ?>bans.php?t=b" title="All Kicks/Bans added automatically by B3">B3 Bans</a></li>
						<li class="n-pubbans<?php if(isPubbans($page)) echo ' selected'; ?>"><a href="<?php echo $path; ?>pubbans.php" title="A public list of bans in the database">Public Ban List</a></li>
					</ul>
				</li>
				<?php
					endif; // end reqLevel penalties DD
				?>
				
				
				<li class="cdd">
					<a href="#">Other</a>
					<ul class="dd">
						<li class="n-pbss<?php if($page == 'pbss') echo ' selected'; ?>"><a href="<?php echo $path; ?>clients.php" title="Punkbuster&trade; screenshots">PBSS</a></li>
						<li class="n-chat<?php if($page == 'chat') echo ' selected'; ?>"><a href="<?php echo $path; ?>clients.php" title="Logs of chats from the servers">Chat Logs</a></li>
						<?php if($config['games'][$game]['plugins']['ctime']['enabled'] == 1) : /* if the plugin is enabled show the link */ ?>
							<li class="n-ctime<?php if($page == 'ctime') echo ' selected'; ?>"><a href="<?php echo $path; ?>ctime.php" title="Records of how long people are spending on the server">Current Activity</a></li>
						<?php endif; ?>
						<li class="n-notices<?php if($page == 'notices') echo ' selected'; ?>">
							<a href="<?php echo $path; ?>notices.php" title="In-game Notices">Notices</a>
						</li>
					</ul>
				</li>
				
				
				<li class="cdd">
					<a href="#">Echelon</a>
					<ul class="dd">
					
						<?php if($mem->reqLevel('manage_settings')) : ?>
							<li class="cdd-2 n-settings <?php if(isSettings($page)) echo 'selected'; ?>">
								<a href="<?php echo $path; ?>settings.php">Site Settings</a>
								
								<ul class="dd-2">
									<li class="<?php if(isSettingsGame($page)) echo 'selected'; ?>">
										<a href="<?php echo $path; ?>settings-games.php" title="Game Settings">Game Settings</a>
									</li>
									<li class="<?php if(isSettingsServer($page)) echo 'selected'; ?>">
										<a href="<?php echo $path; ?>settings-server.php" title="Server Settings">Server Settings</a>
									</li>
								</ul>
							</li>
						<?php endif; ?>
						
						<?php if($mem->reqLevel('siteadmin')) : ?>
							<li class="n-sa<?php if(isSA($page)) echo ' selected'; ?>">
								<a href="<?php echo $path; ?>sa.php" title="Site Administration">Site Admin</a>
							</li>
						<?php endif; ?>
						
						<li class="n-me<?php if(isMe($page)) echo ' selected'; ?>">
							<a href="<?php echo $path; ?>me.php" title="Edit your account">My Account</a>
						</li>
					</ul>
				</li>			
				
			<?php } else { ?>
			
				<li class="login<?php if(isLogin($page)) echo ' selected'; ?>"><a href="<?php echo $path; ?>login.php" title="Login to Echelon to see the good stuff!">Login</a></li>
				<li class="pubbans<?php if(isPubbans($page)) echo ' selected'; ?>"><a href="<?php echo $path; ?>pubbans.php" title="Public Ban List">Public Ban List</a></li>
				
			<?php } ?>
		</ul><!-- end #nav -->
		
		<div id="user-info">
			<?php if($mem->loggedIn()) { ?>
				<div class="log-cor">
					<a href="<?php echo $path; ?>actions/logout.php" class="logout" title="Sign out">Sign Out</a>
				</div>
			<?php } ?>
			
			<div class="info">
				<?php 
					$grav = $mem->getGravatar($_SESSION['email']); 
					echo $grav;
				?>
				<span class="display-name"><?php $mem->displayName(); ?></span>
				<?php if($mem->loggedIn()) {
					echo '<span class="last-seen">';
						$mem->lastSeen();
					echo '</span>';	
				} ?>
			</div>
			
		</div><!-- end #user-info -->
		
		<br class="clear" />
		
	</div><!-- end #menu -->
		
	<div id="content">
	
	<?php 
	
		## if Site Admin check for current Echelon Version and if not equal add warning
		if($mem->reqLevel('see_update_msg')) :
			if(isSA($page) || isSettings($page) || isHome($page)) {
				$latest = getEchVer();
				if(ECH_VER !== $latest) // if current version does not equal latest version show warning message
					set_warning('You are not using the lastest version of Echelon, please check the <a href="http://www.bigbrotherbot.com/forums/" title="Check the B3 Forums">B3 Forums</a> for more information.');
			}
		endif;
		
		errors(); // echo out all errors/success/warnings
	?>