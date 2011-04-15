<?php
## if the page has the normal query process & there is a connectionn to the B3 DB
if($query_normal && (!$db_error)) :
	$results = $db->query($query_limit);

	$num_rows = $results['num_rows']; // the the num_rows
	$data_set = $results['data']; // seperate out the return data set
endif;

## Pagination for pages with tables ## 
if($pagination == true && (!$db_error)) : // if pagination is needed on the page
	## Find total rows ##
	$total_num_rows = $db->query($query, false); // do not fetch the data
	$total_rows = $total_num_rows['num_rows'];
	
		$query_string_page = queryStringPage();
	
	// create query_string
	if($total_rows > 0) {

		$total_pages = totalPages($total_rows, $limit_rows);
		
		if($page_no > $total_pages) {
			$db->error = true;
			$db->error_msg = 'That page does not exists, please select a real page.';
		}
	} else
		$total_pages = 0;

endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?php echo $site_name; ?> Echelon - <?php echo $page_title; ?></title>

	<!-- favicon -->
	<link rel="shortcut icon" href="favicon.ico" >
	
	<!-- Load CSS Stylesheet -->
	<link href="<?php echo PATH; ?>css/style.min.css" rel="stylesheet" media="screen" type="text/css" />

	<?php 
	## Include CSS For pages ##
	if(isLogin())
		css_file('login');

	if(isCD())
		css_file('cd');
		
	if(isSettings())
		css_file('settings');
		
	if(isHome())
		css_file('home');
	
	## Header JS for Map Page ##
	if(isMap())
		echo $map_js;
		
	// return any plugin CSS files
	if(!$no_plugins_active)
		$plugins->getCSS();

	?>

	<!-- ALL JS TO BE LOADED INTO THE FOOTER -->
</head>

<body id="<?php echo $page; ?>">

<div id="page-wrap">

<div id="header">
	<a name="t"></a>
	
	<img src="images/logo.png" alt="logo" id="h-img" />
	<h1 id="title"><a href="<?php echo PATH; ?>" title="Go to the home page">Echelon</a></h1>
	<h2 id="subtitle">B3 repository and investigation tool</h2>		
	
</div><!-- end #header -->
	
<div id="mc">

<div id="menu">

	<ul id="nav">
		<?php if($mem->loggedIn()) { ?>

		<li class="home<?php if(isHome()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>" title="Home Page">Home</a></li>

		<?php 
		$games_list = $dbl->getActiveGamesList();
		$count = count($games_list);
		if($count > 0) : ?>

		<li class="cdd">
			<a href="#">Games</a>
			<ul class="dd games-list">
				<?php
					$this_cur_page = basename($_SERVER['SCRIPT_NAME']);						
					$i = 0;
					while($i < $count) :

						if($game == $games_list[$i]['id'])
							echo '<li class="selected">';
						else
							echo '<li>';
						echo '<a href="'.PATH . $this_cur_page .'?game='.$games_list[$i]['id'].'" title="Switch to this game">'.$games_list[$i]['name_short'].'</a></li>';
						
						$i++;
					endwhile;
				?>	
			</ul>
		</li>
		
		<?php if($mem->reqLevel('clients')) : ?>
		<li class="cdd">
			<a href="#">Clients</a>
			<ul class="dd">
				<li class="n-clients<?php if(isClients()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>clients.php" title="Clients Listing">Clients</a></li>
				<li class="n-active<?php if($page == 'active') echo ' selected'; ?>"><a href="<?php echo PATH; ?>active.php" title="In-active admins">In-active Admins</a></li>
				<li class="n-regular<?php if($page == 'regular') echo ' selected'; ?>"><a href="<?php echo PATH; ?>regular.php" title="Regular non admin visitors to your servers">Regular Visitors</a></li>
				<li class="n-admins<?php if($page == 'admins') echo ' selected'; ?>"><a href="<?php echo PATH; ?>admins.php" title="A list of all admins">Admin Listing</a></li>
				<li class="n-world<?php if(isMap()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>map.php" title="Player map">World Player Map</a></li>
			</ul>
		</li>
		<?php
			endif; // reqLevel clients DD
			
			if($mem->reqLevel('penalties')) :
		?>
		<li class="cdd">
			<a href="#">Penalties</a>
			<ul class="dd">
				<li class="n-adminkicks<?php if($page == 'adminkicks') echo ' selected'; ?>"><a href="<?php echo PATH; ?>kicks.php?t=a">Admin Kicks</a></li>
				<li class="n-adminbans<?php if($page == 'adminbans') echo ' selected'; ?>"><a href="<?php echo PATH; ?>bans.php?t=a">Admin Bans</a></li>
				<li class="n-b3bans<?php if($page == 'b3kicks') echo ' selected'; ?>"><a href="<?php echo PATH; ?>kicks.php?t=b" title="All kicks added automatically by B3">B3 Kicks</a></li>
				<li class="n-b3bans<?php if($page == 'b3bans') echo ' selected'; ?>"><a href="<?php echo PATH; ?>bans.php?t=b" title="All bans added automatically by B3">B3 Bans</a></li>
				<li class="n-pubbans<?php if(isPubbans()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>pubbans.php" title="A public list of bans in the database">Public Ban List</a></li>
			</ul>
		</li>
		<?php
			endif; // end reqLevel penalties DD
		?>
		
		<li class="cdd">
			<a href="#">Other</a>
			<ul class="dd">
				<li class="n-notices<?php if($page == 'notices') echo ' selected'; ?>">
					<a href="<?php echo PATH; ?>notices.php" title="In-game Notices">Notices</a>
				</li>
				<?php 
					if(!$no_plugins_active)
						$plugins->displayNav(); 
				?>
			</ul>
		</li>

		<?php endif; // end if no games hide the majority of the navigation ?>

		<li class="cdd">
			<a href="#">Echelon</a>
			<ul class="dd">
			
				<?php if($mem->reqLevel('manage_settings')) : ?>
					<li class="cdd-2 n-settings <?php if(isSettings()) echo 'selected'; ?>">
						<a href="<?php echo PATH; ?>settings.php">Site Settings</a>
						
						<ul class="dd-2">
							<li class="<?php if(isSettingsGame()) echo 'selected'; ?>">
								<a href="<?php echo PATH; ?>settings-games.php" title="Game Settings">Game Settings</a>
							</li>
							<li class="<?php if(isSettingsServer()) echo 'selected'; ?>">
								<a href="<?php echo PATH; ?>settings-server.php" title="Server Settings">Server Settings</a>
							</li>
						</ul>
					</li>
				<?php endif; ?>

				<?php if($mem->reqLevel('siteadmin')) : ?>
					<li class="n-sa<?php if(isSA()) echo ' selected'; ?>">
						<a href="<?php echo PATH; ?>sa.php" title="Site Administration">Site Admin</a>
					</li>
					<li class="n-tools<?php if(isPerms()) echo ' selected'; ?>">
						<a href="<?php echo PATH; ?>sa.php?t=perms" title="User Permissions Management">Permissions</a>
					</li>
				<?php endif; ?>
				
				<li class="n-me<?php if(isMe()) echo ' selected'; ?>">
					<a href="<?php echo PATH; ?>me.php" title="Edit your account">My Account</a>
				</li>
			</ul>
		</li>			

		<?php } else { ?>
	
		<li class="login<?php if(isLogin()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>login.php" title="Login to Echelon to see the good stuff!">Login</a></li>
		<li class="pubbans<?php if(isPubbans()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>pubbans.php" title="Public Ban List">Public Ban List</a></li>

		<?php } ?>
	</ul><!-- end #nav -->

	<div id="user-info">
		<?php if($mem->loggedIn()) { ?>
			<div class="log-cor">
				<a href="<?php echo PATH; ?>actions/logout.php" class="logout" title="Sign out">Sign Out</a>
			</div>
		<?php } ?>

		<div class="info">
			<?php 
				if(GRAVATAR)
					echo $mem->getGravatar($mem->email); 
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
		$day_of_week = date('N');
	
		if( (isSA() || isHome()) && ($day_of_week == 1) ) {
			$latest = getEchVer();
			if(ECH_VER !== $latest && $latest != false) // if current version does not equal latest version show warning message
				set_warning('You are not using the lastest version of Echelon, please check the <a href="http://www.bigbrotherbot.com/forums/" title="Check the B3 Forums">B3 Forums</a> for more information.');
		}
	endif;

	errors(); // echo out all errors/success/warnings

	if($query_normal) : // if this is a normal query page and there is a db error show message
	
		if($db->error)
			dbErrorShow($db->error_msg); // show db error

	endif;