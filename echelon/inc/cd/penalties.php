<?php
$auth_name = 'clients'; // this page is add user, so that if you can add keys you should be able to remove them
$b3_conn = true; // this page requries access to the B3 DB
require '../../inc.php';

$cid = $_GET['id']; 
if($cid == '')
	echo '<tr class="table-error"><td colspan="7"><span>That user id does not match any known users</span></td></tr>'; // return message of no id sent

$query = "SELECT p.id, p.type, p.time_add, p.time_expire, p.reason, p.data, p.inactive, p.duration, 
			COALESCE(c.id,'1') as admin_id, COALESCE(c.name, 'B3') as admin_name 
			FROM penalties p LEFT JOIN clients c ON c.id = p.admin_id
			WHERE p.client_id = ? ORDER BY id DESC";
$stmt = $db->mysql->prepare($query);
$stmt->bind_param('i', $cid); // bind in the client_id for the query
$stmt->execute(); // run query
$stmt->store_result(); // store the result - needed for the num_rows check

if($stmt->num_rows) : // if results exist
	$stmt->bind_result($pid, $type, $time_add, $time_expire, $reason, $data, $inactive, $duration, $admin_id, $admin_name);
	
	$rowcolor = 0;
	while($stmt->fetch()) : // fetcht the results and store in an array
			
		// Change into readable times
		$time_add = date($tformat, $time_add);
		$time_expire = date($tformat, $time_expire);
		$reason = tableClean(removeColorCode($reason));
		$admin_name = tableClean($admin_name);
		
		// Row odd/even
		$rowcolor = 1 - $rowcolor;
		if($rowcolor == 0)
			$odd_even = "odd";
		else 
			$odd_even = "even";
		
		$row = <<<EOD
		<tr class="$odd_even">
			<td>$pid</td>
			<td>$type</td>
			<td>$time_add</td>
			<td>$duration</td>
			<td>$time_expire</td>
			<td>$reason<br /><em>$data</em></td>
			<td><a href="clientdetails.php?id=$admin_id" title="View the admin's client page">$admin_name</a></td>	
		</tr>
EOD;

		echo $row;
		
	endwhile;
	
else : // if no results

	echo '<tr class="table-error"><td colspan="7"><span>This user has no recorded penalties!</span></td></tr>';

endif;

$stmt->close(); // close the stmt connection

?>