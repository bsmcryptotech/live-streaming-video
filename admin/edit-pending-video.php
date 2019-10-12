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
$load_tagsinput = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_uniform = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$load_fileinput_upload = 1;

$_page_title = 'Edit suggested/uploaded video';
include('header.php');

$pm_temp_id = (int) $_GET['id'];
$r = array();
$errors = array();
$success = false;


if ($pm_temp_id)
{
	$sql = "SELECT * 
			FROM pm_temp 
			WHERE id = '". $pm_temp_id ."'";
	$result = mysql_query($sql);
	$r = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	$r['featured'] = 0;
	$r['restricted'] = 0;
	$r['site_views'] = 0;
	$r['submitted'] = $r['username'];
	$r['url_flv'] = $r['url'];
	$r['direct'] = $r['url'];
	$r['yt_thumb'] = '';
	if (preg_match('/http(s)?:/', $r['thumbnail']) || strpos($r['thumbnail'], '//') === 0)
	{
		$r['yt_thumb'] = $r['thumbnail'];
	}
	elseif ($r['thumbnail'] != '')
	{
		$r['yt_thumb'] = _THUMBS_DIR . $r['thumbnail'];
	}
	$my_tags_str = $r['tags'];
	$r['added'] = time();
	$r['allow_comments'] = 1;
	$r['allow_embedding'] = 1;
	
}
else
{
	$errors[] = 'Missing video ID';
}
if ($_POST['submit'] != '' && $pm_temp_id)
{
	$inputs = array();
	
	foreach($_POST as $k => $v)
	{
		if ( ! is_array($v))
		{
			$inputs[$k] = stripslashes(trim($v));
		}
		else
		{
			$inputs[$k] = $v;
		}
	}
	$inputs['featured'] 		= (int) $_POST['featured'];
	$inputs['allow_comments'] 	= (int) $_POST['allow_comments'];
	$inputs['allow_embedding'] 	= (int) $_POST['allow_embedding'];
	
	
	if (strlen($inputs['video_title']) == 0)
	{
		$errors[] = 'Insert the video title';
	}
	if ((is_array($inputs['category']) && pm_count($inputs['category']) == 0) || ( ! isset($inputs['category'])))
	{
		$errors[] = 'Please select a category for this video';
	}
	
	$added = validate_item_date($_POST);
	
	if ($added === false)
	{
		$errors[] = 'Invalid publish date provided.';
		$result = false;
	}
	
	// save and approve video.
	if (pm_count($errors) == 0)
	{
		define('PHPMELODY', true);
		$video_details = array(	'uniq_id' => '',	
								'video_title' => '',	
								'description' => '',	
								'yt_id' => '',	
								'yt_length' => '',	
								'category' => '',
								'submitted_user_id' => 0,	
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
								'restricted' => 0,
								'allow_comments' => 1,
								'allow_embedding' => 1
								);
		$sources = a_fetch_video_sources();
		
		$video_details = array_merge($video_details, $inputs);
		
		$video_details['yt_length'] = ($inputs['yt_min'] * 60) + $inputs['yt_sec'];
		$video_details['added'] = pm_mktime($added);
		$video_details['site_views'] = $inputs['site_views_input'];
		$video_details['submitted_user_id'] = username_to_id($video_details['submitted']);
		
		$uniq_id = generate_video_uniq_id();
		$video_details['uniq_id'] = $uniq_id;
		
		
		//	upload or download thumbnail picture.
		if($_FILES['thumb']['name'] != '')
		{
			require_once('img.resize.php');
			$img = new resize_img();
			$img->sizelimit_x = THUMB_W_VIDEO;
			$img->sizelimit_y = THUMB_H_VIDEO;
			
			$new_thumb_name = $uniq_id . "-1";
			
			//	resize image and save it
			if($img->resize_image($_FILES['thumb']['tmp_name']) === false)
			{
				$message .= $img->error;
			}
			else
			{
				$img->save_resizedimage(_THUMBS_DIR_PATH, $new_thumb_name);
			}
			$inputs['yt_thumb'] = _THUMBS_DIR . $new_thumb_name . '.' . strtolower($img->output);
			$video_details['yt_thumb'] = $inputs['yt_thumb'];
			
			// delete uploaded thumbnail
			if ($r['thumbnail'] != '' && $r['source_id'] == $sources['localhost']['source_id'])
			{
				if (file_exists(_THUMBS_DIR_PATH . $r['thumbnail']))
				{
					unlink(_THUMBS_DIR_PATH . $r['thumbnail']);
				}
			}
		}
		
		if ($inputs['yt_thumb'] != '' && $r['thumbnail'] != '' && $r['source_id'] == $sources['localhost']['source_id'])
		{
			// thumbnail URL changed?
			if ($inputs['yt_thumb'] != $r['yt_thumb'])
			{
				// delete uploaded thumbnail
				if (file_exists(_THUMBS_DIR_PATH . $r['thumbnail']))
				{
					unlink(_THUMBS_DIR_PATH . $r['thumbnail']);
				}
			}

			if (strpos($inputs['yt_thumb'], 'http') !== false) // remote image
			{
				require_once( "./src/localhost.php");
				
				$download_thumb = $sources['localhost']['php_namespace'] .'\download_thumb';
				
				$img = $download_thumb($inputs['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
				generate_social_thumb($img);
			}
		}
		
		// just uploaded
		if ($inputs['yt_thumb_local'] != '')
		{
			$video_details['yt_thumb'] = $input['yt_thumb_local'];
		}
		
		//	fetch information about this video
		if ($inputs['source_id'] != $sources['localhost']['source_id'])
		{
			switch ($sources[ $video_details['source_id'] ]['source_name'])
			{
				case 'divx':
				case 'windows media player':
				case 'quicktime':
				case 'mp3':
					$video_details['source_id'] = $sources['other']['source_id'];
				break;
			}
			
			require_once( "./src/" . $sources[ $video_details['source_id'] ]['source_name'] . ".php");

			$do_main = $sources[ $video_details['source_id'] ]['php_namespace'] .'\do_main';
			$download_thumb = $sources[ $video_details['source_id'] ]['php_namespace'] .'\download_thumb';
			
			$do_main($temp, $video_details['direct'], false);
			
			if($temp['yt_id'] == '')
			{
				$video_details['yt_id'] = substr( md5( time() ), 2, 9);
			}
			else
			{
				$video_details['yt_id'] = $temp['yt_id'];
			}
			
			if ($video_details['source_id'] == $sources['other']['source_id'])
			{
				$video_details['url_flv']	=	$video_details['direct'];
			}
			else
			{
				$video_details['url_flv']	=	$temp['url_flv'];
			}
			
			$video_details['mp4']		=	$temp['mp4'];
			
			if ($video_details['yt_thumb'] == '')
			{
				$video_details['yt_thumb']	= $temp['yt_thumb'];
			}
			
			if ($video_details['yt_length'] == 0)
			{
				$video_details['yt_length']	= (int) $temp['yt_length'];
			}
		}
		else // user uploaded video
		{
			if ($video_details['url_flv'] == '')
			{
				$video_details['url_flv'] = $r['url'];
			}

			if ($r['yt_thumb'] == $video_details['yt_thumb'])
			{
				// rename thumbnail
				$tmp_parts = explode('.', $r['thumbnail']);
				$ext = array_pop($tmp_parts);
				$ext = strtolower($ext);
				
				if ($r['thumbnail'] != '' && file_exists(_THUMBS_DIR_PATH . $r['thumbnail']))
				{
					if (rename(_THUMBS_DIR_PATH . $r['thumbnail'], _THUMBS_DIR_PATH . $uniq_id . '-1.'. $ext))
					{
						$r['thumbnail'] =  $uniq_id . '-1.'. $ext;
						generate_social_thumb(_THUMBS_DIR_PATH . $uniq_id . '-1.'. $ext);
					}
				}
				
				$inputs['yt_thumb'] = _THUMBS_DIR . $r['thumbnail'];
				$video_details['yt_thumb'] = $inputs['yt_thumb'];
			}
		}
		
		//	download thumbnail
		if ('' != $video_details['yt_thumb'] && $video_details['source_id'] != $sources['localhost']['source_id'])
		{
			$img = $download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
			generate_social_thumb($img);
		}
		
		// uploaded thumbnail
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

			$social_thumb_filename = str_replace('-1', '-social', $thumb_filename);
			if (file_exists(_THUMBS_DIR_PATH . $social_thumb_filename))
			{
				rename(_THUMBS_DIR_PATH . $social_thumb_filename, _THUMBS_DIR_PATH . $uniq_id . '-social.'. $thumb_ext);
			}

			if ( ! $renamed)
			{
				$video_details['yt_thumb'] = $video_details['yt_thumb_local'];
			}
			
			// delete user-uploaded thumbnail
			if ($r['thumbnail'] != '' && strpos($r['thumbnail'], 'http') === false && file_exists(_THUMBS_DIR_PATH . $r['thumbnail']))
			{
				unlink(_THUMBS_DIR_PATH . $r['thumbnail']);
			}
		}
		
		foreach($video_details as $k => $v)
		{
			$video_details[$k] = str_replace("&amp;", "&", $v);
		}
		
		if (is_array($video_details['category']))
		{
			$video_details['category'] = implode(',', $video_details['category']);
		}
		//	Ok, let's add this video to our database
		$new_video = insert_new_video($video_details, $new_video_id);
		if ($new_video !== true)
		{
			$errors[] = '<em>Ouch, sorry! Could not insert new video in your database;</em><br /><strong>MySQL Reported: '.$new_video[0].'<br /><strong>Error Number:</strong> '.$new_video[1];				
		}
		else
		{
			if($video_details['tags'] != '')
			{
				$tags = explode(",", $video_details['tags']);
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
			
			//	remove the suggested video from 'pm_temp'
			@mysql_query("DELETE FROM pm_temp WHERE id = '". $pm_temp_id ."'");
			$success = 'The video was saved and approved.</strong> <a href="'. _URL .'/watch.php?vid='. $uniq_id .'" target="_blank" title="Watch video">Watch this video</a>';
		}
		
	}
	
	$r = $inputs;
	
	if( !empty($r['category']) ) {
		$r['category'] = implode(',', $r['category']);
	}
}

?>
<!-- Main content -->
<div class="content-wrapper">

<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title; ?></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
					<a href="pending-videos.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2 pm-show-loader d-none d-md-inline">Cancel</a>
					<button type="submit" name="submit" value="Save &amp; Approve" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.forms[0].submit({return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')});" form="update-form"><i class="mi-check"></i> Save &amp; Approve</button>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
			<div class="d-horizontal-scroll">				
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="videos.php" class="breadcrumb-item">Videos</a>
					<a href="pending-videos.php" class="breadcrumb-item"><span class="breadcrumb-item">Videos pending approval</span></a>
					<a href="edit-pending-video.php" class="breadcrumb-item active"><?php echo $_page_title; ?></a>
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
	<div class="content">

	
	<?php if ($success) : ?>
		<?php echo pm_alert_success($success);?>
		<hr />
		<a href="pending-videos.php" class="btn btn-sm btn-success">&larr; Approve other videos</a> 
		<a href="edit-video.php?vid=<?php echo $uniq_id;?>" class="btn btn-sm btn-success">Edit</a>
		</div><!-- .content -->
		<?php
		include('footer.php');
		exit();
		?>	
	<?php endif;?>
	
<?php 
	if (pm_count($errors) > 0)
	{
		echo pm_alert_error($errors);
	}
?>

<form name="update" id="update-form" enctype="multipart/form-data" action="edit-pending-video.php?id=<?php echo $pm_temp_id; ?>" method="post" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')">
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
				<input name="video_title" type="text" class="form-control form-required font-weight-semibold font-size-lg" value="<?php echo htmlspecialchars($r['video_title']); ?>" />
				<br >
				<div id="textarea-dropzone" class="upload-file-dropzone">
					<div class="fileinput-button">
						<input type="file" name="file" id="upload-file-wysiwyg-btn" class="file-input file-input-custom form-control form-control-sm alpha-grey" multiple="multiple" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload images" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" data-fouc />
					</div>
					<textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce"><?php echo $r['description']; ?></textarea>
					<span class="autosave-message"></span>
				</div>					
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Video Details</h6>
			</div>

				<?php
				if($r['yt_length'] > 0) {	
					$yt_minutes = intval($r['yt_length'] / 60);
					$yt_seconds = intval($r['yt_length'] % 60); 
				} else {
					$yt_minutes = 0;
					$yt_seconds = 0;
				}
				?>
				<ul class="nav nav-tabs nav-tabs-bottom">
					
					<li class="nav-item"><a href="#badge-tab0" class="nav-link active" data-toggle="tab">Tags</a></li>
					<li class="nav-item"><a href="#badge-tab1" class="nav-link" data-toggle="tab">Duration <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-yt_length"><strong><?php echo sec2min($r['yt_length']);?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab2" class="nav-link" data-toggle="tab">Comments <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($r['allow_comments'] == 1) ? 'alpha-success text-success-600' : 'alpha-primary';?>" id="value-comments"><strong><?php echo ($r['allow_comments'] == 1) ? 'allowed' : 'closed';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab3" class="nav-link" data-toggle="tab">Embedding <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($r['allow_embedding'] == 1) ? 'alpha-success text-success-600' : 'alpha-primary';?>" id="value-embedding"><strong><?php echo ($r['allow_embedding'] == 1) ? 'allowed' : 'disallowed';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab4" class="nav-link" data-toggle="tab">Featured <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($r['featured'] == 1) ? 'alpha-alpha text-success-600' : 'alpha-primary';?>" id="value-featured"><strong><?php echo ($r['featured'] == 1) ? 'yes' : 'no';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab5" class="nav-link" data-toggle="tab">Private <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-register"><strong><?php echo ($r['restricted'] == 1) ? 'yes' : 'no';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab7" class="nav-link" data-toggle="tab">Published <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-publish"><strong><?php echo date("M d, y", $r['added']);?></strong></span></a></li>

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
							<input type="text" id="tags_addvideo_1" name="tags" value="<?php echo $my_tags_str; ?>" class="tags form-control tags-input" />
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
							<label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($r['allow_comments'] == 1) echo 'checked="checked"';?> /> Allow comments on this video</label>
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
							<label><input name="allow_embedding" id="allow_embedding" type="checkbox" value="1" <?php if ($r['allow_embedding'] == 1) echo 'checked="checked"';?> /> Allow embedding to 3rd party sites</label>
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
							<label><input type="checkbox" name="featured" id="featured" value="1" <?php if($r['featured'] == 1) echo 'checked="checked"';?> /> Yes, mark as featured</label>
					</div>

					<div class="tab-pane" id="badge-tab5">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Require registration to watch video:</div>
							<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php if ($r['restricted'] == 1) echo 'checked="checked"'; ?> /> Yes</label>
							<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php if ($r['restricted'] == 0) echo 'checked="checked"'; ?> /> No</label>
					</div>

					<div class="tab-pane" id="badge-tab6">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Views:</div>
							<input type="hidden" name="site_views" value="<?php echo $r['site_views'];?>" />
							<input type="text" name="site_views_input" id="site_views_input" value="<?php echo $r['site_views']; ?>" size="10" class="form-control col-md-3" />
					</div>

					<div class="tab-pane" id="badge-tab7">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Publish date:</div>
							<?php echo show_form_item_date($r['added']);?>
							<div class="text-muted mt-2">You can schedule videos to be available at a future date.</div>
					</div>

					<div class="tab-pane" id="badge-tab8">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Submitted by:</div>
							<input type="text" name="submitted" id="submitted" value="<?php echo htmlspecialchars($r['submitted']); ?>" class="form-control col-md-3" />
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

				<?php if ($r['source_id'] == 1) : ?>
				<div class="upload-file-dropzone" id="video-file-dropzone">
					<div class="float-right">
						<span class="fileinput-button">
							<input type="file" name="file" id="upload-video-source-btn" class="file-input form-control form-control-sm alpha-grey" data-show-caption="false" data-show-upload="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload &amp; Replace" data-browse-class="btn btn-primary btn-sm font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" />
						</span>
					</div>
					<span class="text-uppercase font-weight-semibold">Local file:</span>
					<a href="<?php echo _VIDEOS_DIR . $r['url_flv']; ?>" target="_blank"><?php echo $r['url_flv']; ?></a>
				</div>
				<?php endif; ?>
				

				<?php if ($r['source_id'] != 1) : ?>
				<legend class="font-weight-semibold text-uppercase font-size-sm">Video URLS</legend>

				<?php
				if ($r['source_id'] == 0 && is_array($r['jw_flashvars'])) :
					$pieces = explode(';', $r['url_flv'], 2);
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
							<option value="rtmp" <?php echo ($r['jw_flashvars']['provider'] == 'rtmp') ? 'selected="selected"' : '';?>>RTMP</option>
							<option value="http" <?php echo ($r['jw_flashvars']['provider'] == 'http') ? 'selected="selected"' : '';?>>HTTP</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Load Balancing (<small>Optional</small>):
						<i class="mi-info-outline" rel="popover" data-html="true" data-trigger="hover" title="" data-content="This is the equivalent of JW Player's <code><i>rtmp.loadbalance</i></code> flashvar."></i>
						</label>
					<div class="col-lg-9">
						<label><input class="checkbox inline" type="radio" name="jw_rtmp_loadbalance" value="true" <?php echo ($r['jw_flashvars']['loadbalance'] == 'true') ? 'checked="checked"' : '';?> /> On</label> 
						<label><input class="checkbox inline" type="radio" name="jw_rtmp_loadbalance" value="" <?php echo ($r['jw_flashvars']['loadbalance'] != 'true') ? 'checked="checked"' : '';?> /> Off</label>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Subscribe (<small>Optional</small>):
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="This is the equivalent of JW Player's <code>rtmp.subscribe</code> flashvar."></i>
						</label>
					<div class="col-lg-9">
						<label><input class="checkbox inline" type="radio" name="jw_rtmp_subscribe" value="true" <?php echo ($r['jw_flashvars']['subscribe'] == 'true') ? 'checked="checked"' : '';?> /> Yes</label> 
						<label><input class="checkbox inline" type="radio" name="jw_rtmp_subscribe" value="" <?php echo ($r['jw_flashvars']['subscribe'] != 'true') ? 'checked="checked"' : '';?> /> No</label>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Secure Token (<small>Optional</small>):
						<i class="mi-info-outline" rel="popover" data-html="true" data-trigger="hover" data-animation="true" title="" data-content="Some service providers (e.g Wowza Media Server) have a feature called Secure Token that is used to protect your streams from downloading.<br />This <code>securetoken</code> parameter is optional and might not be compatible with all RTMP Service providers."></i>
						</label>
					<div class="col-lg-9">
						<input type="text" name="jw_securetoken" value="<?php echo $r['jw_flashvars']['securetoken'] ;?>" size="20" class="form-control" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Startparam (<small>Optional</small>):
						<i class="mi-info-outline" rel="popover" data-html="true" data-trigger="hover" data-animation="true" title="" data-content="This is the equivalent of JW Player's <code><i>rtmp.startparam</i></code> flashvar."></i>
						</label>
					<div class="col-lg-9">
						<input type="text" name="jw_startparam" value="<?php echo $r['jw_flashvars']['startparam'];?>" size="20" class="form-control" />
					</div>
				</div>

				<?php else: ?> <!--Local video sources-->

				<?php if ($r['source_id'] != 1 && $r['source_id'] != 2) : ?>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						Original Video URL:
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" title="Warning" data-content="Changing this URL will re-import the video. All other data (title, tags, description, etc.) will remain the same."></i>
						</label>
					<div class="col-lg-9">
						<input type="text" name="direct" class="form-control" value="<?php echo $r['direct']; ?>" />
						<input type="hidden" name="direct-original" value="<?php echo $r['direct']; ?>" placeholder="http://"  />
					</div>
				</div>

				<?php endif; ?>

				<div class="form-group row">
					<label class="col-lg-3 col-form-label">
						File Location:
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" title="Warning" data-content="Changing the FLV/MOV/WMV/MP4 location of this video may cause it to stop working!"></i>
						</label>
					<div class="col-lg-9">
						<input type="text" name="url_flv" value="<?php echo $r['url_flv']; ?>" class="form-control" />	
						<input type="hidden" name="url_flv-original" value="<?php echo $r['url_flv']; ?>" placeholder="http://" />
					</div>
				</div>

				<?php endif; ?>

				<?php endif; ?> <!--External video sources-->

				</div>
			</div>
		</div><!--.card-->


<?php if($r['source_id'] == 0 || $r['source_id'] != 1 || $r['source_id'] != 2) : ?>    
		<div class="card">
			<div class="card-header bg-white header-elements-inline header-toggles" data-target="#cardEmbedCode" data-toggle="collapse" aria-expanded="false" aria-controls="cardEmbedCode">
				<h6 class="card-title font-weight-semibold">Embed Code</h6>
				<div class="header-elements">
					<div class="list-icons">
						<i class="mi-info-outline" rel="popover" data-trigger="hover" data-animation="true" data-content="Add or edit the embed code ONLY if you wish to change this video's source. Once an embed code is given, PHP Melody will consider it to be the default video."></i>
						<a href="#" data-target="#cardEmbedCode" data-toggle="collapse" aria-expanded="false" aria-controls="cardEmbedCode" class="text-default"><i class="icon-arrow-up12"></i></a>
					</div>
				</div>
			</div>
			<div class="collapse <?php if($r['source_id'] == 0) echo 'show'; ?>" id="cardEmbedCode">
				<div class="card-body">

				<textarea name="embed_code" rows="2" class="form-control"><?php
				$embed_code = $r['embed_code'];
				$embed_code = str_replace('%%player_w%%', _PLAYER_W_EMBED, $embed_code);
				$embed_code = str_replace('%%player_h%%', _PLAYER_H_EMBED, $embed_code);

				echo $embed_code;
				?></textarea>
				<span class="text-muted mt-1">Accepted HTML tags: <strong>&lt;iframe&gt;</strong>  <strong>&lt;embed&gt;</strong> <strong>&lt;object&gt;</strong> <strong>&lt;param&gt;</strong> and <strong>&lt;video&gt;</strong></span>

				</div>
			</div>
		</div><!--.card-->
<?php endif; ?>


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
						<?php	
							foreach ($meta_data as $meta_id => $meta) : 
									echo admin_custom_fields_row($meta_id, $meta);
							endforeach;
						endif; 
						?>
						</div>

						<?php echo admin_custom_fields_add_form($r['id'], IS_VIDEO); ?>
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
					if (strpos($r['yt_thumb'], 'http') !== 0 && strpos($r['yt_thumb'], '//') !== 0 && $r['yt_thumb'] != '')
					{
						$r['yt_thumb'] = _THUMBS_DIR . $r['yt_thumb'];
					}
					if ( empty($r['yt_thumb']) ) : ?>
					<a href="#" id="show-thumb" data-toggle="collapse" data-target="#show-opt-thumb" rel="tooltip" title="Change the thumbnail URL"></a>

					<div class="d-block justify-content-end text-center rounded bt-slate alpha-slate" style="min-height: 150px;">
						<div>
							<i class="icon-image2 icon-3x text-default border-slate alpha-slate p-3 mt-1 mt-1"></i>
							<h5>No thumbnail</h5>
						</div>
					</div>

					<?php else : ?>
					<a href="#" id="show-thumb" data-toggle="collapse" data-target="#show-opt-thumb" rel="tooltip" title="Click here to change the thumbnail URL"><img src="<?php echo make_url_https($r['yt_thumb']); ?>?cache_buster=<?php echo $time_now;?>" class="img-fluid" /></a>
					<?php endif; ?>
				</div>

				<div id="show-opt-thumb" class="collapse mt-3 p-3">
					<div class="input-group">
					<input type="text" name="yt_thumb" value="<?php echo $r['yt_thumb']; ?>" class="form-control col-md-10" placeholder="http://" /> <span class="input-group-text bg-transparent border-0"><i class="mi-info-outline" rel="tooltip" data-position="top" title="Change this URL to replace the existing thumbnail."></i></span>
					<input type="hidden" name="yt_thumb_old" value="<?php echo $r['yt_thumb']; ?>" class="form-control col-md-12" placeholder="http://" />
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
					<input type="hidden" name="categories_old" class="form-control" value="<?php echo $r['category'];?>"  />
				</div>

					<?php 
					$categories_dropdown_options = array(
													'attr_name' => 'category[]',
													'attr_id' => 'main_select_category',
													'attr_class' => 'category_dropdown custom-select mb-1',
													'select_all_option' => false,
													'spacer' => '&mdash;',
													'selected' => explode(',', $r['category']),
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
							<input type="file" name="file" id="upload-subtitle-btn" class="file-input form-control form-control-lg alpha-grey" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload" data-show-remove="false" data-show-preview="false" />
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
</div>

<input type="hidden" name="categories_old" value="<?php echo $r['category'];?>" />
<input type="hidden" name="language" value="1" />
<input type="hidden" name="source_id" value="<?php echo $r['source_id']; ?>" />
<input type="hidden" name="user_id" value="<?php echo $r['user_id'];?>" />
<input type="hidden" name="url_flv" value="<?php echo $r['url_flv']; ?>" />
<input type="hidden" name="direct" value="<?php echo $r['direct']; ?>" />
<input type="hidden" name="upload-type" value="" />
<input type="hidden" name="p" value="upload" /> 
<input type="hidden" name="do" value="upload-image" />
<!-- .permalink-input filed added to prevent errors in JS -->
<input type="hidden" name="permalink-input-hack" class="permalink-input" value="" />

<div id="stack-controls-disabled" class="list-controls">
	<div class="float-right">
		<a href="pending-videos.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2 pm-show-loader">Cancel</a>
		<button type="submit" name="submit" value="Save &amp; Approve" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.forms[0].submit({return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')});" form="update-form"><i class="mi-check"></i> Save &amp; Approve</button>
	</div>
</div><!-- #list-controls -->
</form>

</div><!-- .content -->
<?php
$uniq_id = substr(md5($_POST['uniq_id'] . time()), 1, 8); // temporary value; defined specifically for upload-image.php
include('footer.php');