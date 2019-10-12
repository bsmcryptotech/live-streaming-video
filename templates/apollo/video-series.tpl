{include file='header.tpl' p="general" tpl_name="video-series"}

<div id="content">
	<div id="category-header" class="container-fluid">
		<div class="pm-category-highlight">
			<h1>{if $meta_title =! $smarty.const._SITENAME}{$meta_title}{/if}</h1>
		</div>

		{if $genres.$genre_id.child_count > 0}
			<div class="row pm-category-header-subcats">
				<div class="col-md-12">
					<div class="pm-section-head">
					<h2>{$lang.related_genres|default:"Related Genres"}</h2>
						<div class="btn-group btn-group-sort">
							<button class="btn btn-xs" id="pm-slide-prev_subcategories"><i class="fa fa-chevron-left"></i></button>
							<button class="btn btn-xs" id="pm-slide-next_subcategories"><i class="fa fa-chevron-right"></i></button>
						</div>
					</div>
					<ul class="pm-ul-carousel-videos list-inline pm-ul-browse-subcategories thumbnails mt-3" data-slider-id="subcategories" data-slides="5" id="pm-carousel_subcategories">
					{foreach from=$genres item=genre_array name=genres_items_loop}
						{if $genre_array.parent_id == $genre_id}
						<li>
							<div class="pm-li-category">
								<a href="{$genre_array.url}">
									<h3>{$genre_array.name|truncate:32}</h3>
								</a>
							</div>
						</li>
						{/if}
					{/foreach}
					</ul>
				</div>
			</div>
		{/if}

	</div>



	<div class="container-fluid">
	{if isset($featured_items) && (count($featured_items) > 0)}
	<div class="row">
		<div class="col-md-12 col-md-12 border border-grey border-left-0 border-top-0 border-right-0">
			<div class="pm-section-head">
				<h2>{$lang._feat} {$lang._series}</h2>
				<div class="btn-group btn-group-sort">
				<button class="btn btn-xs" id="pm-slide-prev_featured"><i class="fa fa-chevron-left"></i></button>
				<button class="btn btn-xs" id="pm-slide-next_featured"><i class="fa fa-chevron-right"></i></button>
				</div>
			</div>
			<div id="">
			<!-- Carousel items -->
				<ul class="pm-ul-carousel-videos pm-ul-carousel-series list-inline" data-slider-id="featured" data-slides="5" id="pm-carousel_featured">
					{foreach from=$featured_items key=k item=item_data name=featured_items_loop}
						{if $item_data._item_type_ == 'series'}
							<li>
								{include file='item-series-obj.tpl' hideLabels='1' hideMeta='1' thumbSize='poster'}
							</li>
						{/if}
					{/foreach}
				</ul>
			</div><!-- #pm-slider -->
		</div>
	</div>
	{/if}

	<!-- featured genres -->
	{if isset($featured_genres_data) && (count($featured_genres_data) > 0)}
		{foreach from=$featured_genres_data key=genre_id item=series_data_array}
			{if $genres.$genre_id.total_series > 0 && pm_count($series_data_array) > 0}
				<div class="row">
					<div class="col-md-12 col-md-12 border border-grey border-left-0 border-top-0 border-right-0">
						<div class="pm-section-head">
							<h2><a href="{$genres.$genre_id.url}">{$genres.$genre_id.name}</a></h2>
							<div class="btn-group btn-group-sort">
							<button class="btn btn-xs" id="pm-slide-prev_{$genre_id}"><i class="fa fa-chevron-left"></i></button>
							<button class="btn btn-xs" id="pm-slide-next_{$genre_id}"><i class="fa fa-chevron-right"></i></button>
							</div>
						</div>
						<div id="">
						<!-- Carousel items -->
							<ul class="pm-ul-carousel-videos pm-ul-carousel-series list-inline" data-slider-id="{$genre_id}" data-slides="" id="pm-carousel_{$genre_id}">
								{foreach from=$series_data_array key=k item=item_data name=featured_items_loop}
									<li>
										{include file='item-series-obj.tpl' hideLabels='1' hideMeta='1' thumbSize='poster' isObj='series'}
									</li>
								{/foreach}
							</ul>
						</div><!-- #pm-slider -->
					</div>
				</div>
			{/if}
		{/foreach}
	{/if}

	<div class="row">
		<div class="col-md-8">
		{if pm_count($new_episodes) > 0}
			<!-- New episodes list --> 
			<div class="pm-section-head">
				<h2>{$lang.new_episodes|default:"New Episodes"}</h2>
			</div>
			<ul class="pm-ul-browse-videos list-unstyled">
				{foreach from=$new_episodes key=k item=item_data name=new_episodes_loop}
				<li class="col-xs-6 col-sm-6 col-md-4">
					{include file='item-series-obj.tpl' hideLabels='1' hideMeta='1' thumbSize='medium' isObj='episode'}
				</li>
				{foreachelse}
				<li class="col-xs-12 col-sm-12 col-md-12">
					{$lang.top_videos_msg2}
				</li>
				{/foreach}
			</ul>
			<div class="clearfix"></div>
		{/if}
		</div><!-- .col-md-8 -->
		<div class="col-md-4 col-md-sidebar">
		{if pm_count($top_episodes) > 0}
			<div class="widget">
				<div class="pm-section-head">
					<h2>{$lang.most_watched_episodes|default:"Most Watched Episodes"}</h2>
				</div>
				<ul class="row pm-ul-browse-videos list-unstyled">
					{foreach from=$top_episodes key=k item=item_data name=new_episodes_loop}
					<li class="col-xs-6 col-sm-6 col-md-6">
						{include file='item-series-obj.tpl' hideLabels='1' hideMeta='1' thumbSize='small' isObj='episode'}
					</li>
					{foreachelse}
					<li class="col-xs-12 col-sm-12 col-md-12">
						{$lang.top_videos_msg2}
					</li>
					{/foreach}
				</ul>
				<div class="clearfix"></div>
			</div><!-- .widget -->
		{/if}
		</div><!-- .col-md-4 -->
	</div><!-- .row -->

		<div class="row">
		<div class="col-md-12">
		<div class="pm-section-head">
			<h2>{$lang._series}</h2>
		</div>

			{if pm_count($series) > 0}
				<ul class="pm-ul-browse-videos pm-ul-carousel-series list-unstyled d-flex flex-wrap align-content-start">
				{foreach from=$series key=k item=item_data name=series_loop}
					<li>
					{include file='item-series-obj.tpl' hideLabels='1' hideMeta='0' thumbSize='poster' isObj='series'}
					</li>
				{/foreach}
				</ul>

				{if is_array($pagination)}
					{include file='item-pagination-obj.tpl' custom_class='pagination-arrows'}
				{/if}

			{else}
				<div class="row">
					<div class="col-md-12 text-center">
						<p></p>
						<p>{$lang.browse_msg2}</p>
					</div>
				</div>
			{/if}

		</div><!-- #content -->
		</div><!-- .row -->
	</div><!-- .container -->
{include file="footer.tpl" tpl_name="video-series"}