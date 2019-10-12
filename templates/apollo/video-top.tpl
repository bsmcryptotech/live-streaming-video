{include file="header.tpl" p="general" tpl_name="video-top"}
<div id="content">


	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8">
				<div id="category-header" class="container-fluid pm-popular-videos-page">
					<div class="pm-category-highlight">
						<h1>{$lang.top_m_videos}{if $cat_name}: {$cat_name}{/if}</h1>
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
							<li role="presentation" class="dropdown-header">{$lang.most_liked}</li>
							<li><a href="{$smarty.const._URL}/topvideos.{$smarty.const._FEXT}?do=rating"{if $smarty.get.do == 'rating'} class="disabled"{/if}>{$lang.any_time}</a></li>
							<li role="presentation" class="dropdown-header">{$lang.by_time}</li>
							<li><a href="{$smarty.const._URL}/topvideos.{$smarty.const._FEXT}"{if $smarty.get.do == 'rating'} class="disabled"{/if}>{$lang.any_time}</a></li>
							<li><a href="{$smarty.const._URL}/topvideos.{$smarty.const._FEXT}?do=recent"{if $smarty.get.do == 'recent'} class="disabled"{/if}>{$chart_days}</a></li>
							<li role="presentation" class="dropdown-header">{$lang.by_cat}</li>
							{$categories_ul_list}
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
				<li class="col-xs-6 col-sm-6 col-md-3">
				{include file='item-video-obj.tpl' tpl_name='video-top'}
				</li>
				{foreachelse}
				<li class="col-xs-12 col-sm-12 col-md-12 text-center">
					{$lang.top_videos_msg2}
				</li>
				{/foreach}
				</ul>
				<div class="clearfix"></div>
				
				{if is_array($pagination)}
					{include file='item-pagination-obj.tpl' custom_class='pagination-arrows'}
				{/if}
			</div>
		</div><!-- .row -->
	</div>

{include file="footer.tpl" tpl_name="video-top"} 