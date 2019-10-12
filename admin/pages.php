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
$load_scrolltofixed = 1;
$_page_title = 'Manage pages';
include('header.php');

$action = $_GET['do'];
if ( ! in_array($action, array('edit', 'new', 'delete')) )
{
	$action = 'new';	//	default action
}


$action	= (int) $_GET['action'];
$current_page	= (int) $_GET['page'];

if($current_page == 0)
	$current_page = 1;

$total_pages = 0;
$limit = get_admin_ui_prefs('pages_pp');
$from = $current_page * $limit - ($limit);

$filter = '';
$filters = array('public', 'private', 'mostviewed');
$filter_value = '';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
}

//	Batch delete
if ($_POST['submit'] == "Delete" && ! csrfguard_check_referer('_admin_pages'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['submit'] == "Delete")
{
	if(pm_count($_POST['checkboxes']) > 0)
	{
		$page_ids = array();
		foreach ($_POST['checkboxes'] as $k => $id)
		{
			$page_ids[] = (int) $id;
		}
		
		$result = mass_delete_pages($page_ids);
		
		if ($result['type'] == 'error')
		{
			$info_msg = pm_alert_error($result['msg']);
		}
		else
		{
			$info_msg = pm_alert_success($result['msg']);
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}

if ('' != $_POST['submit'] && $_POST['submit'] == 'Search')
{
	$pages = list_pages($_POST['keywords'], $_POST['search_type'], $from , $limit); 
	$total_pages = pm_count($pages);
}
else
{
	switch ($filter)
	{
		default:
		case 'mostviewed':
		
			$total_pages = $config['total_pages'];
		
		break;
		
		case 'private':
		
			$total_pages = count_entries('pm_pages', 'status', '0');
		
		break;

		case 'public':
		
			$total_pages = count_entries('pm_pages', 'status', '1');
		
		break;
	}
	$pages = list_pages('', '', $from , $limit, $filter, $filter_value);
}

// generate smart pagination
$filename = 'pages.php';
$pagination = '';

if( ! isset($_POST['submit']))
{
	$pagination = a_generate_smart_pagination($current_page, $total_pages, $limit, 5, $filename, '');
}
?>

<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title; ?></span> <a href="edit-page.php?do=new" class="badge badge-success badge-addnew font-size-sm ml-2">+ add new</a></h4>
			</div>
			<a href="#" class="header-elements-toggle text-default d-md-none"><i class="mi-search"></i></a>
			<div class="header-elements with-search d-none">
				<div class="d-flex-inline align-self-center ml-auto">
					<form name="search" action="pages.php" method="post" class="form-search-listing form-inline float-right">
						<div class="input-group input-group-sm input-group-search">
							<div class="input-group-append">
								<input name="keywords" type="text" value="<?php echo $_POST['keywords']; ?>" class="search-query search-quez input-medium form-control form-control-sm border-right-0" placeholder="Enter keyword" id="form-search-input" />
							</div>
							<select name="search_type" class="form-control form-control-sm border-left-0 border-right-0">
								<option value="title" <?php echo ($_POST['search_type'] == 'title') ? 'selected="selected"' : '';?>>Title</option>
								<option value="content" <?php echo ($_POST['search_type'] == 'content') ? 'selected="selected"' : '';?>>Content</option>
							</select>
							<div class="input-group-append">
							<button type="submit" name="submit" class="btn btn-light border-left-0" value="Search" id="submitFind"><i class="mi-search"></i><span class="findLoader"><img src="img/ico-loader.gif" width="16" height="16" /></span></button>
							</div>
						</div>
					</form>
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

<?php if ( $total_pages > 0 || !empty($filter)) : ?> <!--Start ifempty-->
		<div class="row p-0 mt-1 ml-0 mr-2">
			<div class="col-md-12">
				<ul class="nav nav-pills nav-pills-bottom m-0">
					<li class="nav-item">
						<a class="nav-link <?php echo (empty($filter) || $filter == 'mostviewed' || $filter == 'public') ? 'active' : ''; ?>" href="pages.php">All <?php echo (empty($filter) || $filter == 'mostviewed' || $filter == 'public') ? '<span class="text-muted">('. pm_number_format(count_entries('pm_pages', '', '')) .')</span>' : ''; ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo ($filter == 'private') ? 'active' : '';?>" href="pages.php?filter=private">Private <?php echo ($filter == 'private') ? '<span class="text-muted">('. pm_number_format($total_pages) .')</span>' : ''; ?></a>
					</li>
				</ul>
			</div>
		</div>
<?php endif; ?> <!--End ifempty-->
	</div><!--.page-header -->
</div><!--.page-header-wrapper-->	
<div class="page-help-panel" id="help-assist"> 
		<div class="row">
			<div class="col-2 help-panel-nav">
				<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<a class="nav-link active" id="v-pills-tab-help-one" data-toggle="pill" href="#v-pills-one" role="tab" aria-controls="v-pills-one" aria-selected="true" data-toggle="tab">Overview</a>
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">Filtering</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>Pages allow for easy content publishing. Pages can be used to things such as creating a &quot;Terms of agreement&quot; page, a promotion page or any other additional content you might need.<br />Published pages will appear as links in the footer of your site.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
						<p>Listing pages such as this one contain a filtering area which comes in handy when dealing with a large number of entries. The filtering options is always represented by a gear icon positioned on the top right area of the listings table. Clicking this icon usually reveals a  search form and one or more drop-down filters.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content content-full-width">

<?php if ( $total_pages == 0 && empty($filter)) : ?> <!--Start ifempty-->

<div class="align-middle text-center mt-3 pt-3">
	<i class="icon-files-empty icon-3x text-muted mb-3"></i>
	<h6 class="font-weight-semibold text-grey mb-1"><?php echo (!empty($_POST['keywords'])) ? 'No such pages found' : 'No pages yet';?></h6>
	<p class="text-grey mb-3 pb-1">Create a new page now.</p>
	<a href="edit-page.php?do=new" class="btn btn-sm bg-blue border-blue border-2 rounded font-weight-semibold">Add Page</a>
</div>

<?php else : ?> <!--Else ifempty-->

<?php echo $info_msg; ?>
  
<div id="display_result" style="display:none;"></div>

	<div class="card card-blanche">
		<div class="card-body">
		<div class="row">
			<div class="col-sm-12 col-md-6">
			<?php if ( ! empty($_POST['keywords'])) : ?>
			<h5 class="font-weight-semibold mt-2">SEARCH RESULTS FOR <mark><?php echo $_POST['keywords']; ?></mark> <a href="#" onClick="parent.location='pages.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear search results"><i class="icon-cancel-circle2"></i></a></h5>
			<?php endif; ?>
			</div>
			<div class="col-sm-12 col-md-6 d-none d-md-block">
				<div class="float-right mb-3">
					<form name="pages_per_page" action="pages.php" method="get" class="form-inline float-right">
						<input type="hidden" name="ui_pref" value="pages_pp" />
						<label class="mr-1" for="inlineFormCustomSelectPref">Entries/page</label>
						<select name="results" class="custom-select custom-select-sm w-auto" onChange="this.form.submit()" >
						<option value="25" <?php if($limit == 25) echo 'selected="selected"'; ?>>25</option>
						<option value="50" <?php if($limit == 50) echo 'selected="selected"'; ?>>50</option>
						<option value="75" <?php if($limit == 75) echo 'selected="selected"'; ?>>75</option>
						<option value="100" <?php if($limit == 100) echo 'selected="selected"'; ?>>100</option>
						<option value="125" <?php if($limit == 125) echo 'selected="selected"'; ?>>125</option>
						</select>
						<?php
						// filter persistency
						if (strlen($_SERVER['QUERY_STRING']) > 0)
						{
							$pieces = explode('&', $_SERVER['QUERY_STRING']);
							foreach ($pieces as $k => $val)
							{
								$p = explode('=', $val);
								if ($p[0] != 'page' && $p[0] != 'results') :	
								?>
								<input type="hidden" name="<?php echo $p[0];?>" value="<?php echo $p[1];?>" />
								<?php 
								endif;
							}
						}
						?>
					</form>
				</div>
			</div>
		</div> <!--.row with filters -->
	</div><!--.card-body-->



<form name="pages_checkboxes" id="pages_checkboxes" action="pages.php?page=<?php echo $current_page;?>" method="post">
<div class="datatable-scroll">
<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
<thead>
	<tr>
	<th align="center" class="text-center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
	<th width="">Page Title</th>
	<th width="10%" class="text-center">Author</th>	
	<th width="10%" class="text-center">
		<a href="pages.php?page=1&filter=public&<?php echo ($filter_value == 'desc' && $filter == 'added') ? 'asc' : 'desc';?>" data-popup="tooltip" data-html="true" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'added') ? 'ascending' : 'descending';?>">Added</a>
	</th>
	<th width="5%" class="text-center">
		<a href="pages.php?page=1&filter=mostviewed&<?php echo ($filter_value == 'desc' && $filter == 'added') ? 'asc' : 'desc';?>" data-popup="tooltip" data-html="true" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'added') ? 'ascending' : 'descending';?>">Views</a>
	</th>
	<th style="text-align:center; width: 90px;">Action</th>
	</tr>
</thead>
<tbody>
<?php 

/*
 *  List existing pages
 */ 
if ( ! array_key_exists('type', $pages) && $total_pages > 0)
{
	$alt = 1;
	
	foreach ($pages as $k => $page)
	{
		$col = ($alt % 2) ? 'table_row1' : 'table_row2';
		$alt++;
		
		?>
		 
		<tr class="<?php echo $col;?>" id="page-<?php echo $page['id'];?>">
		 <td align="center" class="text-center" width="3%">
			<input name="checkboxes[]" type="checkbox" value="<?php echo $page['id']; ?>" />
		 </td>
		 <td>
			<?php 
				if ($page['status'] == 0)
				{
					echo '<a href="pages.php?page=1&filter=private" data-popup="tooltip" data-container="body" data-original-title="This page is private. Only the Administrator can see this page."><span class="badge badge-secondary">DRAFT</span></a>';
				}
			?>
		   <a href="<?php echo _URL.'/page.php?p='. $page['id']; ?>" target="_blank"><?php echo htmlspecialchars($page['title']); ?></a>
		 </td>
		 <td align="center" class="text-center">
		  <?php 
			$author = fetch_user_advanced($page['author']);
			
			echo '<a href="edit-user.php?uid='. $author['id'] .'" title="Edit">'. $author['username'] .'</a>';
		  ?>
		 </td>
		 <td align="center" class="text-center">
		 <span rel="tooltip" title="<?php echo date('l, F j, Y g:i A', $page['date']); ?>">
		 <?php echo date('M d, Y', $page['date']); ?>
		 </span>
		 </td>
		 <td align="center" class="text-center"><?php echo pm_number_format($page['views']); ?></td>

		 <td align="center" class="text-center table-col-action">
			<div class="list-icons">
				<a href="edit-page.php?do=edit&id=<?php echo $page['id'];?>" class="list-icons-item mr-2" rel="tooltip" title="Edit"><i class="icon-pencil7"></i></a> 
				<a href="#" onclick="onpage_delete_page('<?php echo $page['id']; ?>', '#display_result', '#page-<?php echo $page['id'];?>')" class="list-icons-item text-warning" rel="tooltip" title="Delete"><i class="icon-bin"></i></a>
			</div>
		 </td>
		</tr>
		
		<?php
	}
}
else	//	Error?
{
	if (strlen($pages['msg']) > 0)
	{
		echo pm_alert_error($pages['msg']);
	}
	
	if ($total_pages == 0)
	{
		?>
		<tr>
		 <td colspan="8" align="center" class="text-center">
		 No pages found.
		 </td>
		</tr>
		<?php
	}
}
?>
	
<?php if ($pagination != '') : ?>
<tr class="tablePagination">
	<td colspan="7" class="tableFooter">
		<div class="table table-md table-striped table-columned pm-tables tablesorter"><?php echo $pagination; ?></div>
	</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

	<div class="datatable-footer">
		<div id="stack-controls" class="row list-controls">
			<div class="col-md-12">
				<div class="float-right form-inline">
					<button type="submit" name="submit" value="Delete" class="btn btn-sm btn-danger" onClick="return confirm_delete_all();">Delete</button>
				</div>
			</div>
		</div><!-- #list-controls -->
		<?php echo csrfguard_form('_admin_articles'); ?>
	</div>

<?php echo csrfguard_form('_admin_pages');?>
</form>
</div><!--.card-->
<?php endif ?> <!--End ifempty-->
</div><!-- .content -->
<?php
include('footer.php');