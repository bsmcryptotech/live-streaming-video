<ul class="pm-social-ul-notifications list-unstyled">
	{foreach from=$notification_list key=notification_id item=el}
		{if $el.activity_type == $smarty.const.ACT_TYPE_FOLLOW}
			{assign var='main_href' value=$el.metadata.from_userdata.profile_url}
		{else}
			{assign var='main_href' value=$el.metadata.object.video_href}
		{/if}
		<li class="media {if $el.seen == 0}media-unread{/if}">
			{if $el.metadata.from_userdata.id != 0}
			<a href="{$el.metadata.from_userdata.profile_url}" class="pull-left"><img src="{$el.metadata.from_userdata.avatar_url}" width="40" height="" border="0" class="media-object"></a>
			{else}
			<a href="#" class="pull-left"><img src="{$el.metadata.from_userdata.avatar_url}" width="40" height="" border="0" class="media-object"></a>
			{/if}

			{if $article.meta._post_thumb_show != ''}
			<a href="{$article.link}" class="pull-left" title="{$article.title}"><img src="{$smarty.const._ARTICLE_ATTACH_DIR}/{$article.meta._post_thumb_show}" align="left" width="55" height="55" border="0" alt="{$article.title}" class="media-object"></a>
			{/if}
			<div class="media-body">
				<div class="media-heading">
		            {if $el.metadata.from_userdata.id != 0}
						<a href="{$el.metadata.from_userdata.profile_url}">{$el.metadata.from_userdata.name}</a>
					{else}
						<strong>{$el.metadata.from_userdata.name}</strong>
					{/if}
	                {if $el.activity_type == $smarty.const.ACT_TYPE_LIKE}{/if}
	                {$el.lang}
	                {if $el.metadata.object_type == $smarty.const.ACT_OBJ_VIDEO}
	                    <a href="{$el.metadata.object.video_href}">{$el.metadata.object.video_title}</a>
	                {/if}
	                {if $el.metadata.object_type == $smarty.const.ACT_OBJ_ARTICLE}
	                    <a href="{$el.metadata.object.link}">{$el.metadata.object.title}</a>
	                {/if}
	            </div>
                <div class="media-date">{$el.time_since} {$lang.ago}</div>
			</div>
			<div class="clearfix"></div>
		</li>
	{foreachelse}
		<li class="m-3 text-center">{$lang.notification_list_empty}</li>
	{/foreach}
</ul>
{if $total_notifications == 7}
	<div class="clearfix"></div>
	<span name="notifications_load_more" id="btn_notifications_load_more"></span>
{/if}