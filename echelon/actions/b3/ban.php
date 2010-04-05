<?php
$auth_name = 'ban';
$b3_conn = true; // this page needs to connect to the B3 database
require '../../inc.php';

if(!$_POST['ban-sub']) { // if the form not is submitted
	set_error('Please do not call that page directly, thank you.');
	send('../../index.php');
}

## check that the sent form token is corret
if(verifyFormToken('ban', $tokens) == false) // verify token
	ifTokenBad('Add ban');

## Type of ban and get and set vars ##
$pb_ban = cleanvar($_POST['pb']);
if($pb_ban == 'on') {
	$is_pb_ban = true;
} else {
	$is_pb_ban = false;
	$duration_form = cleanvar($_POST['duration']);
	$time = cleanvar($_POST['time']);
	emptyInput($time, 'time frame');
	emptyInput($duration_form, 'penlaty duration');
}

$reason = cleanvar($_POST['reason']);
$client_id = cleanvar($_POST['cid']);
$pbid = cleanvar($_POST['c-pbid']);
$c_name = cleanvar($_POST['c-name']);
$c_ip = cleanvar($_POST['c-ip']);

// check for empty reason
emptyInput($reason, 'ban reason');

## Check sent client_id is a number ##
if(!is_numeric($client_id))
	sendBack('Invalid data sent, ban not added');
	
## Sort out some ban information
if($is_pb_ban) { // if the ban is perma ban
	$type = 'Ban';
	$time_expire = '-1';
	$duration = 0;
} else {
	$type = 'TempBan';
	
	// NOTE: the duration in the DB is done in MINUTES and the time_expire is written in unix timestamp (in seconds)
	if($time == 'h') { // if time is in hours
		$duration = $duration_form*60;
	} elseif($time == 'd') { // time in days
		$duration = $duration_form*60*24;
	} elseif($time == 'w') { // time in weeks
		$duration = $duration_form*60*24*7;
	} elseif($time == 'mn') { // time in months (lets just say 30 days to a month)
		$duration = $duration_form*60*24*30;
	} elseif($time == 'y') { // time in years
		$duration = $duration_form*60*24*365;
	} else { // default time to mintues
		$duration = $duration_form;
	}
	
	$duration_secs = $duration*60; // find the duration in seconds
	
	$time_expire = time() + $duration_secs; // time_expire is current time plus the duration in seconds

} // end if pb/tempban var setup

$data = ''; // data is NULL

$reason .= ' (Echelon: '.$_SESSION['name']. ' ['. $_SESSION['user_id'] .'])'; // since this ban goes down as a B3 ban, tag on some user information (display name and echelon user id)

## Add Ban to the penalty table ##
$result = $db->penClient($type, $client_id, $duration, $reason, $data, $time_expire);
	
## Make PB ban to server if Pb is enabled ##
if($is_pb_ban == true) :
	$i = 1;
	while($i <= $game_num_srvs) :

		if($config['game'.$game]['servers'][$i]['pb_active'] == '1') {
			// get the rcon information from the massive config array
			$rcon_pass = $config['game'.$game]['servers'][$i]['rcon_pass'];
			$rcon_ip = $config['game'.$game]['servers'][$i]['rcon_ip'];
			$rcon_port = $config['game'.$game]['servers'][$i]['rcon_port'];
			$c_ip = trim($c_ip);
		
			// PB_SV_BanGuid [guid] [player_name] [IP_Address] [reason]
			$command = "pb_sv_banguid " . $pbid . " " . $c_name . " " . $c_ip . " " . $reason;
			rcon($rcon_ip, $rcon_port, $rcon_pass, $command); // send the ban command
			sleep(1); // sleep for 1 sec in ordere to the give server some time
			$command_upd = "pb_sv_updbanfile"; // we need to update the ban files
			rcon($rcon_ip, $rcon_port, $rcon_pass, $command_upd); // send the ban file update command
		}

		$i++;
	endwhile;
endif; // end if a $is_pb_ban == true

if($result)
	sendGood('Ban added to banlist and to the DB');
else
	sendBack('Something went wrong the ban was not added');

exit;