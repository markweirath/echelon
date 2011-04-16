<?php
$auth_name = 'edit_ban';
$b3_conn = true; // this page needs to connect to the B3 database
require '../../inc.php';

if(!$_POST['eb-sub']) { // if the form not is submitted
	set_error('Please do not call that page directly, thank you.');
	send('../../index.php');
}

## check that the sent form token is corret
if(verifyFormToken('editban', $tokens) == false) // verify token
	ifTokenBad('Edit ban');
	
$ban_id = cleanvar($_POST['banid']);
$pbid = cleanvar($_POST['pbid']);
$pb_ban = cleanvar($_POST['pb']);
$reason = cleanvar($_POST['reason']);
$cid = cleanvar($_POST['cid']);
if($pb_ban == 'on') {
	$is_pb_ban = true;
	$type = 'Ban';
	$duration = 0;
	$time_expire = '-1';
} else {
	$is_pb_ban = false;
	$type = 'TempBan';
	$duration_form = cleanvar($_POST['duration']);
	$time = cleanvar($_POST['time']);
	emptyInput($time, 'time frame');
	emptyInput($duration_form, 'penalty duration');
	
	// NOTE: the duration in the DB is done in MINUTES and the time_expire is written in unix timestamp (in seconds)
	$duration = penDuration($time, $duration_form);
	
	$duration_secs = $duration*60; // find the duration in seconds
	
	$time_expire = time() + $duration_secs; // time_expire is current time plus the duration in seconds
}

// check for empty reason
emptyInput($reason, 'ban reason');

if( !isID($ban_id) || !isID($cid) )
	sendBack('Some of the information sent by you is invalid, the ban was not edited');

## Query Section ##
$query = "UPDATE penalties SET type = ?, duration = ?, time_edit = UNIX_TIMESTAMP(), time_expire = ?, reason = ? WHERE id = ? LIMIT 1";
$stmt = $db->mysql->prepare($query) or die('DB Error');
$stmt->bind_param('siisi', $type, $duration, $time_expire, $reason, $ban_id);
$stmt->execute();

if($stmt->affected_rows > 0)
	$results = true;
else
	sendBack('Something went wrong');

## If a permaban send unban rcon command (the ban will still be enforced then by the B3 DB ##
if($type == 'Ban') :
	
	## Loop thro server for this game and send unban command and update ban file
	$i = 1;
	while($i <= $game_num_srvs) :

		if($config['games'][$game]['servers'][$i]['pb_active'] == '1') {
			// get the rcon information from the massive config array
			$rcon_pass = $config['game']['servers'][$i]['rcon_pass'];
			$rcon_ip = $config['game']['servers'][$i]['rcon_ip'];
			$rcon_port = $config['game']['servers'][$i]['rcon_port'];
		
			// PB_SV_BanGuid [guid] [player_name] [IP_Address] [reason]
			$command = "pb_sv_unbanguid " . $pbid;
			rcon($rcon_ip, $rcon_port, $rcon_pass, $command); // send the ban command
			sleep(1); // sleep for 1 sec in ordere to the give server some time
			$command_upd = "pb_sv_updbanfile"; // we need to update the ban files
			rcon($rcon_ip, $rcon_port, $rcon_pass, $command_upd); // send the ban file update command
		}

		$i++;
	endwhile;

endif;

// set comment for the edit ban action
if($duration == 0)
	$comment = 'Changed ban #' . $ban_id . ' to a permanent ban';
else {
	$dur_name = array(
			'm' => 'minute',
			'h' => 'hour',
			'd' => 'day',
			'w' => 'week',
			'mn' => 'month',
			'y' => 'year'
		);
		$comment = 'Changed ban #' . $ban_id . ' to a '. $duration_form . ' ' . $dur_name[$time] . ' temp ban';
}

## Query ##
$result = $dbl->addEchLog('Edit Ban', $comment, $cid, $mem->id, $game);

if($results)
	sendGood('Ban edited');
else
	sendBack('NO!');

exit;