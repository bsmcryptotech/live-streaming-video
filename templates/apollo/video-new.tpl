{include file="header.tpl" p="general" tpl_name="video-new"}
<div id="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8">
				<div id="category-header" class="container-fluid pm-new-videos-page">
					<div class="pm-category-highlight">
						<h1>{$lang.new_videos}</h1>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="pm-section-head">
					<div class="btn-group btn-group-sort">
						<button class="btn btn-default" id="show-grid" rel="tooltip" title="{$lang._grid}"><i class="fa fa-th"></i></button>
						<button class="btn btn-default" id="show-list" rel="tooltip" title="{$lang._list}"><i class="fa fa-list"></i></button>
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						{$lang.sorting} <span class="caret"></span>
						</button>
						<ul class="dropdown-menu scrollable-menu" role="menu">
							<li><a href="{$smarty.const._URL}/newvideos.{$smarty.const._FEXT}">{$lang.any_time}</a></li>
							<li><a href="{$smarty.const._URL}/newvideos.{$smarty.const._FEXT}?d=today">{$lang.today}</a></li>
							<li><a href="{$smarty.const._URL}/newvideos.{$smarty.const._FEXT}?d=yesterday">{$lang.yesterday}</a></li>
							<li><a href="{$smarty.const._URL}/newvideos.{$smarty.const._FEXT}?d=month">{$lang.this_month}</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">

		<ul class="row pm-ul-browse-videos list-unstyled" id="pm-grid">
		{foreach from=$results key=k item=video_data}
		<li class="col-xs-6 col-sm-4 col-md-3">
		{include file='item-video-obj.tpl'}
		</li>
		{foreachelse}
		<li class="col-xs-12 col-sm-12 col-md-12 text-center">
			{$lang.top_videos_msg2}
		</li>
		{/foreach}
		</ul>
		<div class="clearfix"></div>

		
		{if $empty_results}
			<p class="alert">{$lang.nv_page_sorry_msg}</p>
		{/if}

		<div class="clearfix"></div>
		{if is_array($pagination)}
			{include file='item-pagination-obj.tpl' custom_class='pagination-arrows'}
		{/if}
		</div><!-- #content -->
	  </div><!-- .row -->
	</div><!-- .container -->
{include file="footer.tpl" tpl_name="video-new"}