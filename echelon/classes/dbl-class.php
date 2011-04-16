<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'dbl-class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

/**
 * class DBL
 * desc: File to deal with the connection and all queries to the Echelon Database 
 * note: this is a singleton type class
 *
 * @var object $mysql - the var that stores the connection to the mysql DB
 * @var object $instance - the pointer to the instance of the class
 * @var bool $dbl_error - holds the db errors if any
 * @var string $install_error - holds any installation test connection errors
 * @var bool $install - is this instance to be a install test connection or a full connection
 */ 

class DbL {

	private $mysql = NULL;
	private $install = false;
	private static $instance;
	
	public $install_erorr = NULL;
	public $dbl_error = false;
	
	/**
	 * Gets the current instance of the class, there can only be one instance (this make the class a singleton class)
	 * 
	 * @param bool $install - weather or not this is an install test connection
	 * @return object $instance - the current instance of the class
	 */
	public static function getInstance($install = false) {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($install);
        }
 
        return self::$instance;
    }
	
	/**
     * Auto run on creation of instance: attempts to connect to the Echelon DB or dies with the mysql error
     */
	private function __construct($install) {
	
		if(isset($install))
			$this->install = $install;
		
		try { 
			$this->connectDB(); // try to connect to the database
			
		} catch (Exception $e) {
		
			if($this->install) // if this is an install test return message
				$this->install_error = $e->getMessage();
			else
				die($e->getMessage());
			
		} // end try/catch
		
	} // end construct
	
	// Do not allow the clone operation
    private function __clone() { }

	/**
     * Makes the connection to the DB or throws error
     */
    private function connectDB () {
		if($this->mysql != NULL) // if it is set/created (default starts at NULL)
			@$this->mysql->close();

        $this->mysql = @new mysqli(DBL_HOSTNAME, DBL_USERNAME, DBL_PASSWORD, DBL_DB); // block any error on connect, it will be caught in the next line and handled properly
		
		if(mysqli_connect_errno()) : // if the connection error is on then throw exception

			if($this->install) :
				$error_msg = '<strong>Database Connection Error</strong>
					<p>'.mysqli_connect_error().'<br />
					The connection information you supplied is incorrect. Please try again.</p>';
			
			elseif(DB_CON_ERROR_SHOW) : // only if settings say show to con error, will we show it, else just say error
				$error_msg = '<h3>Database Connection Error</h3> 
					<p>'.mysqli_connect_error().'<br />
					Since we have encountered a database error, Echelon is shutting down.</p>';
								
			else :
				$error_msg = '<h3>Database Problem</h3>
					<p>Since we have encountered a database error, Echelon is shutting down.</p>';
								
			endif;

			throw new Exception($error_msg);
		endif;
		
    } // end connect
	
	/**
     * __destruct : Destructor for class, closes the MySQL connection
     */
    public function __destruct() {
        if ($this->mysql != NULL) // if it is set/created (defalt starts at NULL)
            @$this->mysql->close(); // close the connection
    }
	
	/**
	 * Handy Query function
	 *
	 * @param string $sql - the SQL query to execute
	 * @param bool $fetch - fetch the data rows or not
	 * @param string $type - typpe of query this is 
	 */
	private function query($sql, $fetch = true, $type = 'select') {

		if($this->error)
			return false;
		
		try {
		
			if($stmt = $this->mysql->prepare($sql))
				$stmt->execute();
			else
				throw new Exception('');

		} catch (Exception $e) {
		
			$this->error = true; // there is an error
			if($this->error_on) // if detailed errors work
				$this->error_msg = "MySQL Query Error (#". $this->mysql->errno ."): ". $this->mysql->error;
			else
				$this->error_msg = $this->error_sec;

			return;
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
	
	/***************************
	
		Start Query Functions
	
	****************************/
	
	
	/**
	 * Gets an array of data for the settings form
	 *
	 * @param string $cat - category of settigs to retrieve
	 * @return array
	 */
	function getSettings() {
        $query = "SELECT SQL_CACHE name, value FROM ech_config";
        $stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->execute();
		
		$stmt->store_result();
		$stmt->bind_result($name, $value);
        
		$settings = array();
		
        while($stmt->fetch()) : // get results
            $settings[$name] = $value;
        endwhile;
		
		$stmt->close();
		
        return $settings;
    }
	
	function getGameInfo($game) {
	
		$query = "SELECT SQL_CACHE id, game, name, name_short, num_srvs, db_host, db_user, db_pw, db_name, plugins, active FROM ech_games WHERE id = ?";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('i', $game);
		$stmt->execute();
		
		$stmt->bind_result($id, $game, $name, $name_short, $num_srvs, $db_host, $db_user, $db_pw, $db_name, $plugins, $active);
        $stmt->fetch(); // get results		
		
		$game = array(
			'id' => $id,
			'game' => $game,
			'name' => $name,
			'name_short' => $name_short,
			'num_srvs' => $num_srvs,
			'db_host' => $db_host,
			'db_user' => $db_user,
			'db_pw' => $db_pw,
			'db_name' => $db_name,
			'plugins' => $plugins,
			'active' => $active
		);
		
		return $game;
	}
    
	/**
	 * Update the settings
	 *
	 * @param string/int $value - the new value for the setting
	 * @param string $name - the name of the settings
	 * @param string $value_type - wheather the value provided is a string or an int
	 * @return bool
	 */
    function setSettings($value, $name, $value_type) {
        
		$query = "UPDATE ech_config SET value = ? WHERE name = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param($value_type.'s', $value, $name);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		$stmt->close();
		
		if($affect > 0)
			return true;
		else
			return false;
    }
	
	/**
	 * Update game settings
	 *
	 * @return bool
	 */
    function setGameSettings($game, $name, $name_short, $db_user, $db_host, $db_name, $db_pw, $change_db_pw, $plugins, $enable = true) {
		
		$query = "UPDATE ech_games SET name = ?, name_short = ?, db_host = ?, db_user = ?, db_name = ?, plugins = ?, active = ?";
		
		if($change_db_pw) // if the DB password is to be chnaged
			$query .= ", db_pw = ?";

		$query .= " WHERE id = ? LIMIT 1";
			
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		if($change_db_pw) // if change DB PW append var
			$stmt->bind_param('sssssssi', $name, $name_short, $db_host, $db_user, $db_name, $plugins, $db_pw, $enable, $game);
		else // else var info not needed in bind_param
			$stmt->bind_param('ssssssii', $name, $name_short, $db_host, $db_user, $db_name, $plugins, $enable, $game);
		$stmt->execute();
		
		return $stmt->affected_rows > 0;
    }
	
	/**
	 * Add a Game to the Echelon list
	 *
	 * @param string $name - name of the game
	 * @param string $game - the game type (eg. cod4, cod2, bfbc2)
	 * @param string $name_short - short name for the game
	 * @param string $db_host - database host
	 * @param string $db_user - database user
	 * @param string $db_pw - database password
	 * @param string $db_name - database name
	 * @return bool
	 */
	function addGame($name, $game, $name_short, $db_host, $db_user, $db_pw, $db_name) {
		// id, name, game, name_short, num_srvs, db_host, db_user, db_pw, db_name
		$query = "INSERT INTO ech_games (name, game, name_short, num_srvs, db_host, db_user, db_pw, db_name, active) VALUES(?, ?, ?, 0, ?, ?, ?, ?, TRUE)";
		$stmt = $this->mysql->prepare($query) or die('Database Error:'. $this->mysql->error);
		$stmt->bind_param('sssssss', $name, $game, $name_short, $db_host, $db_user, $db_pw, $db_name);
		$stmt->execute();
		
		if($stmt->affected_rows > 0)
			return true;
		else
			return false;
	}
	
	function addGameCount() {
		$query = "UPDATE ech_config SET value = (value + 1) WHERE name = 'num_games' LIMIT 1";
		$result = $this->mysql->query($query) or die('Database Error');
		
		return $result;
	}
	
	function getServers($cur_game) {
		$query = "SELECT SQL_CACHE id, name, ip, pb_active, rcon_pass, rcon_ip, rcon_port FROM ech_servers WHERE game = ?";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('i', $cur_game);
		$stmt->execute();
		$stmt->bind_result($id, $name, $ip, $pb_active, $rcon_pass, $rcon_ip, $rcon_port); // bind results into vars

		while($stmt->fetch()) : // get results and store in an array
			$servers[] = array(
				'id' => $id,
				'name' => $name,
				'ip' => $ip,
				'pb_active' => $pb_active,
				'rcon_pass' => $rcon_pass,
				'rcon_ip' => $rcon_ip,
				'rcon_port' => $rcon_port
			);
		endwhile;
		
		$stmt->close();
		return $servers;
	}
	
	function getServer($id) {
		$query = "SELECT game, name, ip, pb_active, rcon_pass, rcon_ip, rcon_port FROM ech_servers WHERE id = ?";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->bind_result($game, $name, $ip, $pb_active, $rcon_pass, $rcon_ip, $rcon_port); // bind results into vars

		while($stmt->fetch()) : // get results and store in an array
			$server = array(
				'game' => $game,
				'name' => $name,
				'ip' => $ip,
				'pb_active' => $pb_active,
				'rcon_pass' => $rcon_pass,
				'rcon_ip' => $rcon_ip,
				'rcon_port' => $rcon_port
			);
		endwhile;
		
		$stmt->close();
		return $server;
	}
	
	/**
	 * Gets a list of all the servers from all the games for the servers table
	 *
	 * @param string $orderby - what var to order by
	 * @param string $order - which way to order (ASC/DESC)
	 * @return bool
	 */
	function getServerList($orderby, $order) {
			
		$query = "SELECT s.id, s.name, s.ip, s.game, s.pb_active, g.name as g_name FROM ech_servers s LEFT JOIN ech_games g ON s.game = g.id ORDER BY ".$orderby." ".$order;
		$result = $this->mysql->query($query);
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) :
			while($row = $result->fetch_object()) : // get results		
				$servers[] = array(
					'id' => $row->id,
					'game' => $row->game,
					'name' => $row->name,
					'ip' => $row->ip,
					'pb_active' => $row->pb_active,
					'game_name' => $row->g_name
				);
			endwhile;
			
			return $servers; // return the information
			
		else :
			return false;
		
		endif;
	}
	
	/**
	 * Update server settings
	 *
	 * @return bool
	 */
    function setServerSettings($server_id, $name, $ip, $pb, $rcon_ip, $rcon_port, $rcon_pw, $change_rcon_pw) {
		
		$query = "UPDATE ech_servers SET name = ?, ip = ?, pb_active = ?, rcon_ip = ?, rcon_port = ?";
		
		if($change_rcon_pw) // if the DB password is to be chnaged
			$query .= ", rcon_pass = ?";

		$query .= " WHERE id = ? LIMIT 1";
			
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		if($change_rcon_pw) // if change RCON PW append
			$stmt->bind_param('ssisisi', $name, $ip, $pb, $rcon_ip, $rcon_port, $rcon_pw, $server_id);
		else // else info not needed in bind_param
			$stmt->bind_param('ssisii', $name, $ip, $pb, $rcon_ip, $rcon_port, $server_id);
		$stmt->execute();
		
		if($stmt->affected_rows > 0)
			return true;
		else
			return false;	
    }
	
	/**
	 * Add a server
	 *
	 * @return bool
	 */
    function addServer($game_id, $name, $ip, $pb, $rcon_ip, $rcon_port, $rcon_pw) {
		
		// id, game, name, ip, pb_active, rcon_pass, rcon_ip, rcon_port
		$query = "INSERT INTO ech_servers VALUES(NULL, ?, ?, ?, ?, ?, ?, ?)";
			
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ississi', $game_id, $name, $ip, $pb, $rcon_pw, $rcon_ip, $rcon_port);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		$stmt->close();
		
		if($affect > 0)
			return true;
		else
			return false;	
    }
	
	/**
	 * After adding a server we need to update the games table to add 1 to num_srvs
	 *
	 * @param int $game_id - the id of the game that is to be updated
	 */
	function addServerUpdateGames($game_id) {
		$query = "UPDATE ech_games SET num_srvs = (num_srvs + 1) WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error:'. $this->mysql->error);;
		$stmt->bind_param('i', $game_id);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		$stmt->close();
		
		if($affect > 0)
			return true;
		else
			return false;
	}
	
	function delServerUpdateGames($game_id) {
		$query = "UPDATE ech_games SET num_srvs = (num_srvs - 1) WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error:'. $this->mysql->error);;
		$stmt->bind_param('i', $game_id);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		$stmt->close();
		
		if($affect > 0)
			return true;
		else
			return false;
	}
	
	function getGamesList() {
		$query = "SELECT SQL_CACHE id, name, name_short FROM ech_games ORDER BY id ASC";
		$results = $this->mysql->query($query) or die('Database error');
		
		while($row = $results->fetch_object()) :	
			$games[] = array(
				'id' => $row->id,
				'name' => $row->name,
				'name_short' => $row->name_short
			);
		endwhile;
		return $games;
	}
	
	/**
	 * This function gets the salt value from the users table by using a clients username
	 *
	 * @param string $username - username of the user you want to find the salt of
	 * @return string/false
	 */
	function getUserSalt($username) {
		
		$query = "SELECT salt FROM ech_users WHERE username = ?";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('s', $username);
		$stmt->execute(); // run query
		
		$stmt->store_result(); // store results
		$stmt->bind_result($salt); // store result
		$stmt->fetch(); // get the one result result

		if($stmt->num_rows == 1)
			return $salt;
		else
			return false;
		
		$stmt->free_result();
		$stmt->close();
	}
	
	/**
	 * This function returns an array of permissions for users
	 *
	 * @return array
	 */
	function getPermissions($get_desc = true) {
	
		if($get_desc)
			$query = "SELECT * FROM ech_permissions";
		else
			$query = "SELECT id, name FROM ech_permissions";
			
		$query .= " ORDER BY id ASC";
			
		$results = $this->mysql->query($query);
		
		while($row = $results->fetch_object()) : // get results
		
			if($get_desc) : // if get desc then return desc in results
				$perms[] = array(
					'id' => $row->id,
					'name' => $row->name,
					'desc' => $row->description
				);
				
			else : // else dont return the desc of the perm
				$perms[] = array(
					'id' => $row->id,
					'name' => $row->name,
				);
			endif;
			
		endwhile;
		return $perms;
	}
    
	/**
	 * This function validates user login info and if correct to return some user info
	 *
	 * @param string $username - username of user for login validation
	 * @param string $pw - password of user for login validation
	 * @return array/false
	 */
	function login($username, $pw) {

		$query = "SELECT u.id, u.ip, u.last_seen, u.display, u.email, u.ech_group, g.premissions FROM ech_users u LEFT JOIN ech_groups g ON u.ech_group = g.id WHERE u.username = ? AND u.password = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ss', $username, $pw);
		$stmt->execute(); // run query
		
		$stmt->store_result(); // store results	
		$stmt->bind_result($id, $ip, $last_seen, $name, $email, $group, $perms); // store results
		$stmt->fetch(); // get results

		if($stmt->num_rows == 1):
			$results = array($id, $ip, $last_seen, $name, $email, $group, $perms);
			return $results; // yes log them in
		else :
			return false;
		endif;
		
		$stmt->free_result();
		$stmt->close();
	}
	
	/**
	 * This function updates user records with new IP address and time last seen
	 *
	 * @param string $ip - current IP address to upodate DB records
	 * @param int $id - id of the current user 
	 */
	function newUserInfo($ip, $id) { // updates user records with new IP, time of login and sets active to 1 if needed
		
		$time = time();
		$query = "UPDATE ech_users SET ip = ?, last_seen = ? WHERE id = ?";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('sii', $ip, $time, $id);
		$stmt->execute(); // run query
		$stmt->close(); // close connection
	}
	
	/**
	 * This function checks to see if the current user IP is on the blacklist
	 *
	 * @param string $ip - IP addrss of current user to check of current BL
	 * @return bool
	 */
	function checkBlacklist($ip) {

		$query = "SELECT id FROM ech_blacklist WHERE ip = ? AND active = 1 LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database error');
		$stmt->bind_param('s', $ip);
		$stmt->execute();

		$stmt->store_result();
		
		if($stmt->num_rows > 0)
			return true;
		
		$stmt->free_result();
		$stmt->close();
		
		return false;
	}

	/**
	 * Blacklist a user's IP address
	 *
	 * @param string $ip - IP address you wish to ban
	 * @param string $comment [optional] - Comment about reason for ban
	 * @param int $admin - id of the admin who added the ban (0 if auto added)
	 */
	function blacklist($ip, $comment = 'Auto Added Ban', $admin = 0) { // add an Ip to the blacklist
		$comment = cleanvar($comment);
		$time = time();
		// id, ip, active, reason, time_add, admin_id
		$query = "INSERT INTO ech_blacklist VALUES(NULL, ?, 1, ?, ?, ?)";
		$stmt = $this->mysql->prepare($query) or die('Database error');
		$stmt->bind_param('ssii', $ip, $comment, $time, $admin);
		$stmt->execute(); // run query
		
		$affect = $stmt->affected_rows;
		$stmt->close();
		
		if($affect > 0)
			return true;
		else
			return false;
	}
	
	/**
	 * Get an array of data about the Blacklist for a table
	 *
	 * @return array
	 */
	function getBL() { // return the rows of info for the BL table

		$query = "SELECT bl.id, bl.ip, bl.active, bl.reason, bl.time_add, u.display 
					FROM ech_blacklist bl LEFT JOIN ech_users u ON bl.admin_id = u.id 
					ORDER BY active ASC";	
		
		$results = $this->query($query);
		
		return $results;
	}
	
	/**
	 * De/Re-actives a Blacklist Ban
	 *
	 * @param int $id - id of the ban
	 * @param bool $active - weather to activate or deactivate
	 */
	function BLactive($id, $active = true) {

		if($active == false)
			$active = 0;
		else
			$active = 1;

		$query = "UPDATE ech_blacklist SET active = ? WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ii', $active, $id);
		$stmt->execute(); // run query
		$stmt->close(); // close connection

	} // end BLactive

	/**
	 * Gets an array of data for the users table
	 *
	 * @return array
	 */
	function getUsers() { // gets an array of all the users basic info

		$query = "SELECT u.id, u.display, u.email, u.ip, u.first_seen, u.last_seen, g.namep FROM ech_users u LEFT JOIN ech_groups g ON u.ech_group = g.id ORDER BY id ASC";
		
		$users = $this->query($query);
		
		return $users;
	}
	
	/**
	 * Gets a list of all currently valid registration keys
	 *
	 * @param $key_expire - the setting for how many days a key is valid for
	 * @return array
	 */
	function getKeys($key_expire) {
		$limit_seconds = $key_expire*24*60*60; // user_key_limit is sent in days so we must convert it
		$time = time();
		$expires = $time+$limit_seconds;
		$query = "SELECT k.reg_key, k.email, k.comment, k.time_add, k.admin_id, u.display 
				  FROM ech_user_keys k LEFT JOIN ech_users u ON k.admin_id = u.id
				  WHERE k.active = 1  AND k.time_add < $time AND comment != 'PW' ORDER BY time_add ASC";

		$reg_keys = $this->query($query);
		
		return $reg_keys;
	}
	
	/**
	 * Check a username is not already in use
	 *
	 * @param string $username - username to check
	 * @return bool
	 */
	function checkUsername($username) {

		$query = "SELECT username FROM ech_users WHERE username = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('s', $username);
		$stmt->execute(); // run query
		$stmt->store_result(); // store query
		
		if($stmt->num_rows >= 1) // if there is a row
			return false;
		else
			return true;
		
		$stmt->free_result();
		$stmt->close();
	}
	
	/**
	 * Find the users pasword salt by using their id
	 *
	 * @param int $user_id
	 * @return string/false
	 */
	function getUserSaltById($user_id) {
		
		$query = "SELECT salt FROM ech_users WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('s', $user_id);
		$stmt->execute(); // run query
		
		$stmt->store_result(); // store results // needed for num_rows
		$stmt->bind_result($salt); // store result
		$stmt->fetch(); // get the one result result

		if($stmt->num_rows == 1)
			return $salt;
		else
			return false;
		
		$stmt->free_result();
		$stmt->close();
	}
	
	/**
	 * Checks that a users password is correct (needed to verify user idenity with edit me page)
	 *
	 * @param int $user_id - id of the user
	 * @param string $hash_pw - hashed version of the pw (including the salt in the hash)
	 * @return bool
	 */
	function validateUserRequest($user_id, $hash_pw) { // see if the supplied password matches that of the supplied user id
		$query = "SELECT id FROM ech_users WHERE id = ? AND password = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('is', $user_id, $hash_pw);
		$stmt->execute();
		$stmt->store_result(); // needed to allow num_rows to buffer
		
		if($stmt->num_rows == 1)
			return true; // the person exists
		else
			return false; // person does not exist
			
		$stmt->free_result();
		$stmt->close();
	}
	
	/**
	 * Allows a user to edit their display name and email
	 *
	 * @param string $name - new display name
	 * @param string $email - new email address
	 * @param int $user_id - id of the user to update
	 * @return bool
	 */
	function editMe($name, $email, $user_id) {
		$query = "UPDATE ech_users SET display = ?, email = ? WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ssi', $name, $email, $user_id);
		$stmt->execute();
		
		if($stmt->affected_rows != 1) 
			return false; // if nothing happened
		else
			return true; // retrun true (it happened)
		
		$stmt->close(); // close connection
	}
	
	/**
	 * Allows user to edit their password
	 *
	 * @param string $password_new - new password in hash form
	 * @param string $salt_new - the new salt for that user
	 * @param int $user_id - id of the user to update
	 * @return bool
	 */
	function editMePW($password_new, $salt_new, $user_id) { // update a user with a new pw and salt
		$query = "UPDATE ech_users SET password = ?, salt = ? WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ssi', $password_new, $salt_new, $user_id);
		$stmt->execute();
		
		if($stmt->affected_rows != 1)
			return false;
		else
			return true;

		$stmt->close();
	}
	
	/**
	 * Add a key key to the user_keys table to allow registration
	 *
	 * @param string $user_key - unique key (40 char hash)
	 * @param string $email - email address of the user the key is for
	 * @param string $comment - comment as to why the user was added
	 * @param int $perms - value of perms this user is allowed access to
	 * @param int $admin_id - id of the admin who added this key
	 * @return bool
	 */
	function addEchKey($user_key, $email, $comment, $group, $admin_id) {
		$time = time();
		// key, ech_group, admin_id, comment, time_add, email, active
		$query = "INSERT INTO ech_user_keys VALUES(?, ?, ?, ?, ?, ?, 1)";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('siisis', $user_key, $group, $admin_id, $comment, $time, $email);
		$stmt->execute();
		
		if($stmt->affected_rows)
			return true;
		else
			return false;
		
		$stmt->close();
	}
	
	/**
	 * Delete a registration key
	 *
	 * @param string $key - unique key to delete
	 * @return bool
	 */
	function delKey($key) {
	
		$query = "DELETE FROM ech_user_keys WHERE reg_key = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query);
		$stmt->bind_param('s', $key);
		$stmt->execute();
		
		if($stmt->affected_rows)
			return true;
		else
			return false;
			
		$stmt->close();
	}
	
	/**
	 * Edit a registration key's comment
	 *
	 * @param string $key - unique key to delete
	 * @param string $comment - the new comment text
	 * @param int $admin_id - the admin who created the key is the only person who can edit the key
	 * @return bool
	 */
	function editKeyComment($key, $comment, $admin_id){
	
		$query = "UPDATE ech_user_keys SET comment = ? WHERE reg_key = ? AND admin_id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ssi', $comment, $key, $admin_id);
		$stmt->execute();
		
		if($stmt->affected_rows)
			return true;
		else
			return false;
			
		$stmt->close();
	}
	
	/**
	 * Verify that the supplied user key, email are valid, that the key has not expired or already been used
	 *
	 * @param string $key - unique key to search for
	 * @param string $email - email address connected with each key
	 * @param string $key_expire - lenght in days of how long a key is active for
	 * @return bool
	 */
	function verifyRegKey($key, $email, $key_expire) {
		$limit_seconds = $key_expire*24*60*60; // user_key_limit is sent in days so we must convert it
		$time = time();
		$expires = $time+$limit_seconds;
	
		$query = "SELECT reg_key, email FROM ech_user_keys WHERE reg_key = ? AND email = ? AND active = 1 AND time_add < ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ssi', $key, $email, $expires);
		$stmt->execute();
		
		$stmt->store_result(); // store results

		if($stmt->num_rows == 1)
			return true;
		else
			return false;
		
		$stmt->free_result();
		$stmt->close();
	}
	
	/**
	 * Find the perms and admin_id assoc with that unique key
	 *
	 * @param string $key - unique key to search with
	 * @return string
	 */
	function getGroupAndIdWithKey($key) {
	
		$query = "SELECT ech_group, admin_id FROM ech_user_keys WHERE reg_key = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('s', $key);
		$stmt->execute();
		$stmt->bind_result($group, $admin_id); // store results	
		$stmt->fetch();
		
		$result = array($group, $admin_id);
		$stmt->free_result();
		$stmt->close();
		
		return $result;
	}
	
	function getIdWithKey($key) {
	
		$query = "SELECT admin_id FROM ech_user_keys WHERE reg_key = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('s', $key);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows == 1) {
			$stmt->bind_result($value); // store results	
			$stmt->fetch();
			$stmt->free_result();
			$stmt->close();
			return $value;
		} else
			return false;
	}
	
	/**
	 * Gets an array of data for the settings form
	 *
	 * @param string $key - the key to deactive
	 * @return bool
	 */
	function deactiveKey($key) {
		$query = "UPDATE ech_user_keys SET active = 0 WHERE reg_key = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('s', $key);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		
		$stmt->close();
		
		if($affect == 1)
			return true;
		else
			return false;
	}
	
	/**
	 * Add the user information the users table
	 *
	 * @param string $username - username of user
	 * @param string $display - display name of user
	 * @param string $email - email of user
	 * @param string $password - password
	 * @param string $salt - salt string for the password
	 * @param string $group - group for the user
	 * @param string $admin_id - id of the admin who added the user key
	 * @return bool
	 */
	function addUser($username, $display, $email, $password, $salt, $group, $admin_id) {
		$time = time();
		// id, username, display, email, password, salt, ip, group, admin_id, first_seen, last_seen
		$query = "INSERT INTO ech_users VALUES(NULL, ?, ?, ?, ?, ?, NULL, ?, ?, ?, NULL)";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('sssssiii', $username, $display, $email, $password, $salt, $group, $admin_id, $time);
		$stmt->execute();
		
		if($stmt->affected_rows)
			return true;
		else
			return false;
		
		$stmt->close();
	}

	/**
	 * Deletes a user
	 *
	 * @param string $id - id of user to deactive
	 * @return bool
	 */
	function delUser($user_id) {
		$query = "DELETE FROM ech_users WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('i', $user_id);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		
		$stmt->close();
		
		if($affect == 1)
			return true;
		else
			return false;
	}
	
	function editUser($id, $username, $display, $email, $ech_group) {
		$query = "UPDATE ech_users SET username = ?, display = ?, email = ?, ech_group = ? WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('sssii', $username, $display, $email, $ech_group, $id);
		$stmt->execute();
		
		if($stmt->affected_rows == 1)
			return true;
		else
			return false;
			
		$stmt->close();
	}
	
	/**
	 * Gets a user's details (for SA view/edit)
	 *
	 * @param string $id - id of user
	 * @return array
	 */
	function getUserDetails($id) {
		$query = "SELECT u.username, u.display, u.email, u.ip, u.ech_group, u.admin_id, u.first_seen, u.last_seen, a.display 
				  FROM ech_users u LEFT JOIN ech_users a ON u.admin_id = a.id WHERE u.id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('i', $id);
		$stmt->execute();
	
		$stmt->store_result();
		$stmt->bind_result($username, $display, $email, $ip, $group, $admin_id, $first_seen, $last_seen, $admin_name);
		$stmt->fetch();

		if($stmt->num_rows == 1) :
			$data = array($username, $display, $email, $ip, $group, $admin_id, $first_seen, $last_seen, $admin_name);
			return $data;
		else :
			return false;
		endif;
		
		$stmt->free_result();
		$stmt->close();
	}
	
	function getUserDetailsEdit($id) {
		$query = "SELECT username, display, email, ech_group FROM ech_users WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('i', $id);
		$stmt->execute();
	
		$stmt->store_result();
		$stmt->bind_result($username, $display, $email, $group_id);
		$stmt->fetch();

		if($stmt->num_rows == 1) :
			$data = array($username, $display, $email, $group_id);
			return $data;
		else :
			return false;
		endif;
		
		$stmt->free_result();
		$stmt->close();
	}
	
	/**
	 * Verifies if a user name and email exist
	 *
	 * @param string $name - username of user
	 * @param string $email - email address of user
	 * @return int($user_id)/bool(false)
	 */
	function verifyUser($name, $email) {
	
		$query = "SELECT id FROM ech_users WHERE username = ? AND email = ? LIMIT 1";
		$stmt =  $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ss', $name, $email);
		$stmt->execute();
		
		$stmt->store_result();
		if($stmt->num_rows == 1) {
			$stmt->bind_result($user_id);
			$stmt->fetch();
			return $user_id;
		} else {
			return false;
		}
		$stmt->free_result();
		$stmt->close();
	}
	
	/**
	 * Gets an array of the echelon groups
	 *
	 * @return array
	 */
	function getGroups() {
		$query = "SELECT id, namep FROM ech_groups ORDER BY id ASC";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->execute();
		$stmt->bind_result($id, $display);
		
		while($stmt->fetch()) :
			$groups[] = array(
				'id' => $id,
				'display' => $display
			);
		endwhile;
		
		$stmt->close();
		return $groups;	
	}
	
	function getGroupInfo($group_id) {
		$query = "SELECT namep, premissions FROM ech_groups WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('DB Error');
		$stmt->bind_param('i', $group_id);
		$stmt->execute();
		
		$stmt->bind_result($display, $perms);
		$stmt->fetch();
		$result = array($display, $perms);
		
		$stmt->close();
		return $result;
	}
	
	function getEchLogs($id, $game_id = NULL, $type = 'client') {
		$query = "SELECT log.id, log.type, log.msg, log.client_id, log.user_id, log.time_add, log.game_id, u.display, g.name_short 
				  FROM ech_logs log LEFT JOIN ech_users u ON log.user_id = u.id LEFT JOIN ech_games g ON log.game_id = g.id ";
		
		if($type == 'admin')
			$query .= "WHERE user_id = ?";
		else
			$query .= "WHERE client_id = ? AND game_id = ?";
			
		$query .= " ORDER BY log.time_add DESC";
			
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		if($type == 'admin')
			$stmt->bind_param('i', $id);
		else
			$stmt->bind_param('ii', $id, $game_id);
		$stmt->execute();
		
		$stmt->store_result();
		$stmt->bind_result($id, $type, $msg, $client_id, $user_id, $time_add, $game_id, $user_name, $name_short);
		
		while($stmt->fetch()) :
			$ech_logs[] = array(
				'id' => $id,
				'type' => $type,
				'msg' => $msg,
				'client_id' => $client_id,
				'user_id' => $user_id,
				'user_name' => $user_name,
				'game_id' => $game_id,
				'time_add' => $time_add,
				'name_short' => $name_short
			); 	
		endwhile;
		
		$stmt->close();
		return $ech_logs;
	}
	
	function addEchLog($type, $comment, $cid, $user_id, $game_id) {
		// id, type, msg, client_id, user_id, time_add, game_id
		$query = "INSERT INTO ech_logs VALUES(NULL, ?, ?, ?, ?, UNIX_TIMESTAMP(), ?)";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ssiii', $type, $comment, $cid, $user_id, $game_id);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		
		$stmt->close();
		
		if($affect == 1)
			return true;
		else
			return false;
	}
	
	/**
	 * Gets an array of the external links from the db
	 */
	function getLinks() {
		$query = "SELECT * FROM ech_links";
		
		$links = $this->query($query);
		
		return $links;
	}
	
	function setGroupPerms($group_id, $perms) {
		$query = "UPDATE ech_groups SET premissions = ? WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('DB Error');
		$stmt->bind_param('si', $perms, $group_id);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		
		$stmt->close();
		
		if($affect == 1)
			return true;
		else
			return false;
	}
	
	function addGroup($name, $slug, $perms) {
		$query = "INSERT INTO ech_groups VALUES(NULL, ?, ?, ?)";
		$stmt = $this->mysql->prepare($query) or die('DB Error');
		$stmt->bind_param('sss', $name, $slug, $perms);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		$stmt->close();
		
		if($affect == 1)
			return true;
		else	
			return false;
	}
	
	function delServer($id) {
		$query = "DELETE FROM ech_servers WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('DB Error');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		
		$affect = $stmt->affected_rows;
		$stmt->close();
		
		if($affect == 1)
			return true;
		else
			return false;
	}
	
	function gamesBanList() {
		$query = "SELECT id, name, db_host, db_user, db_pw, db_name FROM ech_games";
	
		$data = $this->query($query);
		
		return $data;
	}
	
	/**
	 * Checks if the game exsits and is active
	 */
	function isActiveGame($game) {
		$query = "SELECT SQL_CACHE id FROM ech_games WHERE id = $game  AND active = 1";
        $results = $this->mysql->query($query) or die('DB Error');
        return $results->fetch_object()->id > 0;
	}
	
	function getActiveGamesList() {
		$query = "SELECT SQL_CACHE id, name, name_short FROM ech_games WHERE active = 1 ORDER BY id ASC";
		$results = $this->mysql->query($query) or die('Database error');
		
		while($row = $results->fetch_object()) :	
			$games[] = array(
				'id' => $row->id,
				'name' => $row->name,
				'name_short' => $row->name_short
			);
		endwhile;
		return $games;
	}

} // end class