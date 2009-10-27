<?php
//include "../ctracker.php";
//require_once('../Connections/inc_config.php');

function rcon ($command) {

  global $rcon_ip;
  global $rcon_port;
  global $rcon_pass;

	$fp = fsockopen("udp://$rcon_ip",$rcon_port, $errno, $errstr, 2);
	socket_set_timeout($fp,2);

	if (!$fp)	{
		echo "$errstr ($errno)<br>\n";
	} else {
		$query = "\xFF\xFF\xFF\xFFrcon \"" . $rcon_pass . "\" " . $command;
		fwrite($fp,$query);
	}
	$data = '';
	while ($d = fread ($fp, 10000)) {
	    $data .= $d;
	}
	fclose ($fp);
	$data = preg_replace ("/....print\n/", "", $data);
//	$data = stripcolors ($data);
	return $data;
}
?>
