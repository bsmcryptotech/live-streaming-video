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

$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$_page_title = 'Manage series';
$showm = 'mod_series';
include('header.php');

$action = (int) $_GET['action'];
$page   = (int) $_GET['page'];
$filters = array(
    'genre', 'featured', 'mostviewed', 'added'
);
$sorters = array(
	'series_id', 'title', 'release_year', 'date', 'views', 'asc', 'desc'
);
$sort_by = (isset($_GET['sort-by']) && in_array(strtolower($_GET['sort-by']), $sorters) !== false) ? $_GET['sort-by'] : 'series_id';
$sort_by_order = (isset($_GET['order']) && in_array(strtolower($_GET['order']), $sorters) !== false) ? $_GET['order'] : 'DESC';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
    if ($filter == 'mostviewed')
    {
        $sort_by = 'views';
        $sort_by_order = 'desc';
        unset($filter, $filter_value);
    }

    if ($filter == 'added')
    {
        $sort_by = 'date';
        $sort_by_order = 'desc';
        unset($filter, $filter_value);
    }
}

$page = ( ! $page) ? 1 : $page;
$limit = get_admin_ui_prefs('series_pp');
$from = $page * $limit - ($limit);
$total_series = 0;

// generate smart pagination
$filename = 'series.php';
$pagination = '';
$genres = get_genres();

if ($_POST['submit'] == 'Delete' && ! csrfguard_check_referer('_admin_series_listcontrols'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['submit'] == 'Delete')
{
	if (pm_count($_POST['series_ids']) > 0)
	{
		$series_ids = array();
		foreach ($_POST['series_ids'] as $k => $id)
		{
			$series_ids[] = (int) $id;
		}
		
		$result = mass_delete_series($series_ids);
		
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
if ($_POST['Submit'] == 'Mark as featured' || $_POST['Submit'] == 'Mark as regular')
{
	$series_ids = array();
	$series_ids = $_POST['series_ids'];
	$total_ids = pm_count($series_ids);
	if($total_ids > 0)
	{
		$series_ids = array();
		foreach ($_POST['series_ids'] as $k => $id)
		{
			$series_ids[] = (int) $id;
		}
		
		if (count($series_ids) > 0)
		{
			$sql = "UPDATE pm_series ";
			$sql .= ($_POST['Submit'] == 'Mark as featured')
				? " SET featured = '1' "
				: " SET featured = '0' ";
			$sql .=	" WHERE series_id IN (" . implode(',', $series_ids) . ")";
			$result = mysql_query($sql);
			
			if ( ! $result)
			{
				$info_msg = pm_alert_error('There was an error while updating your database: '. mysql_error());
			}
			else
			{
				$info_msg = pm_alert_success('The selected series have been updated.');
			}
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}

if ($_POST['submit'] == 'Search') 
{
    $series = search_series(trim(html_entity_decode($_POST['keywords'])), $_POST['search_type'], $from, $limit, $sort_by, $sort_by_order, $total_series);
} 
else 
{
    $total_series = $config['total_series'];
    $genre_ids = ($filter == 'genre') ? array($filter_value) : array();
    switch ($filter)
    {
        case 'genre':
            $total_series = $genres[$filter_value]['total_series'];
        break;

        case 'featured':
            $total_series = count_entries('pm_series', 'featured', '1');
        break;
    }
    $series = get_series_list(array(), $sort_by, $sort_by_order, $from, $limit, $genre_ids, array(), $filter, $filter_value); 
}

$pagination = a_generate_smart_pagination($page, $total_series, $limit, 5, $filename, '&filter='. $filter .'&fv='. $filter_value .'&sort-by='. $sort_by .'&order='. $sort_by_order);

?>

<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">      
			<div class="page-title d-flex">
				<h4><a href="<?php echo _URL; ?>/series.php" class="open-in-new" target="_blank" data-popup="tooltip" data-placement="right" data-original-title="Jump to Series in Front-End"><span class="font-weight-semibold">Series</span> <i class="mi-open-in-new"></i></a> <a href="edit-series.php?do=new" class="badge badge-success badge-addnew font-size-sm ml-2">+ add new</a></h4>
			</div>
			<a href="#" class="header-elements-toggle text-default d-md-none"><i class="mi-search"></i></a>
			<div class="header-elements with-search d-none">
				<div class="d-flex-inline align-self-center ml-auto">
					<form name="search" action="series.php" method="post" class="form-search-listing form-inline float-right">
						<div class="input-group input-group-sm input-group-search">
							<div class="input-group-append">
								<input name="keywords" type="text" value="<?php echo $_POST['keywords']; ?>" class="search-query search-quez input-medium form-control form-control-sm border-right-0" placeholder="Enter keyword" id="form-search-input" />
							</div>
							<input type="hidden" name="search_type" value="title" />
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
						<a href="series.php" class="breadcrumb-item">Series</a>
						<a href="series.php" class="breadcrumb-item"><span class="breadcrumb-item active"><?php echo $_page_title; ?></span></a>
					</div>
				</div>
			</div>
<?php if ( $total_series > 0 || !empty($filter)) : ?> <!--Start ifempty-->
		<div class="row p-0 mt-1 ml-0 mr-2">
			<div class="col-sm-12 col-md-8">
			<div class="d-horizontal-scroll">
				<ul class="nav nav-pills nav-pills-bottom m-0">
					<li class="nav-item">
						<a class="nav-link <?php echo ($filter == '') ? 'active' : ''; ?>" href="series.php">All series <?php echo (empty($filter)) ? '<span class="text-muted">('. pm_number_format($config['total_series']) .')</span>' : ''; ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo ($filter == 'featured') ? 'active' : ''; ?>" href="series.php?filter=featured">Featured <?php echo ($filter == 'featured') ? '<span class="text-muted">('. pm_number_format($total_series) .')</span>' : ''; ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo ($sort_by == 'date') ? 'active' : ''; ?>" href="series.php?filter=<?php echo $filter; ?>&fv=<?php echo $filter_value; ?>&sort-by=date&order=<?php echo ($sort_by_order == 'desc' && $sort_by == 'date') ? 'asc' : 'desc';?>">Added <?php echo ($sort_by_order == 'desc' && $sort_by == 'date') ? '<i class="mi-arrow-drop-down"></i>' : '<i class="mi-arrow-drop-up"></i>';?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo ($sort_by == 'views') ? 'active' : ''; ?>" href="series.php?filter=<?php echo $filter; ?>&fv=<?php echo $filter_value; ?>&sort-by=views&order=<?php echo ($sort_by_order == 'desc' && $sort_by == 'views') ? 'asc' : 'desc';?>">Views <?php echo ($sort_by_order == 'desc' && $sort_by == 'views') ? '<i class="mi-arrow-drop-down"></i>' : '<i class="mi-arrow-drop-up"></i>';?></a>
					</li>
				</ul>
			</div>
			</div>
			<div class="col-sm-12 col-md-4">
				<div class="float-right mr-1">
					<form name="genre_filter" action="series.php" method="get" class="form-inline">
						<div class="">
						<input type="hidden" name="filter" value="genre" />
						<?php
						$genres_dropdown_options = array(
											'db_table' => 'pm_genres',
											'attr_name' => 'fv',
											'attr_id' => 'select_move_to_genre',
											'attr_class' => 'form-control custom-select custom-select-sm alpha-grey-300 text-grey-300 border-grey-300 font-weight-semibold',
											'first_option_text' => 'Genre',
											'first_option_value' => '',
											'selected' => ($filter == 'genre') ? $filter_value : '',
											'other_attr' => ' onchange=submit() '
											);
						$dd_html = categories_dropdown($genres_dropdown_options);
						$dd_html = str_replace('</select>', '<option value="0">Uncategorized</option></select>', $dd_html);

						if($filter == 'genre')
						{
							$dd_html = str_replace('form-control custom-select custom-select-sm', 'form-control custom-select custom-select-sm font-weight-semibold border-primary text-primary alpha-blue', $dd_html);
						}
						echo $dd_html;
						unset($dd_html);
						?>
						</div>
					</form>
				</div>
			</div>
		</div>
<?php endif; ?> <!--End ifempty-->

		</div><!-- /page header -->
	</div><!--.page-header-wrapper-->

<!-- Content area -->
<div class="content content-full-width">

<?php 
if ( ! _MOD_SERIES)
{
	echo pm_alert_info('The Series Module is currently disabled. You can enable it from <a href="settings.php?view=t6">Settings / Available Modules</a>.');
}

echo $info_msg;

if ( $total_series == 0) : ?> <!--Start ifempty-->

<div class="align-middle text-center mt-3 pt-3">
	<i class="icon-tv icon-3x text-muted mb-3"></i>
	<h6 class="font-weight-semibold text-grey mb-1">No series <?php echo (!empty($filter) || !empty($_GET['keywords']) || $_GET['vid']) ? 'matching these criteria found' : 'yet';?></h6>

	<p class="text-grey mb-3 pb-1"><?php echo (!empty($filter) || !empty($_POST['keywords'])) ? 'Try adjusting the filters or perform a new search.' : 'With series you can group videos by episodes and seasons.';?></p>

	<a href="edit-series.php?do=new" class="btn btn-sm bg-blue border-blue border-2 rounded font-weight-semibold">Add New Series</a>
</div>

<?php else : ?> <!--Else ifempty-->

<div id="display_result" style="display:none;"></div>

<div class="card card-blanche">
	<div class="card-body">
		<div class="row">
			<div class="col-sm-12 col-md-6">
				<?php if ( ! empty($_POST['keywords'])) : ?>
				<h5 class="font-weight-semibold mt-2">SEARCH RESULTS FOR <mark><?php echo htmlentities($_POST['keywords']); ?></mark> <a href="#" onClick="parent.location='series.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear search results"><i class="icon-cancel-circle2"></i></a></h5>
				<?php endif; ?>

				<?php if ( ! empty($filter) ) : ?>
				<h5 class="font-weight-semibold mt-2">FILTERED RESULTS <a href="#" onClick="parent.location='series.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear filter"><i class="icon-cancel-circle2"></i></a></h5>
				<?php endif; ?>
			</div>

			<div class="col-sm-12 col-md-6 d-none d-md-block">
				<div class="float-right mb-3">
					<form name="series_per_page" action="series.php" method="get" class="form-inline pull-right">
						<input type="hidden" name="ui_pref" value="series_pp" />
						<label class="mr-1" for="inlineFormCustomSelectPref">Series/page</label>
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

	<form name="series_checkboxes" id="series_checkboxes" action="series.php?page=<?php echo $page;?>&filter=<?php echo $filter;?>&fv=<?php echo $filter_value;?>" method="post">


<?php if ($total_series > 0) : ?>  <!--Start ifempty-->

	<div class="row row-series">
		<?php foreach ($series as $k => $series_data) : ?>
		<div class="card card-series" id="cardseries_<?php echo $series_data['series_id']; ?>">
			<div class="card-img-actions mx-1 mt-1">
				<a href="#" class="card-series-poster" style="background-image: url('<?php echo series_image_url($series_data) .'?cachebuster='. time(); ?>');">
					<div class="card-img-actions-overlay card-img-top">
						<a href="edit-series.php?do=edit&series_id=<?php echo $series_data['series_id'];?>" class="btn btn-sm btn-outline bg-white text-white font-size-xs border-white border-1 mr-1">Edit</a> 
						<a href="#" onclick="onpage_delete_series('<?php echo $series_data['series_id']; ?>', '#display_result', '#cardseries_<?php echo $series_data['series_id'];?>')" class="btn btn-sm btn-outline bg-white text-white font-size-xs border-white border-1 ml-1">Delete</a>
					</div>
				</a>

				<div class="card-title">
					<h6><a href="episodes.php?filter=series&fv=<?php echo $series_data['series_id']; ?>" class="font-size-md font-weight-semibold"><?php echo $series_data['title']; ?></a></h6>

					<div class="d-flex justify-content-between bg-grey-800 font-size-sm mt-2 rounded">
							<div class="badge badge-series-meta"><?php echo number_format($series_data['seasons_count']); ?> <span>Seasons</span></div>
							<div class="badge badge-series-meta"><?php echo number_format($series_data['episodes_count']); ?> <span>Ep.</span></div>
							<div class="badge badge-series-meta"><?php echo number_format($series_data['views']); ?> <span>Views</span></div>
					</div>
				</div>

				<?php if ($series_data['featured'] == 1) : ?>
					<a href="series.php?filter=featured" class="badge-featured" data-popup="tooltip" data-container="body" data-original-title="This series is Featured. Click to show only featured series."><span class="badge badge-primary">FEATURED</span></a>
				<?php endif; ?>
				<div class="card-select"><input name="series_ids[]" type="checkbox" value="<?php echo $series_data['series_id']; ?>" class="input-select-series" /></div>

			
			</div>
		</div>
		<?php endforeach; ?>
	</div>


	<div class="datatable-footer">
		<div id="stack-controls-disabled" class="row list-controls">

			<div class="col-12">
				<div class="float-right">
					<button type="button" class="btn btn-sm btn-outline bg-primary-400 text-primary-400 border-primary-400 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mark as</button> 
					<div class="dropdown-menu">
						<button type="submit" name="Submit" value="Mark as featured" class="dropdown-item">Featured</button>
						<button type="submit" name="Submit" value="Mark as regular" class="dropdown-item">Regular (non-Featured)</button>
					</div>
					<?php  if ( is_admin() ) : ?>
					<div class="d-inline">
					<button type="submit" name="submit" value="Delete" class="btn btn-sm btn-danger" onClick="return confirm_delete_all();">Delete</button> 
					</div>
					<?php  endif; ?>
					<input type="hidden" name="filter" id="listing-filter" value="<?php echo $filter;?>" />
					<input type="hidden" name="fv" id="listing-filter_value"value="<?php echo $filter_value;?>" /> 
					<input type="hidden" name="sort-by" id="listing-sort-by" value="<?php echo $sort_by;?>" />
					<input type="hidden" name="order" id="listing-order" value="<?php echo $sort_by_order;?>" />
				</div>
			</div>
		</div><!-- #list-controls -->
	</div>

	<?php if ($pagination != '') : ?>
	<div class="d-flex justify-content-center mt-3 pt-3">
		<div class=""><?php echo $pagination; ?></div>
	</div>
	<?php endif; ?>

<?php else : ?>  <!--Else ifempty-->

<div class="align-middle text-center mt-3 pt-3">
	<i class="icon-tv icon-3x text-muted mb-3"></i>
	<h6 class="font-weight-semibold text-grey mb-1"><?php echo (!empty($filter) || !empty($_POST['keywords'])) ? 'No such series found' : 'No series yet';?></h6>

	<p class="text-grey mb-3 pb-1"><?php echo (!empty($filter) || !empty($_POST['keywords'])) ? 'Try adjusting the filters or perform a new search.' : 'With series you can group videos by episodes and seasons.';?></p>

	<a href="edit-series.php?do=new" class="btn btn-sm bg-blue border-blue border-2 rounded font-weight-semibold">Add New Series</a>
</div>

<?php endif; ?>

<?php
echo csrfguard_form('_admin_series_listcontrols');
?>
</form>

</div><!--.card-->
<?php endif ?> <!--End ifempty-->
</div><!-- .content -->
<?php
include('footer.php');