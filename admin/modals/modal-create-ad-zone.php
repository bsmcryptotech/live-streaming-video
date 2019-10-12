<div id="addNew" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">Create new ad zone</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			
			<form name="ad_manager" method="post" action="banner-ads.php?act=addnew" enctype="application/x-www-form-urlencoded">
			<div class="modal-body">

				<div class="form-group">
					<label>Ad Name</label>
					<input type="text" name="position" placeholder="" class="form-control">
				</div>
				<div class="form-group">
					<label>Description</label>
					<input type="text" name="description" placeholder="" class="form-control">
				</div>
			
				<div class="form-group">
					<label>HTML Code for your Ad</label>
					<textarea name="code" cols="60" rows="3" class="form-control"></textarea>
				</div>
				<div class="form-group">
					<label>Enable Statistics</label>
					<br />
					<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="0"> Yes</label> 
					<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="1" checked="checked"> No</label>
				</div>

			</div>
			<div class="modal-footer">
				<input type="hidden" name="active" value="1" />
				<button data-dismiss="modal" aria-hidden="true" class="btn btn-outline bg-grey text-grey-800 btn-icon">Cancel</button>
				<button type="submit" name="Submit" value="Submit" class="btn btn-success" />Save</button>
			</div>
			</form>
		</div>
	</div>
</div>