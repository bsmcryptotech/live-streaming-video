{if $allow_registration == '1'}
<form class="" id="register-form" name="register-form" method="post" action="{$smarty.const._URL}/register.php">
		<div class="form-group">
			<label for="name">{$lang.your_name}</label>
			<div class="controls"><input type="text" class="form-control" name="name" value="{$inputs.name}"></div>
		</div>
		<div class="form-group">
			<label for="username">{$lang.username}</label>
			<div class="controls"><input type="text" class="form-control" name="username" value="{$inputs.username}"></div>
		</div>
		<div class="form-group">
			<label for="email">{$lang.your_email}</label>
			<div class="controls"><input type="email" class="form-control" id="email" name="email" value="{$inputs.email}" autocomplete="off"></div>
		</div>
		<div class="form-group">
			<label for="pass">{$lang.password}</label>
			<div class="controls"><input type="password" class="form-control" id="pass" name="pass" maxlength="32" autocomplete="off"></div>
		</div>
		<div class="form-group">
			<label for="confirm_pass">{$lang.password_retype}</label>
			<div class="controls">
			<input type="password" class="form-control" id="confirm_pass" name="confirm_pass" maxlength="32">
			</div>
		</div>
		<div class="form-group">
			<label for="country">{$lang.country}</label>
			<div class="controls">
		{if $show_countries_list}
		<select name="country" class="form-control">
		<option value="-1">{$lang.select}</option>
			{foreach from=$countries_list key=k item=v}
			<option value="{$k}" {if $inputs.country == $k}selected{/if}>{$v}</option>
			{/foreach}
		</select>
		{/if}
		<input type="text" name="website" class="hide-me botmenot" maxlength="32">
			</div>
		</div>
	{if $spambot_prevention == 'securimage'}
		<div class="form-group">
		<div class="controls">
					<input type="text" name="imagetext" class="form-control" autocomplete="off" placeholder="{$lang.enter_captcha}">
					<img src="{$smarty.const._URL}/include/securimage_show.php?sid={echo_securimage_sid}" id="captcha_image" align="absmiddle" alt="" class="img-rounded">
					<button class="btn btn-link btn-refresh" onclick="document.getElementById('captcha_image').src = '{$smarty.const._URL}/include/securimage_show.php?sid=' + Math.random(); return false"><i class="fa fa-refresh"></i></button>
			</div>
		</div>
	{/if}
	{if $spambot_prevention == 'recaptcha'}
	<div class="form-group">
		<div class="controls">
			{$recaptcha_html}
		</div>
	</div>
	{/if}

	<div class="checkbox">
		<label>
		<input type="checkbox" id="agree" name="agree" {if $inputs.agree == 'on'}checked="checked"{/if}> <span class="help-inline">{$lang.i_agree_with} <a data-toggle="modal" href="#modal-terms" id="element" >{$lang.terms_of_agreement}</a></span>
		</label>
	</div>
	<div class="form-group">
		<input type="hidden" class="form-control" name="gender" value="male">
		<button type="submit" name="Register" value="{$lang.register}" class="btn btn-success" data-loading-text="{$lang.register}">{$lang.register}</button>
	</div>
</form>

{include file='modal-info-terms.tpl'}

{else}
{$lang.registration_closed}
{/if}