<!-- Modal -->
<div class="modal animated fast slideInUp" id="modal-video-episodes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content-disabled">
			<div class="modal-body-disabled">

				<div class="pm-sidebar-playlist">
					<div class="pm-playlist-header">
						<div class="pm-playlist-name">
							<a href="{$episode_data.series_data.url}">{$episode_data.series_data.title} {$lang._season} {$episode_data.season_no}</a>
						</div>
						<div class="pm-playlist-data">
							<span class="pm-playlist-video-count">
								{$season_episode.episode_no} {$lang._episodes}
							</span>
						</div>
						<div class="pm-playlist-controls">
							<a href="#" class="btn btn-sm btn-outline bg-transparent text-white border-white border-1 mt-0 mr-1 mb-2" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">{$lang.close|default:"Close"}</span></a>
						</div>
					</div>

					<div class="pm-video-playlist">
						<ul class="list-unstyled">
						{if pm_count($seasons_data) > 0}
							{foreach from=$seasons_data key=season_no item=season name=seasons_loop}
								{if pm_count($season) > 0}
									{if $season_no == $episode_data.season_no}
										{foreach from=$season item=season_episode key=index name=season_episodes_loop}
											<li {if $episode_data.episode_id == $season_episode.episode_id}class="pm-video-playlist-playing"{/if}>

											<a href="{$season_episode.url}" title="{$season_episode.attr_alt}" class="pm-video-playlist-href"></a>

												<span class="pm-video-index">
												{if $episode_data.episode_id == $season_episode.episode_id}
													&#9658;
												{else}
													{$index+1}
												{/if}
												</span>
												<span class="pm-video-thumb pm-thumb-80">
													<span class="pm-video-li-thumb-info">
														<span class="pm-video-li-thumb-info">
															{if $season_episode.yt_length > 0}<span class="pm-label-duration border-radius3">{$season_episode.duration}</span>{/if}
														</span>
														{if $logged_in}
														<div class="watch-later">
															<button class="pm-watch-later-add btn btn-xs btn-default hidden-xs watch-later-add-btn-{$season_episode.id}" onclick="watch_later_add({$season_episode.id}); return false;" rel="tooltip" data-placement="left" title="{$lang.watch_later}"><i class="fa fa-clock-o"></i></button>
															<button class="pm-watch-later-remove btn btn-xs btn-success hidden-xs watch-later-remove-btn-{$season_episode.id}" onclick="watch_later_remove({$season_episode.id}); return false;" rel="tooltip" title="{$lang.playlist_remove_item}"><i class="fa fa-check"></i></button>
														</div>
														{else}
														<a class="pm-watch-later-add btn btn-xs btn-default hidden-xs" rel="tooltip" data-placement="left" title="{$lang.watch_later}" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#modal-login-form"><i class="fa fa-clock-o"></i></a>
														{/if}
														<a href="{$season_episode.url}" title="{$season_episode.attr_alt}" class="pm-thumb-fix pm-thumb-80">
															<span class="pm-thumb-fix-clip">
																<img src="{$season_episode.thumb_img_url}" alt="{$season_episode.video_title}" width="80">
																<span class="vertical-align"></span>
															</span>
														</a>
														</a>
													</span>
												</span>
												<h3><a href="{$season_episode.playlist_video_href}" class="pm-title-link"  rel="nofollow">{$season_episode.video_title}</a></h3>
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
				</div>

			</div>
		</div>
	</div>
</div>