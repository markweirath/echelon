<?php
$page = "regular";
$page_title = "Regular Pubbers";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_edit";
$order = "ASC"; // either ASC or DESC

//$limit_rows = 75; // limit_rows can be set by the DB settings // uncomment this line to manually overide the number of table rows per page

$time = 1250237292;
$lenght = 7; // default lenght (in days) that the client must have connected to the server(s) on in order to be on the list
$connections_limit = 50; // default number of connections that the player must have (in total) to be on the list

$clan_tags = array("=(e)=","=(eG)=","=(eGO)=","=(eGO)=","{ROC}","*{PotW}*","{DP}","=KND=","{KGB}");

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
######### QUERY ###########

$query = sprintf("SELECT c.id, c.name, c.connections, c.time_edit, g.name as level
	FROM clients c LEFT JOIN groups g ON c.group_bits = g.id
	WHERE c.group_bits <= 2 AND(%d - c.time_edit < %d*60*60*24 ) 
	AND connections > %d AND c.id != 1 ", $time, $lenght, $connections_limit);
	
foreach ($clan_tags as $tag) {
	// run through array appending clantag section for each value in the array
	$query .= "AND c.name NOT LIKE '%".$tag."%' ";
}

$query .= sprintf("ORDER BY %s", $orderby);

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

<div style="float: none; margin: 15px; width: 90%; padding: 5px;" class="error-msg error">
	<p><strong>Faked Time:</strong> <?php echo date($tformat, $time); ?></p>
</div>

<table summary="A list of players who are regular server go'ers o your servers.">
	<caption>Regulars<small>A list of players who are regular server go'ers o your servers. Must have more than <strong><?php echo $connections_limit; ?></strong> connections and been seen in the last <strong><?php echo $lenght; ?></strong> days.</small></caption>
	<thead>
		<tr>
			<th>Name
				<?php linkSort('name', 'Name'); ?>
			</th>
			<th>Connections
				<?php linkSort('connections', 'Connections'); ?>
			</th>
			<th>Client-id
				<?php linkSort('id', 'Client-id'); ?>
			</th>
			<th>Level
				<?php linkSort('group_bits', 'Level'); ?>
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

	 if($num_rows > 0) { // query contains stuff so spit it out
	 
		foreach($clients_data as $clients): // get data from query and loop
			$cid = $clients['id'];
			$name = $clients['name'];
			$level = $clients['level'];
			$connections = $clients['connect'];
			$time_edit = $clients['time_edit'];
			
			## Change to human readable ##
			$time_edit_read = date($tformat, $time_edit); // this must be after the time_diff
			
			## row color ##
			$rowcolor = 1 - $rowcolor;	
			if($rowcolor == 0)
				$odd_even = "odd";
			else 
				$odd_even = "even";
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$odd_even">
				<td><strong><a href="clientdetails.php?id=$cid" title="View everything B3 knows about $name">$name</a></strong></td>
				<td>$connections</td>
				<td>@$cid</td>
				<td>$level</td>
				<td><em>$time_edit_read</em></td>
			</tr>
EOD;

			echo $data;
		endforeach;
		$no_data = false;
	} else {
		$no_data = true;
		echo '<tr class="odd"><td colspan="5">There are no people who have had a total mininium of '.$connections_limit.' and been seen in the last '.$lenght.' days.</td></tr>';
	} // end if query contains information
	?>
	</tbody>
</table>

<?php require 'inc/footer.php'; ?>