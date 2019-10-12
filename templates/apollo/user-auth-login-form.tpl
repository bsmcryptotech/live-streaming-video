<form name="login_form" id="login-form" method="post" action="{$smarty.const._URL}/login.php">
	<div class="form-group">
		<label for="username">{$lang.your_username_or_email}</label>
		<input type="text" class="form-control" name="username" value="{$inputs.username}" placeholder="{$lang.your_username_or_email}">
	</div>
	<div class="form-group">
		<label for="pass">{$lang.password}</label>
		<input type="password" class="form-control" id="pass" name="pass" maxlength="32" autocomplete="off" placeholder="{$lang.password}">
	</div>
	<div class="form-group">
		<button type="submit" name="Login" value="{$lang.login}" class="btn btn-success btn-with-loader" data-loading-text="{$lang.logging_in|default:'Signing in...'}">{$lang.login}</button> 
		<small><a href="{$smarty.const._URL}/login.{$smarty.const._FEXT}?do=forgot_pass">{$lang.forgot_pass}</a></small>
	</div>
</form>