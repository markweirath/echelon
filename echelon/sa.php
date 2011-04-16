<?php
$page = "sa";
$page_title = "Site Adminisration";

if(isset($_GET['t'])) {
	if($_GET['t'] == 'perms' OR $_GET['t'] == 'perms-group' OR $_GET['t'] == 'perms-add')
		$auth_name = 'edit_perms';
		
	elseif($_GET['t'] == 'user')
		$auth_name = 'siteadmin';
		
	elseif($_GET['t'] == 'edituser')
		$auth_name = 'edit_user';
		
} else {
	$auth_name = 'siteadmin';
	
}

## Require the inc files and start up class ##
require 'inc.php';

// If this is a view a user in more detail page
if($_GET['t'] == 'user') :
	$id = $_GET['id'];
	if(!isID($id)) {
		set_error('Invalid data sent. Request aborted.');
		send('sa.php');
	}
	
	## Get a users details
	$result = $dbl->getUserDetails($id);
	if(!$result) { // there was no user matching the sent id // throw error and sedn to SA page
		set_error("That user doesn't exist, please select a real user");
		send('sa.php');
		exit;
	} else {
		## Setup information vars ##
		$username = $result[0];
		$display = $result[1];
		$email = $result[2];
		$ip = $result[3];
		$group = $result[4];
		$admin_id = $result[5];
		$first_seen = $result[6];
		$last_seen = $result[7];
		$admin_name = $result[8];
	}
	
	$ech_logs = $dbl->getEchLogs($id, NULL, 'admin'); // get the echelon logs created by this user (note: admin_id is admin group not the id stored in log)
	
	$token_del = genFormToken('del'.$id);

	$is_view_user = true;
endif; // end 

// if this is an edit user page
if($_GET['t'] == 'edituser') :
	if(!isID($_GET['id'])) {
		set_error('Invalid data sent. Request aborted.');
		send('sa.php');
	} else
		$uid = $_GET['id'];
	
	## Get a users details
	$result = $dbl->getUserDetailsEdit($uid);
	if(!$result) { // there was no user matching the sent id // throw error and sedn to SA page
		set_error('No user matches that id.');
		send('sa.php');
		exit;
	} else {
		## Setup information vars ##
		$u_username = $result[0];
		$u_display = $result[1];
		$u_email = $result[2];
		$u_group_id = $result[3];
	}
	
	// setup form token
	$ad_edit_user_token = genFormToken('adedituser');
	
	// get the names and id of all B3 Groups for select menu
	$ech_groups = $dbl->getGroups();
	
	// set referance var
	$is_edit_user = true;

endif;

## Permissions Setup ##
if($_GET['t'] == 'perms') :

	$is_permissions = true; // helper var
	$page = "perms";
	$page_title = "Echelon Group Management";

endif;

if($_GET['t'] == 'perms-group') :
	
	$group_id = cleanvar($_GET['id']);
	$group_id = (int)$group_id;
	$is_perms_group = true; // helper var
	
	$group_info = $dbl->getGroupInfo($group_id);

	$group_name = $group_info[0];
	$group_perms = $group_info[1];
	$page = "perms";
	$page_title = $group_name." Group";
	

endif;

if($_GET['t'] == 'perms-add') :

	$is_perms_group_add = true;

endif;

## Require Header ##	
require 'inc/header.php';

if($is_edit_user) : 

	echo echUserLink($uid, $u_display, null, '&laquo; Go Back');
?>

	<fieldset>
		<legend>Edit <?php echo $u_display; ?></legend>
		
		<form action="actions/user-edit.php" method="post">
			
			<label for="display">Display Name:</label>
				<input type="text"  name="display" id="display" value="<?php echo $u_display; ?>" /><br />
			
			<label for="username">Username:</label>
				<input type="text" name="username" id="username" value="<?php echo $u_username; ?>" /><br />
				
			<label for="email">Email Address:</label>
				<input type="text" name="email" id="email" value="<?php echo $u_email; ?>" /><br />
				
			<label for="group">Group</label>
				<select name="group" id="group">
					<?php foreach($ech_groups as $group) :
						if($group['id'] == $u_group_id)
							echo '<option value="'.$group['id'].'" selected="selected">'.$group['display'].'</option>';
						else
							echo '<option value="'.$group['id'].'">'.$group['display'].'</option>';
					endforeach; ?>
				</select><br />
			
			<input type="hidden" name="token" value="<?php echo $ad_edit_user_token; ?>" />
			<input type="hidden" name="id" value="<?php echo $uid; ?>" />
				
			<input type="submit" name="ad-edit-user" value="Edit <?php echo $u_display; ?>" />
			
		</form>
		
	</fieldset>

<?php elseif($is_view_user) : ?>
	<a href="sa.php" title="Go back to site admin page" class="float-left">&laquo; Site Admin</a>
	<span class="float-right"><span class="float-left"><?php echo delUserLink($id, $token_del)?></span><?php echo editUserLink($id, $name); ?></span>
	
	<table class="user-table">
		<caption><img src="images/cd-page-icon.png" width="32" height="32" alt="" /><?php echo $display; ?><small>Everything Echelon knows about <?php echo $display; ?></small></caption>
		<tbody>
			<tr>
				<th>Name</th>
					<td><?php echo  tableClean($username); ?></td>
				<th>Display Name</th>
					<td><?php echo $display; ?></td>
			</tr>
			<tr>
				<th>Email</th>
					<td><?php echo emailLink($email, $display); ?></td>
				<th>IP Address</th>
					<td><?php echo ipLink($ip); ?></td>
			</tr>
			<tr>
				<th>First Seen</th>
					<td><?php echo date($tformat, $first_seen); ?></td>
				<th>Last Seen</th>
					<td><?php echo date($tformat, $last_seen); ?></td>
			</tr>
			<tr>
				<th>Creator</th>
					<td colspan="3"><?php echo echUserLink($admin_id, $admin_name); ?></td>
			</tr>
		</tbody>
	</table>
	

	<table>
		<caption>Echelon Logs<small>created by <?php echo $display; ?></caption>
		<thead>
			<tr>
				<th>id</th>
				<th>Type</th>
				<th>Message</th>
				<th>Time Added</th>
				<th>Client</th>
				<th>Game</th>
			</tr>
		</thead>
		<tfoot>
			<tr><th colspan="5"></th></tr>
		</tfoot>
		<tbody>
			<?php displayEchLog($ech_logs, 'admin'); ?>
		</tbody>
	</table>
	
<?php elseif($is_permissions) : ?>
	
	<a href="sa.php" title="Go back to site admin page" class="float-left">&laquo; Site Admin</a>
	
	<a href="sa.php?t=perms-add" title="Add a new Echelon group" class="float-right">Add Group &raquo;</a><br />

	<table>
		<caption>Groups<small>A list of all the Echelon Groups</caption>
		<thead>
			<tr>
				<th>id</th>
				<th>Group Name</th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3"></td>
			</tr>
		</tfoot>
		
		<tbody>
			<?php
				$ech_list_groups = $dbl->getGroups();
				
				$num_rows = count($ech_list_groups);
				
				if($num_rows > 0) :
					foreach($ech_list_groups as $group):
						$id = $group['id'];
						$name = $group['display'];
						
						$alter = alter();
						$name_link = echGroupLink($id, $name);
						
						// setup heredoc (table data)			
						$data = <<<EOD
						<tr class="$alter">
							<td>$id</td>
							<td><strong>$name_link</strong></td>
							<td>&nbsp;</td>
						</tr>
EOD;

						echo $data;
					endforeach;
				else:
				
					echo '<tr><td colspan="3">There are no groups in the Echelon database. <a href="sa.php?t=perms-add" title="Add a new group to Echelon">Add Group</a></td></tr>';
				
				endif;
			
			?>
		</tbody>
	</table>
	
<?php elseif($is_perms_group) : ?>

	<a href="sa.php?t=perms" title="Go back to permissions management homepage" class="float-left">&laquo; Permissions</a><br />

	<fieldset>
		<legend>Permissions for the <?php echo $group_name; ?> Group</legend>
		
		<form action="actions/perms-edit.php?gid=<?php echo $group_id; ?>" method="post">
		
		<table id="perms">
		<tbody>
		<?php
			$perms_token = genFormToken('perm-group-edit');
		
			$perms = $dbl->getPermissions(); // gets a comprehensive list of Echelon groups
		
			$perms_list = array();
			$perms_list = explode(",", $group_perms);
			
			$perms_count = count($perms);
			$rows = ceil($perms_count/5) + 1;
			$ir = 1;
			$in = 0;
			
			while($ir < $rows) :
			
				echo '<tr>';
			
				$i = 1;
			
				while($i <= 5) :
				
					$p_id = $perms[$in]['id'];
					$p_name = $perms[$in]['name'];
					$p_desc = $perms[$in]['desc'];
					
					if(in_array($p_id, $perms_list))
						$checked = 'checked="checked" ';
					else
						$checked =  NULL;
					
					if($p_name != 'pbss') {
						$p_name_read = preg_replace('#_#', ' ', $p_name);
						$p_name_read = ucwords($p_name_read);
					} else
						$p_name_read = 'PBSS';
					
					if($p_id != "") :
						echo '<td class="perm-td"><label for="'. $p_name .'">' . $p_name_read . '</label><input id="'.$p_name.'" type="checkbox" name="' . $p_name . '" ' . $checked . ' />'; 
						tooltip($p_desc);
						echo '</td>';						
					endif;
					
					$in++;
					$i++;
				
				endwhile;
				
				echo '</tr>';
				
				$ir++;
				
			endwhile;
		?>
		</tbody>
		</table>
		
			<br />
			<input type="hidden" name="token" value="<?php echo $perms_token; ?>" />
			<input type="submit" value="Save Changes" />
		
		</form>
		
	</fieldset>	
	
<?php elseif($is_perms_group_add) : ?>
	
	<fieldset>
	
	<legend>Add Echelon Group</legend>
	
	<form action="actions/perms-edit.php?t=add" method="post">
	
		<label for="g-name">Name of Group:</label>
			<input type="text" name="g-name" id="g-name" />
		
		<fieldset class="none" id="perms-fs">
		
		<legend>Group Premissions</legend>
		
		<table id="perms">
		<tbody>
		<?php
		
			$add_g_token = genFormToken('perm-group-add');
		
			$perms = $dbl->getPermissions(); // gets a comprehensive list of Echelon groups
			
			$perms_count = count($perms);
			$rows = ceil($perms_count/5) + 1;
			$ir = 1;
			$in = 0;
			
			while($ir < $rows) :
			
				echo '<tr>';
			
				$i = 1;
			
				while($i <= 5) :
				
					$p_id = $perms[$in]['id'];
					$p_name = $perms[$in]['name'];
					$p_desc = $perms[$in]['desc'];
					
					$p_name_read = preg_replace('#_#', ' ', $p_name);
					$p_name_read = ucwords($p_name_read);
					
					if($p_id != "") :
						echo '<td class="perm-td"><label for="'. $p_name .'">' . $p_name_read . '</label><input id="'.$p_name.'" type="checkbox" name="' . $p_name . '" />'; 
						tooltip($p_desc);
						echo '</td>';						
					endif;
					
					$in++;
					$i++;
				
				endwhile;
				
				echo '</tr>';
				
				$ir++;
				
			endwhile;
		
		?>
		</tbody>
		</table>
		
		</fieldset>
		
		<br />
		<input type="hidden" name="token" value="<?php echo $add_g_token; ?>" />
		<input type="submit" value="Add Group" />
	
	</form>
	
	</fieldset>
	
<?php else : ?>
<a href="sa.php?t=perms" title="Manage Echelon User Permissions" class="float-right">User Permissions &raquo;</a><br />

<table summary="A list of people who have access to login to Echelon">
	<caption>Echelon Users<small>A list of all people who can login to Echelon.</small></caption>
	<thead>
		<tr>
			<?php if(GRAVATAR) echo '<th></th>'; ?>
			<th>id</th>
			<th>Name</th>
			<th>Group</th>
			<th>Email</th>
			<th>IP Address</th>
			<th>First Seen</th>
			<th>Last Seen</th>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<?php 
				if(GRAVATAR)
					echo '<th colspan="9"></th>';
				else
					echo '<th colspan="8"></th>';
			?>
		</tr>
	</tfoot>
	<tbody>
	<?php
		$users_data = $dbl->getUsers();
		
		foreach($users_data['data'] as $users): // get data from query and loop
			$id = $users['id'];
			$name = $users['display'];
			$group = $users['namep'];
			$email = $users['email'];
			
			$time_add = date($tformat, $users['first_seen']);
			$time_edit = date($tformat, $users['last_seen']);
			$ip = ipLink($users['ip']);
			$email_link = emailLink($email, $name);
			
			if(GRAVATAR) // if use gravatar
				$grav = '<td>'.$mem->getGravatar($email).'</td>';
			
			$alter = alter();
			$token_del = genFormToken('del'.$id);
			$name_link = echUserLink($id, $name);
			$user_img_link = echUserLink($id, '<img src="images/user_view.png" alt="view" />', $name);
			$user_edit_link = editUserLink($id, $name);
			$user_del_link = delUserLink($id, $token_del);
			
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				$grav
				<td>$id</td>
				<td><strong>$name_link</strong></td>
				<td>$group</td>
				<td>$email_link</td>
				<td>$ip</td>
				<td><em>$time_add</em></td>
				<td><em>$time_edit</em></td>
				<td class="actions">
					$user_del_link
					$user_edit_link
					$user_img_link
				</td>
			</tr>
EOD;

		echo $data;
		endforeach;
	?>
	</tbody>
</table>
<?php
	$ech_groups = $dbl->getGroups();
	$add_user_token = genFormToken('adduser');
?>
<fieldset>
	<legend>Add Echelon User</legend>
	<form action="actions/user-add.php" method="post" id="add-user-form">
		<div class="left-side">
			<label for="au-email">Email of User:</label>
				<input type="text" name="email" id="au-email" value="" />
			<label for="group">User Group:</label>
				<select name="group">
					<?php foreach($ech_groups as $group) :
						echo '<option value="'.$group['id'].'">'.$group['display'].'</option>';
					endforeach; ?>
				</select>
		</div>
		<label for="au-comment">Comment:</label><br />
			<textarea name="comment" id="au-comment" rows="6" cols="25"></textarea>
			
		<input type="hidden" name="token" value="<?php echo $add_user_token; ?>" />
		
		<input id="add-user" type="submit" name="add-user" value="Add User">
	</form>
</fieldset>


<div style="height:30px;"></div>


<table summary="A list of valid keys for Echelon registration">
	<caption>Registration Keys<small>A list of valid keys for Echelon registrations</small></caption>
	<thead>
		<tr>
			<th>Registration Key</th>
			<th>Email <small>(assoc. with key)</small></th>
			<th>Admin</th>
			<th>Comment</th>
			<th>Added</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="6"></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
		$counter = 1;
		$keys_data = $dbl->getKeys($key_expire);
		
		$num_rows = $keys_data['num_rows'];
		
		if($num_rows > 0) :
		
		foreach($keys_data['data'] as $reg_keys): // get data from query and loop
		
			$reg_key = $reg_keys['reg_key']; // the reg key
			$comment = cleanvar($reg_keys['comment']); // comment about key
			$time_add = date($tformat, $reg_keys['time_add']);
			$email = emailLink($reg_keys['email'], '');
			$admin_link = echUserLink($reg_keys['admin_id'], $reg_keys['display']);
			
			$alter = alter();
			
			$token_keydel = genFormToken('keydel'.$reg_key);
			
			if($mem->id == $admin_id) // if the current user is the person who create the key allow the user to edit the key's comment
				$edit_comment = '<img src="" alt="[Edit]" title="Edit this comment" class="edit-key-comment" />';
			else
				$edit_comment = '';
			
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td class="key">$reg_key</td>
				<td>$email</td>
				<td>$admin_link</td>
				<td><span class="comment">$comment</span> $edit_comment</td>
				<td><em>$time_add</em></td>
				<td class="actions">
					<form action="actions/key-edit.php" method="post" id="regkey-del-$counter">
						<input type="hidden" value="$token_keydel" name="token" />
						<input type="hidden" value="$reg_key" name="key" />
						<input type="hidden" value="del" name="t" />
						<input type="submit" name="keydel" value="Delete" class="action del harddel" title="Delete this registraion key" />
					</form>
				</td>
			</tr>
EOD;

			echo $data;
			$counter++;
		endforeach;
		
		else:
		
			echo '<tr><td colspan="6">There are no registration keys active on file</td></tr></tr>';
		
		endif;	
	?>
	</tbody>
</table>


<div style="height:30px;"></div>


<table summary="A list of people banned from accessing this website">
	<caption>Echelon Blacklist<small>A list of people banned from accessing this website.</small></caption>
	<thead>
		<tr>
			<th>id</th>
			<th>IP Address</th>
			<th>Active</th>
			<th>Comment</th>
			<th>Admin</th>
			<th>Added</th>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="7"></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
		$bl_data = $dbl->getBL();
		$num_rows = $bl_data['num_rows'];
		
		if($num_rows > 0) :
		
			foreach($bl_data['data'] as $bl): // get data from query and loop
				$id = $bl['id'];			
				$ip = $bl['ip'];
				$active = $bl['active'];
				$reason = $bl['reason'];	
				$time_add = $bl['time_add'];
				$admin = $bl['admin'];
				
				$time_add = date($tformat, $time_add);
				$ip = ipLink($ip);		
					
				$alter = alter();
					
				$token = genFormToken('act'.$id);

				if($active == 1) {
					$active = 'Yes';
					$actions = '<form action="actions/blacklist.php" method="post">
						<input type="hidden" name="id" value="'.$id.'" />
						<input type="hidden" name="token" value="'.$token.'" />
						<input type="submit" name="deact" value="De-active" class="action del" title="De-active this ban" />
						</form>';
				} else {
					$active = 'No';
					$alter .= " inact";
					$actions = '<form action="actions/blacklist.php" method="post">
						<input type="hidden" name="id" value="'.$id.'" />
						<input type="hidden" name="token" value="'.$token.'" />
						<input type="submit" name="react" value="Re-active" class="action plus" title="Re-active this ban" />
						</form>';
				}
				
				unset($token);
			
				if($admin == '')
					$admin = 'Auto Added';
				
				// setup heredoc (table data)			
				$data = <<<EOD
				<tr class="$alter">
					<td>$id</td>
					<td><strong>$ip</strong></td>
					<td>$active</td>
					<td>$reason</td>
					<td>$admin</td>
					<td><em>$time_add</em></td>
					<td>
						$actions
					</td>
				</tr>
EOD;

			echo $data;
			endforeach;
			
		else:
		
			echo '<tr><td colspan="7">There are no IPs on the blacklist</td></tr>';
		
		endif;
	?>
	</tbody>
</table>

<fieldset>
	<legend>Add to Blacklist</legend>
	<form action="actions/blacklist.php" method="post" id="add-bl-form">
		<div class="left-side" style="width: auto;">
			<label for="bl-reason">Reason:</label>
				<textarea rows="6" cols="18" name="reason" id="bl-reason" class="clr-txt">Enter a reason for this ban...</textarea>
		</div>
		<div class="left-side">
			<label for="bl-ip" class="ip-label">IP Address:</label>
				<input type="text" name="ip" id="bl-ip" /><br />
				
			<?php $bl_token = genFormToken('addbl'); ?>
			<input type="hidden" name="token" value="<?php echo $bl_token; ?>" />
				
			<input id="add-user-step-2" type="submit" value="Ban IP Address" />
		</div>
	</form>
</fieldset>

<?php
	endif; // end if on what kind of page this is
	require 'inc/footer.php'; 
?>