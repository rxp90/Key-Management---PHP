<div>DATABASECONNECTION</div>
<?php
class DatabaseConnection {
	private static $instance;
	private $_connection;
	private function __construct() {
		$this->_connection = new \PDO ( 'mysql:host=localhost;dbname=key_management', 'root', '', array (
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_PERSISTENT => false,
				\PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8mb4' 
		) );
	}
	public static function getInstance() {
		if (! self::$instance instanceof self) {
			self::$instance = new self ();
		}
		return self::$instance;
	}
	public function getHandler() {
		return $this->_connection;
	}
}
?>