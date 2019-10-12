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
$_page_title = 'Manage episodes';
$showm = 'mod_series';
include('header.php');

$action = (int) $_GET['action'];
$page   = (int) $_GET['page'];
$filters = array('series', 'featured', 'mostviewed', 'added', 'access');
$sorters = array(
	'episode_id', 'episode_id', 'video_title',  'added', 'views', 'asc', 'desc', 
	'mostviewed', 'views', 'site_views', 'added'
);
$sort_by = (isset($_GET['sort-by']) && in_array(strtolower($_GET['sort-by']), $sorters) !== false) ? $_GET['sort-by'] : 'episode_id';
$sort_by_order = (isset($_GET['order']) && in_array(strtolower($_GET['order']), $sorters) !== false) ? $_GET['order'] : 'DESC';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];

	if ($filter == 'mostviewed')
	{
		$sort_by = 'site_views';
		$sort_by_order = 'desc';
		unset($filter, $filter_value);
	}
}

$page = ( ! $page) ? 1 : $page;
$limit = get_admin_ui_prefs('episodes_pp');
$from = $page * $limit - ($limit);
$total_episodes = 0;

// generate smart pagination
$filename = 'episodes.php';
$pagination = '';
$genres = get_genres();
$all_series = get_series_list(array(), 'title', 'ASC', 0, $config['total_series']);

if ($_POST['submit'] == 'Delete' && ! csrfguard_check_referer('_admin_episodes_listcontrols'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['submit'] == 'Delete')
{
	if (pm_count($_POST['episode_ids']) > 0)
	{
		$result = mass_delete_episodes($_POST['episode_ids']);
		
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
		$info_msg = pm_alert_warning('Please select an episode first.');
	}
}

// Fix for IE
if ($_POST['Submit_restrict'] != '' && $_POST['Submit_restrict'] != '')
{
	$_POST['Submit'] = 'Restrict access';
}
if ($_POST['Submit_derestrict'] != '' && $_POST['Submit_derestrict'] != '')
{
	$_POST['Submit'] = 'Derestrict access';
}

switch ($_POST['Submit'])
{
	case 'Move':

		$_POST['episode_ids'] = $_POST['episode_ids'];
		$new_series_id = (int) $_POST['move_to_series'];

		$series_data = get_series($new_series_id);

		$total_ids = pm_count($_POST['episode_ids']);
		
		if (empty($series_data))
		{
			$info_msg = pm_alert_info('Please select a series first.');
		}
		else
		{
			if($total_ids == 0)
			{
				$info_msg = pm_alert_warning('Please select an episode first.');    
			}
			else
			{
				$episodes = get_episode_list($_POST['episode_ids']);
				$add = $total_ids;
				$deduct = array_fill(0, $total_ids-1, 0);

				foreach ($episodes as $k => $episode_data)
				{
					$deduct[$episode_data['series_id']]++;
				}

				$sql = "UPDATE pm_episodes
						SET series_id = '". $new_series_id ."' 
						WHERE episode_id IN (". implode(',', $_POST['episode_ids']) .")";
				$result = mysql_query($sql);
				if ( !$result)
				{
					$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				}
				
				// update pm_categories (deduct video count)
				foreach ($deduct as $series_id => $count)
				{
					$sql = "UPDATE pm_series  
							SET episodes_count = episodes_count - ". $count ." 
							WHERE series_id = ". $series_id;
					mysql_query($sql);
				}

				$sql = "UPDATE pm_series  
						SET episodes_count = episodes_count + ". $add ." 
						WHERE series_id = ". $new_series_id;
				mysql_query($sql);
				
				$info_msg = pm_alert_success('Episodes successfully moved to <strong>'. $series_data['title'] .'</strong>.');
			}
		}

	break;

	case 'Mark as featured':
	case 'Mark as regular':
	case 'Restrict access':
	case 'Derestrict access':

		if (pm_count($_POST['episode_ids']) > 0)
		{
			$video_ids = array();
			$episodes = get_episode_list($_POST['episode_ids']);
			foreach ($episodes as $k => $episode_data)
			{
				$video_ids[] = $episode_data['id'];
			}

			if (pm_count($video_ids) > 0)
			{
				if ($_POST['Submit'] == 'Mark as featured' || $_POST['Submit'] == 'Mark as regular')
				{
					$sql = "UPDATE pm_videos 
							SET featured = '". (($_POST['Submit'] == 'Mark as featured') ? '1' : '0') ."' 
							WHERE id IN (". implode(',', $video_ids) .")";
				}
				else
				{
					$sql = "UPDATE pm_videos 
							SET restricted = '". (($_POST['Submit'] == 'Restrict access') ? '1' : '0') ."' 
							WHERE id IN (". implode(',', $video_ids) .")";
				}

				if ($result = mysql_query($sql))
				{
					$info_msg = pm_alert_success('The selected episodes have been updated.');
				}
				else
				{
					$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				}
			}
		}
		else
		{
			$info_msg = pm_alert_warning('Please select something first.');
		}

	break;
}

if ($_POST['submit'] == 'Search' || ! empty($_GET['q']))
{
	$keywords = ( ! empty($_POST['keywords'])) ?  html_entity_decode($_POST['keywords']) : urldecode($_GET['q']);
	$episodes = search_episode($keywords, $_REQUEST['search_type'], $from, $limit, $sort_by, $sort_by_order, $total_episodes);
} 
else 
{
	$total_episodes = $config['total_episodes'];

	switch ($_GET['filter'])
	{
		case 'series':
			$episodes = get_episode_list(array(), (int) $_GET['fv'], $sort_by, $sort_by_order, $from, $limit); 

			foreach ($all_series as $k => $series_data)
			{
				if ($series_data['series_id'] == (int) $_GET['fv'])
				{
					$total_episodes = $series_data['episodes_count'];
					break;
				}
			}

		break;

		case 'featured':
			$episodes = get_featured_episodes_list($sort_by, $sort_by_order, $from, $limit, $total_episodes); 
		break;

		case 'access':
			$episodes = get_restricted_episodes_list($sort_by, $sort_by_order, $from, $limit, $total_episodes); 
		break;

		default: 
			$episodes = get_episode_list(array(), 0, $sort_by, $sort_by_order, $from, $limit); 
		break;
	}
}

$pagination = a_generate_smart_pagination($page, $total_episodes, $limit, 5, $filename, '&filter='. $filter .'&fv='. $filter_value .'&sort-by='. $sort_by .'&order='. $sort_by_order .(( ! empty($keywords)) ? '&q='. htmlentities($keywords) .'&search_type='. $_REQUEST['search_type'] : ''));
?>

<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
			<div class="d-flex justify-content-between w-100">
				<div class="page-title d-flex">
					<h4>
					<span class="font-weight-semibold">Episodes</span> <a href="edit-episode.php?do=new<?php echo ($filter == 'series') ? '&series_id='. $filter_value : ''; ?>" class="badge badge-success badge-addnew font-size-sm ml-2">+ add new</a>
					</h4>
				</div>
				<a href="#" class="header-elements-toggle text-default d-md-none"><i class="mi-search"></i></a>

				<div class="header-elements with-search d-none">
					<div class="d-flex-inline align-self-center ml-auto">
						<form name="search" action="episodes.php" method="post" class="form-search-listing form-inline float-right">
							<div class="input-group input-group-sm input-group-search">
								<div class="input-group-append">
									<input name="keywords" type="text" value="<?php echo htmlentities($keywords); ?>"  class="search-query search-quez input-medium form-control form-control-sm border-right-0" placeholder="Enter keyword" id="form-search-input" />
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
					<a href="episodes.php" class="breadcrumb-item">Episodes</a>
					<?php if ($filter == 'featured') : ?>
					<a href="episodes.php?filter=featured&page=1" class="breadcrumb-item active">Featured Episodes</a>
					<?php elseif ($filter == 'access') : ?>
					<a href="episodes.php?filter=featured&page=1" class="breadcrumb-item active">Private Episodes</a>
					<?php endif; ?>

					<?php if ( ! empty($keywords)) : ?>
					<span class="breadcrumb-item">Search</span> 
					<span class="breadcrumb-item active">Results for: <?php echo $keywords; ?></span>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="row p-0 mt-1 ml-0 mr-2">
			<div class="col-md-12">
				<div class="d-horizontal-scroll">				
					<ul class="nav nav-md nav-pills nav-pills-bottom m-0">
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'added' || empty($filter)) ? 'active' : ''; ?>" href="episodes.php">All <?php echo ($filter == 'added' || empty($filter)) ? '<span class="text-muted">('. pm_number_format($total_episodes) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'access') ? 'active' : ''; ?>" href="episodes.php?filter=access&page=1">Private <?php echo ($filter == 'access') ? '<span class="text-muted">('. pm_number_format($total_episodes) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'featured') ? 'active' : ''; ?>" href="episodes.php?filter=featured&page=1">Featured <?php echo ($filter == 'featured') ? '<span class="text-muted">('. pm_number_format($total_episodes) .')</span>' : ''; ?></a>
						</li>

						<li class="nav-item dropdown">
							<a class="nav-link font-weight-semibold dropdown-toggle <?php echo ($filter == 'series') ? 'active' : ''?>" data-toggle="dropdown" href="#" id="dd-category" role="button" aria-haspopup="true" aria-expanded="false">Series</a>
							<ul class="dropdown-menu dropdown-menu-scroll" aria-labelledby="dd-category">
								<?php
								ksort($all_series);
								foreach ($all_series as $k => $series_data) : ?>
								<li class="nav-item">
									<a href="episodes.php?filter=series&fv=<?php echo $series_data['series_id']; ?>" class="dropdown-item <?php echo ($filter == 'series' && $filter_value == $series_data['series_id']) ? 'active' : ''; ?>"><?php echo $series_data['title']; echo ( ! empty($series_data['release_year'])) ? " (". $series_data['release_year'] .")" : ''; ?></a>
								</li>
								<?php endforeach; ?>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div><!--.page-header -->
</div><!--.page-header-wrapper-->	

<!-- Content area -->
<div class="content content-full-width">

<?php 
if ( ! _MOD_SERIES)
{
	echo pm_alert_info('The Series Module is currently disabled. You can enable it from <a href="settings.php?view=t6">Settings / Available Modules</a>.');
}
?>
<?php if ( $total_episodes == 0 && empty($filter)) : ?> <!--Start ifempty-->

<div class="align-middle text-center mt-3 pt-3">
	<i class="mi-video-library mi-3x text-muted mb-3"></i>
	<h6 class="font-weight-semibold text-grey mb-1">No episodes yet</h6>
	<p class="text-grey mb-3 pb-1">Add a new episode now.</p>
	<a href="edit-episode.php?do=new" class="btn btn-sm bg-blue border-blue border-2 rounded font-weight-semibold">Add Episode</a>
</div>

<?php else : ?> <!--Else ifempty-->

<div id="display_result" style="display:none;"></div>
<div class="card card-blanche">	
	<div class="card-body">
		<div class="row">
			<div class="col-sm-12 col-md-8">

				<?php if ( ! empty($keywords)) : ?>
				<h5 class="font-weight-semibold mt-2">SEARCH RESULTS FOR <mark><?php echo htmlentities($keywords); ?></mark> <a href="#" onClick="parent.location='episodes.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear search results"><i class="icon-cancel-circle2"></i></a></h5>
				<?php endif; ?>

				<?php if ( ! empty($filter) && $filter != 'added' && $filter != 'series') : ?>
				<h5 class="font-weight-semibold mt-2">FILTERED RESULTS <a href="#" onClick="parent.location='episodes.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear filter"><i class="icon-cancel-circle2"></i></a></h5>
				<?php endif; ?>

				<?php foreach ($all_series as $k => $series_data) : ?>
				<?php	if ($series_data['series_id'] == (int) $_GET['fv']) : ?>
				<div class="d-flex justify-content-start pl-0">
					<div class="">
						<div class="card card-series card-series-sm ml-0 pl-0">
							<div class="card-img-actions mx-1 mt-1">
								<a href="edit-series.php?do=edit&amp;series_id=<?php echo $series_data['series_id']; ?>" class="card-series-poster card-series-poster-sm" style="background-image: url('<?php echo $series_data['image_url']; ?>');" data-popup="tooltip" data-original-title="Edit Series"></a>
							</div>
						</div>		
					</div>
					<div class="ml-3">

							<h4>Episodes from <mark><a href="<?php echo _URL .'/series.php?s='. $series_data['series_slug']; ?>" class="text-dark" target="_blank" data-popup="tooltip" data-placement="top" data-original-title="Preview"><?php echo $series_data['title']; ?></a></mark> <a href="#" onClick="parent.location='episodes.php'" class="text-muted opacity-50" data-popup="tooltip" data-placement="top" data-original-title="Clear filter"><i class="icon-cancel-circle2"></i></a></h4>
							<div><?php echo $series_data['seasons_count']; ?> <?php echo ngettext('season', 'seasons', $series_data['seasons_count']); ?> <span class="text-muted">(out of <?php echo $series_data['seasons']; ?>)</span></div>
							<div><?php echo $series_data['episodes_count']; ?> <?php echo ngettext('episode', 'episodes', $series_data['episodes_count']); ?> <span class="text-muted">(out of <?php echo $series_data['episodes']; ?>)</span></div>
					</div>
				</div>
				<?php 	endif; ?>
				<?php endforeach; ?>

			</div><!-- .col-md-8 -->

			<div class="col-sm-12 col-md-4 d-none d-md-block">
				<div class="float-right">
					<form name="episodes_per_page" action="episodes.php" method="get" class="form-inline pull-right">
						<input type="hidden" name="ui_pref" value="episodes_pp" />
						<label class="mr-1" for="inlineFormCustomSelectPref">Episodes/page</label>
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

			</div><!-- .col-md-4 -->
		</div><!-- .row-->

	</div><!--.card-body-->

	
	<form name="episodes_checkboxes" id="episodes_checkboxes" action="episodes.php?page=<?php echo $page;?>&filter=<?php echo $filter;?>&fv=<?php echo $filter_value;?>" method="post">
	<div class="table-responsive">
	 <table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter datatable-sorting">
	  <thead>
	   <tr>
 		<th align="center" style="text-align:center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
		<th width="65">Poster</th>
		<th>Episode title</th>
		<th>Series</th>
		<th width="60" class="text-center">Season</th>
		<th width="60" class="text-center">Episode</th>
		<th width="120" class="text-center">
			<?php if (isset($keywords)) : ?>
				Added
			<?php else : ?>
				<a href="episodes.php?filter=<?php echo $filter; ?>&fv=<?php echo $filter_value; ?>&sort-by=added&order=<?php echo ($sort_by_order == 'desc' && $sort_by == 'added') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($sort_by_order == 'desc' && $sort_by == 'added') ? 'ascending' : 'descending';?>">Added</a>
			<?php endif; ?>
		</th>
		<th width="65">
			<?php if (isset($keywords)) : ?>
				Views
			<?php else : ?>
			<a href="episodes.php?filter=<?php echo $filter; ?>&fv=<?php echo $filter_value; ?>&sort-by=site_views&order=<?php echo ($sort_by_order == 'desc' && $sort_by == 'site_views') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($sort_by_order == 'desc' && $sort_by == 'site_views') ? 'ascending' : 'descending';?>">Views</a>
			<?php endif; ?>
		</th>
		<th style="width: 130px;" class="text-center">Comments</th>
		<th style="width: 90px;" class="text-center">Action</th>
	   </tr>
	  </thead>
	  <tbody>

		<?php if ($total_episodes > 0) : ?>
			<?php foreach ($episodes as $k => $episode_data) : ?>
				<tr class="" data-episode-id="<?php echo $episode_data['episode_id']; ?>" id="episode-<?php echo $episode_data['episode_id']; ?>">
					<td align="center" style="text-align:center" width="3%">
						<input name="episode_ids[]" type="checkbox" value="<?php echo $episode_data['episode_id']; ?>" />
					</td>
					<td>
						<a href="<?php echo $episode_data['url']; ?>" target="_blank">
							<img src="<?php echo $episode_data['image_url'] .'?cachebuster='. time(); ?>" width="50" />
						</a>
					</td>
					<td>
						<?php if ($episode_data['featured'] == 1) : ?>
							<a href="episodes.php?filter=featured" data-popup="tooltip" data-container="body" data-original-title="This episode is Featured. Click to show only featured episodes."><span class="badge badge-primary">FEATURED</span></a> 
						<?php endif; ?>
						<?php if ($episode_data['restricted'] == 1) : ?>
							<a href="episodes.php?filter=access" data-popup="tooltip" data-container="body" data-original-title="Show only PRIVATE episodes."><span class="badge badge-dark">PRIVATE</span></a>
						<?php endif; ?>
						<a href="<?php echo $episode_data['url']; ?>" target="_blank"><?php echo $episode_data['video_title']; ?> <?php echo ($episode_data['release_year'] > 0) ? '('. $episode_data['release_year'] .')' : ''; ?></a>
						<?php 
						$bin_rating_meta = (function_exists('bin_rating_get_item_meta')) ? bin_rating_get_item_meta($episode_data['uniq_id']) : false;
						if ($bin_rating_meta) : ?>
							<div class="float-right">
								<i class="mi-thumb-up text-muted opacity-50 mr-1"></i> <span class="text-success"><?php echo pm_number_format($bin_rating_meta['up_vote_count']); ?></span>
								&nbsp;&nbsp;
								<i class="mi-thumb-down text-muted opacity-50 mr-1 ml-2"></i> <span class="text-danger"><?php echo pm_number_format($bin_rating_meta['down_vote_count']); ?></span>
							</div>
						<?php endif; ?>
					</td>
					<td>
						<a href="episodes.php?filter=series&fv=<?php echo $episode_data['series_id']; ?>" rel="tooltip" title="Show episodes form this series"><?php echo $episode_data['series_data']['title']; ?> <?php echo ($episode_data['series_data']['release_year'] > 0) ? '('. $episode_data['series_data']['release_year'] .')' : ''; ?></a>
					</td>
					<td class="text-center">
						<?php echo number_format($episode_data['season_no']); ?>
					</td>
					<td class="text-center">
						<?php echo number_format($episode_data['episode_no']); ?>
					</td>
					<td class="text-center d-md-table-cell font-size-sm">
						<?php echo date('M d, Y', $episode_data['added_timestamp']); ?>
					</td>
					<td class="text-center">
						<?php echo number_format($episode_data['site_views']); ?>
					</td>
					<td align="center" class="text-center d-md-table-cell font-size-sm">
						<?php $total_comments = count_entries('pm_comments', 'uniq_id', $episode_data['uniq_id']); ?>
						<a href="comments.php?vid=<?php echo $episode_data['uniq_id']; ?>">View</a> 
						<?php if ((is_admin() || (is_moderator() && mod_can('manage_comments')))) : ?>
						| <a href="#" onclick="delete_episode_comments('<?php echo $episode_data['uniq_id']; ?>', '#display_result', '#episode-<?php echo $episode_data['episode_id'];?>')">Delete</a>
						<?php endif; ?>
						(<span id="comment-count-<?php echo $episode_data['uniq_id']; ?>" class="font-size-sm"><?php echo number_format($total_comments); ?></span>)
					</td>
					<td align="center" class="table-col-action text-center d-md-table-cell" style="width: 90px;">
						<a href="edit-episode.php?episode_id=<?php echo $episode_data['episode_id'];?>"  class="list-icons-item mr-1" data-popup="tooltip" data-container="body" data-original-title="Edit"><i class="icon-pencil7"></i></a>
						<a href="#" onclick="onpage_delete_episode('<?php echo $episode_data['episode_id']; ?>', '#display_result', '#episode-<?php echo $episode_data['episode_id'];?>')" class="list-icons-item text-warning" data-popup="tooltip" data-container="body" data-original-title="Delete"><i class="icon-bin"></i></a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="11" align="center" style="text-align:center">
					No episodes found. <a href="edit-episode.php?do=new" class="font-weight-semibold">Add a new episode now</a>.
				</td>
			</tr>
		<?php endif; ?>

		<?php if ($pagination != '') : ?>
		<tr class="tablePagination">
			<td colspan="11" class="tableFooter">
				<div class="table table-md table-striped table-columned pm-tables tablesorter"><?php echo $pagination; ?></div>
			</td>
		</tr>
		<?php endif; ?>
		
	  </tbody>
	 </table>
	</div><!--/table-responsive-->

	<div class="datatable-footer">
		<div id="stack-controls" class="row list-controls">
			<div class="col-sm-12 col-md-6 d-none d-md-block">
				<div class="float-left form-inline">
					<div class="input-group input-group-sm">
						<label class="mr-1">Move to</label>
							<select name="move_to_series" id="" class="form-control custom-select custom-select-sm border-right-0">
								<option value="-1" selected="selected">series...</option>
								<?php
								foreach ($all_series as $k => $series_data) : ?>
									<option value="<?php echo $series_data['series_id']; ?>" <?php echo ($filter == 'series' && $filter_value == $series_data['series_id']) ? 'selected="selected"' : ''; ?>><?php echo $series_data['title']; echo ( ! empty($series_data['release_year'])) ? " (". $series_data['release_year'] .")" : ''; ?></option>
								<?php endforeach; ?>
							</select>
						<div class="input-group-append">
							<button type="submit" name="Submit" value="Move" data-loading-text="Moving..." class="btn btn-sm btn-light" onClick="if ($('select[name=move_to_series] option:selected').val() == '' || $('select[name=move_to_series] option:selected').val() == '-1') {alert('Please select a series first.'); return false;}" />Move</button>
						</div>
					</div>
				</div>
			</div>

			<div class="col-sm-12 col-md-6 mb-0">
				<div class="float-right">
					<div class="btn-group dropup">
						<button type="button" class="btn btn-sm btn-outline bg-primary-400 text-primary-400 border-primary-400 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mark as</button> 
						<div class="dropdown-menu">
							<button type="submit" name="Submit" value="Mark as featured" class="dropdown-item">Featured</button>
							<button type="submit" name="Submit" value="Mark as regular" class="dropdown-item">Regular (non-Featured)</button>
							<button type="submit" name="Submit_restrict" value="Restrict access" class="dropdown-item" data-popup="tooltip" data-html="true" data-placement="left" title="Private videos will be available only to registered users.">Private</button>
							<button type="submit" name="Submit_derestrict" value="Derestrict access" class="dropdown-item" data-popup="tooltip" data-html="true" data-placement="left" title="Make selected videos public. Remove any viewing restrictions.">Public</button>
						</div>
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

	<?php echo csrfguard_form('_admin_episodes_listcontrols'); ?>
	</form>

</div><!--.card-->
<?php endif; ?> <!--End ifempty-->
</div><!-- .content -->
<?php
include('footer.php');