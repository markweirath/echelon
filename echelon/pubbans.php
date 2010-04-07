<?php
$auth_user_here = false;
$page = 'pubbans';
$page_title = 'Public Ban List';
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
$allowed_orderby = array('name', 'time_add', 'time_expire');
if(!in_array($orderby, $allowed_orderby)) // Check if the sent varible is in the allowed array 
	$orderby = 'time_add'; // if not just set to default id

## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;


###########################
######### QUERIES #########
$query = "SELECT c.name, p.id, p.type, p.time_add, p.time_expire, p.reason, p.duration FROM penalties p LEFT JOIN clients c ON p.client_id = c.id WHERE p.inactive = 0 AND p.type != 'Warning' AND p.type != 'Notice' AND (p.time_expire = -1 OR p.time_expire > UNIX_TIMESTAMP(NOW()))";

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

if($num_rows > 0) :
	$stmt->bind_result($client_name, $ban_id, $type, $time_add, $time_expire, $reason, $duration);

	while($stmt->fetch()) : // get results and put results in an array
		$pens_data[] = array(
			'client_name' => $client_name,
			'ban_id' => $ban_id,
			'type' => $type,
			'time_add' => $time_add,
			'time_expire' => $time_expire,
			'reason' => $reason,
			'duration' => $duration
		);
	endwhile;
endif;

$stmt->free_result(); // free the data in memory from store_result()
$stmt->close(); // closes the prepared statement

## Require Header ##	
require 'inc/header.php';
?>

	<table summary="A list of <?php echo $limit_rows; ?> active tempbans/bans">
		<caption>Public Ban List<small>There are <strong><?php echo $total_rows; ?></strong> active bans/tempbans for 
				<form action="pubbans.php" method="get" id="pubbans-form">
					<select name="game" onchange="this.form.submit()">
						<?php
							$games_list = $dbl->getGamesList();
							foreach($games_list as $item) :
								$loop_game_id = substr($item['category'], -1); // the id of the game is at the end of the string (eg. 'game1') so substr gets the last character (ie. the id)
								$loop_game_name = $item['value'];
								if($loop_game_id == $game)
									echo '<option value="'.$loop_game_id.'" selected="selected">'.$loop_game_name.'</option>';
								else
									echo '<option value="'.$loop_game_id.'">'.$loop_game_name.'</option>';
							endforeach;
						?>
					</select>
				</form>
			</small>
		</caption>
	<thead>
		<tr>
			<th>Client
				<?php linkSort('name', 'Name'); ?>
			</th>
			<th>Ban-id</th>
			<th>Type</th>
			<th>Added
				<?php linkSort('time_add', 'time the penalty was added'); ?>
			</th>
			<th>Duration</th>
			<th>Expires
				<?php linkSort('time_expire', 'time the penalty expires'); ?>
			</th>
			<th>Reason</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="7"></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$rowcolor = 0;

	 if($num_rows > 0) { // query contains stuff

		foreach($pens_data as $pen): // get data from query and loop
			$ban_id = $pen['ban_id'];
			$type = $pen['type'];
			$time_add = $pen['time_add'];
			$time_expire = $pen['time_expire'];
			$reason = tableClean($pen['reason']);
			$client_name = tableClean($pen['client_name']);
			$duration = $pen['duration'];

			## Tidt data to make more human friendly
			if($time_expire != '-1')
				$duration_read = time_duration($duration*60); // all penalty durations are stored in minutes, so multiple by 60 in order to get seconds
			else
				$duration_read = '';

			$time_expire_read = timeExpirePen($time_expire, $tformat);
			$time_add_read = date($tformat, $time_add);
			$reason_read = removeColorCode($reason);

			## Row color
			$rowcolor = 1 - $rowcolor;	
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";

			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td><strong>$client_name</strong></td>
				<td>$ban_id</td>
				<td>$type</td>
				<td>$time_add_read</td>
				<td>$duration_read</td>
				<td>$time_expire_read</td>
				<td>$reason_read</td>
			</tr>
EOD;

			echo $data;
		endforeach;
		
		$no_data = false;
	} else {
		$no_data = true;
		echo '<tr class="odd"><td colspan="7">There no active bans in the B3 Database</td></tr>';
	} // end if query contains
	?>
	</tbody>
</table>

<?php require 'inc/footer.php'; ?>