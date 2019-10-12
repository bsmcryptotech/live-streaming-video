{include file='header.tpl' no_index='1' p="general" tpl_name="video-edit"}
{include file="profile-header.tpl"}
<div id="content" class="content-detached content-video-handler">
<div class="container-fluid">
	<div class="row row-page-heading">
		<div class="col-md-12">
			<a href="{$current_user_data.profile_videos_url}" class="btn btn-sm btn-default hidden-xs" rel="tooltip" title="{$lang.return_to_profile}"><i class="mico mico-keyboard_arrow_left md-24"></i></a>

			<h1>{$lang.edit_video|default:'Edit video'}: <a href="{$video_data.video_href}" target="_blank">{$video_data.video_title}</a></h1>
			<div>
				<small><div id="uploadProgressBar"></div></small>
				<div id="divStatus"></div>
				<ol id="uploadLog" class="list-unstyled"></ol>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			{if $success == 'updated'}
				<div class="alert alert-success">
				{$lang.video_updated|default:'The video was successfully updated. <a href="%s">Return to your videos</a>.'|sprintf:$current_user_data.profile_videos_url} 
				<br />
				<a href="{$video_data.video_href}" target="_blank">{$lang.click_to_watch|default:'Click here to watch'} {$video_data.video_title}</a>
				</div>
			{/if}
			
			{if $success == 'deleted'}
				<div class="alert alert-success">
				{$lang.video_deleted|default:'The video was successfully removed. <a href="%s">Return to your videos</a>.'|sprintf:$current_user_data.profile_videos_url}
				</div>
			{/if}

			{if pm_count($errors) > 0}
			<div class="alert alert-danger">
				<ul class="list-unstyled">
				{foreach from=$errors item=v}
					<li>{$v}</li>
				{/foreach}
				</ul>
			</div>
			{/if}

			{if $show_form}

			{if $video_status == 'pending'}
			<div class="alert alert-danger">
				{$lang.video_pending_approval|default:'This video is pending approval.'}
			</div>
			{/if}


			<div class="alert alert-danger hide-me" id="manage-video-ajax-message"></div>

			<div class="pm-video-manage-form">
			<form class="form-horizontal" name="edit-video-form" id="edit-video-form" enctype="multipart/form-data" method="post" action="{$form_action}">
				<fieldset>

					<div class="form-group">
						<label for="video_title" class="col-md-2 control-label">{$lang.video}</label>
						<div class="col-md-10">
						<input type="text" class="form-control" name="video_title" value="{$video_data.video_title|escape:'html'}">
						</div>
					</div>

					{if $video_type == 'uploaded'}
					<div class="form-group">
						<label for="capture" class="col-md-2 control-label">{$lang.upload_video2}</label>
						<div class="col-md-10">
							<div class="fileinput fileinput-exists" data-provides="fileinput">
								<div class="fileinput-new thumbnail"></div>
								<div class="fileinput-preview fileinput-exists thumbnail">
									{if $video_type == 'suggested'}
										{if $video_data.image_url != ''}
											<img src="{$video_data.image_url}?cache-buster={$smarty.now}" width="200" height="133" />
										{else}
											<img src="{$smarty.const._NOTHUMB}" width="200" />
										{/if}
										<input type="hidden" name="direct" value="{$video_data.direct}" />
										<input type="hidden" name="yt_thumb" value="{$video_data.image_url}" />
									{else if $video_type == 'uploaded'}
										{if $video_data.image_url != ''}
											<img src="{$video_data.image_url}?cache-buster=1234" width="200" height="133" />
										{else}
											<img src="{$smarty.const._NOTHUMB}" width="200" />
										{/if}
									{/if}
								</div>
								<div class="fileinput-buttons">
									<span class="btn btn-default btn-file"><span class="fileinput-new">{$lang.upload_video1}</span>

									<span class="fileinput-exists">{$lang._change}</span>
									<input type="file" name="capture" value="">
									<input type="hidden" name="_pmnonce_t" value="{$upload_csrf_token}" />
									<input type="hidden" name="temp_id" id="temp_id" value="" />
									<input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size}">
									<!-- <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i></a> -->
								</div>
							</div>
						</div>
					</div>
					{/if}

					{if $video_type == 'suggested'}
					<div class="form-group">
						<label for="yt_thumb" class="col-md-2 control-label">{$lang.upload_video2}</label>
						<div class="col-md-10">
							{if $video_data.image_url != ''}
							<img src="{$video_data.image_url}" width="200" height="133">
							{/if}
							<hr />
							<input type="text" class="form-control" name="yt_thumb" value="{$video_data.image_url}" placeholder="https://"> 
						</div>
					</div>
					{/if}

					{if $video_type == 'suggested'}
					<div class="form-group">
						<label for="pm_sources" class="col-md-2 control-label">{$lang._videourl}</label>
						<div class="col-md-10">
							<input type="text" class="form-control" name="direct" value="{$video_data.direct}" placeholder="https://"> 
							<span class="form-control-feedback hide-me" id="loading-gif-top"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" width="" height="" alt=""></span>
						</div>
					</div>
					{/if}


					<div class="form-group">
						<label for="duration" class="col-md-2 control-label">{$lang._duration} <a href="#" rel="tooltip" title="{$lang.duration_format}"><i class="fa fa-question-circle"></i></a></label>
						<div class="col-md-10">
						<input type="text" class="form-control text-center" name="duration" id="duration" value="{$video_data.duration}">
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
							<textarea name="description" class="form-control">{if $video_data.description}{$video_data.description}{/if}</textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="tags" class="col-md-2 control-label">{$lang.tags} <a href="#" rel="tooltip" title="{$lang.suggest_ex}"><i class="fa fa-question-circle"></i></a></label>
						<div class="col-md-10">
							<input id="tags_suggest" name="tags" type="text" class="form-control tags" value="{$video_data.tags}">
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-offset-2 col-md-10">
							<button class="btn btn-success btn-with-loader" name="submit-btn" id="edit-video-submit-btn" value="{$lang.submit_submit}" type="submit">{$lang.save_changes|default:'Save changes'}</button>
							{if $allow_user_delete_video}
							<button class="btn btn-danger btn-with-loader" name="delete-btn" id="edit-video-delete-btn" value="{$lang.delete}" type="button">{$lang.delete_this_video|default:'Delete this video'}</button>
							{/if}
							<a href="{$current_user_data.profile_videos_url}" class="btn btn-link">{$lang.submit_cancel}</a>
							<input type="hidden" name="form_id" value="{$form_id}" />
							<input type="hidden" name="_pmnonce_t_edit_video_form" value="{$form_csrf._pmnonce_t}" id="_pmnonce_t_edit_video_form{$form_csrf._pmnonce_t}" />
							<input type="hidden" name="video_status" value="{$video_status}" />
							<input type="hidden" name="vid" value="{$video_id}" />
							<input type="hidden" name="btn-pressed" value="" />
						</div>
					</div>

					<div class="alert hide-me" id="ajax-error-placeholder"></div>
					<div class="alert alert-success hide-me" id="ajax-success-placeholder"></div>
				</fieldset>
				<input type="hidden" name="p" value="video" />
				<input type="hidden" name="do" value="edit-video" />
			</form>
			</div>
		{/if}
		</div><!-- .col-md-12 -->
	</div><!-- .row -->
</div><!-- .container -->     
</div><!-- #content -->
{include file='footer.tpl' tpl_name="video-edit"}