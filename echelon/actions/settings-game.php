<?php
$auth_name = 'manage_settings';
$b3_conn = true; // needed to test the B3 DB for a successful connection
require '../inc.php';

## Check that the form was posted and that the user did not just stumble here ##
if(!$_POST['game-settings-sub']) :
	set_error('Please do not call that page directly, thank you.');
	send('../index.php');
endif;

## Find Type ##
if($_POST['type'] == 'add')
	$is_add = true;
	
elseif($_POST['type'] == 'edit')
	$is_add = false;
	
else
	sendBack('Missing Data');

## Check Token ##
if($is_add) {
	if(!verifyFormToken('addgame', $tokens)) // verify token
		ifTokenBad('Add Game');
} else {
	if(!verifyFormToken('gamesettings', $tokens)) // verify token
		ifTokenBad('Game Settings Edit');
}

## Get Vars ##
$name = cleanvar($_POST['name']);
$name_short = cleanvar($_POST['name-short']);
if($is_add)
	$game_type = cleanvar($_POST['game-type']);
// DB Vars
$db_host = cleanvar($_POST['db-host']);
$db_user = cleanvar($_POST['db-user']);
$db_pw_cng = cleanvar($_POST['cng-pw']);
$db_pw = cleanvar($_POST['db-pw']);
$db_name = cleanvar($_POST['db-name']);
// Verify Password
$password = cleanvar($_POST['password']);

// Whether to change DB PW or not
if($db_pw_cng == 'on')
	$change_db_pw = true;
else
	$change_db_pw = false;

## Check for empty vars ##
emptyInput($name, 'game name');
emptyInput($name_short, 'short version of game name');
emptyInput($db_user, 'DB Username');
emptyInput($db_host, 'DB Host');
emptyInput($db_name, 'DB name');

if( ($change_db_pw == true) && (!$is_add) )
	emptyInput($db_pw, 'DB password');

if(!$is_add)
	emptyInput($password, 'your current password');
	
if($is_add) :

	## Check game is supported ##
	if(!array_key_exists($game_type, $supported_games))
		sendBack('That game type does not exist, please choose a game');
endif;


## Check that the DB information supplied will make a connection to the B3 database.
$db_test = DB_B3::getInstance($db_host, $db_user, $db_pw, $db_name, true); // the last argument is hard coded because any error report needs to be the full error message, not just the failed connection line; this will only be seen by people who can add/edit settings

if($db_test->error)
	sendBack($db_test->error_msg); // send back with a failed connection message
	

## Update DB ##
if($is_add) : // add game queries
	$result = $dbl->addGame($name, $game_type, $name_short, $db_host, $db_user, $db_pw, $db_name);
	if(!$result) // if everything is okay
		sendBack('There is a problem, the game information was not saved.');
	
	$dbl->addGameCount(); // Add one to the game counter in config table	
	
else : // edit game queries
	$mem->reAuthUser($password, $dbl);
	$result = $dbl->setGameSettings($game, $name, $name_short, $db_user, $db_host, $db_name, $db_pw, $change_db_pw); // update the settings in the DB
	if(!$result)
		sendBack('Something did not update');
endif;

## Return with result message
if($is_add) {
	set_good('Game Added');
	send('../settings-games.php');
} else 
	sendGood('Your settings have been updated');
