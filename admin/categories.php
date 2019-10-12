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
$category_type = (@strtolower($_GET['type']) == 'article') ? 'article' : 'video';
$category_type = (@strtolower($_GET['type']) == 'genre') ? 'genre' : $category_type;
$_page_title = ($category_type == 'article') ? 'Article categories' : 'Video categories';
$_page_title = ($category_type == 'genre') ? 'Genres' : $_page_title;

include('header.php');

$sql_table = ($category_type == 'article') ? 'art_categories' : 'pm_categories';
$sql_table = ($category_type == 'genre') ? 'pm_genres' : $sql_table;

$categories_dropdown_options = array('db_table' => $sql_table,
									 'first_option_text' => '- Root -', 
									 'selected' => $_POST['parent_id'], 
									 'attr_name' => 'parent_id',
									 'attr_class' => 'custom-select mb-1',
									 'spacer' => '&mdash;',
									 );

$action = $_GET['do'];
if ( ! in_array($action, array('update', 'new', 'delete', 'move', 'organize')) )
{
	$action = 'new';	//	default action
}

if ('' != $_POST['update'] || '' != $_POST['submit']) 
{
	if ($action == 'new' || $_POST['submit'] == 'Add category')
	{
		$result = insert_category($_POST, $category_type);
	}
	else if ($action == 'update')
	{
		$result = update_category((int) $_POST['id'], $_POST, $category_type);
	}
	
	if ($result['type'] == 'error')
	{
		$info_msg = pm_alert_error($result['msg']);
	}
	else
	{
		$_POST = array();
		$info_msg = pm_alert_success($result['msg']);
	}
}

if ($_GET['move'] != '' && $_GET['id'] != '')
{
	$id = (int) $_GET['id'];
	
	if ($id > 0)
	{
		$categories = load_categories(array('db_table' => $sql_table));
		
		$limit = 0;
		$is_parent = false;
		$is_child = false;

		if ($categories[$id]['parent_id'] == 0)
		{
			foreach ($categories as $c_id => $c_arr)
			{
				if ($c_arr['parent_id'] == 0)
				{
					$is_parent = true;
					$limit++;
				}
			}
		}
		else
		{
			foreach ($categories as $c_id => $c_arr)
			{
				if ($c_arr['parent_id'] == $categories[$id]['parent_id'])
				{
					$is_child = true;
					$limit++;
				}
			}
		}
		
		$current_position = $categories[$id]['position'];
		$prev_cat_id = $next_cat_id = 0;
		
		// find neighbours 
		foreach ($categories as $c_id => $c_arr)
		{
			if ($c_arr['position'] == ($current_position - 1) && $c_arr['parent_id'] == $categories[$id]['parent_id'])
			{
				$prev_cat_id = $c_id;
			}
			
			if ($c_arr['position'] == ($current_position + 1) && $c_arr['parent_id'] == $categories[$id]['parent_id'])
			{
				$next_cat_id = $c_id;
			}
		}
		
		switch ($_GET['move'])
		{
			case 'up':
				
				if ($current_position > 1 && $current_position <= $limit && $prev_cat_id)
				{
					$sql_1 = "UPDATE $sql_table
							   SET position = '". ($categories[$prev_cat_id]['position'] + 1) ."' 
							 WHERE id = '". $prev_cat_id ."'";
					$sql_2 = "UPDATE $sql_table
							   SET position = '". ($categories[$id]['position'] - 1) ."' 
							 WHERE id = '". $id ."'";
					
					$categories[$prev_cat_id]['position']++;
					$categories[$id]['position']--;
				}
				
			break;
	
			case 'down':
				
				if ($current_position >= 1 && $current_position < $limit && $next_cat_id)
				{
					$sql_1 = "UPDATE $sql_table
							   SET position = '". ($categories[$id]['position'] + 1) ."' 
							 WHERE id = '". $id ."'";
					
					$sql_2 = "UPDATE $sql_table
							   SET position = '". ($categories[$next_cat_id]['position'] - 1) ."' 
							 WHERE id = '". $next_cat_id ."'";
					
					$categories[$id]['position']++;
					$categories[$next_cat_id]['position']--;
				}
				
			break;
		}

		if ($sql_1 != '' && $sql_2 != '')
		{
			if ( ! ($result = mysql_query($sql_1)))
			{
				$info_msg = pm_alert_error('A problem was encountered while updating your database!<br />MySQL returned: '. mysql_error());
			}
			else
			{
				if ( ! ($result = mysql_query($sql_2)))
				{
					$info_msg = pm_alert_error('A problem was encountered while updating your database!<br />MySQL returned: '. mysql_error());
				}
			}
			
			load_categories(array('db_table' => $sql_table, 'reload' => true));
		}
	}
	
	if ($info_msg == '')
	{
		echo '<meta http-equiv="refresh" content="0;URL=categories.php?type='. $category_type .'&id='. $id .'&moved='. $_GET['move'] .'" />';
		exit();
	}
	
}

$categories = load_categories(array('db_table' => $sql_table));
$total_categories = pm_count($categories);

if ($category_type == 'video' || $category_type == 'genre')
{
	$featured_categories = ($config['homepage_featured_categories'] != '') ? unserialize($config['homepage_featured_categories']) : array();
	$featured_genres = ($config['featured_genres'] != '') ? unserialize($config['featured_genres']) : array();
}
?>
<!-- Main content -->
<div class="content-wrapper">

<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
			<h4><span class="font-weight-semibold"><?php echo ($category_type == 'article') ? 'Article Categories' : (($category_type == 'genre') ? 'Genres' : 'Video Categories'); ?></span> <a href="#modal_add_category" class="badge badge-success badge-addnew font-size-sm ml-2" data-toggle="modal">+ add new</a></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<h5 class="font-weight-semibold mb-0 text-center"><?php echo pm_number_format($total_categories); ?></h5>
					<span class="text-muted font-size-sm"><?php echo ($category_type == 'genre') ? 'genres' : 'categories'; ?></span>
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
					<?php elseif ($category_type == 'article') : ?>
					<a href="articles.php" class="breadcrumb-item">Articles</a>
					<?php elseif ($category_type == 'genre') : ?>
					<a href="series.php" class="breadcrumb-item">Series</a>
					<?php endif; ?>
					
					<a href="categories.php?type=<?php echo $category_type;?>" class="breadcrumb-item"><span class="breadcrumb-item active"><?php echo $_page_title; ?></span></a>
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
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<?php if ($category_type != 'genre') : ?>
						<p>Categories are separated into Video and Article categories. The Article Categories appear only if the Article Module is enabled.</p>
						<p>At the top of the page there's a form to quickly add new categories as needed. Below you'll find a list of your current category tree. Categories can be moved up or down depending on the desired hierarchy.<br />Editing existing categories can be made without leaving the page. Simply hover the category to edit.</p>
						<p>Adding a new category requires a &quot;slug&quot;, which is the URL-friendly version of the category name. Categories can be placed in the &quot;root&quot; or in an existing category making it a subcategory.</p>
						<hr />
						<?php endif; ?>
						<p>Clicking on the <i class="icon icon-star-full2"></i> icon next to each category will create a list of recent videos from that category on your homepage.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content content-full-width">

	
	<?php 
	if ($_GET['moved'] != '')
	{
		echo pm_alert_success('Category <strong>'. $_video_categories[$_GET['id']]['name'] .'</strong> moved '. $_GET['moved'] .' a level.', false, true);
	}
	
	if ($_GET['organized'] != '')
	{
		echo pm_alert_success('The new order was saved.', false, true);
	}
	?>

	<?php 
	if ($action == 'organize') {
		$load_jquery_ui = true;
		$load_sortable = true;
		echo pm_alert_info('Drag and drop the items to achieve the desired structure. <strong>Save</strong> the changes once you\'re done.');
	}
	?>

	<?php echo $info_msg; ?>

	<?php  if ( ! $config['mod_article'] && $category_type == 'article') : 
	echo pm_alert_info('The Article Module is currently disabled. You can enable it from <a href="settings.php?view=t6">Settings / Available Modules</a>.');
	endif; ?>

	<?php  if ( !  _MOD_SERIES && $category_type == 'genre') :
	echo pm_alert_info('The Series Module is currently disabled. You can enable it from the <You can enable it from <a href="settings.php?view=t6">Settings / Available Modules</a>.');
	endif; ?>

	<div id="display_result" style="display:none;"></div>


	<?php if ($total_categories > 1) : ?>
	<div class="float-right m-3 d-none d-md-block">
		<a href="categories.php?type=<?php echo $category_type; ?>&do=organize" class="btn btn-sm btn-labeled btn-labeled-left btn-outline bg-blue border-blue btn-blue text-blue">Reposition <b class="bg-transparent"><i class="icon-tree6"></i></b></a>
	</div> <!--.row with filters -->
	<div class="clearfix"></div>
	<?php endif; ?>


	<div class="card card-blanche">
	<?php 
	if ($action == 'organize') : ?>

	<div class="card-body">
	<?php
		if ($total_categories > 0)
		{
			echo a_category_sortable_list($categories);
		}
		else
		{
			echo ($category_type == 'genre') 
				? pm_alert_error('Please <a href="edit-category.php?do=add&type='. $category_type .'">create a genre</a> first.')
				: pm_alert_error('Please <a href="edit-category.php?do=add&type='. $category_type .'">create a category</a> first.');
		}
	?>

		<div class="datatable-footer mt-3">
			<div id="stack-controls" class="list-controls">
				<div class="float-right">
					<div class="btn-group"><a href="categories.php?type=<?php echo $category_type;?>" class="btn btn-outline bg-grey text-grey-800 btn-icon" title="Go back">Cancel</a></div>
					<div class="btn-group"><a href="#" class="btn btn-success" id="organize-category-save-btn">Save</a></div>
				</div>
			</div><!-- #list-controls -->
		</div>
	</div><!--.card-body-->
	<?php else : ?>

	</div><!--.card-->

	<div class="datatable-scroll">
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables pm-tables-inline-edit tablesorter">
		<thead>
			<tr>
				<?php if ($category_type == 'video' || $category_type == 'genre') : ?>
				<th width="10" style="width: 20px;">Featured</th>
				<?php endif;?>
				<!-- <th width="3%" class="text-center">ID</th> -->
				<th><?php echo ($category_type == 'genre') ? 'Genre' : 'Category'; ?> Name</th>
				<th width="40%" style="min-width: 200px;">URL Slug</th>
				<!-- <th width="15%" class="text-center">Parent Category</th> -->
				<th width="5%" class="text-center"><?php echo ($category_type == 'article') ? 'Articles' : (($category_type == 'genre') ? 'Series' : 'Videos'); ?></th>
				<th width="5%" class="text-center d-none d-md-table-cell">Position</th>
				<th width="10%" align="center" style="width: 120px;" class="text-center">Action</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$args = array('page' => 'categories.php?type='. $category_type,
						  'form_action' => 'categories.php?type='. $category_type .'&do=update',
						  'type' => $category_type
						);		
			echo a_category_table_body($categories, $args);	
			?>
		 </tbody>
		</table>
	</div>
	<?php echo csrfguard_form('_admin_catmanager'); ?>
	<?php endif; ?>

</div><!--.card-->	 
</div>
<!-- /content area -->
<?php
include('modals/modal-create-category.php');

include('footer.php');