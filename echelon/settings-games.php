<?php
$page = "settings-game";
$page_title = "Games Settings";
$auth_name = 'manage_settings';
require 'inc.php';

// We are using the game information that was pulled in setup.php
$game_token = genFormToken('gamesettings');

require 'inc/header.php';
?>
<form action="actions/settings-game.php" method="post">

<fieldset>
	<legend>Game Settings <?php echo $game_name; ?></legend>
	
			<label for="<name">Full Name:</label>
				<input type="text" name="name" id="<?php echo $i; ?>-name" value="<?php echo $game_name; ?>" />
			
			<label for="name-short">Short Name:</label>
				<input type="text" name="name-short" id="name-short" value="<?php echo $game_name_short; ?>" />
			
			<fieldset class="none">
				<legend>B3 DB Information</legend>
			
				<label for="db-host">Hostname:</label>
					<input type="text" name="db-host" id="db-host" value="<?php echo $game_db_host; ?>" />
				
				<label for="db-user">User:</label>
					<input type="text" name="db-user" id="db-user" value="<?php echo $game_db_user; ?>" />

				<label for="cng-pw">Change DB Password?</label>
					<input type="checkbox" name="cng-pw" id="cng-pw" /><br />
								
				<div id="change-pw-box">
					<label for="db-pw">DB Password:</label>
						<input type="password" name="db-pw" id="db-pw" />
				</div>
			
				<label for="db-name">DB Name:</label>
					<input type="text" name="db-name" id="db-name" value="<?php echo $game_db_name; ?>" />
			
			</fieldset><!-- end Db info -->

</fieldset><!-- end general game settings -->

<fieldset>
	<legend>Servers for <?php echo $game_name; ?></legend>
	
	<?php
		$i = 0;
		$g_s = $dbl->getServers($game);		
		$srv_count = count($g_s) - 1; // need to minus one since arrays start at 0
	
		while($i <= $srv_count) : 
	?>
	
		<fieldset class="none">
			<legend><?php echo $g_s[$i]['name']; ?></legend>
		
			<label for="s<?php echo $g_s[$i]['id']; ?>-name">Server Name:</label>
				<input type="text" name="s<?php echo $g_s[$i]['id']; ?>-name" id="s<?php echo $g_s[$i]['id']; ?>-name" value="<?php echo $g_s[$i]['name']; ?>" />
		
			<label for="s<?php echo $g_s[$i]['id']; ?>-ip">IP Address:</label>
				<input type="text" name="s<?php echo $g_s[$i]['id']; ?>-ip" id="s<?php echo $g_s[$i]['id']; ?>-ip" value="<?php echo $g_s[$i]['ip']; ?>" />

			<label for="s<?php echo $g_s[$i]['id']; ?>-pb">Punkbuster&trade; Active?</label>
				<input type="checkbox" name="s<?php echo $g_s[$i]['id']; ?>-pb" id="s<?php echo $g_s[$i]['id']; ?>-pb" <?php if($g_s[$i]['pb_active'] == 1) echo 'checked="checked"'; ?> />

			<fieldset class="none">
				<legend>RCON Info</legend>
					
				<label for="s<?php echo $g_s[$i]['id']; ?>-rcon-ip">Rcon IP:</label>
					<input type="text" name="s<?php echo $g_s[$i]['id']; ?>-rcon-ip" id="s<?php echo $g_s[$i]['id']; ?>-rcon-ip" value="<?php echo $g_s[$i]['rcon_ip']; ?>" />

				<label for="s<?php echo $g_s[$i]['id']; ?>-rcon-port">Rcon Port:</label>
					<input type="text" name="s<?php echo $g_s[$i]['id']; ?>-rcon-port" id="s<?php echo $g_s[$i]['id']; ?>-rcon-port" value="<?php echo $g_s[$i]['rcon_port']; ?>" />
			
				<label for="s<?php echo $g_s[$i]['id']; ?>-rcon-pass">Rcon Password:</label>
					<input type="password" name="s<?php echo $g_s[$i]['id']; ?>-rcon-pass" id="s<?php echo $g_s[$i]['id']; ?>-rcon-pass" />
			
			</fieldset>
		
		</fieldset>
		
	<?php
			$i++;
		endwhile;
	?>

</fieldset>

	<fieldset class="none">
		<legend>Verify Identity</legend>

		<label for="verify-pw">Your current password:</label>
			<input type="password" name="password" id="verify-pw" />

	</fieldset>
	
	<br class="clear" />

	<input type="hidden" name="token" value="<?php echo $game_token; ?>" />
	<input type="hidden" name="game" value="<?php echo $game; ?>" />
	<input type="submit" name="game-settings-sub" value="Save Settings" />

</form>

<br />

<?php require 'inc/footer.php'; ?>