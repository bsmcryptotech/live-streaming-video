{include file='header.tpl' p="general"} 
<div id="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-8">
				<div id="category-header" class="container-fluid pm-search-videos-page">
					<div class="pm-category-highlight">
						<h1>{$lang.search_results}{if is_array($results)}: <mark>{$searchstring}</mark>{/if}</h1>
					</div>
				</div>
			</div>
			<div class="hidden-xs hidden-sm col-md-4">
				<div class="pm-section-head">
					<div class="btn-group btn-group-sort">
						<button class="btn btn-default" id="show-grid" rel="tooltip" title="{$lang._grid}"><i class="fa fa-th"></i></button>
						<button class="btn btn-default" id="show-list" rel="tooltip" title="{$lang._list}"><i class="fa fa-list"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container-fluid">
	  <div class="row">
		<div class="col-md-12">

			{$error_msg}

			{if pm_count($series_results) > 0}			
			<h2 class="my-3"><strong>{$lang._series}</strong> {if is_array($results)}: <mark>{$searchstring}</mark>{/if}</h2>

			<div class="d-flex flex-row my-3">
				<ul class="pm-ul-browse-videos pm-ul-carousel-series list-inline  my-3">
				{foreach from=$series_results key=k item=item_data name=series_results_loop}
					<li>
						{include file='item-series-obj.tpl' hideLabels='1' hideMeta='1' thumbSize='poster' isObj='series'}
					</li>
				{/foreach}
				</ul>
			</div>
			{/if}


			{if pm_count($series_results) > 0}
			<hr />
			<h2 class="my-3"><strong>{$lang.videos}</strong> {if is_array($results)}: <mark>{$searchstring}</mark>{/if}</h2>
			{/if}
			
			<ul class="row pm-ul-browse-videos list-unstyled" id="pm-grid">
			{foreach from=$results key=k item=video_data}
				<li class="col-xs-6 col-sm-4 col-md-3">
				{include file='item-video-obj.tpl'}
				</li>
			{/foreach}
			</ul>
			<div class="clearfix"></div>

			{if is_array($pagination)}
				{include file='item-pagination-obj.tpl' custom_class='pagination-arrows'}
			{/if}
		</div><!-- #content -->
	  </div><!-- .row -->
	</div><!-- .container -->
{include file='footer.tpl'}