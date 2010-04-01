<?php
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
	
	<?php if($page == 'login') { ?>
		<!-- Load Login CSS Stylesheet -->
		<link href="<?php echo $path; ?>css/login.css" rel="stylesheet" media="screen" type="text/css" />
	<?php } ?>
	
	<?php if($page == 'clientdetails') { ?>
		<!-- Load Client details CSS Stylesheet -->
		<link href="<?php echo $path; ?>css/cd.css" rel="stylesheet" media="screen" type="text/css" />
	<?php } ?>
	
	<?php if($page == 'settings') { ?>
		<!-- Load settings page CSS Stylesheet -->
		<link href="<?php echo $path; ?>css/settings.css" rel="stylesheet" media="screen" type="text/css" />
	<?php } ?>
	
	<?php if($page == 'home') { ?>
		<!-- Load Home page CSS Stylesheet -->
		<link href="<?php echo $path; ?>css/home.css" rel="stylesheet" media="screen" type="text/css" />
	<?php } ?>
	
	<!-- ALL JS TO BE LOADED INTO THE FOOTER -->
</head>

<body id="<?php echo $page; ?>">
		
<div id="page-wrap">
	
<?php if($mem->loggedIn()) { ?>
<!-- Panel -->
<div id="toppanel">

<div id="panel">
	<div class="content clearfix">
		<div class="left">
			<h3>External Links</h3>
			<ul>
				<li><a href="http://eire32designs.com/" title="The developers site">E32D</a></li>
				<li><a href="http://edgegamers.org/" title="A clan that helped with the development of Echelon">EdgeGamers&trade;</a></li>
				<li><a href="http://bigbrotherbot.com/forums/" title="B3 Website">B3 Forms</a></li>
				<li><a href="#" title="Help and information for Echelon">Echelon Help</a></li>
				<li><a href="http://cback.de/" title="Anti worm injection software">CTracker</a></li>
			</ul>
		</div>
		
		<div class="left">
			<h3>Changelog v2.0</h3>
			<ul>
				<li>Better user management</li>
				<li>IP Blacklist</li>
				<li>Echelon connect</li>
				<li>Editable settings</li>
				<li>Regular visitors page</li>
				<li>In-active admins page</li>
				<li>Multi server for a DB support</li>
				<li>Ability to change a client's mask, greeting, login details, edit a ban</li>
				<li>Security: Anti-session hijacking and fixation, tokens to stop CSRF attacks, prepared statments to prevent SQL injection.</li>
			</ul>
		</div>
		
		<div class="left right">
			<?php if(!is_clients($page)) : ?>
				<h3>Client Search</h3>
				<form action="../clients.php" method="get" id="c-search">
					<input type="text" name="s" id="search" value="Search clients list...." class="clr-txt" style="width: 170px;" />
					
					<select name="t">
						<option value="all" <?php if($search_type == "all") echo 'selected="selected"' ?>>All</option>
						<option value="alias" <?php if($search_type == "alias") echo 'selected="selected"' ?>>Alias</option>
						<option value="pbid" <?php if($search_type == "pbid") echo 'selected="selected"' ?>>PBID</option>
						<option value="ip" <?php if($search_type == "ip") echo 'selected="selected"' ?>>IP</option>
						<option value="id" <?php if($search_type == "id") echo 'selected="selected"' ?>>Player ID</option>
					</select><br />
					
					<input type="submit" id="sub-search" value="Search" />
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- The tab on top -->	
<div class="tab">
	<ul class="login">
		<li class="left">&nbsp;</li>
		<li>Hello <?php echo $_SESSION['name']; ?></li>
		<li class="sep">|</li>
		<li id="toggle">
			<a id="open" class="open" href="#">Help | Search</a>
			<a id="close" style="display: none;" class="close" href="#">Close Panel</a>			
		</li>
		<li class="right">&nbsp;</li>
	</ul> 
</div> <!-- / top -->
	
</div> <!--panel -->
<?php } ?>


<div id="header">
	<a name="t"></a>
	<h1 id="title"><a href="<?php echo $path; ?>" title="Go to the home page">Echelon</a></h1>
	<h2 id="subtitle">B3 repository and investigation tool</h2>		
</div><!-- end #header -->
		
						
<div id="mc">

	<div id="menu">
	
		<ul id="nav">
			<?php if($mem->loggedIn()) { ?>
			
				<li class="home<?php if($page == 'home') echo ' selected'; ?>"><a href="<?php echo $path; ?>" title="Home Page">Home</a></li>
				<li class="cdd">
					<a href="#">Games</a>
					<ul class="dd games-list">
						<?php
							$this_cur_page = basename($_SERVER['SCRIPT_NAME']);
							$games_list = $dbl->getGamesList();
							foreach($games_list as $item) :
								$loop_game_id = substr($item['category'], -1); // the id of the game is at the end of the string (eg. 'game1') so substr gets the last character (ie. the id)
								$loop_game_name = $item['value'];
								if($game == $loop_game_id)
									echo '<li class="selected">';
								else
									echo '<li>';
								echo '<a href="'.$path . $this_cur_page .'?game='.$loop_game_id.'" title="Switch to this game">'.$loop_game_name.'</a></li>';
							endforeach;
						?>	
					</ul>
				</li>
				<li class="cdd">
					<a href="#">Clients</a>
					<ul class="dd">
						<li class="clients<?php if($page == 'client') echo ' selected'; ?>"><a href="<?php echo $path; ?>clients.php" title="Clients Listing">Clients</a></li>
						<li class="active<?php if($page == 'active') echo ' selected'; ?>"><a href="<?php echo $path; ?>active.php" title="In-active admins">In-active Admins</a></li>
						<li class="regular<?php if($page == 'regular') echo ' selected'; ?>"><a href="<?php echo $path; ?>regular.php" title="Regular non admin visitors to your servers">Regular Visitors</a></li>
						<li class="admins<?php if($page == 'admins') echo ' selected'; ?>"><a href="<?php echo $path; ?>admins.php" title="A list of all admins">Admin Listing</a></li>
					</ul>
				</li>
				<li class="cdd">
					<a href="#">Penalties</a>
					<ul class="dd">
						<li class="adminkicks<?php if($page == 'adminkicks') echo ' selected'; ?>"><a href="<?php echo $path; ?>adminkicks.php">Admin Kicks</a></li>
						<li class="adminbans<?php if($page == 'adminbans') echo ' selected'; ?>"><a href="<?php echo $path; ?>bans.php?t=a">Admin Bans</a></li>
						<li class="b3pen<?php if($page == 'b3pen') echo ' selected'; ?>"><a href="<?php echo $path; ?>bans.php?t=b" title="All Kicks/Bans added automatically by B3">B3 Bans</a></li>
						<li class="pubbans<?php if($page == 'pubbans') echo ' selected'; ?>"><a href="<?php echo $path; ?>pubbans.php" title="A public list of bans in the database">Public Ban List</a></li>
					</ul>
				</li>
				<li class="cdd">
					<a href="#">Other</a>
					<ul class="dd">
						<li class="pbss<?php if($page == 'pbss') echo ' selected'; ?>"><a href="<?php echo $path; ?>clients.php" title="Punkbuster&trade; screenshots">PBSS</a></li>
						<li class="chat<?php if($page == 'chat') echo ' selected'; ?>"><a href="<?php echo $path; ?>clients.php" title="Logs of chats from the servers">Chat Logs</a></li>
						<li class="ctime<?php if($page == 'ctime') echo ' selected'; ?>"><a href="<?php echo $path; ?>clients.php" title="Records of how long people are spending on the server">Current Activity</a></li>
						<li class="notices<?php if($page == 'notices') echo ' selected'; ?>"><a href="<?php echo $path; ?>clients.php" title="In-game Notices">Notices</a></li>
					</ul>
				</li>
				<li class="cdd">
					<a href="#">Echelon</a>
					<ul class="dd">
						<li class="settings<?php if($page == 'settings') echo ' selected'; ?>"><a href="<?php echo $path; ?>settings.php" title="Site Settings">Site Settings</a></li>
						<li class="sa<?php if($page == 'sa') echo ' selected'; ?>"><a href="<?php echo $path; ?>sa.php" title="Site Administration">Site Admin</a></li>
						<li class="me<?php if($page == 'me') echo ' selected'; ?>"><a href="<?php echo $path; ?>me.php" title="Edit your account">My Account</a></li>
					</ul>
				</li>			
				
			<?php } else { ?>
			
				<li class="login<?php if($page == 'login') echo ' selected'; ?>"><a href="<?php echo $path; ?>login.php" title="Login to Echelon to see the good stuff!">Login</a></li>
				<li class="pubbans"><a href="<?php echo $path; ?>pubbans.php" title="Public Ban List">Public Ban List</a></li>
				
			<?php } ?>
		</ul><!-- end #nav -->
		
		<div id="user-info">
			<?php if($mem->loggedIn()) { ?>
				<div class="log-cor">
					<a href="<?php echo $path; ?>actions/logout.php" class="logout" title="Sign out">Sign Out</a>
				</div>
			<?php } ?>
			
			<div class="info">
				<?php $grav_url = $mem->getGravatar($_SESSION['email']); ?>
				<span class="gravatar"><a href="http://gravatar.com/" target="_blank" title="Get your own personalised image"><img src="<?php echo $grav_url; ?>" alt="" /></a></span>
				<span class="display-name"><?php $mem->displayName($_SESSION['name']); ?></span>
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
	
	<?php errors(); ?>