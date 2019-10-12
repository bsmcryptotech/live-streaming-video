<div id="addNew" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">Create a new video ad</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			
			<form name="ad_manager" method="post" action="player-video-ads.php?act=addnew">
			<div class="modal-body">

				<div class="form-group">
					<label>Name</label>
					<input type="text" name="name" value="" size="40" class="form-control" />
				</div>

				<div class="form-group">
					<label>Video Ad URL <a href="#" rel="tooltip" title="Supported file types: *.flv, *.mp4" class="text-muted"><i class="mi-info-outline"></i></a></label>
					<input type="text" name="flv_url" value="" size="120" placeholder="http://" class="form-control" />
				</div>
				<div class="form-group">
					<label>Advertised URL</label>
					<input type="text" name="redirect_url" value="" size="120" placeholder="http://" class="form-control" />
				</div>
				<div class="form-group">
					<label>Enable Statistics</label>
					<br />
					<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="0"> Yes</label> 
					<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="1" checked="checked"> No</label>
					<input type="hidden" name="redirect_type" value="0" />
					<input type="hidden" name="active" value="1" />
				</div>
			</div>

			<div class="modal-footer">
				<button data-dismiss="modal" aria-hidden="true" class="btn btn-outline bg-grey text-grey-800 btn-icon">Cancel</button>
				<button type="submit" name="Submit" value="Submit" class="btn btn-success" />Submit</button>
			</div>
			</form>
		</div>
	</div>
</div>