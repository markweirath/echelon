<?php
// Cracker Tracker Protection System
// Created by: Christian Knerr - www.cback.de
// phpBB Users: Please use our complete phpBB2 Mod!
// Version: 2.0.0
//
// License: GPL
//
//
// Begin CrackerTracker  StandAlone

function implode_r($glue, $array, $array_name = NULL) {
	$return = array();
	while(list($key,$value) = @each($array)) {
		if(is_array($value)) {
			$return[] = implode_r($glue, $value, (string) $key);
		} else {
			if($array_name != NULL)
				$return[] = $array_name."[".(string) $key."]=".$value."\n";
			else
				$return[] = $key."=".$value."\n";
		}
	}
	return(implode($glue, $return));
}

//
// Check also POST data
$postTrack = implode_r("", $_POST);

$cracktrack = $_SERVER['QUERY_STRING'];

$wormprotector = array(
	'chr(', 'chr=', 'chr%20', '%20chr', 'wget%20', '%20wget', 'wget(',
	'cmd=', '%20cmd', 'cmd%20', 'rush=', '%20rush', 'rush%20',
	'union%20', '%20union', 'union(', 'union=', 'echr(', '%20echr', 'echr%20', 'echr=',
	'esystem(', 'esystem%20', 'cp%20', '%20cp', 'cp(', 'mdir%20', '%20mdir', 'mdir(',
	'mcd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', '%20rm',
	'mcd(', 'mrd(', 'rm(', 'mcd=', 'mrd=', 'mv%20', 'rmdir%20', 'mv(', 'rmdir(',
	'chmod(', 'chmod%20', '%20chmod', 'chmod(', 'chmod=', 'chown%20', 'chgrp%20', 'chown(', 'chgrp(',
	'locate%20', 'grep%20', 'locate(', 'grep(', 'diff%20', 'kill%20', 'kill(', 'killall',
	'passwd%20', '%20passwd', 'passwd(', 'telnet%20', 'vi(', 'vi%20',
	'insert%20into', 'select%20', 'nigga(', '%20nigga', 'nigga%20', 'fopen', 'fwrite', '%20like', 'like%20',
	'$_request', '$_get', '$request', '$get', '.system', 'HTTP_PHP', '&aim', '%20getenv', 'getenv%20',
	'new_password', '&icq','/etc/passwd','/etc/shadow', '/etc/groups', '/etc/gshadow',
	'HTTP_USER_AGENT', 'HTTP_HOST', '/bin/ps', 'wget%20', 'uname\x20-a', '/usr/bin/id',
	'/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/chown', '/usr/bin', 'g\+\+', 'bin/python',
	'bin/tclsh', 'bin/nasm', 'perl%20', 'traceroute%20', 'ping%20', '.pl', '/usr/X11R6/bin/xterm', 'lsof%20',
	'/bin/mail', '.conf', 'motd%20', 'HTTP/1.', '.inc.php', 'config.php', 'cgi-', '.eml',
	'file\://', 'window.open', '<SCRIPT>', 'javascript\://','img src', 'img%20src','.jsp','ftp.exe',
	'xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', 'nc.exe', '.htpasswd',
	'servlet', '/etc/passwd', 'wwwacl', '~root', '~ftp', '.js', '.jsp', 'admin_', '.history',
	'bash_history', '.bash_history', '~nobody', 'server-info', 'server-status', 'reboot%20', 'halt%20',
	'powerdown%20', '/home/ftp', '/home/www', 'secure_site, ok', 'chunked', 'org.apache', '/servlet/con',
	'<script', '/robot.txt' ,'/perl' ,'mod_gzip_status', 'db_mysql.inc', '.inc', 'select%20from',
	'select from', 'drop%20', '.system', 'getenv', 'http_', '_php', 'php_', 'phpinfo()', '\<?php', '?\>', 'sql=',
	'div style=', 'overflow: auto', 'height: 1px', 'cc%20', 'admin_action=', 'path=', 'action=http',
	'page=http', 'module=http', 'op=http', 'id=http', 'action%3Dhttp', 'page%3Dhttp', 'module%3Dhttp',
	'op%3Dhttp', 'starhack', '../../', 'directory=http', 'dir=http', 'busca', 'uol.com'
);

// Block these words found in POST requests
$postBlacklist = array(
	'div style=', 'overflow: auto', 'height: 1px', 'display: hidden', // Against spam-hiding jackasses
);

// Check against the whole list
$checkworm = str_replace($wormprotector, '*', $cracktrack);

// If it differs to original then blog the attempt
if ($checkworm != $cracktrack)
{
	$cremotead = $_SERVER['REMOTE_ADDR'];
	$cuseragent = $_SERVER['HTTP_USER_AGENT'];

	$mail = "Attack detected:

Remote-IP: ".$cremotead."
User-Agent: ".$cuseragent."
Request-string: ".$cracktrack."
Filtered string: ".$checkworm."
Server: ".$_SERVER['SERVER_NAME']."
Translated: ".$_SERVER['PATH_TRANSLATED']."
Referrer: ".$_SERVER['HTTP_REFERRER']."
";
	mail(EMAIL, "CTracker: Attack detected!", $mail, "From: ctracker@".htmlentities($_SERVER['HTTP_HOST']));
	die('Hack Attempt Foiled!!!!!');
}

// Check POST data here
$checkworm = str_replace($wormprotector, '*', $postTrack);

if ($checkworm != $postTrack)
{
	$cremotead = $_SERVER['REMOTE_ADDR'];
	$cuseragent = $_SERVER['HTTP_USER_AGENT'];

	$mail = "POST-Attack detected:

Remote-IP: ".$cremotead."
User-Agent: ".$cuseragent."
Request-string: ".$postTrack."
Filtered string: ".$checkworm."
Server: ".$_SERVER['SERVER_NAME']."
Translated: ".$_SERVER['PATH_TRANSLATED']."
";
	mail(EMAIL, "CTracker: Attack detected!", $mail, "From: ctracker@".htmlentities($_SERVER['HTTP_HOST']));
	$checkPOST = str_replace($postBlacklist, '*', $postTrack);
	if ($checkPOST != $postTrack) {
		// Block attempt
		die('Hack Attempt Foiled!!!!');
	}
}

//
// End CrackerTracker StandAlone
//