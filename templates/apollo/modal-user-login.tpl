<div class="modal" id="modal-login-form">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{$lang.close}</span></button>
				<h4 class="modal-title">{$lang.sign_in|default:'Sign in'}</h4>
			</div>
			<div class="modal-body">
				{if $logged_in != '1' && $allow_registration == '1'}
				<div class="hidden-md hidden-lg">
					<label>{$lang.no_account_register|default:"Don't have an account yet? Register today!"}</label>
						{if $allow_facebook_login || $allow_twitter_login || $allow_google_login}
						<a class="btn btn-sm btn-block btn-success ajax-modal" data-toggle="modal" data-dismiss="modal" data-backdrop="true" data-keyboard="true" href="#modal-register-form">{$lang.register}</a>
						{else}
						<a class="btn btn-sm btn-block btn-success" href="{$smarty.const._URL}/register.{$smarty.const._FEXT}">{$lang.register}</a>
						{/if}
					<hr />
				</div>
				{/if}

				{include file="user-auth-login-form.tpl"}
				{if $allow_facebook_login || $allow_twitter_login || $allow_google_login}
				<hr />
				<div class="pm-social-accounts">
					<label>{$lang.login_with_social}</label>
					{if $allow_facebook_login}
					<a href="{$smarty.const._URL}/login.php?do=facebook" class="btn btn-facebook" rel="nofollow"><i class="fa fa-facebook-square"></i>Facebook</a>
					{/if}
					{if $allow_twitter_login}
					<a href="{$smarty.const._URL}/login.php?do=twitter" class="btn btn-twitter" rel="nofollow"><i class="fa fa-twitter"></i> Twitter</a>
					{/if}
					{if $allow_google_login}
					<a href="#" class="btn btn-google" id="google-login-btn" rel="nofollow"><i class="fa fa-google"></i> Google</a>
					{/if}
				</div>
				<div class="google-login-response mt-3"></div>
				<div class="clearfix"></div>
				{/if}
			</div>
		</div>
	</div>
</div>