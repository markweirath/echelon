<?php
$page = "regular";
$page_title = "Regular Pubbers";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_edit";
$order = "ASC"; // either ASC or DESC

//$time = 1250237292;
$time = time();
$lenght = $config['cosmos']['reg_days'];; // default length (in days) that the client must have connected to the server(s) on in order to be on the list
$connections_limit = $config['cosmos']['reg_connections']; // default number of connections that the player must have (in total) to be on the list

$clan_tags = $config['cosmos']['reg_clan_tags']; // use the clan tags stored in the DB conifg table

$clan_tags = explode(',', $clan_tags);

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
	// run through array appending clantag section for each value in the arrayi
	if($tag != null)
		$query .= "AND c.name NOT LIKE '%".$tag."%' ";
}

$query .= sprintf("ORDER BY %s", $orderby);

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

<table summary="A list of players who are regular server go'ers o your servers.">
	<caption>Regulars<small>A list of players who are regular server go'ers on your servers, excluding clan members. Must have more than <strong><?php echo $connections_limit; ?></strong> connections and been seen in the last <strong><?php echo $lenght; ?></strong> days.</small></caption>
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
	if($num_rows > 0) : // query contains stuff so spit it out
	 
		foreach($data_set as $clients): // get data from query and loop
			$cid = $clients['id'];
			$name = $clients['name'];
			$level = $clients['level'];
			$connections = $clients['connections'];
			$time_edit = $clients['time_edit'];
			
			## Change to human readable ##
			$time_edit_read = date($tformat, $time_edit); // this must be after the time_diff
			
			## row color ##
			$alter = alter();
	
			$client = clientLink($name, $cid);
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td><strong>$client</td>
				<td>$connections</td>
				<td>@$cid</td>
				<td>$level</td>
				<td><em>$time_edit_read</em></td>
			</tr>
EOD;

			echo $data;
		endforeach;
		$no_data = false;
		
	else:
		$no_data = true;
		echo '<tr class="odd"><td colspan="5">There are no people who have had a total mininium of '.$connections_limit.' connections and been seen in the last '.$lenght.' days.</td></tr>';
	endif; // no records
	?>
	</tbody>
</table>

<?php 
	endif; // db error

	require 'inc/footer.php'; 
?>