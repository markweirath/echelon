<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'plugins-class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

/**
 * class plugin
 * desc: File to deal with Echelon plugins
 *
 */ 

class plugins {

	protected $name; // name of the plugin
	protected $title; // pretty version of the name of the plugin
	private $version = 1.0;
	
	protected static $plugins_class = NULL;
	
	public function __construct($name) {
		$this->name = $name;
	}
	
	public function __destruct() {
	}
	
	protected function setTitle($title) {
		$this->title = $title;
	}
	
	protected function setVersion($version) {
		$this->version = $version;
	}
	
	public static function setPluginsClass($value) {
		self::$plugins_class = $value;
	}
	
	protected function getTitle() {
		return $this->title;
	}
	
	protected function getName() {
		return $this->name;
	}
	
	/**
	 * Internal function: in the case of fatal error die with plugin name and error message
	 */
	protected function error($msg) {
		die($this->getName().' Plugin Error: '. $msg);
	}
	
	/**
	 * This function display information from plugins in the CD's bio area
	 *
	 * @param array $plugins_class - an array of pointers to the class of the plugins
	 */
	function displayCDBio() {
		foreach(self::$plugins_class  as $plugin) :
			if(method_exists($plugin, 'returnClientBio')) {
				$content = $plugin->returnClientBio();
				echo $content;
			}
		endforeach;
	}
	
	/**
	 * This function display the tab of any plugin added forms on the clientdetails page
	 *
	 * @param array $plugins_class - an array of pointers to the class of the plugins
	 */
	function displayCDFormTab() {
		foreach(self::$plugins_class as $plugin) :
			if(method_exists($plugin, 'returnClientFormTab')) {
				$content = $plugin->returnClientFormTab();
				echo $content;
			}
		endforeach;
	}
	
	/**
	 * This function display forms on the clientdetails page added by any plugins
	 *
	 * @param array $plugins_class - an array of pointers to the class of the plugins
	 */
	function displayCDForm($cid = 0) {
		foreach(self::$plugins_class as $plugin) :
			if(method_exists($plugin, 'returnClientForm')) {
				$content = $plugin->returnClientForm($cid);
				echo $content;
			}
		endforeach;
	}
	
	/**
	 * For each plugin check if they want to add a link
	 */
	function displayNav() {
		foreach(self::$plugins_class as $plugin) :
			if(method_exists($plugin, 'returnNav')) {
				$content = $plugin->returnNav();
				echo $content;
			}
		endforeach;
	}
	
	/**
	 * For each plugin check if they want to append something to the end of the CD page
	 */
	function displayCDlogs($cid) {
		foreach(self::$plugins_class as $plugin) :
			if(method_exists($plugin, 'returnClientLogs')) {
				$content = $plugin->returnClientlogs($cid);
				echo $content;
			}
		endforeach;
	}
	
	/**
	 * For each plugin check if they need to include a css file
	 */
	function getCSS() {
		foreach(self::$plugins_class as $plugin) :
			if(method_exists($plugin, 'returnCSS')) {
				$content = $plugin->returnCSS();
				echo $content;
			}
		endforeach;
	}
	
	/**
	 * For each plugin check if they need to include a JS file
	 */
	function getJS() {
		foreach(self::$plugins_class as $plugin) :
			if(method_exists($plugin, 'returnJS')) {
				$content = $plugin->returnJS();
				echo $content;
			}
		endforeach;
	}


} // end class