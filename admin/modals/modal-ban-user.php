<div id="banUser"  class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">Ban User</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form name="ban_user" action="banned-users.php?a=ban" method="post">
				<div class="modal-body">

					<div class="form-group">
						<label>Username</label>
						<input type="text" name="username" value="<?php echo $_POST['username'];?>" size="40" id="focusedInput" class="form-control" />
					</div>
					<div class="form-group">
						<label>Reason</label>
						<textarea name="reason" cols="60" rows="3" class="form-control"><?php echo $_POST['reason'];?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button data-dismiss="modal" aria-hidden="true" class="btn btn-outline bg-grey text-grey-800 btn-icon">Cancel</button>
					<button type="submit" name="Submit" value="Ban" class="btn btn-warning" />Ban user</button>
					<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $banlist_nonce['_pmnonce'];?>" value="<?php echo $banlist_nonce['_pmnonce'];?>" />
					<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $banlist_nonce['_pmnonce'];?>" value="<?php echo $banlist_nonce['_pmnonce_t'];?>" />
				</div>
			</form>
		</div>
	</div>
</div>