<?php
$page = "notices";
$page_title = "Notices";
$auth_name = 'penalties';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_add";
$order = "desc"; // pick either asc or desc


## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o']) 
	$order = addslashes($_GET['o']);


// allowed things to sort by
$allowed_orderby = array('time_add');
if(!in_array($orderby, $allowed_orderby)) // Check if the sent varible is in the allowed array 
	$orderby = 'time_add'; // if not just set to default id
	
## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;


###########################
######### QUERIES #########

$query = "SELECT p.id, p.type, p.client_id, p.time_add, p.reason,
		COALESCE(c1.id, '1') as admin_id, COALESCE(c1.name, 'B3') as admin_name, c2.name
		FROM penalties p LEFT JOIN clients c1 ON c1.id = p.admin_id LEFT JOIN clients c2 ON c2.id = p.client_id
		WHERE p.type = 'Notice'";

$query .= sprintf("ORDER BY %s ", $orderby);

## Append this section to all queries since it is the same for all ##
if($order == "desc")
	$query .= " DESC"; // set to desc 
else
	$query .= " ASC"; // default to ASC if nothing adds up

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Prepare and run Query ##
$stmt = $db->mysql->prepare($query_limit) or die('Database Error: '.$db->mysql->error);
$stmt->execute(); // run query
$stmt->store_result(); // store results (needed to count num_rows)
$num_rows = $stmt->num_rows; // finds the number fo rows retrieved from the database
$stmt->bind_result($id, $type, $client_id, $time_add, $reason, $admin_id, $admin_name, $client_name); // store results

if($num_rows > 0) :
	while($stmt->fetch()) : // get results and put results in an array
		$notices_data[] = array(
			'id' => $id,
			'type' => $type,
			'client_id' => $client_id,
			'time_add' => $time_add,
			'reason' => $reason,
			'admin_id' => $admin_id,
			'admin_name' => $admin_name,
			'client_name' => $client_name
		);
	endwhile;
endif;

$stmt->free_result(); // free the data in memory from store_result
$stmt->close(); // closes the prepared statement

## Require Header ##	
require 'inc/header.php';
?>

<table summary="A list of <?php echo limit_rows; ?> notices made by admins in the server regarding a certain player">
	<caption>Notices<small>There are a total of <strong><?php echo $total_rows; ?></strong> notices, made by admins in the server(s)</small></caption>
	<thead>
		<tr>
			<th>Name</th>
			<th>Client-id</th>
			<th>Time Added
				<?php linkSort('time_add', 'time added'); ?>
			</th>
			<th>Comment</th>
			<th>Admin</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="5">Click client name to see details</th>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$rowcolor = 0;

	if($num_rows > 0) { // query contains stuff
	 
		foreach($notices_data as $notice): // get data from query and loop
			$cname = tableClean($notice['client_name']);
			$cid = $notice['client_id'];
			$aname = tableClean($notice['admin_name']);
			$aid = $notice['admin_id'];
			$reason = tableClean($notice['reason']);
			$time_add = $notice['time_add'];
			
			## Change to human readable			
			$time_add = date($tformat, $time_add);
			
			## Row color
			$rowcolor = 1 - $rowcolor;	
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td><strong><a href="clientdetails.php?id=$cid">$cname</a></strong></td>
				<td>@$cid</td>
				<td><em>$time_add</em></td>
				<td>$reason</td>
				<td><strong><a href="clientdetails.php?id=$aid">$aname</a></strong></td>
			</tr>
EOD;

			echo $data;
		endforeach;
		
		$no_data = false;
	} else {
		$no_data = true;
		echo '<tr class="odd"><td colspan="5">There are no notices in the database.</td></tr>';
	}
	?>
	</tbody>
</table>

<?php require 'inc/footer.php'; ?>