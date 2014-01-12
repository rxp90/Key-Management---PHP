<?php
function connect() {
	$pdo = new \PDO ( 'mysql:host=localhost;dbname=key_management', 'root', '', array (
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_PERSISTENT => false,
			\PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8mb4' 
	) );
	return $pdo;
}
function createUser($email, $password, $name, $birthdate) {
	try {
		$pdo = connect ();
		// PHP will automatically close the connection when your script ends.
		$error = array ();
		
		if (! isset ( $_SESSION )) { // Not sure if necessary
			session_start ();
		}
		
		// Check if email exists
		
		$stmt = $pdo->prepare ( 'select * from users where email = :email' );
		$stmt->bindValue ( ':email', $email );
		$stmt->execute ();
		
		if ($stmt->rowCount () > 0) { // Duplicate email
			
			$error ['email'] = 'That email already exists, choose another one';
			
			// Add errors and previous values to SESSION variable
			$_SESSION ['error'] = $error;
			
			$_SESSION ['signupEmail'] = $email;
			$_SESSION ['signupName'] = $name;
			$_SESSION ['signupBirthdate'] = $birthdate;
			
			return false; // Registration failed
		} else {
			
			unset ( $_SESSION ['error'] );
			
			$stmt = $pdo->prepare ( 'insert into users(id,name,birthdate,email,password,active,type) values (null,:name,:birthdate,:email,:password,:active,:type)' );
			
			$stmt->bindValue ( ':name', $name );
			$stmt->bindValue ( ':birthdate', $birthdate );
			$stmt->bindValue ( ':email', $email );
			$stmt->bindValue ( ':password', crypt ( $password ) );
			$stmt->bindValue ( ':active', true, PDO::PARAM_BOOL );
			$stmt->bindValue ( ':type', 'REGULAR' );
			
			$pdo->beginTransaction ();
			$stmt->execute ();
			$pdo->commit ();
			return true; // Registration succeded
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function deleteUser($id) {
	try {
		$pdo = $pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) { // Not sure if necessary
			session_start ();
		}
		
		// Check if user exists
		
		$stmt = $pdo->prepare ( 'select * from users where id = :id' );
		$stmt->bindValue ( ':id', $id );
		$stmt->execute ();
		
		if ($stmt->rowCount () > 0) { // The user exists
			$stmt = $pdo->prepare ( 'delete from users where id = :id' );
			$stmt->bindValue ( ':id', $id );
			$stmt->execute ();
			return true; // User deleted
		} else {
			
			// User doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function checkPassword($email, $password) {
	try {
		$pdo = $pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) { // Not sure if necessary
			session_start ();
		}
		
		// Check if user exists
		
		$stmt = $pdo->prepare ( 'select * from users where email = :email and password := password' );
		$stmt->bindValue ( ':email', $email );
		$stmt->bindValue ( ':password', crypt ( $password ) );
		$stmt->execute ();
		
		if ($stmt->rowCount () > 0) { // User and pass both correct
			session_regenerate_id ( true ); // to help defend against session fixation and login CSRF
		} else {
			
			// User doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
?>