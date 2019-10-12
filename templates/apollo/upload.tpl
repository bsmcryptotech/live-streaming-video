{include file="header.tpl" no_index="1" p="upload" tpl_name="upload"}
{include file="profile-header.tpl" p="upload"}
<div id="content" class="content-detached content-video-handler">
	<div class="container-fluid">
	<div class="row row-page-heading">
		<div class="col-xs-12 col-sm-7 col-md-10">
			<h1>{$lang.upload_video}</h1>
		</div>
		<div class="col-xs-12 col-sm-5 col-md-2">
			<div class="pull-right">
				<div>
					<ol id="upload-video-selected-files-container"></ol>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
	   <div class="col-md-12">
		{if $success == 1}
			<div class="alert alert-success">
			{$lang.suggest_msg4}
			<br />
			<a href="upload.{$smarty.const._FEXT}">{$lang.add_another_one}</a> or <a href="index.{$smarty.const._FEXT}">{$lang.return_home}</a>
			</div>
		{elseif $success == 2}
			<div class="alert alert-warning">
			{$lang.upload_errmsg11} 
			<a href="index.{$smarty.const._FEXT}">{$lang.return_home}</a>
			</div>
		{elseif $success == 'custom'}
			<div class="alert alert-success">
			{$success_custom_message} 
			<a href="upload.{$smarty.const._FEXT}">{$lang.add_another_one}</a> or <a href="index.{$smarty.const._FEXT}">{$lang.return_home}</a>
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


			<div class="hide-me" id="manage-video-ajax-message"></div>
			<div class="form-horizontal">
			<div class="pm-upload-file-select" id="upload-video-dropzone">
				<i class="mico mico-cloud_upload"></i>
				<p>{$lang.drop_files|default:"Drop file here"}</p>
				<div class="clearfix"></div>
				<span class="btn-upload fileinput-button">
					<span class="btn-upload-value">{$lang.upload_video1}</span>
					<input type="file" name="video" id="upload-video-file-btn" />
				</span>
			</div>
			<div class="clearfix"></div>


			<form class="form-horizontal" name="upload-video-form" id="upload-video-form" enctype="multipart/form-data" method="post" action="{$form_action}" role="form">
				<div class="alert alert-danger hide-me" id="error-placeholder"></div>

				<div class="pm-video-manage-form">
					<fieldset>
						<div id="upload-video-extra">
							<div class="form-group">
							  <label for="video_title" class="col-md-2 control-label">{$lang.video}</label>
							  <div class="col-md-10">
							  <input name="video_title" type="text" value="{$smarty.post.video_title}" class="form-control">
							  </div>
							</div>
							<div class="form-group">
							  <label for="capture" class="col-md-2 control-label">{$lang.upload_video2}</label>
							  <div class="col-md-10">
								<div class="fileinput fileinput-new" data-provides="fileinput">
								  <div class="fileinput-new thumbnail"><img data-src="" alt=" " src="" width="" height=""></div>
								  <div class="fileinput-preview fileinput-exists thumbnail"></div>
								  <div class="fileinput-buttons">
									<span class="btn btn-sm btn-default btn-file"><span class="fileinput-new">{$lang.upload_video1}</span>
									<span class="fileinput-exists">{$lang._change}</span>
									<input type="file" name="capture" value="">
									<!-- <a href="#" class="fileinput-exists" data-dismiss="fileinput"><i class="mico mico-delete"></i></a> -->
								  </div>
								</div>
							  </div>
							</div>
							<div class="form-group">
							  <label for="duration" class="col-md-2 control-label">{$lang._duration} <a href="#" rel="tooltip" title="{$lang.duration_format}"><i class="fa fa-question-circle"></i></a></label>
							  <div class="col-md-10">
							  <input name="duration" id="duration" type="text" value="{$smarty.post.duration}" class="form-control text-center">
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
								<textarea name="description" class="form-control" rows="3">{if $smarty.post.description}{$smarty.post.description}{/if}</textarea>
							  </div>
							</div>
							<div class="form-group">
							  <label for="tags" class="col-md-2 control-label">{$lang.tags} <a href="#" rel="tooltip" title="{$lang.suggest_ex}"><i class="fa fa-question-circle"></i></a></label>
							  <div class="col-md-10">
								<span class="tagsinput">
								  <input id="tags_upload" name="tags" type="text" class="form-control tags" value="{$smarty.post.tags}">
								</span>
							  </div>
							</div>
							<div class="form-group">
							  <div class="col-md-offset-2 col-md-10">
								<button name="Submit" type="submit" id="upload-video-submit-btn" value="{$lang.submit_upload}" class="btn btn-success" data-loading-text="{$lang.submit_send}">{$lang.submit_upload}</button>
								<a href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}" class="btn btn-link">{$lang.submit_cancel}</a>
							  </div>
							</div>
						</div><!-- #upload-video-extra -->
					</fieldset>

					<input type="hidden" name="form_id" value="{$form_id}" />
					<input type="hidden" name="_pmnonce_t" id="upload-video-form-nonce" value="{$form_csrfguard_token}" />
					<input type="hidden" name="temp_id" id="temp_id" value="" />
					<input type="hidden" name="p" value="upload" />
					<input type="hidden" name="do" value="upload-media-file" />
					<input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size}">
				</div>
			</form>
		{/if}
		</div><!-- .col-md-12 -->
	</div><!-- .row --> 
  </div>
  </div>
  <!-- .container -->
{include file="footer.tpl" tpl_name="upload"}