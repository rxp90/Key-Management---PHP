<?php
if (! isset ( $_SESSION )) {
	session_start ();
}
include_once ("./Functions.php");
$id = $_GET ['id'];
$editingRoom = getRoomByID ( $id );
$key = getKeyFromID ( $editingRoom->keys_id );
?>
<hr>

<form
	action="./includes/process.php?action=editRoom"
	method="POST"
	id="form-signup"
	class="form-signin-big"
>
	<input
		type="text"
		name="editID"
		hidden=""
		value="<?php if(isset($editingRoom)) { echo $editingRoom->id; }else{ echo '';}?>"
	>
	<div
		class="form-group <?php isset($_SESSION ['error'] ['roomNumber']) ? 'has-error' : '';?>"
	>
		<input
			type="number"
			name="roomNumber"
			class="form-control"
			placeholder="Room number"
			required=""
			autofocus=""
			value="<?php if(isset($editingRoom)) { echo $editingRoom->number; }else{ echo '';}?>"
		>
						<?php
						if (isset ( $_SESSION ['error'] ['roomNumber'] )) {
							echo '<div class="alert alert-danger">' . $_SESSION ['error'] ['roomNumber'] . '</div>';
						}
						?>
						</div>
	<input
		type="text"
		name="buildingName"
		class="form-control"
		placeholder="Building name"
		required=""
		value="<?php if(isset($editingRoom)) { echo $editingRoom->building; }else{ echo '';}?>"
	>

	<div class="form-group">
		<label for="roomType">Room type</label> <select
			id="roomType"
			name="roomType"
			class="form-control"
		>
			<option
				value="LECTURERS"
				<?php if ($key->type == 'LECTURERS'): ?>
				selected="selected"
				<?php endif; ?>
			>Lectures</option>
			<option
				value="SPECIAL"
				<?php if ($key->type == 'SPECIAL'): ?>
				selected="selected"
				<?php endif; ?>
			>Special</option>
			<option
				value="LABS"
				<?php if ($key->type == 'LABS'): ?>
				selected="selected"
				<?php endif; ?>
			>Laboratory</option>
		</select>
	</div>

	<input
		class="btn btn-danger btn-block"
		type="submit"
		name="delete"
		value="Delete"
	>
	<input
		class="btn btn-primary btn-block"
		type="submit"
		name="edit"
		value="Save"
	>
</form>