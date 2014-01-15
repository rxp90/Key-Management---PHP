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
		}
		
		exit ();
	}
}
// Create room. The key is created automatically
if ($_GET ['action'] == 'createRoom') {
	if (isset ( $_POST ['createRoomSubmit'] )) {
		$number = $_POST ['roomNumber'];
		$building = $_POST ['buildingName'];
		
		if (createUser ( $email, $password, $name, $birthdate )) {
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
	if ($_POST ['edit']) {
		$id = $_POST ['editID'];
		$email = $_POST ['editEmail'];
		$name = $_POST ['editName'];
		$birthdate = $_POST ['editBirthdate'];
		$active = $_POST ['editActive'];
		$type = $_POST ['editType'];
		
		if (checkUserType ( 'ADMIN' ) || $id == $_SESSION ['user']->id) { // Either the user is an ADMIN or he's modifying his own profile
			if (editUser ( $id, $email, $name, $birthdate, $active, $type )) {
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
	if ($_POST ['edit']) {
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
		if (checkUserType ( 'ADMIN' )) { // Either the user is an ADMIN or he's NOT deleting his own profile
			deleteRoom ( $id );
		} else {
			// Not allowed to delete
		}
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