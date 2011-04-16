<?php
$page = "clientdetails";
$page_title = "Client Details";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = false; // this page requires the pagination part of the footer
require 'inc.php';

## Do Stuff ##
if($_GET['id'])
	$cid = $_GET['id'];

if(!isID($cid)) :
	set_error('The client id that you have supplied is invalid. Please supply a valid client id.');
	send('clients.php');
	exit;
endif;
	
if($cid == '') {
	set_error('No user specified, please select one');
	send('clients.php');
}

## Get Client information ##
$query = "SELECT c.ip, c.connections, c.guid, c.name, c.mask_level, c.greeting, c.time_add, c.time_edit, c.group_bits, g.name
		  FROM clients c LEFT JOIN groups g ON c.group_bits = g.id WHERE c.id = ? LIMIT 1";
$stmt = $db->mysql->prepare($query) or die('Database Error '. $db->mysql->error);
$stmt->bind_param('i', $cid);
$stmt->execute();
$stmt->bind_result($ip, $connections, $guid, $name, $mask_level, $greeting, $time_add, $time_edit, $group_bits, $user_group);
$stmt->fetch();
$stmt->close();

## Require Header ##
$page_title .= ' '.$name; // add the clinets name to the end of the title

require 'inc/header.php';
?>
<table class="cd-table">
	<caption><img src="images/cd-page-icon.png" width="32" height="32" alt="" /><?php echo $name; ?><small>Everything B3 knows about <?php echo $name; ?></small></caption>
	<tbody>
		<tr>
			<th>Name</th>
				<td><?php echo  tableClean($name); ?></td>
			<th>@id</th>
				<td><?php echo $cid; ?></td>
		</tr>
		<tr>
			<th>Level</th>
				<td><?php 
					if($user_group == NULL)
						echo 'Un-registered';
					else
						echo $user_group; 
					?>
				</td>
			<th>Connections</th>
				<td><?php echo $connections; ?></td>
		</tr>
		<tr>
			<th>GUID</th>
				<td>
				<?php 
					$guid_len = strlen($guid);
					if($guid_len == 0) {
						echo '(There is no GUID availible)';
					
					} elseif($mem->reqLevel('view_full_guid')) { // if allowed to see the full guid
						if($guid_len == 32) 
							guidCheckLink($guid);
						else 
							echo $guid.' <span class="red" title="This guid is only ' . $guid_len . ' characters long, it should be 32 characters!">['. $guid_len .']</span>';
				
					} elseif($mem->reqLevel('view_half_guid')) { // if allowed to see the last 8 chars of guid
						
						if($guid_len == 32) {
							$half_guid = substr($guid, -16); // get the last 8 characters of the guid
							guidCheckLink($half_guid);
						} else
							echo $guid.' <span class="red" title="This guid is only ' . $guid_len . ' characters long, it should be 32 characters!">['. $guid_len .']</span>';
					
					} else { // if not allowed to see any part of the guid
						echo '(You do not have access to see the GUID)';
					}
				?>
				</td>
			<th>IP Address</th>
				<td>
					<?php
					$ip = tableClean($ip);
					if($mem->reqLevel('view_ip')) :
						if ($ip != "") { ?>
							<a href="clients.php?s=<?php echo $ip; ?>&amp;t=ip" title="Search for other users with this IP adreess"><?php echo $ip; ?></a>
								&nbsp;&nbsp;
							<a href="http://whois.domaintools.com/<?php echo $ip; ?>" title="Whois IP Search"><img src="images/id_card.png" width="16" height="16" alt="W" /></a>
								&nbsp;&nbsp;
							<a href="http://www.geoiptool.com/en/?IP=<?php echo $ip; ?>" title="Show Location of IP origin on map"><img src="images/globe.png" width="16" height="16" alt="L" /></a>
					<?php
						} else {
							echo "(No IP address available)";
						}
					else:	
						echo '(You do not have access to see the IP address)';
					endif; // if current user is allowed to see the player's IP address
					?>
				</td>
		</tr>
		<tr>
			<th>First Seen</th>
				<td><?php echo date($tformat, $time_add); ?></td>
			<th>Last Seen</th>
				<td><?php echo date($tformat, $time_edit); ?></td>
		</tr>
	</tbody>
</table>

<?php 
## Plugins Client Bio Area ##

	if(!$no_plugins_active)
		$plugins->displayCDBio();

##############################
?>

<!-- Start Echelon Actions Panel -->

<a name="tabs" />
<div id="actions">
	<ul class="cd-tabs">
		<?php if($mem->reqLevel('comment')) { ?><li class="cd-active"><a href="#tabs" title="Add a comment to this user" rel="cd-act-comment" class="cd-tab">Comment</a></li><?php } ?>
		<?php if($mem->reqLevel('greeting')) { ?><li><a href="#tabs" title="Edit this user's greeting" rel="cd-act-greeting" class="cd-tab">Greeting</a></li><?php } ?>
		<?php if($mem->reqLevel('ban')) { ?><li><a href="#tabs" title="Add Ban/Tempban to this user" rel="cd-act-ban" class="cd-tab">Ban</a></li><?php } ?>
		<?php if($mem->reqLevel('edit_client_level')) { ?><li><a href="#tabs" title="Change this user's user level" rel="cd-act-lvl" class="cd-tab">Change Level</a></li><?php } ?>
		<?php if($mem->reqLevel('edit_mask')) { ?><li><a href="#tabs" title="Change this user's mask level" rel="cd-act-mask" class="cd-tab">Mask Level</a></li><?php } ?>
		<?php 
			if(!$no_plugins_active)
				$plugins->displayCDFormTab();
		?>
	</ul>
	<div id="actions-box">
		<?php
			if($mem->reqLevel('comment')) :
			$comment_token = genFormToken('comment');	
		?>
		<div id="cd-act-comment" class="act-slide">
			
			<form action="actions/b3/comment.php" method="post">
				<label for="comment">Comment:</label><br />
					<textarea type="text" name="comment" id="comment"></textarea>
					<?php tooltip('Add a comment to this users Echelon profile'); ?>
					<br />
					
				<input type="hidden" name="token" value="<?php echo $comment_token; ?>" />
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				
				<input type="submit" name="comment-sub" value="Add Comment" />
			</form>
		</div>
		<?php
			endif;
			if($mem->reqLevel('greeting')) :
			$greeting_token = genFormToken('greeting');
		?>
		<div id="cd-act-greeting" class="act-slide">
			<form action="actions/b3/greeting.php" method="post">
				<label for="greeting">Greeting Message:</label><br />
					<textarea name="greeting" id="greeting"><?php echo $greeting; ?></textarea><br />
					
				<input type="hidden" name="token" value="<?php echo $greeting_token; ?>" />
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				<input type="submit" name="greeting-sub" value="Edit Greeting" />
			</form>
		</div>
		<?php
			endif;
			if($mem->reqLevel('ban')) :
			$ban_token = genFormToken('ban');
		?>
		<div id="cd-act-ban" class="act-slide">
			<form action="actions/b3/ban.php" method="post">
		
				<fieldset class="none">
					<legend>Type</legend>
					
					<label for="pb">Permanent Ban?</label>
						<input type="checkbox" name="pb" id="pb" /><?php tooltip('Is this ban to last forever?'); ?><br />
					
					<div id="ban-duration">
						<label for="duration">Duration:</label>
							<input type="text" name="duration" id="duration" class="int dur" /><?php tooltip('This is the number (eg. 3) of minutes/hours ect.'); ?>
							
							<select name="time">
								<option value="m">Minutes</option>
								<option value="h">Hours</option>
								<option value="d">Days</option>
								<option value="w">Weeks</option>
								<option value="mn">Months</option>
								<option value="y">Years</option>
							</select>
							<?php tooltip('How long should this ban last'); ?>
					</div>
				</fieldset>
				<br class="clear" />
				
				<label for="reason">Reason:</label>
					<input type="text" name="reason" id="reason" />
					
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				<input type="hidden" name="c-name" value="<?php echo $name; ?>" />
				<input type="hidden" name="c-ip" value="<?php echo $ip; ?>" />
				<input type="hidden" name="c-pbid" value="<?php echo $guid; ?>" />
				<input type="hidden" name="token" value="<?php echo $ban_token; ?>" />
				<input type="submit" name="ban-sub" value="Ban User" />
			</form>
		</div>
		<?php
			endif; // end hide ban section to non authed
			$b3_groups = $db->getB3Groups(); // get a list of all B3 groups from the B3 DB
			
			if($mem->reqLevel('edit_client_level')) :
			$level_token = genFormToken('level');
		?>
		<div id="cd-act-lvl" class="act-slide">
			<form action="actions/b3/level.php" method="post">
				<label for="level">Level:</label>
					<select name="level" id="level">
						<?php
							foreach($b3_groups as $group) :
								$gid = $group['id'];
								$gname = $group['name'];
								if($group_bits == $gid)
									echo '<option value="'.$gid.'" selected="selected">'.$gname.'</option>';
								else
									echo '<option value="'.$gid.'">'.$gname.'</option>';
							endforeach;
						?>
					</select><br />
					
				<div id="level-pw">
					<label for="password">Your Current Password:</label>
						<input type="password" name="password" id="password" />
						
						<?php tooltip('We need your password to make sure it is really you'); ?>
						
					<br />
				</div>
					
				<input type="hidden" name="old-level" value="<?php echo $group_bits; ?>" />
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				<input type="hidden" name="token" value="<?php echo $level_token; ?>" />
				<input type="submit" name="level-sub" value="Change Level" />
			</form>
		</div>
		<?php
			endif; // end if 
			if($mem->reqLevel('edit_mask')) : 
			$mask_lvl_token = genFormToken('mask');
		?>
		<div id="cd-act-mask" class="act-slide">
			<form action="actions/b3/level.php" method="post">
				<label for="mlevel">Mask Level:</label>
					<select name="level" id="mlevel">
						<?php
							foreach($b3_groups as $group) :
								$gid = $group['id'];
								$gname = $group['name'];
								if($mask_level == $gid)
									echo '<option value="'.$gid.'" selected="selected">'.$gname.'</option>';
								else
									echo '<option value="'.$gid.'">'.$gname.'</option>';
							endforeach;
						?>
					</select>
					<?php tooltip('Masking a user masks their user level from everyone in the game server, as whatever value is here'); ?>
				
				<input type="hidden" name="old-level" value="<?php echo $group_bits; ?>" />
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				<input type="hidden" name="token" value="<?php echo $mask_lvl_token; ?>" />
				<input type="submit" name="mlevel-sub" value="Change Mask" />
			</form>
		</div>
		<?php 
			endif;
			
			## Plugins CD Form ##
			if(!$no_plugins_active)
				$plugins->displayCDForm($cid)
			
		?>
	</div><!-- end #actions-box -->
</div><!-- end #actions -->

<!-- Start Client Aliases -->

<h3 class="cd-h">Aliases</h3>
<table>
	<thead>
		<tr>
			<th>Alias</th>
			<th>Times Used</th>
			<th>First Used</th>
			<th>Last Used</th>
		</tr>
	</thead>
	<tfoot>
		<tr><th colspan="4"></th></tr>
	</tfoot>
	<tbody>
	<?php
		// notice on the query we say that time_add does not equal time_edit, this is because of bug in alias recording in B3 that has now been solved
		$query = "SELECT alias, num_used, time_add, time_edit FROM aliases WHERE client_id = ? ORDER BY time_edit DESC";
		$stmt = $db->mysql->prepare($query) or die('Alias Database Query Error'. $db->mysql->error);
		$stmt->bind_param('i', $cid);
		$stmt->execute();
		$stmt->bind_result($alias, $num_used, $time_add, $time_edit);
		
		$stmt->store_result(); // needed for the $stmt->num_rows call

		if($stmt->num_rows) :
			
			while($stmt->fetch()) :
	
				$time_add = date($tformat, $time_add);
				$time_edit = date($tformat, $time_edit);
				
				$alter = alter();
				
				$token_del = genFormToken('del'.$id);		
				
				// setup heredoc (table data)			
				$data = <<<EOD
				<tr class="$alter">
					<td><strong>$alias</strong></td>
					<td>$num_used</td>
					<td><em>$time_add</em></td>
					<td><em>$time_edit</em></td>
				</tr>
EOD;
				echo $data;
			
			endwhile;
		
		else : // if there are no aliases connected with this user then put out a small and short message
		
			echo '<tr><td colspan="4">'.$name.' has no aliaises.</td></tr>';
		
		endif;
	?>
	</tbody>
</table>

<!-- Start Client Echelon Logs -->

<?php
	## Get Echelon Logs Client Logs (NOTE INFO IN THE ECHELON DB) ##
	$ech_logs = $dbl->getEchLogs($cid, $game);
	
	$count = count($ech_logs);
	if($count > 0) : // if there are records
?>
	<h3 class="cd-h">Echelon Logs</h3>
	<table>
		<thead>
			<tr>
				<th>id</th>
				<th>Type</th>
				<th>Message</th>
				<th>Time Added</th>
				<th>Admin</th>
			</tr>
		</thead>
		<tfoot>
			<tr><th colspan="5"></th></tr>
		</tfoot>
		<tbody>
			<?php displayEchLog($ech_logs, 'client'); ?>
		</tbody>
	</table>
<?php
	endif; // end hide is no records
?>

<!-- Client Penalties -->

<div id="penalties">
	<h3 class="cd-h cd-slide" id="cd-pen">Penalties <img class="cd-open" src="images/add.png" alt="Open" /></h3>
	<table id="cd-pen-table" class="slide-panel">
		<thead>
			<tr>
				<th></th>
				<th>Type</th>
				<th>Added</th>
				<th>Duration</th>
				<th>Expires</th>
				<th>Reason</th>
				<th>Admin</th>
			</tr>
		</thead>
		<tfoot>
			<tr><td colspan="7"></td></tr>
		</tfoot>
		<tbody id="contain-pen">
			<?php 
				$type_inc = 'client';
				include 'inc/cd/penalties.php'; 
			?>
		</tbody>
	</table>
</div>

<!-- Admin History -->

<div id="admin">
	<h3 class="cd-h cd-slide" id="cd-admin">Admin Actions <img class="cd-open" src="images/add.png" alt="Open" /></h3>
	<table id="cd-admin-table" class="slide-panel">
		<thead>
			<tr>
				<th></th>
				<th>Type</th>
				<th>Added</th>
				<th>Duration</th>
				<th>Expires</th>
				<th>Reason</th>
				<th>Admin</th>
			</tr>
		</thead>
		<tfoot>
			<tr><td colspan="7"></td></tr>
		</tfoot>
		<tbody id="contain-admin">
			<?php 
				$type_inc = 'admin';
				include 'inc/cd/penalties.php'; 
			?>
		</tbody>
	</table>
</div>

<?php
## Plugins Log Include Area ##
if(!$no_plugins_active)
	$plugins->displayCDlogs($cid);

// Close page off with the footer
require 'inc/footer.php'; 
?>