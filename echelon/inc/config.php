<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'config.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!'); // do not edit

##### Start Editing From below here #####		
## General ##
$path = "/echelon/"; // path to echelon from root of web directory. include starting and trailing /'s (eg. "/echelon/" )
define("PATH", $path);

## Connection info to connect to the database containing the echelon tables
define("DBL_HOSTNAME", "localhost"); // hostname of where the server is located
define("DBL_USERNAME", "root"); // username that can connect to that DB
define("DBL_PASSWORD", ""); // Password for that user
define("DBL_DB", "echelon"); // name of the database to connect to

define("DB_CON_ERROR_SHOW", TRUE); // show DB connection error if any (values: TRUE/FALSE)

#############################
///// IGNORE BELOW HERE /////
## Echelon Version ##
define("ECH_VER", 'v.2.0a');

define("SALT", "D2pPnJhmxRC5"); // do not change this, for this is salt for hashes

$supported_games = array( // supported games
	'q3a' => 'Quake 3 Arena', 
	'cod '=> 'Call of Duty', 
	'cod2' => 'Call of Duty 2', 
	'cod4' => 'Call of Duty 4 MW', 
	'cod5' => 'Call of Duty: World at War', 
	'iourt41' => 'Urban Terror', 
	'wop' => 'World of Padman'
);

// URL to check for updates with
define("VER_CHECK_URL", "http://b3-echelon.com/update/version.txt");