{if $logged_in && $current_user_data.id == $s_user_id}
<div id="profile-header" class="container-fluid">
	<div class="row">
		<div class="col-sm-12 col-xs-12 col-md-12">
			<div class="pm-user-brief">
				<div class="pm-avatar">
					<a href="{$current_user_data.profile_url}"><img src="{$current_user_data.avatar_url}" alt="{$current_user_data.username}"  border="0" class="img-responsive"></a>
				</div>
				<div class="pm-username">{$current_user_data.username}</div>

				<div class="nav-responsive">
					<ul class="nav nav-tabs nav-underlined">
						<li{if $p == "profile-edit"} class="active"{/if}><a href="{$smarty.const._URL}/edit-profile.{$smarty.const._FEXT}">{$lang.edit_profile}</a>
						<li{if $p == "playlists"} class="active"{/if}><a href="{$smarty.const._URL}/playlists.{$smarty.const._FEXT}">{$lang.manage_playlists|default:'Manage Playlists'}</a></li> 
						<li{if $p == "members"} class="active"{/if}><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}">{$lang.members}</a></li>
						{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}
						<li{if $p == "suggest"} class="active"{/if}><a href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}">{$lang.suggest}</a></li>
						{/if}
						{if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
						<li{if $p == "upload"} class="active"{/if}><a href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}">{$lang.upload_video}</a></li>
						{/if}
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
{/if}