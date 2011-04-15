<?php
if(!isset($_GET['t']))
	$t = 'b';
else
	$t = $_GET['t'];
	
if($t == 'a') :
	$page = "adminbans";
	$page_title = "Admin Bans";
	$type_admin = true;
else :
	$page = "b3bans";
	$page_title = "B3 Penalties";
	$type_admin = false; // this is not an admin page
endif;
	
$auth_name = 'penalties';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_add";
$order = "ASC";

## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o'])
	$order = addslashes($_GET['o']);

// allowed things to sort by
$allowed_orderby = array('client_name', 'type', 'time_add', 'duration', 'time_expire');
if(!in_array($orderby, $allowed_orderby)) // Check if the sent varible is in the allowed array 
	$orderby = 'time_add'; // if not just set to default id

## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;


###########################
######### QUERIES #########
if($type_admin)
	$query = "SELECT p.type, p.time_add, p.time_expire, p.reason, p.duration, target.id as client_id, target.name as client_name, c.id as admins_id, c.name as admins_name FROM penalties p, clients c, clients as target WHERE admin_id != '0' AND (p.type = 'Ban' OR p.type = 'TempBan') AND inactive = 0 AND p.time_expire <> 0 AND p.client_id = target.id AND p.admin_id = c.id";
else
	$query = "SELECT p.type, p.time_add, p.time_expire, p.reason, p.data, p.duration, p.client_id, c.name as client_name FROM penalties p LEFT JOIN clients c ON p.client_id = c.id WHERE p.admin_id = 0 AND (p.type = 'Ban' OR p.type = 'TempBan') AND p.inactive = 0";


$query .= sprintf(" ORDER BY %s ", $orderby);

## Append this section to all queries since it is the same for all ##
if($order == "DESC")
	$query .= " DESC"; // set to desc 
else
	$query .= " ASC"; // default to ASC if nothing adds up

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Require Header ##	
require 'inc/header.php';

if(!$db->error) :

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
				<?php linkSortType('client_name', 'Name', $t); ?>
			</th>
			<th>Type
				<?php linkSortType('type', 'penalty type', $t); ?>
			</th>
			<th>Added
				<?php linkSortType('time_add', 'time the penalty was added', $t); ?>
			</th>
			<th>Duration
				<?php linkSortType('duration', 'duration of penalty', $t); ?>
			</th>
			<th>Expires
				<?php linkSortType('time_expire', 'time the penalty expires', $t); ?>
			</th>
			<th>Reason</th>
			<?php if($type_admin) echo '<th>Admin</th>'; // only the admin type needs this header line ?>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<?php 
				if($type_admin) 
					echo '<th colspan="7"></th>'; // admin type has 7 cols
				else 
					echo '<th colspan="6"></th>'; // the b3 type has only 6 cols
			?>
		</tr>
	</tfoot>
	<tbody>
	<?php
	if($num_rows > 0) { // query contains stuff

		foreach($data_set as $data): // get data from query and loop
			$type = $data['type'];
			$time_add = $data['time_add'];
			$time_expire = $data['time_expire'];
			$reason = tableClean($data['reason']);
			$pen_data = tableClean($data['data']);
			$duration = $data['duration'];
			$client_id = $data['client_id'];
			$client_name = tableClean($data['client_name']);
			
			if($type_admin) { // only admin type needs these lines
				$admin_id = $data['admins_id'];
				$admin_name = tableClean($data['admins_name']);
			}

			## Tidt data to make more human friendly
			if($time_expire != '-1')
				$duration_read = time_duration($duration*60); // all penalty durations are stored in minutes, so multiple by 60 in order to get seconds
			else
				$duration_read = '';

			$time_expire_read = timeExpirePen($time_expire);
			$time_add_read = date($tformat, $time_add);
			$reason_read = removeColorCode($reason);
			
			if($type_admin) // admin cell only needed for admin type
				$admin = '<td><strong>'. clientLink($admin_name, $admin_id) .'</strong></td>';
			else
				$admin = NULL;

			## Row color
			$alter = alter();
				
			$client = clientLink($client_name, $client_id);

			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td><strong>$client</strong></td>
				<td>$type</td>
				<td>$time_add_read</td>
				<td>$duration_read</td>
				<td>$time_expire_read</td>
				<td>$reason_read
					<br /><em>$pen_data</em>
				</td>
				$admin
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

<?php 
	endif; // db error

	require 'inc/footer.php'; 
?>