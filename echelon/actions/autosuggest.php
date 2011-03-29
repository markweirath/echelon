<?php
$auth_name = 'clients';
$b3_conn = true;
require '../inc.php';

## If no string die
if(!isset($_GET['s']))
	die('There should be no direct access to this script!');

## Continue
$string = cleanvar($_GET['s']);

if(strlen($string) > 0) {

	$query = "SELECT name FROM clients WHERE UPPER(name) LIKE '". $string ."%' ORDER BY name LIMIT 10";
	$stmt = $db->mysql->prepare($query);
	$stmt->execute();
	$stmt->store_result();
	
	if($stmt->num_rows) {
	
		$stmt->bind_result($name);
	
		echo '<ol>';
			while ($stmt->fetch()) :
				echo '<li onClick="fill(\''.tableClean($name).'\');">'.$name.'</li>';
			endwhile;
		echo '</ol>';
		
	} else { // else try more flexible query
		
		$query_2 = "SELECT name FROM clients WHERE SOUNDEX(name) = SOUNDEX('%%". $string ."%%') ORDER BY name LIMIT 10";
		$stmt = $db->mysql->prepare($query_2);
		$stmt->execute();
		$stmt->store_result();
				
		if($stmt->num_rows) { // if something return data
		
			$stmt->bind_result($name);
		
			echo '<ol>';
			while ($stmt->fetch()) :
				echo '<li onClick="fill(\''.tableClean($name).'\');">'.$name.'</li>';
			endwhile;
			echo '</ol>';
			
		} else { // if nothing try even more flexible query
		
			$query_3 = "SELECT name FROM clients WHERE SUBSTRING(SOUNDEX(name),2) = SUBSTRING(SOUNDEX('%%%". $string ."%%%'),2) ORDER BY name LIMIT 10";
			$stmt = $db->mysql->prepare($query_3);
			$stmt->execute();
			$stmt->store_result();
			
			if($stmt->num_rows) { // if something return data
			
				$stmt->bind_result($name);
			
				echo '<ol>';
				while ($result = $query_3->fetch_object()) :
					echo '<li onClick="fill(\''.tableClean($name).'\');">'.$name.'</li>';
				endwhile;
				echo '</ol>';
				
			} else { // if nothing try even more flexible query
				
				echo '<ol><li>There are no matches to your search</li></ol>';
			
			} // show error if nothing gotten overal
		
		} // end if two returned nothing!
	
	} // end if query one returned nothing !

}

?>