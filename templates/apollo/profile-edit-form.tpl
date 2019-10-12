<form class="form-horizontal" name="register-form" id="register-form" method="post" action="{$form_action}" role="form">
	<fieldset>
		<h4>{$lang.about_me}</h4>
		<hr>
		<div class="form-group">
			<label for="name" class="col-md-2 control-label">{$lang.your_name}</label>
			<div class="col-md-4"><input type="text" class="form-control" name="name" {if isset($inputs.name)}value="{$inputs.name}"{else}value="{$userdata.name}"{/if}></div>
		</div>
		<div class="form-group">
			<label for="email" class="col-md-2 control-label">{$lang.your_email} <a href="#" rel="tooltip" title="{$lang.safe_email}"><i class="fa fa-question-circle"></i> </a></label>
			<div class="col-md-4">
			<input type="text" class="form-control" name="email" {if isset($inputs.email)}value="{$inputs.email}"{else}value="{$userdata.email}"{/if}>
			</div>
		</div>

		<div class="form-group">
			<label for="country" class="col-md-2 control-label">{$lang.country}</label>
			<div class="col-md-2">
			{if $show_countries_list}
			<select name="country" size="1" class="form-control">
			<option value="-1">{$lang.select}</option>
					{foreach from=$countries_list key=k item=v}
					<option value="{$k}" {if $inputs.country == $k}selected{elseif $userdata.country == $k}selected{/if}>{$v}</option>
					{/foreach}
			</select>
			{/if}
			</div>
		</div>
		
		<div class="form-group">
			<label for="aboutme" class="col-md-2 control-label">{$lang.about_me}</label>
			<div class="col-md-4"><textarea name="aboutme" class="form-control" rows="2">{if isset($inputs.aboutme)}{$inputs.aboutme}{elseif isset($userdata.about)}{$userdata.about}{/if}</textarea></div>
		</div>
	</fieldset>

	<fieldset>
		<h4>{$lang._social}</h4>
		<hr>
		<div class="form-group">
			<label for="website" class="col-md-2 control-label">{$lang.profile_social_website}</label>
			<div class="col-md-4"><input type="text" class="form-control" name="website" {if isset($inputs.website)}value="{$inputs.website}"{else}value="{$userdata.social_links.website}"{/if} placeholder="https://"></div>
		</div>
		<div class="form-group">
			<label for="facebook" class="col-md-2 control-label">{$lang.profile_social_fb}</label>
			<div class="col-md-4"><input type="text" class="form-control" name="facebook" {if isset($inputs.facebook)}value="{$inputs.facebook}"{else}value="{$userdata.social_links.facebook}"{/if} placeholder="https://"></div>
		</div>
		<div class="form-group">
			<label for="twitter" class="col-md-2 control-label">{$lang.profile_social_twitter}</label>
			<div class="col-md-4"><input type="text" class="form-control" name="twitter" {if isset($inputs.twitter)}value="{$inputs.twitter}"{else}value="{$userdata.social_links.twitter}"{/if} placeholder="https://"></div>
		</div>
		<div class="form-group">
			<label for="instagram" class="col-md-2 control-label">{$lang.profile_social_instagram|default:'Instagram URL'}</label>
			<div class="col-md-4"><input type="text" class="form-control" name="instagram" {if isset($inputs.instagram)}value="{$inputs.instagram}"{else}value="{$userdata.social_links.instagram}"{/if} placeholder="https://"></div>
		</div>
	</fieldset>

	<fieldset>
		<h4>{$lang.change_pass}</h4>
		<hr>
		<div class="form-group has-error">
			<label for="current_pass" class="col-md-2 control-label">{$lang.existing_pass}</label>
			<div class="col-md-4">
			<input type="password" class="form-control" name="current_pass" maxlength="32">
			</div>
		</div>
		<div class="form-group">
			<label for="new_pass" class="col-md-2 control-label">{$lang.new_pass}</label>
			<div class="col-md-4">
			<input type="password" class="form-control" name="new_pass" maxlength="32">
			<p class="help-block"><small>{$lang.ep_msg5}</small></p>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-2 col-md-10">
			<input type="hidden" class="form-control" name="gender" value="male">
			<button type="submit" name="save" value="{$lang.submit_save}" class="btn btn-success" data-loading-text="{$lang.submit_save}">{$lang.submit_save}</button>
			</div>
		</div>
	</fieldset>
</form>