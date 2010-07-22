<?php
$auth_name = 'clients'; // this page is add user, so that if you can add keys you should be able to remove them
$b3_conn = true; // this page requries access to the B3 DB
require '../../inc.php';

$cid = $_GET['id']; 
if($cid == '')
	echo '<tr class="table-error"><td colspan="7"><span>No user selected, please send a client id</span></td></tr>'; // return message of no id sent

$type = 'client';
if($_GET['type'] == 'admin')
	$type = 'admin';
else
	$type = 'client';

if($type == 'client')
	$query = "SELECT p.id, p.type, p.time_add, p.time_expire, p.reason, p.data, p.inactive, p.duration, 
	COALESCE(c.id,'1') as admin_id, COALESCE(c.name, 'B3') as admin_name 
	FROM penalties p LEFT JOIN clients c ON c.id = p.admin_id WHERE p.client_id = ? ORDER BY id DESC";
else
	$query = "SELECT p.id, p.type, p.time_add, p.time_expire, p.reason, p.data, p.inactive, p.duration, 
	COALESCE(c.id,'1') as admin_id, COALESCE(c.name, 'B3') as admin_name 
	FROM penalties p LEFT JOIN clients c ON c.id = p.client_id WHERE p.admin_id = ? ORDER BY id DESC";

$stmt = $db->mysql->prepare($query) or die('<tr class="table-good"><td colspan="7"><span>Problem getting records from the database</span></td></tr>');
$stmt->bind_param('i', $cid); // bind in the client_id for the query
$stmt->execute(); // run query
$stmt->store_result(); // store the result - needed for the num_rows check

if($stmt->num_rows) : // if results exist
	$stmt->bind_result($pid, $type, $time_add, $time_expire, $reason, $data, $inactive, $duration, $admin_id, $admin_name);

	while($stmt->fetch()) : // fetcht the results and store in an array
			
		// Change into readable times
		$time_add = date($tformat, $time_add);
		
		$time_expire_read = timeExpire($time_expire, $type, $inactive);
		$reason = tableClean(removeColorCode($reason));
		$data = tableClean($data);
		$admin_name = tableClean($admin_name);
		
		if($type != 'Kick' && $type != 'Notice' && $time_expire != '-1')
			$duration = time_duration($duration*60, 'yMwdhm'); // all penalty durations are stored in minutes, so multiple by 60 in order to get seconds
		else
			$duration = '';
		
		// Row odd/even colouring
		$alter = alter();
			
		if($admin_id != 1) // if admin is not B3 show clientdetails link else show just the name
			$admin_link = '<a href="clientdetails.php?id='.$admin_id.'" title="View the admin\'s client page">'.$admin_name.'</a>';
		else
			$admin_link = $admin_name;
		
		if($mem->reqLevel('unban')) // if user has access to unban show unban button
			$unban = unbanButton($pid, $cid, $type, $inactive);
		else
			$unban = '';
			
		if($mem->reqLevel('edit_ban')) // if user  has access to edit bans show the button
			$edit_ban = editBanButton($type, $pid, $inactive);
		else
			$edit_ban = '';
		
		$row = <<<EOD
		<tr class="$alter">
			<td>$pid<br /> $unban $edit_ban</td>
			<td>$type</td>
			<td>$time_add</td>
			<td>$duration</td>
			<td>$time_expire_read</td>
			<td>$reason<br /><em>$data</em></td>
			<td>$admin_link</td>	
		</tr>
EOD;

		echo $row;
		
	endwhile;
	
else : // if no results
	
	if($type == 'client')
		echo '<tr class="table-good"><td colspan="7"><span>This user has no recorded penalties!</span></td></tr>';
	else 
		echo '<tr class="table-good"><td colspan="7"><span>This user has no recorded admin actions!</span></td></tr>';

endif;

$stmt->close(); // close the stmt connection