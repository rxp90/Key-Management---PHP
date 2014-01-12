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
			header ( "Location: ./correctSignup.php" );
		} else {
			header ( "Location: ../index.php" );
		}
		
		exit ();
	}
}

// Delete user
?>