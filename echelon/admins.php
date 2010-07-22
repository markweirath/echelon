<?php
$page = "admins";
$page_title = "Admin Listing";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
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

## Require Header ##	
require 'inc/header.php';

if(!$db->error) :
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
	
	if($num_rows > 0) : // query contains stuff
	 
		foreach($data_set as $data): // get data from query and loop
			$cid = $data['id'];
			$name = $data['name'];
			$level = $data['level'];
			$connections = $data['connections'];
			$time_edit = $data['time_edit'];
			
			## Change to human readable		
			$time_edit_read = date($tformat, $time_edit); // this must be after the time_diff
			$client_link = clientLink($name, $cid);
			
			## row color
			$alter = alter();
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td><strong>$client_link</strong></td>
				<td>$level</td>
				<td>@$cid</td>
				<td>$connections</td>
				<td><em>$time_edit_read</em></td>
			</tr>
EOD;

			echo $data;
		endforeach;
		
		$no_data = false;
	else:
		$no_data = true;
		echo '<tr class="odd"><td colspan="5">There are no registered admins</td></tr>';
	endif; // no records
	?>
	</tbody>
</table>

<?php 
	endif; // db error

	require 'inc/footer.php'; 
?>