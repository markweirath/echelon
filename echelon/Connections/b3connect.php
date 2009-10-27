<?php 

include('inc_config.php');

if (isset($_GET['game']))	{
  $game = (get_magic_quotes_gpc()) ? $_GET['game'] : addslashes($_GET['game']);
} elseif (isset($_POST['game'])) {
  $game = (get_magic_quotes_gpc()) ? $_POST['game'] : addslashes($_POST['game']);
} else $game = 1;

$numservers = $config["numservers"];
$xlrdatabase = ($config["numservers"] > 1) ? "multi" : "single";

for ($i=1; $i<=$config['numservers']; $i++) $gamename[$i] = $config["servers"][$i]["name"];

if (array_key_exists($game, $config["servers"])) {
  loadGameConfig($config["servers"][$game]);
} else {
  $hostname_b3connect = "";
  $database_b3connect = "";
  $username_b3connect = "";
  $password_b3connect = "";
  $b3connect = null;
  $PBactive = "";
  $rcon_ip = "";
  $rcon_port = "";
  $rcon_pass = "";
}

  
function loadGameConfig ( $gameConfig ) {
  global $hostname_b3connect, $database_b3connect, $username_b3connect, $password_b3connect, $b3connect, $PBactive, $rcon_ip, $rcon_port, $rcon_pass, $chatlogger_plugin_activated ;
  $hostname_b3connect = $gameConfig["hostname"];
  $database_b3connect = $gameConfig["database"];
  $username_b3connect = $gameConfig["username"];
  $password_b3connect = $gameConfig["password"];
  $b3connect = mysql_pconnect($hostname_b3connect, $username_b3connect, $password_b3connect) or die(mysql_error());
  $PBactive = $gameConfig["PBactive"];
  $rcon_ip = $gameConfig["rcon_ip"];
  $rcon_port = $gameConfig["rcon_port"];
  $rcon_pass = $gameConfig["rcon_pass"];
  $chatlogger_plugin_activated = ($gameConfig["chatlogger_activated"] == 1);
}


?>
