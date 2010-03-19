<?php
$page = "sa";
$page_title = "Site Adminisration";
$auth_name = 'siteadmin';
## Require the inc files and start up class ##
require 'inc.php';

// If this is a view a user in more detail page
if($_GET['t'] == 'user') :
	$id = $_GET['id'];
	if(!is_numeric($id)) {
		set_error('Invalid data sent. Request aborted.');
		send('sa.php');
	}
	
	## Get a users details
	$result = $dbl->getUserDetails($id);
	if(!$result) { // there was no user matching the sent id // throw error and sedn to SA page
		set_error('No user matches that id.');
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

	if($admin_name == '') { // if the admin_id matches no user then set name as unknown
		$admin_name = 'Unknown';
		$admin_id = 0;	
	}

	$is_view_user = true;
endif; // end 

// if this is an dit user page
if($_GET['t'] == 'edituser') :
	$uid = $_GET['id'];
	if(!is_numeric($uid)) {
		set_error('Invalid data sent. Request aborted.');
		send('sa.php');
	}
	
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

## Require Header ##	
require 'inc/header.php';

if($is_edit_user) : ?>

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
	<a href="sa.php" title="Go back to the site admin page">&laquo; Go Back</a>
	<a href="sa.php?t=edituser&amp;id=<?php echo $id; ?>" title="Go back to the site admin page" class="float-right">Edit this user &raquo;</a>
	<fieldset>
		<legend>User: <?php echo $display; ?></legend>
			<p>Username: <?php echo $username; ?></p>
			<p>Display Name: <?php echo $display; ?></p>
			<p>Email: <?php echo emailLink($email, $display); ?></p>
			<p>IP Address: <?php echo ipLink($ip); ?></p>
			<p>First Seen: <?php echo date($tformat, $first_seen); ?></p>
			<p>Last Seen: <?php echo date($tformat, $last_seen); ?></p>
			<p>Creator: <?php echo echUserLink($admin_id, $admin_name); ?></p>
	</fieldset>

<?php else : ?>
<table summary="A list of people who have access to login to Echelon">
	<caption>Echelon Users<small>A list of all people who can login to Echelon.</small></caption>
	<thead>
		<tr>
			<th></th>
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
			<th colspan="9"></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
		$rowcolor = 0;
		$users_data = $dbl->getUsers();
		foreach($users_data as $users): // get data from query and loop
			$id = $users['id'];
			$name = $users['display'];
			$email = $users['email'];
			$ip = $users['ip'];
			$time_add = $users['first_seen'];
			$time_edit = $users['last_seen'];
			$group = $users['group'];
			
			
			$time_add = date($tformat, $time_add);
			$time_edit = date($tformat, $time_edit);
			$ip = ipLink($ip);
			$email_link = emailLink($email, $name);
			
			$grav_url = $mem->getGravatar($email);
			
			$rowcolor = 1 - $rowcolor;
			
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
			
			$token_del = genFormToken('del'.$id);
			
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td><img src="$grav_url" alt="" /></td>
				<td>$id</td>
				<td><strong><a href="sa.php?t=user&amp;id=$id" title="View $name in more detail">$name</a></strong></td>
				<td>$group</td>
				<td>$email_link</td>
				<td>$ip</td>
				<td><em>$time_add</em></td>
				<td><em>$time_edit</em></td>
				<td class="actions">
					<form action="actions/user-edit.php" method="post" id="user-del">
						<input type="hidden" value="$token_del" name="token" />
						<input type="hidden" value="$id" name="id" />
						<input type="hidden" value="del" name="t" />
						<input class="harddel" type="image" src="images/user_del.png" alt="Delete" title="Delete this user forever" />
					</form>
					<a href="sa.php?t=edituser&amp;id=$id" title="Edit $name"><img src="images/user_edit.png" alt="edit" /></a>
					<a href="sa.php?t=user&amp;id=$id" title="View $name in more detail"><img src="images/user_view.png" alt="view" /></a>
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
		$rowcolor = 0;
		$counter = 1;
		$keys_data = $dbl->getKeys($time_expire);
		foreach($keys_data as $reg_keys): // get data from query and loop
			$reg_key = $reg_keys['reg_key']; // the reg key
			$email = $reg_keys['email']; // email assoc with key
			$comment = $reg_keys['comment']; // comment about key
			$time_add = $reg_keys['time_add']; // time key was added
			$admin_id = $reg_keys['admin_id']; // id of admin who added key
			$admin = $reg_keys['admin']; // display name of admin who added key
			
			$time_add = date($tformat, $time_add);
			$email = emailLink($email, '');
			$admin_link = echUserLink($admin_id, $admin);
			$rowcolor = 1 - $rowcolor;
			
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
			
			$token_keydel = genFormToken('keydel'.$reg_key);
			
			if($_SESSION['user_id'] == $admin_id) { // if the current user is the person who create the key allow the user to edit the key's comment
				$edit_comment = '<img src="" alt="[Edit]" title="Edit this comment" class="edit-key-comment" />';
			} else {
				$edit_comment = '';
			}
			
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
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
		foreach($bl_data as $bl): // get data from query and loop
			$id = $bl['id'];			
			$ip = $bl['ip'];
			$active = $bl['active'];
			$reason = $bl['reason'];	
			$time_add = $bl['time_add'];
			$admin = $bl['admin'];
			
			$time_add = date($tformat, $time_add);
			$ip = ipLink($ip);		
				
			$rowcolor = 1 - $rowcolor;
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
				
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
				$odd_even .= " inact";
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
			<tr class="$odd_even">
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