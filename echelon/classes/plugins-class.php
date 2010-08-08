<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'plugins-class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

/**
 * class plugin
 * desc: File to deal with Echelon plugins
 *
 */ 

class plugins {

	public $name; // name of the plugin
	public $title; // pretty version of the name of the plugin
	private $version = 1.0;
	
	public static $plugins_class = NULL;
	
	function __construct($name) {
		$this->name = $name;
	}
	
	public function __destruct() {
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function setVersion($version) {
		$this->version = $version;
	}
	
	public static function setPluginsClass($value) {
		self::$plugins_class = $value;
	}
	
	/**
	 * This function display information from plugins in the CD's bio area
	 *
	 * @param array $plugins_class - an array of pointers to the class of the plugins
	 */
	function displayCDBio() {
		
		foreach(self::$plugins_class  as $plugin) :
			$content = $plugin::returnClientBio();
			echo $content;
		endforeach;
	}
	
	/**
	 * This function display the tab of any plugin added forms on the clientdetails page
	 *
	 * @param array $plugins_class - an array of pointers to the class of the plugins
	 */
	function displayCDFormTab() {
	
		foreach(self::$plugins_class as $plugin) :
			$content = $plugin::returnClientFormTab();
			echo $content;
		endforeach;
	}
	
	/**
	 * This function display forms on the clientdetails page added by any plugins
	 *
	 * @param array $plugins_class - an array of pointers to the class of the plugins
	 */
	function displayCDForm($cid = 0) {
	
		foreach(self::$plugins_class as $plugin) :
			$content = $plugin::returnClientForm($cid);
			echo $content;
		endforeach;
	}
	
	function displayNav() {
		foreach(self::$plugins_class as $plugin) :
			$content = $plugin::returnNav();
			echo $content;
		endforeach;
	}
	
	function displayCDlogs($cid) {
	
		foreach(self::$plugins_class as $plugin) :
			$content = $plugin::returnClientlogs($cid);
			echo $content;
		endforeach;
	}
	
	function getCSS() {
	}
	
	function getJS() {
	}


} // end class