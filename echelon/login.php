<?php
$auth_user_here = false;
$pagination = false;
$b3_conn = false;
$page = 'login'; // do not remove needed to keep the toke in the session array and not be moved into the $tokens array
require 'inc.php';

if(!$mem->loggedIn()) // if not logged in
	checkBL(); // check the blacklist for the users IP
	
##### start script #####

if($mem->loggedIn()) { ## if logged in users may skip this page
	sendHome(); // send to the index/home page
	
} elseif ($_POST['f-name']) { ## if this is a log in request 

	// if over the maxium amount of wrong attempts,
	// or if hack attempts detected, BL user IP and remove user
	locked();

	// set sent vars
	$username = cleanvar($_POST['f-name']); // strip and remove spaces from vars
	$pw = $_POST['f-pw'];
	$game_input = cleanvar($_POST['f-game']);

	// are they empty values
	emptyInput($username, 'username');
	emptyInput($pw, 'password');
	
	if(!verifyFormTokenLogin('login')) : // verify token
		wrong(1); // plus 1 to wrong counter
		hack(1); // plus 1 to hack counter
		writeLog('Login - Bad Token'); // make note in log
		sendBack('Login Failed - Stop! Attack detected!!!');
		exit;
	endif;

	// Building a whitelist array with keys which will send through the form, no others would be accepted later on
	$whitelist = array('token','f-name','f-pw', 'f-game');

	// Building an array with the $_POST-superglobal 
	foreach ($_POST as $key=>$item) :
		if(!in_array($key, $whitelist)) {
			wrong(1); // plus 1 to wrong counter
			hack(1); // plus 1 to hack counter
			writeLog('Login - Unknown form fields provided'); // make note of event
			sendBack('Login Failed - Stop! Attack detected');
			exit;
		}
	endforeach;
	
		
	if(!is_numeric($game_input) && !$no_games) {
		wrong(1); // plus 1 to wrong counter
		hack(1); // plus 1 to hack counter
		writeLog('Login - Bad game number'); // make note in log
		sendBack('Invalid data sent, that is not a valid game');
		exit;
	}
		
	if($game_input > $num_games && !$no_games)
		sendBack('That is not a game, please choose to load a real game');
	
	######## Everything is all right continue with script #########
	
	// we must first find the salt of the user trying to login
	$salt_result = $dbl->getUserSalt($username);
	if($salt_result == false) { // if nothing found then the user name must not exsist
		wrong(1); // add one to wrong counter
		sendBack('Bad login attempt, please try again.');
	} else {
		$salt = $salt_result; // set var salt with information from the database
	}
	
	$hash_pw = genPW($pw, $salt); // hash the inputted pw with the returned salt

	## Check login info off db records ##
	$results = $dbl->login($username, $hash_pw); // check recieved information off the DB records
	
	if(is_array($results)) // if true // for true is returned in an array
		$login_success = true;
	else
		$login_success = false;	
	
	if(!$login_success) { // send back if user login failed
		wrong(1); // add one to wrong counter
		sendBack('Bad login attempt, please try again.'); // return to login page with the error
	}
	
	## get premissions
	$perms = $dbl->getPermissions(); // get a comprehensive list of Echelon permissions
	$perms_list = $results[6]; // value of perms for users group db

	$_SESSION['perms'] = array();
	
	$perms_list_items = array();
	$perms_list_items = explode(",", $perms_list);
	
	foreach($perms as $perm) :
		$id = $perm['id'];
		$name = $perm['name'];
		$_SESSION['perms'][$name] = false;
		
		if(in_array($id, $perms_list_items)) {
			$_SESSION['perms'][$name] = true;
		}	
	endforeach;
		
	if(!$_SESSION['perms']['login']) : // if the perm login is not granted the account has been deactivated
		wrong(1);
		sendBack('Your account has beeen de-activated, please contact your site admin.');
		exit;
	endif;
	
	$_SESSION['user_id'] = $results[0]; // set user id	
	$_SESSION['last_ip'] = $results[1]; // set last known ip
	$_SESSION['last_seen'] = $results[2]; // set last time seen.
	$_SESSION['username'] = $username; // set username equal to the username that was used to login
	$_SESSION['name'] = $results[3]; // get the users display name
	$_SESSION['email'] = $results[4]; // users email address
	$_SESSION['group'] = $results[5]; // what ecg-group is the user in
	
	$_SESSION['auth'] = true; // authorise user to access logged in areas
	$_SESSION['wrong'] = 0; // reset wrong counter
	$_SESSION['hack'] = 0; // reset hack atempt count
	
	setcookie("game", $game_input, time()*60*60*24*31, $path); // set the game cookie equal to the game choosen in the login form

	$_SESSION['finger'] = $ses->getFinger(); // find the hash of user agent plus salt

	$ip = getRealIp(); // get users current IP
	$result = $dbl->newUserInfo($ip, $results[0]); // update user to have new time and IP
	
	sendHome(); // return to home page
	
	exit; // We are done with this page so we can end here

} elseif($_POST['lostpw']) { // if this is a lost password first step submit

	$page = 'lostpw';

	if(verifyFormTokenLogin('lostpw', $tokens) == false) // verify token
		ifTokenBad('Lost Password'); // if bad log and send error
	
	$name = cleanvar($_POST['name']);
	$email = cleanvar($_POST['email']);
	
	// check the new email address is a valid email address
	if(!filter_var($email,FILTER_VALIDATE_EMAIL))
		sendBack('That email is not valid');
	
	$verify = $dbl->verifyUser($name, $email);
	if($verify == false) // no user, return error
		sendBack('Either the username or email supplied do not match any known user.');
	else // there is user by that name and email, return the user's id
		$user_id = $verify;
	
	// generate some random string with thier username, email, the current time in micro seconds and their user id
	$rand = $name.$email.uniqid(microtime(), true).$user_id;
	$key = genHash($rand); // hash the random text for a 40 char key
	 
	// key, email, comment, perms, admin_id (in the case of admin_id it will serve as the place to store the connected client_id for the password reset)
	$db_results = $dbl->addEchKey($key, $email, 'PW', 0, $user_id); // create a key for the link that is to be sent
	if(!$db_results) // if no rows affected (return false)
		sendBack('Failure on key creation and storage');
	
	## email user the link ##
	$body = '<html><body>';
	$body .= '<h2>Echelon Lost Password Service</h2>';
	$body .= $config['cosmos']['email_header'];
	$body .= 'This email is about how to reset your password on Echelon. Please do not foward this message on to anyone, this is a private email.
			If you did not request a password reset don\'t worry. You\'re password is still secure and has not been changed. Delete this email if you like.<br /><br />
			
			To reset your password please click on the following link. This link will bring you to a page in order to set a new password. This password reset is for the username: '. $name.'<br />
			<a href="http://'.$_SERVER['SERVER_NAME'].$path.'login.php?t=reset&amp;key='.$key.'&amp;email='.$email.'"> >>>>>>>>Reset your password<<<<<<<<< </a>.<br />';
	$body .= $config['cosmos']['email_footer'];
	$body .= '</body></html>';
	
	// replace %ech_name% in body of email with var from config
	$body = preg_replace('#%ech_name%#', $config['cosmos']['name'], $body);
	// replace %name%
	$body = preg_replace('#%name%#', $name, $body);
	
	$headers = "From: echelon@".$_SERVER['HTTP_HOST']."\r\n";
	$headers .= "Reply-To: ". $config['cosmos']['email'] ."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	// Subject of the email
	$subject = "Echelon Password Reset";
	
	// send email
	if(!mail($email, $subject, $body, $headers))
		sendback('There was a problem sending the email.');
	
	// all good set good message
	set_good('The final instructions have been emailed to you. Please check your inbox.');
	sendLogin(); // return to login page instead of sending back

	exit;

} elseif($_POST['resetpw']) {

	## This section works with the results of the reset password form ##
	
	if(verifyFormTokenLogin('resetpw', $tokens) == false) // verify token
		ifTokenBad('Lost Password'); // if bad log and send error
	
	// get and clean vars
	$pw1 = $_POST['pw2'];
	$pw2 = $_POST['pw1'];
	$key = cleanvar($_POST['key']);
	$email = cleanvar($_POST['email']);
	
	if(!testPW($pass1)) // test for unwanted characters
		sendBack('Your new password contains illegal characters: = \' " or space');
	
	// check both passwords are the same
	if($pw1 != $pw2)
		sendBack('The two passwords do not match');
	
	// check email is valid
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		sendBack('You cannot reset your password without a valid link from an email');
		exit;
	}
	
	// check if the key is valid again
	$result = $dbl->verifyRegKey($key, $email, $key_expire);
	if(!$result) { // if non key
		sendBack('You cannot reset your password without a valid link from an email');
		exit;
	}
	
	// get user's id from key // (Find the perms and admin_id assoc with that unique key)
	// but since we said that we are going to use the admin_id to store the user_id for the terms of password reset
	$result = $dbl->getIdWithKey($key); // admin_id/user_id is return as the second item in an array
	if(!$result) // if false
		sendBack('There is a problem with our records');
	else
		$id_with_key = $result;
		
	
	// generate and reset password in this whole function
	$result = $mem->genAndSetNewPW($pw1, $id_with_key, $min_pw_len);
	if($result != true) { // result is either a boolean (true) or an error string
		sendBack($result);
		exit;
	}
	
	// deactive key to stop multiple use
	$dbl->deactiveKey($key);
	
	set_good('Your password has been changed.');
	sendLogin();
	exit;
	
} elseif($_GET['t'] == 'reset') { // if this is a reset password
	
	// NOTE: This page can only be reached properly by a link in an email,
	//			so the sendBack() function will not work because sendBack() will send the user to the
	//			referer, which is their email client. We must set an error and return to the login page.
	
	// page normal setup
	$page = "resetpw";
	$page_title = "Reset Your Password";
	require 'inc/header.php'; // require the header
	$token = genFormToken('resetpw'); // setup token
	
	// gets vars and check valid
	$key = cleanvar($_GET['key']);
	$email = cleanvar($_GET['email']);
	
	// check email is valid
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		set_error('You cannot reset your password without a valid link from an email');
		sendLogin();
		exit;
	}
	
	$result = $dbl->verifyRegKey($key, $email, $key_expire);
	if(!$result) { // if non key
		set_error('You cannot reset your password without a valid link from an email');
		sendLogin();
		exit;
	}
?>
<fieldset id="lostpw-field">
	<legend>Reset Password</legend>

	<form id="lostpw-form" action="login.php" method="post">

		<p>Please enter your new password.</p>

		<label for="pw1">Password:</label>
			<input type="password" name="pw1" id="pw1" tabindex="1" />
		
		<label for="pw2">Password Again:</label>
			<input type="password" name="pw2" id="pw2" tabindex="2" />

		<input type="hidden" name="token" value="<?php echo $token; ?>" />
		<input type="hidden" name="key" value="<?php echo $key; ?>" />
		<input type="hidden" name="email" value="<?php echo $email; ?>" />

		<input type="submit" name="resetpw" value="Reset Password" />
	</form>

</fieldset>

<?php
	require 'inc/footer.php';
	exit; // no need to continue with this page

} elseif($_GET['t'] == 'lost') { // if this is a lost password page

	$page = "lostpw";
	$page_title= "Lost Password";
	require 'inc/header.php';
	$token_pw = genFormToken('lostpw');
?>

<fieldset id="lostpw-field">
	<legend>Lost Password</legend>

	<form id="lostpw-form" action="login.php" method="post">

		<p>To reset your password please input your username and your email address. An email will be sent to telling you how to finish the steps.</p>

		<label for="name">Username:</label>
			<input type="text" name="name" id="name" tabindex="1" />
		
		<label for="email">Email:</label>
			<input type="text" name="email" id="email" tabindex="2" />

		<input type="hidden" name="token" value="<?php echo $token_pw; ?>" />

		<input type="submit" name="lostpw" value="Recover Password" />
	</form>

</fieldset>

<?php
	require 'inc/footer.php';
	exit; // no need to continue with this page
	
} else { // else if not logged in and not a login request
	$page = "login";
	$page_title = "Login";
	require 'inc/header.php';
?>
<fieldset id="login-field">
	<legend>Login</legend>

	<form id="login-form" action="login.php" method="post">

		<?php
			trys();
			$token = genFormToken('login');
		?>

		 <label for="f-name">Username:</label>
			<input type="text" name="f-name" id="f-name" tabindex="1" /><br />

		 <label for="f-pw">Password:</label>
			<input type="password" name="f-pw" id="f-pw" tabindex="2" />
		
		<?php if($num_games != 0) : ?>
		
		<label for="f-game">Game:</label>
			<select name="f-game" id="f-game" tabindex="3">
				<?php
					$games_list = $dbl->getActiveGamesList();
					$i = 0;
					$count = count($games_list);
					$count--; // minus 1
					while($i <= $count) :
						
						echo '<option value="'. $games_list[$i]['id'] .'">'. $games_list[$i]['name'] .'</option>';
						
						$i++;
					endwhile;
				?>	
			</select>
			
		<?php endif; ?>

		<input type="hidden" name="token" value="<?php echo $token; ?>" />	

		<div class="lower">
			<span class="links-lower"><a href="?t=lost">Lost your password?</a><span class="sep">|</span><a href="register.php" title="register">Register</a></span>

			<input type="submit" value="Login" />
		</div>
	</form>

	<br class="clear" />

</fieldset>
<?php
	require 'inc/footer.php';
} // end if/else of what kind of page this is.
?>