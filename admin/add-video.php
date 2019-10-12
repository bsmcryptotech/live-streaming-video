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
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
if(isset($_GET['step'])) {
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_uniform = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$load_fileinput_upload = 1;
}


$submission_type = (isset($_GET['mode']) && $_GET['mode'] != '') ? $_GET['mode'] : 'url';

$_page_title = ($submission_type == 'upload') ? 'Upload Video' : 'Add Video';

include('header.php');

define('PHPMELODY', true);

$message = '';
$allowed_ext = array('.flv', '.mp4', '.mov', '.wmv', '.divx', '.avi', '.mkv', '.asf', '.wma', '.mp3', '.m4v', '.m4a', '.3gp', '.3g2');

$step = (int) $_GET['step'];
if($step == '')
	$step = 1;


if($step == 2 && isset($_POST['Submit']))
{
	if(trim($_POST['url']) == '')
	{
		$step = 1;
		$message = pm_alert_error('Please provide a valid URL.');	
	}
}


function add_video_form($video_details = array())
{
	global $modframework;
	$categories_dropdown_options = array(
									'attr_name' => 'category[]',
									'attr_id' => 'main_select_category',
									'select_all_option' => false,
									'spacer' => '&mdash;',
									'selected' => 0,
									'other_attr' => 'multiple="multiple"'
									);

	if ($video_details['url_flv'] == '') 
	{
		$video_lookup = pm_alert_warning('<strong>Sorry, no video was found at this location.</strong> Please try again or use another URL.');
	}
	
	if ($video_details['video_title'] != '')
	{
		$video_details['video_slug'] = sanitize_title($video_details['video_title']);
	}
	
	// Generate a video title from the file name
	if(isset($_GET['filename']) && $_GET['filename'] != '')
	{
		$_GET['filename'] = urldecode($_GET['filename']);
		
		$uploaded_file = pathinfo($_GET['filename']);
		$uploaded_file_name =  basename($_GET['filename'],'.'.$uploaded_file['extension']);
		$unwanted_chars = array("-", "_", ",","'",".","(",")","[","]","*","{","}","  ","   ");
		$video_details['video_title'] = ucwords(str_replace($unwanted_chars, " ", $uploaded_file_name));
	}
?>
<form method="post" enctype="multipart/form-data" action="add-video.php?step=3" name="addvideo_form_step2" id="addvideo_form_step2" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted) or make sure the URL you entered at STEP 1 is valid.')">
<div class="row">
	<div class="col-sm-12 col-md-9">
		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Title and Description</h6>
				<div class="header-elements">
					<div class="list-icons">
					</div>
				</div>
			</div>
			<div class="card-body">
				<?php echo $video_lookup; ?>
				<input name="video_title" type="text" class="form-control form-required font-weight-semibold font-size-lg" value="<?php echo str_replace('"', '&quot;', $video_details['video_title']); ?>" />
				<div class="permalink-field mt-2 mb-2">
					<?php if (_SEOMOD) : ?>
					<strong>Permalink:</strong> <?php echo _URL .'/';?><input class="permalink-input" type="text" name="video_slug" value="<?php echo urldecode($video_details['video_slug']);?>" value="<?php echo urldecode($video_details['video_slug']);?>" /><?php echo '_UniqueID.html';?>
					<?php endif; ?>	
				</div>

				<div id="textarea-dropzone" class="upload-file-dropzone">

					<div class="fileinput-button">
						<input type="file" name="file" id="upload-file-wysiwyg-btn" class="file-input file-input-custom form-control form-control-sm alpha-grey" multiple="multiple" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload images" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" data-fouc />
					</div>

					<textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce"><?php echo nl2br($video_details['description']); ?></textarea>
					<span class="autosave-message"></span>
				</div>					
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Video Details</h6>
			</div>
				<?php
				if($video_details['yt_length'] > 0) {	
					$yt_minutes = intval($video_details['yt_length'] / 60);
					$yt_seconds = intval($video_details['yt_length'] % 60); 
				} else {
					$yt_minutes = 0;
					$yt_seconds = 0;
				}
				?>
				<ul class="nav nav-tabs nav-tabs-bottom">
					
					<li class="nav-item"><a href="#badge-tab0" class="nav-link active" data-toggle="tab">Tags</a></li>
					<li class="nav-item"><a href="#badge-tab1" class="nav-link" data-toggle="tab">Duration <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-yt_length"><strong><?php echo $yt_minutes; ?> min. <?php echo $yt_seconds; ?> sec.</strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab2" class="nav-link" data-toggle="tab">Comments <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($video_details['allow_comments'] == 1) ? 'alpha-success text-success-600' : 'alpha-primary';?>" id="value-comments"><strong><?php echo ($video_details['allow_comments'] == 1) ? 'allowed' : 'closed';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab3" class="nav-link" data-toggle="tab">Embedding <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($video_details['allow_embedding'] == 1) ? 'alpha-success text-success-600' : 'alpha-primary';?>" id="value-embedding"><strong><?php echo ($video_details['allow_embedding'] == 1) ? 'allowed' : 'disallowed';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab4" class="nav-link" data-toggle="tab">Featured <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($video_details['featured'] == 1) ? 'alpha-success text-success-600' : 'alpha-primary';?>" id="value-featured"><strong><?php echo ($video_details['featured'] == 1) ? 'yes' : 'no';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab5" class="nav-link" data-toggle="tab">Private <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-register"><strong><?php echo ($video_details['restricted'] == 1) ? 'yes' : 'no';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab7" class="nav-link" data-toggle="tab">Publish <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-publish"><strong>now</strong></span></a></li>

					<li class="nav-item dropdown">
						<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Other</a>
						<div class="dropdown-menu dropdown-menu-right">
							<a href="#badge-tab6" class="dropdown-item" data-toggle="tab">Views </span></a>
							<a href="#badge-tab8" class="dropdown-item" data-toggle="tab">Submitted by</strong></span></a>
						</div>
					</li>

				</ul>

			<div class="card-body pt-0">
				<div class="tab-content">
					<div class="tab-pane show active" id="badge-tab0">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Video Tags:</div>
						<div class="tagsinput bootstrap-tagsinput">
							<input type="text" id="tags_addvideo_1" name="tags" value="<?php echo $video_details['tags']; ?>" class="tags form-control tags-input" />
						</div>
					</div>
					<div class="tab-pane" id="badge-tab1">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Video Duration:</div>
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
							<label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($video_details['allow_comments'] == 1) echo 'checked="checked"';?> /> Allow comments on this video</label>
							<?php if ($config['comment_system'] == 'off') : ?>
							<div class="alert alert-info">
							Comments are disabled site-wide. 
							<br />
							To enable comments, visit the <a href="settings.php?view=comment" title="Settings page" target="_blank">Settings</a> page.
							</div>
							<?php endif;?>
					</div>

					<div class="tab-pane" id="badge-tab3">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Video Embedding:</div>
							<label><input name="allow_embedding" id="allow_embedding" type="checkbox" value="1" <?php if ($video_details['allow_embedding'] == 1) echo 'checked="checked"';?> /> Allow embedding to 3rd party sites</label>
							<?php if ($config['allow_embedding'] == '0') : ?>
							<div class="alert alert-info">
							Embedding is disabled site-wide. 
							<br />
							To enable embedding, visit the <a href="settings.php?view=video" title="Settings page" target="_blank">Settings</a> page.
							</div>
							<?php endif;?>
					</div>

					<div class="tab-pane" id="badge-tab4">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Mark this video as featured:</div>
							<label><input type="checkbox" name="featured" id="featured" value="1" <?php if($video_details['featured'] == 1) echo 'checked="checked"';?> /> Yes, mark as featured</label>
					</div>

					<div class="tab-pane" id="badge-tab5">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Require registration to watch video:</div>
							<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php if ($video_details['restricted'] == 1) echo 'checked="checked"'; ?> /> Yes</label>
							<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php if ($video_details['restricted'] == 0) echo 'checked="checked"'; ?> /> No</label>
					</div>

					<div class="tab-pane" id="badge-tab6">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Views:</div>
							<input type="hidden" name="site_views" value="<?php echo $video_details['site_views'];?>" />
							<input type="text" name="site_views_input" id="site_views_input" value="<?php echo $video_details['site_views']; ?>" size="10" class="form-control col-md-3" />
					</div>

					<div class="tab-pane" id="badge-tab7">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Publish date:</div>
						<?php echo ($_POST['date_month'] != '') ? show_form_item_date( pm_mktime($_POST) ) : show_form_item_date();	?>
						<div class="text-muted mt-2">You can schedule videos to be available at a future date.</div>
					</div>

					<div class="tab-pane" id="badge-tab8">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Submitted by:</div>
							<input type="text" name="submitted" id="submitted" value="<?php echo htmlspecialchars($video_details['submitted']); ?>" class="form-control col-md-3" />
							<!-- <span class="text-danger text-sm">Use only a valid username!</span> -->
					</div>

				</div>
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline header-toggles" data-target="#cardVideoSource" data-toggle="collapse" aria-expanded="false" aria-controls="cardVideoSource">
				<h6 class="card-title font-weight-semibold">Video Source</h6>
				<div class="header-elements">
					<div class="list-icons">
						<a href="#" data-target="#cardVideoSource" data-toggle="collapse" aria-expanded="false" aria-controls="cardVideoSource" class="text-default collapsed"><i class="icon-arrow-up12"></i></a>
					</div>
				</div>
			</div>
			<div class="collapse" id="cardVideoSource">
				<div class="card-body">

				<?php if ($video_details['source_id'] == 1) : ?>
				<div class="upload-file-dropzone" id="video-file-dropzone">
					<div class="float-right">
						<span class="fileinput-button">
							<input type="file" name="file" id="upload-video-source-btn" class="file-input form-control form-control-sm alpha-grey" data-show-caption="false" data-show-upload="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload &amp; Replace" data-browse-class="btn btn-primary btn-sm font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" />
						</span>
					</div>
					<span class="text-uppercase font-weight-semibold">Local file:</span>
					<a href="<?php echo _VIDEOS_DIR . $video_details['url_flv']; ?>" target="_blank"><?php echo $video_details['url_flv']; ?></a>
				</div>
				<?php endif; ?>
				

				<?php if ($video_details['source_id'] != 1) { ?>
				<legend class="font-weight-semibold text-uppercase font-size-sm">Video URLS</legend>

				<?php
				if ($video_details['source_id'] == 0 && is_array($video_details['jw_flashvars'])) :
					$pieces = explode(';', $video_details['url_flv'], 2);
				?>
				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						File location (URL):
						<i class="mi-info-outline" data-popup="popover" data-trigger="hover" title="" data-html="true" data-content="Internal URL of video or audio file you want to stream.<br />This is the equivalent of JW Player's <code><i>file</i></code> flashvar. "></i>
						</label>
					<div class="col-lg-9">
						<input name="jw_file" type="text" class="form-control form-required" placeholder="http://" value="<?php echo $pieces[0]; ?>" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Streamer:
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="Location of an RTMP or HTTP server instance to use for streaming."></i> 
						</label>
					<div class="col-lg-9">
						<input name="jw_streamer" type="text" class="form-control form-required" value="<?php echo $pieces[1]; ?>" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Provider (<small>Optional</small>):
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="RTMP or HTTP"></i>
						</label>
					<div class="col-lg-9">
						<select name="jw_provider" class="custom-select col-md-3">
							<option value=''></option>
							<option value="rtmp" <?php echo ($video_details['jw_flashvars']['provider'] == 'rtmp') ? 'selected="selected"' : '';?>>RTMP</option>
							<option value="http" <?php echo ($video_details['jw_flashvars']['provider'] == 'http') ? 'selected="selected"' : '';?>>HTTP</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Load Balancing (<small>Optional</small>):
						<i class="mi-info-outline" rel="popover" data-html="true" data-trigger="hover" title="" data-content="This is the equivalent of JW Player's <code><i>rtmp.loadbalance</i></code> flashvar."></i>
						</label>
					<div class="col-lg-9">
						<label><input class="checkbox inline" type="radio" name="jw_rtmp_loadbalance" value="true" <?php echo ($video_details['jw_flashvars']['loadbalance'] == 'true') ? 'checked="checked"' : '';?> /> On</label> 
						<label><input class="checkbox inline" type="radio" name="jw_rtmp_loadbalance" value="" <?php echo ($video_details['jw_flashvars']['loadbalance'] != 'true') ? 'checked="checked"' : '';?> /> Off</label>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Subscribe (<small>Optional</small>):
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="This is the equivalent of JW Player's <code>rtmp.subscribe</code> flashvar."></i>
						</label>
					<div class="col-lg-9">
						<label><input class="checkbox inline" type="radio" name="jw_rtmp_subscribe" value="true" <?php echo ($video_details['jw_flashvars']['subscribe'] == 'true') ? 'checked="checked"' : '';?> /> Yes</label> 
						<label><input class="checkbox inline" type="radio" name="jw_rtmp_subscribe" value="" <?php echo ($video_details['jw_flashvars']['subscribe'] != 'true') ? 'checked="checked"' : '';?> /> No</label>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Secure Token (<small>Optional</small>):
						<i class="mi-info-outline" rel="popover" data-html="true" data-trigger="hover" data-animation="true" title="" data-content="Some service providers (e.g Wowza Media Server) have a feature called Secure Token that is used to protect your streams from downloading.<br />This <code>securetoken</code> parameter is optional and might not be compatible with all RTMP Service providers."></i>
						</label>
					<div class="col-lg-9">
						<input type="text" name="jw_securetoken" value="<?php echo $video_details['jw_flashvars']['securetoken'] ;?>" size="20" class="form-control" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Startparam (<small>Optional</small>):
						<i class="mi-info-outline" rel="popover" data-html="true" data-trigger="hover" data-animation="true" title="" data-content="This is the equivalent of JW Player's <code><i>rtmp.startparam</i></code> flashvar."></i>
						</label>
					<div class="col-lg-9">
						<input type="text" name="jw_startparam" value="<?php echo $video_details['jw_flashvars']['startparam'];?>" size="20" class="form-control" />
					</div>
				</div>

				<?php else: ?> <!--Local video sources-->

				<?php if ($video_details['source_id'] != 1 && $video_details['source_id'] != 2) : ?>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Original Video URL:
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" title="Warning" data-content="Changing this URL will re-import the video. All other data (title, tags, description, etc.) will remain the same."></i>
						</label>
					<div class="col-lg-9">
						<input type="text" name="direct" class="form-control" value="<?php echo $video_details['direct']; ?>" />
						<input type="hidden" name="direct-original" value="<?php echo $video_details['direct']; ?>" placeholder="http://"  />
					</div>
				</div>

				<?php endif; ?>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						File Location:
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" title="Warning" data-content="Changing the FLV/MOV/WMV/MP4 location of this video may cause it to stop working!"></i>
						</label>
					<div class="col-lg-9">
						<input type="text" name="url_flv" value="<?php echo $video_details['url_flv']; ?>" class="form-control" />
						<input type="hidden" name="url_flv-original" value="<?php echo $video_details['url_flv']; ?>" placeholder="http://" />
					</div>
				</div>

				<?php endif; ?>

				<?php } ?> <!--External video sources-->

				</div>
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline header-toggles" data-target="#cardCustomFields" data-toggle="collapse" aria-expanded="false" aria-controls="cardCustomFields">
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
						<?php if (pm_count($meta_data) > 0) : ?>
						<div class="row">
							<div class="col-md-3"><strong>Name</strong></div>
							<div class="col-md-9"><strong>Value</strong></div>
						</div>
						<?php foreach ($_POST['meta'] as $meta_id => $meta) : 
									$meta['meta_key'] = $meta['key'];
									$meta['meta_value'] = $meta['value'];
									
									echo admin_custom_fields_row($meta_id, $meta);
								endforeach;
							endif; ?>
						</div>

						<?php echo admin_custom_fields_add_form(0, IS_VIDEO); ?>
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
					if (($video_details['source_id'] == 0 || $video_details['source_id'] == 1 || $video_details['source_id'] == 2) && strpos($video_details['yt_thumb'], 'http') === false && $video_details['yt_thumb'] != '') 
					{
					$video_details['yt_thumb'] = _THUMBS_DIR . $video_details['yt_thumb'];
					}
					if (empty($video_details['yt_thumb']) && empty($video_details['yt_thumb_local'])) : ?>

					<a href="#" id="show-thumb" data-toggle="collapse" data-target="#show-opt-thumb" rel="tooltip" title="Change the thumbnail URL"></a>

					<div class="d-block justify-content-end text-center rounded bt-slate alpha-slate" style="min-height: 150px;">
						<div>
							<i class="icon-image2 icon-3x text-default border-slate alpha-slate p-3 mt-1 mt-1"></i>
							<h5>No thumbnail</h5>
						</div>
					</div>

					<?php else : ?>
					<a href="#" id="show-thumb" data-toggle="collapse" data-target="#show-opt-thumb" rel="tooltip" title="Click here to change the thumbnail URL"><img src="<?php echo make_url_https($video_details['yt_thumb']); ?>?cache_buster=<?php echo $time_now;?>" class="img-fluid" /></a>
					<?php endif; ?>
				</div>

				<div id="show-opt-thumb" class="collapse mt-3 p-3">
					<div class="input-group">
					<input type="text" name="yt_thumb" value="<?php echo $video_details['yt_thumb']; ?>" class="form-control col-md-10" placeholder="http://" /> <span class="input-group-text bg-transparent border-0"><i class="mi-info-outline" rel="tooltip" data-position="top" title="The thumbnail will refresh after you save the form."></i></span>
					</div>
				</div>
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Category</h6>
				<div class="header-elements">
					<div class="list-icons">
						<a href="#" id="inline_add_new_category" class="btn btn-sm btn-link text-default text-uppercase font-weight-semibold" data-popup="tooltip" data-html="true" data-original-title="Create a new category" /><i class="mi-control-point"></i> Add</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div id="inline_add_new_category_form" class="collapse border-grey border-bottom pb-3 mb-3">
					<span id="add_category_response"></span>

					<input name="add_category_name" type="text" placeholder="Category name" id="add_category_name" class="form-control mb-1" />
					<input name="add_category_slug" type="text" placeholder="Slug" class="form-control mb-1" data-popup="tooltip" data-html="true" data-original-title="Slugs are used in the URL (e.g. http://example.com/category/<strong>slug</strong>/) and can only contain numbers, letters, dashes and underscores." />
					<label class="text-muted">Create in (<em>optional</em>)</label>
					<?php 
						$categories_dropdown_options = array(
												'first_option_text' => '&ndash; Parent Category &ndash;', 
												'first_option_value' => '-1',
												'attr_name' => 'add_category_parent_id',
												'attr_id' => '',
												'attr_class' => 'custom-select mb-1',
												'select_all_option' => true,
												'spacer' => '&mdash;'
												);
						echo categories_dropdown($categories_dropdown_options); 
					?>
					<button name="add_category_submit_btn" value="Add category" class="btn btn-sm btn-success" />Create Category</button>
					<input type="hidden" name="categories_old" class="form-control" value="<?php echo $video_details['category'];?>"  />
				</div>
					<?php 
					$categories_dropdown_options = array(
													'attr_name' => 'category[]',
													'attr_id' => 'main_select_category',
													'attr_class' => 'category_dropdown custom-select mb-1',
													'select_all_option' => false,
													'spacer' => '&mdash;',
													'selected' => explode(',', $video_details['category']),
													'other_attr' => 'multiple="multiple"'
													);
					echo categories_dropdown($categories_dropdown_options);
					?>
			</div>
		</div><!--.card-->
		<div class="card">
		<div class="card-header bg-white header-elements-inline header-toggles" data-target="#cardSubtitles" data-toggle="collapse" aria-expanded="false" aria-controls="cardSubtitles">
				<h6 class="card-title font-weight-semibold">Video Subtitles</h6>
				<div class="header-elements">
					<div class="list-icons">
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" title="Subtitles" data-content="Select the language you intend to assign a subtitle file for and then click the 'Upload' button. You can also replace or delete existing subtitles in the same manner. If you don't see the 'Delete' link for a subtitle, simply refresh this page."></i>
						<a href="#" data-target="#cardSubtitles" data-toggle="collapse" aria-expanded="false" aria-controls="cardSubtitles" class="text-default collapsed"><i class="icon-arrow-up12"></i></a>
					</div>
				</div>
			</div>
			<div class="collapse" id="cardSubtitles">
				<div class="card-body">
					<div class="upload-file-dropzone btn-hide-upload" id="subtitle-dropzone">
						<select name="language" id="language" class="custom-select mb-2">
							<option value="">- Choose language -</option>
							<?php
							$languages = a_get_languages();
							foreach($languages as $tag => $label)
							{
								echo '<option value="'. $tag .'">'. $label .'</option>';
							}
							?>
						</select>

						<span class="fileinput-button">
							<input type="file" name="file" id="upload-subtitle-btn" class="file-input form-control form-control-lg alpha-grey" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Select file" data-show-remove="false" data-show-preview="false" />
						</span>
					</div>
				</div>
				<div class="card-footer bg-white">
					<ul class="list-unstyled" id="showSubtitle">
					</ul>
				</div>
			</div>
		</div><!--.card-->

	</div><!--. col-md-3 sidebar-->
</div><!--.row-->


<input type="hidden" name="language" value="1" />
<input type="hidden" name="yt_id" value="<?php echo $video_details['yt_id']; ?>" />
<input type="hidden" name="url_flv" value="<?php echo $video_details['url_flv']; ?>" />
<input type="hidden" name="source_id" value="<?php echo $video_details['source_id']; ?>" />
<input type="hidden" name="submitted_user_id" value="<?php echo $video_details['submitted_user_id']; ?>" />
<input type="hidden" name="submitted" value="<?php echo $video_details['submitted']; ?>" />
<input type="hidden" name="mp4" value="<?php echo $video_details['mp4']; ?>" />
<input type="hidden" name="direct" value="<?php echo $video_details['direct']; ?>" />
<input type="hidden" name="age_verification" value="0" />
<input type="hidden" name="uniq_id" value="<?php echo $video_details['uniq_id']; ?>" />
<input type="hidden" name="upload-type" value="" />
<input type="hidden" name="p" value="upload" /> 
<input type="hidden" name="do" value="upload-image" />
<input type="hidden" name="video_type" value="<?php echo IS_VIDEO; ?>" />

<div id="stack-controls-disabled" class="list-controls">
	<div class="float-right">
	<button type="submit" name="submit" value="Submit" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2"><i class="mi-check"></i> Add video</button>
	</div>
</div><!-- #list-controls -->

<?php
if($video_details['yt_id'] == '') 
	$video_details['yt_id'] = generate_activation_key(9); 
?>
</form>
<?php
} // add_video_form()


$video_details = array(	'uniq_id' => '',
						'video_title' => '',
						'description' => '',
						'yt_id' => '',
						'yt_length' => '',
						'category' => '',
						'submitted' => '',
						'source_id' => '',
						'language' => '',
						'age_verification' => '',
						'url_flv' => '',
						'yt_thumb' => '',
						'yt_thumb_local' => '',
						'mp4' => '',
						'direct' => '',
						'tags' => '', 
						'featured' => 0,
						'added' => '',
						'restricted' => 0, 
						'allow_comments' => 1,
						'allow_embedding' => 1
						);

?>

<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper page-header-edit">
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo ((isset($_GET['filename']) && $_GET['filename'] != '') || $_POST['source_id'] == 1) ? 'Upload Video' : 'Add Video'; ?></span></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<?php if ($step == 2) : ?>
				<div class="">
					<a href="add-video.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2 pm-show-loader">Cancel</a>
					<button type="submit" name="submit" value="Submit" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.forms[0].submit({return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')});" form="addvideo_form_step2"><i class="mi-check"></i> Add video</button>
				</div>
				<?php endif; ?>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="videos.php" class="breadcrumb-item">Videos</a>
					<a href="add-video.php<?php echo ($submission_type == 'upload' || $_GET['filename'] != '' || $_POST['filename']) ? '?mode=upload' : ''; ?>" class="breadcrumb-item"><span class="breadcrumb-item active"><?php echo ($submission_type == 'upload' || $_GET['filename'] != '') ? 'Upload Video' : 'Add Video'; ?></span></a>
				</div>


			</div>

			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
					<a href="#" id="show-help-assist" class="breadcrumb-elements-item"><i class="mi-help-outline text-muted"></i></a>
				</div>
			</div>
		</div>
	</div><!--.page-header -->
</div><!--.page-header-wrapper-->	
<div class="page-help-panel" id="help-assist"> 
		<div class="row">
			<div class="col-2 help-panel-nav">
				<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<a class="nav-link active" id="v-pills-tab-help-one" data-toggle="pill" href="#v-pills-one" role="tab" aria-controls="v-pills-one" aria-selected="true" data-toggle="tab">Overview</a>
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">Adding self-hosted video</a>
					<a class="nav-link" id="v-pills-tab-help-three" data-toggle="pill" href="#v-pills-three" role="tab" aria-controls="v-pills-three" aria-selected="false" data-toggle="tab">Adding a remote video</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
					<p>This page makes adding videos from remote or even local sources as easy as copy/pasting a URL.</p>
					<p>The allowed URLs are either your self-hosted videos (*.flv, *.mp4, *.wmv, *.mov, etc.) or videos hosted by remote services (Youtube, Vimeo, DailyMotion, etc.)</p>
					<p>You can also use the "ADD VIDEO" button located in the header to quickly paste a video URL.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
					<p>If you decide to self-host videos, you can use a 3rd party service such as AWS S3 or even your own hosting provider. The form below allows you to add self-hosted videos from any location. Just paste the URL to your *.flv, *.mp4, *.wmv, *.mov video below.</p>
					</div>
					<div class="tab-pane" id="v-pills-three" role="tabpanel" aria-labelledby="v-pills-tab-help-three">
						<p>Remote videos are hosted by 3rd party video sites. Below is a list of supported sites:</p>
						<ul style="height:200px; overflow-y: scroll; margin:3px 0;padding: 3px; color:#666; border: 1px solid #e2d59c; box-shadow: inset 0 1px 2px #ccc;" class="rounded">
						<?php
						$sources = a_fetch_video_sources();
						$sources = array_reverse($sources);
						$sources = array_sort($sources, 'source_name', SORT_ASC);
						$counter = 1;

						foreach ($sources as $k => $src)
						{
						if (is_int($k) && $k >= 2): 
						?>
						<li><?php echo $counter.'. '. ucfirst($src['source_name']);?> <small>(e.g. <?php echo $src['url_example'];?>)</small></li>
						<?php 
						$counter++;
						endif;
						}
						?>
						</ul>
						<p></p>
						<p>After pasting the desired  URL below, PHP Melody will automatically retrieve as much data as possible from the remote location. This includes, thumbnails, video title, description and so on. On some occasions you will have to add such data manually.</p>
						<p>Please note that no video files will be downloaded to your domain in this process.</p>
						<p>Learn how to use the <strong>custom fields</strong>: <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank">http://help.phpmelody.com/how-to-use-the-custom-fields/</a></p>
					</div>


				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content">


	<?php 	
		echo $message; 

		switch($step)
		{

			case 1:		//	STEP 1
	?>


		<?php if($submission_type == 'upload') : ?>

		<div class="card">
			<div class="card-body">
				<h5 class="mb-3 font-weight-semibold">Upload Video</h5>
					<div class="d-block">
						<form name="upload-video-addvideo-page" id="upload-video-addvideo-page" enctype="multipart/form-data" action="admin-ajax.php" method="post" class="btn-hide-upload">

						<div class="input-group input-group-lg mb-3">
							<div class="form-group-feedback form-group-feedback-left">
								<input type="file" name="file" id="upload-video-addvideo-btn" class="file-input form-control form-control-lg alpha-grey" data-main-class="input-group-lg" data-show-upload="false" data-show-remove="false" data-show-preview="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Select file" data-fouc>
								<div class="form-control-feedback form-control-feedback-lg">
									<i class="icon-media text-muted"></i>
								</div>
								<input type="hidden" name="upload-type" value="" /> 
								<input type="hidden" name="p" value="upload" /> 
								<input type="hidden" name="do" value="upload-file" />
								<span id="debugging-output-serverdata" class="form-text text-muted"></span>
							</div>
						</div>
						</form>
					</div>
			</div>
		</div>

		<?php else : ?>

		<div class="card">
			<div class="card-body">
				<h5 class="mb-3 font-weight-semibold">Enter URL</h5>
					<form name="add" action="add-video.php?step=2" method="post" class="form">
					<div class="d-block">
						<div class="input-group mb-3">
							<div class="form-group-feedback form-group-feedback-left">
							<input type="text" id="addvideo_direct_input" name="url" class="form-control form-control-lg alpha-grey" placeholder="https://" /> 
								<div class="form-control-feedback form-control-feedback-lg">
									<i class="icon-link2 text-muted"></i>
								</div>
							</div>
							<div class="input-group-append">
								<button type="submit" name="Submit" class="btn btn-primary btn-lg" id="addvideo_direct_submit">Continue <i class="mi-navigate-next"></i></button>
							</div>
						</div>
					</div>

					<div class="d-md-flex align-items-md-center flex-md-wrap text-center text-md-left">
						<ul class="list-inline mb-0 ml-md-auto">
							<li class="list-inline-item"><a href="#list-sources" class="btn btn-link text-default" data-toggle="collapse" data-target="#list-sources"><i class="icon-menu7 mr-2"></i> Available Sources</a></li>
						</ul>
					</div>

					<div id="list-sources" class="collapse">
						<div class="mt-2 pt-2">
							<div class="row">
								<div class="col-md-12">
								<ul style="height:200px; overflow-y: scroll; padding: 6px; color:#666; border: 1px solid #eee;" class="list-unstyled rounded  alpha-grey">
								<?php
								$sources = a_fetch_video_sources();
								$sources = array_reverse($sources);
								$sources = array_sort($sources, 'source_name', SORT_ASC);
								$counter = 1;

								foreach ($sources as $k => $src)
								{
								if (is_int($k) && $k >= 2): 
								?>
								<li><?php echo $counter.'. '. ucfirst($src['source_name']);?> <small>(e.g. <?php echo $src['url_example'];?>)</small></li>
								<?php 
								$counter++;
								endif;
								}
								?>
								</ul>
								</div>
							</div><!-- .row --> 
						</div><!-- #import-opt-content -->
					</div>
					</form>
			</div>
		</div>

		<?php endif; ?>

	<?php
	break;
	
	case 2:		//	STEP 2

		if(isset($_POST['Submit']) || $_GET['url'] != '' || isset($_GET['filename']))
		{

			if ($_POST['uniq_id']) //@since v2.3
			{
				$uniq_id = $_POST['uniq_id'];
			}
			else
			{
				$uniq_id = generate_video_uniq_id();
			}

			$video_details['uniq_id'] = $uniq_id;

			if($_POST['url'] != '' || $_GET['url'] != '')
				$url = (isset($_POST['url'])) ? trim($_POST['url']) : trim($_GET['url']);
			
			if($_POST['submitted'] != '' || $_GET['submitted'] != '')
			{
				$submitted = (isset($_POST['submitted'])) ? $_POST['submitted'] : trim($_GET['submitted']);
				$submitted_user_id = username_to_id($submitted);// $userdata['id'];
			}
			else
			{
				$submitted = $userdata['username'];
				$submitted_user_id = $userdata['id'];
			}
			/*
				MODE
				1 = Outsource (e.g. youtube)
				2 = Direct URL to video file
				3 = Direct URL/Path/Filename to video hosted locally
			*/
			
			$mode = 0;
			$temp = '';
			
			$url = expand_common_short_urls($url);
			
			//	Is this a direct link to a video file?
			if (strpos($url, '?') !== false)
			{
				$temp = explode('?', $url);
				$url = $temp[0];
			}
			
			$ext = pm_get_file_extension($url, true);
			
			if (is_array($temp) && pm_count($temp) > 0)
			{
				$url = '';
				$temp[0] = rtrim($temp[0], '?');
				$temp[0] = $temp[0] .'?';
				foreach ($temp as $k => $v)
				{
					$url .= $v;
				}
			}
			
			if(in_array($ext, $allowed_ext) && (preg_match('/photobucket\.com/', $url) == 0))
			{
				if(!is_url($url))
				{
					// maybe it's an IP address
					if (is_ip_url($url))
					{
						$mode = 2;
					}
					else
					{
						$mode = 3;
					}
				}
				else if(strpos($url, _URL) !== false)
				{
					$mode = 3;
				}
				else
				{
					// filenames that look like domains pass as URLs (e.g. some-file.info.mp4) 
					// so we need to check them again for http(s), "//" and www
					if ( ! preg_match('%^((http(s?)\://)|(//)|(www\.))%', $url)) 
					{
						$mode = 3;
					}
					else 
					{
						$mode = 2;
					}
				}
			}
			elseif(is_url($url))
			{
				$mode = 1;
			}
			else	//	default;
			{
				$mode = 2;
			}
			if(isset($_GET['filename']) && $_GET['filename'] != '')
				$mode = 3;
			
			//	Build the $video_details array;
			switch($mode)
			{
				case 1: 	//	 Outsource (e.g. youtube); 
					$sources = a_fetch_video_sources();
					$use_this_src = -1;

					if($sources === false || pm_count($sources) == 0)
					{
						$message = "There are no sources available.";
						break;
					}
					
					foreach($sources as $src_id => $source)
					{
						if($use_this_src > -1)
						{
							break;
						}
						else
						{
							if(@preg_match($source['source_rule'], $url))
							{
								$use_this_src = $source['source_id'];
							}
						}
					}

					if($use_this_src > -1)
					{
						if(!file_exists( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php"))
						{
							$message = "File '/src/" . $sources[ $use_this_src ]['source_name'] . ".php'" . " not found.";
							break;
						}
						else
						{
							$temp = array();
							$do_main = $sources[ $use_this_src ]['php_namespace'] .'\do_main';
							if ( ! function_exists($do_main))
							{
								require_once( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php");
							}
							$do_main($temp, $url);
							
							$video_details = array_merge($video_details, $temp);
							
							unset($temp);
							
							$video_details['source_id'] = $use_this_src;
						}
					}
					else
					{
						$message = "<strong>The submitted video source might not be supported</strong>. For a full list of supported sites, open the 'Help' section (Top right of this page).";
					}
				break;
				
				case 2:		//	2 = direct link to .flv/.mp4 (outsource)
					if(!is_url($url) && ! is_ip_url($url))
					{
						$message = '<strong>'.$url.'</strong><br />This doesn\'t look like a valid link. Please <a href="add-video.php?step=1">return</a> and try again.';
						break;
					}
					$video_details['source_id'] = 2;
					$video_details['url_flv'] = $url;
					$video_details['direct'] = $url;
				break;
				case 3:		//	flv hosted locally or just uploaded
				
					if(isset($_GET['filename']) && $_GET['filename'] != '')
					{
						$submission_type = 'upload';

						$contents = get_config('last_video');
						update_config('last_video', '');
						
						//	try the backup file
						if($contents == '')
						{
							$fp = fopen('./temp/upload-file.tmp', 'r');
							$contents = fread($fp, 512);
							fclose($fp);
						}
						
						//	clear file contents anyway
						$fp = fopen('./temp/upload-file.tmp', 'w');
						fwrite($fp, '');
						fclose($fp);
						
						if ($contents == '')	
						{
							$message  = 'Could not retreive the name of the uploaded file. ';
							$message .= '<br />Check your <a href="'. _URL .'/'._ADMIN_FOLDER.'/log.php">System log</a> for any error messages.';
							
							if ( ! is_writable(ABSPATH .''._ADMIN_FOLDER.'/temp/upload-file.tmp'))
							{
								$message .= '<br />Make sure the "<em>/'._ADMIN_FOLDER.'/temp/upload-file.tmp</em>" file has the required permissions (0777) ';
								$message .= 'and then try uploading the video again.';
							}
						}
						else
						{
							//	get filename
							$content  = explode("/", $contents);
							$filename = $content[ pm_count($content)-1 ];
							
							//	move the new file to the videos directory 
							$oldpath = $contents;
							$newpath = _VIDEOS_DIR_PATH . $filename;
							
							if ($oldpath != $newpath)
							{
								if(!rename($oldpath, $newpath))
								{
									$message  = 'Could not move uploaded file to the uploads directory. ';
									$message .= 'Make sure the /uploads directory is writable (0777).';
									break;
								}
							}
							$video_details['url_flv'] = $filename;
							$video_details['direct'] = $filename;
							
						}				
					}
					else
					{
						//	this means $url is either the path or a direct link to the .flv file whick is hosted locally(!)
						//	we only need the filename
						$temp = explode("/", $url);
						$video_details['url_flv'] = $temp[ pm_count($temp)-1 ];
						unset($temp);
					}
					$sources = a_fetch_video_sources();
					
					$use_this_src = -1;
					foreach($sources as $src_id => $source)
						if($source['source_name'] == 'localhost')
							$use_this_src = $source['source_id'];
						$video_details['source_id'] = ($use_this_src != -1) ? $use_this_src : 1; //	1 = Default for LOCALHOST
				break;
			}
			//	Prevent adding the same video twice
			if ($video_details['direct'] != '')
			{
				$sql = "SELECT * FROM pm_videos_urls WHERE direct = '". $video_details['direct'] ."'";
				
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$message .= 'This video is already in your database.';
					$message .= '</div><div>';
					$message .= '<a href="'. _URL .'/watch.php?vid='. $row['uniq_id'] .'" class="btn btn-primary" target="_blank">Watch <i class="mi-open-in-new"></i></a> ';
					$message .= '<a href="edit-video.php?vid='. $row['uniq_id'] .'" class="btn btn-success">Edit
					</a> ';
				}
				unset($row, $sql, $result);
			}
			if (strlen($message) == 0 && $video_details['url_flv'] != '')
			{
				$sql = "SELECT * FROM pm_videos WHERE url_flv = '". $video_details['url_flv'] ."'";
				
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$message .= 'This video is already in your database.';
					$message .= '</div><div>';
					$message .= '<a href="'. _URL .'/watch.php?vid='. $row['uniq_id'] .'" class="btn btn-primary" target="_blank">Watch <i class="mi-open-in-new"></i></a> ';
					$message .= '<a href="edit-video.php?vid='. $row['uniq_id'] .'" class="btn btn-success">Edit</a> ';

				}
				unset($row, $sql, $result);
			}
			if($message != '')
			{
				echo pm_alert_error($message);
			}
			else	//	show form
			{
				$video_details['submitted_user_id'] = (int) $submitted_user_id;
				$video_details['submitted'] = $submitted;
				add_video_form($video_details);
			}
		}	//	endif isset(POST or GET)
		else
		{
			echo "<a href=\"add-video.php?step=1\" class=\"btn btn-primary\">&larr; Please go to Step 1</a>";
			if ( ! headers_sent())
			{
				header("Location: add-video.php?step=1");
			}
			else 
			{
				echo '<meta http-equiv="refresh" content="0;URL=add-video.php?step=1" />';
			}
			exit();
		}
	break;
	case 3:		//	STEP 3
	

		if(isset($_POST['submit']))
		{
			$required_fields = array('video_title' => 'The "Video Title" field cannot be empty',
									'url_flv' => 'A direct link to the video file is missing', 
									'category' => 'Please assign at least one category to this video'
									);
			$message = '';
			
			foreach($video_details as $field => $value)
			{
				if ($field == 'category' && is_array($_POST[$field]))
				{
					$_POST[$field] = implode(',', $_POST[$field]);
				}
				$video_details[$field] = trim($_POST[$field]);
				if(trim($_POST[$field]) == '' && array_key_exists($field, $required_fields))
					$message .= $required_fields[$field] . '<br />';
			}

			$video_details['yt_length'] = ($_POST['yt_min'] * 60) + $_POST['yt_sec'];
			$video_details['meta'] = $_POST['meta'];
			
			$added = validate_item_date($_POST);
			if ($added === false)
			{
				$message .= "Invalid date. Please correct it.<br />";
			}
			else
			{
				$video_details['added'] = pm_mktime($added);
			}
			
			if($message != '')
			{
				echo pm_alert_error($message);
				add_video_form($video_details);
				break;
			}
			else
			{
				$message = '';
				//	check if this video already exists
				if(count_entries('pm_videos', 'url_flv', $video_details['url_flv']) > 0)
				{
					$message .= "This video (".$video_details['url_flv'].") is already in your database. Please go back and make the right adjustments.<br />";
				}
				elseif( ($video_details['direct'] != "") && (count_entries('pm_videos_urls', 'direct', $video_details['direct']) > 0))
				{
					$message .= "This direct link <em>'".$video_details['direct']."'</em> already exists in your database. <br />Please go back and make the right adjustments.<br />";
				}
				else
				{
					if ($_POST['uniq_id']) //@since v2.3
					{
						$uniq_id = $_POST['uniq_id'];
					}
					else
					{
						$uniq_id = generate_video_uniq_id();
					}

					$video_details['uniq_id'] = $uniq_id;
					
					//	upload, download or rename thumbnail file
					if ($video_details['yt_thumb_local'] != '')
					{
						$tmp_parts = explode('/', $video_details['yt_thumb_local']);
						$thumb_filename = array_pop($tmp_parts);
						$tmp_parts = explode('.', $thumb_filename);
						$thumb_ext = array_pop($tmp_parts);
						$thumb_ext = strtolower($thumb_ext);
						$renamed = false;
						
						if (file_exists(_THUMBS_DIR_PATH . $thumb_filename))
						{
							if (rename(_THUMBS_DIR_PATH . $thumb_filename, _THUMBS_DIR_PATH . $uniq_id . '-1.'. $thumb_ext))
							{
								$video_details['yt_thumb'] = $uniq_id . '-1.'. $thumb_ext;
								$renamed = true;
							}
						}
			
						if ( ! $renamed)
						{
							$video_details['yt_thumb'] = $video_details['yt_thumb_local'];
						}

						generate_social_thumb(_THUMBS_DIR_PATH . $video_details['yt_thumb']);
					}
					else
					{
						//	download thumbnail
						$sources = a_fetch_video_sources();
						$use_this_src = -1;
						
						foreach($sources as $src_id => $source)
						{
							if($src_id == $video_details['source_id'])
							{
								$use_this_src = $source['source_id'];
								break;
							}
						}
						
						$download_thumb = $sources[ $use_this_src ]['php_namespace'] .'\download_thumb';
						if ( ! function_exists($download_thumb))
						{
							require_once( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php");
						}
						
						if ('' != $video_details['yt_thumb'])
						{
							$img = $download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
							generate_social_thumb($img);
						}
						else 
						{
							$img = true;
						}
						
						//if($img === false)
						//	$message .= "An error occurred while downloading the thumbnail!<br />";
					}
				}
				
				if ($img === false)
				{
					echo pm_alert_error('An error occurred while downloading the thumbnail. Check that GD Library is installed and enabled on your server.');
				}
				
				if ($message != '')
				{
					echo pm_alert_info($message);
					echo '<a href="add-video.php?step=1" class="btn btn-success">&larr; Return</a> ';

				}
				else	//	Everything is good. Now we can add the new video to the database
				{
					if ($_POST['featured'] == '1')
					{
						$video_details['featured'] = 1;
					}
					else
					{
						$video_details['featured'] = 0;
					}
					
					$new_video = insert_new_video($video_details, $new_video_id);
					if($new_video !== true)
					{
						$message = "<em>A problem occurred! Couldn't add the new video in your database;</em><br /><strong>MySQL Reports:</strong> ".$new_video[0]."<br /><strong>Error Number:</strong> ".$new_video[1]."<br />";		
					}
					else
					{
						//	tags?
						if(trim($_POST['tags']) != '')
						{
							$tags = explode(",", $_POST['tags']);
							foreach($tags as $k => $tag)
							{
								$tags[$k] = stripslashes(trim($tag));
							}
							//	remove duplicates and 'empty' tags
							$temp = array();
							for($i = 0; $i < pm_count($tags); $i++)
							{
								if($tags[$i] != '')
									if($i <= (pm_count($tags)-1))
									{
										$found = 0;
										for($j = $i + 1; $j < pm_count($tags); $j++)
										{
											if(strcmp($tags[$i], $tags[$j]) == 0)
												$found++;
										}
										if($found == 0)
											$temp[] = $tags[$i];
									}
							}
							$tags = $temp;
							//	insert tags
							if(pm_count($tags) > 0)
								insert_tags($uniq_id, $tags);
						}
						$message = "The video has been successfully submitted.";
					}
					echo pm_alert_success($message);
					echo '<br />';
					
					if((isset($_GET['filename']) && $_GET['filename'] != '') || $video_details['source_id'] == 1)
					{
					
						echo '<a href="add-video.php?mode=upload" class="btn btn-success pl-3 mx-1">&larr;  Upload another video</a> ';

					} 
					else
					{

						echo '<a href="add-video.php?step=1" name="add_new" class="btn btn-success mr-2" />&larr; Add another new video</a>';

					}

						echo '<a href="videos.php" name="import_new" class="btn btn-success mx-1">Manage videos</a> ';

					}
			}
		}	//	end if post['submit'];
		else
		{
			if(headers_sent())
			{
				echo '<meta http-equiv="refresh" content="0;URL=add-video.php?step=1" />';
			}
			else
			{
				header("Location: add-video.php?step=1");
			}
			exit();
		}
	break;
}
?>
</div>
<!-- /content area -->
<?php
include('footer.php');