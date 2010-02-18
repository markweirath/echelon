<?php
$auth_name = 'edit_user';
require '../inc.php';

if($_POST['t'] == 'del') :

	## get and clean vars ##
	$token = cleanvar($_POST['token']);
	$id = cleanvar($_POST['id']);
	
	## check numeric id ##
	if(!is_numeric($id))
		sendBack('Invalid data sent, request aborted');
		
	# verify token #
	if(verifyFormToken('del'.$id, $tokens) == false)
		ifTokenBad('Delete User');

	$dbl = new DBL();

	$result = $dbl->delUser($id);
	if($result)
		sendGood('User has been deleted');
	else
		sendBack('There is a problem. The user has not been deleted');
	exit;

endif;