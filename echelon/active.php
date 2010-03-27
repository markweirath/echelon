<?php
$page = "active";
$page_title = "Inactive Admins";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_edit";
$order = "ASC";

//$limit_rows = 75; // limit_rows can be set by the DB settings // uncomment this line to manually overide the number of table rows per page

$time = time();
$lenght = 7; // default lenght in days that the admin must be in active to show on this list


## Sorts requests vars ##
if($_GET['ob']) {
	$orderby = addslashes($_GET['ob']);
}

if($_GET['o']) {
	$order = addslashes($_GET['o']);
}

// allowed things to sort by
$allowed_orderby = array('id', 'name', 'connections', 'group_bits', 'time_edit');
if(!in_array($orderby, $allowed_orderby)) { // Check if the sent varible is in the allowed array 
	$orderby = 'time_edit'; // if not just set to default id
}

## Page Vars ##
if ($_GET['p']) {
  $page_no = addslashes($_GET['p']);
}

$start_row = $page_no * $limit_rows;


###########################
######### QUERIES #########

$query = sprintf("SELECT c.id, c.name, c.connections, c.time_edit, g.name as level
	FROM clients c LEFT JOIN groups g ON c.group_bits = g.id
	WHERE c.group_bits >= 8
	AND  c.group_bits <=64 AND(%d - c.time_edit > %d*60*60*24 )", $time, $lenght);

$query .= sprintf("ORDER BY %s ", $orderby);

## Append this section to all queries since it is the same for all ##
if($order == "desc") {
	$query .= " DESC"; // set to desc 
} else {
	$query .= " ASC"; // default to ASC if nothing adds up
}

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Prepare and run Query ##
$stmt = $db->mysql->prepare($query_limit) or die('Database Error: '.$db->mysql->error);
$stmt->execute(); // run query
$stmt->store_result(); // store results (needed to count num_rows)
$num_rows = $stmt->num_rows; // finds the number fo rows retrieved from the database
$stmt->bind_result($id, $name, $connections, $time_edit, $level); // store results

while($stmt->fetch()) : // get results and put results in an array
	$clients_data[] = array(
		'id' => $id,
		'name' => $name,
		'connect' => $connections,
		'time_edit' => $time_edit,
		'level' => $level
	);
endwhile;

$stmt->free_result(); // free the data in memory from store_result
$stmt->close(); // closes the prepared statement

## Some pagination setup is in the header.php with a if pagination required statement

## Require Header ##	
require 'inc/header.php';
?>

<table summary="A list of <?php echo limit_rows; ?> players who have connected to the server at one time or another.">
	<caption>Inactive Admins<small>There are <strong><?php echo $total_rows; ?></strong> admins who have not been seen by B3 for <strong><?php echo $lenght; ?></strong> days.</small></caption>
	<thead>
		<tr>
			<th>Name
				<?php linkSortClients('name', 'Name', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Client-id
				<?php linkSortClients('id', 'Client-id', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Level
				<?php linkSortClients('group_bits', 'Level', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Connections
				<?php linkSortClients('connections', 'Connections', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Last Seen
				<?php linkSortClients('time_edit', 'Last Seen', $is_search, $search_type, $search_string); ?>
			</th>
			<th>
				Duration
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="6">Click client name to see details</th>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$rowcolor = 0;

	 if($num_rows > 0) { // query contains stuff
	 
		foreach($clients_data as $clients): // get data from query and loop
			$cid = $clients['id'];
			$name = $clients['name'];
			$level = $clients['level'];
			$connections = $clients['connect'];
			$time_edit = $clients['time_edit'];
			
			## Change to human readable		
			$time_diff = time_duration($time - $time_edit, 'yMwd');		
			$time_edit = date($tformat, $time_edit); // this must be after the time_diff
			
			## Row color
			$rowcolor = 1 - $rowcolor;	
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td><strong><a href="clientdetails.php?id=$cid">$name</a></strong></td>
				<td>@$cid</td>
				<td>$level</td>
				<td>$connections</td>
				<td><em>$time_edit</em></td>
				<td><em>$time_diff</em></td>
			</tr>
EOD;

		echo $data;
		endforeach;
	} else {
		$no_data = true;
		echo '<tr class="odd"><td colspan="6">There are no admins that have been in active for more than '. $lenght . ' days.</td></tr>';
	} // end if query contains
	?>
	</tbody>
</table>

<?php require 'inc/footer.php'; ?>