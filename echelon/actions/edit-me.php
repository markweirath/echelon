<?php
$auth_name = 'login';
require '../inc.php';

$user_id = $_SESSION['user_id']; // find out what client this request is for

// set vars
$display = $_POST['name'];
$email = $_POST['email'];
$cur_pw = $_POST['password'];

$change_pw = $_POST['change-pw'];
if($change_pw == 'on') { // check to see if the password is to be changed
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	if($pass1 != $pass2) { // if the passwords don't match send them back
		sendBack('The supplied passwords to do match');
	}
	emptyInput($pass1, 'your new password');
	$is_change_pw = true; // this is a change password request aswell
} else {
	$is_change_pw = false;
}

// check for empty inputs
emptyInput($display, 'display name');
emptyInput($email, 'email');
emptyInput($cur_pw, 'your current password');

// trim and strip_tags is applied to vars
$display = cleanvar($display);
$email = cleanvar($email);
$cur_pw = cleanvar($cur_pw);
if($is_change_pw) // only if its a chnage pw request is this needed
	$pass1 = cleanvar($pass1); // only need to do pass1 because pass2 is only for intial comparison

// check the new email address is a valid email address
if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {  
	sendBack('That email is not valid');
}

// check to see by comparing to session vars if the display name and email have been changed
if($display != $_SESSION['name'] || $email != $_SESSION['email']) // sent display name does not match session and same with email
	$is_change_display_email = true; // this is a change request
else 
	$is_change_display_email = false; // this is not a change request

// if display/email not changed and its not a change pw request then return
if($is_change_display_email == false && $is_change_pw == false) 
	sendBack('You didn\'t change anything, so Echelon has done nothing');


## Query Section ##
// Check to see if this person is real
$salt = $dbl->getUserSaltById($user_id);
$hash_pw = genPW($cur_pw, $salt); // hash the inputted pw with the returned salt

// Check to see that the supplied password is correct
$validate = $dbl->validateUserRequest($user_id, $hash_pw);
if($validate == false) // if false
	sendBack('You have supplied an incorrect current password');

if($is_change_display_email) { // if the display or email have been altered edit them if not skip this section
	// update display name and email
	$results = $dbl->editMe($display, $email, $user_id);
	if($results == false) { // if false (if nothing happened)
		sendBack('There was an error updating your email and display name');
	} else { // its been changed so we must update the session vars
		$_SESSION['email'] = $email;
		$_SESSION['name'] = $display;
	}
}

if($is_change_pw) { // if a change pw request
	genAndSetNewPW($pass1, $user_id); // function to generate and set a new password
}

sendGood('Your user information has been successfully updated');
exit;