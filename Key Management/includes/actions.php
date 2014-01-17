<?php
if (! isset ( $_SESSION )) {
	session_start ();
}
if (isset ( $_SESSION ['user'] )) { // Only showed if logged in
	include_once ("./includes/Functions.php");
	?>
<script
	language="javascript"
	type="text/javascript"
	src="./js/AJAX.js"
></script>

<ul class="nav nav-pills nav-stacked verticalMenu">
	<li class="active"><a
		href="#editProfile"
		data-toggle="pill"
	>Edit profile</a></li>
	<?php
	printAdminLinks ();
	printDoormanLinks ();
	printUserLinks ();
	?>
</ul>

<div class="center">
	<div class="tab-content">

		<!-------------------------------- EDIT_PROFILE --------------------------------->

		<div
			class="tab-pane active"
			id="editProfile"
		>
			<h1>Edit profile</h1>
<?php
	if (isset ( $_SESSION ['success'] ['editProfile'] )) {
		echo '<div class="alert alert-success">' . $_SESSION ['success'] ['editProfile'] . '</div>';
	}
	?>
			<form
				action="./includes/process.php?action=editProfile"
				method="POST"
				id="form-edit"
				class="form-signin-big"
			>
				<input
					type="text"
					name="editID"
					hidden=""
					value="<?php if(isset($_SESSION['user'])) { echo $_SESSION['user']->id; }else{ echo '';}?>"
				>
				<div
					class="form-group <?php isset($_SESSION ['error'] ['email']) ? 'has-error' : '';?>"
				>
					<label for="editEmail">Email</label>
					<input
						type="email"
						name="editEmail"
						id="editEmail"
						class="form-control"
						placeholder="Email address"
						required=""
						autofocus=""
						value="<?php if(isset($_SESSION['user'])) { echo $_SESSION['user']->email; }else{ echo '';}?>"
					>
						<?php
	if (isset ( $_SESSION ['error'] ['email'] )) {
		echo '<div class="alert alert-danger">' . $_SESSION ['error'] ['email'] . '</div>';
	}
	?>
						</div>
				<div
					class="form-group <?php isset($_SESSION ['error'] ['name']) ? 'has-error' : '';?>"
				>
					<label for="editName">Name</label>
					<input
						type="text"
						name="editName"
						class="form-control"
						placeholder="Name"
						required=""
						value="<?php if(isset($_SESSION['user'])) { echo $_SESSION['user']->name; }else{ echo '';}?>"
					>
				</div>
				
				
				<?php
	
	if (checkUserType ( 'ADMIN' )) {
		
		?>
					<div class="checkbox">
					<label> <input
							type="checkbox"
							name="editActive"
							class="form-control"
							<?php if ($_SESSION['user']->active == 1): ?>
							checked="checked"
							<?php endif; ?>
							value="1"
						>Active
					</label>
				</div>
				<div class="form-group">
					<label for="editType">Type</label> <select
						id="editType"
						name="editType"
						class="form-control"
					>
						<option
							value="ADMIN"
							<?php if ($_SESSION['user']->type == 'ADMIN'): ?>
							selected="selected"
							<?php endif; ?>
						>Administrator</option>
						<option
							value="REGULAR"
							<?php if ($_SESSION['user']->type == 'REGULAR'): ?>
							selected="selected"
							<?php endif; ?>
						>Regular employee</option>
						<option
							value="DOORMAN"
							<?php if ($_SESSION['user']->type == 'DOORMAN'): ?>
							selected="selected"
							<?php endif; ?>
						>Doorman</option>
					</select>
				</div>
					<?php }?>
				<input
					class="btn btn-primary btn-block"
					type="submit"
					name="edit"
					value="Save profile"
				>
			</form>
		</div>
		
		<?php
	// <!-------------------------------- CREATE_USER --------------------------------->
	
	if (checkUserType ( 'ADMIN' )) {
		
		include ('./includes/createUser.php');
		include ('./includes/createRoom.php');
		// <!-------------------------------- USER_LIST --------------------------------->
		
		echo '<div
	class="tab-pane fade"
	id="userList"
>';
		
		echo '<h1>User list</h1>';
		
		printUserList ();
		
		echo '</div>';
		
		// <!-------------------------------- ROOM_LIST --------------------------------->
		echo '<div
	class="tab-pane fade"
	id="roomList"
>';
		
		echo '<h1>Room list</h1>';
		
		printRoomList ();
		
		echo '</div>';
		
		?>
	
<?php }?>
		
		<?php
	// <!-------------------------------- KEY_LOG --------------------------------->
	
	// Doorman
	if (checkUserType ( 'DOORMAN' )) {
		
		echo '<div
			class="tab-pane fade"
			id="keyLogs"
		>
			<div>';
		echo '<h1>Key log</h1>';
		
		getKeyLogs ();
		echo '</div></div>';
	}
	if (checkUserType ( 'REGULAR' )) {
		
		echo '<div
			class="tab-pane fade"
			id="keyLogsUser"
		>
			<div>';
		echo '<h1>Keys</h1>';
		getKeyLogs ();
		echo '</div></div>';
	}
	
	?>

	</div>

</div>
<?php }?>