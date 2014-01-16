<?php
if (! isset ( $_SESSION )) {
	session_start ();
}
include_once ("./Functions.php");
$id = $_GET ['id'];
$editingUser = getUserByID ( $id );
$accessList = getAccessList ( $id );
?>
<hr>
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
		value="<?php if(isset($editingUser)) { echo $editingUser->id; }else{ echo '';}?>"
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
			value="<?php if(isset($editingUser)) { echo $editingUser->email; }else{ echo '';}?>"
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
			value="<?php if(isset($editingUser)) { echo $editingUser->name; }else{ echo '';}?>"
		>
	</div>
	<div
		class="form-group <?php isset($_SESSION ['error'] ['birthdate']) ? 'has-error' : '';?>"
	>
		<label for="editBirthdate">Birthdate</label>
		<input
			id="birthdate"
			name="editBirthdate"
			type="text"
			class="form-control"
			placeholder="Birthdate"
			required=""
			pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))"
			value="<?php if(isset($editingUser)) { echo $editingUser->birthdate; }else{ echo '';}?>"
		>
	</div>
	<!-------------------------------- END_USER --------------------------------->

	<!-------------------------------- START_ADMIN --------------------------------->
				
				<?php
				
				if (checkUserType ( 'ADMIN' )) {
					
					?>
					<div class="checkbox">
		<label> <input
				type="checkbox"
				name="editActive"
				class="form-control"
				<?php if ($editingUser->active == 1): ?>
				checked="checked"
				<?php endif; ?>
				value="1"
			>Active
		</label>
	</div>

	<div class="form-group">
		<label for="accessList">Access</label> <select
			id="accessList"
			name="access[]"
			class="form-control"
			multiple
		>
				<?php
					$options = array (
							'LABS',
							'SPECIAL',
							'LECTURERS' 
					);
					foreach ( $options as $opt ) {
						$sel = '';
						if (in_array ( $opt, $accessList )) {
							$sel = ' selected=selected ';
						}
						echo '<option ' . $sel . ' value="' . $opt . '">' . $opt . '</option>';
					}
					?>
			</select>
	</div>
	<div class="form-group">
		<label for="editType">Type</label> <select
			id="editType"
			name="editType"
			class="form-control"
		>
			<option
				value="ADMIN"
				<?php if ($editingUser->type == 'ADMIN'): ?>
				selected="selected"
				<?php endif; ?>
			>Administrator</option>
			<option
				value="REGULAR"
				<?php if ($editingUser->type == 'REGULAR'): ?>
				selected="selected"
				<?php endif; ?>
			>Regular employee</option>
			<option
				value="DOORMAN"
				<?php if ($editingUser->type == 'DOORMAN'): ?>
				selected="selected"
				<?php endif; ?>
			>Doorman</option>
		</select>
	</div>
	<input
		class="btn btn-danger btn-block"
		type="submit"
		name="delete"
		value="Delete"
	>
				
				<?php }?>
				<!-------------------------------- END_ADMIN --------------------------------->
	<input
		class="btn btn-primary btn-block"
		type="submit"
		name="edit"
		value="Save profile"
	>
</form>