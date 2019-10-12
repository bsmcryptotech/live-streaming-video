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

$showm = 'mod_pages';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_scrolltofixed = 1;
$load_tagsinput = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$load_fileinput_upload = 1;

$_page_title = 'Create new page';

$action = $_GET['do'];
if ( ! in_array($action, array('edit', 'new', 'delete')) )
{
	$action = 'new';    //  default action
}
if ($action == 'edit')
{
	$_page_title = 'Edit page';
}
include('header.php');
?>
<?php

$inputs = array();
$info_msg = '';

if ('' != $_POST['submit'])
{
	$page_id = ($_POST['id'] != '') ? (int) $_POST['id'] : (int) $_GET['id'];

	$_POST['page_title'] = after_post_filter($_POST['page_title']);
	$_POST['title'] = $_POST['page_title'];
	
	if ($action == 'new')
	{
		$result = insert_new_page($_POST);
		$page_id = $result['page_id'];
		$action = 'edit';
		$inputs = $_POST;
	}
	else if ($action == 'edit')
	{
		$result = update_page($_POST['id'], $_POST);
	}
	
	if ($result['type'] == 'error')
	{
		$info_msg = pm_alert_error($result['msg']);
	}
	else
	{
		$info_msg = pm_alert_success('<strong>'. $result['msg'] .'</strong> <a href="'. _URL .'/page.php?p='. $page_id .'" target="_blank">See how it looks</a>.');
	}   
}

if ($action == 'edit')
{
	if ( ! $page_id)
	{
		$page_id = (int) $_GET['id'];
	}
	
	if ($page_id == 0)
	{
		$action = 'new';
		$inputs = array();
		$inputs['status'] = 1;
		$inputs['author'] = $userdata['id'];
	}
	else
	{
		$inputs = get_page($page_id);
	}
}
else if ($action == 'new')
{
	if ('' != $_POST['submit'])
	{
		$inputs = $_POST;
	}
}

//  Filter some fields before output
$inputs['title'] = pre_post_filter($inputs['title']);
?>

<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper page-header-edit"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title; ?></span>
				<?php if($action != 'new') : ?>
				<a href="<?php echo _URL .'/page.php?p='. $page_id; ?>" target="_blank" data-popup="tooltip" data-original-title="Preview"><small class="text-muted"><?php echo htmlspecialchars($inputs['title']); ?> <i class="mi-open-in-new"></i></small></a>
				<?php endif; ?>
				</h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
						<a href="pages.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2"> Cancel</a>
						<button type="submit" name="submit" value="<?php echo ($action == 'add') ? 'Save' : 'Publish';?>" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.forms[0].submit({return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')});" form="write_page"><i class="mi-check"></i> <?php echo ($action == 'edit') ? 'Save' : 'Publish';?></button>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="pages.php" class="breadcrumb-item">Pages</a>
					<span class="breadcrumb-item active"><?php echo $_page_title; ?></span>
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
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">Navigation</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>After choosing a title for your page you can use the WYSIWYG editor to design your content. Images can be uploaded as needed using the right hand side button contained within the editor. We mention this because on lower resolutions, such as those found on notebooks, the button might not appear.</p>
						<p>Pages can be saved as drafts and remain unpublished by choosing the right &quot;Status&quot; option.<br />
						Permalink simply indicates how the URL will look in the address bar. You will see a live preview below the input form.<br />
						The meta keywords and description fields are useful for SEO purposes.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content content-edit-page">

<?php echo ($info_msg); ?>

<div id="display_result" style="display:none;"></div>

<form name="write_page" id="write_page" method="post" action="edit-page.php?do=<?php echo $action; ?>&id=<?php echo $page_id;?>" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted).')">
<div class="row">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Title and Description</h6>
				<div class="header-elements">
					<div class="list-icons">
						
					</div>
				</div>
			</div>
			<div class="card-body">
				<input name="page_title" type="text" class="form-control form-required permalink-make font-weight-semibold font-size-lg" placeholder="Page title" value="<?php echo htmlspecialchars($inputs['title']); ?>" />
	
				<div class="permalink-field mt-2 mb-2">
				<?php if ($inputs['page_name'] == '404') : ?>
				<input name="page_name" id="item-slug" type="hidden" value="404" />
				<?php else : ?>
					<?php if (_SEOMOD) : ?>
					<strong>Permalink:</strong> <?php echo _URL .'/pages/';?><input type="text" name="page_name" class="permalink-input" value="<?php echo urldecode($inputs['page_name']);?>" /><?php echo   (($r['uniq_id'] == '') ? '' : $r['uniq_id']) .'.html';?>
					<?php else : ?>
					<input type="hidden" name="page_name" class="permalink-input" value="<?php echo $inputs['page_name']; ?>" />
					<?php endif; ?>
				<?php endif; ?>
				</div>

				<div id="textarea-dropzone" class="upload-file-dropzone">

					<div class="fileinput-button">
						<input type="file" name="file" id="upload-file-wysiwyg-btn" class="file-input file-input-custom form-control form-control-sm alpha-grey" multiple="multiple" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload images" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" data-fouc />
					</div>

					<textarea name="content" rows="20" cols="100" id="textarea-WYSIWYG" class="tinymce"><?php echo $inputs['content']; ?></textarea>
					<span class="autosave-message"></span>
				</div>					
			</div>
		</div><!--.card-->
	</div><!-- col-md-9 -->
	<div class="col-md-4">
		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Meta Data</h6>
				<div class="header-elements">
					<div class="list-icons">
					</div>
				</div>
			</div>
			<table class="table pm-tables-settings">
			<tr>
				<td class="w-25">Meta Keywords</td>
				<td>
				<input type="text" name="meta_keywords" size="45" class="form-control" value="<?php echo $inputs['meta_keywords']; ?>" />
				</td>
				</tr>
			<tr>
			<tr>
				<td>Meta Description</td>
				<td>
				<textarea name="meta_description" rows="3" class="form-control" /><?php echo $inputs['meta_description']; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>Page Status</td>
				<td>
					<label class="checkbox inline mr-2"><input type="radio" name="status" id="restricted" value="1" <?php if ($inputs['status'] == '1' || !$inputs['status']) echo 'checked="checked"'; ?> /> Published</label>
					<label class="checkbox inline mr-2"><input type="radio" name="status" id="restricted" value="0" <?php if ($inputs['status'] == '0') echo 'checked="checked"'; ?> /> Draft</label>
				</td>
			</tr>
			<tr>
				<td>Show in footer</td> 
				<td>
					<label class="checkbox inline mr-2"><input type="radio" name="showinmenu" id="show_in_menu" value="1" <?php if ($inputs['showinmenu'] == '1' || !$inputs['showinmenu']) echo 'checked="checked"'; ?> /> Yes</label>
					<label class="checkbox inline mr-2"><input type="radio" name="showinmenu" id="show_in_menu" value="0" <?php if ($inputs['showinmenu'] == '0') echo 'checked="checked"'; ?> /> No</label> 
				</td>
			</tr>
			</table>
		</div><!--.card-->
	</div>
</div>

<input type="hidden" name="author" value="<?php  echo $inputs['author'];?>" />
<input type="hidden" name="id" value="<?php echo $inputs['id'];?>" />
<input type="hidden" name="p" value="upload" /> 
<input type="hidden" name="do" value="upload-image" />
<!-- .permalink-input filed added to prevent errors in JS -->
<input type="hidden" name="permalink-input-hack" class="permalink-input" value="" />

<div id="stack-controls-disabled" class="list-controls">
<div class="float-right">
	<a href="pages.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2"> Cancel</a>
	<button type="submit" name="submit" value="<?php echo ($action == 'add') ? 'Save' : 'Publish';?>" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2"><i class="mi-check"></i> <?php echo ($action == 'edit') ? 'Save' : 'Publish';?></button>
</div>
</div><!-- #list-controls -->
	
</form>

</div>
<!-- /content area -->
<?php
include('footer.php');