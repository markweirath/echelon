<?php
if (!empty($_SERVER["SCRIPT_FILENAME"]) && "inc.php" == basename($_SERVER["SCRIPT_FILENAME"]))
	die ("Please do not load this page directly. Thanks!"); // do not edit

error_reporting(E_ALL ^ E_NOTICE); // show all errors but notices

require 'inc/ctracker.php'; // anti worm injection protection
require 'inc/config.php'; // load the config file

if(INSTALLED != 'yes') // if echelon is not install (a constant is added to the end of the config during install) then die and tell the user to go install Echelon
	die('You still need to install Echelon. <a href="install/index.php">Install</a>');

require_once 'inc/functions.php'; // require all the basic functions used in this site
require 'classes/dbl-class.php'; // class to preform all DB related actions
$dbl = DBL::getInstance(); // start connection to the local Echelon DB

require 'inc/setup.php'; // class to preform all DB related actions

## If SSL required die if not an ssl connection ##
if($https_enabled == 1) :
	if(!detectSSL() && !isError()) { // if this is not an SSL secured page and this is not the error page
		sendError('ssl');
		exit;
	}
endif;

require 'classes/session-class.php'; // class to deal with the management of sesssions
require 'classes/members-class.php'; // class to preform all B3 DB related actions

## fire up the Sessions ##
$ses = new Session(); // create Session instance
$ses->sesStart('echelon', 0, PATH); // start session (name 'echelon', 0 => session cookie, path is echelon path so no access allowed oustide echelon path is allowed)

## create istance of the members class ##
$mem = new member($_SESSION['user_id'], $_SESSION['name'], $_SESSION['email']);

## Is B3 needed on this page ##
if($b3_conn) : // This is to stop connecting to the B3 Db for non B3 Db connection pages eg. Home, Site Admin, My Account
	require 'classes/mysql-class.php'; // class to preform all B3 DB related actions
	$db = DB_B3::getInstance($game_db_host, $game_db_user, $game_db_pw, $game_db_name, DB_B3_ERROR_ON); // create connection to the B3 DB

	// unset all the db info vars
	unset($game_db_host);
	unset($game_db_user);
	unset($game_db_pw);
	unset($game_db_name);
	
endif;

## Plugins Setup ##
if(!$no_plugins_active) : // if there are any registered plugins with this game
	
	require 'classes/plugins-class.php'; // require the plugins base class

	$plugins = new plugins(NULL);
	
	foreach($config['game']['plugins'] as $plugin) : // foreach plugin there is 
	
		// file = root to www path + echelon path + path to plugin from echelon path
		$file = getenv("DOCUMENT_ROOT").PATH.'lib/plugins/'.$plugin.'/class.php'; // abolsute path - needed because this page is include in all levels of this site
		
		if(file_exists($file)) :
			require $file;
			$plugins_class["$plugin"] = call_user_func(array($plugin, 'getInstance'), 'name');
			//$plugin::getInstance(); // create a new instance of the plugin (whatever, eg. xlrstats) plugin
		else :
			if($mem->reqLevel('manage_settings')) // only show the error to does who can fix it
				set_error('Unable to include the plugin file for the plugin '. $plugin .'<br /> In the directory: '. $file);
		endif;
		
	endforeach;
	
	plugins::setPluginsClass($plugins_class);
	
endif;

## If auth needed on this page ##
if(!isset($auth_user_here))
	$auth_user_here = true; // default to login required
	
if($auth_user_here != false) // some pages do not need auth but include this file so this following line is optional
	$mem->auth($auth_name); // see if user has the right access level is not on the BL and has not got a hack counter above 3

## remove tokens from 2 pages ago to stop build up
if(!isLogin()) : // stop login page from using this and moving the vars
	$tokens = array();
		
	$num_tokens = count($_SESSION['tokens']);
	
	if($num_tokens > 0) :
		foreach($_SESSION['tokens'] as $key => $value) :
			$tokens[$key] = $value;
		endforeach;
		$_SESSION['tokens'] = array();
	endif;
	
endif;

## if no time zone set display error ##
if(NO_TIME_ZONE) // if no time zoneset show warning message
	set_warning("Setup Error: The website's time zone is not set, defaulting to use Europe/London (GMT)");

## Block Internet Explorer ###
if($allow_ie == 0) {
	if (detectIE() && !isError()) // alow IE on the pubbans page aswell as the error page
		sendError('ie');
}