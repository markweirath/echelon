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
	$_SESSION['name'] = $this->name;
}

/**
 * Sets the class email var
 */
function setEmail($email) {
	$this->email = $email;
	$_SESSION['email'] = $this->email;
}

/**
 * Checks if a user is logged in or not
 *
 * @return bool
 */
function loggedIn() { // are they logged in
	if($_SESSION['auth']) // if authorised allow access
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
	if(!$this->loggedIn()) { // if not authorised/logged in
		global $page;
		if($page != "home") //don't set an error if we came from the homepage
			set_error('Please login to Echelon');
		sendLogin();
		exit;
	}
	if(!$this->reqLevel($name)) { // if users level is less than needed access, deny entry, and cause error
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
 * @param int $min_pw_len - min len of password
 * @return bool(true)/string(error)
 */
function genAndSetNewPW($password, $user_id, $min_pw_len) {

	// get the DB instance pointer
	$dbl = DBL::getInstance();
	
	// check that the supplied password meets the required password policy for strong passwords
	if(!$this->pwStrength($password, $min_pw_len)) { // false: not strong enough
		return 'The password you supplied is not strong enough, a password must be longer than '. $min_pw_len .' character and should follow this <a href="http://echelon.bigbrotherbot.net/pw/" title="Echelon Password Policy">policy</a>.';
		exit;
	}
	
	// generate a new salt for the user
	$salt_new = genSalt();
	
	// find the hash of the supplied password and the new salt
	$password_hash = genPW($password, $salt_new);
	
	// update the user with new password and new salt
	$results_pw = $dbl->editMePW($password_hash, $salt_new, $user_id);
	if($results_pw == false) {
		return 'There was an error changing your password';
	} else
		return true;
}

/**
 * Echo out the display name in a link, if the display name is not set echo guest.
 */
function displayName() {

	if($this->name == '')
		echo 'Guest';
	else
		echo '<a href="'.PATH.'me.php" title="Go to your own account settings">'. $this->name .'</a>';
		
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
		gravatar_id=".md5( strtolower($email) ) . '?d=identicon';
	} else {
		$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( $email ) ).'?d=identicon';
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

/**
 * Checks if the password is strong enough - uses mutliple checks
 *
 * @param string $password - the password you wish to check
 * @param int $min_pw_len - minimun lenght a password can be
 * @return bool (false: not strong enough/ true strong enough)
 */
function pwStrength($password, $min_pw_len = 8) {

	$length = strlen($password); // get the length of the password 

	$power = 0; // start at 0

	// if password is shorter than min required lenght return false
	if($length < $min_pw_len)
		return false;

    // check if password is not all lower case 
    if(strtolower($password) != $password)
        $power++;
    
    // check if password is not all upper case 
    if(strtoupper($password) == $password)
        $power++;

    // check string length is 8-16 chars 
    if($length >= 8 && $length <= 16)
        $power++;

    // check if lenth is 17 - 25 chars 
    elseif($length >= 17 && $length <=25)
        $power += 2;

    // check if length greater than 25 chars 
    elseif($length > 25)
        $power += 3;
    
    // get the numbers in the password 
    preg_match_all('/[0-9]/', $password, $numbers);
    $power += count($numbers[0]);

    // check for special chars 
    preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^\\\]/', $password, $specialchars);
    $spec = sizeof($specialchars[0]);
	if($spec < 2)
		$power--;
	elseif($spec <= 4)
		$power++;
	elseif($spec == 5)
		$power += 2;
	elseif($length / $spec >= 2) //check that half the password isn't special characters
		$power += 3;
	

    // get the number of unique chars 
    $chars = str_split($password);
    $num_unique_chars = sizeof( array_unique($chars) );
    $power += floor(($num_unique_chars / 2));
	
	if($power > 10)
		$power = 10;
	
	// if the password is strong enough return true, else return false
	if($power >= 5)
		return true;
	return false;

}


#############################
} // end class