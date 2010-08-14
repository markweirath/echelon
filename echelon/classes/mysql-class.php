<?php
## Die if the user has come to this page directly ##
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

	## Settings ##
	public $mysql = NULL; // object var (if connection exists this will not be NULL)
	public $error = FALSE; // was there a DB query error
	public $query_error_pub = NULL;
	public $error_msg;

	private $error_sec = 'We are having some database problems, please check back later.'; // message to show the public if the DB query/connect fails
	private $error_on = false; // are detailed error reports on (default, can be overidden)
	
	private static $instance = NULL;

	## Connection Vars ##
	private $host; // B3 DB MySQL Host
	private $user; // B3 DB MySQL User
	private $pass; // B3 DB MySQL Password
	private $name; // B3 DB MySQL Database Name

	/**
	 * Gets the current instance of the class, there can only be one instance (this make the class a singleton class)
	 * 
	 * @return object $instance - the current instance of the class
	 */
	public static function getInstance($host, $user, $pass, $name, $error_on = false) {
		if (!(self::$instance instanceof self))
			self::$instance = new self($host, $user, $pass, $name, $error_on);

        return self::$instance;
    }
	
	public static function getPointer() {
	
		return self::$instance;
	
	}
	
	/**
	 * Auto Load in sent vars and make connection to the B3 DB
	 */
	public function __construct($host, $user, $pass, $name, $error_on) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->name = $name;
		$this->error_on = $error_on;

		try { // try to connect to the DB or die with an error
			$this->connectDB();
				
		} catch (Exception $e) {
			// get exception information
			$error_msg = strip_tags($e->getMessage());
			$code = $e->getCode();
			
			// log info
			echLog('mysqlconnect', $error_msg, $code);
			
			// set vars for outside class
			$this->error = true;
			$this->error_msg = $error_msg;
			
		} // end catch
		
	} // end constructor function
	
	// Do not allow the clone operation
    private function __clone() { }
	
	/**
	 * If access to a protected or private function is called
	 */
	public function __call($name, $arg) {
		echLog('error', 'System tried to access function '. $name .', a private or protected function in class '. get_class($this)); // log error
		echo "<strong>" . $name . "</strong> is a private function that cannot be accessed outside the B3 MySQL class"; // error out error
    }
	
	/**
     * __destruct : Destructor for class, closes the MySQL connection
     */
    public function __destruct() {
        if($this->mysql != NULL) // if it is set/created (defalt starts at NULL)
            @$this->mysql->close(); // close the connection
		
		$this->instance = NULL;
    }
	
	/**
     * Makes the connection to the DB or throws error
     */
    private function connectDB() {
	
		if($this->mysql != NULL) // if it is set/created (defalt starts at NULL)
			@$this->mysql->close();
		
		// Create new connection
        $this->mysql = @new mysqli($this->host, $this->user, $this->pass, $this->name);
		
		// if there was a connection error 
		if (mysqli_connect_errno()) : // NOTE: we are using the procedural method here because of a problem with the OOP method before PHP 5.2.9

			$code = @$this->mysql->connect_errno; // not all versions of PHP respond nicely to this
			if(empty($code)) // so if it does not then 
				$code = 1; // set to 1
		
			if($this->error_on) // only if settings say show to con error, will we show it, else just say error
				$error_msg = '<strong>B3 Database Connection Error:</strong> (#'. $code .') '.mysqli_connect_error();
			else
				$error_msg = $this->error_sec;
				
			$traces = NULL;
			$log_success = echLog('mysql', $error_msg, $code, $traces);
			if(!$log_success)
				die('Could not log fatal error');
				
			throw new Exception($error_msg, $code); // throw new mysql typed exception
			
		endif;
    }
	
	/**
	 * Handy Query function
	 *
	 * @param string $sql - the SQL query to execute
	 * @param bool $fetch - fetch the data rows or not
	 * @param string $type - typpe of query this is 
	 */
	public function query($sql, $fetch = true, $type = 'select') {

		if($mysql = NULL || $this->error)
			return false;
		
		try {
		
			if($stmt = $this->mysql->prepare($sql))
				$stmt->execute();
			else
				throw new MysqlException($this->mysql->error, $this->mysql->errno);

		} catch (MysqlException $e) {
		
			$this->error = true; // there is an error
			if($this->error_on) // if detailed errors work
				$this->error_msg = "MySQL Query Error (#". $this->mysql->errno ."): ". $this->mysql->error;
			else
				$this->error_msg = $this->error_sec;

			return false;
		}
		
		## setup results array
		$results = array();
		$results['data'] = array();
		
		## do certain things depending on type of query
		switch($type) { 
			case 'select': // if type is a select query
				$stmt->store_result();
				$results['num_rows'] = $stmt->num_rows(); // find the number of rows retrieved
			break;
			
			case 'update':
			case 'insert': // if insert or update find the number of rows affected by the query
				$results['affected_rows'] = $stmt->affected_rows(); 
			break;
		}
		
		## fetch the results
		if($fetch) : // only fetch data if we need it
		
			$meta = $stmt->result_metadata();

			while ($field = $meta->fetch_field()) :
				$parameters[] = &$row[$field->name];
			endwhile;

			call_user_func_array(array($stmt, 'bind_result'), $parameters);

			while ($stmt->fetch()) :
				foreach($row as $key => $val) {
					$x[$key] = $val;
				}
				$results['data'][] = $x;
			endwhile;

		endif;
		
		## return and close off connections
		return $results;
		$results->close();
		$stmt->close();
		
	} // end query()
	
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
		$query = "INSERT INTO penalties (type, client_id, admin_id, duration, inactive, keyword, reason, data, time_add, time_edit, time_expire) VALUES(?, ?, 0, ?, 0, 'Echelon', ?, ?, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), ?)";
		$stmt = $this->mysql->prepare($query) or die('MySQL Error');
		$stmt->bind_param('siissi', $type, $cid, $duration, $reason, $data, $time_expire);
		$stmt->execute();
		
		if($stmt->affected_rows > 0) // if something happened
			return true;
		else
			return false;
			
		$stmt->close();
	}
	
	/**
	 * Deactive a penalty
	 *
	 * @param string $pen_id - id of penalty to deactive
	 * @return bool
	 */
	function makePenInactive($pen_id) {
		$query = "UPDATE penalties SET inactive = 1 WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query);
		$stmt->bind_param('i', $pen_id);
		$stmt->execute();
		
		if($stmt->affected_rows > 0)
			return true;
		else
			return false;
			
		$stmt->close();
	
	}
	
	/**
	 * Get the pbid of the client from a penalty id
	 *
	 * @param string $pen_id - id of penalty to search with
	 * @return string - pbid of the client
	 */
	function getPBIDfromPID($pen_id) {
		$query = "SELECT c.pbid FROM penalties p LEFT JOIN clients c ON p.client_id = c.id WHERE p.id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query);
		$stmt->bind_param('i', $pen_id);
		$stmt->execute();
		
		$stmt->store_result();
		$stmt->bind_result($pbid);
		$stmt->fetch();
		$stmt->free_result();
		$stmt->close();
	
		return $pbid;
	}

#############################
#############################
} // end class

/**
 * Exceptions class for the B3 DB
 *
 */
class MysqlException extends Exception {

	private $ex_error;
	private $ex_errno;

	public function __construct($error, $errno) {
		
		// get sent vars
		$this->ex_error = $error;
		$this->ex_errno = $errno;
		
		// get exception information from parent class
		$traces = parent::getTraceAsString();
		
		// find error message and code
		$code = $this->ex_errno;
		$message = $this->ex_error;
		
		// log error message
		$log_success = echLog('mysql', $message, $code, $traces);
		if(!$log_success)
			die('Could not log fatal error');

		// call parent constructor
		parent::__construct($message, $code);
	}

}