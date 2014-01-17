<?php
include ("./Functions.php");

// Create account
if ($_GET ['action'] == 'signup') {
	if (isset ( $_POST ['signupSubmit'] )) {
		$email = $_POST ['signupEmail'];
		$password = $_POST ['signupPassword'];
		$name = $_POST ['signupName'];
		createUser ( $email, $password, $name );
	}
}
// Create room. The key is created automatically
if ($_GET ['action'] == 'createRoom') {
	if (isset ( $_POST ['createRoomSubmit'] )) {
		$number = $_POST ['roomNumber'];
		$building = $_POST ['buildingName'];
		$type = $_POST ['roomType'];
		createRoom ( $number, $building, $type );
	}
}
// Log in
if ($_GET ['action'] == 'login') {
	if (isset ( $_POST ['loginSubmit'] )) {
		$email = $_POST ['loginEmail'];
		$password = $_POST ['loginPassword'];
		checkPassword ( $email, $password );
	}
}

// Edit profile
if ($_GET ['action'] == 'editProfile') {
	if (isset ( $_POST ['edit'] )) {
		$id = $_POST ['editID'];
		$email = $_POST ['editEmail'];
		$name = $_POST ['editName'];
		$active = $_POST ['editActive'];
		$type = $_POST ['editType'];
		$access = $_POST ['access'];
		if (checkUserType ( 'ADMIN' ) || $id == $_SESSION ['user']->id) { // Either the user is an ADMIN or he's modifying his own profile
			editUser ( $id, $email, $name, $active, $type, $access );
		} else {
		}
		exit ();
	} else if ($_POST ['delete']) {
		$id = $_POST ['editID'];
		if (checkUserType ( 'ADMIN' ) || $id != $_SESSION ['user']->id) { // Either the user is an ADMIN or he's NOT deleting his own profile
			deleteUser ( $id );
		} else {
			// Not allowed to delete
		}
	}
}
// Edit room
if ($_GET ['action'] == 'editRoom') {
	if (isset ( $_POST ['edit'] )) {
		$id = $_POST ['editID'];
		$number = $_POST ['roomNumber'];
		$building = $_POST ['buildingName'];
		$type = $_POST ['roomType'];
		
		if (checkUserType ( 'ADMIN' )) {
			editRoom ( $id, $number, $building, $type );
		}
	} else if ($_POST ['delete']) {
		$id = $_POST ['editID'];
		if (checkUserType ( 'ADMIN' )) {
			deleteRoom ( $id );
		} else {
			// Not allowed to delete
		}
	}
}
// Lend key
if (strpos ( $_GET ['action'], 'getKey?id=' ) !== false) { // Not the best way...
	
	$pieces = explode ( '=', $_GET ['action'] );
	$id = $pieces [1];
	if (isAvailable ( $id )) {
		addToLog ( $id, $_SESSION ['user']->id );
		header ( "Location: ../index.php#keyLogsUser" );
		exit ();
	} else {
		// Key not available
	}
}
// Transfer key
if ($_GET ['action'] == 'transferKey') {
	if (isset ( $_POST ['transferUser'] )) {
		$uid = $_POST ['transferUser'];
		$keyId = $_POST ['keyID'];
		if (transferKey ( $keyId, $uid )) {
			header ( "Location: ../index.php#keyLogsUser" );
			exit ();
		}
	}
}

// Return key
if (strpos ( $_GET ['action'], 'returnKey?id=' ) !== false) { // Not the best way...
	
	$pieces = explode ( '=', $_GET ['action'] );
	$id = $pieces [1];
	if (returnKey ( $id )) {
		header ( "Location: ../index.php#keyLogsUser" );
		exit ();
	}
}
// Logout
if ($_GET ['action'] == 'logout') {
	session_start ();
	if (isset ( $_SESSION ['user'] )) {
		
		logout ();
		
		header ( "Location: ../index.php" ); // Edited correctly
		
		exit ();
	}
}
?>