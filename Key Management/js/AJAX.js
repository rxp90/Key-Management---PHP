$(function() {

	// Bind the event.
	$(window).bind('hashchange', function() {
		var linkName = location.hash + 'Link';
		$(linkName).click();
	});

});

function loadTransferForm(id) {
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("transferKeyFormAJAX").innerHTML = xmlhttp.responseText;
			$('#myModal').modal('show');
		}
	}
	xmlhttp.open("GET", "./includes/transferKey.php?id=" + id, true);
	xmlhttp.send();
}
function loadUserForm(id) {
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("editUserFormAJAX").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET", "./includes/editUser.php?id=" + id, true);
	xmlhttp.send();
}
function loadRoomForm(id) {
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("editRoomFormAJAX").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET", "./includes/editRoom.php?id=" + id, true);
	xmlhttp.send();
}
