<?php 
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'setup.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

require_once 'config.php'; // if config is not loaded load it in

$this_page = cleanvar($_SERVER["PHP_SELF"]);

$cookie_time = time()*60*60*24*31; // 31 days from now
## setup the game var ##
if($_GET['game']) {
	$game = $_GET['game'];
	setcookie("game", $game, $cookie_time, PATH); // set the cookie to game value

} elseif($_POST['game']) {
	$game = addslashes($_POST['game']);
	setcookie("game", $game, $cookie_time, PATH); // set the cookie to game value

} elseif($_COOKIE['game']) {
	$game = $_COOKIE['game'];
	
} else {
	$game = 1;
	setcookie("game", $game, $cookie_time, PATH); // set the cookie to game value
}


## Setup Arrays ##
$config = array();
$config['cosmos'] = array();

## Get the config array ##
$config['cosmos'] = $dbl->getSettings();

// find the number of games from the config array
$num_games = $config['cosmos']['num_games'];

$site_name = $config['cosmos']['name'];
$limit_rows = $config['cosmos']['limit_rows'];
$allow_ie = $config['cosmos']['allow_ie'];
$min_pw_len = $config['cosmmos']['min_pw_len'];
$https_enabled = $config['cosmos']['https'];
$key_expire = $config['cosmos']['user_key_expire']; // This var says how long it takes for a user creation key to expire
$tformat = $config['cosmos']['time_format'];
$time_zone = $config['cosmos']['time_zone'];

// define email constant
define("EMAIL", $config['cosmos']['email']);

## Time Zone Setup ##
if($time_zone == '') {
	$time_zone == 'Europe/London';
	define("NO_TIME_ZONE", TRUE);
} else {
	define("NO_TIME_ZONE", FALSE);
}
date_default_timezone_set($time_zone);


// if $game is greater than num_games then game doesn't exist so send to error page with error and reset game to 1
if($num_games == 0) {
	$no_games = true;

} elseif($game > $num_games) {
	setcookie("game", 1, time()*60*60*24*31, $path); // set the cookie to game value
	set_error('That game doesn\'t exist');
	if($page != 'error')
		sendError();
}

## Get the games Information for the current game ##
$config['game'] = $dbl->getGameInfo($game);

## setup the plugins into an array
if(!empty($config['game']['plugins'])) {
	$config['game']['plugins'] = explode(",", $config['game']['plugins']);
	$no_plugins_active = false;
} else
	$no_plugins_active = true;

## Get and setup the servers information into the array ##
$servers = $dbl->getServers($game);

$config['game']['servers'] = array(); // create array

## add server information to config array##
$i = 1; // start counter ("i") at 1

if(!empty($servers)) :

	foreach($servers as $server) : // loop thro the list of servers for current game
		
		$config['game']['servers'][$i] = array();
		$config['game']['servers'][$i]['name'] = $server['name'];
		$config['game']['servers'][$i]['ip'] = $server['ip'];
		$config['game']['servers'][$i]['pb_active'] = $server['pb_active'];
		$config['game']['servers'][$i]['rcon_pass'] = $server['rcon_pass'];
		$config['game']['servers'][$i]['rcon_ip'] = $server['rcon_ip'];
		$config['game']['servers'][$i]['rcon_port'] = $server['rcon_port'];
		
		$i++; // increment counter
	endforeach;

endif;

if($config['game']['num_srvs'] > 1) :
	define("MULTI_SERVER", true); 
	define("NO_SERVER", false);
	
elseif($config['game']['num_srvs'] == 1) : 
	define("MULTI_SERVER", false);
	define("NO_SERVER", false);

else :	// equal to no servers
	define("MULTI_SERVER", false);
	define("NO_SERVER", true);

endif;

## Setup some handy easy to access information for the CURRENT GAME only ##

$game_name = $config['game']['name'];
$game_name_short = $config['game']['name_short'];
$game_num_srvs = $config['game']['num_srvs'];
$game_db_host = $config['game']['db_host'];
$game_db_user = $config['game']['db_user'];
$game_db_pw = $config['game']['db_pw'];
$game_db_name = $config['game']['db_name'];

## setup default page number so this doesn't have to be in every file ##
$page_no = 0;