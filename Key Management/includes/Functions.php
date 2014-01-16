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
		if (checkUserType ( 'ADMIN' )) { // Only ADMINs can create accounts
			$pdo = connect ();
			// PHP will automatically close the connection when your script ends.
			$error = array ();
			
			if (! isset ( $_SESSION )) {
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
		} else {
			// NO-ADMIN USER TRYNG TO CREATE ACCOUNT
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function editUser($id, $email, $name, $birthdate, $active, $type, $access) {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
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
				// Update USERS table
				$pdo->beginTransaction ();
				
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
				$update->execute ();
				// Update ACCESS LIST
				$deleteAL = $pdo->prepare ( 'DELETE from access_list WHERE user_id = :id' );
				$deleteAL->bindValue ( ':id', $id );
				$deleteAL->execute ();
				
				$addAL = $pdo->prepare ( 'INSERT INTO access_list(access_type,user_id) VALUES (:access,:user_id)' );
				foreach ( $access as $element ) {
					$addAL->bindValue ( ':access', $element );
					$addAL->bindValue ( ':user_id', $id );
					$addAL->execute ();
				}
				
				$return = $pdo->commit ();
				
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
		$pdo->rollBack ();
	}
}
function editRoom($id, $number, $building, $type) {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
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
			if (($room->number != $number || $room->building != $building) && $roomDuplicatedQuery->rowCount () > 0) { // Number/building changed and duplicated
				$error ['roomNumber'] = 'That room already exists in that building. Change either the building or the number';
				
				// Add errors and previous values to SESSION variable
				$_SESSION ['error'] = $error;
				$_SESSION ['roomType'] = $type;
				$_SESSION ['roomNumber'] = $number;
				$_SESSION ['buildingName'] = $building;
				return false;
			} else { // Correct
				
				unset ( $_SESSION ['error'] );
				
				$update = $pdo->prepare ( 'UPDATE rooms SET number = :number, building = :building WHERE id = :id' );
				
				$update->bindValue ( ':number', $number );
				$update->bindValue ( ':building', $building );
				$update->bindValue ( ':id', $id );
				
				$updateKeyType = $pdo->prepare ( 'UPDATE room_key SET type = :type WHERE id = :id' );
				$updateKeyType->bindValue ( ':id', $room->keys_id );
				$updateKeyType->bindValue ( ':type', $type );
				
				$pdo->beginTransaction ();
				$update->execute ();
				$updateKeyType->execute ();
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
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
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
function deleteRoom($id) {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		
		// Check if rooms exists
		
		$stmt = $pdo->prepare ( 'select * from rooms where id = :id' );
		$stmt->bindValue ( ':id', $id );
		$stmt->execute ();
		
		if ($stmt->rowCount () > 0) { // The user exists
			$stmt = $pdo->prepare ( 'delete from rooms where id = :id' );
			$stmt->bindValue ( ':id', $id );
			return ($stmt->execute ());
		} else {
			
			// Rooms doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function checkPassword($email, $password) {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		
		// Check if user exists
		
		$stmt = $pdo->prepare ( 'select * from users where email = :email' );
		$stmt->bindValue ( ':email', $email );
		$stmt->execute ();
		
		if ($stmt->rowCount () > 0) { // User exists
			$user = $stmt->fetchobject ();
			if ($user->password == crypt ( $password, $user->password )) {
				if ($user->active == 1) {
					session_regenerate_id ( true ); // to help defend against session fixation and login CSRF
					$_SESSION ['user'] = $user;
					return true;
				} else { // User not active
				}
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
		$pdo = connect ();
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
			return $result->name;
		} else {
			
			// User doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function getAllowedKeys() {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		// Check if user exists
		
		$stmt = $pdo->prepare ( 'select * from users where id = :id' );
		$stmt->bindValue ( ':id', $_SESSION->id );
		$stmt->execute ();
		if ($stmt->rowCount () == 1) {
			$user = $stmt->fetchobject ();
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
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
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
function getKeyFromID($id) {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		// Check if user exists
		
		$stmt = $pdo->prepare ( 'select * from room_key where id = :id' );
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
function getUsersAllowed($keyID) {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		// Check if user exists
		$stmt = $pdo->prepare ( 'select * from room_key where id = :id' );
		$stmt->bindValue ( ':id', $id );
		$stmt->execute ();
		if ($stmt->rowCount () == 1) {
			$key = $stmt->fetchobject ();
			$type = $key->type;
			// Get al the users who have access to this type of key
			$stmt = $pdo->prepare ( 'select user_id from access_list where access_type = :type' );
			$stmt->bindValue ( ':type', $type );
			$stmt->execute ();
			if ($stmt->rowCount () > 0) {
				$usersID = $stmt->fetchAll ( PDO::FETCH_COLUMN, 0 );
				$inQuery = implode ( ',', array_fill ( 0, count ( $usersID ), '?' ) );
				
				$stmt = $pdo->prepare ( 'select * from users where id IN(' . $inQuery . ') and active = 1' );
				// bindvalue is 1-indexed, so $k+1
				foreach ( $usersID as $k => $id ) {
					$stmt->bindValue ( ($k + 1), $id );
				}
				$stmt->execute ();
				if ($stmt->rowCount () > 0) {
					$users = $stmt->fetchAll ();
					return $users;
				}
			}
		} else {
			
			// User doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function getKeyLogs() {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		
		if (checkUserType ( 'REGULAR' )) {
			$id = $_SESSION ['user']->id;
			$access_list = getAccessList ( $id );
			$query = 'SELECT id FROM room_key WHERE ';
			foreach ( $access_list as $item ) {
				$query .= ' type = \'' . $item . '\' OR ';
			}
			$query .= '0'; // In case of empty access list
			$stmt = $pdo->prepare ( $query );
			$stmt->execute ();
			if ($stmt->rowCount () > 0) {
				echo '
				<table class="table table-striped">
				<thead>
					<tr>
						<th>Key</th>
						<th>Available / User</th>
					</tr>
				</thead>
				<tbody>';
				// Keys in use
				$stmt->execute ();
				$accessibleKeys = $stmt->fetchAll ( PDO::FETCH_COLUMN, 0 );
				
				$keysInUseArray = array ();
				$inQuery = implode ( ',', array_fill ( 0, count ( $accessibleKeys ), '?' ) );
				
				$stmt = $pdo->prepare ( 'select * from log where keys_id IN(' . $inQuery . ') and active = 1' );
				// bindvalue is 1-indexed, so $k+1
				foreach ( $accessibleKeys as $k => $id ) {
					$stmt->bindValue ( ($k + 1), $id );
				}
				$stmt->execute ();
				
				if ($stmt->rowCount () > 0) {
					$keysInUse = $stmt->fetchAll ();
					foreach ( $keysInUse as $row ) {
						$keysInUseArray [] = $row ['keys_id'];
						?>
<tr>
	<td><?php echo getRoomFromKeyID($row['keys_id']); ?></td>
	<td><?php
						if ($_SESSION ['user']->id == $row ['Users_id']) {
							// The user has this key. He can either return the key
							echo '<a href="./includes/process.php?action=returnKey?id=' . $row ['keys_id'] . '">Return key</a>';
							// or transfer it
						} else {
							echo getNameFromID ( $row ['Users_id'] );
						}
						?></td>
</tr>
<?php
					}
				}
				// Accesible keys - Keys in use
				if (! empty ( $keysInUseArray )) {
					$queryAvailableKeys = array_diff ( $accessibleKeys, $keysInUseArray );
					if (! empty ( $queryAvailableKeys )) {
						$inQuery2 = implode ( ',', array_fill ( 0, count ( $queryAvailableKeys ), '?' ) );
						$stmt2 = $pdo->prepare ( 'select * from room_key where id IN(' . $inQuery2 . ')' );
						// bindvalue is 1-indexed, so $k+1
						foreach ( $queryAvailableKeys as $z => $id ) {
							$stmt2->bindValue ( ($z + 1), $id );
						}
						$stmt2->execute ();
						if ($stmt2->rowCount () > 0) {
							$availableKeys = $stmt2->fetchAll ();
							foreach ( $availableKeys as $row ) {
								?>
<tr>
	<td><?php echo getRoomFromKeyID($row['id']); ?></td>
	<td><?php echo '<a href="./includes/process.php?action=getKey?id='.$row['id'].'">Lend key</a>';?></td>
</tr>
<?php
							}
						}
					}
				} else {
					// All the keys are available
					$inQuery = implode ( ',', array_fill ( 0, count ( $accessibleKeys ), '?' ) );
					$stmt = $pdo->prepare ( 'select * from room_key where id IN (' . $inQuery . ')' );
					foreach ( $accessibleKeys as $j => $id ) {
						$stmt->bindValue ( ($j + 1), $id );
					}
					$stmt->execute ();
					if ($stmt->rowCount () > 0) {
						$availableKeys = $stmt->fetchAll ();
						foreach ( $availableKeys as $row ) {
							?>
<tr>
	<td><?php echo getRoomFromKeyID($row['id']); ?></td>
	<td><?php echo '<a href="./includes/process.php?action=getKey?id='.$row['id'].'">Lend key</a>';?></td>
</tr>
<?php
						}
					}
				}
				echo '</tbody></table>';
			}
		} else if (checkUserType ( 'DOORMAN' )) {
			
			$stmt = $pdo->prepare ( 'select * from log where active = 1' );
			$stmt->execute ();
			if ($stmt->rowCount () == 1) {
				$result = $stmt->fetchAll ();
				return $result;
			} else {
				// Key doesn't exist
				return false;
			}
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function isAvailable($id) {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		
		$stmt = $pdo->prepare ( 'select * from log where keys_id = :id and active = 1' );
		$stmt->bindValue ( ':id', $id );
		$stmt->execute ();
		return ($stmt->rowCount () == 0);
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function addToLog($id, $userID) {
	try {
		$pdo = connect ();
		$error = array ();
		
		$log = $pdo->prepare ( 'insert into log(Users_id,keys_id,time,active) values (:user,:key,DEFAULT,:active)' );
		$log->bindValue ( ':user', $userID );
		$log->bindValue ( ':key', $id );
		// $now = time ();
		// $log->bindValue ( ':time', $now );
		$log->bindValue ( ':active', 1 );
		
		return ($log->execute ());
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function returnKey($id) {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		
		$log = $pdo->prepare ( 'UPDATE log SET active = :active WHERE Users_id = :user AND keys_id = :key' );
		$log->bindValue ( ':user', $_SESSION ['user']->id );
		$log->bindValue ( ':key', $id );
		$log->bindValue ( ':active', 0 );
		
		return ($log->execute ());
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function transferKey($keyId, $userID) {
	if (returnKey ( $keyId )) {
		return addToLog ( $id, $userID );
	} else {
		return false;
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
	if (! isset ( $_SESSION )) {
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
			$pdo = connect ();
			$error = array ();
			
			if (! isset ( $_SESSION )) {
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
			$pdo = connect ();
			$error = array ();
			
			if (! isset ( $_SESSION )) {
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
				</table> 	
			<div id="editUserFormAJAX"></div>
		';
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
				</table>
			<div id="editRoomFormAJAX"></div>
				';
}
function printAdminLinks() {
	if (checkUserType ( 'ADMIN' )) {
		echo '<li><a 
					href="#signup"
					data-toggle="pill"
				>Create account</a></li>';
		echo '<li><a 	
					href="#userList"
					data-toggle="pill"
				>Userlist</a></li>';
		echo '<li><a 
					href="#createRoom"
					data-toggle="pill"
				>Create room</a></li>';
		
		echo '<li><a				
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
function printUserLinks() {
	if (checkUserType ( 'REGULAR' )) {
		echo '<li><a
			href="#keyLogsUser"
			data-toggle="pill"
		>Keys</a></li>';
	}
}
function getAccessList($id) {
	try {
		$pdo = connect ();
		$error = array ();
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		// Check if user exists
		
		$stmt = $pdo->prepare ( 'select access_type from access_list where user_id = :id' );
		$stmt->bindValue ( ':id', $id );
		$stmt->execute ();
		if ($stmt->rowCount () > 0) {
			$result = $stmt->fetchAll ( PDO::FETCH_COLUMN, 0 );
			return $result;
		} else {
			
			// User doesn't exist
			
			return false;
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function getUserByID($id) {
	try {
		$pdo = connect ();
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
function createRoom($number, $building, $type) {
	try {
		$pdo = connect ();
		// PHP will automatically close the connection when your script ends.
		$error = array ();
		
		if (! isset ( $_SESSION )) {
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
			$newKey = $pdo->prepare ( 'insert into room_key(id,type) values (null,:roomType)' );
			$newKey->bindValue ( ':roomType', $type );
			$pdo->beginTransaction ();
			$newKey->execute ();
			
			$key_id = $pdo->lastInsertId (); // Get the ID of the key
			
			$newRoom = $pdo->prepare ( 'insert into rooms(id,number,building,keys_id) values (null,:number,:building,:keys_id)' );
			
			$newRoom->bindValue ( ':number', $number );
			$newRoom->bindValue ( ':building', $building );
			$newRoom->bindValue ( ':keys_id', $key_id );
			
			$newRoom->execute ();
			$pdo->commit ();
			return true; // Registration succeded
		}
	} catch ( PDOException $e ) {
		echo 'Error: ' . $e->getMessage ();
	}
}
function getRoomByID($id) {
	try {
		$pdo = connect ();
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