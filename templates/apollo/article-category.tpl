{include file='header.tpl' p='article' tpl_name='article-category'}
<div id="content">

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8">

				<h1>{$article_h2}</h1>

				{if $cat_id > 0 && $categories.$cat_id.description}
				<div class="pm-category-description">
				{$categories.$cat_id.description}
				</div>
				<div class="clearfix"></div>
				{/if}

				{if ! is_array($articles)}
				<article class="post mt-3">
					<h3>{$lang.article_no_articles}</h3>
				</article>
				{else}
				<ul class="pm-ul-browse-articles list-unstyled">
					{foreach from=$articles key=id item=article}
					<li{if $article.featured == '1'} class="sticky-article"{/if}>
						<article class="post">
							<header>
								{if $logged_in && $is_admin == 'yes'}
								<span class="pull-right"><a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/edit-article.php?do=edit&id={$article.id}" rel="tooltip" title="{$lang.edit} ({$lang._admin_only})" target="_blank" class="btn btn-sm btn-default hidden-xs">{$lang.edit}</a></span>
								{/if}
								<h2><a href="{$article.link}" title="{$article.title}">{$article.title}</a></h2>
								<div class="entry-meta">
									<span class="entry-date"><i class="fa fa-clock-o"></i> <a rel="bookmark" href="{$article.link}"><time datetime="{$article.html5_datetime}" title="{$article.full_datetime}" pubdate>{$article.date}</time></a></span>
									<span class="entry-author"><i class="fa fa-user"></i> <a href="{$article.author_profile_href}">{$article.name}</a></span>
									<span class="entry-comments"><i class="fa fa-comment"></i> <a href="{$article.link}#jump-comments">{$article.comment_count} {$lang.comments}</a></span>
								</div>
							</header><!-- /header -->

							{if $article.restricted == '1' && ! $logged_in}
								{$lang.article_restricted_sorry}
							{else}
							<div class="entry-summary">
									{$article.content}
									<a href="{$article.link}" class="btn btn-default entry-read-more">{$lang.read_more}</a>
							</div>
							{/if}
						</article>
					</li>
					{/foreach}
				</ul>
				{/if}
				<div class="clearfix"></div>

				{if is_array($pagination)}
					{include file='item-pagination-obj.tpl' custom_class='pagination-arrows'}
				{/if}
			</div>

			<div class="col-md-4">

				{if pm_count($latest_articles) > 0}
				<div class="widget">
					<h4>{$lang.articles_latest}</h4>
					<ul class="pm-sidebar-articles list-unstyled">
					{foreach from=$latest_articles key=id item=article}
						<li class="media{if $article.featured == '1'} media-featured{/if}">
							{if $article.meta._post_thumb_show != ''}
							<a href="{$article.link}" class="pull-left" title="{$article.title}"><img src="{$smarty.const._ARTICLE_ATTACH_DIR}/{$article.meta._post_thumb_show}" align="left" border="0" alt="{$article.title}" class="media-object"></a>
							{/if}
							<div class="media-body">
								<h5 class="media-heading"><a href="{$article.link}" title="{$article.title}" >{$article.title}</a></h5>
								<span class="ellipsis">{$article.excerpt|truncate:130}</span>
							</div>
						</li>
					{/foreach}
					</ul>
				</div>
				{/if}

				{if pm_count($popular_articles) > 0}
				<div class="widget">
					<h4>{$lang.articles_mostread}</h4>
					<ul class="pm-sidebar-articles list-unstyled">
					{foreach from=$popular_articles key=id item=article}
						<li class="media{if $article.featured == '1'} media-featured{/if}">
							{if $article.meta._post_thumb_show != ''}
							<a href="{$article.link}" class="pull-left" title="{$article.title}"><img src="{$smarty.const._ARTICLE_ATTACH_DIR}/{$article.meta._post_thumb_show}" align="left" border="0" alt="{$article.title}" class="media-object"></a>
							{/if}
							<div class="media-body">
								<h5 class="media-heading"><a href="{$article.link}" title="{$article.title}" >{$article.title}</a></h5>
								<span class="ellipsis">{$article.excerpt|truncate:130}</span>
							</div>
						</li>
					{/foreach}
					</ul>
				</div>
				{/if}


			</div>
		</div><!-- .row -->
	</div><!-- .container -->
{include file='footer.tpl' tpl_name='article-category'}