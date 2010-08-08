<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

/**
 * class chatlogs
 * desc: File to deal with Echelon plugin Chatlogger
 *
 */ 

class chatlogs extends plugins {

	public static $instance;
	public $name;
	
	private function getClass() {
		$name =	get_class($this);
		$this->name = $name;
		return $name;
	}
	
	/**
	 *	You may edit below here
	 */
	
	private static $tables = 'chatlog,chatlog_164,chatlog_165';
	
	/**
	 * Gets the current instance of the class, there can only be one instance (this make the class a singleton class)
	 * note: this is needed as a work around for the inc.php file do not change
	 * 
	 * @return object $instance - the current instance of the class
	 */
	public static function getInstance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
	
	public function __construct() {
		parent::__construct($this->getClass()); // call the parent constructor
	
		parent::setTitle('Chatlogger');
		parent::setVersion(1.0);
	}
	
	public function __destruct() {
		parent::__destruct();
	}
	
	/**
	 * Main Function - DO NOT REMOVE any of the below functions
	 */
	public static function returnSettings() {}
	public static function returnClientFormTab() {}
	public static function returnClientForm($cid) {}
	public static function returnClientBio() {}
	public static function returnCSS() {}
	public static function returnJS() {}
	
	/**
	 * You may edit below here
	 */
	
	public static function getTables() {
		return self::$tables;
	}
	
	/**
	 * Returns a list of chatlogs of the client
	 *
	 * @param int $cid - the client id of the user that we need the logs for
	 */
	public static function returnClientLogs($cid) {
	
		$tables_info = self::$tables; // get the table information for the chatlogs queries
		
		global $tformat; // get the time format for use in the logs
	
		include 'chatlogs-cd.php'; // include the file
		
	}
	
	/**
	 * Returns the link to the needed in the nav for the full chatlogs page
	 */
	public static function returnNav() {
	
		global $mem; // get pointer to the members class
		
		if($mem->reqLevel('chatlogs')) :
		
			global $page; // bring in the current page var from main Echelon
			
			if($page == 'chat')
				$data = '<li class="n-chat selected">';
			else
				$data = '<li class="n-chat">';
			
			$data .= '<a href="'. PATH .'plugin.php?pl='.__CLASS__.'" title="Chatlogs from the server(s)">Chat Logs</a></li>';
		
			return $data;
		
		else:
			return NULL;
		
		endif;
	
	}
	

} // end class