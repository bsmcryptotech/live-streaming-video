<ul class="row pm-ul-browse-playlists list-unstyled mt-3">
	{foreach from=$playlists key=k item=playlist_data name=playlists_foreach}
		<li class="col-xs-6 col-sm-4 col-md-3">
			<div class="thumbnail">
				<div class="pm-video-thumb">
					<img src="{$playlist_data.thumb_url}" alt="{$playlist_data.title}" class="img-responsive">
					<div class="pm-pl-count"><span class="pm-pl-items">{$playlist_data.items_count}</span> {if $playlist_data.items_count == 1}{$lang._video}{else}{$lang.videos}{/if}</div> 
					<a href="{$playlist_data.playlist_watch_all_href}" class="thumbnail-overlay" rel="nofollow">&#9658; {$lang.play_all}</a>
				</div>
				<div class="caption">
				<h3 class="ellipsis-line">{if $playlist_data.visibility == $smarty.const.PLAYLIST_PRIVATE}<i class="fa fa-lock"></i> {/if}<a href="{if $s_user_id == $playlist_data.user_id}{$playlist_data.playlist_href}{else}{$playlist_data.playlist_watch_all_href}{/if}" title="{$playlist_data.title}">{$playlist_data.title|truncate:50}</a></h3>
				<div class="pm-video-meta hidden-xs">
					<span class="pm-video-since"><time datetime="{$playlist_data.html5_datetime}" title="{$playlist_data.full_datetime}">{$playlist_data.time_since} {$lang.ago}</time></span>
				</div>
				</div>
			</div>
		</li>
	{/foreach}
</ul>