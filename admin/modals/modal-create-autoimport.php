<!-- add cron job modal -->
<div id="add-cron-job-modal" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">New Automated Job (Auto-importing)</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form name="add-cron-job-form" id="add-cron-job-form" data-sub-id="" action="" method="post">
				<div class="modal-body">
					<span id="cron-add-modal-loading">
						<img src="img/ico-loading.gif" width="16" height="16"  /> Loading... 
					</span>
					<div id="cron-add-modal-content"></div>
				</div>
				<div class="modal-footer">
					<button data-dismiss="modal" aria-hidden="true" class="btn btn-outline bg-grey text-grey-800 btn-icon">Cancel</button>
					<button class="btn btn-success" name="Submit" value="Save" id="cron-add-submit-btn" data-job-id="" data-sub-id="">Create Job</button>
				</div>
			</form>
		</div>
	</div>
</div>