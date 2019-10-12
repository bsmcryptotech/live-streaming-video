<div name="mycommentspan" id="mycommentspan" class="hide-me"></div>
{if $logged_in == '1'}
<div class="row" id="pm-post-form">
	<div class="col-xs-2 col-sm-1 col-md-1">
		<span class="pm-avatar"><img src="{$s_avatar_url}" class="pm-round-avatar" height="40" width="40" alt=""></span>
	</div>
	<div class="col-xs-10 col-sm-11 col-md-11">
	<form action="" name="form-user-comment" method="post">
		<div class="form-group">
			{if $allow_emojis && ($tpl_name == 'article-read' || $tpl_name == 'video-watch' || $tpl_name == 'video-watch-episode' || $tpl_name == 'channel')}
			<a data-toggle="modal" data-remote="{$smarty.const._URL}/templates/{$template_dir}/emoji-help.php" href="#" data-target="#modalEmojiList" class="emoji-shortcut"><i class="mico mico-tag_faces"></i></a>
			{/if}
			<textarea name="comment_txt" id="c_comment_txt" rows="2" class="form-control" placeholder="{$lang.your_comment}"></textarea>
		</div>
		<div class="form-group">
			<input type="hidden" id="c_vid" name="vid" value="{$uniq_id}">
			<input type="hidden" id="c_user_id" name="user_id" value="{$user_id}">
			<button type="submit" id="c_submit" name="Submit" class="btn btn-sm btn-success btn-with-loader" data-loading-text="{$lang.submit_comment}">{$lang.submit_comment}</button>
		</div>
	</form>
	</div>
</div>

{elseif $logged_in == 0 && $guests_can_comment == 1}
<div class="row" id="pm-post-form">
	<div class="col-xs-2 col-sm-1 col-md-1">
		<span class="pm-avatar"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/pm-avatar.png" class="pm-round-avatar" width="40" height="40" alt="" border="0"></span>
	</div>
	<div class="col-xs-10 col-sm-11 col-md-11">
		<form action="" name="form-user-comment" method="post">
		<div class="form-group">
			<div class="row">
				<div class="col-md-11">
					<textarea name="comment_txt" id="c_comment_txt" rows="2" class="form-control" placeholder="{$lang.your_comment}"></textarea>
				</div>
			</div>
		</div>
		<div class="form-group hide-me" id="pm-comment-form">
			<div class="row">
				<div class="col-md-4">
					<input type="text" id="c_username" name="username" value="{$guestname}" class="form-control" placeholder="{$lang.your_name}">
				</div>
				<div class="col-md-4">
					<input type="text" id="captcha" name="captcha" class="form-control" placeholder="{$lang.confirm_code}">
				</div>
				<div class="col-md-4">
					<img src="{$smarty.const._URL}/include/securimage_show.php?sid={echo_securimage_sid}" id="captcha-image" alt="" width="100" height="35">
					<button class="btn btn-sm btn-link btn-refresh" onclick="document.getElementById('captcha-image').src = '{$smarty.const._URL}/include/securimage_show.php?sid=' + Math.random(); return false"><i class="fa fa-refresh"></i></button>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-md-11">
					<input type="hidden" id="c_vid" name="vid" value="{$uniq_id}">
					<input type="hidden" id="c_user_id" name="user_id" value="0">
					<button type="submit" id="c_submit" name="Submit" class="btn btn-sm btn-success btn-with-loader" data-loading-text="{$lang.submit_comment}">{$lang.submit_comment}</button>
				</div>
			</div>
		</div>
		</form>
	</div>
</div>
{/if}