<?php
$auth_name = 'add_user'; // this page is add user, so that if you can add keys you should be able to remove them
require '../inc.php';

if($_POST['t'] == 'del') : // if this is a deletion request

	$key = cleanvar($_POST['key']);

	if(verifyFormToken('keydel'.$key, $tokens) == false) // verify token
		ifTokenBad('Reg Key Delete'); // if bad token, log and send error

	$result = $dbl->delKey($key);
	if($result)
		sendGood('Registration key deleted');
	else
		sendBack('There was an problem completeing your request');
		
	exit; // no need continuing with this script

elseif($_POST['comment']) : // if this is an edit comment request

	$key = cleanvar($_POST['key']);
	$comment = cleanvar($_POST['comment']);
	
	emptyInput($comment, 'comment');
	
	$result = $dbl->editKeyComment($key, $comment, $mem->id);
	// this is an ajax request, so we need to echo error/success messages
	if($result)
		echo 'yes';
	else
		echo 'no';
	exit; // no need to continue

else : // if form not submitted
	set_error('Please do not load that page directly, thank you.');
	sendHome();
endif;