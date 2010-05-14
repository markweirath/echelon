<?php
$page = "map";
$page_title = "Player Map";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = false; // this page requires the pagination part of the footer
require 'inc.php';

######### QUERIES #########

$time = 1250237292; // fake the time since we're using a DB backup
$length = 7; // one day

$query = sprintf("SELECT ip FROM clients WHERE (%d - time_edit < %d*60*60*24 )", $time, $length);

## Prepare and run Query ##
$stmt = $db->mysql->prepare($query) or die('Database Error: '.$db->mysql->error);
$stmt->execute(); // run query
$stmt->store_result(); // store results (needed to count num_rows)
$num_rows = $stmt->num_rows; // finds the number fo rows retrieved from the database
$stmt->bind_result($ip); // store results

if($num_rows > 0) :
	while($stmt->fetch()) : // get results and put results in an array
		$ips[] = $ip;
	endwhile;
endif;

$stmt->free_result(); // free the data in memory from store_result
$stmt->close(); // closes the prepared statement

require_once("inc/geoip.php");

$gi = geoip_open("lib/GeoIP.dat", GEOIP_STANDARD);

$countries = array();

foreach($ips as $ip) :

	$country_name = geoip_country_name_by_addr($gi, $ip);
	$count = $countries[$country_name];
	$countries[$country_name] = $count + 1;

endforeach;

geoip_close($gi);

$num_countries = count($countries);

## Create Code to be inserted into the header


$map_js = "
<script type='text/javascript' src='http://www.google.com/jsapi'></script>
<script type='text/javascript'>
	google.load('visualization', '1', {'packages': ['geomap']});
	google.setOnLoadCallback(drawMap);

	function drawMap() {
		var data = new google.visualization.DataTable();
		data.addRows(".$num_countries.");
		data.addColumn('string', 'Country');
		data.addColumn('number', 'Player Connections');";
		
		$i = 0;
		foreach($countries as $key => $value) :
			
			$map_js .= "
				data.setValue(". $i .", 0, '". $key ."');
				data.setValue(". $i .", 1, ". $value .");
				";
			
			$i++;
		endforeach;
		
$map_js .= "
		var options = {};
		options['dataMode'] = 'regions';
		options['width'] = '800px';
		options['height'] = '550px';

		var container = document.getElementById('map-box');
		var geomap = new google.visualization.GeoMap(container);
		geomap.draw(data, options);
	};
</script>
";

## Require Header ##	
require 'inc/header.php';
?>

<h3>Player Map</h3>
<div id="map-box"></div>
<p><small>Map shows unique connections of players on a world map. There were a total of <strong><?php echo $num_rows; ?></strong> unique connections in the last <strong>7 days</strong>.</small></p>
<br />

<?php require 'inc/footer.php'; ?>