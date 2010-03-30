<?php
$page = "admins";
$page_title = "Admin Listing";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "group_bits";
$order = "DESC"; // either ASC or DESC

//$limit_rows = 75; // limit_rows can be set by the DB settings // uncomment this line to manually overide the number of table rows per page

## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o'])
	$order = addslashes($_GET['o']);

// allowed things to sort by
$allowed_orderby = array('id', 'name', 'connections', 'group_bits', 'time_edit');
// Check if the sent varible is in the allowed array 
if(!in_array($orderby, $allowed_orderby))
	$orderby = 'time_edit'; // if not just set to default id


## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;


###########################
######### QUERIES #########

$query = sprintf("SELECT c.id, c.name, c.connections, c.time_edit, g.name as level
	FROM clients c LEFT JOIN groups g ON c.group_bits = g.id
	WHERE c.group_bits >= 8 ORDER BY %s", $orderby);

## Append this section to all queries since it is the same for all ##
$order = strtoupper($order); // force uppercase to stop inconsistentcies
if($order == "DESC")
	$query .= " DESC"; // set to desc 
else
	$query .= " ASC"; // default to ASC if nothing adds up

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Prepare and run Query ##
$stmt = $db->mysql->prepare($query_limit) or die('Database Error: '.$db->mysql->error);
$stmt->execute(); // run query
$stmt->store_result(); // store results (needed to count num_rows)
$num_rows = $stmt->num_rows; // finds the number fo rows retrieved from the database
$stmt->bind_result($id, $name, $connections, $time_edit, $level); // store results

if($num_rows > 0) :
	while($stmt->fetch()) : // get results and put results in an array
		$clients_data[] = array(
			'id' => $id,
			'name' => $name,
			'connect' => $connections,
			'time_edit' => $time_edit,
			'level' => $level,
		);
	endwhile;
endif;

$stmt->free_result(); // free the data in memory from store_result
$stmt->close(); // closes the prepared statement

## Require Header ##	
require 'inc/header.php';
?>

<table summary="A list of all registered admins">
	<caption>Admin Listing<small>A list of all registered admins</small></caption>
	<thead>
		<tr>
			<th>Name
				<?php linkSort('name', 'Name'); ?>
			</th>
			<th>Level
				<?php linkSort('group_bits', 'Level'); ?>
			</th>
			<th>Client-id
				<?php linkSort('id', 'Client-id'); ?>
			</th>
			<th>Connections
				<?php linkSort('connections', 'Connections'); ?>
			</th>
			<th>Last Seen
				<?php linkSort('time_edit', 'Last Seen'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="5"></th>
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
			$time_edit_read = date($tformat, $time_edit); // this must be after the time_diff
			
			## row color
			$rowcolor = 1 - $rowcolor;	
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td><strong><a href="clientdetails.php?id=$cid" title="View everything B3 knows about $name">$name</a></strong></td>
				<td>$level</td>
				<td>@$cid</td>
				<td>$connections</td>
				<td><em>$time_edit_read</em></td>
			</tr>
EOD;

			echo $data;
		endforeach;
		
		$no_data = false;
	} else {
		$no_data = true;
		echo '<tr class="odd"><td colspan="5">There are no registered admins</td></tr>';
	} // end if query contains
	?>
	</tbody>
</table>

<?php require 'inc/footer.php'; ?>