<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'mysql-class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');
		
## file to deal with the connection to the B3 databases ##
/* 
* class DB_B3
* @param Host 
* @param User 
* @param Password 
* @param Name 
*/  

class DB_B3 {

	public $mysql; // create public var
	
	var $host; // B3 DB MySQL Host
	var $user; // B3 DB MySQL User
	var $pass; // B3 DB MySQL Password
	var $name; // B3 DB MySQL Database Name
	
	// start connection to db on load
	function __construct($host, $user, $pass, $name) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->name = $name;
		
		$this->mysql = new mysqli($this->host, $this->user, $this->pass, $this->name);
	}
	
	function getB3Groups() {
		$query = "SELECT id, name FROM groups ORDER BY id ASC";
		$stmt = $this->mysql->prepare($query);
		$stmt->execute();
		$stmt->bind_result($id, $name);
		
		while($stmt->fetch()) :
			$groups[] = array(
				'id' => $id,
				'name' => $name
			); 	
		endwhile;
	
		$stmt->close();
		return $groups;	
	}
	
	function getB3GroupsLevel() {
		$query = "SELECT id FROM groups ORDER BY id ASC";
		$stmt = $this->mysql->prepare($query);
		$stmt->execute();
		$stmt->bind_result($id);
		
		$groups = array();
		
		while($stmt->fetch()) :
			array_push($groups, $id);
		endwhile;
	
		$stmt->close();
		return $groups;	
	
	}
	
	/**
	 * insert a penalty into the penalty table in the B3 DB
	 *
	 * @param string $type - type of penalty
	 * @param int $cid - client_id who is getting the penalty
	 * @param int $duration - duration of pen
	 * @param string $reason - reason for pen
	 * @param string $data - some additional data
	 * @param int $time_expire - time (unix time) for when the pen will expire
	 * @return bool
	 */
	function penClient($type, $cid, $duration, $reason, $data, $time_expire) {
	
		// id(), type, client_id, admin_id(0), duration, inactive(0), keyword(Echelon), reason, data, time_add(time), time_edit(time), time_expire
		$query = "INSERT INTO penalties (type, client_id, admin_id, duration, inactive, keyword, reason, data, time_add, time_edit, time_expire) VALUES(?, ?, 0, ?, 0, 'Echelon', ?, '', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), ?)";
		$stmt = $this->mysql->prepare($query) or die('MySQL Error');
		$stmt->bind_param('siisi', $type, $cid, $duration, $reason, $time_expire);
		$stmt->execute();
		
		if($stmt->affected_rows > 0) // if something happened
			return true;
		else
			return false;
			
		$stmt->close();
	}

#############################
#############################
} // end class