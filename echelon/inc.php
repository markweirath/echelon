<?php
require 'inc/ctracker.php'; // anti worm injection protection
require_once 'inc/config.php'; // load the config file
require_once 'inc/functions.php'; // require all the basic functions used in this site
require_once 'classes/dbl-class.php'; // class to preform all DB related actions
$dbl = new DBL();
require_once 'inc/setup.php'; // class to preform all DB related actions
require_once 'classes/session-class.php'; // class to deal with the management of sesssions
require_once 'classes/mysql-class.php'; // class to preform all B3 DB related actions

$ses = new Session(); // create Session Object
$ses->sesStart(); // start session

if($b3_conn) // if this is true then connect. This is to stop connecting to the B3 Db for non b3 Db connection pages eg. Home, Site Admin, My Account
	$db = new DB_B3($game_db_host, $game_db_user, $game_db_pw, $game_db_name); // create connection to the B3 DB

auth($auth_name); // see if user has the right access level is not on the BL and has not got a hack counter above 3

## remove tokens from 2 pages ago to stop build up
$tokens = array();
foreach($_SESSION['tokens'] as $key => $value) :
	$tokens[$key] = $value;
endforeach;
$_SESSION['tokens'] = array();

## Check for HTTPS
$https = detectSSL(); // find out if SSL is enabled for this site

if($https_enabled) { // if https is FORCE enabled
	if($https == FALSE) { // Check if https is off // If off throw error, logout and end script
		set_error('An SSL connection is required for this site, and you did not seem to have one.');
		logout();
		exit;
	}
}