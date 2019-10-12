<div id="modal_subscribe" class="modal" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">Subscribe to this Search</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			
			<form name="subscribe-to-search" method="post" action="">
				<div class="modal-body">
					<div class="alert alert-info alert-styled-left border-top-0 border-bottom-0 border-right-0 mb-3">
						Save this search for quick access to <strong><span class="sub-name-html"></span></strong> videos. The subscription will save your current search (<em>including filters</em>). 
					</div>

					<div class="modal-response-placeholder hide"></div>
					
					<div class="form-group">
						<label>Subscription Name</label>
						<input type="text" name="sub-name" value="" size="40" class="form-control" />
						<input type="hidden" name="sub-params" value="" />
						<input type="hidden" name="sub-type" value="search" />
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="status" value="1" />
					<button data-dismiss="modal" aria-hidden="true" class="btn btn-outline bg-grey text-grey-800 btn-icon">Cancel</button>
					<button type="submit" name="Submit" value="Submit" class="btn btn-success" id="btn-subscribe-modal-save" />Save</button>
				</div>
				<?php echo csrfguard_form('_admin_import_subscriptions'); ?>
			</form>
		</div>
	</div>
</div>