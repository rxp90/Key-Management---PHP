<!DOCTYPE html>
<?php
if (! isset ( $_SESSION )) {
	session_start ();
	var_dump ( $_SESSION );
	var_dump ( $_POST );
}
?>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Key Management</title>
<!-- Latest compiled and minified CSS -->
<link
	rel="stylesheet"
	href="https://netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css"
>
<link
	rel="stylesheet"
	href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"
/>
<!-- Optional theme -->
<link
	rel="stylesheet"
	href="https://netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap-theme.min.css"
>

<link
	rel="stylesheet"
	href="./css/main.css"
>
<script src="http://code.jquery.com/jquery-2.0.2.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<script
	type="text/javascript"
	src="./js/progression.min.js"
></script>

<!-- Latest compiled and minified JavaScript -->
<script
	src="https://netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"
></script>


</head>
<body>
	<!-- NavBar -->
	<?php include("./includes/navbar.php"); ?>
	<!-- Content -->
	<?php include("./includes/welcomeContent.php"); ?>
	
</body>
</html>