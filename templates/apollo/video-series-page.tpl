{include file='header.tpl' p="general" tpl_name="video-series-page"}
{if pm_count($series_data) > 0}
<div id="content" class="content-series-page">
	<div class="series-header">
		<div class="row">
			<div class="col-md-12">
				<div class="pm-series-brief">
					<div class="pm-poster-img">
						<img src="{$series_data.image_url}" alt="{$series_data.attr_alt}" width="145">
					</div>

					<div class="pm-series-meta">
						<h1>
							{$series_data.title} {if $series_data.release_year}({$series_data.release_year}){/if}
							{if $logged_in && $is_admin == 'yes'}
								<a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/edit-series.php?do=edit&series_id={$series_data.series_id}" target="_blank" class="btn btn-sm btn-primary ml-3 py-1 px-2 hidden-xs hidden-sm" rel="tooltip" title="{$lang.edit} ({$lang._admin_only})">{$lang.edit}</a>
							{/if}
						</h1>

						{if !empty($series_data.description)}
							<div class="pm-series-description">
								<div itemprop="description">
									<p>{$series_data.description}</p>
								</div>
							</div>
						{/if}

						<div class="pm-series-details my-3 py-3">
							{if pm_count($series_data.genres) > 0}
							<div class="d-flex justify-content-start p-1">
								<span class="font-weight-bold wmin-100">{$lang._genre|default:"Genre"}</span>
								<ul class="list-unstyled list-inline">
									{foreach $series_data.genres key=k item=genre_data}
										<li><a href="{$genre_data.url}" class="font-weight-semibold">{$genre_data.name}</a></li>
									{/foreach}
								</ul>
							</div>
							{/if}

							<div class="d-flex justify-content-start p-1">
								<span class="font-weight-bold wmin-100">{$lang._seasons|default:"Seasons"}</span> <span>{$series_data.seasons_count} / {$series_data.seasons}</span>
							</div>
							<div class="d-flex justify-content-start p-1">
								<span class="font-weight-bold wmin-100">{$lang._episodes|default:"Episodes"}</span> <span>{$series_data.episodes_count} / {$series_data.episodes}</span>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="clearfix"></div>

<div class="container-fluid">
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		{foreach from=$seasons_data key=season_no item=season name=seasons_loop}

			<div class="panel panel-default">
				<div class="panel-heading" role="tab" id="heading_{$season_no}">
					<h4 class="panel-title">
						<a role="button" class="{if $smarty.foreach.seasons_loop.index != 0}collapsed{/if}" data-toggle="collapse" data-parent="#accordion" href="#collapse_{$season_no}" aria-expanded="{if $smarty.foreach.seasons_loop.index == 0}true{else}false{/if}" aria-controls="collapse_{$season_no}">
							{$lang._season|default:'Season'} {$season_no}
						</a>
					</h4>
				</div>

				{if pm_count($season) > 0}
				<div id="collapse_{$season_no}" class="panel-collapse collapse {if $smarty.foreach.seasons_loop.index == 0}in{/if}" role="tabpanel" aria-labelledby="heading_{$season_no}">
				  <div class="panel-body">
					<ul class="pm-ul-list-episodes list-unstyled">
						{foreach from=$season item=item_data name=season_episodes_loop}
							<li class="pm-episode {if pm_count($item_data.media_sources) == 0}without-sources{/if}">
								<a href="{$item_data.url}" title="{$item_data.attr_alt}" class="d-flex justify-content-start py-3">
									<span class="identifier text-muted d-inline-flex align-items-center">
										S{$item_data.season_no} - E{$item_data.episode_no}
									</span>
									<span class="title d-inline-flex align-items-center">
										<h3>
											{if $item_data.video_title}
												{$item_data.video_title}
											{else} 
												Season {$item_data.season_no} - Episode {$item_data.episode_no}
											{/if}
										</h3>
										{if $item_data.restricted == '1'}
										<span class="align-bottom text-muted ml-3" rel="tooltip" title="{$lang.require_registration|default:'Requires registration'}">
											<i class="fa fa-lock"></i>
										</span>
										{/if}
									</span>

									<span class="meta text-muted d-inline-flex ml-auto align-items-center">
										<span class="hidden-xs">
										{$item_data.release_date|date_format:"%B %d, %Y"}
										</span>

										{if $logged_in && $is_admin == 'yes'}
											<button onclick="event.preventDefault(); window.open('{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/edit-episode.php?episode_id={$item_data.episode_id}')" class="btn btn-sm btn-primary ml-3 py-1 px-2 hidden-xs hidden-sm" rel="tooltip" title="{$lang.edit} ({$lang._admin_only})">{$lang.edit}</button> 
										{/if}
									</span>
								</a>
							</li>
						{/foreach}
					</ul>
				  </div>
				</div>
				{/if}
			</div>
		{foreachelse}
		<div class="row">
			<div class="col-md-12 text-center">
				<p></p>
				<p class="m-3 p-3">{$lang.browse_msg2}</p>			
			</div>
		</div>
		{/foreach}
	</div><!--.panel-group-->

{if pm_count($related_series) > 0}
	<div class="row">
		<div class="col-md-12">
			<div class="pm-section-head">
				<h2>{$lang.you_might_like|default:"You might also like"}</h2>
				<div class="btn-group btn-group-sort">
				<button class="btn btn-xs" id="pm-slide-prev_related"><i class="fa fa-chevron-left"></i></button>
				<button class="btn btn-xs" id="pm-slide-next_related"><i class="fa fa-chevron-right"></i></button>
				</div>
			</div>
			<div id="">
			<!-- Carousel items -->
				<ul class="pm-ul-carousel-videos pm-ul-carousel-series list-inline" data-slider-id="related" data-slides="7" id="pm-carousel_related">
					{foreach from=$related_series key=k item=item_data name=related_series_loop}
						<li>
							{include file='item-series-obj.tpl' hideLabels='1' hideMeta='0' thumbSize='poster' isObj='series'}
						</li>
					{/foreach}
				</ul>
			</div><!-- #pm-slider -->
		</div>
	</div>
{/if}

</div>

{else}

<div id="content" class="content-series-page">
	<div class="row">
		<div class="col-md-12 text-center">
			<p class="m-3 p-3">{$lang.browse_msg2}</p>
		</div>
	</div>
</div>
{/if}

</div>
{include file="footer.tpl" tpl_name="video-series-page"}