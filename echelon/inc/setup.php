<?php 
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'setup.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

include 'config.php';

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

## get the config array ##
$config = $dbl->getConfig();

$config['cosmos'] = array();
foreach($config as $setting) :
	if($setting['category'] == 'cosmos') {
		$config['cosmos'][$setting['name']] = $setting['value'];
	}
endforeach;
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

// if $game is greater than num_games then game doesn't exist so send to error page with error and reset game to 1
if($game > $num_games) {
	//$_SESSION['game'] = 1;
	//set_error('That game doesn\'t exist');
	//sendError();
	//exit;
	die('Massive Error');
}

// send the $config['game1'] arrays
$counter = 1;
while($counter <= $num_games) :
	$config['game'.$counter] = array();
	
	foreach($config as $setting) :
		if($setting['category'] == 'game'.$counter) {
			$config['game'.$counter][$setting['name']] = $setting['value'];
		}
	endforeach;
	
	$counter++;
endwhile;

$game_name = $config['game'.$game]['name'];
$game_name_short = $config['game'.$game]['short_name'];
$game_num_srvs = $config['game'.$game]['num_srvs'];
$game_db_host = $config['game'.$game]['db_host'];
$game_db_user = $config['game'.$game]['db_user'];
$game_db_pw = $config['game'.$game]['db_pw'];
$game_db_name = $config['game'.$game]['db_name'];

$page_no = 0; // setup default page number so this doesn't have to be in every file
