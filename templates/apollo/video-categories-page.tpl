{include file='header.tpl' p="general" tpl_name="video-categories-page"}
<div id="content">
	<div id="category-header" class="container-fluid">
		<div class="pm-category-highlight">
		<h1>{$lang._categories}</h1>
		</div>
	</div>
	<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
		<div class="pm-section-head">
			
		</div>

		<ul class="pm-ul-browse-categories list-unstyled thumbnails">
		{foreach from=$categories_data key=k item=category_data}
			{if $category_data.parent_id == 0}
			<li>
				<div class="pm-li-category">
				<a href="{$category_data.url}">
					<span class="pm-video-thumb pm-thumb-234 pm-thumb">
						<div class="pm-thumb-fix pm-thumb-234"><span class="pm-thumb-fix-clip"><img src="{$category_data.image_url}" alt="{$category_data.attr_alt}" width="234"><span class="vertical-align"></span></span></div>
					</span>
					<h3>{$category_data.name|truncate:32}</h3>
				</a>
				</div>
			</li>
			{/if}
		{/foreach}
		</ul>
		</div><!-- #content -->
	  </div><!-- .row -->
	</div><!-- .container -->
{include file="footer.tpl" tpl_name="video-categories-page"}