<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'config.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!'); // do not edit

##### Start Editing From below here #####		
## General ##
global $path; // ignore line
$path = "/echelon/"; // path to echelon from root of web directory. include starting and trailing /'s (eg. "/echelon/" )
define("PATH", "/echelon/");

## Echelon Version ##
define("ECH_VER", 'v.2.0a');

## Encryption Key
// you can generate a strong one at a site like http://www.goodpassword.com/
// It should be long and random
define("EN_KEY", "XXXXXXXXXX");


## Connection info to connect to the database containing the echelon tables 
// (where you installed the SQL script that came with the install
define("DBL_HOSTNAME", "localhost"); // hostname of where the server is located
define("DBL_USERNAME", "root"); // username that can connect to that DB
define("DBL_PASSWORD", ""); // Password for that user
define("DBL_DB", "echelon"); // name of the database to connect to


///// IGNORE BELOW HERE /////
$supported_games = array(
	'q3a' => 'Quake 3 Arena', 
	'cod '=> 'Call of Duty', 
	'cod2' => 'Call of Duty 2', 
	'cod4' => 'Call of Duty 4 MW', 
	'cod5' => 'Call of Duty: World at War', 
	'iourt41' => 'Urban Terror', 
	'wop' => 'World of Padman'
);