<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'dbl-class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');


/**
 * class DbL
 * desc: File to deal with all connections/queries to the Echelon Database 
 */ 

class DbL {

	private $mysql;

	function __construct() {
		$this->mysql = new mysqli(DBL_HOSTNAME, DBL_USERNAME, DBL_PASSWORD, DBL_DB) or die("Error Connecting to the Echelon Database");
		// connect to the database or die with an error
	}
	
	
	/**
	 * Gets an array of data for the settings form
	 *
	 * @param string $cat - category of settigs to retrieve
	 * @return array
	 */
	function getSettings($cat) {
        $query = "SELECT name, value FROM config WHERE category = ?";
        $stmt = $this->mysql->prepare($query) or die('DB Error');
		$stmt->bind_param('s', $cat);
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
	
	function getGamesInfo() {
	
		$query = "SELECT id, game_id, game, name, name_short, num_srvs, db_host, db_user, db_pw, db_name FROM games ORDER BY game_id ASC";
		$results = $this->mysql->query($query);
        
		$games = array();
		
		$i = 1;
        while($row = $results->fetch_object()) : // get results		
			$games[$i] = array(
				'id' => $row->id,
				'game_id' => $row->game_id,
				'game' => $row->game,
				'name' => $row->name,
				'name_short' => $row->name_short,
				'num_srvs' => $row->num_srvs,
				'db_host' => $row->db_host,
				'db_user' => $row->db_user,
				'db_pw' => $row->db_pw,
				'db_name' => $row->db_name
			);
			$i++; // increment counter
		endwhile;
		
		return $games;
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
        
		$query = "UPDATE config SET value = ? WHERE name = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query);
		$stmt->bind_param($value_type.'s', $value, $name);
		$stmt->execute();
		
		if($stmt->affected_rows > 0)
			return true;
		else
			return false;
		
    }
	
	/**
	 * Update game settings
	 *
	 * @return bool
	 */
    function setGameSettings($game, $name, $name_short, $db_user, $db_host, $db_name, $db_pw, $change_db_pw) {
		
		$query = "UPDATE games SET name = ?, name_short = ?, db_host = ?, db_user = ?, db_name = ?";
		
		if($change_db_pw) // if the DB password is to be chnaged
			$query .= ", db_pw = ?";

		$query .= " WHERE id = ? LIMIT 1";
			
		$stmt = $this->mysql->prepare($query);
		if($change_db_pw) // if chnage DB PW append var
			$stmt->bind_param('ssssssi', $name, $name_short, $db_host, $db_user, $db_name, $db_pw, $game);
		else // else var info not needed in bind_param
			$stmt->bind_param('sssssi', $name, $name_short, $db_host, $db_user, $db_name, $game);
		$stmt->execute();
		
		if($stmt->affected_rows > 0)
			return true;
		else
			return false;
		
    }
	
	function getServers($cur_game) {
		$query = "SELECT id, name, ip, pb_active, rcon_pass, rcon_ip, rcon_port FROM servers WHERE game = ?";
		$stmt = $this->mysql->prepare($query);
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
		$query = "SELECT game, name, ip, pb_active, rcon_pass, rcon_ip, rcon_port FROM servers WHERE id = ?";
		$stmt = $this->mysql->prepare($query);
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
			
		$query = "SELECT s.id, s.name, s.ip, s.game, s.pb_active, g.name as g_name FROM servers s LEFT JOIN games g ON s.game = g.game_id ORDER BY ".$orderby." ".$order;
		$result = $this->mysql->query($query);

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
		
		return $servers;
	}
	
	/**
	 * Update server settings
	 *
	 * @return bool
	 */
    function setServerSettings($server_id, $name, $ip, $pb, $rcon_ip, $rcon_port, $rcon_pw, $change_rcon_pw) {
		
		$query = "UPDATE servers SET name = ?, ip = ?, pb_active = ?, rcon_ip = ?, rcon_port = ?";
		
		if($change_rcon_pw) // if the DB password is to be chnaged
			$query .= ", rcon_pass = ?";

		$query .= " WHERE id = ? LIMIT 1";
			
		$stmt = $this->mysql->prepare($query);
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
		$query = "INSERT INTO servers VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)";
			
		$stmt = $this->mysql->prepare($query) or die('DB Error');
		$stmt->bind_param('ississi', $game_id, $name, $ip, $pb, $rcon_pw, $rcon_ip, $rcon_port);
		$stmt->execute();
		
		if($stmt->affected_rows > 0)
			return true;
		else
			return false;	
    }
	
	/**
	 * After adding a server we need to update the games table to add 1 to num_srvs
	 */
	function addServerUpdateGames($game_id) {
		$query = "UPDATE games SET num_srvs = (num_srvs + 1) WHERE game_id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('DB Error');;
		$stmt->bind_param('i', $game_id);
		$stmt->execute();
	}
	
	function getPlugins($game) {
	
		$query = "SELECT id, name, title, info FROM plugins WHERE game_id = ? AND enabled = 1";
		$stmt = $this->mysql->prepare($query) or die('DB Error');
		$stmt->bind_param('i', $game);
		$stmt->execute();
		
		$stmt->bind_result($id, $name, $title, $info);
		
		while($stmt->fetch()) :
			$plugins[$name] = array(
				'id' => $id,
				'title' => $title,
				'info' => $info,
				'enabled' => 1 // it must be enabled because the query said so
			);
		endwhile;
		
		$stmt->close();
		return $plugins;
	
	}
	
	function getGamesList() {
		$query = "SELECT game_id, name FROM games ORDER BY game_id ASC";
		$results = $this->mysql->query($query) or die('Database error');
		
		while($row = $results->fetch_object()) :	
			$games[] = array(
				'id' => $row->game_id,
				'name' => $row->name
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
		
		$query = "SELECT salt FROM users WHERE username = ? LIMIT 1";
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
	function getPermissions() {
		$query = "SELECT id, name, description FROM permissions ORDER BY id DESC";
		$results = $this->mysql->query($query);
		
		while($row = $results->fetch_object()) : // get results		
			$perms[] = array(
				'id' => $row->id,
				'name' => $row->name,
				'desc' => $row->description
			);
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

		$query = "SELECT u.id, u.ip, u.last_seen, u.display, u.email, u.ech_group, g.premissions FROM users u LEFT JOIN groups g ON u.ech_group = g.id WHERE u.username = ? AND u.password = ? LIMIT 1";
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
		$query = "UPDATE users SET ip = ?, last_seen = ? WHERE id = ?";
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

		$query = "SELECT ip FROM blacklist WHERE ip = ? AND active = 1";
		$stmt = $this->mysql->prepare($query) or die('Database error');
		$stmt->bind_param('s', $ip);
		$stmt->execute();
		
		$stmt->store_result(); // store results
		$stmt->bind_result($bl_ip); // store results
		
		if($stmt->num_rows): // if there is a blacklist
		
			while($stmt->fetch()) :
				if($ip == $bl_ip) // if ip mathces one on BL return true
					return true; // IP on BL
				else
					return false; // IP NOT on BL
			endwhile; // end loop through list
			
		else: // if no ban list just return false
			return false; // theres no blacklist so no one can be on it	
		endif;
		
		$stmt->free_result();
		$stmt->close();
		
	}

	/**
	 * Blacklist a user's IP address
	 *
	 * @param string $ip - IP address you wish to ban
	 * @param string $comment [optional] - Comment about reason for ban
	 */
	function blacklist($ip, $comment = 'Auto Added Ban') { // add an Ip to the blacklist
		$comment = htmlentities(strip_tags($comment));
		// id, ip, active, reason, time_add, admin_id
		$query = "INSERT INTO blacklist VALUES(NULL, ?, 1, ?, UNIX_TIMESTAMP(), 0)";
		$stmt = $this->mysql->prepare($query) or die('Database error');
		$stmt->bind_param('ss', $ip, $comment);
		$stmt->execute(); // run query
		
		$stmt->close();
	}
	
	/**
	 * Get an array of data about the Blacklist for a table
	 *
	 * @return array
	 */
	function getBL() { // return the rows of info for the BL table

		$query = "SELECT bl.id, bl.ip, bl.active, bl.reason, bl.time_add, u.display 
					FROM blacklist bl LEFT JOIN users u ON bl.admin_id = u.id 
					ORDER BY active ASC";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->execute();

		$stmt->bind_result($id, $ip, $active, $reason, $time_add, $admin_name); // store results
		
		while($stmt->fetch()) : // get results		
			$bls[] = array(
				'id' => $id,
				'ip' => $ip,
				'reason' => $reason,
				'active' => $active,
				'time_add' => $time_add,
				'admin' => $admin_name	
			);
		endwhile;

		$stmt->close();
		return $bls;
		
	}
	
	/**
	 * This function adds a new ban to the BL thats is added by a user
	 *
	 * @param string $ip - IP address to ban
	 * @param string $reason - reason to ban user
	 * @param int $admin - id of the admin who banned the user
	 * @return true/false
	 */
	function addBlBan($ip, $reason, $admin) {
		$time = time();
							// id, ip, active, reason, time_add, admin_id
		$query = "INSERT INTO blacklist VALUES(NULL, ?, 1, ?, ?, ?)";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('ssii', $ip, $reason, $time, $admin);
		$stmt->execute(); // run query
		if($stmt->affected_rows)
			return true;
		else
			return false;
		
		$stmt->close(); // close connection
	}
	
	/**
	 * De/Re-actives a Blacklist Ban
	 *
	 * @param int $id - id of the ban
	 * @param bool $active - weather to activate or deactivate
	 */
	function BLactive($id, $active) {

		if($active == true) // true turns on
			$active = 1;
		else // if false turn off
			$active = 0;

		$query = "UPDATE blacklist SET active = ? WHERE id = ? LIMIT 1";
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

		$query = "SELECT u.id, u.display, u.email, u.ip, u.first_seen, u.last_seen, g.display FROM users u LEFT JOIN groups g ON u.ech_group = g.id ORDER BY id ASC";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->execute();
		$stmt->bind_result($id, $display, $email, $ip, $first_seen, $last_seen, $group); // store results
		
		while($stmt->fetch()) : // get results
			$users[] = array(
				'id' => $id,
				'display' => $display,
				'email' => $email,
				'ip' => $ip,
				'first_seen' => $first_seen,
				'last_seen' => $last_seen,
				'group' => $group
			); 		
		endwhile;
		
		$stmt->free_result();
		$stmt->close();
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
				  FROM user_keys k LEFT JOIN users u ON k.admin_id = u.id
				  WHERE k.active = 1  AND k.time_add < ? AND comment != 'PW' ORDER BY time_add ASC";
		$stmt = $this->mysql->prepare($query) or die('Database Error: '. $this->mysql->error);
		$stmt->bind_param('i', $expires);
		$stmt->execute();
		
		$stmt->bind_result($key, $email, $comment, $time_add, $admin_id, $admin); // store results
		
		while($stmt->fetch()) : // get results
			$reg_keys[] = array(
				'reg_key' => $key,		
				'email' => $email,
				'comment' => $comment,
				'time_add' => $time_add,
				'admin_id' => $admin_id,
				'admin' => $admin
			); 		
		endwhile;
		
		$stmt->free_result();
		$stmt->close();
		return $reg_keys;

	}
	
	/**
	 * Check a username is not already in use
	 *
	 * @param string $username - username to check
	 * @return bool
	 */
	function checkUsername($username) {

		$query = "SELECT username FROM users WHERE username = ?";
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
		
		$query = "SELECT salt FROM users WHERE id = ? LIMIT 1";
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
		$query = "SELECT id FROM users WHERE id = ? AND password = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('is', $user_id, $hash_pw);
		$stmt->execute();
		$stmt->store_result(); // needed to allow num_rows to buffer
		
		if($stmt->num_rows == 1) {
			return true; // the person exists
		} else {
			return false; // person does not exist
		}
	
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
		$query = "UPDATE users SET display = ?, email = ? WHERE id = ? LIMIT 1";
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
		$query = "UPDATE users SET password = ?, salt = ? WHERE id = ? LIMIT 1";
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
		$query = "INSERT INTO user_keys VALUES(?, ?, ?, ?, ?, ?, 1)";
		$stmt = $this->mysql->prepare($query) or die('Database Error'. $this->mysql->error);
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
	
		$query = "DELETE FROM user_keys WHERE reg_key = ? LIMIT 1";
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
	
		$query = "UPDATE user_keys SET comment = ? WHERE reg_key = ? AND admin_id = ? LIMIT 1";
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
	
		$query = "SELECT reg_key, email FROM user_keys WHERE reg_key = ? AND email = ? AND active = 1 AND time_add < ? LIMIT 1";
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
	
		$query = "SELECT ech_group, admin_id FROM user_keys WHERE reg_key = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error: '. $this->mysql->error);
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
	
		$query = "SELECT admin_id FROM user_keys WHERE reg_key = ? LIMIT 1";
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
		} else {
			return false;
		}
	}
	
	/**
	 * Gets an array of data for the settings form
	 *
	 * @param string $key - the key to deactive
	 * @return bool
	 */
	function deactiveKey($key) {
		$query = "UPDATE user_keys SET active = 0 WHERE reg_key = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('s', $key);
		$stmt->execute();
		
		if($stmt->affected_rows)
			return true;
		else
			return false;
		
		$stmt->close();
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
		$query = "INSERT INTO users VALUES(NULL, ?, ?, ?, ?, ?, NULL, ?, ?, ?, NULL)";
		$stmt = $this->mysql->prepare($query) or die('Database Error'. $this->mysql->error);
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
		$query = "DELETE FROM users WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error');
		$stmt->bind_param('i', $user_id);
		$stmt->execute();
		
		if($stmt->affected_rows == 1)
			return true;
		else
			return false;
	
		$stmt->close();
	}
	
	function editUser($id, $username, $display, $email, $ech_group) {
		$query = "UPDATE users SET username = ?, display = ?, email = ?, ech_group = ? WHERE id = ? LIMIT 1";
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
				  FROM users u LEFT JOIN users a ON u.admin_id = a.id WHERE u.id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error '. $this->mysql->error);
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
		$query = "SELECT username, display, email, ech_group FROM users WHERE id = ? LIMIT 1";
		$stmt = $this->mysql->prepare($query) or die('Database Error '. $this->mysql->error);
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
	
		$query = "SELECT id FROM users WHERE username = ? AND email = ? LIMIT 1";
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
		$query = "SELECT id, display FROM groups ORDER BY id ASC";
		$stmt = $this->mysql->prepare($query);
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
	
	function getEchLogs($client_id) {
		$query = "SELECT l.id, l.type, l.msg, l.user_id, u.display, l.time_add FROM ech_logs l LEFT JOIN users u ON l.user_id = u.id WHERE client_id = ? ORDER BY time_add DESC";
		$stmt = $this->mysql->prepare($query);
		$stmt->bind_param('i', $client_id);
		$stmt->execute();
		
		$stmt->store_result();
		$stmt->bind_result($id, $type, $msg, $user_id, $user_name, $time_add);
		
		while($stmt->fetch()) :
			$ech_logs[] = array(
				'id' => $id,
				'type' => $type,
				'msg' => $msg,
				'user_id' => $user_id,
				'user_name' => $user_name,
				'time_add' => $time_add,
			); 	
		endwhile;
		
		$stmt->close();
		return $ech_logs;
	
	}
	
	function addEchLog($type, $comment, $cid, $user_id) {
		// id, type, msg, client_id, user_id, time_add
		$query = "INSERT INTO ech_logs VALUES(NULL, ?, ?, ?, ?, UNIX_TIMESTAMP())";
		$stmt = $this->mysql->prepare($query) or die('MySQL Error: '. $this->mysql->error);
		$stmt->bind_param('ssii', $type, $comment, $cid, $user_id);
		$stmt->execute();
		
		if($stmt->affected_rows )
			return true;
		else
			return false;
	
	}

} // end class