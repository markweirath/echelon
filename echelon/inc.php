<?php
error_reporting(E_ALL ^ E_NOTICE); // show all errors but notices
require_once 'inc/ctracker.php'; // anti worm injection protection
require_once 'inc/config.php'; // load the config file
require_once 'inc/functions.php'; // require all the basic functions used in this site

require 'classes/dbl-class.php'; // class to preform all DB related actions
$dbl = new DBL(); // start connection to the local Echelon DB

require 'inc/setup.php'; // class to preform all DB related actions
require 'classes/session-class.php'; // class to deal with the management of sesssions
require 'classes/members-class.php'; // class to preform all B3 DB related actions

## fire up the Sessions ##
$ses = new Session(); // create Session instance
$ses->sesStart('echelon', 0, PATH); // start session (name 'echelon', 0 => session cookie, path is echelon path so no access allowed oustide echelon path is allowed)

## create istance of the members class ##
$mem = new member($_SESSION['user_id'], $_SESSION['name'], $_SESSION['email']);

## Is B3 needed on this page ##
if($b3_conn == true) : // This is to stop connecting to the B3 Db for non B3 Db connection pages eg. Home, Site Admin, My Account
	require 'classes/mysql-class.php'; // class to preform all B3 DB related actions
	require 'classes/mysql-exception-class.php'; // class to preform all B3 DB related actions
	$db = new DB_B3($game_db_host, $game_db_user, $game_db_pw, $game_db_name, DB_B3_ERROR_ON); // create connection to the B3 DB
endif;

## If auth needed on this page ##
if(!isset($auth_user_here))
	$auth_user_here = true; // default to login required
	
if($auth_user_here != false) // some pages do not need auth but include this file so this following line is optional
	$mem->auth($auth_name); // see if user has the right access level is not on the BL and has not got a hack counter above 3

## remove tokens from 2 pages ago to stop build up
if(!isLogin($page)) { // stop login page from using this and moving the vars
	$tokens = array();
	foreach($_SESSION['tokens'] as $key => $value) :
		$tokens[$key] = $value;
	endforeach;
	$_SESSION['tokens'] = array();
}

## Check for HTTPS ##
$https = detectSSL(); // find out if SSL is enabled for this site

if($https_enabled) : // if https is FORCE enabled
	if($https == FALSE && !isError($page)) { // Check if https is off // If off throw error, logout and end script
		if($mem->loggedIn()) // if logged in
			$ses->logout(); // log user out
		exit;
		sendError('ssl'); // send user to error page with the SSL error code
	}
endif;

## if no time zone set display error ##
if(NO_TIME_ZONE) // if no time zoneset show warning message
	set_warning("Setup Error: The website's time zone is not set, defaulting to use Europe/London (GMT)");

## Block Internet Explorer ###
if($allow_ie == 0) {
	if (detectIE() && $page != 'error') // alow IE on the pubbans page aswell as the error page
		sendError('ie');
}