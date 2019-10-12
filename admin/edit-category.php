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
$showm = '3';
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$load_fileinput_upload = 1;

$_page_title = 'Edit category';
include('header.php');

$mode = ($_GET['mode'] != '') ? $_GET['mode'] : 'add';
$category_type = $_GET['type'];
$category_type = ($category_type == '') ? 'video' : $category_type;
$category_id = (int) $_GET['id'];

$_page_title = ($category_type == 'genre' && $mode == 'edit') ? 'Edit Genre' : 'Edit category';
$_page_title = ($category_type == 'genre' && $mode == 'add') ? 'Add Genre' : 'Add new category';

$form_data = $errors = array();
$success_add = $success_edit = $show_footer_early = false;
$sql_table = ($category_type == 'article') ? 'art_categories' : 'pm_categories';
$sql_table = ($category_type == 'genre') ? 'pm_genres' : $sql_table;

$categories_dropdown_options = array('first_option_text' => '- Root -', 
									 'attr_class' => 'category_dropdown span12',
									 'spacer' => '&mdash;',
									 'selected' => 0,
									 'db_table' => $sql_table
									);

$all_categories = load_categories(array('db_table' => $sql_table, 'with_image' => true));
$category_data = $all_categories[$category_id];

if ($mode == 'edit' && empty($category_id))
{
	$errors[] = ($category_type == 'genre') ? 'Invalid genre ID.' : 'Invalid category ID.';
}
else if ($mode == 'edit')
{
	$form_data = $category_data;
	$categories_dropdown_options['selected'] = $category_data['parent_id'];
}

if ($_POST['save'] != '' && pm_count($errors) == 0)
{
	foreach ($_POST as $k => $v)
	{
		$_POST[$k] = stripslashes( trim($v) );
	}

	switch ($mode)
	{
		case 'add':
			
			$_POST['name'] = str_replace('&amp;', '"', $_POST['name']);
			$_POST['tag'] = sanitize_title(trim($_POST['tag']));
			
			$result = insert_category($_POST, $category_type);
			
			if ($result['type'] == 'error')
			{
				$errors[] = $result['msg'];
			}
			else
			{
				$success_add = true;
				$show_footer_early = true;
			}

		break;
		
		case 'edit':
			
			$_POST['name'] = str_replace('&amp;', '"', $_POST['name']);
			$_POST['tag'] = sanitize_title(trim($_POST['tag']));
			$_POST['old_tag'] = $category_data['tag'];
 
			$result = update_category($category_id, $_POST, $category_type);
			
			if ($result['type'] == 'error')
			{
				$errors[] = $result['msg'];
			}
			else
			{
				$success_edit = true;
				$show_footer_early = false;
			}

		break;
	}
	
	$form_data = $_POST;
	$categories_dropdown_options['selected'] = $form_data['category'];
}
?>
<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper page-header-edit"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<?php if ($mode == 'add') : ?>
				<h4><span class="font-weight-semibold">Add New</span></h4> 
				<?php else : ?>
				<h4>
				<span class="font-weight-semibold">Edit <?php echo ($category_type == 'genre') ? 'Genre' : ''; ?>

				<?php if ($category_type == 'genre') : ?>
				<a href="<?php echo make_link('genre', array('tag' => $category_data['tag'])); ?>" target="_blank" data-popup="tooltip" data-original-title="Preview"><small class="text-muted"><?php echo $form_data['name'];?> <i class="mi-open-in-new"></i></small></a>
				<?php elseif($category_type == 'article') : ?>
				<a href="<?php echo art_make_link('category', array('tag' => $category_data['tag'])); ?>" target="_blank" data-popup="tooltip" data-original-title="Preview"><small class="text-muted"><?php echo $form_data['name'];?> <i class="mi-open-in-new"></i></small></a>
				<?php else : ?>
				<a href="<?php echo make_link('category', array('tag' => $category_data['tag'])); ?>" target="_blank" data-popup="tooltip" data-original-title="Preview"><small class="text-muted"><?php echo $form_data['name'];?> <i class="mi-open-in-new"></i></small></a>
				<?php endif; ?>

				</span>
				</h4>
				<?php endif; ?>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<a href="categories.php?type=<?php echo $category_type;?>" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2 pm-show-loader"> Cancel</a>
					<button type="submit" name="save" value="<?php echo ($mode == 'add') ? 'Submit' : 'Save';?>" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.forms[0].submit({return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')});" form="edit-category"><i class="mi-check"></i> <?php echo ($mode == 'add') ? 'Submit' : 'Save';?></button>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>

					<?php if ($category_type == 'video') : ?>
					<a href="videos.php" class="breadcrumb-item">Videos</a>
					<a href="categories.php?type=video" class="breadcrumb-item">Categories</a>

					<?php elseif ($category_type == 'article') : ?>
					<a href="articles.php" class="breadcrumb-item">Articles</a>
					<a href="categories.php?type=article" class="breadcrumb-item">Categories</a>

					<?php elseif ($category_type == 'genre') : ?>
					<a href="series.php" class="breadcrumb-item">Series</a>
					<a href="categories.php?type=genre" class="breadcrumb-item">Genres</a>
					<?php endif; ?>

					<?php if ($mode == 'add') : ?>
					<span class="breadcrumb-item active">Add New</span>
					<?php else : ?>
					<span class="breadcrumb-item active">Editing <?php echo $form_data['name'];?></span>
					<?php endif; ?>

				</div>
			</div>
			<div class="header-elements d-none d-md-block">
				<div class="breadcrumb justify-content-center">
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->
</div><!--.page-header-wrapper-->

	<!-- Content area -->
	<div class="content content-edit-page">

	<?php 
	if (($errors_count = pm_count($errors)) > 0)
	{
		echo ($errors_count > 1) ? pm_alert_error($errors) : pm_alert_error($errors[0]);
	} 
	?>
	
	<?php if ($success_add) : ?>
	<?php echo pm_alert_success((($category_type == 'genre') ? 'Genre' : 'Category') .' <strong>'. htmlentities($_POST['name']) .'</strong> added successfully.'); ?>

	<?php if ($category_type == 'video') : ?>
	<a href="categories.php?type=video" class="btn btn-secondary">&larr; Video Categories</a>
	<a href="edit-category.php?mode=add&type=video" class="btn btn-success">Add another video category &rarr;</a>
	<?php elseif ($category_type == 'article') : ?>
	<a href="categories.php?type=article" class="btn btn-secondary">&larr; Article Categories</a>
	<a href="edit-category.php?mode=add&type=article" class="btn btn-success">Add another article category &rarr;</a>
	<?php elseif ($category_type == 'genre') : ?>
	<a href="categories.php?type=genre" class="btn btn-secondary">&larr; Genre</a>
	<a href="edit-category.php?mode=add&type=genre" class="btn btn-success">Add another genre &rarr;</a>

	<?php endif; ?>
	
	<?php if ($show_footer_early) : ?>
		</div><!-- .content -->
	<?php
	include('footer.php');
	exit();
	endif; // show_footer_early
	?>
	<?php endif; //if ($success_add) : ?>
	
	<?php if ($success_edit) : ?>
	<?php echo pm_alert_success((($category_type == 'genre') ? 'Genre' : 'Category') .' updated.'); ?>
	<?php endif; ?>
	<?php if ($show_footer_early) : ?>
		</div><!-- .content -->
	<?php
	include('footer.php');
	exit();
	endif; // show_footer_early
	?>

<form name="edit-category" id="edit-category" method="POST" action="edit-category.php?mode=<?php echo $mode; ?>&type=<?php echo $category_type; echo ($mode == 'edit') ? '&id='. $category_id : '';?>" class="form-horizontal" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')">
<div class="row">
	<div class="col-md-9">
		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Name and Description</h6>
				<div class="header-elements">
					<div class="list-icons">
					</div>
				</div>
			</div>
			<div class="card-body">
				<input name="name" type="text" class="form-control form-required permalink-make" placeholder="Name" value="<?php echo str_replace('"', '&quot;', $form_data['name']); ?>" />
	
				<div class="permalink-field mt-2 mb-2">
					<?php if (_SEOMOD) : ?>
						<?php if ($category_type == 'genre') : ?>
							<input type="hidden" name="tag" class="permalink-input" value="<?php echo $form_data['tag']; ?>" />
							<?php else : ?>
							<strong>Permalink:</strong> <?php echo _URL .'/browse-';?><input type="text" name="tag" class="permalink-input" value="<?php echo $form_data['tag']; ?>" />-1-date.html
						<?php endif; ?>
					<?php else : ?>
					<input type="hidden" name="tag" class="permalink-input" value="<?php echo $form_data['tag']; ?>" />
					<?php endif; ?>
				</div>

				<div id="textarea-dropzone" class="upload-file-dropzone">
					<div class="fileinput-button">
						<input type="file" name="file" id="upload-file-wysiwyg-btn" class="file-input file-input-custom form-control form-control-sm alpha-grey" multiple="multiple" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload images" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" data-fouc />
					</div>

					<textarea name="description" rows="8" cols="100" id="textarea-WYSIWYG" class="tinymce"><?php echo $form_data['description']; ?></textarea>
					<span class="autosave-message"></span>
				</div>			
			</div>

		</div><!--.card-->
	</div><!-- col-md-9 -->
	<div class="col-md-3">


		<?php if ($category_type == 'video'): ?>
		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Thumbnail</h6>
				<div class="header-elements">
					<div class="list-icons">
						<span class="fileinput-button">
							<input type="file" name="file" id="upload-category-image-btn" class="file-input form-control form-control-sm alpha-grey" data-show-caption="false" data-show-upload="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Update" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" />
						</span>
					</div>
				</div>
			</div>
			<div class="card-body upload-file-dropzone" id="video-thumb-dropzone">
				<div id="category-image-container">
										
					<?php if ($form_data['image'] == '') : ?>
					<div class="d-block justify-content-end text-center rounded bt-slate alpha-slate" style="min-height: 150px;">
						<div>
							<i class="icon-image2 icon-3x text-default border-slate alpha-slate p-3 mt-1 mt-1"></i>
							<h5>No category thumbnail</h5>
						</div>
					</div>

					<?php else : ?>
					<a href="#"><img src="<?php echo _THUMBS_DIR . $form_data['image']; ?>?cache_buster=<?php echo $time_now;?>" class="img-fluid" /></a>
					<?php endif; ?>
					<input type="hidden" name="image" value="<?php echo $form_data['image']; ?>" />
				</div>

				<div id="show-opt-thumb" class="collapse mt-3 p-3">
					<div class="input-group">
					<input type="text" name="yt_thumb" value="<?php echo $r['yt_thumb']; ?>" class="form-control col-md-10" placeholder="http://" /> <span class="input-group-text bg-transparent border-0"><i class="mi-info-outline" rel="tooltip" data-position="top" title="Change this URL to replace the existing thumbnail."></i></span>
					<input type="hidden" name="yt_thumb_old" value="<?php echo $r['yt_thumb']; ?>" class="form-control col-md-12" placeholder="http://" />
					</div>
				</div>
			</div>
		</div><!--.card-->
		<?php endif; ?>

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
				<td class="w-25">Parent Category</td>
				<td>
					<?php echo categories_dropdown($categories_dropdown_options);?>
				</td>
				</tr>
			<tr>
			<?php if (! _SEOMOD) : ?>
			<tr>
				<td class="w-25">URL Slug</td>
				<td>
				<input type="text" name="tag" class="form-control form-required permalink-input " value="<?php echo $form_data['tag']; ?>" />
				</td>
				</tr>
			<tr>
			<?php endif; ?>
			<tr>
				<td class="w-25">Meta Title</td>
				<td>
				<input type="text" name="meta_title" size="45" class="form-control" value="<?php echo str_replace('"', '&quot;', $form_data['meta_title']);?>" />
				</td>
				</tr>
			<tr>
			<tr>
				<td class="w-25">Meta Keywords</td>
				<td>
				<input type="text" name="meta_keywords" size="45" class="form-control" value="<?php echo str_replace('"', '&quot;', $form_data['meta_keywords']);?>" />
				</td>
				</tr>
			<tr>
			<tr>
				<td>Meta Description</td>
				<td>
				<textarea name="meta_description" rows="3" class="form-control" /><?php echo str_replace('"', '&quot;', $form_data['meta_description']);?></textarea>
				</td>
			</tr>
			</table>
		</div><!--.card-->


	</div><!-- col-md-3 -->
</div>
	  
<div id="stack-controls-disabled" class="list-controls">
	<div class="float-right">
		<a href="categories.php?type=<?php echo $category_type;?>" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2"> Cancel</a>
		<button type="submit" name="save" value="<?php echo ($mode == 'add') ? 'Submit' : 'Save';?>" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2"><i class="mi-check"></i> <?php echo ($mode == 'add') ? 'Submit' : 'Save';?></button>
	</div>
</div><!-- #list-controls -->

<input type="hidden" name="cat_id" value="<?php echo $category_id; ?>" />
<input type="hidden" name="upload-type" value="category-image" />
<input type="hidden" name="p" value="upload" /> 
<input type="hidden" name="do" value="upload-image" />
<!-- .permalink-input filed added to prevent errors in JS -->
<input type="hidden" name="permalink-input-hack" class="permalink-input" value="" />
</form>

<?php echo csrfguard_form('_admin_catmanager'); ?>
	</div><!-- .content -->
<?php
include('footer.php');