<?php
if (! isset ( $_SESSION )) {
	session_start ();
}
include_once ("./Functions.php");

if (checkUserType ( 'REGULAR' )) {
	
	$keyID = $_GET ['id'];
	$possibleUsers = getUsersAllowed ( $keyID );
	?>
<!-- Modal -->
<div
	class="modal fade"
	id="myModal"
	tabindex="-1"
	role="dialog"
	aria-labelledby="myModalLabel"
	aria-hidden="true"
>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button
					type="button"
					class="close"
					data-dismiss="modal"
					aria-hidden="true"
				>&times;</button>
				<h4
					class="modal-title"
					id="myModalLabel"
				>Transfer key</h4>
			</div>
			<div class="modal-body">

				<form
					action="./includes/process.php?action=transferKey"
					method="POST"
					id="form-edit"
					class="form-signin-big"
				>
					<input
						type="text"
						name="keyID"
						hidden=""
						value="<?php echo $keyID;?>"
					>

					<div class="form-group">
						<label for="transferUser">Select user</label> <select
							id="transferUser"
							name="transferUser"
							class="form-control"
						>
			<?php
	
	foreach ( $possibleUsers as $element ) {
		echo '<option value="' . $element ['id'] . '">' . $element ['name'] . '</option>';
	}
	?>
		</select>
					</div>

					<input
						class="btn btn-primary btn-block"
						type="submit"
						value="Transfer key"
					>
				</form>
			</div>
			<div class="modal-footer">
				<button
					type="button"
					class="btn btn-default"
					data-dismiss="modal"
				>Close</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<?php }?>
