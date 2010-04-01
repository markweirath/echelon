<?php
$auth_name = 'comment';
$b3_conn = true; // this page needs to connect to the B3 database
require '../../inc.php';

if($_POST['comment-sub']) : // if the form is submitted

	## check that the sent form token is corret
	if(verifyFormToken('comment', $tokens) == false) // verify token
		ifTokenBad('Add comment');
	
	// Gets vars from form
	$cid = cleanvar($_POST['cid']);
	$comment = cleanvar($_POST['comment']);
	
	// set common vars	
	$type = 'Comment';
	$time_add = time();
	$user_id = $_SESSION['user_id'];

	## Query ##
	$result = $dbl->addEchLog($type, $comment, $cid, $user_id);
	if($result)
		sendGood('Comment added');
	else
		sendBack('There is a problem, your comment was not added to the database');
		
else :

	set_error('Please do not call that page directly');
	sendHome();

endif;