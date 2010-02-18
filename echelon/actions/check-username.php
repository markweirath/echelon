<?php
require '../inc/functions.php';
require '../inc/config.php';
require '../classes/dbl-class.php'; // require sessions class

// set and clean var
$name = cleanvar($_POST['username']);

// if name is empty return nothing
if(empty($name)) {
	echo '';
	exit;
}

$dbl = new DBl(); // create DBL (echelon DB link)

$result = $dbl->checkUsername($name); // check to see if the name is already in use // return bool
if($result)
	echo 'yes';
else
	echo 'no';

exit;