{include file="header.tpl" p="article" tpl_name="article-read"} 
<div id="content" class="pm-text-pages">
{if $show_addthis_widget == '1'}
{include file='widget-socialite.tpl'}
{/if}
	<div id="content-main" class="container-fluid">
		<div class="row">
			<div class="col-md-8" itemscope itemtype="http://schema.org/Article">
			{if is_array($article)} 
				<article class="post">
					<header>
						<h1 itemprop="name">{$article.title}</h1>
						<meta itemprop="interactionCount" content="UserComments:{$article.comment_count}"/>
						{if $article.meta._post_thumb_show != ''}
						<meta itemprop="thumbnailUrl" content="{$smarty.const._ARTICLE_ATTACH_DIR}/{$article.meta._post_thumb_show}"/>
						{/if}
						<div class="entry-meta">
							{if $logged_in && $is_admin == 'yes'}
							<span class="entry-edit"><a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/edit-article.php?do=edit&id={$article.id}" class="hidden-xs" rel="tooltip" title="{$lang.edit} ({$lang._admin_only})" target="_blank"><i class="fa fa-pencil"></i> {$lang.edit}</a></span>
							{/if}

							<span class="entry-date"><a rel="bookmark" href="{$article.link}"><i class="fa fa-clock-o"></i> <time datetime="{$article.html5_datetime}" title="{$article.full_datetime}" pubdate>{$article.date}</time></a></span>
							<span class="entry-author"><a href="{$article.author_profile_href}"><i class="fa fa-user"></i> {$article.name}</a></span>
							<span class="entry-comments"><a href="#jump-comments"><i class="fa fa-comment"></i> {$article.comment_count} {$lang.comments}</a></span>
							<span class="entry-category"> 
							{foreach from=$article.pretty_cats key=cat_name item=cat_href}
							<a href="{$cat_href}" title="{$cat_name}"><i class="fa fa-folder"></i> {$cat_name}</a>
							{/foreach} 
							</span>
						</div>
					</header><!-- /header -->

					{if $article.restricted == '1' && ! $logged_in}
						<div class="pm-restricted-item">
							<h2>{$lang.article_restricted_sorry}</h2>
							<p>{$lang.article_restricted_register}</p>
							<div class="pm-restricted-login-form">
							{include file='user-auth-login-form.tpl'}
							</div>
						</div>
					{else}
					<div class="entry-post">
						<div itemprop="articleBody">{$article.content}</div>
					</div>
					{/if}
				</article>

			{else}
			<p>{$lang.page_missing_title}</p>
			{/if}

			{if $ad_4 != ''}
			<div class="pm-ads-banner" align="center">{$ad_4}</div>
			{/if}

			<div class="clearfix"></div>
			{if !empty($article.tags) }
			<div class="entry-tags tag-links">
				{foreach name=tag_links from=$article.tags key=k item=t}
				{if $smarty.foreach.tag_links.last}
				<a rel="tag" href="{$t.link}" title="{$t.tag}">{$t.tag}</a>
				{else}
				<a rel="tag" href="{$t.link}" title="{$t.tag}">{$t.tag}</a>
				{/if}
				{/foreach}
			</div>
			{/if}

			{include file="comments.tpl" tpl_name="article-read" allow_comments=$article.allow_comments}
			</div><!-- #content -->

			<div class="col-md-4">
				{if is_array($related_articles) && pm_count($related_articles) > 0}
				<div class="widget">
					<h4>{$lang.articles_related}</h4>
					<ul class="pm-sidebar-articles list-unstyled">
					{foreach from=$related_articles item=related key=id}
						<li class="media{if $related.featured == '1'} media-featured{/if}">
							{if $related.meta._post_thumb_show != ''}
							<a href="{$related.link}" class="pull-left" title="{$related.title}"><img src="{$smarty.const._ARTICLE_ATTACH_DIR}/{$related.meta._post_thumb_show}" align="left" border="0" alt="{$related.title}" class="media-object"></a>
							{/if}
							<div class="media-body">
								<h5 class="media-heading"><a href="{$related.link}" title="{$related.title}" >{$related.title}</a></h5>
								<span class="ellipsis">{$related.excerpt|truncate:130}</span>
							</div>
						</li>
					{/foreach}
					</ul>
				</div>
				{/if}


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
			</div>
		</div><!-- .row -->
	</div><!-- .container -->
{include file="footer.tpl" tpl_name="article-read"}