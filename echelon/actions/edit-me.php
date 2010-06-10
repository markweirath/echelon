<?php
$auth_name = 'login';
require '../inc.php';

// set vars
$display = cleanvar($_POST['name']);
$email = cleanvar($_POST['email']);
$cur_pw = cleanvar($_POST['password']);
$change_pw = cleanvar($_POST['change-pw']);

if($change_pw == 'on') { // check to see if the password is to be changed
	$pass1 = cleanvar($_POST['pass1']);
	$pass2 = $_POST['pass2'];
	if($pass1 != $pass2) // if the passwords don't match send them back
		sendBack('The supplied passwords to do match');

	emptyInput($pass1, 'your new password');
	$is_change_pw = true; // this is a change password request aswell
	
} else
	$is_change_pw = false;

// check for empty inputs
emptyInput($display, 'display name');
emptyInput($email, 'email');
emptyInput($cur_pw, 'your current password');

// check the new email address is a valid email address
if(!filter_var($email,FILTER_VALIDATE_EMAIL))
	sendBack('That email is not valid');

// check to see by comparing to session vars if the display name and email have been changed
if($display != $mem->name || $email != $mem->email) // sent display name does not match session and same with email
	$is_change_display_email = true; // this is a change request
else 
	$is_change_display_email = false; // this is not a change request

// if display/email not changed and its not a change pw request then return
if( (!$is_change_display_email) && (!$is_change_pw) ) 
	sendBack('You didn\'t change anything, so Echelon has done nothing');

## Query Section ##
$mem->reAuthUser($cur_pw, $dbl); // check user current password is correct

if($is_change_display_email) : // if the display or email have been altered edit them if not skip this section
	// update display name and email
	$results = $dbl->editMe($display, $email, $mem->id);
	if(!$results) { // if false (if nothing happened)
		sendBack('There was an error updating your email and display name');
	} else { // its been changed so we must update the session vars
		$_SESSION['email'] = $email;
		$_SESSION['name'] = $display;
		$mem->setName($display);
		$mem->setEmail($email);
	}
endif;

if($is_change_pw) // if a change pw request
	$mem->genAndSetNewPW($pass1, $mem->id, $dbl); // function to generate and set a new password

sendGood('Your user information has been successfully updated');
exit;