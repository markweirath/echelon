<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

/**
 * class xlrstats
 * desc: File to deal with Echelon plugin XLRStats
 *
 */ 

class xlrstats extends plugins {

	public static $instance;
	public $name;
	
	function getClass() {
		$name =	get_class($this);
		$this->name = $name;
		return $name;
	}
	
	/**
	 * You may edit below here
	 */
	
	public static $xlr_user = false;
	public static $xlr_hide = 0;
	public static $xlr_fixed_name = NULL;
	
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
		parent::__construct($this->getClass());
	
		parent::setTitle('XLRStats');
		parent::setVersion(1.0);
	}
	
	public function __destruct() {
		parent::__destruct();
	}
	
	/**
	 * Main Function - DO NOT REMOVE any of the below functions
	 */
	public static function returnClientLogs($cid) {}
	public static function returnNav() {}
	public static function returnCSS() {}
	public static function returnJS() {}
	
	/**
	 * You may edit below here
	 */
	
	public static function returnClientFormTab() {
		
		global $mem; // use the member class instance from outside this class
	
		if($mem->reqLevel('edit_xlrstats'))
			return '<li><a href="#tabs" title="Edit some XLRStats information" rel="cd-act-xlrstats" class="cd-tab">XLRStats</a></li>';

	}// end returnClientFormTab
	
	public static function returnClientForm($cid) {
	
		if(empty($cid))
			return NULL;
	
		global $mem; // use the member class instance from outside this class
	
		if($mem->reqLevel('edit_xlrstats')) :
	
			$xlr_token = genFormToken('xlrstats');
	
			if(self::$xlr_hide == 1) 
				$hide = 'checked="checked"';
	
			$data = '<div id="cd-act-xlrstats" class="act-slide">
				<form action="lib/plugins/'.__CLASS__.'/actions.php" method="post">
				
					<label for="xlr-name">Fixed Name:</label>
						<input type="text" name="fixed-name" value="'. self::$xlr_fixed_name .'" id="xlr-name" /><br />
					
					<label for="xlr-hid">Hide Stats:</label>
						<input type="checkbox" name="hidden" id="xlr-hid" '.$hide.' />
						
					<div class="xlr"></div>
					
					<input type="hidden" name="cid" value="'.$cid.'" />
					<input type="hidden" name="token" value="'. $xlr_token .'" />
					<input type="submit" name="xlrstats-sub" value="Save Changes" />
				</form>
			</div>';
		
			return $data;
			
		else:
			return NULL;
		
		endif;
	
	} // end returnClientForm
	
	/**
	 * Internal function to connect to the DB and retrieve clients XLR bio Infomation
	 */
	private static function getClientBio() {
	
		$db = DB_B3::getPointer(); // get the pointer to the current B3 connection
		global $cid;
	
		## Get information for xlrstats ##
		$query_xlr = "SELECT id, kills, deaths, ratio, skill, rounds, hide, fixed_name FROM xlr_playerstats WHERE client_id = ? LIMIT 1";
		$stmt = $db->mysql->prepare($query_xlr) or die('Database Error');
		$stmt->bind_param('i', $cid);
		$stmt->execute();
		$stmt->store_result();

		if($stmt->num_rows > 0) {
			
			self::$xlr_user = true;
			$stmt->bind_result($id, $kills, $deaths, $ratio, $skill, $rounds, $hide, $fixed_name);
			$stmt->fetch();
			
			$results = array(
				'id' => $id,
				'kills' => $kills,
				'deaths' => $deaths,
				'ratio' => $ratio,
				'skill' => $skill,
				'rounds' => $rounds,
				'hide' => $hide,
				'fixed_name' => $fixed_name
			);
			
			self::$xlr_fixed_name = $fixed_name;
			self::$xlr_hide = $hide;
			
			
		} else
			self::$xlr_user = false;

		$stmt->free_result();
		$stmt->close();
		
		return $results;
			
	}
	
	## Main Function ##
	public static function returnClientBio() {
	
		$result = self::getClientBio();
	
		if(self::$xlr_user) :
		
			$ratio = number_format($result['ratio'], 2, '.', '');
			$skill = number_format($result['skill'], 2, '.', '');
			
			if(empty($result['fixed_name'])) 
				$name =  "Non Set";
			else 
				$name = $result['fixed_name'];
				
			if($results['hide'] == 1) 
				$hide = "Yes";
			else
				$hide = "No";

			$data = '<table class="cd-table" id="xlrstats-table">
				<tbody>
				<tr>
					<th>Kills</th>
						<td>'.$result['kills'].'</td>
					<th>Deaths</th>
						<td>'.$result['deaths'].'</td>
				</tr>
				<tr>
					<th>Ratio</th>
						<td>'.$ratio.'</td>
					<th>Skill</th>
						<td>'.$skill.'</td>
				</tr>
				<tr>
					<th>Rank</th>
						<td>(Not Working)</td>
					<th>XLRStats id</th>
						<td>'.$result['id'].'</td>
				</tr>
				<tr>
					<th>Fixed Name</th>
						<td>'.$name.'</td>
					<th>Hidden</th>
						<td>'.$hide.'</td>
				</tr>
				</tbody>
			</table>';
			
			return $data;
		
		endif;
		
	} // end displayXLRclient

} // end class