<!-- Modal -->
<div class="modal animated fast slideInUp" id="modal-video-addtoplaylist" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">{$lang.add_to_playlist}</h4>
			</div>
			<div class="modal-body">
				<div id="pm-vc-playlists-content">
				{if $logged_in}
				<div id="playlist-ajax-response" class="hide-me"></div>
				<div id="playlist-create-ajax-response" class="hide-me"></div>

				{include file="video-watch-playlists.tpl"}
				<hr />
				<h4>{$lang.create_new_playlist}</h4>
				<div class="clear"></div>
				<form class="form-inline" role="form">
					 <div class="form-group">
								<input type="text" class="form-control" name="playlist_name" placeholder="{$lang.playlist_enter_name}" size="36">
						</div>
					 <div class="form-group">
						<select name="visibility" class="form-control">
								<option value="{$smarty.const.PLAYLIST_PUBLIC}">{$lang.public}</option>
								<option value="{$smarty.const.PLAYLIST_PRIVATE}">{$lang.private}</option>
						</select>
					 </div>
						<div class="form-group">
							<input type="hidden" name="video_id" value="{$video_data.id}" />
							<button type="submit" id="create_playlist_submit_btn" class="btn btn-success" onclick="playlist_create(this, 'video-watch'); return false;" disabled>{$lang.playlist_create_new}</button>
						</div>
				</form>
				{else}
				<div class="alert alert-danger">
					{$lang.playlist_msg_login_required}
				</div>
				{/if}
				</div>                
			</div>
		</div>
	</div>
</div>