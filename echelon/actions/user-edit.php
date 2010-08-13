<?php
$auth_name = 'edit_user';
require '../inc.php';

if($_POST['t'] == 'del') : // delete user

	## get and clean vars ##
	$token = cleanvar($_POST['token']);
	$id = cleanvar($_POST['id']);
	
	## check numeric id ##
	if(!is_numeric($id))
		sendBack('Invalid data sent, request aborted');
		
	# verify token #
	if(!verifyFormToken('del'.$id, $tokens))
		ifTokenBad('Delete Echelon User');

	$result = $dbl->delUser($id);
	if($result)
		sendGood('User has been deleted');
	else
		sendBack('There is a problem. The user has not been deleted');
		
	exit;

elseif($_POST['ad-edit-user']): // admin edit user
	
	## get and clean vars ##
	$username = cleanvar($_POST['username']);
	$display = cleanvar($_POST['display']);
	$email = cleanvar($_POST['email']);
	$group = cleanvar($_POST['group']);
	$id = cleanvar($_POST['id']);
	
	## check numeric id ##
	if(!is_numeric($id))
		sendBack('Invalid data sent, request aborted');
		
	# verify token #
	if(!verifyFormToken('adedituser', $tokens))
		ifTokenBad('Edit Echelon User');	
	
	$result = $dbl->editUser($id, $username, $display, $email, $group);
	if($result)
		sendGood($display."'s information has been updated");
	else
		sendBack('There is a problem. The user information has not been changed');

	exit;
	
else :
	set_error('You cannot view this page directly');
	send('sa.php');

endif;