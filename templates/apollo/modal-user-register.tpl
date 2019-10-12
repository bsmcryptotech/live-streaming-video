<div class="modal" id="modal-register-form">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{$lang.close}</span></button>
				<h4 class="modal-title">{$lang.create_account}</h4>
			</div>
			<div class="modal-body">
				<a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}" class="btn btn-success btn-block">{$lang.register_with_email}</a>
				{if $allow_facebook_login || $allow_twitter_login || $allow_google_login}
				<hr />
				<div class="pm-social-accounts">
					<label>{$lang.register_with_social}</label>
					{if $allow_facebook_login}
					<a href="{$smarty.const._URL}/login.php?do=facebook" class="btn btn-facebook" rel="nofollow"><i class="fa fa-facebook-square"></i>Facebook</a>
					{/if}
					{if $allow_twitter_login}
					<a href="{$smarty.const._URL}/login.php?do=twitter" class="btn btn-twitter" rel="nofollow"><i class="fa fa-twitter"></i> Twitter</a>
					{/if}
					{if $allow_google_login}
					<a href="#" class="btn btn-google" id="google-register-btn" rel="nofollow"><i class="fa fa-google"></i> Google</a>
					{/if}
				</div>
				<div class="google-login-response mt-3"></div>
				<div class="clearfix"></div>
				{/if}
			</div>
		</div>
	</div>
</div>