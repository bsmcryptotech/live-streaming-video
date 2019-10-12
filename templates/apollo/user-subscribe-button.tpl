{if ! $profile_data.am_following}
	<button id="btn_follow_{$profile_user_id}" class="btn btn-sm btn-success btn-follow" data-user-id="{$profile_user_id}">{$lang.subscribe|default:'Subscribe'}</button>
{else}
	<button id="btn_unfollow_{$profile_user_id}" class="btn btn-sm btn-success btn-unfollow" data-user-id="{$profile_user_id}"><i class="fa fa-check"></i> {$lang.subscribed|default:'Subscribed'}</button>
{/if}