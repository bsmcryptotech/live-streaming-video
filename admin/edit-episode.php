<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '2';
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_uniform = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$load_edit_episode_js = true;
$load_jquery_ui = true;
$load_sortable = true;
$load_googlesuggests = 1;
$_page_title = (isset($_GET['do']) && $_GET['do'] == 'new') ? 'Add New Episode' : 'Edit episode';
include('header.php');

$is_new_episode = (isset($_GET['do']) && $_GET['do'] == 'new') ? true : false;
$episode_data = ( ! empty($_GET['episode_id'])) 
	? get_episode($_GET['episode_id'])
	: array('episode_id' => 0,
			'uniq_id' => '',
			'media_sources' => array(),
			'series_data' => ( ! empty($_GET['series_id'])) ? get_series((int) $_GET['series_id']) : array()
		);

// fallback form processing
if ( ! empty($_POST))
{
	if ($_POST['episode_id'] == 0)
	{
		$result = insert_new_episode($_POST);

		if ($result['episode_id'] > 0)
		{
			$episode_data = get_episode($result['episode_id']);
		}
	}
	else
	{
		$result = update_episode($episode_data['episode_id'], $_POST);
		$episode_data = get_episode($episode_data['episode_id']);
	}

	$message = ($result['type'] == 'error') 
		? pm_alert_error($result['msg'])
		: pm_alert_success($result['msg']);
}

if (empty($episode_data)) 
{
?>
<!-- Main content -->
<div class="content-wrapper">
<div class="content">
<h2>Edit Episode</h2>
<?php echo pm_alert_error('The requested episode was not found.'); ?>
</div>
<?php
include('footer.php');
exit();
}
?>

<div class="content-wrapper">
<div class="page-header-wrapper page-header-edit"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4>
				<span class="font-weight-semibold"><?php echo ($is_new_episode) ? 'Add New' : 'Edit'; ?> Episode</span>
				<?php if ( ! $is_new_episode) : ?>
					<a href="<?php echo _URL .'/episode.php?id='. $episode_data['episode_id']; ?>" target="_blank" data-popup="tooltip" data-original-title="Preview"><small class="text-muted"><?php echo htmlspecialchars($episode_data['video_title']); ?> <i class="mi-open-in-new"></i></small></a> 
				<?php endif; ?>
				</h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<a href="episodes.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2 pm-show-loader">Cancel</a>
                    <button id="" type="button" name="submit-form" value="Save" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.getElementById('edit-episode-form-submit-btn').click();" form="edit-episode-form"><i class="mi-check"></i> Save</button>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
			<div class="d-horizontal-scroll">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="series.php" class="breadcrumb-item">Series</a>
					<a href="episodes.php" class="breadcrumb-item">Episodes</a>
					<?php if ( ! $is_new_episode && ! empty($episode_data['series_data']['title'])) : ?>
					<a href="episodes.php?filter=series&fv=<?php echo $episode_data['series_data']['series_id'];?>" class="breadcrumb-item"><?php echo $episode_data['series_data']['title']; ?></a>
					<?php endif; ?>
					<span class="breadcrumb-item active"><?php echo $_page_title; ?></span>
				</div>
			</div>
			</div>

			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->
</div><!--.page-header-wrapper-->

<!-- Content area -->
<div class="content content-edit-page">
<?php echo $message; ?>

<form id="edit-episode-form" name="update" enctype="multipart/form-data" action="edit-episode.php?episode_id=<?php echo $episode_data['episode_id']; ?>" method="post" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')">
<div class="row">
	<div class="col-sm-12 col-md-9">

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Part of Series</h6>
			</div>
			<div class="card-body">
				
				<div class="row">
					<div class="col-md-1">

						<img src="<?php echo $episode_data['series_data']['image_url']; ?>" width="50" id="episode-series-img" class="" />

					</div>

					<div class="col-md-4">
						<label>
							<strong id="episode-series-title"><?php echo (!empty($episode_data['series_data']['title'])) ? $episode_data['series_data']['title'] : 'Series'; ?></strong>
						</label>

						<?php $all_series = get_series_list(array(), null, null, 0, $config['total_series']); ?>
						<select name="series_id" id="main_select_episode_series" class="category_dropdown custom-select mb-1 form-required chzn-select" data-placeholder="Choose a series..." tabindex="-1">
							<option value></option>
							<?php foreach ($all_series as $k => $series_data) : ?>
								<option value="<?php echo $series_data['series_id']; ?>" <?php echo ($series_data['series_id'] == $episode_data['series_data']['series_id']) ? 'selected="selected"' : ''; ?>><?php echo $series_data['title']; echo ( ! empty($series_data['release_year'])) ? " (". $series_data['release_year'] .")" : ''; ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="col-md-3">
						<label>
							<strong>Season number</strong>
						</label>

						<div class="form-group form-group-feedback form-group-feedback-right">
							<input type="text" name="season_no" id="season_no" value="<?php echo $episode_data['season_no']; ?>" size="4" class="form-control form-required" />
							<div class="form-control-feedback">
								<a href="#" class="badge badge-light" id="latest-season-no-autofill" data-popup="popover" data-placement="top" data-trigger="hover" data-content="Autofill the season number">autofill</a>
							</div>
						</div>


					</div>

					<div class="col-md-3">
						<label class="control-label" for="">
							<strong>Episode number</strong>
						</label>
						<div class="form-group form-group-feedback form-group-feedback-right">
							<input type="text" name="episode_no" id="episode_no" value="<?php echo $episode_data['episode_no']; ?>" size="4" class="form-control form-required" />
							<div class="form-control-feedback">
								<a href="#" class="badge badge-light" id="latest-episode-no-autofill" data-popup="popover" data-placement="top" data-trigger="hover" data-content="Autofill with the next episode number">autofill</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Title and Description</h6>
				<div class="header-elements">
				</div>
			</div>
			<div class="card-body">
				<input name="video_title" type="text" class="form-control form-required permalink-make font-weight-semibold font-size-lg" placeholder="Episode title" value="<?php echo htmlspecialchars($episode_data['video_title']); ?>" />
				<div class="permalink-field mt-2 mb-2">
					<?php if (_SEOMOD) : ?>
						<strong>Permalink:</strong> <?php echo _URL .'/series/'; ?><span id="episode_permalink_series_slug"><?php echo $episode_data['series_data']['series_slug']; ?></span>/<input class="permalink-input" type="text" name="video_slug" value="<?php echo urldecode($episode_data['video_slug']);?>" />
						<input type="hidden" name="current_video_slug" value="<?php echo urldecode($episode_data['video_slug']);?>" />
					<?php endif; ?>
				</div>

				<div id="textarea-dropzone" class="upload-file-dropzone">
					<div class="fileinput-button">
						<input type="file" name="file" id="upload-file-wysiwyg-btn" class="file-input file-input-custom form-control form-control-sm alpha-grey" multiple="multiple" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload images" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" data-fouc />
					</div>
					<textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce"><?php echo $episode_data['description']; ?></textarea>
					<span class="autosave-message"></span>
				</div>					
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Episode Details</h6>
			</div>
				<?php
				if($episode_data['yt_length'] > 0) {
					$yt_minutes = intval($episode_data['yt_length'] / 60);
					$yt_seconds = intval($episode_data['yt_length'] % 60); 
				} else {
					$yt_minutes = 0;
					$yt_seconds = 0;
				}
				?>
				<ul class="nav nav-tabs nav-tabs-bottom">
					<li class="nav-item"><a href="#badge-tab0" class="nav-link active" data-toggle="tab">Tags</a></li>
					<li class="nav-item"><a href="#badge-tab1" class="nav-link" data-toggle="tab">Duration <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-yt_length"><strong><?php echo sec2min($episode_data['yt_length']);?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab2" class="nav-link" data-toggle="tab">Comments <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($episode_data['allow_comments'] == 1 || ($is_new_episode && $config['comment_system'] != 'off')) ? 'alpha-success text-success-600' : 'alpha-primary';?>" id="value-comments"><strong><?php echo ($episode_data['allow_comments'] == 1 || ($is_new_episode && $config['comment_system'] != 'off')) ? 'allowed' : 'closed';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab4" class="nav-link" data-toggle="tab">Featured <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($episode_data['featured'] == 1) ? 'alpha-success text-success-600' : 'alpha-primary';?>" id="value-featured"><strong><?php echo ($episode_data['featured'] == 1) ? 'yes' : 'no';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab5" class="nav-link" data-toggle="tab">Private <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-register"><strong><?php echo ($episode_data['restricted'] == 1) ? 'yes' : 'no';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab7" class="nav-link" data-toggle="tab">Released <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-publish"><strong><?php echo date("M d, Y", ($is_new_episode) ? time() : $episode_data['release_date']);?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab6" class="nav-link" data-toggle="tab">Views <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-views"><strong><?php echo number_format($episode_data['site_views']);?></strong></span></a></li>
				</ul>

				<div class="card-body pt-0">
					<div class="tab-content">
						<div class="tab-pane show active" id="badge-tab0">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Video Tags:</div>
							<div class="tagsinput bootstrap-tagsinput">
								<input type="text" id="tags_addvideo_1" name="tags" value="<?php echo $episode_data['tags_compact']; ?>" class="tags form-control tags-input" />
							</div>
						</div>
						<div class="tab-pane" id="badge-tab1">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Duration:</div>
							<div class="input-group input-group-sm custom-date-form">
								<input type="text" name="yt_min" id="yt_length" value="<?php echo $yt_minutes; ?>" size="4" class="form-control form-control-sm" />
									<span class="input-group-append">
									<span class="input-group-text">min.</span>
								</span>
								<input type="text" name="yt_sec" id="yt_length" value="<?php echo $yt_seconds; ?>" size="3" class="form-control form-control-sm" /> 
								<span class="input-group-append">
									<span class="input-group-text">sec.</span>
								</span>
							</div>

							<input type="hidden" name="yt_length" id="yt_length" value="<?php echo trim(($yt_minutes * 60) + $yt_seconds); ?>" />
						</div>

						<div class="tab-pane" id="badge-tab2">
								<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Comments:</div>
								<label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($episode_data['allow_comments'] == 1 || $is_new_episode) echo 'checked="checked"';?> /> Allow comments on this episode</label>
								<?php if ($config['comment_system'] == 'off') : ?>
								<div class="alert alert-info">
								Comments are disabled site-wide. 
								<br />
								To enable comments, visit the <a href="settings.php?view=comment" title="Settings page" target="_blank">Settings</a> page.
								</div>
								<?php endif;?>
						</div>

						<div class="tab-pane" id="badge-tab4">
								<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Mark this video as featured:</div>
								<label><input type="checkbox" name="featured" id="featured" value="1" <?php if($episode_data['featured'] == 1) echo 'checked="checked"';?> /> Yes, mark as featured</label>
						</div>

						<div class="tab-pane" id="badge-tab5">
								<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Require registration to watch video:</div>
								<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php if ($episode_data['restricted'] == 1) echo 'checked="checked"'; ?> /> Yes</label>
								<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php if ($episode_data['restricted'] == 0) echo 'checked="checked"'; ?> /> No</label>
						</div>

						<div class="tab-pane" id="badge-tab6">
								<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Views:</div>
								<input type="hidden" name="site_views" value="<?php echo $episode_data['site_views'];?>" />
								<input type="text" name="site_views_input" id="site_views_input" value="<?php echo $episode_data['site_views']; ?>" size="10" class="form-control col-md-3" />
						</div>

						<div class="tab-pane" id="badge-tab7">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Release date:</div>
								<?php echo show_form_item_date($episode_data['release_date'], false);?>
						</div>

						<div class="tab-pane" id="badge-tab8">
								<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Submitted by:</div>
								<input type="text" name="submitted" id="submitted" value="<?php echo htmlspecialchars(($is_new_episode) ? $userdata['username'] : $episode_data['submitted']); ?>" class="form-control col-md-3" />
								<span class="text-danger text-sm">Use only a valid username!</span>
						</div>


					</div>
				</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline header-toggles" data-target="#cardVideoSource" data-toggle="collapse" aria-expanded="true" aria-controls="cardVideoSource">
				<h6 class="card-title font-weight-semibold">Media Sources</h6>
				<div class="header-elements">
					<div class="list-icons">
						<a href="#" data-target="#cardVideoSource" data-toggle="collapse" aria-expanded="false" aria-controls="cardVideoSource" class="text-default"><i class="icon-arrow-up12"></i></a>
					</div>
				</div>
			</div>
			<div class="collapse---" id="cardVideoSource">
				<div class="card-body">
					<div class="upload-file-dropzone" id="episode-media-source-dropzone">
						<div  id="ms-sortable-list">
							<?php 
							if ( ! $is_new_episode && pm_count($episode_data['media_sources']) == 0)
							{
								echo pm_alert_warning("This episode doesn't have any media sources. Add a source now.");
							}

							foreach ($episode_data['media_sources'] as $ms_index => $media_source)
							{
								switch ($media_source['type'])
								{
									case 'file':
										?>
										<div class="form-group row media-source-container">
											<label class="col-lg-2 col-form-label">
												Uploaded file:
											</label>
											<div class="col-lg-8">
												<div class="input-group">
													
													<input class="form-control disabled" value="<?php echo _VIDEOS_DIR . $media_source['data']; ?>" readonly>
													<div class="input-group-append">
														<span class="input-group-text"><a href="<?php echo _VIDEOS_DIR . $media_source['data']; ?>" title="Download file" class="text-dark"><i class="icon-download"></i></a></span>
													</div>
												</div>
											</div>
											<div class="col-lg-2">
												<div class="btn-group float-right" role="group">
													<a href="#" class="btn btn-outline bg-warning text-warning-800 btn-icon ml-2 media-source-delete-btn" data-ms-id="<?php echo $media_source['id']; ?>" data-ms-type="<?php echo $media_source['type']; ?>"><i class="icon-bin"></i></a></span>
													<a href="#" class="btn btn-link media-source-drag-handle text-grey" data-popup="popover" title="" data-html="true" data-trigger="hover" data-content="Arrange media sources by the order in which you wish them to appear on your site."><i class="icon-arrow-resize8"></i></a></span>
												</div>
											</div>
											<input type="hidden" name="media_source[]" value="<?php echo $media_source['data']; ?>" />
											<input type="hidden" name="media_source_id[]" value="<?php echo $media_source['id']; ?>" />
											<input type="hidden" name="media_source_type[]" value="file" />
	                                        <input type="hidden" name="media_source_yt_id[]" value="<?php echo $media_source['yt_id']; ?>" />
										</div>
										<?php
									break;

									case 'embed code':
										?>
										<div class="form-group row media-source-container">
											<label class="col-lg-2 col-form-label">
												Embed code:
											</label>
											<div class="col-lg-8">
												<textarea name="media_source[]" rows="2" class="textarea-embed form-control"><?php echo $media_source['data']; ?></textarea>
												<span class="text-muted font-size-sm mt-1">Accepted HTML tags: <strong>&lt;iframe&gt;</strong>  <strong>&lt;embed&gt;</strong> <strong>&lt;object&gt;</strong> <strong>&lt;param&gt;</strong> and <strong>&lt;video&gt;</strong></span>
											</div>
											<div class="col-lg-2">
												<div class="btn-group float-right" role="group">
													<a href="#" class="btn btn-outline bg-warning text-warning-800 btn-icon ml-2 media-source-delete-btn" rel="tooltip" title="Remove media source" data-ms-id="<?php echo $media_source['id']; ?>" data-ms-type="<?php echo $media_source['type']; ?>"><i class="icon-bin"></i></a></span>
													<a href="#" class="btn btn-link media-source-drag-handle text-grey" data-popup="popover" title="" data-html="true" data-trigger="hover" data-content="Arrange media sources by the order in which you wish them to appear on your site."><i class="icon-arrow-resize8"></i></a>
												</div>
											</div>
											<input type="hidden" name="media_source_id[]" value="<?php echo $media_source['id']; ?>" />
											<input type="hidden" name="media_source_type[]" value="embed code" />
	                                        <input type="hidden" name="media_source_yt_id[]" value="<?php echo $media_source['yt_id']; ?>" />
										</div>
										<?php
									break;
									
									case 'url':
										?>
										<div class="form-group row media-source-container">
											<label class="col-lg-2 col-form-label">
												Video URL: <i class="mi-info-outline" data-popup="popover" data-trigger="hover" title="" data-html="true" data-content="Use only a <strong>direct link</strong> (e.g. https://host.com/media.mp4) or a URL from any supported 3rd party video website (e.g. <strong>YouTube, DailyMotion, Vimeo, etc.</strong>)"></i>
											</label>
											<div class="col-lg-8">
												<input type="text" class="form-control" name="media_source[]" value="<?php echo $media_source['data']; ?>" />
											</div>
											<div class="col-lg-2">
												<div class="btn-group float-right" role="group">
													<a href="#" class="btn btn-outline bg-warning text-warning-800 btn-icon ml-2 media-source-delete-btn" rel="popover" title="Remove media source" data-ms-id="<?php echo $media_source['id']; ?>" data-ms-type="<?php echo $media_source['type']; ?>"><i class="icon-bin"></i></a>												
													<a href="#" class="btn btn-link media-source-drag-handle text-grey" data-popup="popover" title="" data-html="true" data-trigger="hover" data-content="Arrange media sources by the order in which you wish them to appear on your site."><i class="icon-arrow-resize8"></i></a>
												</div>
											</div>
											<input type="hidden" name="media_source_id[]" value="<?php echo $media_source['id']; ?>" />
											<input type="hidden" name="media_source_type[]" value="url" />
											<input type="hidden" name="media_source_yt_id[]" value="<?php echo $media_source['yt_id']; ?>" />
										</div>
										<?php
									break; 
								}
							}
							?>
							<span id="append-media-source-input"></span>
						</div><!-- #ms-sortable-list -->
						
						<div class="d-inline-flex flex-wrap">
							<span class="fileinput-button">
								<input type="file" name="file" id="upload-episode-media-btn" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload" data-browse-class="btn btn-sm btn-primary font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" />
							</span>
							<a href="#" class="btn btn-sm btn-primary font-weight-semibold media-add-link ml-1 mr-1"><i class="icon-link"></i> Add link</a>
							<a href="#" class="btn btn-sm btn-primary font-weight-semibold media-add-embed-code"><i class="icon-embed2"></i> Add embed code</a>
						</div>
					</div>

				</div>
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline header-toggles" data-target="#cardCustomFields" data-toggle="collapse" aria-expanded="<?php echo get_admin_ui_prefs('custom_fields_panel'); ?>" aria-controls="cardCustomFields">
				<h6 class="card-title font-weight-semibold">Custom Fields</h6>
				<div class="header-elements">
					<div class="list-icons">
						<a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" rel="popover" data-trigger="hover" data-animation="true" data-content="Click here to learn more about the 'Custom Fields'" target="_blank" class="text-dark"><i class="mi-info-outline"></i></a>
						<a href="#" data-target="#cardCustomFields" data-toggle="collapse" aria-expanded="false" aria-controls="cardCustomFields" class="text-default collapsed"><i class="icon-arrow-up12"></i></a>
					</div>
				</div>
			</div>
			<div class="collapse" id="cardCustomFields">
				<div class="card-body">
					<div class="control-group">	
					<?php if (pm_count($episode_data['metadata']) > 0) : ?>
					<div class="row">
						<div class="col-md-3"><strong>Name</strong></div>
						<div class="col-md-9"><strong>Value</strong></div>
					</div>
					<?php	
						foreach ($episode_data['metadata'] as $meta_id => $meta) : 
								echo admin_custom_fields_row($meta_id, $meta);
						endforeach;
					endif; 
					?>
					</div>

					<?php echo admin_custom_fields_add_form($episode_data['metadata'], IS_EPISODE); ?>
				</div>
			</div>
		</div><!--.card-->

	</div><!--. col-md-9 main-->
	<div class="col-sm-12 col-md-3">
		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Thumbnail</h6>
				<div class="header-elements">
					<div class="list-icons">
						<span class="fileinput-button">
							<input type="file" name="file" id="upload-video-image-btn" class="file-input form-control form-control-sm alpha-grey" data-show-caption="false" data-show-upload="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Update" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" />
						</span>
					</div>
				</div>
			</div>
			<div class="card-body upload-file-dropzone" id="video-thumb-dropzone">
				<div id="video-thumb-container">
					<?php
					if (strpos($episode_data['yt_thumb'], 'http') !== 0 && strpos($episode_data['yt_thumb'], '//') !== 0 && $episode_data['yt_thumb'] != '')
					{
						$episode_data['yt_thumb'] = _THUMBS_DIR . $episode_data['yt_thumb'];
					}
					if ( empty($episode_data['yt_thumb']) ) : ?>
					<a href="#" id="show-thumb" data-toggle="collapse" data-target="#show-opt-thumb" rel="tooltip" title="Change the thumbnail URL"></a>

					<div class="d-block justify-content-end text-center rounded bt-slate alpha-slate" style="min-height: 150px;">
						<div>
							<i class="icon-image2 icon-3x text-default border-slate alpha-slate p-3 mt-1 mt-1"></i>
							<h5>No thumbnail</h5>
						</div>
					</div>

					<?php else : ?>
					<a href="#" id="show-thumb" data-toggle="collapse" data-target="#show-opt-thumb" rel="tooltip" title="Click here to change the thumbnail URL"><img src="<?php echo make_url_https($episode_data['yt_thumb']); ?>?cache_buster=<?php echo $time_now;?>" class="img-fluid" /></a>
					<?php endif; ?>
				</div>

				<div id="show-opt-thumb" class="collapse mt-1 p-3">
					<div class="input-group">
					<input type="text" name="yt_thumb" value="<?php echo $episode_data['yt_thumb']; ?>" class="form-control col-md-10" placeholder="http://" /> <span class="input-group-text bg-transparent border-0"><i class="mi-info-outline" rel="tooltip" data-position="top" title="Change this URL to replace the existing thumbnail."></i></span>
					<input type="hidden" name="yt_thumb_old" value="<?php echo $episode_data['yt_thumb']; ?>" class="form-control col-md-12" placeholder="http://" />
					</div>
				</div>
			</div>
		</div><!--.card-->

	</div><!--. col-md-3 sidebar-->
</div>

		

<div id="stack-controls-disabled" class="list-controls">
	<div class="float-right">
		<a href="episodes.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2 pm-show-loader">Cancel</a>
        <button id="edit-episode-form-submit-btn" type="submit" name="submit-form" value="Save" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2"><i class="mi-check"></i> Save</button>
	</div>
</div><!-- #list-controls -->

<input type="hidden" name="episode_id" value="<?php echo $episode_data['episode_id']; ?>" />
<input type="hidden" name="language" value="1" />
<input type="hidden" name="uniq_id" value="<?php echo $episode_data['uniq_id']; ?>" />
<input type="hidden" name="source_id" value="<?php echo $episode_data['source_id']; ?>" />
<input type="hidden" name="added_old" value="<?php echo $episode_data['added']; ?>" />
<input type="hidden" name="upload-type" value="" /> 
<input type="hidden" name="p" value="upload" />
<input type="hidden" name="do" value="upload-image" />
<input type="hidden" name="video_type" value="<?php echo IS_EPISODE; ?>" />
<input type="hidden" name="allow_embedding" value="<?php echo $episode_data['allow_embedding']; ?>" />
<?php echo csrfguard_form('_admin_edit_episode'. $episode_data['episode_id']); ?>
</form>


<!-- Media Sources templates -->
<script type="text/x-custom-template" id="embed-code-template">
<div class="form-group row media-source-container">
	<label class="col-lg-2 col-form-label">
		Embed code:
	</label>
	<div class="col-lg-8">
		<textarea name="media_source[]" rows="2" class="textarea-embed form-control" placeholder="Accepted HTML tags: <iframe> <embed> <object> <param> and <video>"></textarea>
		<span class="text-muted font-size-sm mt-1">Accepted HTML tags: <strong>&lt;iframe&gt;</strong>  <strong>&lt;embed&gt;</strong> <strong>&lt;object&gt;</strong> <strong>&lt;param&gt;</strong> and <strong>&lt;video&gt;</strong></span>
	</div>
	<div class="col-lg-2">
		<div class="btn-group float-right" role="group">
			<a href="#" class="btn btn-outline bg-warning text-warning-800 btn-icon ml-2 media-source-delete-btn" rel="tooltip" title="Remove media source" data-ms-id="{{media-source-id}}" data-ms-type="embed code"><i class="icon-bin"></i></a></span>
			<a href="#" class="btn btn-link media-source-drag-handle text-grey" data-popup="popover" data-html="true" data-trigger="hover" title="Arrange media sources by the order in which you wish them to appear on your site."><i class="icon-arrow-resize8"></i></a>
		</div>
	</div>
	<input type="hidden" name="media_source_id[]" value="{{media-source-id}}" />
	<input type="hidden" name="media_source_type[]" value="embed code" />
	<input type="hidden" name="media_source_yt_id[]" value="" />
</div>
</script>
<script type="text/x-custom-template" id="add-link-template">
<div class="form-group row media-source-container">
	<label class="col-lg-2 col-form-label">
		Video URL: 
	</label>
	<div class="col-lg-8">
		<input type="text" class="form-control" name="media_source[]" value="" placeholder="https://" />

		<span class="text-muted font-size-sm mt-1">
			Use only a <strong>direct link</strong> (e.g. https://host.com/media.mp4) or a URL from any supported websites (e.g. <strong>YouTube, Vimeo, etc.</strong>)
		</span>
	</div>
	<div class="col-lg-2">
		<div class="btn-group float-right" role="group">
			<a href="#" class="btn btn-outline bg-warning text-warning-800 btn-icon ml-2 media-source-delete-btn" rel="popover" title="Remove media source" data-ms-id="{{media-source-id}}" data-ms-type="url"><i class="icon-bin"></i></a>
			<a href="#" class="btn btn-link media-source-drag-handle text-grey" data-popup="popover" data-html="true" data-trigger="hover" title="Arrange media sources by the order in which you wish them to appear on your site."><i class="icon-arrow-resize8"></i></a>
		</div>
	</div>
	<input type="hidden" name="media_source_id[]" value="{{media-source-id}}" />
	<input type="hidden" name="media_source_type[]" value="url" />
	<input type="hidden" name="media_source_yt_id[]" value="" />
</div>
</script>
<script type="text/x-custom-template" id="uploaded-episode-file-template">
<div class="form-group row media-source-container">
	<label class="col-lg-2 col-form-label">
		Uploaded file:
	</label>
	<div class="col-lg-8">
		<div class="input-group">
			
			<input class="form-control disabled" value="{{uploaded-file-download-url}}" readonly>
			<div class="input-group-append">
				<span class="input-group-text"><a href="{{uploaded-file-download-url}}" title="Download file" class="text-dark"><i class="icon-download"></i></a></span>
			</div>
		</div>
	</div>
	<div class="col-lg-2">
		<div class="btn-group float-right" role="group">
			<a href="#" class="btn btn-outline bg-warning text-warning-800 btn-icon ml-2 media-source-delete-btn" data-ms-id="{{media-source-id}}" data-ms-data="{{uploaded-file-name}}"><i class="icon-bin"></i></a></span>
			<a href="#" class="btn btn-link media-source-drag-handle text-grey" data-popup="popover" data-html="true" data-trigger="hover" title="Arrange media sources by the order in which you wish them to appear on your site."><i class="icon-arrow-resize8"></i></a>
		</div>
	</div>
<input type="hidden" name="media_source_id[]" value="{{media-source-id}}" />
<input type="hidden" name="media_source[]" value="{{uploaded-file-name}}" />
<input type="hidden" name="media_source_type[]" value="file" />
<input type="hidden" name="media_source_yt_id[]" value="" />
</div>
</script>
<script type="text/javascript">
var is_new_episode = <?php echo ($is_new_episode) ? 'true' : 'false'; ?>;
var episode_data = <?php echo json_encode($episode_data); ?>;
var series_data = <?php echo json_encode($all_series); ?>;

function bind_remove_media_source_btn() {
	
	$('.media-source-cancel-btn').off('click').on('click', function(e){
		e.preventDefault();
		$(this).closest('.media-source-container').css('border-bottom', '5px solid #f4543c').slideUp('normal', function(){
			$(this).closest('.media-source-container').remove()
		});

		return false;
	});
	$('.media-source-delete-btn').off('click').on('click', function(e){
		e.preventDefault();
		var this_btn = $(this);
		$.ajax({
			url: phpmelody.admin_ajax_url,
			data: {
				"p": "episodes",
				"do": "delete-media-source-file",
				"ms_id": $(this).attr('data-ms-id'),
				"ms_type": $(this).attr('data-ms-type'),
				"ms_data": $(this).attr('data-ms-data'),
				"episode_id": episode_data.episode_id,
				"_pmnonce": '_admin_edit_episode'+ episode_data.episode_id,
				"_pmnonce_t": $('#_pmnonce_t_admin_edit_episode'+ episode_data.episode_id).val()
			},
			type: 'POST',
			dataType: 'json',
			beforeSend: function() {
				$.notifyClose();
			}
		}).done(function(data) { 
			if (data.success == false) {
				$.notify({message: data.msg}, {type: data.alert_type});
			} else {
				this_btn.closest('.media-source-container').css('border-bottom', '5px solid #f4543c').slideUp('normal', function(){
					this_btn.closest('.media-source-container').remove()
					if (this_btn.attr('data-ms-id')) {
						$.each(episode_data.media_sources, function(index, ms){
							if (this_btn.attr('data-ms-id') == ms.id) {
								// episode_data.media_sources.splice(index, 1);
							}
						});
					}
				});
				
				$.notify({message: "Media source removed. Remember to save your changes."}, {type: 'success'});
			}

			if (data._pmnonce) {
				$('#_pmnonce_t_admin_edit_episode'+ episode_data.episode_id).val(data._pmnonce_t);
			}

			return false;
		});
	});
}

$(document).ready(function(){
	bind_remove_media_source_btn();
	
	$('#ms-sortable-list').sortable({
		handle: '.media-source-drag-handle',
		items: '> .media-source-container',
		// cancel: "input,textarea,button,select,option",
		revert: false,
		placeholder: 'transport',
		forcePlaceholderSize: true,
		opacity: .6,
		update: function(){
			phpmelody.prevent_leaving_without_saving = true;
		}
	});

	/*
	 * Add {media source} buttons
	 */
	$('.media-add-embed-code').click(function(e){
		e.preventDefault();

		var html_tpl = $('#embed-code-template').html();
		$(pm_replace_all(html_tpl, {
			'{{media-source-id}}': pm_generate_random_string(32)
		})).insertBefore($('#append-media-source-input'));
		bind_remove_media_source_btn();
		return false;
	});
	$('.media-add-link').click(function(e){
		e.preventDefault();
		
		var html_tpl = $('#add-link-template').html();
		$(pm_replace_all(html_tpl, {
			'{{media-source-id}}': pm_generate_random_string(32)
		})).insertBefore($('#append-media-source-input'));
		bind_remove_media_source_btn();
		return false;
	});
	
	// visually update series on change
	$('select[name="series_id"]').change(function(){
		var select_val = $(this).val();
		$.each(series_data, function(index, series){
			if (series['series_id'] == select_val) {
				$('#episode-series-img').attr('src', series['image_url']);
				$('#episode-series-title').html(series['title']); 
				$('#episode_permalink_series_slug').text(series['series_slug']);

				$('#latest-season-no-autofill')
					.html('Latest season: '+ (parseInt(series['seasons'])))
					.on('click', function(e){
						$('input[name="season_no"]').val(series['seasons']);
						return false;
				});
				$('#latest-episode-no-autofill')
					.html('Next episode: '+ (parseInt(series['episodes']) + 1))
					.on('click', function(e){
						$('input[name="episode_no"]').val((parseInt(series['episodes']) + 1));
						return false;
				});
			}
		});

		$('#latest-episode-no-autofill').css({"display":"inline-block"});
		$('#latest-season-no-autofill').css({"display":"inline-block"});
	});

	if (is_new_episode && window.location.href.indexOf('series_id') > 0) {
		$('select[name="series_id"]').trigger("change"); // autofill season and episode counters
	}

	/*
	 * Episode media source file uploader
	 */
	$('#upload-episode-media-btn').fileupload(
		$.extend({}, pm_fileupload_single_options, {
			dropZone: $('#episode-media-source-dropzone')
		})
	)
	.bind('fileuploadadd', function (e, data) {
		
		$('input[name="upload-type"]').val('episode');
		$('input[name="do"]').val('upload-file');
		
		pm_fileupload_add(e, data);
		
		if (data.files.length > 0) {
			data.submit();
		}
	})
	.bind('fileuploadsubmit', function (e, data) {
		pm_fileupload_submit(e, data);
	})
	.bind('fileuploadstart', function (e, data) {
		pm_fileupload_start(e, data);
	})
	.bind('fileuploadprogress', function (e, data) {
		pm_fileupload_progress(e, data);
	})
	.bind('fileuploadprogressall', function (e, data) {
		pm_fileupload_progress_all(e, data);
	})
	.bind('fileuploaddone', function (e, data) { 
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);
			var html_tpl = $('#uploaded-episode-file-template').html();
			
			if (server_response.success == true) {
				$(pm_replace_all(html_tpl, {
					'{{media-source-id}}': server_response.ms_id,
					'{{uploaded-file-path}}': server_response.filepath,
					'{{uploaded-file-download-url}}': server_response.fileurl,
					'{{uploaded-file-name}}': server_response.filename
				})).insertBefore($('#append-media-source-input'));
			}
			bind_remove_media_source_btn();
		});
		
		pm_fileupload_done(e, data);
	})
	.bind('fileuploadfail', function (e, data) {
		pm_fileupload_fail(e, data);
	});
});
</script>

</div><!-- .content -->
<?php
include('footer.php');