<?php
	error_reporting(E_ALL ^ E_NOTICE); // show all errors but notices
	
	require '../inc/functions.php';
	require '../classes/session-class.php';
	require '../classes/members-class.php';
	
	## fire up the Sessions ##
	$ses = new Session(); // create Session instance
	$ses->sesStart('install_echelon'); // start session (name 'echelon', 0 => session cookie, path is echelon path so no access allowed oustide echelon path is allowed)
	
	if($_GET['t'] == 'install') :

		## find the Echelon directory ##
		$install_dir = $_SERVER['SCRIPT_NAME'];
		$echelon_dir = preg_replace('#install/index.php#', '', $install_dir);
		
		## Create an Echelon salt 
		$ech_salt = genSalt(16);
		$ses_salt = randPass(6);

		## Get the form information ##
		$email = cleanvar($_POST['email']);
		$useMail = cleanvar($_POST['useMail']);
		$db_host = cleanvar($_POST['db-host']);
		$db_user = cleanvar($_POST['db-user']);
		$db_pass = cleanvar($_POST['db-pass']);
		$db_name = cleanvar($_POST['db-name']);
		
		emptyInput($email, 'your email address');
		emptyInput($db_host, 'your email address');
		emptyInput($db_host, 'database hostname');
		emptyInput($db_user, 'database username');
		emptyInput($db_name, 'database name');
		
		// check the new email address is a valid email address
		if(!filter_var($email,FILTER_VALIDATE_EMAIL))
			sendBack('That email is not valid');
			
		if($useMail == 'On')
			$useMail = 'TRUE';
		else
			$useMail = 'FALSE';
				
		## test connection is to the Db works ##
		define("DBL_HOSTNAME", $db_host); // hostname of where the server is located
		define("DBL_USERNAME", $db_user); // username that can connect to that DB
		define("DBL_PASSWORD", $db_pass); // Password for that user
		define("DBL_DB", $db_name); // name of the database to connect to
		define("DB_CON_ERROR_SHOW", TRUE);
		
		if(!function_exists('mysqli_connect')) {
			sendBack('Echelon requires mysqli');
		}
		
		// start connectionn to the DB
		require '../classes/dbl-class.php';
		$dbl = DBL::getInstance(true); // test connection if it fails then it dies (install test is true)
		
		if($dbl->install_error != NULL)
			sendBack($dbl->install_error);
			
		## Read Config ##
		$file_read = 'config.txt';
		$file_write = 'config.php';
		
		if(file_exists($file_read)) :
		
			if(is_readable($file_read)) {

				$fr = fopen($file_read, 'r');
				$fw = fopen($file_write, 'w');
				
				if($fw === false)
					die("Couldn't write to the config file");
				
				while (!feof($fr)) :
				
					## get the line
					$config = fgets($fr, 512); 
					
					## replace anything that needs to be replaced
					$config = preg_replace("/%ech_path%/", $echelon_dir, $config);
					$config = preg_replace("/%ech_salt%/", $ech_salt, $config);
					$config = preg_replace("/%db_host%/", $db_host, $config);
					$config = preg_replace("/%db_user%/", $db_user, $config);
					$config = preg_replace("/%db_pass%/", $db_pass, $config);
					$config = preg_replace("/%db_name%/", $db_name, $config);
					$config = preg_replace("/%ses_salt%/", $ses_salt, $config);
					$config = preg_replace("/%use_mail%/", $useMail, $config);
					fwrite($fw, $config);

				endwhile;
				
				fclose($fr);
				fclose($fw);
				if(!rename($file_write, '../inc/config.php'))
					sendBack('Failed to move config file');
				
			} else {
				die('Config file is not readable or does not exsist');
			}
			
		else:
			die('Config file does not exist');
		endif;
		
		
		## Setup the random information for the original admin user ##
		$user_salt = genSalt(12);
		$user_pw = randPass(10);
		
		$pass_hash = genPw($user_pw, $user_salt);
		
		## Add user to the database
		$result = $dbl->addUser('admin', 'Admin', $email, $pass_hash, $user_salt, 2, 1);
		if(!$result)
			sendBack('Their was a problem adding the admin user to the admin tables, please check the users table exists in your Echelon database');
				//update the admins email address

		$dbl->setSettings($email, 'email', 's');
		
		if($useMail == "TRUE") {
			## Send the admin their email ##
			$body = '<html><body>';
			$body .= '<h2>Echelon Admin User Information</h2>';
			$body .= 'This is the admin user login informtion.<br />';
			$body .= 'Username: <b>admin</b><br />';	
			$body .= 'Password: <b>' . $user_pw . "</b><br />";
			$body .= 'If you have not already, please entirely remove the install folder from Echelon (' . $echelon_dir . '/install/).<br />';
			$body .= 'Thank you for downloading and installing Echelon, <br />';
			$body .= 'The B3 Dev. Team';
			$body .= '</body></html>';
	
			$headers = "From: echelon@" . $_SERVER['HTTP_HOST'] . "\r\n";
			$headers .= "Reply-To: " . $email . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			
			$subject = "Echelon Admin User Setup";
	
			// send email
			if(!mail($email, $subject, $body, $headers))
				sendBack('There was a problem sending the user login information email. Username: admin Password: ' . $user_pw . ' This is the only time you will get you\re password');
			## Done ##
			send('index.php?t=done'); // send to a thank you done page that explains what next
		}
		else {
			## Done ##
			send('index.php?t=done&pw=' . $user_pw); // send to a thank you done page that explains what next
		}
	
	endif; // end install
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Echelon Install Package</title>
	
		<link href="../css/style.css" rel="stylesheet" media="screen" type="text/css" />
		
		<link href="style.css" rel="stylesheet" media="screen" type="text/css" />
	</head>
	
	<body>
		
		<div id="wrap">
		
			<div id="header">
				<h1>Echelon: <small>B3 repository, investigation and control tool</small></h1>
			</div>
			
			<div id="install-content">
			
			<?php if($_GET['t'] == 'done') : ?>
			
				<div id="success">
				
					<div class="msg success"><h2>Echelon is Installed</h2></div>
					
					<div id="ilists">
					
						<div class="ilist">
							<h3>Things that are done</h3>
							<ul>
								<li>The database information you provided was correct</li>
								<li>Your config file was writen</li>
								<?php if(isset($_GET['pw'])) : ?>
								<li><span class="imp">You may now login with the username; <b>admin</b> and the password; <b><?echo $_GET['pw'] ?> </b></span></li>
								<?php else : ?>
								<li>An email was sent, to the email address you supplied, with the user information for your Echelon 'Admin' account</li>
								<?php endif;?>
							</ul>
						</div>
						
						<div class="ilist install-left">
							<h3>What do I do next?</h3>
							<ul>
								<li>You are finished installing Echelon. <span class="imp">Please delete the install directory completely from the Echelon folder.</span> If you did not there are huge security concerns</li>
								<li>Read the Echelon the <a href="http://echelon.bigbrotherbot.net/help/usage/" title="Learn more about how to use Echelon">how to use Echelon guide</a>.</li>
								<li>Once you login to Echelon please go the Settings page to config you Echelon site</li>
								<li><a href="../">ENJOY ECHELON!</a></li>
							</ul>
						</div>

						<br class="clear" />
						
					</div><!-- close #ilists -->
					
					<p><small>Thank-you for installing Echelon, B3 Dev. Team</small></p>
				
				</div>

			<?php else : ?>
			
				<?php errors(); ?>
			
				<form action="index.php?t=install" method="post">
			
					<fieldset>
						<legend>General information</legend>
					
						<div class="float-left">
							
							<label>Your Email:</label><?php tooltip('The email to send the login information for your first Echelon user'); ?>
								<input type="text" name="email" />
							<label>Use mail server:</label><?php tooltip('If this is not clicked echelon will not send any email, but will still use the addresses'); ?>
								<input type="checkbox" class="checkbox" name="useMail" value="On" checked="checked" />
						</div>
					
					</fieldset>
					
					<fieldset>
						<legend>Echelon Database Setup</legend>
					
						<div class="float-left">
						
							<label>Database Host:</label><?php tooltip('The host for the Echelon DB, eg. <strong>localhost</strong> or <strong>mysql.example.com</strong> or <strong>8.8.8.8</strong>'); ?>
								<input type="text" name="db-host" />
								
							<label>Database Username:</label><?php tooltip('Username for the connection; default in setup is <strong>echelon</strong>'); ?>
								<input type="text" name="db-user" value="echelon" />
								
						</div>
						
						<div class="float-left install-left">

							<label>Database Password:</label><?php tooltip('Password for the Echelon database user'); ?>
								<input type="password" name="db-pass" />
								
							<label>Database Name:</label><?php tooltip('Name of the Echelon database, default is <strong>echelon</strong>'); ?>
								<input type="text" name="db-name" value="echelon" />
					
						</div>
						
					</fieldset><!-- end db setup fieldset -->
				
					<input type="submit" name="install" value="Install Echelon" />
				
				</form><!-- close install form -->
				
			<?php endif // close what kind of page ?>
			
			</div><!-- close #content -->
			
			<div class="push"></div>
		
		</div><!-- close #wrap -->
		
		<div id="footer">
			<p class="links">
				<a href="http://echelon.bigbrotherbot.net/help/" title="Get help with Echelon">Echelon Help</a>
				<a href="http://bigbrotherbot.net/forums/forum/" title="Visit the B3 Forums">B3 Forums</a>
			</p>
		</div>
		
	<!-- load jQuery off google CDN -->
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
	
	<!-- Load in some jquery plugins -->
	<script src="../js/jquery.plugins.js" type="text/javascript" charset="utf-8"></script>

	<!-- load main site js -->
	<script src="install.js" type="text/javascript" charset="utf-8"></script>
	
	</body>

</html>

<?php

?>