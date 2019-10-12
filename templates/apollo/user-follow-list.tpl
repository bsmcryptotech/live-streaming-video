<ul class="row pm-channels-list list-unstyled">
{foreach from=$profile_list key=profile_user_id item=profile}
<li class="col-sm-6 col-md-4">
	<div class="pm-channel">
		<div class="pm-channel-header">
			<div class="pm-channel-cover">
				{if $profile.channel_cover.max}
				<img src="{$profile.channel_cover.450}" alt="{$profile.username}"  border="0" class="img-responsive">
				{/if}
			</div>
			<div class="pm-channel-profile-pic">
				<a href="{$profile.profile_url}"><img src="{$profile.avatar_url}" alt="{$profile.username}"  border="0" class="img-responsive"></a>
			</div>
		</div>
		<div class="pm-channel-body">
			<h3><a href="{$profile.profile_url}" class="ellipsis {if $profile.user_is_banned}pm-user-banned{/if}">{$profile.name}</a> 
			{if $profile.channel_verified == 1}<a href="#" rel="tooltip" title="{$lang.verified_channel|default:'Verified Channel'}"><img src="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/ico-verified.png" width="12" height="12" alt="" /></a>{/if} 
			{if $profile.is_following_me}<span class="label label-success label-follow-status pull-right">{$lang.subscriber|default:'Subscriber'}</span>{/if}</h3>
			<div class="pm-channel-stats">{$profile.followers_count} {$lang.subscribers|default:'Subscribers'}</div>
			<!-- <div class="pm-channel-desc">{$profile.about|truncate:50}</div> -->

			<div class="pm-channel-buttons">
				{if $profile_user_id != $s_user_id}
					{include file="user-subscribe-button.tpl" profile_data=$profile profile_user_id=$profile_user_id}
				{else}
					<button class="btn btn-regular btn-success btn-follow" rel="tooltip" title="{$lang.this_is_you}" disabled="disabled">{$lang.follow}</button>
				{/if}
			</div>
		</div>
	</div>
</li>
{/foreach}
{if $follow_count == 0}
	{$lang.memberlist_msg3}
{/if}
</ul>

{if $total_profiles == $smarty.const.FOLLOW_PROFILES_PER_PAGE}
	<div class="clearfix"></div>
	<span id="btn_follow_load_more"></span>
{/if}