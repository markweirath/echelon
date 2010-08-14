<?php
$page = "settings-server";
$page_title = "Server Settings";
$auth_name = 'manage_settings';
require 'inc.php';

// We are using the game information that was pulled in setup.php
$game_token = genFormToken('serversettings');

$page_type = 'none';
if($_GET['t'])
	$page_type = cleanvar($_GET['t']);

if($page_type == 'add') : ## if add a server page ##

	$token = genFormToken('addserver');

elseif($page_type == 'srv') : ## if edit a server page ##
	
	$server_id = cleanvar($_GET['id']);
	if($server_id == '') {
		set_error('No server id chosen, please choose a server');
		send('game-settings.php');
		exit;
	}
	
	$token = genFormToken('editserversettings');
	
	## get server information
	$server = $dbl->getServer($server_id);
	
else: ## if a normal list page ##

	## Default Vars ##
	$orderby = "id";
	$order = "ASC"; // either ASC or DESC

	## Sorts requests vars ##
	if($_GET['ob'])
		$orderby = addslashes($_GET['ob']);

	if($_GET['o'])
		$order = addslashes($_GET['o']);

	## allowed things to sort by ##
	$allowed_orderby = array('id', 'name', 'ip', 'pb_active');
	if(!in_array($orderby, $allowed_orderby)) // Check if the sent varible is in the allowed array 
		$orderby = 'id'; // if not just set to default id
	
	if($order == 'DESC')
		$order = 'DESC';
	else
		$order = 'ASC';
	
	## Get List ##
	if(!$no_servers) // if there are servers
		$servers = $dbl->getServerList($orderby, $order);
	
	## Find num of servers found ##
	if(!$servers) // if false
		$num_rows = 0;
	else
		$num_rows = count($servers);

endif;

require 'inc/header.php';

if($num_games < 1) : ?>

	<h3>No Games Created</h3>
		<p>Please go to <a href="settings-games.php?t=add">Settings Games</a>, and add a game before you can add/edit any server settings</p>

<?php elseif($page_type == 'add') : ?>

	<a href="settings-server.php" title="Go back to the main server listing" class="float-left">&laquo; Server List</a><br />

	<h3>Add Server</h3>
	
	<form action="actions/settings-server.php" method="post">

	<fieldset>
		<legend>Add a Server</legend>
		
		<fieldset class="none wide">
			<legend>General Info</legend>
		
			<label for="name">Server Name:</label>
				<input type="text" name="name" id="name"  />

			<label for="ip">IP Address:</label>
				<input type="text" name="ip" id="ip" /><?php tooltip('The public IP address of the server'); ?>

			<br /><label for="pb">Punkbuster&trade; Active?</label>
				<input type="checkbox" name="pb" id="pb" /><?php tooltip('Is punkbuster running on this server?'); ?>
				
			<label for="game-id">Game:</label>
				<select name="game-id" id="game-id">
					<?php
					$i = 0;
					$count = count($games_list);
					$count--; // minus 1
					while($i <= $count) :

						echo '<option value="'.$games_list[$i]['id'].'">'.$games_list[$i]['name'].'</option>';
						
						$i++;
					endwhile;
					?>
				</select><?php tooltip('What game is this server to be connected with?'); ?>
		</fieldset>
			
		<fieldset class="none">
			<legend>Rcon Info</legend>
				
			<label for="rcon-ip">Rcon IP:</label><?php tooltip('The IP used to connect to Rcon of this server'); ?>
				<input type="text" name="rcon-ip" id="rcon-ip" />

			<label for="rcon-port">Rcon Port:</label>
				<input type="text" class="int" style="width: 50px !important" name="rcon-port" id="rcon-port" /><br />
			
			<label for="rcon-pass">Rcon Password:</label>
				<input type="password" name="rcon-pass" id="-rcon-pass" />

		</fieldset>

	</fieldset><!-- end general game settings -->
	
		<input type="hidden" name="type" value="add" />
		<input type="hidden" name="cng-pw" value="on" />
		<input type="hidden" name="token" value="<?php echo $token; ?>" />
		<input type="submit" name="server-settings-sub" value="Add Server" />

	</form>

<?php elseif($page_type == 'srv') : /* if edit server page */ ?>
	
	<a href="settings-server.php" title="Go back to the main server listing" class="float-left">&laquo; Server List</a>
	<a href="settings-server.php?t=add" title="Add a server" class="float-right">Add Server &raquo;</a>
	<br />
	<form action="actions/settings-server.php" method="post">

	<fieldset>
		<legend>Server Settings for <?php echo $server['name']; ?></legend>
		
		<fieldset class="none wide">
			<legend>General Info</legend>
		
			<label for="name">Server Name:</label>
				<input type="text" name="name" id="name" value="<?php echo $server['name']; ?>" />

			<label for="ip">IP Address:</label>
				<input type="text" name="ip" id="ip" value="<?php echo $server['ip']; ?>" />

			<br /><label for="pb">Punkbuster&trade; Active?</label>
				<input type="checkbox" name="pb" id="pb" <?php if($server['pb_active'] == 1) echo 'checked="checked"'; ?> />
		</fieldset>
			
		<fieldset class="none">
			<legend>Rcon Info</legend>
				
			<label for="rcon-ip">Rcon IP:</label>
				<input type="text" name="rcon-ip" id="rcon-ip" value="<?php echo $server['rcon_ip']; ?>" />

			<label for="rcon-port">Rcon Port:</label>
				<input type="text" class="int" style="width: 50px !important" name="rcon-port" id="rcon-port" value="<?php echo $server['rcon_port']; ?>" /><br />
			
			<label for="cng-pw">Change Rcon Password?</label>
				<input type="checkbox" name="cng-pw" id="cng-pw" /><br />
							
			<div id="change-pw-box">
				<label for="rcon-pass">Rcon Password:</label>
					<input type="password" name="rcon-pass" id="-rcon-pass" />
			</div>

		</fieldset>

	</fieldset><!-- end general server settings -->

		<input type="hidden" name="type" value="edit" />
		<input type="hidden" name="token" value="<?php echo $token; ?>" />
		<input type="hidden" name="server" value="<?php echo $server_id; ?>" />
		<input type="submit" name="server-settings-sub" value="Save Settings" />

	</form>

	<br />
	
<?php else : /* if normal list page type */ ?>

	<a href="settings-server.php?t=add" title="Add a new server to Echelon Db" class="float-right">Add Server &raquo;</a>

	<table summary="A list of game servers">
		<caption>Servers<small>This is all the servers Echelon knows about, across all the games Echelon knows about</small></caption>
	<thead>
		<tr>
			<th>id
				<?php linkSort('id', 'id'); ?>
			</th>
			<th>Name
				<?php linkSort('name', 'Name'); ?>
			</th>
			<th>IP
				<?php linkSort('ip', 'Server IP'); ?>
			</th>
			<th>PB Enabled
				<?php linkSort('pb_active', 'Punkbuster Enabled Status'); ?>
			</th>
			<th>Game</th>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="6"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php
		if($num_rows > 0) : // query contains stuff
		 
			foreach($servers as $server): // get data from query and spit it out
				$id = $server['id'];
				$name = $server['name'];
				$game_id = $server['game'];
				$pb_active = $server['pb_active'];
				$ip = $server['ip'];
				$game_name = $server['game_name'];
				
				## row color
				$alter = alter();
				
				## Make it human readable
				if($pb_active == 1)
					$pb_active_read = '<span class="on">Yes</span>';
				else
					$pb_active_read = '<span class="off">No</span>';
					
				$ip_read = ipLink($ip);
				
				// set a warning that the active game has changed since the last page?
				if($game != $game_id)
					$warn = 'game';
				else
					$warn = '';
					
				$del_token = genFormToken('del-server'.$id);
			
				$table = <<<EOD
				<tr class="$alter">
					<td>$id</td>
					<td><strong><a href="settings-server.php?t=srv&amp;id=$id">$name</a></strong></td>
					<td>$ip_read</td>
					<td>$pb_active_read</td>
					<td><a href="settings-games.php?game=$game_id&amp;w=$warn" title="Edit the settings for $game_name">$game_name</a></td>
					<td>
						<a href="settings-server.php?t=srv&amp;id=$id"><img src="images/edit.png" alt="[E]" /></a>
						<form style="display: inline;" method="post" action="actions/settings-server.php?t=del&amp;id=$id">
							<input type="hidden" name="token" value="$del_token" />
							<input class="harddel" type="image" title="Delete this Server" src="images/delete.png" alt="[D]" />
						</form>
					</td>
				</tr>
EOD;

				echo $table;
			endforeach;

		else :
			echo '<tr class="odd"><td colspan="6">There are no servers would you like to <a href="settings-server.php?t=add" title="Add a new Server to Echelon DB">add a server</a>.</td></tr>';
		endif; // end if query contains
		?>
	</tbody>
	</table>

	<br />

<?php endif; // if no an empty id ?>

<?php require 'inc/footer.php'; ?>