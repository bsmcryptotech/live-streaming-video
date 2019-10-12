
{include file="header.tpl" p="detail" tpl_name="video-watch-episode"}
<div class="pm-section-highlighted">
	<div class="container-fluid">
		<div class="row">
			<div class="container">
				<div class="row pm-video-heading">
					<div class="col-xs-12 col-sm-12 col-md-10">
						{if $episode_data.featured == 1}
						<span class="label label-featured">{$lang.featured|default:'Featured'}</span>
						{/if}
						<h1 itemprop="name">{if !empty($episode_data.video_title)}{$episode_data.video_title}{else}{$episode_data.series_data.title} (S{$episode_data.season_no}E{$episode_data.episode_no}){/if}</h1>
						{if $episode_data.series_data.url}<h6><a href="{$episode_data.series_data.url}">{$episode_data.series_data.title}</a> (S{$episode_data.season_no} E{$episode_data.episode_no})</h6>{/if}
					</div>

					<div class="hidden-xs hidden-sm col-md-2">
						<div class="pm-video-adjust btn-group">
							{if $logged_in && $is_admin == 'yes'}
							<a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/edit-episode.php?episode_id={$episode_data.episode_id}" class="btn btn-sm btn-default" rel="tooltip" title="{$lang.edit} ({$lang._admin_only})" target="_blank">{$lang.edit}</a>
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
							<!-- media sources tabs/list -->
							{if pm_count($episode_data.media_sources) > 1}
							<div class="float-right">
								<div class="btn-group btn-group-nice-dropdown">
									<a href="#" class="dropdown-toggle toggle-strong btn btn-sm btn-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										{$lang.available_sources|default:"Available Sources"} <span class="badge">{pm_count($episode_data.media_sources)}</span> <span class="caret"></span></h4>
									</a>
									<ul class="dropdown-menu animated fast absoluteSlideInUp" id="media-sources-tab">
									{foreach from=$episode_data.media_sources key=ms_index item=media name=media_sources_tabs_loop}
										<li>
											<a href="#play{$ms_index+1}" class="episode-switch-player" data-target="#play{$ms_index+1}" data-toggle="tab" data-ms-index="{$ms_index}" data-ms-id="{$media.id}">{$lang._source|default:"Source"} {$ms_index+1}</a>
										</li>
									{/foreach}
									</ul>
								</div>
							</div>
							<div class="clearfix"></div>
							{/if}
							<!-- Player -->
							<div id="episode-player-container">
								{foreach from=$episode_data.media_sources key=ms_index item=media name=media_sources_player_loop}
									{if $ms_index == $preselected_video_index || ($ms_index == 0 && empty($preselected_video_index))}
										{$media.player_html}
									{/if}
								{foreachelse}
									<div class="blank-player">{$lang.no_source_player|default:"Sorry, no available source yet."}</div>
								{/foreach}

								{if $ad_900 != ''}
								<div class="pm-ads-inplayer alert-dismissible">
									{$ad_900}
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								{/if}
							</div>

						{/if}
						</div><!--video-wrapper-->
					</div><!--/#player-->

					<div class="clearfix"></div>
					<div class="pm-episodes-nav d-flex justify-content-center flex-nowrap">
						<div class="align-self-center pm-nav-back">
							{if $prev_episode_data}
							<a href="{$prev_episode_data.url}" rel="tooltip" title="{$lang.prev_episode|default:'Previous Episode'}"><i class="mico md-18 mico-arrow_back"></i></a>
							{/if}
						</div>
						<div class="align-self-center pm-nav-modal">
							<a href="#" data-toggle="modal" data-target="#modal-video-episodes"><i class="mico md-18 mico-format_list_bulleted"></i> {$lang.episodes_list|default:"EPISODES LIST"}</a>
						</div>
						<div class="align-self-center pm-nav-forward">
							<!-- next episode data --> 
							{if $next_episode_data}
							<a href="{$next_episode_data.url}" rel="tooltip" title="{$lang.next_episode|default:'Next Episode'}"><i class="mico md-18 mico-arrow_forward"></i></a>
							{/if}
						</div>
					</div>

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
				{$episode_data.site_views_formatted} {$lang.views}
			</span>
			<div class="clearfix"></div>
		</div>
		<div class="col-xs-8 col-sm-7 col-md-6">
			<ul class="pm-video-main-methods list-inline pull-right nav nav-pills">
			<li>
				<button class="btn btn-video {if $bin_rating_vote_value == 1}active{/if}" id="bin-rating-like" type="button" rel="tooltip" data-title="{$episode_data.up_vote_count_formatted} {$lang._likes}"><i class="mico mico-thumb_up"></i> <span class="hidden-xs">{$episode_data.up_vote_count_formatted}</span></button>
				<button class="btn btn-video {if $bin_rating_vote_value == 0 && $bin_rating_vote_value !== false}active{/if}" id="bin-rating-dislike" type="button" rel="tooltip" data-title="{$episode_data.down_vote_count_formatted} {$lang._dislikes}"><i class="mico mico-thumb_down"></i> <span class="hidden-xs">{$episode_data.down_vote_count_formatted}</span></button>

				<input type="hidden" name="bin-rating-uniq_id" value="{$episode_data.uniq_id}">

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
				<li><a href="#" id="pm-vc-playlists" rel="tooltip" title="{$lang.add_to_playlist}" data-video-id="{$episode_data.id}" data-toggle="modal" data-target="#modal-video-addtoplaylist"><i class="mico mico-playlist_add"></i></a></li>
				{/if}
				<li><a href="#" id="" rel="tooltip" title="{$lang._share}" data-video-id="{$episode_data.id}" data-toggle="modal" data-target="#modal-video-share"><i class="mico mico-share"></i></a></li>
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
				   <a href="{$episode_data.author_profile_href}"><img src="{$episode_data.author_avatar_url}" class="pm-round-avatar" height="40" width="40" alt="" border="0"></a>
				</div>
				<div class="col-xs-9 col-sm-8 col-md-8">
					<div class="pm-video-posting-info">
						<div class="author"><a href="{$episode_data.author_profile_href}">{$episode_data.author_username}</a> {if $episode_data.author_data.channel_verified == 1 && $smarty.const._MOD_SOCIAL}<a href="#" rel="tooltip" title="{$lang.verified_channel|default:'Verified Channel'}" class="pm-verified-user"><img src="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/ico-verified.png" /></a>{/if}</div>
						<div class="publish-date">{$lang._published} <time datetime="{$episode_data.html5_datetime}" title="{$episode_data.full_datetime}">{$episode_data.html5_datetime|date_format:"%b %e, %Y"}</time></div>
					 </div>
				</div>
				<div class="col-xs-2 col-sm-3 col-md-3">
					{if $smarty.const._MOD_SOCIAL && $logged_in == '1' && $episode_data.author_user_id != $s_user_id}
						<div class="pull-right">{include file="user-subscribe-button.tpl" profile_data=$video_data profile_user_id=$episode_data.author_user_id}</div>
					{/if}
				</div>
			</div><!--/.pm-user-header-->

			<div class="clearfix"></div>
			
			<div class="pm-video-description">
				{if !empty($episode_data.description)}
				<div itemprop="description">
					{$episode_data.description}
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

			{include file="comments.tpl" tpl_name="video-watch" allow_comments=$episode_data.allow_comments}
		</div><!-- /pm-video-watch-main -->

		<div class="col-xs-12 col-sm-12 col-md-4 pm-video-watch-sidebar">

			<div id="pm-related">
				{if pm_count($seasons_data) > 0}
					<div class="btn-group btn-group-nice-dropdown">
						<a href="#" type="button" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<h4>{$episode_data.series_data.title} {$lang._season} {$episode_data.season_no} <span class="caret"></span></h4>
						</a>
						<ul class="dropdown-menu animated fast absoluteSlideInUp">
						{foreach from=$seasons_data key=season_no item=season name=seasons_loop}
							{if pm_count($season) > 0}
								{foreach from=$season item=season_episode key=index name=season_episodes_loop}
									{if $smarty.foreach.season_episodes_loop.index == 0}
									<li>
										{if $season_no == $episode_data.season_no}
										<a href="{$season_episode.url}" class="active">{$lang._season} {$season_no} </a>
										{else}
										<a href="{$season_episode.url}">{$lang._season} {$season_no} </a>
										{/if}
									</li>
									{/if}
								{/foreach}
							{/if}
						{/foreach}
						</ul>
					</div>
				{else}
					<h4><a href="{$episode_data.series_data.url}">{$episode_data.series_data.title} {$lang._season} {$episode_data.season_no}</a></h4>
				{/if}
				<div class="clearfix"></div>

				<ul class="pm-ul-sidelist-videos pm-ul-sidelist-episodes list-unstyled">
					{if pm_count($seasons_data) > 0}
						{foreach from=$seasons_data key=season_no item=season name=seasons_loop}
							{if pm_count($season) > 0}
								{if $season_no == $episode_data.season_no}
									{foreach from=$season item=season_episode name=season_episodes_loop}
										<li {if $episode_data.episode_id == $season_episode.episode_id}class="li-now-playing"{/if}>
											<div class="pm-video-thumb">
												{if $season_episode.yt_length > 0}<span class="pm-label-duration">{$season_episode.duration}</span>{/if}
												<div class="watch-later">
													<button class="pm-watch-later-add btn btn-xs btn-default hidden-xs watch-later-add-btn-{$season_episode.id}" onclick="watch_later_add({$season_episode.id}); return false;" rel="tooltip" data-placement="left" title="{$lang.watch_later}"><i class="fa fa-clock-o"></i></button>
													<button class="pm-watch-later-remove btn btn-xs btn-success hidden-xs watch-later-remove-btn-{$season_episode.id}" onclick="watch_later_remove({$season_episode.id}); return false;" rel="tooltip" title="{$lang.playlist_remove_item}"><i class="fa fa-check"></i></button>
												</div>
												<a href="{$season_episode.url}" title="{$season_episode.attr_alt}">
												<div class="pm-video-labels hidden-xs">
													{if $episode_data.episode_id == $season_episode.episode_id}
														<span class="label label-new">{$lang.now_playing}</span>
													{/if}
												</div>
													{if $season_episode.thumb_img_url == ''}
													<img src="{$smarty.const._URL}/templates/{$template_dir}/img/melody-lzld.png" alt="{$season_episode.attr_alt}" data-echo="{$season_episode.series_data.image}" class="img-responsive">
													{else}
													<img src="{$smarty.const._URL}/templates/{$template_dir}/img/melody-lzld.png" alt="{$season_episode.attr_alt}" data-echo="{$season_episode.thumb_img_url}" class="img-responsive">
													{/if}
												</a>
											</div>
											<h3><a href="{$season_episode.url}" title="{$season_episode.attr_alt}" class="ellipsis">{$season_episode.video_title|truncate:100}</a></h3>
											<div class="pm-video-meta">
												<span class="pm-video-author">{$lang._by} <a href="{$season_episode.author_profile_href}">{$season_episode.author_username}</a></span>
												<span class="pm-video-since"><time datetime="{$season_episode.html5_datetime}" title="{$season_episode.full_datetime}">{$season_episode.time_since_added} {$lang.ago}</time></span>
												<span class="pm-video-views">{$season_episode.views_compact} {$lang.views}</span>
											</div>
										</li>
									{/foreach}
								{/if}
							{/if}
						{/foreach}
					{else}
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

{include file="modal-video-report.tpl" video_data=$episode_data}
{include file="modal-video-addtoplaylist.tpl"}
{include file='modal-video-share.tpl'}
{include file="modal-video-episodes.tpl"}

{include file="footer.tpl" p="detail" tpl_name="video-watch-episode"}