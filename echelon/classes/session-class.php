<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'session-class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die('Please do not load this page directly. Thanks!');

class Session {

        /**
         * This function starts, validates and secures a session.
         *
         * @param string $name The name of the session.
         * @param int $limit Expiration date of the session cookie, 0 for session only
         * @param string $path Used to restrict where the browser sends the cookie
         * @param string $domain Used to allow subdomains access to the cookie
         * @param bool $secure If true the browser only sends the cookie over https
         */
        static function sesStart($name = 'echelon', $limit = 0, $path = '/', $domain = null, $secure = null) {
		
			// Set the cookie name
			session_name($name . '_session_' . SES_SALT);

			// Set SSL level
			$https = isset($secure) ? $secure : detectSSL();
			
			// Set session cookie options
			// httpOnly is set to true // this can help prevent identiy theft with XSS hacks
			session_set_cookie_params($limit, $path, $domain, $https, true);
			session_start();
			
			// Make sure the session hasn't expired, and destroy it if it has
			if(self::validateSession()) {
			
				// Check to see if the session is new or a hijacking attempt
				if(!self::preventHijacking()) {
				
						// Reset session data and regenerate id
						$_SESSION['finger'] = self::getFinger();
						self::regenerateSession();

				// Give a 20% chance of the session id changing on any request
				} elseif(mt_rand(1, 100) <= 20) {
						self::regenerateSession();
				}
			} else {
				// logout and send to home page
				self::logout();
				sendHome();
			}
				
        } // end sesStart

        /**
         * This function regenerates a new ID and invalidates the old session. This should be called whenever permission
         * levels for a user change.
         *
         */
        static function regenerateSession() {
		
			// If this session is obsolete it means there already is a new id
			if(isset($_SESSION['OBSOLETE']) || $_SESSION['OBSOLETE'] == true)
				return;

			// Set current session to expire in 10 seconds
			$_SESSION['OBSOLETE'] = true;
			$_SESSION['EXPIRES'] = time() + 10;

			// Create new session without destroying the old one
			session_regenerate_id(false);

			// Grab current session ID and close both sessions to allow other scripts to use them
			$newSession = session_id();
			session_write_close();

			// Set session ID to the new one, and start it back up again
			session_id($newSession);
			session_start();

			// Now we unset the obsolete and expiration values for the session we want to keep
			unset($_SESSION['OBSOLETE']);
			unset($_SESSION['EXPIRES']);
        }

        /**
         * This function is used to see if a session has expired or not.
         *
         * @return bool
         */
        static protected function validateSession() {

			if(isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES']))
				return false;

			if(isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time())
				return false;

			return true;
        }

        /**
         * This function checks to make sure a session exists and is coming from the proper host. On new visits and hacking
         * attempts this function will return false.
         *
         * @return bool
         */
        static protected function preventHijacking() {
	
			if(!isset($_SESSION['finger']))
				return false;

			if($_SESSION['finger'] != self::getFinger())
				return false;

			return true;
        }
		
		/**
		 * Logs a user out
		 */
		static public function logout() {

			$error = $_SESSION['error']; // perserve errors if the person is loggedout by error

			$_SESSION = array(); // unsets all varibles

			// If it's desired to kill the session, also delete the session cookie.
			// Note: This will destroy the session, and not just the session data!
			if (isset($_COOKIE[session_name()])) {
			   setcookie(session_name(), '', time()-420, PATH);
			}
			
			// This is useful for when you change authentication states as it also invalidates the old session. 
			self::regenerateSession();
			
			// Finally, destroy the session.
			session_destroy();

			self::sesStart(); // start session
			$_SESSION['error'] = $error; // add error to new session
			
		}
		
		/**
		 * Generates a fingerprint for anti session hijacking
		 *
		 * @return string
		 */
		static function getFinger() {
			$user_agent = $_SERVER['HTTP_USER_AGENT']; // get browser name from user
			return genHash($user_agent.SALT); // return hash of browser and salt
		}

		
} // end class