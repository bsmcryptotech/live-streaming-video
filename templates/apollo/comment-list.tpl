<ul class="pm-ul-comments list-unstyled{if $tpl_name == 'article-read'} article-comments{elseif $tpl_name == 'video-watch' || $tpl_name == 'video-watch-episode'} video-comments{/if}">
	{if is_array($most_liked_comment)}
	<li id="comment-999" class="media pm-top-comment">
		<div class="label-top-comment">{$lang.top_comment}</div>
		{include file='comment-list-li-body.tpl' comment_data=$most_liked_comment}
	</li>
	{/if}
	<li id="preview_comment" class="media"></li>
	{foreach from=$comment_list key=k item=comment_data name=comment_foreach}
	<li id="comment-{$smarty.foreach.comment_foreach.iteration}" class="media {if $comment_data.downvoted}pm-downvoted-comment{/if}">
		{include file='comment-list-li-body.tpl'}
	</li>
	{/foreach}
</ul>