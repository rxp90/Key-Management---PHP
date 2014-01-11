<?php
?>
<script>
<!--
	$.datepicker.setDefaults({
		dateFormat : 'yy-mm-dd' //	ISO 8601
	});
	$(function() {
		$("#birthdate").datepicker();
	});
//-->
</script>
<script src="https://raw.github.com/enyo/dropzone/master/downloads/dropzone.js">
</script>
<div class="container">

	<div class="jumbotron">
		<h1>Welcome</h1>
		<p>This is a web application for managing keys of Pollub</p>
		<p>Please, sign in to use the application or create a new account</p>
		<div class="center">


			<ul
				class="nav nav-tabs"
				id="welcomeTabs"
			>
				<li class="active"><a
					href="#signin"
					data-toggle="tab"
				>Sign in</a></li>
				<li><a
					href="#signup"
					data-toggle="tab"
				>Create account</a></li>
			</ul>

			<div class="tab-content">
				<div
					class="tab-pane fade active"
					id="signin"
				>
					<form
						id="form-signin-big"
						class="form-signin-big"
					>

						<div class="left-inner-addon ">
							<span class="glyphicon glyphicon-envelope"></span>
							<input
								type="email"
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
								class="form-control"
								placeholder="Password"
								required=""
							>
						</div>

						<button
							class="btn btn-primary btn-block"
							type="submit"
						>Sign in</button>
					</form>
				</div>
				<div
					class="tab-pane fade"
					id="signup"
				>
					<form
						id="form-signup"
						class="form-signin-big"
					>

						<input
							type="email"
							name="signupEmail"
							class="form-control"
							placeholder="Email address"
							required=""
							autofocus=""
						>
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
						>
						<input
							id="birthdate"
							name="signupBirthdate"
							type="text"
							class="form-control"
							placeholder="Birthdate"
							required=""
							pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))"
						>
						<input
							id="fileupload"
							name="signupPhoto"
							type="file"
							class="dropzone"
							required=""
						>
						<button
							class="btn btn-primary btn-block"
							type="submit"
							name="signupSubmit"
						>Sign up</button>
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