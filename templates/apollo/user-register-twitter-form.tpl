<form class="form-horizontal" id="register-form" name="register-form" method="post" action="{$smarty.const._URL}/login.php?do=twitter&step=email">
	{if pm_count($errors) > 0}
		<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<ul class="subtle-list">
			{foreach from=$errors item=v}
				<li>{$v}</li>
			{/foreach}
		</ul>
		</div>
	{/if}
	
	<div class="alert alert-info">
		{$lang.enter_mail_for_twitter}
	</div>
	
	<div class="pm-twitter-account">
		<div class="pm-twitter-cover">
			<img src="{$twitter_userdata.profile_banner_url}" />
		</div>
		<div class="pm-twitter-avatar">
			<img src="{$twitter_avatar_url}" width="100" height="100" class="img-responsive" />
		</div>

		<div class="pm-twitter-account-details">

			<div class="form-group">
				<h3>@{$twitter_userdata.screen_name}</h3>
			</div>
			<div class="form-group">
				<label for="email">{$lang.your_email}</label>
				<div class="controls">
					<input type="email" class="form-control" id="email" name="email" value="" autocomplete="off">
					<small>A password will be e-mailed to you</small>
				</div>
			</div>
			<div class="form-group">
				<button type="submit" name="Register" value="{$lang.register}" class="btn btn-success" data-loading-text="{$lang.register}">{$lang.register}</button>
			</div>
		</div>
	</div>

	<div class="clearfix"></div>
</form>