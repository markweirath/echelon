<?php
$auth_name = 'comment';
$b3_conn = true; // this page needs to connect to the B3 database
require '../../inc.php';


if($_POST['comment-sub']) : // if the form is submitted

	## check that the sent form token is corret
	if(verifyFormToken('comment', $tokens) == false) // verify token
		ifTokenBad('Add comment');

	$comment = cleanvar($_POST['comment']);
	$limit_level = cleanvar($_POST['limit']);

else :

	set_error('Please do not call that page directly');
	sendHome();

endif;