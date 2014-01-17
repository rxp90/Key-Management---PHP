<div
	class="tab-pane fade"
	id="signup"
>
	<h1>Create user</h1>

	<form
		action="./includes/process.php?action=signup"
		method="POST"
		id="form-signup"
		class="form-signin-big"
	>
	<?php
	if (isset ( $_SESSION ['success']['createUser'] )) {
		echo '<div class="alert alert-success">' . $_SESSION ['success']['createUser'] . '</div>';
	}
	?>
	
		<div
			class="form-group <?php isset($_SESSION ['error'] ['email']) ? 'has-error' : '';?>"
		>
			<input
				type="email"
				name="signupEmail"
				id="signupEmail"
				class="form-control"
				placeholder="Email address"
				required=""
				autofocus=""
				value="<?php isset($_SESSION['signupEmail']) ? $_SESSION['signupEmail'] : '';?>"
			>
						<?php
						if (isset ( $_SESSION ['error'] ['email'] )) {
							echo '<div class="alert alert-danger">' . $_SESSION ['error'] ['email'] . '</div>';
						}
						?>
						</div>

		<input
			type="password"
			name="signupPassword"
			class="form-control"
			placeholder="Password"
			required=""
		>
		<input
			type="text"
			name="signupName"
			class="form-control"
			placeholder="Name"
			required=""
			value="<?php isset($_SESSION['signupName']) ? $_SESSION['signupName'] : '';?>"
		>
		<button
			class="btn btn-primary btn-block"
			type="submit"
			name="signupSubmit"
		>Sign up</button>
	</form>
</div>