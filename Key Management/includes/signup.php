<?php
include_once ("./includes/Functions.php");

$db = Functions::getConnection ();
if (isset ( $_POST ['signupSubmit'] )) {
	echo 'HOLAAAAAAAA';
	$email = $_POST ['signupEmail'];
	$password = $_POST ['signupPassword'];
	$name = $_POST ['signupName'];
	$birthdate = $_POST ['signupBirthdate'];
	$photo = $_POST ['$signupPhoto'];
}
?>