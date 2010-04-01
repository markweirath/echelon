<?php
if($_GET['t'] == 'a') :
	$page = "adminbans";
	$page_title = "Admin Bans";
	$type_admin = true;
else :
	$page = "b3pen";
	$page_title = "B3 Penalties";
	$type_admin = false; // this is not an admin page
endif;
	
$auth_name = 'penalties';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_add";
$order = "desc";

//$limit_rows = 75; // limit_rows can be set by the DB settings // uncomment this line to manually overide the number of table rows per page

## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o'])
	$order = addslashes($_GET['o']);

// allowed things to sort by
$allowed_orderby = array('target_name', 'type', 'time_add', 'duration', 'time_expire');
if(!in_array($orderby, $allowed_orderby)) // Check if the sent varible is in the allowed array 
	$orderby = 'time_add'; // if not just set to default id

## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;


###########################
######### QUERIES #########
if($type_admin)
	$query = "SELECT p.type, p.time_add, p.time_expire, p.reason, p.duration, target.id as target_id, target.name as target_name, c.id as admins_id, c.name as admins_name FROM penalties p, clients c, clients as target WHERE admin_id != '0' AND (p.type = 'Ban' OR p.type = 'TempBan') AND inactive = 0 AND p.time_expire <> 0 AND p.client_id = target.id AND p.admin_id = c.id";
else
	$query = "SELECT p.type, p.time_add, p.time_expire, p.reason, p.duration, p.client_id, c.name FROM penalties p LEFT JOIN clients c ON p.client_id = c.id WHERE p.admin_id = 0 AND (p.type = 'Ban' OR p.type = 'TempBan') AND p.inactive = 0";


$query .= sprintf(" ORDER BY %s ", $orderby);

## Append this section to all queries since it is the same for all ##
if($order == "desc")
	$query .= " DESC"; // set to desc 
else
	$query .= " ASC"; // default to ASC if nothing adds up

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Prepare and run Query ##
$stmt = $db->mysql->prepare($query_limit) or die('Database Error: '.$db->mysql->error);
$stmt->execute();
$stmt->store_result();
$num_rows = $stmt->num_rows;

if($type_admin) :
	$stmt->bind_result($type, $time_add, $time_expire, $reason, $duration, $client_id, $client_name, $admin_id, $admin_name);

	while($stmt->fetch()) : // get results and put results in an array
		$pens_data[] = array(
			'type' => $type,
			'time_add' => $time_add,
			'time_expire' => $time_expire,
			'reason' => $reason,
			'duration' => $duration,
			'client_id' => $client_id,
			'client_name' => $client_name,
			'admin_id' => $admin_id,
			'admin_name' => $admin_name
		);
	endwhile;
	
else :
	$stmt->bind_result($type, $time_add, $time_expire, $reason, $duration, $client_id, $client_name);

	while($stmt->fetch()) : // get results and put results in an array
		$pens_data[] = array(
			'type' => $type,
			'time_add' => $time_add,
			'time_expire' => $time_expire,
			'reason' => $reason,
			'duration' => $duration,
			'client_id' => $client_id,
			'client_name' => $client_name,
		);
	endwhile;

endif;

$stmt->free_result(); // free the data in memory from store_result
$stmt->close(); // closes the prepared statement

## Require Header ##	
require 'inc/header.php';

if($type_admin) :
	echo '<table summary="A list of '.$limit_rows.' active tempbans/bans made by admins in a servers">';
		echo '<caption>Admin Bans<small>There are <strong>'. $total_rows .'</strong> active bans/tempbans that have been added by admins</caption>';
else :
	echo '<table summary="A list of '.$limit_rows.' active tempbans/bans made by B3 in a servers">';
		echo '<caption>B3 Bans<small>There are <strong>'. $total_rows .'</strong> active bans/tempbans that have been added by the B3 bot</caption>';
endif;
?>	
	<thead>
		<tr>
			<th>Target
				<?php linkSort('target_name', 'Name'); ?>
			</th>
			<th>Type
				<?php linkSort('type', 'penalty type'); ?>
			</th>
			<th>Added
				<?php linkSort('time_add', 'time the penalty was added'); ?>
			</th>
			<th>Duration
				<?php linkSort('duration', 'duration of penalty'); ?>
			</th>
			<th>Expires
				<?php linkSort('time_expire', 'time the penalty expires'); ?>
			</th>
			<th>Reason</th>
			<?php 
				if($type_admin) { // only the admin type needs this header line
					echo '<th>Admin</th>';
				}
			?>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<?php 
				if($type_admin) { // admin type has 7 cols
					echo '<th colspan="7"></th>';
				} else { // the b3 type has only 6 cols
					echo '<th colspan="6"></th>';
				}
			?>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$rowcolor = 0;

	 if($num_rows > 0) { // query contains stuff

		foreach($pens_data as $pen): // get data from query and loop
			$type = $pen['type'];
			$time_add = $pen['time_add'];
			$time_expire = $pen['time_expire'];
			$reason = tableClean($pen['reason']);
			$duration = $pen['duration'];
			$client_id = $pen['client_id'];
			$client_name = tableClean($pen['client_name']);
			
			if($type_admin) { // only admin type needs these lines
				$admin_id = $pen['admin_id'];
				$admin_name = tableClean($pen['admin_name']);
			}

			## Tidt data to make more human friendly
			if($time_expire != '-1')
				$duration_read = time_duration($duration*60); // all penalty durations are stored in minutes, so multiple by 60 in order to get seconds
			else
				$duration_read = '';

			$time_expire_read = timeExpirePen($time_expire, $tformat);
			$time_add_read = date($tformat, $time_add);
			$reason_read = removeColorCode($reason);
			
			if($type_admin) { // admin cell only needed for admin type
				$admin_line = '<td><strong><a href="clientdetails.php?id='.$admin_id.'" title="View more information on '.$admin_name.'">'.$admin_name.'</a></strong></td>';
			} else {
				$admin_line = '';
			}

			## Row color
			$rowcolor = 1 - $rowcolor;	
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";

			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td><strong><a href="clientdetails.php?id=$client_id" title="View more information on $client_name">$client_name</a></strong></td>
				<td>$type</td>
				<td>$time_add_read</td>
				<td>$duration_read</td>
				<td>$time_expire_read</td>
				<td>$reason_read</td>
				$admin_line
			</tr>
EOD;

			echo $data;
		endforeach;
		
		$no_data = false;
	} else {
		$no_data = true;
		if($type_admin) // slight chnages between different page types
			echo '<tr class="odd"><td colspan="7">There no tempbans or bans made by admins in the database</td></tr>';
		else
			echo '<tr class="odd"><td colspan="6">There no tempbans or bans made by the B3 bot in the database</td></tr>';
	} // end if query contains
	?>
	</tbody>
</table>

<?php require 'inc/footer.php'; ?>