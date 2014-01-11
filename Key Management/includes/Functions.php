<?php
include_once ("./includes/DatabaseConnection.php");
class Functions {
	protected $_connection;
	public function __construct() {
		$this->_connection = DatabaseConnection::getInstance ()->getHandler ();
	}
	public function createUser($email, $password, $name, $birthdate, $photo) {
		$handle = $this->_connection->prepare ( "insert into users(id,name,birthdate,email,password,photo) values (null,?,?,?,?,?,?)" );
		
		$handle->bindValue ( 1, $name );
		$handle->bindValue ( 2, $birthdate );
		$handle->bindValue ( 3, $email );
		$handle->bindValue ( 4, crypt ( $password ) );
		$handle->bindValue ( 5, $photo, PDO::PARAM_LOB );
		
		$this->_connection->beginTransaction ();
		$handle->execute ();
		$this->_connection->commit ();
	}
	public function deleteUser($id) {
	}
	public function getConnection() {
		$this->_connection = new Functions ();
		return $this->_connection;
	}
}
?>