<script>
<!--
	$.datepicker.setDefaults({
		dateFormat : 'yy-mm-dd' //	ISO 8601
	});
	$(function() {
		$("#birthdate").datepicker({
				changeMonth: true,
			    changeYear: true,
			    maxDate: "-18Y"
		});
	});
	
//-->
</script>
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
		<input
			id="birthdate"
			name="signupBirthdate"
			type="text"
			class="form-control"
			placeholder="Birthdate"
			required=""
			pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))"
			value="<?php isset($_SESSION['signupBirthdate']) ? $_SESSION['signupBirthdate'] : '';?>"
		>
		<button
			class="btn btn-primary btn-block"
			type="submit"
			name="signupSubmit"
		>Sign up</button>
	</form>
</div>