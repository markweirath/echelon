<?php
$auth_name = 'login';
require '../inc.php';

$ses->logout(); // logut user

send('../login.php'); // send back to login back