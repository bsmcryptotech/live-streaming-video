{include file='header.tpl' no_index='1' p="suggest" tpl_name="suggest"}
{include file="profile-header.tpl" p="suggest"}

<div id="content" class="content-detached">
	<div class="container-fluid">
	<div class="row row-page-heading">
		<div class="col-md-12">
			<h1>{$lang.suggest}</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
		
		{if $success == 3}
			<div class="alert alert-info">
			{$lang.suggest_msg1}
			</div>
		{/if}
		{if $success == 4}
			<div class="alert alert-info">
			{$lang.suggest_msg2}
			</div>
		{/if}
		{if $success == 5}
			<div class="alert alert-danger">
			{$lang.suggest_msg3}
			</div>
		{/if}
		{if $success == 1}
			<div class="alert alert-success">
			{$lang.suggest_msg4}
			<a href="suggest.{$smarty.const._FEXT}">{$lang.add_another_one}</a> | <a href="index.{$smarty.const._FEXT}">{$lang.return_home}</a>
			</div>
		{else}
		
		{if pm_count($errors) > 0}
		<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<ul class="list-unstyled">
				{foreach from=$errors item=v}
					<li>{$v}</li>
				{/foreach}
				</ul>
		</div>
		{/if}

		<div class="alert alert-warning hide-me" id="ajax-error-placeholder"></div>
		<div class="alert alert-success hide-me" id="ajax-success-placeholder"></div>

		<form class="form-horizontal" id="suggest-form" name="suggest-form" method="post" action="{$form_action}">
			<fieldset>
				<div class="form-group has-feedback">
					<label for="pm_sources" class="col-md-2 control-label">{$lang._videourl}</label>
					<div class="col-md-10">
						<input type="text" class="form-control" name="yt_id" value="{$smarty.post.yt_id}" placeholder="https://">
						<span class="form-control-feedback hide-me" id="loading-gif-top"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" width="" height="" alt=""></span>
					</div>
				</div>

				<div class="pm-video-manage-form hide-me" id="suggest-video-extra">
						<div class="form-group">
							<label for="video_title" class="col-md-2 control-label">{$lang.video}</label>
							<div class="col-md-10">
							<input type="text" class="form-control" name="video_title" value="{$smarty.post.video_title}">
							</div>
						</div>
						<div class="form-group">
							<label for="capture" class="col-md-2 control-label">{$lang.upload_video2}</label>
							<div class="col-md-10">
							<div id="video-thumb-placeholder"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="category" class="col-md-2 control-label">{$lang.category}</label>
							<div class="col-md-10">
								{$categories_dropdown}
							</div>
						</div>
						<div class="form-group">
							<label for="description" class="col-md-2 control-label">{$lang.description}</label>
							<div class="col-md-10">
								<textarea name="description" class="form-control">{if $smarty.post.description}{$smarty.post.description}{/if}</textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="tags" class="col-md-2 control-label">{$lang.tags} <a href="#" rel="tooltip" title="{$lang.suggest_ex}"><i class="fa fa-question-circle"></i></a></label>
							<div class="col-md-10">
									<input id="tags_suggest" name="tags" type="text" class="form-control tags" value="{$smarty.post.tags}">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-offset-2 col-md-10">
								<button class="btn btn-success" name="Submit" id="Submit" value="{$lang.submit_submit}" type="submit">{$lang.submit_submit}</button> <span class="hide-me" id="loading-gif-bottom"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" width="" height="" alt=""></span>
							</div>
						</div>
				</div><!-- #suggest-video-extra -->
				<input type="hidden" name="source_id" value="-1">
				<input type="hidden" name="p" value="suggest">
				<input type="hidden" name="do" value="submitvideo">
			</fieldset>
		</form>
		{/if}
		</div><!-- #content -->
	</div><!-- .row -->
</div><!-- .container -->     
{include file="footer.tpl" tpl_name="suggest"}