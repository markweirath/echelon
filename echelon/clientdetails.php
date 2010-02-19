<?php
$page = "clientdetails";
$page_title = "Client Details";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = false; // this page requires the pagination part of the footer
require 'inc.php';

## Do Stuff ##
$cid = 3;
if($_GET['id'])
	$cid = $_GET['id'];
	
if(!is_numeric($cid))
	die('Invalid data sent');
	
if($cid == 0)
	die('Failed to detect data, shutting down.');

$query = "SELECT c.ip, c.connections, c.guid, c.name, c.mask_level, c.greeting, c.time_add, c.time_edit, c.group_bits, g.name
		  FROM clients c LEFT JOIN groups g ON c.group_bits = g.id WHERE c.id = ? LIMIT 1";
$stmt = $db->mysql->prepare($query) or die('Database Error '. $db->mysql->error);
$stmt->bind_param('i', $cid);
$stmt->execute();
$stmt->bind_result($ip, $connections, $guid, $name, $mask_level, $greeting, $time_add, $time_edit, $group_bits, $user_group);
$stmt->fetch();
$stmt->close();

// query for xlrstats
$query_xlr = "SELECT id, kills, deaths, ratio, skill, rounds, hide, fixed_name FROM xlr_playerstats WHERE client_id = ? LIMIT 1";
$stmt = $db->mysql->prepare($query_xlr) or die('2 - MySQL Error: #'.$db->mysql->errno.' '.$db->mysql->error);
$stmt->bind_param('i', $cid);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows) {
	
	$is_xlrstats_user = true;	
	$stmt->bind_result($xlr_id, $kills, $deaths, $ratio, $skill, $rounds, $hide, $fixed_name);
	$stmt->fetch();
	
} else {
	$is_xlrstats_user = false;
}

$stmt->free_result();
$stmt->close();

## Require Header ##
$page_title .= ' '.$name;
require 'inc/header.php';
?>
<table class="cd-table">
	<caption><?php echo $name; ?><small>Everything B3 knows about <?php echo $name; ?></small></caption>
	<tbody>
		<tr>
			<th>Name</th>
				<td><?php echo  htmlspecialchars($name); ?></td>
			<th>@id</th>
				<td><?php echo $cid; ?></td>
		</tr>
		<tr>
			<th>Level</th>
				<td><?php echo $user_group; ?></td>
			<th>Connections</th>
				<td><?php echo $connections; ?></td>
		</tr>
		<tr>
			<th>GUID</th>
				<td><?php echo '<a href="http://www.punksbusted.com/cgi-bin/membership/guidcheck.cgi?guid='.$guid.'" title="Check this guid is not banned by PunksBusted.com">'.$guid.'</a>'; ?></td>
			<th>IP Address</th>
				<td>
					<?php if ($ip != "") { ?>
						<a href="<?php echo $path; ?>clients.php?s=<?php echo $ip; ?>&amp;t=ip" title="Search for other users with this IP adreess"><?php echo $ip; ?></a>
							&nbsp;&nbsp;
						<a href="http://whois.domaintools.com/<?php echo $ip; ?>" target="_blank" title="Whois IP Search">W</a>
							&nbsp;&nbsp;
						<a href="http://geotool.servehttp.com/?ip=<?php echo $ip; ?>" target="_blank" title="Show Location of IP origin on map">L</a>
					<?php
						} else {
							echo "(No IP address available)";
						}
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

<?php if($is_xlrstats_user) : ?>
<table class="cd-table" id="xlrstats-table">
	<tbody>
	<tr>
		<th>Kills</th>
			<td><?php echo $kills; ?></td>
		<th>Deaths</th>
			<td><?php echo $deaths; ?></td>
	</tr>
	<tr>
		<th>Ratio</th>
			<td><?php echo number_format($ratio, 2, '.', ''); ?></td>
		<th>Skill</th>
			<td><?php echo number_format($skill, 2, '.', ''); ?></td>
	</tr>
	<tr>
		<th>Rank</th>
			<td>#49</td>
		<th>XLRStats id</th>
			<td><?php echo $xlr_id; ?></td>
	</tr>
	<tr>
		<th>Fixed Name</th>
			<td><?php if($fixed_name == "") { echo "Non Set"; } else { echo $fixed_name; } ?></td>
		<th>Hidden</th>
			<td><?php if($hide == 1) { echo "Yes"; } else { echo "No"; } ?></td>
	</tr>
	</tbody>
</table>
<?php endif; ?>

<a name="tabs" />
<div id="actions">
	<ul class="cd-tabs">
		<li class="cd-active"><a href="#tabs" title="Add a comment to this user" rel="cd-act-comment" class="cd-tab">Comment</a></li>
		<li><a href="#tabs" title="Edit this user's greeting" rel="cd-act-greeting" class="cd-tab">Greeting</a></li>
		<li><a href="#tabs" title="Add Ban/Tempban to this user" rel="cd-act-ban" class="cd-tab">Ban</a></li>
		<li><a href="#tabs" title="Change this user's user level" rel="cd-act-lvl" class="cd-tab">Change Level</a></li>
		<li><a href="#tabs" title="Change this user's mask level" rel="cd-act-mask" class="cd-tab">Mask Level</a></li>
	</ul>
	<div id="actions-box">
		<div id="cd-act-comment" class="act-slide">
			<?php
				$groups = $dbl->getGroups();
			?>
			<form action="actions/comment.php" method="post">
				<label for="comment">Comment:</label><br />
					<textarea type="text" name="comment" id="comment"></textarea>
					
				<label for="com-level">Limit Access:</label>
					<select name="limit" id="com-level">
						<option value="all">All Access</option>
						<?php
							foreach($groups as $group) :
								$gid = $group['id'];
								$gname = $group['display'];
								echo '<option value="'.$gid.'">'.$gname.'</option>';
							endforeach;
						?>
					</select>
					
				<input type="hidden" name="token" value="" />
				
				<input type="submit" name="comment-sub" value="Add Comment" />
			</form>
		</div>
		<div id="cd-act-greeting" class="act-slide">
			<form action="actions/greeting.php" method="post">
				<label for="greeting">Greeting Message:</label>
					<input type="text" name="greeting" id="greeting" value="<?php echo $greeting; ?>" />
					
				<input type="hidden" name="token" value="" />
				<input type="submit" name="greeting-sub" value="Chnage Greeting" />
			</form>
		</div>
		<div id="cd-act-ban" class="act-slide">
			<form action="actions/ban.php" method="">
		
				<fieldset class="none">
					<legend>Type</legend>
					
					<label for="pb">Permanent Ban?</label>
						<input type="checkbox" name="pb" id="pb" /><br />
						
					<label for="duration">Duration:</label>
						<input type="text" name="duration" id="duration" class="int" style="width: 60px" />
						
						<select name="time">
							<option value="m">Minutes</option>
							<option value="h">Hours</option>
							<option value="d">Days</option>
							<option value="w">Weeks</option>
							<option value="mn">Months</option>
							<option value="y">Years</option>
						</select>			
				</fieldset>
				<br class="clear" />
				
				<label for="reason">Reason:</label>
					<input type="text" name="reason" id="reason" />
					
				<input type="hidden" name="token" value="Banned with Echelon!" />
				<input type="submit" name="ban-sub" value="Ban User" />
			</form>
		</div>
		<div id="cd-act-lvl" class="act-slide">
			<?php
				$b3_groups = $db->getB3Groups(); // get a list of all B3 groups from the B3 DB
			?>
			<form action="actions/level.php" method="post">
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
					</select>
					
				<input type="hidden" name="token" value="" />
				<input type="submit" name="level-sub" value="Change Level" />
			</form>
		</div>
		<div id="cd-act-mask" class="act-slide">
			<form action="actions/level.php" method="post">
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
					
				<input type="hidden" name="token" value="" />
				<input type="submit" name="mlevel-sub" value="Change Mask" />
			</form>
		</div>
	</div><!-- end #actions-box -->
</div><!-- end #actions -->

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
		$query = "SELECT alias, num_used, time_add, time_edit FROM aliases WHERE client_id = ? AND time_add != time_edit ORDER BY time_edit DESC";
		$stmt = $db->mysql->prepare($query) or die('Alias Database Query Error'. $db->mysql->error);
		$stmt->bind_param('i', $cid);
		$stmt->execute();
		$stmt->bind_result($alias, $num_used, $time_add, $time_edit);
		
		$rowcolor = 0;
		while($stmt->fetch()) :

			$time_add = date($tformat, $time_add);
			$time_edit = date($tformat, $time_edit);
			
			$rowcolor = 1 - $rowcolor;
			
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
			
			$token_del = genFormToken('del'.$id);		
			
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td><strong>$alias</td>
				<td>$num_used</td>
				<td><em>$time_add</em></td>
				<td><em>$time_edit</em></td>
			</tr>
EOD;
			echo $data;
		
		endwhile;	
	?>
	</tbody>
</table>

<div id="penalties">
	<h3 class="cd-h" id="cd-h-pen" rel="cd-table-pen">Penalties <img class="cd-open" src="images/add.png" alt="Open" /></h3>
	<table class="cd-table-fold" id="cd-table-pen" rel="<?php echo $cid; ?>">
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
			<tr id="cd-tr-load-pen"><td colspan="7"><img class="load-large" id="load-pen" src="images/indicator-large.gif" alt="Loading....." title="Loading penalty data for <?php echo $name; ?>" /></td></tr>
		</tbody>
	</table>
</div>
<?php require 'inc/footer.php'; ?>