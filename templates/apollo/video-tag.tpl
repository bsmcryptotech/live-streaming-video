{include file='header.tpl' p="general"} 
<div id="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div id="category-header" class="container-fluid pm-search-videos-page">
					<div class="pm-category-highlight">
						<h1>{$lang.search_results}{if is_array($results)}: <mark>{$searchstring}</mark>{/if}</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid">
	  <div class="row">
		<div class="col-md-12">
			<div class="pm-section-head">
			<div class="btn-group btn-group-sort">
				<button class="btn btn-default" id="show-grid" rel="tooltip" title="{$lang._grid}"><i class="fa fa-th"></i></button>
				<button class="btn btn-default" id="show-list" rel="tooltip" title="{$lang._list}"><i class="fa fa-list"></i></button>
			</div>
			</div>
			
			{$error_msg}
			{$total_results}

			{if is_array($results)}
			<ul class="row pm-ul-browse-videos list-unstyled" id="pm-grid">
			{foreach from=$results key=k item=video_data}
				<li class="col-xs-6 col-sm-4 col-md-3">
				{include file='item-video-obj.tpl'}
				</li>
			{/foreach}
			</ul>
			{/if}
			<div class="clearfix"></div>

			{if is_array($pagination)}
				{include file='item-pagination-obj.tpl' custom_class='pagination-arrows'}
			{/if}
        </div><!-- #content -->
      </div><!-- .row -->
    </div><!-- .container -->
{include file='footer.tpl'}