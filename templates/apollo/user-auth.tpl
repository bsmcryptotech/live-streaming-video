{include file='header.tpl' p="general"} 
<div id="content">
  <div class="container-fluid">
	<div class="row pm-user-auth">
	<div class="col-md-12">
		<nav class="tabbable" role="navigation">
			<h1>{$lang.my_account|default:'My Account'}</h1>

			<ul class="nav nav-tabs nav-underlined nav-right">
				{if $allow_registration == '1'}
				<li{if $display_form == 'register' || $display_form == 'twitter'} class="active"{/if}>
					{if $display_form == 'register'}
						<a href="#pm-register" data-toggle="tab">{$lang.create_account}</a>
					{else}
						<a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}">{$lang.create_account}</a>
					{/if}
				</li>
				{/if}
				<li{if $display_form == 'login'} class="active"{/if}><a href="#pm-login" data-toggle="tab">{$lang.login}</a></li>
				
				{if $display_form == 'forgot_pass'}
				<li class="active"><a href="#pm-reset" data-toggle="tab">{$lang.forgot_pass}</a></li>
				{/if}
			</ul>
		</nav><!-- #site-navigation -->

		<div class="tab-content">
			<div class="tab-pane{if $display_form == 'register' || $display_form == 'twitter'} active{/if}" id="pm-register">
			{if $display_form == 'register'}
				{if $success}
					<div class="alert alert-info">
						{$lang.register_msg2} {$inputs.email}. <br />{$msg}<br />
					</div>
				{else}
					{if pm_count($errors) > 0}
						<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<ul class="list-unstyled">
							{foreach from=$errors item=v}
								<li><i class="fa fa-warning"></i> {$v}</li>
							{/foreach}
						</ul>
						</div>
					{/if}
					{include file='user-register-form.tpl'}
				{/if}
			{elseif $display_form == 'twitter'}
				{include file='user-register-twitter-form.tpl'}
			{else}
				{include file='user-register-form.tpl'}
			{/if}
			</div>
			
			<div class="tab-pane{if $display_form == 'login'} active{/if}" id="pm-login">
			{if $display_form == 'login'}
				{if $success}
					
				{else}
					{if pm_count($errors) > 0}
						<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<ul class="list-unstyled">
							{foreach from=$errors item=v}
								<li><i class="fa fa-warning"></i> {$v}</li>
							{/foreach}
						</ul>
						</div>
					{/if}
					{include file='user-auth-login-form.tpl'}
				{/if}
			{else}
				{include file='user-auth-login-form.tpl'}
			{/if} 
			</div>
			
			<div class="tab-pane{if $display_form == 'forgot_pass'} active{/if}" id="pm-reset">
			{if $display_form == 'forgot_pass'}
				{if $success}
					<div class="alert alert-info">
						{$lang.fp_msg}
					</div>
				{else}
					{if pm_count($errors) > 0}
					<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<ul class="list-unstyled">
						{foreach from=$errors item=v}
						  <li><i class="fa fa-warning"></i> {$v}</li>
						{/foreach}
						</ul>
					</div>
					{/if}
					{include file='user-auth-forgot-pass-form.tpl'}
				{/if}
			{else}
				{include file='user-auth-forgot-pass-form.tpl'}
			{/if}
			</div>


			<div class="tab-pane{if $display_form == 'activate_acc'} active{/if}" id="pm-reset">
			{if $display_form == 'activate_acc'}
				{if $success}
					<div class="alert alert-success">
						{$lang.activate_account_msg1}
					</div>
				{else}
					{if pm_count($errors) > 0}
					<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<ul class="list-unstyled">
						{foreach from=$errors item=v}
						  <li><i class="fa fa-warning"></i> {$v}</li>
						{/foreach}
						</ul>
					</div>
					{/if}
				{/if}
			{/if}
			</div>
			
			<div class="tab-pane{if $display_form == 'pwdreset'} active{/if}" id="pm-reset">
			{if $display_form == 'pwdreset'}
				{if $success}
					<div class="alert alert-success">
						{$lang.activate_pass_msg1}
					</div>
				{else}
					{if pm_count($errors) > 0}
					<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<ul class="list-unstyled">
						{foreach from=$errors item=v}
						  <li><i class="fa fa-warning"></i> {$v}</li>
						{/foreach}
						</ul>
					</div>
					{/if}
				{/if}
			{/if}
			</div>
			
		</div><!-- /tab-content -->
	</div><!-- #content -->
	</div><!-- .row --> 
  </div><!-- .container -->


{include file='footer.tpl' p='auth'} 