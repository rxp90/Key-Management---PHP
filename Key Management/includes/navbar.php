<div
	class="navbar navbar-default navbar-fixed-top"
	role="navigation"
>
	<div class="container">
		<div class="navbar-header">
			<button
				type="button"
				class="navbar-toggle"
				data-toggle="collapse"
				data-target=".navbar-collapse"
			>
				<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span>
				<span class="icon-bar"></span> <span class="icon-bar"></span>
			</button>
			<a
				class="navbar-brand"
				href="#"
			>Key Management</a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="active"><a href="#">Home</a></li>
				<li><a href="#about">About</a></li>
			</ul>
			<?php if(isset($_SESSION['user'])){?>
				<div id="userInfo">
				Welcome <?php echo $_SESSION['user']->name;?>
				<a
					type="button"
					href="./includes/process.php?action=logout"
					class="btn btn-default"
				> <span class="glyphicon glyphicon-off"></span> Logout
				</a>
			</div>
			<?php
			}
			?>
		</div>
		<!--/.nav-collapse -->
	</div>	
</div>