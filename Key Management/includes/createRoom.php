<div
	class="tab-pane fade"
	id="createRoom"
>
	<h1>Create room</h1>

	<form
		action="./includes/process.php?action=createRoom"
		method="POST"
		id="form-signup"
		class="form-signin-big"
	>
	<?php
	if (isset ( $_SESSION ['success'] ['createRoom'] )) {
		echo '<div class="alert alert-success">' . $_SESSION ['success'] ['createRoom'] . '</div>';
	}
	?>
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
				value="<?php if(isset($_SESSION['roomNumber'])){echo $_SESSION['roomNumber'];}?>"
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
			value="<?php if(isset($_SESSION['buildingName'])){echo $_SESSION['buildingName'];}?>"
		>
		<div class="form-group">
			<label for="roomType">Room type</label> <select
				id="roomType"
				name="roomType"
				class="form-control"
			>
				<option value="LECTURERS">Lectures</option>
				<option value="SPECIAL">Special</option>
				<option value="LABS">Laboratory</option>
			</select>
		</div>
		<button
			class="btn btn-primary btn-block"
			type="submit"
			name="createRoomSubmit"
		>Create room</button>
	</form>
</div>