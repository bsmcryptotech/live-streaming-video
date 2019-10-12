<div id="view-cron-log-modal" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">History Log</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			
			<form name="edit-cron-job-form" id="edit-cron-job-form-<?php echo $job['job_id']; ?>" data-job-id="<?php echo $job['job_id']; ?>" action="automated-jobs.php?page=<?php echo $page; ?>" method="post">
				<div class="modal-body">
					<span id="view-cron-log-modal-loading">
						<img src="img/ico-loading.gif" width="16" height="16"  /> Loading... 
					</span>
					<div id="view-cron-log-modal-content"></div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger btn-outline border-danger text-danger" id="cron-clear-log-btn" data-job-id="">Clear Log</button>
					<button class="btn btn-success" data-dismiss="modal" aria-hidden="true">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>