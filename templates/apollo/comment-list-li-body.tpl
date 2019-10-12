<div class="media-object">
{if $comment_data.user_id == 0}
	<a href="#" class="pull-left"><img src="{$comment_data.avatar_url}" class="pm-round-avatar" height="40" width="40" alt=""></a>
{else}
	<a href="{$comment_data.user_profile_href}" class="pull-left"><img src="{$comment_data.avatar_url}" class="pm-round-avatar" height="40" width="40" alt=""></a>
{/if}
</div>

<div class="media-body{if $comment_data.user_is_banned} media-body-banned{/if}">
	<div class="media-heading">
	{if $comment_data.user_id == 0} 
		{$comment_data.name}
	{else} 
	<a href="{$comment_data.user_profile_href}" class="pm-comment-user">{$comment_data.name}</a>
	{if $comment_data.channel_verified == 1}<a href="#" rel="tooltip" title="{$lang.verified_channel|default:'Verified Channel'}"><img src="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/ico-verified.png" width="12" height="12" /></a>{/if}

	{if $comment_data.user_id > 0 && $comment_data.user_id != $s_user_id && $can_manage_comments && $comment_data.power != $smarty.const.U_ADMIN}
		{if $comment_data.user_is_banned}
		<button type="button" id="unban-{$comment_data.id}" class="unban-{$comment_data.user_id} btn btn-xs btn-link" rel="tooltip" title="{$lang.user_account_remove_ban}"><i class="fa fa-ban"></i></button>
		{else}
		<button type="button" id="ban-{$comment_data.id}" class="ban-{$comment_data.user_id} btn btn-xs btn-link" rel="tooltip" title="{$lang.user_account_add_ban}"><i class="fa fa-ban"></i></button>
		{/if}
	{/if}

	<span class="label label-danger label-banned-{$comment_data.user_id} {if ! $comment_data.user_is_banned}hide-me{/if}">{$lang.user_account_banned_label}</span>
	{/if}
	<div class="media-date"><time datetime="{$comment_data.html5_datetime}" title="{$comment_data.full_datetime}">{$comment_data.time_since_added} {$lang.ago}</time></div>
	{if $can_manage_comments}<span class="pm-comment-user-ip">({$comment_data.user_ip})</span>{/if}
	</div>
    <p>{$comment_data.comment}</p>

	{if $logged_in}
	<div class="media-actions" id="users-{$smarty.foreach.comment_foreach.iteration}">
		<div class="btn-group">
			<button type="button" class="btn btn-xs btn-link {if $comment_data.user_likes_this}active{/if}" {if $comment_data.user_id != $s_user_id}id="comment-like-{$comment_data.id}"{/if} rel="tooltip" title="{$lang._like}"><span id="comment-like-count-{$comment_data.id}">
			{if $comment_data.up_vote_count > 0}
				{$comment_data.up_vote_count}
			{/if}
			</span> <i class="fa fa-thumbs-o-up"></i>
			</button>
			<button type="button" class="btn btn-xs btn-link {if $comment_data.user_dislikes_this}active{/if}" {if $comment_data.user_id != $s_user_id}id="comment-dislike-{$comment_data.id}"{/if} rel="tooltip" title="{$lang._dislike}">
			<span id="comment-dislike-count-{$comment_data.id}">
			{if $comment_data.down_vote_count > 0}
				{$comment_data.down_vote_count}
			{/if}
			</span> <i class="fa fa-thumbs-o-down"></i>
			</button>
			<button type="button" id="comment-flag-{$comment_data.id}" class="btn btn-xs btn-link {if $comment_data.user_flagged_this}active{/if}" rel="tooltip" title="{$lang.report_comment}"><i class="fa fa-flag-o"></i></button>
			{if $can_manage_comments}
			<button onclick="onpage_delete_comment('{$comment_data.id}', '{$comment_data.uniq_id}', '#comment-{$smarty.foreach.comment_foreach.iteration}'); return false;" class="btn btn-xs btn-link" rel="tooltip" title="{$lang.delete}"><i class="fa fa-trash-o"></i></button>
			{/if}
		</div>
	</div>
	{/if}
</div>