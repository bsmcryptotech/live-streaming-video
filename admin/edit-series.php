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
// | Copyright: (c) 2004-2019 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = 'mod_series';

$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;

$action = $_GET['do'];
if ( ! in_array($action, array('edit', 'new', 'delete')) )
{
	$action = 'new';    //  default action
}

$_page_title = ($action == 'new') ? 'Add new series' : 'Edit series';
$is_new_series = ($_GET['do'] == 'new') ? true : false;
include('header.php');
?>

<!-- Main content -->
<div class="content-wrapper">
<?php 

$inputs = array();

if ($action == 'edit')
{
	$series_id = (int) $_GET['series_id'];
	if ($series_id == 0)
	{
		$action = 'new';
		$inputs = array();
	}
	else
	{
		$inputs = get_series($series_id);
	}
}
else if ($action == 'new')
{
	$inputs = ('' != $_POST['submit']) ? $_POST : array();
}

if ('' != $_POST['submit'])
{
	$_POST['title'] = after_post_filter($_POST['title']);
	$_POST['tags'] = after_post_filter($_POST['tags']);

	if ($action == 'new')
	{
		$result = insert_new_series($_POST);
		$inputs = get_series($result['series_id']);
	}
	else if ($action == 'edit')
	{
		$result = update_series($_POST['series_id'], $_POST);
	}

	if ($result['type'] == 'error')
	{
		$info_msg = pm_alert_error($result['msg']);
	}
	else
	{
		if ($action == 'new')
		{
			$info_msg = pm_alert_success('<strong>'. $result['msg'] .'</strong> ('.$_POST['title'].'). <a href="edit-series.php?do=new">Create a new series</a> or <a href="series.php">return to manage series</a>.');
		}
		else
		{
			$info_msg = ($result['type'] != 'ok') 
				? pm_alert_error($result['msg'])
				: pm_alert_success($result['msg']);
		}
	}
}

$genres = get_genres();
//  Filter some fields before output
$inputs['title'] = pre_post_filter($inputs['title']);
$inputs['tags'] = pre_post_filter($inputs['tags']);
?>

<div class="page-header-wrapper page-header-edit"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4>
				<span class="font-weight-semibold"><?php echo $_page_title; ?></span>
				<?php if ($action == 'edit') : ?>
					<a href="<?php echo _URL .'/series.php?s='. $inputs['series_slug']; ?>" target="_blank" data-popup="tooltip" data-original-title="Preview"><small class="text-muted"><?php echo htmlspecialchars($inputs['title']); ?> <i class="mi-open-in-new"></i></small></a> 
				<?php endif; ?>
				</h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<a href="series.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2 pm-show-loader">Cancel</a>
					<button type="submit" name="submit" value="Save" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.forms[0].submit({return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')});" form="edit-series"><i class="mi-check"></i> Save</button>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="series.php" class="breadcrumb-item">Series</a>
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

<?php
echo $info_msg;
?>

<div id="display_result" style="display:none;"></div>


<form name="edit-series" id="edit-series" method="post" action="edit-series.php?do=<?php echo $action; ?>&series_id=<?php echo $_GET['series_id'];?>" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted).')">
<div class="row">
	<div class="col-sm-12 col-md-9">
		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Title and Description</h6>
				<div class="header-elements">
				</div>
			</div>
			<div class="card-body">
				<input name="title" type="text" class="form-control form-required permalink-make font-weight-semibold font-size-lg" placeholder="Series name" value="<?php echo htmlspecialchars($inputs['title']); ?>" />
				<div class="permalink-field mt-2 mb-2">
					<?php if (_SEOMOD) : ?>
						<strong>Permalink:</strong> <?php echo _URL .'/series/';?><input class="permalink-input" type="text" name="series_slug" value="<?php echo urldecode($inputs['series_slug']);?>" />
						<input type="hidden" name="current_series_slug" value="<?php echo urldecode($inputs['series_slug']);?>" />
					<?php endif; ?>	
				</div>

				<div id="textarea-dropzone" class="upload-file-dropzone">

					<div class="fileinput-button">
						<input type="file" name="file" id="upload-file-wysiwyg-btn" class="file-input file-input-custom form-control form-control-sm alpha-grey" multiple="multiple" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Upload images" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" data-fouc />
					</div>

					<textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce"><?php echo $inputs['description']; ?></textarea>
					<span class="autosave-message"></span>
				</div>					
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Series Details</h6>
			</div>
				<ul class="nav nav-tabs nav-tabs-bottom">

					<li class="nav-item"><a href="#badge-tab0" class="nav-link active" data-toggle="tab">Seasons <span class="<?php echo ($inputs['seasons'] != '') ? 'badge badge-flat border-primary text-primary-600 border-0 alpha-primary' : ''; ?>" id="value-seasons"><strong><?php echo $inputs['seasons']; ?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab1" class="nav-link" data-toggle="tab">Episodes <span class="<?php echo ($inputs['episodes'] != '') ? 'badge badge-flat border-primary text-primary-600 border-0 alpha-primary' : ''; ?>" id="value-episodes"><strong><?php echo $inputs['episodes']; ?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab2" class="nav-link" data-toggle="tab">Featured <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary <?php echo ($inputs['featured'] == 1) ? 'border-success text-success-600' : '';?>" id="value-featured"><strong><?php echo ($inputs['featured'] == 1) ? 'yes' : 'no';?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab3" class="nav-link" data-toggle="tab">Views <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-views"><strong><?php echo number_format($inputs['views']); ?></strong></span></a></li>
					<li class="nav-item"><a href="#badge-tab4" class="nav-link" data-toggle="tab">Release year <span class="badge badge-flat border-primary text-primary-600 border-0 alpha-primary" id="value-release_year"><strong><?php echo $inputs['release_year']; ?></strong></span></a></li>
				</ul>
			<div class="card-body pt-0">
				<div class="tab-content">
					<div class="tab-pane show active" id="badge-tab0">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Seasons Available:</div>
						<input type="hidden" name="seasons" value="<?php echo $inputs['seasons']; ?>" />
						<input type="text" name="seasons_input" id="seasons_input" value="<?php echo $inputs['seasons']; ?>" size="10" class="form-control form-control-sm form-required w-auto" data-popup="tooltip" data-placement="top" data-original-title="Total number of released seasons" />
					</div>

					<div class="tab-pane" id="badge-tab1">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Episodes Available:</div>
						<input type="hidden" name="episodes" value="<?php echo $inputs['episodes']; ?>" />
						<input type="text" name="episodes_input" id="episodes_input" value="<?php echo $inputs['episodes']; ?>" size="10" class="form-control form-control-sm w-auto" data-popup="tooltip" data-placement="top" data-original-title="Total number of released episodes" />
					</div>

					<div class="tab-pane" id="badge-tab2">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Mark as featured:</div>
						<label><input type="checkbox" name="featured" id="featured" value="1" <?php if($inputs['featured'] == 1) echo 'checked="checked"';?> /> Yes, mark as featured</label>
					</div>

					<div class="tab-pane" id="badge-tab3">
						<div class="text-uppercase font-size-xs font-weight-semibold mb-2">Views:</div>
						<input type="hidden" name="views" value="<?php echo $inputs['views'];?>" />
						<input type="text" name="views_input" id="site_views_input" value="<?php echo $inputs['views']; ?>" size="10" class="form-control w-auto" />
					</div>

					<div class="tab-pane" id="badge-tab4">
						<input type="hidden" name="release_year" value="<?php echo $inputs['release_year']; ?>" />
						<input type="text" name="release_year_input" id="release_year_input" value="<?php echo $inputs['release_year']; ?>" size="10" class="form-control form-control-sm w-auto" />
					</div>

				</div>
			</div>
		</div><!--.card-->

	</div><!--. col-md-9 main-->
	<div class="col-sm-12 col-md-3">

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Poster</h6>
				<div class="header-elements">
					<div class="list-icons">
						<span class="fileinput-button">
							<input type="file" name="file" id="upload-series-image-btn" class="file-input form-control form-control-sm alpha-grey" data-show-caption="false" data-show-upload="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Update" data-browse-class="btn btn-link btn-sm text-default font-weight-semibold" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" />
						</span>
					</div>
				</div>
			</div>
			<div class="card-body upload-file-dropzone" id="series-image-dropzone">
				<div id="series-image-container">
					<?php
					if (strpos($inputs['image'], 'http') !== 0 && strpos($inputs['image'], '//') !== 0 && $inputs['image'] != '')
					{
						$inputs['image'] = _THUMBS_DIR . $inputs['image'];
					}
					if (empty($inputs['image'])) : ?>

					<div class="d-block justify-content-end text-center rounded bt-slate alpha-slate" style="min-height: 150px;">
						<div>
							<i class="icon-image2 icon-3x text-default border-slate alpha-slate p-3 mt-1 mt-1"></i>
							<h5>No thumbnail</h5>
						</div>
					</div>

					<?php else : ?>
					<a href="#" id="show-thumb" data-toggle="collapse" data-target="#show-opt-thumb" rel="tooltip" title="Click here to change the thumbnail URL"><img src="<?php echo make_url_https($inputs['image']); ?>?cache_buster=<?php echo $time_now;?>" class="img-fluid img-poster" /></a>
					<div id="show-opt-thumb" class="collapse mt-1 p-3">
						<div class="input-group">
							<input type="text" name="image" value="<?php echo $inputs['image']; ?>" class="form-control col-md-10" placeholder="http://" /> <span class="input-group-text bg-transparent border-0"><i class="mi-info-outline" rel="tooltip" data-position="top" title="Change this URL to replace the existing thumbnail."></i></span>
							<input type="hidden" name="image_old" value="<?php echo $inputs['image']; ?>" class="form-control col-md-12" placeholder="http://" />
						</div>
					</div>
					<?php endif; ?>
				</div>

			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Genre</h6>
				<div class="header-elements">
					<div class="list-icons">
						<a href="#" id="inline_add_new_category" class="btn btn-sm btn-link text-default text-uppercase font-weight-semibold" data-popup="tooltip" data-html="true" data-original-title="Create a new genre" /><i class="mi-control-point"></i> Add</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div id="inline_add_new_category_form" class="collapse border-grey border-bottom pb-3 mb-3">
					<span id="add_category_response"></span>
					
					<input name="add_category_name" type="text" placeholder="Genre name" id="add_category_name" class="form-control mb-1" />
					<input name="add_category_slug" type="text" placeholder="Slug" class="form-control mb-1" data-popup="tooltip" data-html="true" data-original-title="Slugs are used in the URL (e.g. http://example.com/series/<strong>slug</strong>/) and can only contain numbers, letters, dashes and underscores." />
					<label class="text-muted">Create in (<em>optional</em>)</label>
					<?php 
					$genres_dropdown_options = array(
						'db_table' => 'pm_genres',
						'first_option_text' => '&ndash; Parent Genre &ndash;', 
						'first_option_value' => '-1',
						'attr_name' => 'add_category_parent_id',
						'attr_id' => '',
						'attr_class' => 'custom-select mb-1',
						'select_all_option' => true,
						'spacer' => '&mdash;'
					);
					echo categories_dropdown($genres_dropdown_options); 
					?>
					<button name="add_category_submit_btn" value="Add genre" class="btn btn-sm btn-success" />Add</button>

					<input type="hidden" name="genres_old" value="<?php echo $inputs['genre_ids_str'];?>"  />
				</div>

					<?php
					$genres_dropdown_options = array(
						'db_table' => 'pm_genres',
						'attr_name' => 'genres[]',
						'attr_id' => 'main_select_genres',
						'attr_class' => 'category_dropdown custom-select mb-1 form-required',
						'select_all_option' => false,
						'spacer' => '&mdash;',
						'selected' => $inputs['genre_ids'], 
						'other_attr' => 'multiple="multiple" size="3"',
						'option_attr_id' => 'check_ignore'
					);
					echo categories_dropdown($genres_dropdown_options);
					?>
			</div>
		</div><!--.card-->

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Meta Data</h6>
			</div>
			<table class="table pm-tables-settings">
			<tr>
				<td class="w-25">Meta Keywords</td>
				<td>
				<input type="text" name="meta_keywords" size="45" class="form-control" value="<?php echo $inputs['meta_keywords']; ?>" size="50" />
				</td>
				</tr>
			<tr>
			<tr>
				<td>Meta Description</td>
				<td>
				<textarea name="meta_description" rows="3" class="form-control" /><?php echo $inputs['meta_description']; ?></textarea>
				</td>
			</tr>
			</table>
		</div><!--.card-->
	</div><!--. col-md-3 sidebar-->
</div>

<input type="hidden" name="series_id" value="<?php echo $inputs['series_id'];?>" />
<input type="hidden" name="date_old" value="<?php echo $inputs['date'];?>" />
<input type="hidden" name="upload-type" value="series-image" />
<input type="hidden" name="p" value="upload" /> 
<input type="hidden" name="do" value="upload-image" />
	
<div id="stack-controls-disabled" class="list-controls">
	<div class="float-right">
		<a href="series.php" class="btn btn-sm btn-outline alpha-grey text-grey-400 border-transparent border-2 pm-show-loader">Cancel</a>
		<button type="submit" name="submit" value="Save" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2"><i class="mi-check"></i> Save</button>
	</div>
</div><!-- #list-controls -->
</form>
		
</div><!-- .content -->

<script type="text/javascript">
$(document).ready(function(){
	$('input[name="title"]').typeWatch({
		callback: function () {
			var title = $('input[name="title"]').val();
			var slug = $('input[name="series_slug"]').val();

			if (slug == '' && title != '') {
				$('#loading').show();
				$.ajax({
					type: "POST",
					url: "./admin-ajax.php",
					data: {
						"p": "series",
						"do": "generate-series-slug",
						"title": title
					},
					dataType: "html",
					success: function (data) {
						$('input[name="series_slug"]').val(data);
						$('#loading').hide();
					}
				});
			}
		},
		wait: 1000,
		highlight: true,
		captureLength: 2
	});
	$('input[name="series_slug"]').change(function () {
		if ($(this).val() != '') {
			$('#loading').show();

			$.ajax({
				type: "POST",
				url: "./admin-ajax.php",
				data: {
					"p": "series",
					"do": "generate-series-slug",
					"title": $(this).val()
				},
				dataType: "html",
				success: function (data) {
					$('input[name="series_slug"]').val(data);
					$('#loading').hide();
				}
			});
		}
	});
});
</script>
<?php
include('footer.php');