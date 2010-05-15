<?php
/**
 * Originially Author: Anubis
 * Adapted to 2.0: Eire.32
 */
$page = "ctime";
$page_title = "Client Activity";
$auth_name = 'ctime';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
require 'inc.php';

##########################
######## Varibles ########

if($_GET['d'])
	$duration = addslashes($_GET['d']);
else
	$duration = false;
	
if($_GET['f'])
	$filter = addslashes($_GET['f']);

## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;

###########################
######### QUERIES #########

$query = "SELECT c.name, g.name as level, c.connections, c.id, sum(gone - came) as czas
		FROM ctime ct, clients c, groups g
		WHERE ct.guid = c.guid AND c.group_bits = g.id ";
							
if($filter == "ad")
   $query .= "AND c.group_bits >= 16 ";
   
if($duration) {
    if($duration == "fn") {
        $query .= "AND ct.came > UNIX_TIMESTAMP()-1209600";
		$duration = "fn";
		
    } elseif($duration == "w") {
        $query .= "AND ct.came > UNIX_TIMESTAMP()-604800";
		$duration = "w";
		
    } elseif($duration == "d") {
        $query .= "AND ct.came > UNIX_TIMESTAMP()-86400";
		$duration = "d";
    }
} else {
    $query .= "AND ct.came > UNIX_TIMESTAMP()-2419200"; // default to one month if nothing else works
	$duration = "m";
}

$query .= " GROUP BY ct.guid ORDER BY czas DESC";

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Require Header ##	
require 'inc/header.php';

if(!$db->error) :
?>

<table summary="A list of <?php echo limit_rows; ?> players who spent the most time on the servers">
	<caption>Activity Time<small>A list of</small>
		<form action="ctime.php" method="get" id="ctime-f-form" class="sm-f-select">
			<select name="f" onchange="this.form.submit()">
				<option value="ad"<?php if($filter == 'ad') echo ' selected="selected"'; ?>>Admins</option>
				<option value="all"<?php if($filter != 'ad') echo ' selected="selected"'; ?>>All Clients</option>
			</select>
		</form>
		<small class="n">who spent the most time on the server(s) over the past</small>
		<form action="ctime.php" method="get" id="ctime-d-form" class="sm-f-select">
			<select name="d" onchange="this.form.submit()">
				<option value="m"<?php if($duration == 'm') echo ' selected="selected"'; ?>>Month</option>
				<option value="fn"<?php if($duration == 'fn') echo ' selected="selected"'; ?>>Forthnight</option>
				<option value="w"<?php if($duration == 'w') echo ' selected="selected"'; ?>>Week</option>
				<option value="d"<?php if($duration == 'd') echo ' selected="selected"'; ?>>Day</option>
			</select>
		</form>
		</caption>
	<thead>
		<tr>
			<th>no.</th>
			<th>Name</th>
			<th>Play Duration</th>
			<th>Level</th>
			<th>Connections</th>
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
	
		$i = $start_row+1; // counter is the start row
	 
		foreach($data_set as $clients): // get data from query and loop
			$cid = $clients['id'];
			$name = $clients['name'];
			$level = $clients['level'];
			$connections = $clients['connections'];
			$czas = $clients['czas'];
			
			## Change to human readable		
			$czas = time_duration($czas, 'yMwdhm');
			
			## Row color
			$rowcolor = 1 - $rowcolor;	
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
				
			$client = clientLink($name, $cid);
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td>$i</td>
				<td>$czas</td>
				<td><strong>$client</strong></td>
				<td>$level</td>
				<td>$connections</td>
			</tr>
EOD;

			echo $data;
			$i++;
			
		endforeach;
		
		$no_data = false;
	} else {
		$no_data = true;
		echo '<tr class="odd"><td colspan="6">';
		if($filter == 'ad')
			echo 'There have been no admins active in a while!</td></tr>';
		else
			echo 'There have been no clients active in a while!</td></tr>';
	} // end if query contains information
	?>
	</tbody>
</table>

<?php 
	endif; // db error

	require 'inc/footer.php'; 
?>