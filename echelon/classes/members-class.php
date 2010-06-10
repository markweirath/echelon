<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'members-class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');
		
## Some User Functions ##

class member {

var $user_id;
var $name;
var $email;

function __construct($user_id, $name, $email) {
	$this->id = $user_id;
	$this->name = $name;
	$this->email = $email;
}

/**
 * Sets the class name var
 */
function setName($name) {
	$this->name = $name;
}

/**
 * Sets the class email var
 */
function setEmail($email) {
	$this->email = $email;
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
 * Takes password and generates salt and hash. Then updates their password in the DB
 *
 * @param string $password - the new password the user
 * @param int $user_id - the id of the user that is being edited
 * @param object $dbl - the instance of the local DB connection class
 * @return bool
 */
function genAndSetNewPW($password, $user_id, $dbl) {
	// generate a new salt for the user
	$salt_new = genSalt();
	
	// find the hash of the supplied password and the new salt
	$password_new = genPW($password, $salt_new);
	
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
 * Echo out the display name in a link, if the display name is not set echo guest.
 */
function displayName() {

	if($this->name == '')
		echo 'Guest';
	else
		echo '<a href="'.PATH.'me.php" title="Go to your own account settings">'.$this->name.'</a>';
		
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
	$size = 32;

	$https = detectSSL();
	
	if($https) {
		$grav_url = "https://secure.gravatar.com/avatar.php?
		gravatar_id=".md5( strtolower($email) );
	} else {
		$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( $email ) );
	}
	
	$gravatar = '<span class="gravatar">
			<a href="http://gravatar.com/" target="_blank" title="Get your own personalised image">
				<img width="32" src="'.$grav_url.'" alt="" />
			</a>
		</span>';
	
	return $gravatar;
}

/**
 * Using a user's password this func sees if the user inputed the right password for action verification
 *
 * @param string $password
 */
function reAuthUser($password, $dbl) {

	// Check to see if this person is real
	$salt = $dbl->getUserSaltById($this->id);

	if($salt == false) // only returns false if no salt found, ie. user does not exist
		sendBack('There is a problem, you do not seem to exist!');

	$hash_pw = genPW($password, $salt); // hash the inputted pw with the returned salt

	// Check to see that the supplied password is correct
	$validate = $dbl->validateUserRequest($this->id, $hash_pw);
	if(!$validate) {
		hack(1); // add one to hack counter to stop brute force
		sendBack('You have supplied an incorrect current password');
	}
	
}

#############################
} // end class