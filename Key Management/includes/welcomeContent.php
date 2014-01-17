<?php
if (! isset ( $_SESSION )) {
	session_start ();
}
if (! isset ( $_SESSION ['user'] )) { // Only showed if not logged in	?>


<div class="container">

	<div class="jumbotron">
		<h1>Welcome</h1>
		<p>This is a web application for managing keys of Pollub</p>
		<p>Please, sign in to use the application</p>
		<div class="center">

			<ul
				class="nav nav-tabs"
				id="welcomeTabs"
			>
				<li class="active"><a
					href="#signin"
					data-toggle="tab"
				>Sign in</a></li>
			</ul>

			<div class="tab-content">
				<div
					class="tab-pane active"
					id="signin"
				>
					<form
						action="./includes/process.php?action=login"
						method="post"
						id="form-signin-big"
						class="form-signin-big"
					>

						<div class="left-inner-addon ">
							<span class="glyphicon glyphicon-envelope"></span>
							<input
								type="email"
								name="loginEmail"
								class="form-control"
								placeholder="Email address"
								required=""
								autofocus=""
							>
						</div>
						<div class="left-inner-addon ">
							<span class="glyphicon glyphicon-lock"></span>
							<input
								type="password"
								name="loginPassword"
								class="form-control"
								placeholder="Password"
								required=""
							>
						</div>
						
						<?php
	// ////////////////////// Possible errors
	if (isset ( $_SESSION ['error'] ['incorrectPassword'] )) {
		echo '<div class="alert alert-danger">' . $_SESSION ['error'] ['incorrectPassword'] . '</div>';
	}
	?>
	<?php
	if (isset ( $_SESSION ['error'] ['incorrectUser'] )) {
		echo '<div class="alert alert-danger">' . $_SESSION ['error'] ['incorrectUser'] . '</div>';
	}
	?>
	<?php
	if (isset ( $_SESSION ['error'] ['disabledAccount'] )) {
		echo '<div class="alert alert-danger">' . $_SESSION ['error'] ['disabledAccount'] . '</div>';
	}
	// //////////////////////////////////
	?>

						<button
							name="loginSubmit"
							class="btn btn-primary btn-block"
							type="submit"
						>Sign in</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<script>
		<!--
		$(function () {
		    $('#welcomeTabs a:last').tab('show')
		});
		//  -->
</script>
<?php }?>