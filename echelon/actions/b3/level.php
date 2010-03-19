<?php
## NOTE: this page deals with both the request from the change lient user level as well the requests to change a user's mask level ##
if($_POST['level-sub']) // what kind of request is it
	$is_mask = false;
else
	$is_mask = true;

if(!$is_mask) // check which auth level is needed
	$auth_name = 'edit_client_level';
else
	$auth_name = 'edit_mask';
	
$b3_conn = true; // this page needs to connect to the B3 database
require '../../inc.php';

if($_POST['level-sub'] || $_POST['mlevel-sub']) : // if the form is submitted

	## check that the sent form token is corret
	if(!$is_mask) {
		if(verifyFormToken('level', $tokens) == false) // verify token
			ifTokenBad('Change client level');
	} else {
		if(verifyFormToken('mask', $tokens) == false) // verify token
			ifTokenBad('Change client mask level');
	}
	
	## Set and clean vars ##
	$level = cleanvar($_POST['level']);
	$client_id = cleanvar($_POST['cid']);
	
	## Check if the client_id is numeric ##
	if(!is_numeric($client_id))
		sendBack('Invalid data sent, greeting not changed');
	
	## Check if the group_bits provided match a known group (Knwon groups is a list of groups pulled from the DB -- this allow more control for custom groups)
	$b3_groups = $db->getB3GroupsLevel();
	if(!in_array($level, $b3_groups))
		sendBack('That group does not exist, please submit a real group');

	## Query Section ##
	if(!$is_mask)
		$query = "UPDATE clients SET group_bits = ? WHERE id = ? LIMIT 1";
	else
		$query = "UPDATE clients SET mask_level = ? WHERE id = ? LIMIT 1";
	$stmt = $db->mysql->prepare($query) or die('Database Error: '.$db->mysql->error);
	$stmt->bind_param('ii', $level, $client_id);
	$stmt->execute();
	if($stmt->affected_rows)
		sendGood('User level has been changed');
	else
		sendBack('User level was not changed');
	
	$stmt->close(); // close connection

else :

	set_error('Please do not call that page directly, thank you.');
	send('../../index.php');

endif;