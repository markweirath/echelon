<?php 
error_reporting(E_ALL);

include "ctracker.php";

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;

require_once('Connections/b3connect.php');
require_once('login/inc_authorize.php');
require_once('lib/JSON.php');

// next array defines what action exists in that script
$authorised_actions = array(
  'searchPlayerByName',
  'getLastChat',
  'talkback',
); 


$json_service = new Services_JSON();
$action = getPostOrGet('action');
if (in_array($action, $authorised_actions)) call_user_func($action); 


// helper functions
function getPostOrGet($var) {
  if (isset($_POST[$var])) return (get_magic_quotes_gpc()) ? $_POST[$var] : addslashes($_POST[$var]);
  elseif (isset($_GET[$var])) return (get_magic_quotes_gpc()) ? $_GET[$var] : addslashes($_GET[$var]);
  else return null;
}

function jsonResponse($data) {
  global $json_service;
  header('Content-type: text/json');
  echo $json_service->encode($data);
  exit;
}

/////////////////////// define your actions below ///////////////////////

/**
 * Suited for jquery.autocomple plugin
*/
function searchPlayerByName() {
  global $database_b3connect, $b3connect;

  $search = getPostOrGet('q');
  if (is_null($search)) exit;
  else {
    mysql_select_db($database_b3connect, $b3connect);
    $query_rs_clients = sprintf("SELECT name FROM clients WHERE UPPER(name) like '%%%s%%' ORDER BY name LIMIT 0, 20", strtoupper($search));
    $rs_clients = mysql_query($query_rs_clients, $b3connect) or die(mysql_error());
    $totalRows_rs_clientsearch = mysql_num_rows($rs_clients);
    if ($totalRows_rs_clientsearch == 0) {
      // try to find result with SOUNDEX
      $query_rs_clients = sprintf("SELECT name FROM clients WHERE SOUNDEX(name) = SOUNDEX('%s') ORDER BY name LIMIT 0, 20", $search);
      $rs_clients = mysql_query($query_rs_clients, $b3connect) or die(mysql_error());
    }
    $totalRows_rs_clientsearch = mysql_num_rows($rs_clients);
    if ($totalRows_rs_clientsearch == 0) {
      // try to find result with  a more flexible SOUNDEX
      $query_rs_clients = sprintf("SELECT name FROM `clients` WHERE SUBSTRING(SOUNDEX(name),2) = SUBSTRING(SOUNDEX('%s'),2) ORDER BY name LIMIT 0, 20", $search);
      $rs_clients = mysql_query($query_rs_clients, $b3connect) or die(mysql_error());
    }    
    
    header('Content-type: application/x-json');
    while ($row_rs_client = mysql_fetch_assoc($rs_clients)) printf("%s|%s\n", $row_rs_client['name'],htmlentities($row_rs_client['name']));
    //echo "<pre>"; print_r($row_rs_clients); echo "</pre>"; exit;
    //jsonResponse($row_rs_clients);
  }
}


function getLastChat() {
  global $database_b3connect, $b3connect;

  $lastPostId = getPostOrGet('lastId');
  if (is_null($lastPostId)) exit;
  
  mysql_select_db($database_b3connect, $b3connect);
  
  $query = sprintf("SELECT * FROM chatlog WHERE id > %s ORDER BY id DESC LIMIT 10", $lastPostId);
  $rs = mysql_query($query, $b3connect) or die(mysql_error());
  $totalRows = mysql_num_rows($rs);
  
  header('Content-type: text/json');
  $Amsg = array();
  while ($row = mysql_fetch_assoc($rs)) {
    array_push($Amsg, $row);
  }
  jsonResponse($Amsg);
}
  
  
function talkback() {
  $game = getPostOrGet('game');
  if (is_null($game)) {
    header("HTTP/1.1 400 Bad Request");
    echo "game undefined";
    exit();
  }
  
  global $config;
  //echo print_r($config["servers"][$game], true); exit;
  loadGameConfig($config["servers"][$game]);

  global $rcon_ip, $rcon_port, $rcon_pass;
  //echo "$rcon_ip, $rcon_port, $rcon_pass"; exit;
  
  $talkback = getPostOrGet('talkback');
  if (is_null($talkback)) {
    header("HTTP/1.1 400 Bad Request");
    echo "talkback undefined";
    exit();
  }
  
  if ("".$talkback != "") {
    require_once('admin/rcon.php');
    $command = "say ^4[B3](^3" . $_SESSION['xlradmin'] . "^4): ^3" . $talkback;
    //echo "command: " . $command;
    header('Content-type: text/plain');
    echo rcon($command)."\n";
  } else {
    header("HTTP/1.1 400 Bad Request");
    echo "talkback message is empty";
    exit();
  }
}
?>