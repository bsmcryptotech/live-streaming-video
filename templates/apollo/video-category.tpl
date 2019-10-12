{include file='header.tpl' p="general" tpl_name="video-category"}
<div id="content">

<div id="category-header" class="container-fluid">
	<div class="pm-category-highlight">
		<h1>{$gv_category_name}</h1>

		<div class="clearfix"></div>

		{if $gv_category_description}
			<div class="pm-category-description">
			{$gv_category_description}
			</div>
		{/if}

	</div>
	{if ! empty($list_subcats)}
	<div class="row pm-category-header-subcats">
		<div class="col-md-12">
			<div class="pm-section-head">
				<h2>{$lang.related_cats}</h2>
				<div class="btn-group btn-group-sort">
					<button class="btn btn-xs" id="pm-slide-prev_subcategories"><i class="fa fa-chevron-left"></i></button>
					<button class="btn btn-xs" id="pm-slide-next_subcategories"><i class="fa fa-chevron-right"></i></button>
				</div>
			</div>
			<ul class="pm-ul-carousel-videos list-inline pm-ul-browse-subcategories thumbnails mt-3" data-slider-id="subcategories" data-slides="5" id="pm-carousel_subcategories">
			{foreach from=$categories_data key=k item=category_data}
				{if $category_data.parent_id == $cat_id}
				<li>
					<div class="pm-li-category">
						<a href="{$category_data.url}">
							<span class="pm-video-thumb pm-thumb">
								<div class="pm-thumb-fix"><span class="pm-thumb-fix-clip"><img src="{$category_data.image_url}" alt="{$category_data.attr_alt}"><span class="vertical-align"></span></span></div>
							</span>
							<h3>{$category_data.name|truncate:32}</h3>
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
		<div class="row">
		<div class="col-md-12">

		{if ! empty($results)}
			<div class="pm-section-head">
				<div class="btn-group btn-group-sort">
					<a class="btn btn-default" id="show-grid" rel="tooltip" title="{$lang._grid}"><i class="fa fa-th"></i></a>
					<a class="btn btn-default" id="show-list" rel="tooltip" title="{$lang._list}"><i class="fa fa-list"></i></a>
					<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-target="#">
					{if $gv_sortby == ''}{$lang.sorting}{/if} {if $gv_sortby == 'date'}{$lang.date}{/if}{if $gv_sortby == 'views'}{$lang.views}{/if}{if $gv_sortby == 'rating'}{$lang.rating}{/if}{if $gv_sortby == 'title'}{$lang.title}{/if}
					<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						{if $smarty.const._SEOMOD == '1'}
						<li {if $gv_sortby == 'date'}class="selected"{/if}>
						<a href="{$smarty.const._URL}/browse-{$gv_cat}-videos-{$gv_pagenumber}-date.html" rel="nofollow">{$lang.date}</a></li>
						<li {if $gv_sortby == 'views'}class="selected"{/if}>
						<a href="{$smarty.const._URL}/browse-{$gv_cat}-videos-{$gv_pagenumber}-views.html" rel="nofollow">{$lang.views}</a></li>
						<li {if $gv_sortby == 'rating'}class="active"{/if}>
						<a href="{$smarty.const._URL}/browse-{$gv_cat}-videos-{$gv_pagenumber}-rating.html" rel="nofollow">{$lang.rating}</a></li>
						<li {if $gv_sortby == 'title'}class="active"{/if}>
						<a href="{$smarty.const._URL}/browse-{$gv_cat}-videos-{$gv_pagenumber}-title.html" rel="nofollow">{$lang.title}</a></li>
						{else}
						<li {if $gv_sortby == 'date'}class="selected"{/if}>
						<a href="{$smarty.const._URL}/category.php?cat={$gv_cat}&page={$gv_pagenumber}&sortby=date" rel="nofollow">{$lang.date}</a></li>
						<li {if $gv_sortby == 'views'}class="selected"{/if}>
						<a href="{$smarty.const._URL}/category.php?cat={$gv_cat}&page={$gv_pagenumber}&sortby=views" rel="nofollow">{$lang.views}</a></li>
						<li {if $gv_sortby == 'rating'}class="selected"{/if}>
						<a href="{$smarty.const._URL}/category.php?cat={$gv_cat}&page={$gv_pagenumber}&sortby=rating" rel="nofollow">{$lang.rating}</a></li>
						<li {if $gv_sortby == 'title'}class="selected"{/if}>
						<a href="{$smarty.const._URL}/category.php?cat={$gv_cat}&page={$gv_pagenumber}&sortby=title" rel="nofollow">{$lang.title}</a></li>
						{/if}
					</ul>
				</div>
			</div>
			<div class="clearfix"></div>

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
{include file="footer.tpl" tpl_name="video-category"}