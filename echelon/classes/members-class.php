<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'members-class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');
		
## Some User Functions ##

class member {

function __construct() {

}

/**
 * Checks if a user is logged in or not
 *
 * @return bool
 */
function loggedIn() { // are they logged in
	if($_SESSION['auth'] == true) // if authorised allow access
		return true;
	else
		return false; // if not authorised
}

/**
 * Checks if a user has the rights to view this page, is not locked/banned or not logged in
 *
 * @param string $name - permission name
 */
function auth($name) {
	locked(); // stop blocked people from acessing	
	if(!$this->loggedIn()) { // if not authorised
		set_error('You must log in');
		sendLogin();
		exit;
	}
	checkBL(); // check ban list for IP	
	$perm_required = $_SESSION['perms'][$name];
	if(!$perm_required) { // if users level is less than needed access, deny entry, and cause error
		set_error('You do not have the correct privilages to view that page');
		sendHome();
		exit;
	}
}

function reqLevel($name) {
	$perm_required = $_SESSION['perms'][$name];
	if(!$perm_required) // if users level is less than needed access return false
		return false;
	else
		return true;
}

/**
 * Logs a user out
 */
function logout() {

	$error = $_SESSION['error']; // perserve errors if the person is loggedout by error

	$_SESSION = array(); // unsets all varibles

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (isset($_COOKIE[session_name()])) {
	   setcookie(session_name(), '', time()-42000, '/');
	}
	
	// This is useful for when you change authentication states as it also invalidates the old session. 
	session::regenerateSession();
	
	// Finally, destroy the session.
	session_destroy();

	session::sesStart(); // start session
	$_SESSION['error'] = $error; // add error to new session
	
}

/**
 * Takes password and generates salt and hash. Then updates their password in the DB
 *
 * @param string $password - the new password the user
 * @param int $user_id - the id of the user that is being edited
 * @return bool
 */
function genAndSetNewPW($password, $user_id) {
	// generate a new salt for the user
	$salt_new = randPass(12);
	
	// find the hash of the supplied password and the new salt
	$password_new = genPW($password, $salt_new);
	
	if(!isset($dbl))
		$dbl = new DBL();
	
	// update the user with new password and new salt
	$results_pw = $dbl->editMePW($password_new, $salt_new, $user_id);
	if(results_pw == false) {
		set_error('There was an error changing your password');
		return false;
	} else {
		return true;
	}
}

/**
 * Generates a fingerprint for anti session hijacking
 *
 * @return string
 */
static function getFinger() {
	$user_agent = $_SERVER['HTTP_USER_AGENT']; // get browser name from user
	return genHash($user_agent."DcEx"); // return hash of browser and a small salt
}

/**
 * Echo out the display name in a link, if the display name is not set echo guest.
 *
 * @param string $name - display name of the user
 */
function displayName($name) {
	if($name == '')
		echo 'Guest';
	else
		echo '<a href="'.$path.'me.php" title="Go to your own account settings">'.$name.'</a>';
		
	return;
}

/**
 * Echo out the time the user was last seen, if there is no time echo out a welcome
 *
 * @param string $time - time format (default is availble)
 */
function lastSeen($time_format = 'd M y') {

	if($_SESSION['last_seen'] != '')
		echo 'Last Seen: '. date($time_format, $_SESSION['last_seen']);
	else
		echo 'Welcome to Echelon!';
}

/**
 * Gets a users gravatar from gravatar.com
 *
 * @param string $email - email address of the current user
 * @return string
 */
function getGravatar($email) {
	$default = "https://s.eire32designs.com/echelon/images/nav/default-person.jpg"; // get variable from config.php
	$size = 32;
	
	$grav_url = "https://secure.gravatar.com/avatar.php?
	gravatar_id=".md5( strtolower($email) ).
	"&amp;default=".urlencode($default).
	"&amp;size=".$size; 
	
	return $grav_url;
}

#############################
} // end class