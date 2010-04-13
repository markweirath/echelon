<?php 
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'setup.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

require_once 'config.php';

$this_page = $_SERVER["PHP_SELF"];

## setup the game var ##
if($_GET['game']) {
	$game = $_GET['game'];
	setcookie("game", $game, time()*60*60*24*31, $path); // set the cookie to game value

} elseif($_POST['game']) {
	$game = addslashes($_POST['game']);
	setcookie("game", $game, time()*60*60*24*31, $path); // set the cookie to game value

} elseif($_COOKIE['game']) {
	$game = $_COOKIE['game'];
	
} else {
	$game = 1;
	setcookie("game", $game, time()*60*60*24*31, $path); // set the cookie to game value
	//die('Default Error');
}


## Setup Arrays ##
$config = array();
$config['cosmos'] = array();

## Get the config array ##
$config['cosmos'] = $dbl->getSettings('cosmos');

// find the number of games from the config array
$num_games = $config['cosmos']['num_games'];

$site_name = $config['cosmos']['name'];
$limit_rows = $config['cosmos']['limit_rows'];
$allow_ie = $config['cosmos']['allow_ie'];
$min_pw_len = $config['cosmmos']['min_pw_len'];
$https_enabled = $config['cosmos']['https'];
$key_expire = $config['cosmos']['user_key_expire']; // This var says how long it takes for a user creation key to expire
global $tformat; // make global to allow the formatting to be used inside functions
$tformat = $config['cosmos']['time_format'];
$time_zone = $config['cosmos']['time_zone'];

## Time Zone Setup ##
if($time_zone == '') {
	$time_zone == 'Europe/London';
	$no_time_zone = true;
} else {
	$no_time_zone = false;
}
date_default_timezone_set($time_zone);


// if $game is greater than num_games then game doesn't exist so send to error page with error and reset game to 1
if($game > $num_games) {
	setcookie("game", 1, time()*60*60*24*31, $path); // set the cookie to game value
	set_error('That game doesn\'t exist');
	sendError();
	exit;
	//die('Massive Error');
}

## Get the games Information ##
$games = $dbl->getGamesInfo();

## Append the games unto the the config array ##
$config['games'] = $games;

## Get and setup the servers information into the array ##
$servers = $dbl->getServers($game);

$config['games'][$game]['servers'] = array(); // create array

if($config['games'][$game]['num_srvs'] == 1) : // if the current game has only ONE server then NO loop is needed
	
	$config['games'][$game]['servers'][1] = array();
	$config['games'][$game]['servers'][1]['name'] = $server['name'];
	$config['games'][$game]['servers'][1]['ip'] = $server['ip'];
	$config['games'][$game]['servers'][1]['pb_active'] = $server['pb_active'];
	$config['games'][$game]['servers'][1]['rcon_pass'] = $server['rcon_pass'];
	$config['games'][$game]['servers'][1]['rcon_ip'] = $server['rcon_ip'];
	$config['games'][$game]['servers'][1]['rcon_port'] = $server['rcon_port'];

else: // there is more than one server so a loop is needed

	$i = 1; // restart i to 1
	foreach($servers as $server) : // loop thro the list of servers
		
		$config['games'][$game]['servers'][$i] = array();
		$config['games'][$game]['servers'][$i]['name'] = $server['name'];
		$config['games'][$game]['servers'][$i]['ip'] = $server['ip'];
		$config['games'][$game]['servers'][$i]['pb_active'] = $server['pb_active'];
		$config['games'][$game]['servers'][$i]['rcon_pass'] = $server['rcon_pass'];
		$config['games'][$game]['servers'][$i]['rcon_ip'] = $server['rcon_ip'];
		$config['games'][$game]['servers'][$i]['rcon_port'] = $server['rcon_port'];
		
		$i++; // increment counter
	endforeach;

endif;

## Get plguin Information ##
$config['games'][$game]['plugins'] = $dbl->getPlugins($game);

## Handy's ##
if($config['games'][$game]['plugins']['xlrstats']['enabled'] == 1) {
	$plugin_xlrstats_enabled = true;
}

## Setup some handy easy to access information for the CURRENT GAME only ##

$game_name = $config['games'][$game]['name'];
$game_name_short = $config['games'][$game]['short_name'];
$game_num_srvs = $config['games'][$game]['num_srvs'];
$game_db_host = $config['games'][$game]['db_host'];
$game_db_user = $config['games'][$game]['db_user'];
$game_db_pw = $config['games'][$game]['db_pw'];
$game_db_name = $config['games'][$game]['db_name'];

## setup default page number so this doesn't have to be in every file ##
$page_no = 0;