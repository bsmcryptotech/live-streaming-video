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

$showm = 'mod_article';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$load_fileinput_upload = 1;

$_page_title = 'Post a new article';

$action = $_GET['do'];
if ( ! in_array($action, array('edit', 'new', 'delete')) )
{
	$action = 'new';	//	default action
}

if ($action == 'edit')
{
	$_page_title = 'Edit article';
}
include('header.php');

?>
<script type="text/javascript">
$(document).ready(function(){
	$("img[name='article_thumbnail']").click(function() {
		var img = $(this);
		var ul = img.parents('.thumbs_ul');
		var li = img.parent();
		var input = $("input[name='post_thumb_show']");
		
		if ( ! li.hasClass('art-thumb-selected'))
		{
			ul.children().removeClass('art-thumb-selected').addClass('art-thumb-default');
			li.addClass('art-thumb-selected');
			input.val(img.attr('src'));
		}
	});
});
</script>
<!-- Main content -->
<div class="content-wrapper">

<div class="page-header-wrapper page-header-edit"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title; ?></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<a href="articles.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2"> Cancel</a>
					<button type="submit" name="submit" value="<?php echo ($action == 'edit') ? 'Save' : 'Publish';?>" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.forms[0].submit({return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')});" form="write_article"><i class="mi-check"></i> <?php echo ($action == 'edit') ? 'Save' : 'Publish';?></button>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="articles.php" class="breadcrumb-item">Articles</a>
					<span class="breadcrumb-item active"><?php echo $_page_title; ?></span>
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

		<div id="display_result" style="display:none;"></div>

<?php
$inputs = array();

if ('' != $_POST['submit'])
{
	$_POST['title'] = after_post_filter($_POST['title']);
	$_POST['tags'] = after_post_filter($_POST['tags']);
	
	if ($action == 'new')
	{
		$result = insert_new_article($_POST);
	}
	else if ($action == 'edit')
	{
		$result = update_article($_POST['id'], $_POST);
	}
	
	if ($result['type'] == 'error')
	{
		echo pm_alert_error($result['msg']);
	}
	else
	{
		if ($action == 'new')
		{
			echo pm_alert_success('<strong>'. $result['msg'] .'.</strong> <a href="'. _URL .'/article-read.php?a='.$result['article_id'].'&mode=preview" target="_blank">See how it looks</a>.');

			echo '<input name="continue" type="button" value="&larr; Manage articles" onClick="location.href=\'articles.php\'" class="btn btn-light" /> ';
			echo ' <input name="add_new" type="button" value="Post a new article &rarr;" onClick="location.href=\'edit-article.php?do=new\'" class="btn btn-success" />';
			echo '</div>';
			
			include('footer.php');
			exit();
		}
		else
		{
			echo pm_alert_success('<strong>'. $result['msg'] .'</strong> <a href="'. _URL .'/article-read.php?a='. $_POST['id'] .'&mode=preview" target="_blank">See how it looks</a>.');
		}
	}	
}

if ($action == 'edit')
{
	$id = (int) $_GET['id'];
	if ($id == 0)
	{
		$action = 'new';
		$inputs = array();
		$inputs['allow_comments'] = 1;
		$inputs['status'] = 1;
		$inputs['author'] = $userdata['id'];
		$inputs['category_as_arr'] = array();
	}
	else
	{
		$inputs = get_article($id);
	}
	$meta_data = get_all_meta_data($inputs['id'], IS_ARTICLE);
	
	if ($inputs['article_slug'] == '')
	{
		$inputs['article_slug'] = 'read-'. sanitize_title($inputs['title']);
		$inputs['article_slug'] = preg_replace('/-video$/', '_video', $inputs['article_slug']);
	}
	
}
else if ($action == 'new')
{
	if ('' != $_POST['submit'])
	{
		$inputs = $_POST;
	}
	else
	{
		$inputs['allow_comments'] = 1;
		$inputs['status'] = 1;
		$inputs['author'] = $userdata['id'];
	}
	if ( ! is_array($inputs['category_as_arr']))
	{
		$inputs['category_as_arr'] = array();
	}
}

$categories = art_get_categories();

//	Filter some fields before output
$inputs['title'] = pre_post_filter($inputs['title']);
$inputs['tags'] = pre_post_filter($inputs['tags']);


if($inputs['date'] > time()) 
{
	$message = 'This article is <strong>scheduled</strong> to appear on your site ';
	$days_until_release = count_days($inputs['date'], time());
	if ($days_until_release == 0)
	{
		$days_until_release = 'today at '. date('g:i A', $inputs['date']);
	}
	else
	{
		$message .= 'in';
		$days_until_release = ($days_until_release == 1) ? $days_until_release .' day' : $days_until_release .' days';
	}
	$message .= ' <strong>'. $days_until_release .'</strong>.<br> <small>Change the "Publish date" below to update its schedule date ('. date("M d, Y g:i A", $inputs['date']) .').</small>';
	
	echo pm_alert_warning($message);
}

?>
<form name="write_article" id="write_article" method="post" action="edit-article.php?do=<?php echo $action; ?>&id=<?php echo $_GET['id'];?>" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted).')">
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
				<input name="title" type="text" class="form-control form-required font-weight-semibold font-size-lg" value="<?php echo $inputs['title']; ?>" />
				<div class="permalink-field mt-2 mb-2">
					<?php if (_SEOMOD) : ?>
					<strong>Permalink:</strong> <?php echo _URL .'/articles/';?><input class="permalink-input" type="text" name="article_slug" value="<?php echo urldecode($inputs['article_slug']);?>" /><?php echo  '_'. (($inputs['id'] == '') ? 'ID' : $inputs['id']) .'.html';?>
					<?php endif; ?>	
				</div>
				<div id="textarea-dropzone" class="upload-file-dropzone">
					<div class="fileinput-button">
						<input type="file" name="file" id="upload-file-wysiwyg-btn" class="file-input file-input-custom form-control form-control-sm alpha-grey" multiple="multiple" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload images" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" data-fouc />
					</div>
					<textarea name="content" cols="100" id="textarea-WYSIWYG" class="tinymce" rows="12"><?php echo $inputs['content']; ?></textarea>
					<span class="autosave-message"></span>
				</div>					
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Article Details</h6>
			</div>

				<?php
				if($inputs['yt_length'] > 0) {	
					$yt_minutes = intval($inputs['yt_length'] / 60);
					$yt_seconds = intval($inputs['yt_length'] % 60); 
				} else {
					$yt_minutes = 0;
					$yt_seconds = 0;
				}
				?>
				<ul class="nav nav-tabs nav-tabs-bottom">	
					<li class="nav-item"><a href="#badge-tab0" class="nav-link active" data-toggle="tab">Tags</a></li>
					<li class="nav-item"><a href="#badge-tab1" class="nav-link" data-toggle="tab">Comments <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($inputs['allow_comments'] == 1) ? 'alpha-success text-success-600' : 'alpha-primary';?>" id="value-comments"><strong><?php echo ($inputs['allow_comments'] == 1) ? 'allowed' : 'closed';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab2" class="nav-link" data-toggle="tab">Sticky <span class="badge badge-flat border-primary text-primary-600 border-0 <?php echo ($inputs['featured'] == 1) ? 'alpha-success text-success-600' : 'alpha-primary';?>" id="value-featured"><strong><?php echo ($inputs['featured'] == 1) ? 'yes' : 'no';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab3" class="nav-link" data-toggle="tab">Private <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-register"><strong><?php echo ($inputs['restricted'] == 1) ? 'yes' : 'no';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab4" class="nav-link" data-toggle="tab">Visibility <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-visibility"><strong><?php echo ($inputs['status'] == 1) ? 'Public' : 'Draft';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab5" class="nav-link" data-toggle="tab">Publish date <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-publish"></span></a></li>
				</ul>

			<div class="card-body">
				<div class="tab-content">
					<div class="tab-pane show active" id="badge-tab0">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Tags:</div>
						<div class="tagsinput bootstrap-tagsinput">
							<input type="text" id="tags_addvideo_1" name="tags" value="<?php echo $my_tags_str; ?>" class="tags form-control tags-input" />
						</div>
					</div>

					<div class="tab-pane" id="badge-tab1">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Comments:</div>
							<label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($inputs['allow_comments'] == 1) echo 'checked="checked"';?> /> Allow comments on this article</label>
							<?php if ($config['comment_system'] == 'off') : ?>
							<div class="alert alert-info">
							Comments are disabled site-wide. 
							<br />
							To enable comments, visit the <a href="settings.php?view=comment" title="Settings page" target="_blank">Settings</a> page.
							</div>
							<?php endif;?>
					</div>

					<div class="tab-pane" id="badge-tab2">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Sticky:</div>
							<label><input type="checkbox" name="featured" id="featured" value="1" <?php if($inputs['featured'] == 1) echo 'checked="checked"';?> /> Yes, stick to front page</label>
					</div>

					<div class="tab-pane" id="badge-tab3">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Require registration:</div>
							<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php if ($inputs['restricted'] == 0) echo 'checked="checked"'; ?> /> Yes</label>
							<label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php if ($inputs['restricted'] == 0) echo 'checked="checked"'; ?> /> No</label>
					</div>

					<div class="tab-pane" id="badge-tab4">
							<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Visibility:</div>
							<label class="checkbox inline"><input type="radio" name="status" id="visibility" value="0" <?php if ($inputs['status'] == 0) echo 'checked="checked"'; ?> /> Draft</label> 
							<label class="checkbox inline"><input type="radio" name="status" id="visibility" value="1" <?php if ($inputs['status'] == 1) echo 'checked="checked"'; ?> /> Public</label>
					</div>

					<div class="tab-pane" id="badge-tab5">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Publish date:</div>
							<?php echo show_form_item_date($inputs['date']);?>
							<div class="text-muted mt-2">You can schedule videos to be available at a future date.</div>
					</div>

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
						<div class="row">
							<div class="col-md-3"><strong>Name</strong></div>
							<div class="col-md-9"><strong>Value</strong></div>
						</div>
						<?php 
						if ($action == 'new') :
							if (pm_count($_POST['meta']) > 0) :
								foreach ($_POST['meta'] as $meta_id => $meta) : 
									$meta['meta_key'] = $meta['key'];
									$meta['meta_value'] = $meta['value'];
									
									echo admin_custom_fields_row($meta_id, $meta);
								endforeach;
							endif;
							
							echo admin_custom_fields_add_form(0, IS_ARTICLE);
						else :
							if (pm_count($meta_data) > 0) :
								foreach ($meta_data as $meta_id => $meta) : 
									echo admin_custom_fields_row($meta_id, $meta);
								endforeach;
							endif;
							
							echo admin_custom_fields_add_form($inputs['id'], IS_ARTICLE);
						endif; ?>
						</div>
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
							<input type="file" name="file" id="upload-article-thumb-btn" class="file-input form-control form-control-sm alpha-grey" data-show-caption="false" data-show-upload="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Update" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" />
						</span>
					</div>
				</div>
			</div>
			<div class="card-body upload-file-dropzone" id="article-thumb-dropzone">

		<div class="widget border-radius4 shadow-div">
			<div class="control-group">
			<div class="controls">
				<div class="clearfix"></div>
				<input type="hidden" name="post_thumb_show" value="<?php if ($inputs['meta']['_post_thumb_show'] != '') echo $inputs['meta']['_post_thumb_show'];?>" />
			</div>
			</div>
		</div><!-- .widget -->


				<div id="article-thumb-container">
				<?php
					$all_meta = $inputs['meta']['*'];
					$total_thumbs = pm_count($all_meta['_post_thumb']);
					
					if (strpos($inputs['yt_thumb'], 'http') !== 0 && strpos($inputs['yt_thumb'], '//') !== 0 && $inputs['yt_thumb'] != '')
					{
						$inputs['show_thumb'] = _ARTICLE_ATTACH_DIR . $inputs['meta']['_post_thumb_show'];
					}
					if ( empty($inputs['show_thumb']) && $total_thumbs == 0 ) : ?>

						<div class="d-block justify-content-end text-center rounded bt-slate alpha-slate" style="min-height: 150px;">
							<div>
								<i class="icon-image2 icon-3x text-default border-slate alpha-slate p-3 mt-1 mt-1"></i>
								<h5>No thumbnail</h5>
							</div>
						</div>

					<?php else : ?>
						<img src="<?php echo _ARTICLE_ATTACH_DIR . $inputs['meta']['_post_thumb_show']; ?>?cache_buster=<?php echo $time_now;?>" class="img-fluid" />
					<?php endif; ?>
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
												'db_table' => 'art_categories',
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
					<input type="hidden" name="categories_old" value="<?php echo $inputs['category'];?>"  />
				</div>

					<?php 
							$categories_dropdown_options = array(
										'db_table' => 'art_categories',
										'attr_name' => 'categories[]',
										'attr_id' => 'main_select_category',
										'attr_class' => 'category_dropdown custom-select mb-1 required-field',
										'select_all_option' => false,
										'spacer' => '&mdash;',
										'selected' => explode(',', $inputs['category']), 
										'other_attr' => 'multiple="multiple" size="3"',
										'option_attr_id' => 'check_ignore'
										);
						echo categories_dropdown($categories_dropdown_options);
					?>
			</div>
		</div><!--.card-->
	</div><!--. col-md-3 sidebar-->
</div>

<input type="hidden" name="author" value="<?php  echo $inputs['author'];?>" />
<input type="hidden" name="id" value="<?php echo $inputs['id'];?>" />
<input type="hidden" name="date_old" value="<?php echo $inputs['date'];?>" />
<input type="hidden" name="p" value="upload" /> 
<input type="hidden" name="do" value="upload-image" />
<input type="hidden" name="upload-type" value="article-thumb" /> 

<div id="stack-controls-disabled" class="list-controls">
	<div class="float-right">
		<a href="articles.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2"> Cancel</a>
		<button type="submit" name="submit" value="<?php echo ($action == 'edit') ? 'Save' : 'Publish';?>" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2"><i class="mi-check"></i> <?php echo ($action == 'edit') ? 'Save' : 'Publish';?></button>
	</div>
</div><!-- #list-controls -->
	
</form>
</div><!-- .content -->
<?php
include('footer.php');