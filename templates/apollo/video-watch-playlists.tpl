<div id="playlist-container">
	{if pm_count($my_playlists) > 0}
	<ul class="pm-playlist-items list-group">
		{foreach from=$my_playlists key=index  item=playlist_data name=my_playlists_foreach}
		<li data-playlist-id="{$playlist_data.list_id}" class="list-group-item{if $playlist_data.has_current_video} pm-playlist-item-selected{/if}">
		<!--<li class="pm-playlist-item-selected">-->
			<a href="#" onclick="{if $playlist_data.has_current_video}playlist_remove_item({$playlist_data.list_id}, {$video_data.id});{else}playlist_add_item({$playlist_data.list_id}, {$video_data.id});{/if} return false;">
				<span class="pm-playlist-response">
					{if $playlist_data.has_current_video}
						<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
						<polyline class="path check" fill="none" stroke="#73AF55" stroke-width="16" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
						</svg>
<!-- 						<span class="label label-success">{$lang.added}</span> -->
					{else}
						<span class="pm-playlist-response"></span>
					{/if}
				</span>
				<span class="pm-playlists-name">
					{$playlist_data.title|truncate:40} 
				</span> 
				<span class="pm-playlist-visibility">
					{if $playlist_data.visibility == $smarty.const.PLAYLIST_PUBLIC}
						<!--{$lang.public}-->
					{/if}
					{if $playlist_data.visibility == $smarty.const.PLAYLIST_PRIVATE}
						<span rel="tooltip" title="{$lang.private}"><i class="mico mico-lock"></i></span>
					{/if}
				</span>
				<span class="pm-playlists-video-count">{$playlist_data.items_count} {$lang.videos}</span>
<!-- 				<span class="pm-playlist-created">
					<time datetime="{$playlist_data.html5_datetime}" title="{$playlist_data.full_datetime}">{$playlist_data.time_since} {$lang.ago}</time>
				</span> -->
			</a>
		{/foreach}
	</ul>
	{else}
	<img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" alt="{$lang.please_wait}" align="absmiddle" border="0" width="16" height="16" /> {$lang.please_wait}
	{/if}
</div>