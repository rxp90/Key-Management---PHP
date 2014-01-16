<?php
include ("./Functions.php");

// Create account
if ($_GET ['action'] == 'signup') {
	if (isset ( $_POST ['signupSubmit'] )) {
		$email = $_POST ['signupEmail'];
		$password = $_POST ['signupPassword'];
		$name = $_POST ['signupName'];
		$birthdate = $_POST ['signupBirthdate'];
		
		if (createUser ( $email, $password, $name, $birthdate )) {
			header ( "Location: ./correctOperation.php" );
		} else {
			header ( "Location: ../index.php" );
			// Usar hashChange para redirigir bien y simular clic
		}
		
		exit ();
	}
}
// Create room. The key is created automatically
if ($_GET ['action'] == 'createRoom') {
	if (isset ( $_POST ['createRoomSubmit'] )) {
		$number = $_POST ['roomNumber'];
		$building = $_POST ['buildingName'];
		$type = $_POST ['roomType'];
		if (createRoom ( $number, $building, $type )) {
			header ( "Location: ./correctOperation.php" );
		} else {
			header ( "Location: ../index.php" );
		}
		
		exit ();
	}
}
// Log in
if ($_GET ['action'] == 'login') {
	if (isset ( $_POST ['loginSubmit'] )) {
		$email = $_POST ['loginEmail'];
		$password = $_POST ['loginPassword'];
		if (checkPassword ( $email, $password )) {
			header ( "Location: ../index.php" );
		} else {
			// header ( "Location: ../index.php" );// Wrong password
		}
		
		exit ();
	}
}

// Edit profile
if ($_GET ['action'] == 'editProfile') {
	if (isset ( $_POST ['edit'] )) {
		$id = $_POST ['editID'];
		$email = $_POST ['editEmail'];
		$name = $_POST ['editName'];
		$birthdate = $_POST ['editBirthdate'];
		$active = $_POST ['editActive'];
		$type = $_POST ['editType'];
		$access = $_POST ['access'];
		if (checkUserType ( 'ADMIN' ) || $id == $_SESSION ['user']->id) { // Either the user is an ADMIN or he's modifying his own profile
			if (editUser ( $id, $email, $name, $birthdate, $active, $type, $access )) {
				header ( "Location: ../index.php" ); // Edited correctly
			} else {
				header ( "Location: ../index.php" ); // Error
			}
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
		
		if (checkUserType ( 'ADMIN' ) && editRoom ( $id, $number, $building )) {
			header ( "Location: ../index.php" ); // Edited correctly
		} else {
			header ( "Location: ../index.php" ); // Error
		}
		
		exit ();
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
	} else {
		// Key not available
	}
}
// Transfer key
if (strpos ( $_GET ['action'], 'transferKey?id=' ) !== false) { // Not the best way...
	
	$parameters = explode ( '?', $_GET ['action'] );
	$values = explode ( '=', $parameters );
	$id = $values [1];
	$uid = $values [3];
	if (transferKey ( $keyId, $userID )) {
		// Correct
	} else {
		// Error
	}
}
// Return key
if (strpos ( $_GET ['action'], 'returnKey?id=' ) !== false) { // Not the best way...
	
	$pieces = explode ( '=', $_GET ['action'] );
	$id = $pieces [1];
	if (returnKey ( $id )) {
		// Correct
	} else {
		// Error
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