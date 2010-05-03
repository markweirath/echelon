<?php
$auth_name = 'manage_settings';
require '../inc.php';

## Check that the form was posted and that the user did not just stumble here ##
if(!$_POST['game-settings-sub']) :
	set_error('Please do not call that page directly, thank you.');
	send('../index.php');
endif;

## Check Token ##
if(verifyFormToken('gamesettings', $tokens) == false) // verify token
	ifTokenBad('Settings Edit');

## Get Vars ##
$name = cleanvar($_POST['name']);
$name_short = cleanvar($_POST['name-short']);
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
if($change_db_pw == true)
	emptyInput($db_pw, 'DB password');
emptyInput($db_name, 'DB name');
emptyInput($password, 'your current password');

## Check that authorisation passsword is correct ##
$mem->reAuthUser($password, $dbl);
	
## Update DB ##
$result = $dbl->setGameSettings($game, $name, $name_short, $db_user, $db_host, $db_name, $db_pw, $change_db_pw); // update the settings in the DB

if($result == false)
	sendBack('Something did not update');

## Return ##
sendGood('Your settings have been updated');
