<?php
	#### FUNCTIONS.PHP ####
## Basic functions that help run all pages on this site ##
## This page is included on all pages in this project ##

/**
 * Generates a general hash with sha1 and md5
 *
 * @param string $unhashed_text - the text you would like to hash
 * @return string
 */
function genHash($unhashed_text) {
	$salt = "D2pPnJhmxRC5"; // define salt
	$md5 = md5($unhashed_text); // get md5
	$hashed = sha1($salt.$md5); // get hash of text plus salt in sha1

	return $hashed; // return the inputted text
}

/**
 * Generates a password
 *
 * @param string $input - the actual clear text password
 * @param string $salt - the salt with which to hash the password
 * @return string $pw - hashed form of salt and inputted text
 */
function genPW($input, $salt) {
	$data = $input.$salt;
	$pw = hash("sha256", $data); // sha256 hash the passsword and the salt for an irrevrsible hash
	return $pw;
}

/**
 * Detect an AJAX request
 *
 * @return bool
 */
function detectAJAX() {
	/* AJAX check  */
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		return true;
	else
		return false;
		
	// This method is not full proof since all servers do not support the $_SERVER['HTTP_X_REQUESTED_WITH'] varible.
}

/**
 * Checks if a user has attempted to login to many times or has been caught hacking the site
 */
function locked() {
	if($_SESSION['wrong'] >= 3 || $_SESSION['hack'] >= 3) {
		if($mem->loggedIn()) {
			logout(); // if they are logged in log them out
		}
		if(!isset($dbl)){ // if no Db object
			$dbl = new DBl(); // create DB
		}
		$ip = getRealIp(); // get users ip
		$dbl->blacklist($ip); // add top blacklist
		writeLog('Locked out automatically.');
		sendLocked();
	}
}

/**
 * Checks Blacklist for the users IP address and if banned send to locked
 */
function checkBL() {
	if(!isset($dbl)){ // if no Db object
		$dbl = new DBl(); // create DB
	}
	$ip = getRealIp(); // find real IP
	$result = $dbl->checkBlacklist($ip); // query db and check if ip is on list
	if($result == true)// if on blacklist
		sendLocked(); // send to locked page
}

/**
 * Find how many login attempts the user has made
 */
function trys() { //
	echo '<em class="trys">';

	if($_SESSION['wrong'] != 0)
		echo 'You have used '.$_SESSION['wrong'].' of 3 attempts to login';
	else
		echo 'Please login to Echelon';
		
	echo '</em><br />';
}

/**
 * Add a number to the wrong login attempt counter
 *
 * @param string $num - num to add to the wrong counter
 */
function wrong($num) { // add $num to number of already recorded wrong attempts
	$_SESSION['wrong'] = $_SESSION['wrong'] + $num;
}

/**
 * Add a number to the hacking attempt counter
 *
 * @param string $num - num to add to the hacking attempt counter
 */
function hack($num) {
	$_SESSION['hack'] = $_SESSION['hack'] + $num;
}

/**
 * Set an error message that is to be sent to the user
 *
 * @param string $msg - the error message
 */
function set_error($msg) {
	$_SESSION['error'] = $msg;
}

/**
 * Set a sucess message to be sent to the user
 *
 * @param string $msg - the message
 */
function set_good($msg) {
	$_SESSION['good'] = $msg;
}

/**
 * Get the IP address of the current user
 *
 * @return string $ip - IP address of the user
 */
function getRealIp() {
	if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

/**
 * Check is a form var is empty if so set error and send back to reffering page
 *
 * @param string $var_name - the varible to check
 * @param string $field - the name of the varible (used in the error message) eg. 'your new password'
 */
function emptyInput($var_name, $field) {
	$var = trim($var_name);
	$ref = $_SERVER['HTTP_REFERER'];
	if(empty($var)) {
		set_error('You must put something in the '.$field.' field.');
		send($ref); // send back to referering page
		exit;
	}
} // end function

/**
 * Cleans var of unwanted materials
 *
 * @param string $var - var to be cleaned
 * @return string
 */
function cleanvar($var) {
	$var = trim(strip_tags($var));
	return $var;
} // end clean var

/**
 * Send a user back to the reffering page with an error
 *
 * @param string $error - the error message that will be sent to the user
 */
function sendBack($error) {
	$ref = $_SERVER['HTTP_REFERER'];
	set_error($error);
	send($ref); // send back to referering page
	exit; // end script
}

/**
 * Send a user back to the reffering page with a sucess msg
 *
 * @param string $good - sucess message to be sent to the user
 */
function sendGood($good) {
	$ref = $_SERVER['HTTP_REFERER'];
	set_good($good);
	send($ref); // send back to referering page
	exit; // end script
}

/**
 * Send user to a given page
 *
 * @param string $where - page to send user to
 */
function send($where) {
	header("Location: {$where}");
}

/**
 * Send user to login page
 */
function sendLogin() { 
	header("Location: {$path}login.php");
}

/**
 * send to the locked page
 */
function sendLocked() {
	header("Location: {$path}error.php?t=locked");
}

/**
 * Send to home page
 */
function sendHome() {
	header("Location: {$path}index.php");
}

/**
 * Send to the error page
 */
function sendError() {
	header("Location: {$path}error.php");
}

function is_clients($page) {
	if($page == 'client')
		return true;
	else
		return false;
}

function guidCheckLink($guid) {

	echo '<a class="external" href="http://www.punksbusted.com/cgi-bin/membership/guidcheck.cgi?guid='.$guid.'" title="Check this guid is not banned by PunksBusted.com">'.$guid.'</a>';

}

/**
 * parse IP address into link to ipwhois
 *
 * @param string $ip - ip address to use in link
 * @return string $msg - the link to whois of IP
 */
function ipLink($ip) {
	$msg = '<a href="http://whois.domaintools.com/'.$ip.'/" target="_blank" title="WhoIs IP Search this User">'.$ip.'</a>';
	return $msg;
}

/**
 * parse Email address into a mailto user link
 *
 * @param string $email - email address for link
 * @param string $name - name of the person
 * @return string $msg - the link to whois of IP
 */
function emailLink($email, $name) {
	if($name == '') // if name is not set make name the same as email
		$name = $email;
		
	$msg = '<a href="mailto:'.$email.'" title="Send '.$name.' an email">'.$email.'</a>';
	return $msg;
}

/**
 * Parse vars in a view user in more details link
 *
 * @param string $id - id of the user
 * @param string $name - name of the person
 * @return string $msg - the link to user
 */
function echUserLink($id, $name) {
	$msg = '<a href="sa.php?t=user&amp;id='.$id.'" title="View '.$name.' in more detail">'.$name.'</a>';
	return $msg;
}

function totalPages($total_rows, $max_rows) {
	$total_pages = ceil($total_rows/$max_rows)-1;
	return $total_pages;
}

function recordNumber($start_row, $max_rows, $total_rows) {

	echo 'Records: '.($start_row + 1).'&nbsp;to&nbsp;'.min($start_row + $max_rows, $total_rows).'&nbsp;of&nbsp;'.$total_rows;
}

function queryStringPage() {

	if (!empty($_SERVER['QUERY_STRING'])) {
	
		$params = explode("&", $_SERVER['QUERY_STRING']);
		$newParams = array();
		
		foreach ($params as $param) {
			if (stristr($param, "p") == false) {
				array_push($newParams, $param);
			}
		}
		if (count($newParams) != 0) {
			$query_string_page = "&" . implode("&", $newParams);
		}
		
	}
	
	return $query_string_page;
}

function linkSort($keyword, $title) {

	$this_p = $_SERVER['PHP_SELF'];
	
	echo '<a title="Sort information by '.$title.' ascending." href="'.$this_p.'?ob='.$keyword.'&amp;o=asc"><img src="'. $path .'images/asc.png" alt="ASC" class="asc-img" /></a>
			&nbsp;
			<a title="Sort information by '.$title.' descending." href="'.$this_p.'?ob='.$keyword.'&amp;o=desc"><img src="'. $path .'images/desc.png" alt="DESC" class="desc-img" /></a>';

}

function linkSortClients($keyword, $title, $is_search, $search_type, $search_string) {

	$this_p = $_SERVER['PHP_SELF'];
	
	if($is_search == false) {
		echo'<a title="Sort information by '.$title.' ascending." href="'.$this_p.'?ob='.$keyword.'&amp;o=asc"><img src="'. $path .'images/asc.png" alt="ASC" class="asc-img" /></a>
			&nbsp;
		<a title="Sort information by '.$title.' descending." href="'.$this_p.'?ob='.$keyword.'&amp;o=desc"><img src="'. $path .'images/desc.png" alt="DESC" class="desc-img" /></a>';
	} else {
		echo'<a title="Sort information by '.$title.' ascending." href="'.$this_p.'?ob='.$keyword.'&amp;o=asc&amp;s='.urlencode($search_string).'&amp;t='.$search_type.'"><img src="'. $path .'images/asc.png" alt="ASC" class="asc-img" /></a>
			&nbsp;
		<a title="Sort information by '.$title.' descending." href="'.$this_p.'?ob='.$keyword.'&amp;o=desc&amp;s='.urlencode($search_string).'&amp;t='.$search_type.'"><img src="'. $path .'images/desc.png" alt="DESC" class="desc-img" /></a>';
	}

}

function removeColorCode($text) {

	$text = preg_replace('/\\^([0-9])/ie', '', $text);
	return $text;
}

function tableClean($text) {

	$text = htmlspecialchars($text);
	return $text;
}

function timeExpire($time_expire, $type, $inactive, $tformat) {

	$time = time();

	if (($time_expire <= $time) && ($time_expire != -1)) {
		$msg = "<span class=\"p-expired\">".date($tformat, $time_expire)."</span>";

	} elseif ($time_expire == '-1') {
		$msg = "<span class=\"p-permanent\">Permanent</span>";

	} elseif ($time_expire > $time) {
		$msg = "<span class=\"p-active\">".date($tformat, $time_expire)."</span>";
	}

	if ($type == 'Kick') {
		$msg = "<em>(Kick Only)</em>";

	} elseif ($type == 'Notice'){
		$msg = "<span class=\"p-inactive\">Notice</span>";

	} elseif ($inactive == "1") {
		$msg = "<span class=\"p-inactive\">De-activated</span>";

	}
	
	if($msg == '') // if we got nothing then return unknown
		$msg = '<em>(Unknwon)</em>';

	return $msg;
}

function timeExpirePen($time_expire, $tformat) {
	if (($time_expire <= time()) && ($time_expire != -1))
		$msg = "<span class=\"p-expired\">".date($tformat, $time_expire)."</span>"; 
	
	if ($time_expire == -1)
		$msg = "<span class=\"p-permanent\">Permanent</span>"; 
	
	if ($time_expire > time())
		$msg = "<span class=\"p-active\">".date($tformat, $time_expire)."</span>"; 
	
	return $msg;
}


function settingText($name, $title, $value, $type) {
	switch ($type) {
		case 'int':
			$text = '<input type="text" class="int" value="'.$value.'" name="'.$name.'" /><label for="'.$name.'">'.$title.'</label><br />';
		break;
		case 'password':
			$text = '<label for="'.$name.'">'.$title.':</label><input type="password" value="'.$value.'" name="'.$name.'" /><br />';
		break;
		case 'textarea':
			$text = '<label for="'.$name.'">'.$title.':</label><textarea rows="6" name="'.$name.'">'.$value.'</textarea><br />';
		break;
		case 'check':
			if($value == 1) {
				$text = '<input type="checkbox" name="'.$name.'" checked="checked"><label for="'.$name.'">'.$title.'</label><br /><br />';
			} else {
				$text = '<input type="checkbox" name="'.$name.'"><label for="'.$name.'">'.$title.'</label><br /><br />';
			}
		break;
		default: // if text
			$text = '<label for="'.$name.'">'.$title.':</label><input type="text" value="'.$value.'" name="'.$name.'" /><br />';
		break;
	}
	return $text;
}

function writeLog($where) {
    
	$ip = getRealIp(); // Get the IP from superglobal
	$host = gethostbyaddr($ip);    // Try to locate the host of the attack
	$date = date("d M Y (H:i)");
	
	// create a logging message with php heredoc syntax
	$logging = <<<LOGMSGG
	There was a hacking attempt on E32Ds. \n 
	Date of Attack: {$date}
	IP-Adress: {$ip} \n
	Host of Attacker: {$host}
	Point of Attack: {$where}
LOGMSGG;
// Awkward but LOG must be flush left

	$to = HACK_EMAIL;  
	$subject = 'HACK ATTEMPT';
	$header = 'From: echelon@b3-echelon.com';
	mail($to, $subject, $logging, $header);    
} // end hackLog
	
function verifyFormToken($form, $tokens) {
        
	// check if a session is started and a token is transmitted, if not return an error
	if(!isset($tokens[$form])) 
		return false;
	
	// check if the form is sent with token in it
	if(!isset($_POST['token']))
		return false;
	
	// compare the tokens against each other if they are still the same
	if ($tokens[$form] !== $_POST['token'])
		return false;
	
	return true;
}

function verifyFormTokenLogin($form) {
        
	// check if a session is started and a token is transmitted, if not return an error
	if(!isset($_SESSION['tokens'][$form]))
		return false;

	// check if the form is sent with token in it
	if(!isset($_POST['token']))
		return false;

	// compare the tokens against each other if they are still the same
	if ($_SESSION['tokens'][$form] !== $_POST['token'])
		return false;

	return true;
}

function genFormToken($form) {
    
	// generate a token from an unique value, taken from microtime, you can also use salt-values, other crypting methods...
	$token = genHash(uniqid(microtime(), true));  
	
	// Write the generated token to the session variable to check it against the hidden field when the form is sent
	$_SESSION['tokens'][$form] = $token; 
	
	return $token;
}

function ifTokenBad($place) {
	hack(1); // plus 1 to hack counter
	writeLog($place.' - Bad Token'); // make note in log
	sendBack('Hack Attempt Detected - If you continue you will be removed from this site'); // return to login page
	exit;
}

/**
 * Echos out all the different types of error/sucess/warning messages
 */
function errors() {
    $message = '';
    if($_SESSION['good'] != '') {
        $message = '<div id="msg" class="success"><strong>Success:</strong> '.$_SESSION['good'].'<a class="err-close">Dismiss</a></div>';
        $_SESSION['good'] = '';
    }
    if($_SESSION['error'] != '') {
        $message = '<div id="msg" class="error"><strong>Error:</strong> '.$_SESSION['error'].'<a class="err-close">Dismiss</a></div>';
        $_SESSION['error'] = '';
    }
	if($_SESSION['warning'] != '') {
        $message = '<div id="msg" class="warning"><strong>Warning:</strong> '.$_SESSION['warning'].'<a class="err-close">Dismiss</a></div>';
        $_SESSION['warning'] = '';
    }
    
	echo $message;
}

/**
 * Generate a random password or string
 *
 * @param int $count - lenght of the string
 * @return string
 */
function randPass($count) {  

	$pass = str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@#%$*'); //shuffle
	
	return substr($pass,3,$count); //returns the password  
}

/**
 * Detect an SSL connection
 *
 * @reutnr bool
 */
function detectSSL(){
	if($_SERVER["https"] == "on")
		return true;
		
	elseif ($_SERVER["https"] == 1)
		return true;
		
	elseif ($_SERVER['SERVER_PORT'] == 443)
		return true;
		
	else
		return false;
}

/**
 * Encrypt a string
 *
 * @param string $algo - the encryption algorithm
 * @param string $mode - the mode of encryption
 * @param string $key_sent - the passcode to encrypt the text
 * @param string $data_sent - the data to encrypt
 * @param string $iv_sent - the iv used to encrypt
 * @return string $results - the base64 of the encryption (its return in base64 to make the text compatible with certain applications eg. MySQL)
 */
function encrypt($algo = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC, $key_sent, $data, $iv_sent) {
	
	srand((double)microtime()*1000000 ); // seed iv by starting random number generator
	$td = mcrypt_module_open($algo, '', $mode, ''); // select algo and mode
	if($iv_sent == "") { // weather or not a new iv needs to be created
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	} else {
		$iv = base64_decode($iv_sent);
	}
	$ks = mcrypt_enc_get_key_size($td); // find value of posible largest key size
	$key = substr(sha1($key_sent), 0, $ks); // generate largest posible key from hash
	
	mcrypt_generic_init($td, $key, $iv);
	$ciphertext = mcrypt_generic($td, $data); // run encryption
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	
	## encode data and return in an array
	$iv = base64_encode($iv);
	$enc = base64_encode(trim($ciphertext));
	$results = array($iv, $enc);
	return $results;
}

/**
 * Decrypt a string
 *
 * @param string $algo - the decryption algorithm
 * @param string $mode - the mode of decryption
 * @param string $key_sent - the passcode to decrypt the text
 * @param string $data_sent - the data to decrypt in base64
 * @param string $iv_sent - the iv used to encrypt
 * @return string $plaintext
 */
function decrypt($algo = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC, $key_sent, $data_sent, $iv_sent) {
	
	$td = mcrypt_module_open($algo, '', $mode, ''); // select algo and mode
	$iv = base64_decode($iv_sent); // decode
	$ks = mcrypt_enc_get_key_size($td); // find value of posible largest key size
	$key = substr(sha1($key_sent), 0, $ks); // generate largeest posible key from hash
	$data = base64_decode($data_sent); // decode
	
	mcrypt_generic_init($td, $key, $iv);
	$plaintext = mdecrypt_generic($td, $data);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	
	return trim($plaintext);
}

/**
 * A function for making time periods readable
 *
 * @link        http://aidanlister.com/2004/04/making-time-periods-readable/
 * @param       int     number of seconds elapsed
 * @param       string  which time periods to display
 * @param       bool    whether to show zero time periods
 */
function time_duration($seconds, $use = null, $zeros = false) {

	if($seconds == '') {
		return '';
	}
	
    // Define time periods
    $periods = array (
        'years'     => 31556926,
        'Months'    => 2629743,
        'weeks'     => 604800,
        'days'      => 86400,
        'hours'     => 3600,
        'minutes'   => 60,
        'seconds'   => 1
        );

    // Break into periods
    $seconds = (float) $seconds;
    foreach ($periods as $period => $value) {
        if ($use && strpos($use, $period[0]) === false) {
            continue;
        }
        $count = floor($seconds / $value);
        if ($count == 0 && !$zeros) {
            continue;
        }
        $segments[strtolower($period)] = $count;
        $seconds = $seconds % $value;
    }

    // Build the string
    foreach ($segments as $key => $value) {
        $segment_name = substr($key, 0, -1);
        $segment = $value . ' ' . $segment_name;
        if ($value != 1) {
            $segment .= 's';
        }
        $array[] = $segment;
    }

    $str = implode(', ', $array);
    return $str;
}

// end functions file