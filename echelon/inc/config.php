<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'config.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!'); // do not edit

##### Start Editing From below here #####		
## General ##
global $path; // ignore line
$path = "/echelon/"; // path to echelon from root of web directory. include starting and trailing /'s (eg. "/echelon/" )


## Encryption Key
// you can generate a strong one at a site like http://www.goodpassword.com/
// It should be long and random
define("EN_KEY", "<$o2!%<.b5A[5$;/o>C911J(");


## Connection info to connect to the database containing the echelon tables 
// (where you installed the SQL script that came with the install
define("DBL_HOSTNAME", "internal-db.s67256.gridserver.com"); // hostname of where the server is located
define("DBL_USERNAME", "db67256_echelon"); // username that can connect to that DB
define("DBL_PASSWORD", "10rabbiT01"); // Password for that user
define("DBL_DB", "db67256_ech"); // name of the database to connect to