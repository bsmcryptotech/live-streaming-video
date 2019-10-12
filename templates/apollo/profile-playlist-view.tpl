{include file='header.tpl' no_index='1' p="playlists"}
{include file="profile-header.tpl" p="playlists"}

<div id="content" class="content-detached">
	<div class="container-fluid">

	<div class="row row-page-heading">
		<div class="col-md-12">
			<!--<i class="mico mico-keyboard_arrow_left md-24"></i> -->
			<h1>{if $my_playlist}<a href="{$smarty.const._URL}/playlists.{$smarty.const._FEXT}" rel="tooltip" title="{$lang.my_playlists}">{$lang.manage_playlists}</a>{else}"{$playlist.title}" {if ! $my_playlist}{$lang._by} <a href="{$playlist.user_profile_href}">{$playlist.user_name}</a>{/if}{/if}</h1>
		</div>

	</div>
	<div class="row">
		<div class="col-md-12">
		<div class="row">
			<div class="col-md-12">
			{if ! is_array($playlist)}
				<div class="alert alert-danger">
					{$lang.playlist_not_found}
				</div>
			{elseif $playlist.visibility == $smarty.const.PLAYLIST_PRIVATE && ! $my_playlist}
				<div class="alert alert-info">
					<i class="fa fa-lock"></i> {$lang.playlist_private}
				</div>
			{else}
				<div class="pm-playlist-edit">
					<div class="row pm-pl-header">
						<div class="hidden-xs col-sm-3 col-md-2">
							<div class="pm-pl-thumb">
							<img src="{$playlist.thumb_url}" border="0" class="img-responsive">
							<a href="{$playlist.playlist_watch_all_href}" class="thumbnail-overlay" rel="nofollow">&#9658; {$lang.play_all}</a>
							</div>
						</div>
						<div class="col-xs-12 col-sm-9 col-md-10">
							<div class="pm-pl-header-title">
								{if $playlist.visibility == $smarty.const.PLAYLIST_PRIVATE}
									<a href="#playlist-settings" {if $my_playlist}data-toggle="modal" data-backdrop="true" data-keyboard="true"{/if} rel="tooltip" title="{$lang.playlist_private_desc}" class="pm-pl-status-icon"><i class="fa fa-lock"></i></a>
								{/if}
								{if $playlist.visibility == $smarty.const.PLAYLIST_PUBLIC}
									<a href="#playlist-settings" {if $my_playlist}data-toggle="modal" data-backdrop="true" data-keyboard="true"{/if} rel="tooltip" title="{$lang.playlist_public_desc}" class="pm-pl-status-icon"><i class="fa fa-unlock"></i></a>
								{/if}
								<h3 class="ellipsis">{$playlist.title}</h3>
							</div>

							<ul class="list-unstyled list-inline">
								<li>{$lang._by} <a href="{$playlist.user_profile_href}">{$playlist.user_name}</a></li>
								<li>{if $playlist.items_count == 1}1 {$lang._video}{else}{$playlist.items_count} {$lang.videos}{/if}</li>
							</ul>
							
							<div class="pm-pl-actions">
								{if $playlist.items_count > 0}
								<a href="{$playlist.playlist_watch_all_href}" class="btn btn-sm btn-default" rel="nofollow"><i class="fa fa-play"></i> {$lang.play_all}</a>
								{/if}
								{if $share_link != '' && $playlist.visibility == $smarty.const.PLAYLIST_PUBLIC}
								<a href="#playlist-share" class="btn btn-sm btn-default" data-toggle="modal" data-backdrop="true" data-keyboard="true"><i class="fa fa-share"></i> {$lang._share}</a>
								{/if}

								{if $my_playlist && $playlist.type != $smarty.const.PLAYLIST_TYPE_WATCH_LATER && $playlist.type != $smarty.const.PLAYLIST_TYPE_HISTORY}
								<a href="#playlist-settings" class="btn btn-sm btn-default" data-toggle="modal" data-backdrop="true" data-keyboard="true"><i class="fa fa-cog"></i> {$lang.playlist_settings}</a>
								{/if}

								{if $my_playlist && $playlist.type == $smarty.const.PLAYLIST_TYPE_CUSTOM}
								<a href="#" class="btn btn-sm btn-danger btn-with-loader hidden-xs" {if $playlist.type == $smarty.const.PLAYLIST_TYPE_CUSTOM} onclick="playlist_delete({$playlist.list_id}, this);" {/if}>{$lang.submit_delete}</a>
								{/if}



							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<ul class="pm-pl-list list-unstyled">
						{if $playlist.items_count == 0}
						<li>
							<p>{$lang.playlist_empty}</p>
						</li>
						{else}
						{foreach from=$playlist_items key=index item=list_video name=playlist_items_foreach}
						<li id="playlist_item_{$list_video.id}">
							<div class="pm-pl-list-index visible-md visible-lg">{$index+1}</div>
							<div class="pm-pl-list-thumb"><a href="{$list_video.playlist_video_href}" rel="nofollow"><img src="{$list_video.thumb_img_url}" height="40" border="0"></a></div>
							<div class="pm-pl-list-title">
								<h3><a href="{$list_video.playlist_video_href}" rel="nofollow">{$list_video.video_title}</a></h3>
								<div class="pm-pl-list-author hidden-xs">{$lang.submittedby} <a href="{$list_video.author_profile_href}">{$list_video.author_name}</a></div>
							</div>
							
							{if $my_playlist}
							<div class="pm-pl-list-action">
								<a href="#" class="btn btn-sm btn-link" onclick="playlist_delete_item({$playlist.list_id}, {$list_video.id}, '#playlist_item_{$list_video.id}'); return false;" rel="tooltip" title="{$lang.playlist_remove_item}"><i class="fa fa-trash"></i></a>
							</div>
							{/if}
						</li>
						{/foreach}
						{/if}
					</ul>
				</div>
				{/if}
			</div>
		</div>


		<!-- #playlist-share modal -->
		{if $share_link != '' && $playlist.visibility == $smarty.const.PLAYLIST_PUBLIC}
			{include file="modal-playlist-share.tpl"}
		{/if}

		<!-- #playlist-settings modal -->
		{if $playlist.type != $smarty.const.PLAYLIST_TYPE_WATCH_LATER && $playlist.type != $smarty.const.PLAYLIST_TYPE_HISTORY}
			{include file="modal-playlist-settings.tpl"}
		{/if}
	</div><!-- #content -->
	</div><!-- .row -->
</div><!-- .container-fluid -->     
{include file='footer.tpl'} 