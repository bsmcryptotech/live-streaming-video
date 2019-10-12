<form name="playlist-settings" class="form-horizontal" method="post" action="">
<div class="modal" id="playlist-settings" role="dialog" aria-labelledby="playlist-settings-form-label">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{$lang.close}</span></button>
			<h4 class="modal-title">{$lang.playlist_settings}</h4>
		</div>
		<div class="modal-body">
			<div id="playlist-modal-ajax-response" class="hide-me"></div>
			{if $playlist.type == $smarty.const.PLAYLIST_TYPE_CUSTOM}
			<div class="form-group">
				<label class="col-md-3 control-label">{$lang.playlist_name}</label>
				<div class="col-md-8">
				<input type="text" class="form-control" name="playlist_name" value="{$playlist.title}" />
				</div>
			</div>
			{/if} 
			
			<div class="form-group">
				<label class="col-md-3 control-label">{$lang.playlist_privacy}</label>
				<div class="col-md-8">
				<select name="visibility" class="form-control">
					<option value="{$smarty.const.PLAYLIST_PUBLIC}" {if $playlist.visibility == $smarty.const.PLAYLIST_PUBLIC}selected="selected"{/if}>{$lang.public}</option>
					<option value="{$smarty.const.PLAYLIST_PRIVATE}" {if $playlist.visibility == $smarty.const.PLAYLIST_PRIVATE}selected="selected"{/if}>{$lang.private}</option>
				</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">{$lang.video_ordering}</label>
				<div class="col-md-8">
				<select name="sorting" class="form-control">
					<option value="default" {if $playlist.sorting == 'default'}selected="selected"{/if}>{$lang._manual}</option>
					<option value="popular" {if $playlist.sorting == 'popular'}selected="selected"{/if}>{$lang.most_popular}</option>
					<option value="date-added-desc" {if $playlist.sorting == 'date-added-desc'}selected="selected"{/if}>{$lang.sort_added_new}</option>
					<option value="date-added-asc" {if $playlist.sorting == 'date-added-asc'}selected="selected"{/if}>{$lang.sort_added_old}</option>
					<option value="date-published-desc" {if $playlist.sorting == 'date-published-desc'}selected="selected"{/if}>{$lang.sort_published_new}</option>
					<option value="date-published-asc" {if $playlist.sorting == 'date-published-asc'}selected="selected"{/if}>{$lang.sort_published_old}</option>
				</select>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			{if $my_playlist && $playlist.type == $smarty.const.PLAYLIST_TYPE_CUSTOM}
				<a href="#" class="btn btn-sm btn-danger pull-left" {if $playlist.type == $smarty.const.PLAYLIST_TYPE_CUSTOM} onclick="playlist_delete({$playlist.list_id}, this);" {/if}>{$lang.submit_delete}</a>
			{/if}
			<a href="#" class="btn btn-sm btn-link" data-dismiss="modal" >{$lang.submit_cancel}</a>
			<a href="#" class="btn btn-sm btn-success btn-with-loader" onclick="playlist_save_settings({$playlist.list_id}, this); return false;">{$lang.submit_save}</a>
		</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</form>