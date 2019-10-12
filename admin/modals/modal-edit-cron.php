<div id="edit-cron-job-modal" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">Edit Job</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			
			<form name="edit-cron-job-form" id="edit-cron-job-form-<?php echo $job['job_id']; ?>" data-job-id="<?php echo $job['job_id']; ?>" action="automated-jobs.php?page=<?php echo $page; ?>" method="post">
				<div class="modal-body">
					<span id="cron-edit-modal-loading" class="text-center">
						<img src="img/ico-loading.gif" width="16" height="16"  /> Loading... 
					</span>
					<div id="cron-edit-modal-content"></div>
				</div>
				<div class="modal-footer">
					<button data-dismiss="modal" aria-hidden="true" class="btn btn-outline bg-grey text-grey-800 btn-icon">Cancel</button>
					<button class="btn btn-success" name="Submit" value="Save" id="cron-edit-submit-btn" data-job-id="">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>