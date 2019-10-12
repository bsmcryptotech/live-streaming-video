{include file='header.tpl' p="general"}
<div id="content">
	<div class="container-fluid">
	  <div class="row">
		<div class="col-md-12">

		<div class="pm-profile">
				<div class="pm-profile-header">
					<div class="pm-profile-cover">
						{if $profile_data.id == $s_user_id}
						<div class="pm-profile-cover-preview" data-cropit-height="200">
							<div class="cropit-image-preview">
								{if $profile_data.channel_cover.max != ''}
								<img src="{$profile_data.channel_cover.max}" alt="" border="0" class="img-responsive img-channel-cover">
								{else}
								<img src="{$smarty.const._URL}/templates/{$template_dir}/img/bg-channel-cover.png" alt="" border="0" class="img-responsive img-cover">
								{/if}
								<span class="pm-profile-cover-edit"><a href="#" class="btn btn-link btn-edit" id="btn-edit-cover"><i class="fa fa-pencil"></i></a></span>
							</div>

							<form action="#" class="cropit-form" id="cropit-cover-form">
								<input type="file" class="cropit-image-input" id="cropit-cover-input" />
								<input type="hidden" name="image-data" class="hidden-cover-data-img" />
								<input type="hidden" name="p" value="upload" />
								<input type="hidden" name="do" value="channel-cover" />
								<button class="btn btn-default btn-cancel">{$lang.submit_cancel}</button>
								<button type="submit" class="btn btn-success">{$lang.submit_save}</button>
							</form>
						</div>
						{else}
							{if $profile_data.channel_cover.max != ''}
							<img src="{$profile_data.channel_cover.max}" alt="" border="0" class="img-responsive img-channel-cover">
							{else}
							<img src="{$smarty.const._URL}/templates/{$template_dir}/img/bg-channel-cover.png" alt=""  border="0" class="img-responsive img-channel-cover">
							{/if}
						{/if}

						<div class="pm-profile-avatar-pic">
							{if $profile_data.id == $s_user_id}
							<div class="cropit-image-preview">
								<img src="{$profile_data.avatar_url}" alt="{$profile_data.username}"  border="0" class="img-responsive">
								<span class="pm-profile-avatar-edit"><a href="#" title="{$lang.update_avatar}" class="btn btn-link btn-edit" id="btn-edit-avatar"><i class="fa fa-pencil"></i></a></span>
							</div>
							<form action="#" class="cropit-form" id="cropit-avatar-form">
								<input type="file" class="cropit-image-input" id="cropit-avatar-input" />
								<input type="hidden" name="image-data" class="hidden-avatar-data-img" />
								<input type="hidden" name="p" value="upload" />
								<input type="hidden" name="do" value="user-avatar" />
								<button class="btn btn-default btn-cancel-avatar">{$lang.submit_cancel}</button>
								<button type="submit" class="btn btn-mini btn-success">{$lang.submit_save}</button>
							</form>
							{else}
								<img src="{$avatar}" alt="{$profile_data.username}" border="0" class="img-responsive">
							{/if}
						</div>
						
						<div class="pm-profile-user-info">
							<h1>{$profile_data.username}

							{if $profile_data.channel_verified && $smarty.const._MOD_SOCIAL}
							<a href="#" rel="tooltip" title="{$lang.verified_channel|default:'Verified Channel'}" class="pm-verified-user"><img src="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/ico-verified.png" /></a>
							{/if}
							<!--{if $profile_data.channel_featured == 1}<span class="label">{$lang._featured}</span>{/if}--> 
							{if $user_is_banned} <span class="label label-banned">{$lang.user_account_banned_label}</span>{/if}
							{if $smarty.const._MOD_SOCIAL && $logged_in == 1 && $s_user_id != $profile_data.id}
								{if $profile_data.is_following_me}
									<span class="label label-social-follows hidden-xs">{$lang.subscriber|default:'Subscriber'}</span>
								{/if}
							{/if}
							</h1>

							<div class="pm-profile-buttons hidden-xs">
								{if $smarty.const._MOD_SOCIAL && $logged_in == 1 && $s_user_id != $profile_data.id}
									{include file='user-subscribe-button.tpl' profile_user_id=$profile_data.id}
								{/if}
							</div>
						</div>
					</div>
				</div>

				<div class="pm-profile-body">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-9">
							<ul class="pm-profile-stats list-inline">
								<li>{pm_number_format number=$total_submissions} <span>{$lang.videos|lower}</span></li>
								{if $smarty.const._MOD_SOCIAL}
								<li>{pm_number_format number=$profile_data.followers_count} <span>{$lang.subscribers|lower}</span></li>
								{/if}
								<li>{pm_number_format number=$total_playlists} <span>{$lang._playlists|lower}</span></li>
							</ul>
						</div>
						<div class="hidden-xs hidden-sm col-md-3">
							{if pm_count($profile_data.social_links) > 0}
							<ul class="pm-profile-links list-inline pull-right">
								{if $profile_data.social_links.website != ''}<li><a href="{$profile_data.social_links.website}" target="_blank" rel="nofollow"><i class="fa fa-globe"></i></a></li>{/if}
								{if $profile_data.social_links.facebook != ''}<li><a href="{$profile_data.social_links.facebook}" target="_blank" rel="nofollow"><i class="fa fa-facebook"></i></a></li>{/if}
								{if $profile_data.social_links.twitter != ''}<li><a href="{$profile_data.social_links.twitter}" target="_blank" rel="nofollow"><i class="fa fa-twitter"></i></a></li>{/if}
								{if $profile_data.social_links.instagram != ''}<li><a href="{$profile_data.social_links.instagram}" target="_blank" rel="nofollow"><i class="fa fa-instagram"></i></a></li>{/if}
							</ul>
							{/if}
						</div>
					</div>
					<div class="clearfix"></div>
					{if $profile_data.about}
						<div class="pm-profile-desc hidden-xs hidden-sm">
						{$profile_data.about|truncate:280}
						</div>
					{/if}
				</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="nav-responsive">
					<ul class="nav nav-tabs nav-underlined">
						<li {if $smarty.get.view == 'videos' || $smarty.get.view == ''}class="active"{/if}><a href="#pm-pro-own" data-toggle="tab">{$lang.videos}</a></li>
						{if $smarty.const._MOD_SOCIAL}
						{if  $s_user_id == $profile_data.id}
							<li><a href="#pm-pro-activity-stream" data-toggle="tab">{$lang.activity_newsfeed}</a></li>
						{/if}
						{if $s_user_id == $profile_data.id || $profile_data.am_following}
							<li><a href="#pm-pro-user-activity" data-toggle="tab">{$lang.my_activity}</a></li>
						{/if}
						{/if}
						<li {if $smarty.get.view == 'playlists'}class="active"{/if}><a href="#pm-pro-playlists" id="pm-pro-playlists-btn" class="pm-pro-playlists-btn" data-profile-id="{$profile_data.id}" data-toggle="tab">{$lang._playlists}</a></li>
						{if $smarty.const._MOD_SOCIAL && $s_user_id == $profile_data.id}
							<li><a href="#pm-pro-followers" data-toggle="tab">{$lang.subscribers|default:'Subscribers'}</a></li>
							<li><a href="#pm-pro-following" data-toggle="tab">{$lang.subscribed_to|default:'Subscribed to'}</a></li>
						{/if}
						<li><a href="#pm-pro-about" data-toggle="tab">{$lang._about}</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="col-md-12">
		<div class="tab-content tab-content-channel">
			<div class="tab-pane fade" id="pm-pro-about">
				<h4>{$lang._about}</h4>
				{if $about}
				<p>{$about}</p>
				{else}
				<p>{$lang.profile_msg_about_empty}</p>
				{/if}
				<div class="clearfix"></div>

	 			{if pm_count($profile_data.social_links) > 0}
				<h4>{$lang._social}</h4>
				<ul class="pm-pro-social-links list-unstyled">
					{if $profile_data.social_links.website != ''} 
						<li><i class="fa fa-globe"></i> <a href="{$profile_data.social_links.website}" target="_blank" rel="nofollow">{$profile_data.social_links.website}</a></li>
					{/if}
					{if $profile_data.social_links.facebook != ''}
						<li><i class="fa fa-facebook-square"></i> <a href="{$profile_data.social_links.facebook}" target="_blank" rel="nofollow">{$profile_data.social_links.facebook}</a></li>
					{/if}
					{if $profile_data.social_links.twitter != ''}
						<li><i class="fa fa-twitter"></i> <a href="{$profile_data.social_links.twitter}" target="_blank" rel="nofollow">{$profile_data.social_links.twitter}</a></li>
					{/if}
					{if $profile_data.social_links.instagram != ''}
						<li><i class="fa fa-instagram"></i> <a href="{$profile_data.social_links.instagram}" target="_blank" rel="nofollow">{$profile_data.social_links.instagram}</a></li> 
					{/if}
				</ul>
				{/if}
				<div class="clearfix"></div>
 			</div>

			<div class="tab-pane {if $smarty.get.view == 'playlists'}fade in active{else}fade{/if}" id="pm-pro-playlists">
				{if $profile_data.id == $s_user_id}
				<h4>{$lang.my_playlists}</h4>
				{else}
				<h4>{$lang._playlists}</h4>
				{/if}
				<div id="profile-playlists-container">
					<span id="loading"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" alt="{$lang.please_wait}" align="absmiddle" border="0" width="16" height="16" /> {$lang.please_wait}</span>
				</div>
			</div>

			<div class="tab-pane {if $smarty.get.view == 'videos' || $smarty.get.view == ''}active{else}fade{/if}" id="pm-pro-own">
				{if $profile_data.id == $s_user_id}
				<h4>{$lang.your_videos|default:"Your Videos"}</h4>
				{else}
				<h4>{$lang.videos_from_s|sprintf:$profile_data.username}</h4>
				{/if}

				<ul class="row pm-ul-browse-videos list-unstyled">
					{foreach from=$submitted_video_list key=k item=video_data}
					<li class="col-xs-6 col-sm-4 col-md-3">
						{include file='item-video-obj.tpl'}
					</li>
					{foreachelse}
					<li class="col-xs-12 col-sm-12 col-md-12">
						{$lang.top_videos_msg2}
					</li>
					{/foreach}

					{if pm_count($submitted_video_list) > 10}
					<li class="col-xs-6 col-sm-4 col-md-3">
						<div class="thumbnail_watch_all">
							<a href="{$smarty.const._URL}/search.php?keywords={$username}&btn=Search&t=user" class="btn btn-primary" title="{$lang.profile_watch_all}">{$lang.profile_watch_all}</a>
						</div>
					</li>
					{/if}
				</ul>
			</div>

			{if $smarty.const._MOD_SOCIAL}
			<div class="tab-pane fade" id="pm-pro-followers">
				<h4>{$lang.subscribers|default:'Subscribers'}</h4>
				<div id="pm-pro-followers-content" class="mt-3"></div>
			</div>
			
			<div class="tab-pane fade" id="pm-pro-following">
			{if is_array($who_to_follow_list)}
				<h4>{$lang.suggested_channels|default:'Suggested channels'}</h4>
				<ul class="row pm-channels-list list-unstyled mt-3">
				{foreach from=$who_to_follow_list key=k item=user_data}
				<li class="col-sm-6 col-md-4">
					<div class="pm-channel">
						<div class="pm-channel-header">
							<div class="pm-channel-cover">
								{if $user_data.channel_cover.max}
								<img src="{$user_data.channel_cover.450}" alt="{$user_data.username}"  border="0" class="img-responsive">
								{/if}
							</div>
							<div class="pm-channel-profile-pic">
								<a href="{$user_data.profile_url}"><img src="{$user_data.avatar_url}" alt="{$user_data.username}"  border="0" class="img-responsive"></a>
							</div>
						</div>
						<div class="pm-channel-body">
							<h3><a href="{$user_data.profile_url}" class="ellipsis {if $user_data.user_is_banned}pm-user-banned{/if}">{$user_data.name}</a> {if $user_data.is_following_me}<span class="label label-sm label-success label-follow-status pull-right">{$lang.subscriber|default:'Subscriber'}</span>{/if}</h3>
							<div class="pm-channel-stats"> {$user_data.videos_count_formatted} {$lang.videos|lower} / {$user_data.followers_count_formatted} {$lang.subscribers|lower}</div>
							<!-- <div class="pm-channel-desc">{$user_data.about}</div> -->
							<div class="pm-channel-buttons">
								{if $smarty.const._MOD_SOCIAL && $logged_in == '1' && $user_data.id != $s_user_id}
									{include file="user-subscribe-button.tpl" profile_data=$user_data profile_user_id=$user_data.id}
								{/if}
							</div>
						</div>
					</div>
				</li>
				{/foreach}
				</ul>
			{/if}

			<h4>{$lang.subscribed_to|default:'Subscribed to'}</h4>
			<div id="pm-pro-following-content" class="mt-3"></div>
			</div>

			{if $s_user_id == $profile_data.id || $profile_data.am_following}
			<div class="tab-pane fade" id="pm-pro-user-activity"> 
				<h4>{$lang.my_activity}</h4>
				<div id="pm-pro-user-activity-content"></div>
			</div>
			{/if}
			
			{if $s_user_id == $profile_data.id}
			<div class="tab-pane fade" id="pm-pro-activity-stream">
				<h4>{$lang.activity_newsfeed}</h4>
				<form name="user-update-status" method="post" action="" onsubmit="update_status();return false;">
					<div class="form-group">
						<textarea class="form-control" name="post-status" ></textarea>
					</div>
					<div class="form-group">
						<button type="submit" name="btn-update-status" class="btn btn-sm btn-success">{$lang.status_update}</button>
					</div>
				</form>
				<div id="pm-pro-activity-stream-content">
					{include file='activity-stream.tpl'}
				</div>
			</div>
			{/if}
			{/if}
		  </div><!-- /tab-content -->
		</div>
		<input type="hidden" name="profile_user_id" value="{$profile_data.id}" />
		</div><!-- #content -->
	  </div><!-- .row -->
	</div><!-- .container -->
{include file='footer.tpl' tpl_name='channel'}