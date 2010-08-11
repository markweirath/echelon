<?php
error_reporting(E_ALL ^ E_NOTICE); // show all errors but notices
$page = 'banlist';

require_once 'inc/ctracker.php'; // anti worm injection protection
require_once 'inc/config.php'; // load the config file

if(INSTALLED != 'yes') // if echelon is not install (a constant is added to the end of the cnfig during install) then die and tell the user to go  install Echelon
	die('You still need to install Echelon. <a href="install/index.php">Install</a>');

require 'classes/dbl-class.php'; // class to preform all DB related actions
$dbl = DBL::getInstance(); // start connection to the local Echelon DB

$games_list = $dbl->gamesBanlist();

$num_games = $games_list['num_rows'];

if($num_games > 0) :

	require 'classes/mysql-class.php'; // class to preform all B3 DB related actions
	
	header("Content-type: text/plain");
	header("Cache-Control: no-store, no-cache");
	echo "\n";
	echo "//------------------------------------------------------\n";
	echo "//            BAN LIST\n";
	echo "//------------------------------------------------------\n";
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
		
	foreach($games_list['data'] as $game) :

		$db = new DB_B3($game['db_host'], $game['db_user'], $game['db_pw'], $game['db_name'], true);
		
		$banInfo = new gameBanInfo($game['name']);
		
	endforeach;
	
endif;

class gameBanInfo {
  
	private $game_name;
	private $banCount = 0;
  
	public function __construct($game_name) {
		$this->game_name = $game_name; // log the game name
		
		$results = $this->getBanFromDB(); // get the data from the db
		
		$this->banCount = $results['num_rows']; // find out how many bans there are
		
		$this->writeBans($results['data']); // spit out the results
	}
	
	public function destruct() {
		$this->banCount = 0;
	}
	
	private function getBanCount() {
		return $this->banCount;
	}
  
	private function getBanFromDB() {
		global $db;

		$sql = "SELECT p.id, p.type, p.time_add, p.reason, p.inactive, c.name, c.ip FROM penalties p, clients c WHERE p.type = 'Ban' AND inactive = 0 AND p.client_id = c.id AND p.time_expire = -1 AND c.ip != '' ORDER BY p.id DESC";
		$results = $db->query($sql);
		return $results;
	}
  
	public function writeBans($data) {
		global $config;

		echo "\n"; 
		echo "\n";
		echo "---------------------------------------------------------------\n";
		echo "// Permanent bans from " . $this->game_name . " \n";
		echo "// There are ". $this->getBanCount() ." permanent bans for this game \n";
		foreach ($data as $ban) :
			$text = preg_replace('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.\d{1,3}/','$1.$2.$3.0',$ban['ip']) . ':-1   // ';
			$text .= $ban['name'];
			$text .= '    banned on  ' . date('d/m/Y (H:i)',$ban['time_add']);
			$text .= ', reason : ' . $ban['reason'];
			echo $text . "\n";
		endforeach;
	}  

}
