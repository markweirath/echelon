<?php 
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );
require_once('Connections/b3connect.php');

$currentPage = $_SERVER["PHP_SELF"];

$AserverBanInfos = array();
foreach($config['servers'] as $serverConfig) {
  if ($serverConfig['include_in_banlist']!==1) continue;
  array_push( $AserverBanInfos, new ServerBanInfo($serverConfig) );
}


$nbBan = 0;
foreach ($AserverBanInfos as $serverBanInfo) $nbBan += $serverBanInfo->banCount;

header("Content-type: text/plain");
header("Cache-Control: no-store, no-cache");
echo "\n";
echo "//------------------------------------------------------\n";
echo "//            " . $config['clanname'] . " BAN LIST\n";
echo "//------------------------------------------------------\n";
echo "// IP count :  " . $nbBan . " permanent ban\n";
echo "// Generated :   " . date(DATE_ATOM, time()) . "\n";
echo "//------------------------------------------------------\n";
echo "//\n";
echo "// Cut & paste into your banlist.txt or save it to use it directly. \n";
echo "// The -1 places the ban forever.\n";
echo "// The 0 in IPs prevents IP changing : the ban is made for all the IP mask ( from 0 to 255 ).\n";
echo "// You need to set g_filterban to 1 on your server ( add g_filterban 1 to your cfg )\n";
echo "//\n";
echo "//------------------------------------------------------\n";
echo "\n";
foreach ($AserverBanInfos as $serverBanInfo) $serverBanInfo->writeBans();
 


class ServerBanInfo {
  
  private $serverConfig;
  private $Aban;
  
  public $banCount;
  
  function __construct($serverConfig) {
    $this->serverConfig = $serverConfig;
    $this->Aban = $this->getBanFromBDD();
    $this->banCount = sizeof($this->Aban);
  }
  
  private function getBanFromBDD() {
    global $database_b3connect, $b3connect;
    loadGameConfig($this->serverConfig);
    
    mysql_select_db($database_b3connect, $b3connect);
    $query_rs_activebans = sprintf("SELECT penalties.id, penalties.type, penalties.time_add, penalties.time_expire, penalties.reason, penalties.inactive, penalties.duration, penalties.admin_id, target.id as target_id, target.name as target_name, target.ip as target_ip FROM penalties, clients as target WHERE penalties.type = 'Ban' AND inactive = 0 AND penalties.client_id = target.id AND ( penalties.time_expire = -1) ORDER BY penalties.id DESC");

    $rs_activebans = mysql_query($query_rs_activebans, $b3connect) or die(mysql_error());

    $Aban = array();
    while ($row = mysql_fetch_assoc($rs_activebans)) array_push($Aban, $row);
    mysql_free_result($rs_activebans);
    return $Aban;
  }
  
  public function writeBans() {
    echo "\n";  
    echo "//  permanent bans from " . $this->serverConfig['name'] . " \n";
    foreach ($this->Aban as $ban) {
      $text = preg_replace('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.\d{1,3}/','$1.$2.$3.0',$ban['target_ip']) . ':-1   // ';
      $text .= $ban['target_name'];
      $text .= '    banned on  ' . date('d/m/Y (H:i)',$ban['time_add']);
      $text .= ', reason : ' . $ban['reason'];
      echo $text . "\n";
    }
  }  

}
?>

