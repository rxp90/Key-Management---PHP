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
function editUser($id, $email, $name, $birthdate, $active, $type) {
	try {
		$pdo = $pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) { // Not sure if necessary
			session_start ();
		}
		
		// Check if email is changed and if so, if already exists
		
		$stmt = $pdo->prepare ( 'select * from users where id = :id' );
		$stmt->bindValue ( ':id', $id );
		$stmt->execute ();
		
		$emailDuplicatedQuery = $pdo->prepare ( 'select * from users where email = :email' );
		$emailDuplicatedQuery->bindValue ( ':email', $email );
		$emailDuplicatedQuery->execute ();
		
		if ($stmt->rowCount () > 0) { // User exists
			$user = $stmt->fetchobject ();
			if ($user->email != $email && $emailDuplicatedQuery->rowCount () > 0) { // Email changed and duplicated
				$error ['email'] = 'That email already exists, choose another one';
				
				// Add errors and previous values to SESSION variable
				$_SESSION ['error'] = $error;
				$_SESSION ['editID'] = $id;
				$_SESSION ['editEmail'] = $email;
				$_SESSION ['editName'] = $name;
				$_SESSION ['editBirthdate'] = $birthdate;
				$_SESSION ['editActive'] = $active;
				$_SESSION ['editType'] = $type;
				return false;
			} else { // Email not changed or not duplicated
				
				unset ( $_SESSION ['error'] );
				
				$update = $pdo->prepare ( 'UPDATE users SET name = :name, email = :email, birthdate = :birthdate, active = :active, type = :type WHERE id = :id' );
				
				$update->bindValue ( ':id', $id );
				$update->bindValue ( ':name', $name );
				$update->bindValue ( ':birthdate', $birthdate );
				$update->bindValue ( ':email', $email );
				if (checkUserType ( 'ADMIN' )) { // Only an ADMIN can change this values
					$update->bindValue ( ':active', $active, PDO::PARAM_BOOL );
					$update->bindValue ( ':type', $type );
				} else {
					$update->bindValue ( ':active', $_SESSION ['user']->active, PDO::PARAM_BOOL );
					$update->bindValue ( ':type', $_SESSION ['user']->type );
				}
				$return = $update->execute ();
				
				if ($return && $id == $_SESSION ['user']->id) { // If the user was updated and is his own profile, refresh the session variable
					$getNewUser = $pdo->prepare ( 'select * from users where id = :id' );
					$getNewUser->bindValue ( ':id', $id );
					$getNewUser->execute ();
					$user = $getNewUser->fetchobject ();
					$_SESSION ['user'] = $user; // Update the user in the session
				}
				return $return;
			}
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function editRoom($id, $number, $building) {
	try {
		$pdo = $pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) { // Not sure if necessary
			session_start ();
		}
		
		// Check if number-building already exists
		
		$stmt = $pdo->prepare ( 'select * from rooms where id = :id' );
		$stmt->bindValue ( ':id', $id );
		$stmt->execute ();
		
		$roomDuplicatedQuery = $pdo->prepare ( 'select * from rooms where number = :number AND building = :building' );
		$roomDuplicatedQuery->bindValue ( ':number', $number );
		$roomDuplicatedQuery->bindValue ( ':building', $building );
		
		$roomDuplicatedQuery->execute ();
		
		if ($stmt->rowCount () > 0) { // Room exists
			$room = $stmt->fetchobject ();
			if ($room->number != $number || $room->building != $building && $roomDuplicatedQuery->rowCount () > 0) { // Number/building changed and duplicated
				$error ['roomNumber'] = 'That room already exists in that building. Change either the building or the number';
				
				// Add errors and previous values to SESSION variable
				$_SESSION ['error'] = $error;
				
				$_SESSION ['roomNumber'] = $number;
				$_SESSION ['buildingName'] = $building;
				return false;
			} else { // Correct
				
				unset ( $_SESSION ['error'] );
				
				$update = $pdo->prepare ( 'UPDATE rooms SET number = :number, building = :building WHERE id = :id' );
				
				$update->bindValue ( ':number', $number );
				$update->bindValue ( ':building', $building );
				
				$pdo->beginTransaction ();
				$update->execute ();
				$pdo->commit ();
				
				return true; // Registration succeded
			}
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
			return ($stmt->execute ());
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
		
		$stmt = $pdo->prepare ( 'select * from users where email = :email' );
		$stmt->bindValue ( ':email', $email );
		$stmt->execute ();
		
		if ($stmt->rowCount () > 0) { // User exists
			$user = $stmt->fetchobject ();
			if ($user->password == crypt ( $password, $user->password )) {
				session_regenerate_id ( true ); // to help defend against session fixation and login CSRF
				$_SESSION ['user'] = $user;
				return true;
			}
		} else {
			
			// User doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function getNameFromID($id) {
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
		if ($stmt->rowCount () == 1) {
			$result = $stmt->fetchobject ();
			return $result->name;
		} else {
			
			// User doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function getRoomFromKeyID($keyid) {
	try {
		$pdo = $pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) { // Not sure if necessary
			session_start ();
		}
		
		$stmt = $pdo->prepare ( 'select * from rooms where keys_id = :id' );
		$stmt->bindValue ( ':id', $keyid );
		$stmt->execute ();
		if ($stmt->rowCount () == 1) {
			$result = $stmt->fetchobject ();
			$fullname = $result->number . ' - Building: ' . $result->building;
			return $fullname;
		} else {
			// Key doesn't exist
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function getKeyLogs() {
	try {
		$pdo = $pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) { // Not sure if necessary
			session_start ();
		}
		
		// Check if the user is a doorman
		// $user = $_SESSION ['user'];
		// if ($user->type == 'DOORMAN') {
		$stmt = $pdo->prepare ( 'select * from log where active = 1' );
		$stmt->execute ();
		if ($stmt->rowCount () == 1) {
			$result = $stmt->fetchAll ();
			return $result;
		} else {
			// Key doesn't exist
			return false;
		}
		// } else {
		// NOT ALLOWED
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function printKeyLog() {
	echo '<table class="table table-striped">
				<thead>
					<tr>
						<th>Key</th>
						<th>Name</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>';
	$result = getKeyLogs ();
	foreach ( $result as $row ) :
		?>
<tr>
	<td><?php echo getRoomFromKeyID($row['keys_id']); ?></td>
	<td><?php echo getNameFromID($row['Users_id']); ?></td>
	<td><?php echo $row['time']; ?></td>
</tr>
<?php
	endforeach
	;
	echo '</tbody>
				</table>';
}
function checkUserType($type) {
	if (! isset ( $_SESSION )) { // Not sure if necessary
		session_start ();
	}
	
	if (isset ( $_SESSION ['user'] ) && $_SESSION ['user']->type == $type) {
		return true;
	} else {
		return false; // Not logged in or wrong type
	}
}
function logout() {
	// Initialize the session.
	session_start ();
	
	// Unset all of the session variables.
	$_SESSION = array ();
	
	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get ( "session.use_cookies" )) {
		$params = session_get_cookie_params ();
		setcookie ( session_name (), '', time () - 42000, $params ["path"], $params ["domain"], $params ["secure"], $params ["httponly"] );
	}
	
	// Finally, destroy the session.
	session_destroy ();
}
function getUsers() {
	if (checkUserType ( 'ADMIN' )) {
		try {
			$pdo = $pdo = connect ();
			$error = array ();
			
			if (! isset ( $_SESSION )) { // Not sure if necessary
				session_start ();
			}
			
			$stmt = $pdo->prepare ( 'select * from users where id != :id' );
			$stmt->bindValue ( ':id', $_SESSION ['user']->id );
			$stmt->execute ();
			
			$result = $stmt->fetchAll ();
			return $result;
		} catch ( PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
}
function getRooms() {
	if (checkUserType ( 'ADMIN' )) {
		try {
			$pdo = $pdo = connect ();
			$error = array ();
			
			if (! isset ( $_SESSION )) { // Not sure if necessary
				session_start ();
			}
			
			$stmt = $pdo->prepare ( 'select * from rooms where 1' );
			$stmt->execute ();
			
			$result = $stmt->fetchAll ();
			return $result;
		} catch ( PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
}
function printUserList() {
	echo '<table class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Email</th>
						<th>Name</th>
						<th>Type</th>
					</tr>
				</thead>
				<tbody>';
	$result = getUsers ();
	foreach ( $result as $row ) :
		?>
<tr>
	<td><?php echo '<a href="#" onclick="loadUserForm('.$row['id'].')">Edit</a>'; ?></td>
	<td><?php echo $row['email']; ?></td>
	<td><?php echo $row['name']; ?></td>
	<td><?php echo $row['type']; ?></td>

</tr>
<?php
	endforeach
	;
	echo '</tbody>
				</table>';
}
function printRoomList() {
	echo '<table class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Number</th>
						<th>Building</th>
						<th>Key</th>
					</tr>
				</thead>
				<tbody>';
	$result = getRooms ();
	foreach ( $result as $row ) :
		?>
<tr>
	<td><?php echo '<a href="#" onclick="loadRoomForm('.$row['id'].')">Edit</a>'; ?></td>
	<td><?php echo $row['number']; ?></td>
	<td><?php echo $row['building']; ?></td>
	<td><?php echo $row['keys_id']; ?></td>
</tr>
<?php
	endforeach
	;
	echo '</tbody>
				</table>';
}
function printAdminLinks() {
	if (checkUserType ( 'ADMIN' )) {
		echo '<li><a onclick="hideAjax()"
					href="#signup"
					data-toggle="pill"
				>Create account</a></li>';
		echo '<li class="ajaxList"><a
					href="#userList"
					data-toggle="pill"
				>Userlist</a></li>';
		echo '<li><a onclick="hideAjax()"
					href="#createRoom"
					data-toggle="pill"
				>Create room</a></li>';
		
		echo '<li class="ajaxList"><a
					href="#roomList"
					data-toggle="pill"
				>Room list</a></li>';
	}
}
function printDoormanLinks() {
	if (checkUserType ( 'DOORMAN' )) {
		echo '<li><a
			href="#keyLogs"
			data-toggle="pill"
		>Key logs</a></li>';
	}
}
function getUserByID($id) {
	try {
		$pdo = $pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		// Check if user exists
		
		$stmt = $pdo->prepare ( 'select * from users where id = :id' );
		$stmt->bindValue ( ':id', $id );
		$stmt->execute ();
		if ($stmt->rowCount () == 1) {
			$result = $stmt->fetchobject ();
			return $result;
		} else {
			
			// User doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function createRoom($number, $building) {
	try {
		$pdo = connect ();
		// PHP will automatically close the connection when your script ends.
		$error = array ();
		
		if (! isset ( $_SESSION )) { // Not sure if necessary
			session_start ();
		}
		
		// Check if (number-building) exists
		
		$stmt = $pdo->prepare ( 'select * from rooms where number = :number AND building = :building' );
		$stmt->bindValue ( ':number', $number );
		$stmt->bindValue ( ':building', $building );
		
		$stmt->execute ();
		
		if ($stmt->rowCount () > 0) { // Duplicate (number-building)
			
			$error ['roomNumber'] = 'That room already exists in that building. Change either the building or the number';
			
			// Add errors and previous values to SESSION variable
			$_SESSION ['error'] = $error;
			
			$_SESSION ['roomNumber'] = $number;
			$_SESSION ['buildingName'] = $building;
			
			return false; // Registration failed
		} else {
			
			unset ( $_SESSION ['error'] );
			
			$stmt = $pdo->prepare ( 'insert into keys(id) values (null)' );
			$pdo->beginTransaction ();
			$stmt->execute ();
			$pdo->commit;
			
			$key_id = $pdo->lastInsertId ();
			
			$stmt->bindValue ( ':number', $number );
			$stmt->bindValue ( ':building', $building );
			$stmt->bindValue ( ':keys_id', $key_id );
			
			$pdo->beginTransaction ();
			$stmt->execute ();
			$pdo->commit ();
			return true; // Registration succeded
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function getRoomByID($id) {
	try {
		$pdo = $pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		// Check if room exists
		
		$stmt = $pdo->prepare ( 'select * from rooms where id = :id' );
		$stmt->bindValue ( ':id', $id );
		$stmt->execute ();
		if ($stmt->rowCount () == 1) {
			$result = $stmt->fetchobject ();
			return $result;
		} else {
			
			// Room doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
?>