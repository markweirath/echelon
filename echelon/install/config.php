<?php
if (!empty($_SERVER["SCRIPT_FILENAME"]) && "config.php" == basename($_SERVER["SCRIPT_FILENAME"]))
		die ("Please do not load this page directly. Thanks!"); // do not edit

##### Start Editing From below here #####
define("DB_CON_ERROR_SHOW", TRUE); // show DB connection error if any (values: TRUE/FALSE)
define("GRAVATAR", TRUE); // show gravatars image in header (values: TRUE/FALSE)
define("DB_B3_ERROR_ON", TRUE); // show detailed error messages on B3 DB query failure (values TRUE/FALSE)
$ech_log_path = getenv("DOCUMENT_ROOT").PATH."lib/log.txt";
define("ECH_LOG", $ech_log_path); // location of the Echelon Log file
unset($ech_log_path);

