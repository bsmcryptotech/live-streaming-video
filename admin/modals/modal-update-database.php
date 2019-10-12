<div id="modalDBUpdate" class="modal mt-3" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">PHP Melody Update
				<small class="d-block">Database Update Required</small>
				</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
				<p class="alert alert-info alert-styled-left border-top-0 border-bottom-0 border-right-0 mb-3">PHP Melody will now update your database to match the latest version. <br> Click '<strong>Continue</strong>' finalize the update process.</p>
			</div>
			<div class="modal-footer">
				<a href="db_update.php" class="btn btn-success">Continue &rarr;</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$('#modalDBUpdate').modal({
	show: true,
	backdrop: 'static',
	keyboard: false
});
</script>