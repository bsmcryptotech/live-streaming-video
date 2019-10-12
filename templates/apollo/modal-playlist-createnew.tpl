<form name="new-playlist" class="form-horizontal" method="post" action="">
<div class="modal" id="modal-new-playlist" role="dialog" aria-labelledby="new-playlist-form-label">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{$lang.close}</span></button>
			<h4 class="modal-title">{$lang.playlist_create_new}</h4>
		</div>
		<div class="modal-body">
			<div id="playlist-modal-ajax-response" class="hide-me"></div>
			<div class="form-group">
				<label class="col-md-3 control-label">{$lang.playlist_name}</label>
				<div class="col-md-8">
				<input type="text" class="form-control"name="playlist_name" value="" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label">{$lang.playlist_privacy}</label>
				<div class="col-md-8">
				<select name="visibility" class="form-control">
					<option value="{$smarty.const.PLAYLIST_PUBLIC}">{$lang.public}</option>
					<option value="{$smarty.const.PLAYLIST_PRIVATE}">{$lang.private}</option>
				</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label">{$lang.sort_by}</label>
				<div class="col-md-8">
				<select name="sorting" class="form-control">
					<option value="default">{$lang._manual}</option>
					<option value="popular">{$lang.most_popular}</option>
					<option value="date-added-desc">{$lang.sort_added_new}</option>
					<option value="date-added-asc">{$lang.sort_added_old}</option>
					<option value="date-published-desc">{$lang.sort_published_new}</option>
					<option value="date-published-asc">{$lang.sort_published_old}</option>
				</select>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn btn-sm btn-link" data-dismiss="modal" >{$lang.submit_cancel}</a>
			<a href="#" class="btn btn-sm btn-success btn-with-loader" id="create_playlist_submit_btn" onclick="playlist_create(this, 'playlists-modal'); return false;" disabled>{$lang.playlist_create_new}</a>
		</div>
		</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</form>