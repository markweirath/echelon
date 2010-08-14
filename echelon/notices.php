<?php
$page = "notices";
$page_title = "Notices";
$auth_name = 'penalties';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_add";
$order = "DESC"; // pick either asc or desc


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
		COALESCE(c1.id, '1') as admin_id, COALESCE(c1.name, 'B3') as admin_name, c2.name as client_name
		FROM penalties p LEFT JOIN clients c1 ON c1.id = p.admin_id LEFT JOIN clients c2 ON c2.id = p.client_id
		WHERE p.type = 'Notice'";

$query .= "ORDER BY $orderby";

## Append this section to all queries since it is the same for all ##
if($order == "DESC")
	$query .= " DESC"; // set to desc 
else
	$query .= " ASC"; // default to ASC if nothing adds up

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section


## Require Header ##	
require 'inc/header.php';

if(!$db->error) :
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
	if($num_rows > 0) :
	 
		foreach($data_set as $notice): // get data from query and loop
			$cname = tableClean($notice['client_name']);
			$cid = $notice['client_id'];
			$aname = tableClean($notice['admin_name']);
			$aid = $notice['admin_id'];
			$reason = tableClean($notice['reason']);
			$time_add = $notice['time_add'];
			
			## Change to human readable	time
			$time_add = date($tformat, $time_add);
			
			## Row color
			$alter = alter();
				
			$client = clientLink($cname, $cid);
			$admin = clientLink($aname, $aid);
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td><strong>$client</strong></td>
				<td>@$cid</td>
				<td><em>$time_add</em></td>
				<td>$reason</td>
				<td><strong>$admin</strong></td>
			</tr>
EOD;

			echo $data;
		endforeach;
		
		$no_data = false;
	else:
		$no_data = true;
		echo '<tr class="odd"><td colspan="5">There are no notices in the database.</td></tr>';
	endif; // no records
	?>
	</tbody>
</table>

<?php 
	endif; // db error

	require 'inc/footer.php'; 
?>