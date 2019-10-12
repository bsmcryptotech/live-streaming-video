{include file="header.tpl" p="detail" tpl_name="video-watch"}
<div class="pm-section-highlighted">
	<div class="container-fluid">
		<div class="row">
			{if $logged_in && $is_admin == 'yes' && $video_data.in_trash}
			<div class="alert alert-warning">
				{$lang.viewing_deleted_video}
			</div>
			{/if}
			<div class="container">
				<div class="row pm-video-heading">
					<div class="col-xs-12 col-sm-12 col-md-10">
						{if $video_data.featured == 1}
						<span class="label label-featured">{$lang.featured|default:'Featured'}</span>
						{/if}
						<h1 itemprop="name">{$video_data.video_title}</h1>
						{if $playlist}<h6>{$lang.from_playlist|default:'This video is part of the %s playlist.'|sprintf:$playlist.title}</h6>{/if}
						<meta itemprop="duration" content="{$video_data.iso8601_duration}" />
						<meta itemprop="thumbnailUrl" content="{$video_data.thumb_img_url}" />
						<meta itemprop="contentURL" content="{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}" />
						{if $video_data.allow_embedding == 1}
						<meta itemprop="embedURL" content="{$video_data.embed_href}" />
						{/if}
						<meta itemprop="uploadDate" content="{$video_data.html5_datetime}" />
					</div>
					<div class="hidden-xs hidden-sm col-md-2">
						<div class="pm-video-adjust btn-group">
							{if $logged_in && $is_admin == 'yes' && ! $video_data.in_trash}
							<a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/edit-video.php?vid={$video_data.uniq_id}" class="btn btn-sm btn-default" rel="tooltip" title="{$lang.edit} ({$lang._admin_only})" target="_blank">{$lang.edit}</a> <a href="#" onclick="return confirm_action(pm_lang.delete_video_confirmation, '{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/edit-video.php?vid={$video_data.uniq_id}&a=3'); return false;" class="btn btn-sm btn-danger" rel="tooltip" title="{$lang.trash|default:'Trash'} ({$lang._admin_only})" target="_blank">{$lang.trash|default:'Trash'}</a>
							{/if}
						</div>
					</div>
				</div><!-- /.pm-video-watch-heading -->

				<div class="row">
					<div id="player" class="{if $ad_5}col-xs-12 col-sm-12 col-md-8 narrow-player{else}col-xs-12 col-sm-12 col-md-12 wide-player{/if}">
						<div id="video-wrapper">
						{if $display_preroll_ad == true}
						<div id="preroll_placeholder">
							<div class="preroll_countdown">
							{$lang.preroll_ads_timeleft} <span class="preroll_timeleft">{$preroll_ad_data.timeleft_start}</span>

								{if $preroll_ad_data.skip}
								<div class="preroll_skip_button">
									<div class="btn btn-sm btn-success preroll_skip_countdown"  disabled="disabled" id="">
										{$lang.preroll_ads_skip} (<span class="preroll_skip_timeleft">{$preroll_ad_data.skip_delay_seconds}</span>)
									</div>
									<button class="btn btn-sm btn-success hide-me" id="preroll_skip_btn">{$lang.preroll_ads_skip}</button>
								</div>
								{/if}

							</div>
							{$preroll_ad_data.code}
							{if $preroll_ad_data.disable_stats == 0}
								<img src="{$smarty.const._URL}/ajax.php?p=stats&do=show&aid={$preroll_ad_data.id}&at={$smarty.const._AD_TYPE_PREROLL}" width="1" height="1" border="0" />
							{/if}
						</div>
						{else}
							{include file="player.tpl" page="detail"}
						{/if}
						</div><!--video-wrapper-->
					</div><!--/#player-->

					{if $ad_5}
					<div class="col-xs-12 col-sm-12 col-md-4">
						<div class="pm-ads-banner" align="center">{$ad_5}</div>
					</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>

<div id="content">
{if $show_addthis_widget == '1'}
{include file='widget-socialite.tpl'}
{/if}

<div id="video-control">
	<div class="row pm-video-control">
		<div class="col-xs-4 col-sm-5 col-md-6">
			<span class="pm-video-views">
				{$video_data.site_views_formatted} {$lang.views}
			</span>
			<div class="clearfix"></div>
		</div>
		<div class="col-xs-8 col-sm-7 col-md-6">
			<ul class="pm-video-main-methods list-inline pull-right nav nav-pills">
				<li>
			<button class="btn btn-video {if $bin_rating_vote_value == 1}active{/if}" id="bin-rating-like" type="button" rel="tooltip" data-title="{$video_data.up_vote_count_formatted} {$lang._likes}"><i class="mico mico-thumb_up"></i> <span class="hidden-xs">{$video_data.up_vote_count_formatted}</span></button>
			<button class="btn btn-video {if $bin_rating_vote_value == 0 && $bin_rating_vote_value !== false}active{/if}" id="bin-rating-dislike" type="button" rel="tooltip" data-title="{$video_data.down_vote_count_formatted} {$lang._dislikes}"><i class="mico mico-thumb_down"></i> <span class="hidden-xs">{$video_data.down_vote_count_formatted}</span></button>

			<input type="hidden" name="bin-rating-uniq_id" value="{$video_data.uniq_id}">

			<!-- <div id="bin-rating-response" class="hide-me alert"></div> -->
			<div id="bin-rating-like-confirmation" class="hide-me alert animated fadeInDown">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{$lang.confirm_like}</p>
				<p>
				<a href="https://www.facebook.com/sharer.php?u={$facebook_like_href}&amp;t={$facebook_like_title}" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Facebook"><i class="pm-vc-sprite facebook-icon"></i></a>
				<a href="https://twitter.com/home?status=Watching%20{$facebook_like_title}%20on%20{$facebook_like_href}" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Twitter"><i class="pm-vc-sprite twitter-icon"></i></a>
				</p>
			</div>

			<div id="bin-rating-dislike-confirmation" class="hide-me alert animated fadeInDown">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<p>{$lang.confirm_dislike}</p>
			</div>
				</li>

				{if $logged_in}
				<li><a href="#" id="pm-vc-playlists" rel="tooltip" title="{$lang.add_to_playlist}" data-video-id="{$video_data.id}" data-toggle="modal" data-target="#modal-video-addtoplaylist"><i class="mico mico-playlist_add"></i></a></li>
				{/if}
				<li><a href="#" id="" rel="tooltip" title="{$lang._share}" data-video-id="{$video_data.id}" data-toggle="modal" data-target="#modal-video-share"><i class="mico mico-share"></i></a></li>
				<li><a href="#" rel="tooltip" title="{$lang.report_video}" data-toggle="modal" data-target="#modal-video-report"><i class="mico mico-report"></i></a></li>
			</ul>
		</div>
	</div><!--.pm-video-control-->
</div>

<div id="content-main" class="container-fluid">
	<div class="row">

		<div class="col-xs-12 col-sm-12 col-md-8 pm-video-watch-main" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
			{if $ad_3 != ''}
			<div class="pm-ads-banner" align="center">{$ad_3}</div>
			{/if}
	
			<div class="row pm-user-header">
				<div class="col-xs-3 col-sm-1 col-md-1">
				   <a href="{$video_data.author_profile_href}"><img src="{$video_data.author_avatar_url}" class="pm-round-avatar" height="40" width="40" alt="" border="0"></a>
				</div>
				<div class="col-xs-9 col-sm-8 col-md-8">
					<div class="pm-video-posting-info">
						<div class="author"><a href="{$video_data.author_profile_href}">{$video_data.author_username}</a> {if $video_data.author_data.channel_verified == 1 && $smarty.const._MOD_SOCIAL}<a href="#" rel="tooltip" title="{$lang.verified_channel|default:'Verified Channel'}" class="pm-verified-user"><img src="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/ico-verified.png" /></a>{/if}</div>
						<div class="publish-date">{$lang._published} <time datetime="{$video_data.html5_datetime}" title="{$video_data.full_datetime}">{$video_data.html5_datetime|date_format:"%b %e, %Y"}</time></div>
					 </div>
				</div>
				<div class="col-xs-2 col-sm-3 col-md-3">
					{if $smarty.const._MOD_SOCIAL && $logged_in == '1' && $video_data.author_user_id != $s_user_id}
						<div class="pull-right">{include file="user-subscribe-button.tpl" profile_data=$video_data profile_user_id=$video_data.author_user_id}</div>
					{/if}
				</div>
			</div><!--/.pm-user-header-->

			<div class="clearfix"></div>
			
			<div class="pm-video-description">
				{if !empty($video_data.description)}
				<div itemprop="description">
					{$video_data.description}
				</div>
				{/if}

				<dl class="dl-horizontal">

					{if !empty($category_name)}
					<dt>{$lang.category}</dt>
					<dd>{$category_name}</dd>
					{/if}
					{if !empty($tags)}
					<dt>{$lang.tags}</dt>
					<dd>{$tags}</dd>
					{/if}
				</dl>
			</div>

			{include file="comments.tpl" tpl_name="video-watch" allow_comments=$video_data.allow_comments}
		</div><!-- /pm-video-watch-main -->
		
		<div class="col-xs-12 col-sm-12 col-md-4 pm-video-watch-sidebar">
			{if $playlist}
			<div class="pm-sidebar-playlist">
				<div class="pm-playlist-header">
					<div class="pm-playlist-name">
						<a href="#">{$playlist.title|truncate:50}</a>
					</div>
					<div class="pm-playlist-data">
						<span class="pm-playlist-creator">
							{$lang._by} <a href="{$playlist.user_profile_href}">{$playlist.user_name}</a>
						</span> 
						&ndash; 
						<span class="pm-playlist-video-count">
							{if $playlist.items_count == 1}
								1 {$lang._video}
							{else}
								{$playlist.items_count} {$lang.videos}
							{/if}
						</span>
					</div>
					<div class="pm-playlist-controls">
						<a href="{$playlist_prev_url}" rel="nofollow"><i class="mico mico-skip_previous" rel="tooltip" title="{$lang.playlist_prev_video}"></i></a> 
						<a href="{$playlist_next_url}" rel="nofollow"><i class="mico mico-skip_next" rel="tooltip" title="{$lang.playlist_next_video}"></i></a>
						{if $playlist.user_id == $s_user_id} 
						<a href="{$playlist.playlist_href}" rel="nofollow"><i class="mico mico-settings" rel="tooltip" title="{$lang.playlist_settings}"></i></a>
						{/if}
					</div>
				</div>

				<div class="pm-video-playlist">
					<ul class="list-unstyled">
						{foreach from=$playlist_items key=index item=list_video}
						<li {if $video_data.id == $list_video.id}class="pm-video-playlist-playing"{/if}>
						<a href="{$list_video.playlist_video_href}" class="pm-video-playlist-href" rel="nofollow"></a>

							<span class="pm-video-index">
							{if $video_data.id == $list_video.id}
								&#9658;
							{else}
								{$index+1}
							{/if}
							</span>
							<span class="pm-video-thumb pm-thumb-80">
								<span class="pm-video-li-thumb-info">
									<span class="pm-video-li-thumb-info">
										{if $list_video.yt_length > 0}<span class="pm-label-duration border-radius3">{$list_video.duration}</span>{/if}
									</span>
									{if $logged_in}
									<div class="watch-later">
										<button class="pm-watch-later-add btn btn-xs btn-default hidden-xs watch-later-add-btn-{$list_video.id}" onclick="watch_later_add({$list_video.id}); return false;" rel="tooltip" data-placement="left" title="{$lang.watch_later}"><i class="fa fa-clock-o"></i></button>
										<button class="pm-watch-later-remove btn btn-xs btn-success hidden-xs watch-later-remove-btn-{$list_video.id}" onclick="watch_later_remove({$list_video.id}); return false;" rel="tooltip" title="{$lang.playlist_remove_item}"><i class="fa fa-check"></i></button>
									</div>
									{else}
									<a class="pm-watch-later-add btn btn-xs btn-default hidden-xs" rel="tooltip" data-placement="left" title="{$lang.watch_later}" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#modal-login-form"><i class="fa fa-clock-o"></i></a>
									{/if}
									<a href="{$list_video.playlist_video_href}" class="pm-thumb-fix pm-thumb-80" rel="nofollow">
										<span class="pm-thumb-fix-clip">
											<img src="{$list_video.thumb_img_url}" alt="{$list_video.video_title}" width="80">
											<span class="vertical-align"></span>
										</span>
									</a>
								</span>
							</span>
							<h3><a href="{$list_video.playlist_video_href}" class="pm-title-link"  rel="nofollow">{$list_video.video_title}</a></h3>
						</li>
						{/foreach}
					</ul>
				</div>
			</div>
			<div class="clearfix"></div>
			{/if}
			
			<div class="{if ! $playlist && $video_data.video_player_autoplay_next_support}pm-related-with-autoplay{/if} {if $smarty.cookies.pm_autoplay_next == 'on'}with-highlight{else}without-highlight{/if}" id="pm-related">
				<h4>{if $playlist}{$lang.tab_related}{else}{$lang.up_next|default:'Up Next'}{/if}</h4>
				{if ! $playlist && $video_data.video_player_autoplay_next_support}
				<div class="pm-autoplay-select">
					<div class="pm-autoplay-info">
					{$lang._autoplay} 
					</div>
					<div class="pm-autoplay-switch">
						<input type="checkbox" name="pm-autoplay-switch" class="autoplayonoff-checkbox" id="autoplayonoff" {if $smarty.cookies.pm_autoplay_next == "on"}checked="checked"{/if}>
						<label class="autoplayonoff-label" for="autoplayonoff">
							<span class="autoplayonoff-inner"></span>
							<span class="autoplayonoff-switch"></span>
						</label>
					</div>
				</div>
				{/if}

				<ul class="pm-ul-sidelist-videos list-unstyled">
				{foreach from=$related_video_list key=k item=related_video_data}
				<li>
					<div class="pm-video-thumb">
						{if $related_video_data.yt_length > 0}<span class="pm-label-duration">{$related_video_data.duration}</span>{/if}
						<div class="watch-later">
							<button class="pm-watch-later-add btn btn-xs btn-default hidden-xs watch-later-add-btn-{$related_video_data.id}" onclick="watch_later_add({$related_video_data.id}); return false;" rel="tooltip" data-placement="left" title="{$lang.watch_later}"><i class="fa fa-clock-o"></i></button>
							<button class="pm-watch-later-remove btn btn-xs btn-success hidden-xs watch-later-remove-btn-{$related_video_data.id}" onclick="watch_later_remove({$related_video_data.id}); return false;" rel="tooltip" title="{$lang.playlist_remove_item}"><i class="fa fa-check"></i></button>
						</div>
						<a href="{$related_video_data.video_href}" title="{$related_video_data.attr_alt}">
						<div class="pm-video-labels hidden-xs">
							{if $related_video_data.mark_new}<span class="label label-new">{$lang._new}</span>{/if}
						</div>
						<img src="{$smarty.const._URL}/templates/{$template_dir}/img/melody-lzld.png" alt="{$related_video_data.attr_alt}" data-echo="{$related_video_data.thumb_img_url}" class="img-responsive">
						</a>
					</div>
					<h3><a href="{$related_video_data.video_href}" title="{$related_video_data.attr_alt}" class="ellipsis">{$related_video_data.video_title|truncate:100}</a></h3>
					<div class="pm-video-meta">
						<span class="pm-video-author">{$lang._by} <a href="{$related_video_data.author_profile_href}">{$related_video_data.author_username}</a></span>
						<span class="pm-video-since"><time datetime="{$related_video_data.html5_datetime}" title="{$related_video_data.full_datetime}">{$related_video_data.time_since_added} {$lang.ago}</time></span>
						<span class="pm-video-views">{$related_video_data.views_compact} {$lang.views}</span>
					</div>
				</li>
				{/foreach}

				{foreach from=$popular_video_list key=k item=popular_video_data}
				<li>
					<div class="pm-video-thumb">
						{if $popular_video_data.yt_length > 0}<span class="pm-label-duration">{$popular_video_data.duration}</span>{/if}
						<div class="watch-later">
							<button class="pm-watch-later-add btn btn-xs btn-default hidden-xs watch-later-add-btn-{$popular_video_data.id}" onclick="watch_later_add({$popular_video_data.id}); return false;" rel="tooltip" data-placement="left" title="{$lang.watch_later}"><i class="fa fa-clock-o"></i></button>
							<button class="pm-watch-later-remove btn btn-xs btn-success hidden-xs watch-later-remove-btn-{$popular_video_data.id}" onclick="watch_later_remove({$popular_video_data.id}); return false;" rel="tooltip" title="{$lang.playlist_remove_item}"><i class="fa fa-check"></i></button>
						</div>
						<a href="{$popular_video_data.video_href}" title="{$popular_video_data.attr_alt}">
						<div class="pm-video-labels hidden-xs">
							{if $popular_video_data.mark_new}<span class="label label-new">{$lang._new}</span>{/if}
						</div>
						<img src="{$popular_video_data.thumb_img_url}" alt="{$popular_video_data.attr_alt}" class="img-responsive"></a>
					</div>
					<h3><a href="{$popular_video_data.video_href}" title="{$popular_video_data.attr_alt}" class="ellipsis">{$popular_video_data.video_title}</a></h3>
					<div class="pm-video-meta">
						<span class="pm-video-author">{$lang._by} <a href="{$popular_video_data.author_profile_href}">{$popular_video_data.author_username}</a></span>
						<span class="pm-video-since"><time datetime="{$popular_video_data.html5_datetime}" title="{$popular_video_data.full_datetime}">{$popular_video_data.time_since_added} {$lang.ago}</time></span>
						<span class="pm-video-views">{$popular_video_data.views_compact} {$lang.views}</span>
					</div>
				</li>
				{/foreach}

				{if ! $related_video_list && ! $popular_video_list}
				<li>
				  {$lang.top_videos_msg2}
				</li>
				{/if}
				</ul>
			</div>
		</div><!-- /pm-video-watch-sidebar -->

		<div class="clearfix"></div>
	</div>
</div>

{include file="modal-video-report.tpl"}
{include file="modal-video-addtoplaylist.tpl"}
{include file='modal-video-share.tpl'}

{include file="footer.tpl" p="detail" tpl_name="video-watch"}