<?php
/**
 * Exceptions class for the B3 DB
 *
 */
class MysqlException extends Exception {

	private $error;
	private $errno;

	public function __construct($error, $errno) {
	
		$this->error = $error;
		$this->errno = $errno;

		// find error message and code
		$code = $this->errno;
		$message = $this->error;

		// log error message
		$log_success = echLog('mysql', $message, $code);

		// call parent constructor
		parent::__construct($message, $code);
	}

}