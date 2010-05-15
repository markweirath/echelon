<?php
$page = "adminkicks";
$page_title = "Admin Kicks";
$auth_name = 'penalties';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true; // this is a normal query page, so evoke query function in header.php
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
$allowed_orderby = array('target_name', 'time_add', 'admins_name');
if(!in_array($orderby, $allowed_orderby)) { // Check if the sent varible is in the allowed array 
	$orderby = 'time_add'; // if not just set to default id
}

## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;

###########################
######### QUERIES #########

$query = "SELECT p.time_add, p.reason, target.id as target_id, target.name as target_name, c.id as admin_id, c.name as admin_name FROM penalties p, clients c, clients as target WHERE admin_id != '0' AND p.type = 'Kick' AND inactive = 0 AND p.client_id = target.id AND p.admin_id = c.id";

$query .= sprintf(" ORDER BY %s ", $orderby);

## Append this section to all queries since it is the same for all ##
if($order == "desc")
	$query .= " DESC"; // set to desc 
else
	$query .= " ASC"; // default to ASC if nothing adds up

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Require Header ##	
require 'inc/header.php'; 

if(!$db->error) :
?>
<table summary="A list of <?php echo limit_rows; ?> kicks made by admins in a servers">
	<caption>Admin Kicks<small>There are <strong><?php echo $total_rows; ?></strong> kicks that have been added by admins</caption>
	<thead>
		<tr>
			<th>Client
				<?php linkSort('target_name', 'client name'); ?>
			</th>
			<th>Kicked At
				<?php linkSort('time_add', 'time the penalty was added'); ?>
			</th>
			<th>Reason</th>
			<th>
				Admin
				<?php linkSort('admins_name', 'admin name'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="4"></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$rowcolor = 0;

	 if($num_rows > 0) { // query contains stuff

		foreach($data_set as $data): // get data from query and loop
			$time_add = $data['time_add'];
			$reason = tableClean($data['reason']);
			$client_id = $data['target_id'];
			$client_name = tableClean($data['target_name']);
			$admin_id = $data['admin_id'];
			$admin_name = tableClean($data['admin_name']);

			## Tidt data to make more human friendly
			if($time_expire != '-1')
				$duration_read = time_duration($duration*60); // all penalty durations are stored in minutes, so multiple by 60 in order to get seconds
			else
				$duration_read = '';

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
				<td><strong><a href="clientdetails.php?id=$client_id" title="View more information on $client_name">$client_name</a></strong></td>
				<td>$time_add_read</td>
				<td>$reason_read</td>
				<td><strong><a href="clientdetails.php?id=$admin_id" title="View more information on $admin_name">$admin_name</a></strong></td>
			</tr>
EOD;

		echo $data;
		endforeach;
	} else {
		$no_data = true;
		echo '<tr class="odd"><td colspan="4">There are no kicks in the database</td></tr>';
	} // end if query contains
	?>
	</tbody>
</table>

<?php 
endif;

require 'inc/footer.php'; 
?>