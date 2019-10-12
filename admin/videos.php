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
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$_page_title = 'Manage videos';
include('header.php');

$action	= (int) $_GET['action'];
$page	= (int) $_GET['page'];
$filter = 'added';
$filters = array('broken', 'restricted', 'unchecked', 'localhost', 'featured', 'category', 'source', 'mostviewed', 'access', 'views', 'added', 'addedactive', 'scheduled', 'trash');
$filter_value = 'desc';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];

	if (($filter == 'source' || $filter == 'category') && $filter_value == '')
	{
		$filter = 'added';
	}
}

$page = ( ! $page) ? 1 : $page;
$limit = get_admin_ui_prefs('videos_pp');
$from = $page * $limit - ($limit);

$categories = load_categories();
$in_trash = false;


// Count videos based on status
$total_private_videos = count_entries('pm_videos', 'restricted', 1);
$total_featured_videos = count_entries('pm_videos', 'video_type = '. IS_VIDEO .' AND featured', 1);
$total_scheduled_videos = $config['total_videos'] - $config['published_videos'];
$total_broken_videos = count_entries('pm_videos', 'status', VS_BROKEN);

switch($filter)
{
	case 'broken':
		
		$total_videos = $total_broken_videos;
		
	break;
	
	case 'restricted':
		
		$total_videos = count_entries('pm_videos', 'status', VS_RESTRICTED);
		
	break;
	
	case 'unchecked':
		
		$total_videos = count_entries('pm_videos', 'status', VS_UNCHECKED);
		
	break;
	
	case 'localhost':
		
		$total_videos = count_entries('pm_videos', 'source_id', '1');
		
	break;
	
	case 'featured':
		
		$total_videos = $total_featured_videos;
		
	break;
	
	case 'category':
		
		$filter_value = (int) $filter_value;
		if ($filter_value > 0)
		{
			$total_videos = $categories[$filter_value]['total_videos'];
		}
		else if ($_GET['fv'] == '0')
		{
			$total_videos = count_entries('pm_videos', 'video_type = '. IS_VIDEO .' AND category', '');
		}
		else
		{
			$total_videos = 0;
			unset($filter_value);
		}
		
	break;
	
	case 'source':
		
		$filter_value = (int) $filter_value;
		$total_videos = count_entries('pm_videos', 'source_id', $filter_value);
		
	break;
	
	default:
	case 'added':
	case 'addedactive':
	case 'views':
	case 'mostviewed':
		
		$total_videos = $config['total_videos'];
		
	break;

	case 'scheduled':
		
		$total_videos = $total_scheduled_videos;
		
	break;

	case 'access':
		
		$filter_value = '1';
		$total_videos = $total_private_videos;
		
	break;
	
	case 'trash':
		
		$total_videos = (int) $config['trashed_videos'];
		$in_trash = true;
		
	break; 
}

if(!empty($_POST['submit']) && $_POST['submit'] == 'Search') 
{
	$search_query = secure_sql(trim(html_entity_decode($_POST['keywords'])));
	$search_type = $_POST['search_type'];

	$videos = a_list_videos($search_query, $search_type, $from, $limit, $page);
	$total_videos = preg_match_all("/<\/tr>/", $videos, $matches);
} 
else 
{
	if($total_videos - $from == 1)
		$page--;
		
	$videos = a_list_videos('', '', $from, $limit, $page, $filter, $filter_value); 
	
	if($total_videos - $from == 1)
		$page++;	
}

// generate smart pagination
$filename = 'videos.php';
$pagination = '';

if(!isset($_POST['submit'])) 
	$pagination = a_generate_smart_pagination($page, $total_videos, $limit, 1, $filename, '&filter='. $filter .'&fv='. $filter_value);

?>
<!-- Main content -->
<div class="content-wrapper">

<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
			<div class="d-flex justify-content-between w-100">
				<div class="page-title d-flex">
					<h4>
					<?php if ( ! $in_trash) : ?>
					<a href="<?php echo _URL; ?>/newvideos.php" class="open-in-new" target="_blank" data-popup="tooltip" data-placement="right" data-original-title="Jump to Videos in Front-End"><span class="font-weight-semibold">Videos</span> <i class="mi-open-in-new"></i></a> <a href="#addVideo" onclick="location.href='#addVideo';" data-toggle="modal" class="badge badge-success badge-addnew font-size-sm ml-2">+ add new</a>


					<?php else : ?>
					<span class="font-weight-semibold">Trash</span>
					<?php endif; ?>
					</h4>
				</div>
			<a href="#" class="header-elements-toggle text-default d-md-none"><i class="mi-search"></i></a>
			<div class="header-elements with-search d-none">
				<div class="d-flex-inline align-self-center ml-auto">
						<form name="search" action="videos.php" method="post" class="form-search-listing form-inline float-right">
							<div class="input-group input-group-sm input-group-search">
								<div class="input-group-append">
									<input name="keywords" type="text" value="<?php echo $_POST['keywords']; ?>"  class="search-query search-quez input-medium form-control form-control-sm border-right-0" placeholder="Enter keyword" id="form-search-input" />
								</div>
								<select name="search_type" class="form-control form-control-sm border-left-0  border-right-0">
									<option value="video_title" <?php echo ($_POST['search_type'] == 'video_title') ? 'selected="selected"' : '';?>>Title</option>
									<option value="uniq_id" <?php echo ($_POST['search_type'] == 'uniq_id') ? 'selected="selected"' : '';?>>Unique ID</option>
									<option value="submitted" <?php echo ($_POST['search_type'] == 'submitted') ? 'selected="selected"' : '';?>>Username</option>
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
					<a href="videos.php" class="breadcrumb-item">Videos</a>
					<?php if ($filter == 'featured') : ?>
					<a href="videos.php?filter=featured&page=1" class="breadcrumb-item active">Featured videos</a>
					<?php elseif ($filter == 'scheduled') : ?>
					<a href="videos.php?filter=featured&page=1" class="breadcrumb-item active">Scheduled videos</a>
					<?php elseif ($filter == 'access') : ?>
					<a href="videos.php?filter=featured&page=1" class="breadcrumb-item active">Private videos</a>
					<?php elseif ($filter == 'trash') : ?>						
					<a href="videos.php?filter=featured&page=1" class="breadcrumb-item active">Trash</a>
					<?php endif; ?>

					<?php if ( ! empty($_POST['keywords'])) : ?>
					<span class="breadcrumb-item">Search</span> 
					<span class="breadcrumb-item active">Results for: <?php echo $_POST['keywords']; ?></span>
					<?php endif; ?>
				</div>
			</div>

			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
					<a href="#" id="show-help-assist" class="breadcrumb-elements-item"><i class="mi-help-outline text-muted"></i></a>
				</div>
			</div>
		</div>

		<div class="row p-0 mt-1 ml-0 mr-2">
			<div class="col-md-7">
				<div class="d-horizontal-scroll">
					<ul class="nav nav-pills nav-pills-bottom m-0">
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'added') ? 'active' : ''; ?>" href="videos.php">All videos <span class="text-muted">(<?php echo pm_number_format($config['total_videos']); ?>)</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'access') ? 'active' : ''; ?>" href="videos.php?filter=access">Private <span class="text-muted">(<?php echo pm_number_format($total_private_videos); ?>)</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'featured') ? 'active' : ''; ?>" href="videos.php?filter=featured">Featured <span class="text-muted">(<?php echo pm_number_format($total_featured_videos); ?>)</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'scheduled') ? 'active' : '';  ?>" href="videos.php?filter=scheduled">Scheduled <span class="text-muted">(<?php echo pm_number_format($total_scheduled_videos); ?>)</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'trash') ? 'active' : ''; ?>" href="videos.php?filter=trash">Trash <span class="text-muted">(<?php echo pm_number_format($config['trashed_videos']); ?>)</span></a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-md-5 d-none d-md-block d-lg-block d-xlg-block">
				<div class="dropdown float-right">

					<a class="btn btn-sm <?php echo ($filter == 'source' || $filter == 'localhost') ? 'border-primary text-primary alpha-blue' : 'btn-outline alpha-grey-300 text-grey-300 border-grey-300' ?> font-weight-semibold dropdown-toggle rounded" href="#" role="button" id="dd-source" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Source</a>

					<div class="dropdown-menu dropdown-menu-scroll" aria-labelledby="dd-source">
						<a href="videos.php?filter=localhost&page=1" class="dropdown-item <?php if ($filter == 'localhost') echo 'active'; ?>">Hosted locally</a>
						<?php
						$sources = a_fetch_video_sources('source_name');
						foreach ($sources as $id => $src)
						{
						  $option = '';
						  if (is_int($id) && $id > 1 && $id != 44 && $id != 43)
						  {
							  $option = '<a href="videos.php?filter=source&fv='. $src['source_id'] .'" class="dropdown-item ';
							  if ($filter_value == $id && $filter == 'source')
							  {
								  $option .= ' active ';
							  }
							  $option .= '">'. ucfirst($src['source_name']) .'</a>';
							  echo $option;
						  }
						}
						?>
					</div>
				</div>

				<div class="float-right mr-1">
					<form name="category_filter" action="videos.php" method="get" class="form-inline">
						<div class="">
							<input type="hidden" name="filter" value="category" class="" />
							<?php
							$categories_dropdown_options = array(
												'attr_name' => 'fv',
												'attr_id' => 'select_move_to_category',
												'attr_class' => 'form-control custom-select custom-select-sm alpha-grey-300 text-grey-300 border-grey-300 font-weight-semibold',
												'first_option_text' => 'Category',
												'first_option_value' => '',
												'selected' => ($filter == 'category') ? $filter_value : '',
												'other_attr' => ' onchange=submit() '
												);
							$dd_html = categories_dropdown($categories_dropdown_options);
							if ($filter_value == 0 && $filter == 'category')
							{
								$dd_html = str_replace('</select>', '<option value="0" selected="selected">Uncategorized</option></select>', $dd_html);
							}
							else
							{
								$dd_html = str_replace('</select>', '<option value="0">Uncategorized</option></select>', $dd_html);
							}

							if($filter == 'category')
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
	</div><!--.page-header -->
</div><!--.page-header-wrapper-->	
<div class="page-help-panel" id="help-assist"> 
		<div class="row">
			<div class="col-2 help-panel-nav">
				<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<a class="nav-link active" id="v-pills-tab-help-one" data-toggle="pill" href="#v-pills-one" role="tab" aria-controls="v-pills-one" aria-selected="true" data-toggle="tab">Overview</a>
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">Filtering</a>
					<a class="nav-link" id="v-pills-tab-help-three" data-toggle="pill" href="#v-pills-three" role="tab" aria-controls="v-pills-three" aria-selected="false" data-toggle="tab">Terminology</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>This page provides an excellent overview of your existing video database. Listed below are the latest videos and a bunch of tools to help you get the work done. Most of the times you can do maintenance work without leaving this page.</p>
						<p>The listings contain as much data as we could reasonably fit on a screen. The actionable items (edit, delete, etc.) will always be located in the last column (right side). Some icons such as the video source icons (2nd column) can be used to filter results belonging to that video source. </p>
						<p>As you scroll down the page you will notice a hovering panel at the bottom of your screen. The purpose is to place all major action within easy reach.</p>
						<p>This page also contains a &quot;DELETE ALL VIDEOS&quot; button which if clicked will delete your entire database of videos. This process is irreversible.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
						<p>Listing pages such as this one contain a filtering area which comes in handy when dealing with a large number of entries. The filtering options is always represented by a gear icon positioned on the top right area of the listings table. Clicking this icon usually reveals a  search form and one or more drop-down filters.</p>
					</div>
					<div class="tab-pane" id="v-pills-three" role="tabpanel" aria-labelledby="v-pills-tab-help-three">
						<p><strong>Video sources</strong>:
						Since PHP Melody can automatically recognize, import and handle videos from a vast selection of top video sites, as well as handle video uploads, it's important to define each video as having a source. For example, sources can be: Youtube, Vimeo but also your own AWS S3 hosted videos and/or videos uploaded from this admin area.</p>
						<p><strong>Featured videos</strong>: videos marked as featured will appear within the homepage player. If more than one video is featured, they will be loaded randomly.</p>
						<p><strong>Video status</strong>: we incorporated a way to automatically check videos from remote locations and see whether they are still working or not.  While the system works well for Youtube and a dozen other sources, we recommend using it as a guide rather than a reliable indicator before deleting videos in bulk.</p>
						<p>The video status is represented by a round icon on the 4th column (from left). You can choose to check more than one video at a time by using the select all box (top left table corner) and then clicking the &quot;check status&quot; button.</p>
					</div>

				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->


	<!-- Content area -->
	<div class="content content-full-width">


<?php
if ($_GET['action'] == 'deleted') 
{
	echo pm_alert_success('Video successfully removed.');
}

if ($_GET['action'] == 'deletedcomments') 
{
	echo pm_alert_success('Comments successfully removed.');
}

if ($_GET['action'] == 'badtoken') 
{
	echo pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}

if ($_GET['action'] == 'restored')
{
	echo pm_alert_success('Video successfully restored from "Trash".');
}

if ($_GET['action'] == 'trashed')
{
	echo pm_alert_success('Video moved to Trash. <a href="edit-video.php?vid='. $_GET['vid'] .'&a=4&page='. $page .'&filter='. $filter .'&fv='. $filter_value .'">Undo</a>');
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

//	Batch Delete
if (($_POST['Submit'] == 'Delete selected') && ! csrfguard_check_referer('_admin_videos_listcontrols'))
{
	echo pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['Submit'] == 'Delete selected')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	
	$total_ids = pm_count($video_ids);
	
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		if(strlen($in_arr) > 0)
		{
			$video_list_data = array();
			$sql_table = ( ! $in_trash) ? 'pm_videos' : 'pm_videos_trash';
			
			$sql = "SELECT id, uniq_id, category, url_flv, added, submitted, source_id 
					FROM $sql_table 
					WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_assoc($result))
			{
				$video_list_data[$row['uniq_id']] = $row;
			}
			mysql_free_result($result);

			$sql = "DELETE FROM $sql_table WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			
			if(!$result)
			{
				echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '.mysql_error());
			}
			else
			{
				mysql_query("DELETE FROM pm_comments WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_reports WHERE entry_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_chart WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_tags WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_bin_rating_meta WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_bin_rating_votes WHERE uniq_id IN (" . $in_arr . ")");
				
				$ids = array();
				foreach ($video_list_data as $uniq_id => $video)
				{
					$ids[] = $video['id'];
					
					// handle playlists
					$playlist_ids = array();
					
					$sql = "SELECT list_id 
							FROM pm_playlist_items 
							WHERE video_id = ". $video['id'];
					
					if ($result = @mysql_query($sql))
					{
						$in_playlists = false;
						while ($row = mysql_fetch_assoc($result))
						{
							$playlist_ids[] = (int) $row['list_id'];
							$in_playlists = true;
						}
						mysql_free_result($result);
					
						if ($in_playlists)
						{
							$sql = "DELETE FROM pm_playlist_items
									WHERE video_id = ". $video['id'];
							@mysql_query($sql);
			
							$sql = "UPDATE pm_playlists 
									SET items_count = items_count - 1 
									WHERE list_id IN (". implode(',', $playlist_ids) .")";
							@mysql_query($sql);
						}
					}
				}
				
				mysql_query("DELETE FROM pm_meta WHERE item_id IN (". implode(',', $ids) .") AND item_type = ". IS_VIDEO);
				unset($ids);

				echo pm_alert_success('Videos removed successfully.');
			}
			
			// update video count for each category
			$total_published_ids = 0;
			$video_count = array();
			$video_published_count = array();
			$time_now = time();
			
			foreach ($video_list_data as $uniq_id => $row)
			{
				$subtitles = a_get_video_subtitles($uniq_id);

				// delete hosted files
				if ($row['source_id'] == 1)
				{
					if (file_exists(_VIDEOS_DIR_PATH . $row['url_flv']) && strlen($row['url_flv']) > 0)
					{
						unlink(_VIDEOS_DIR_PATH . $row['url_flv']);
					}
				}
				
				if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg'))
				{
					unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg');
				}
				if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-social.jpg'))
				{
					unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-social.jpg');
				}

				$buffer = explode(',', $row['category']);
				foreach ($buffer as $k => $id)
				{
					$video_count[$id]++;
					if ($row['added'] <= $time_now)
					{
						$video_published_count[$id]++;
					}
				}
				
				if ($row['added'] <= $time_now)
				{
					$total_published_ids++;
				}

				if (pm_count($subtitles) > 0)
				{
					foreach ($subtitles as $k => $sub)
					{
						if (file_exists(_SUBTITLES_DIR_PATH . $sub['filename']) && strlen($sub['filename']) > 0)
						{
							unlink(_SUBTITLES_DIR_PATH . $sub['filename']);
						}
					}

					$sql = "DELETE FROM pm_video_subtitles
							WHERE uniq_id = '". $uniq_id ."'";
					@mysql_query($sql);
				}
			}
							
			if (pm_count($video_count) > 0 && ! $in_trash)
			{
				foreach ($video_count as $cid => $count)
				{
					if ('' != $cid && 0 != $cid)
					{
						$sql = "UPDATE pm_categories SET total_videos=total_videos-". $count;
						if ($video_published_count[$cid] > 0)
						{
							$sql .= ", published_videos = published_videos - ". $video_published_count[$cid];
						}
						$sql .= " WHERE id = '". $cid ."'";
						mysql_query($sql);
					}
				}
			}
		}
		
		if ( ! $in_trash)
		{
			update_config('total_videos', $config['total_videos'] - $total_ids);
			if ($total_published_ids)
			{
				update_config('published_videos', $config['published_videos'] - $total_published_ids);
			}
		}
		else
		{
			update_config('trashed_videos', $config['trashed_videos'] - $total_ids);
		}
		
		if (_MOD_SOCIAL)
		{
			foreach ($video_list_data as $uniq_id => $video)
			{
				remove_all_related_activity($video['id'], ACT_OBJ_VIDEO);
			}
		}
		
		$videos = a_list_videos('', '', $from, $limit, $page, $filter); 
	}
	else
	{
		echo pm_alert_warning('Please select something first.');
	}
}

if (($_POST['Submit'] == 'Trash selected') && ! csrfguard_check_referer('_admin_videos_listcontrols'))
{
	echo pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['Submit'] == 'Trash selected')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	$total_ids = pm_count($video_ids);
	
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);

		$video_list_data = get_video_list('', '', 0, $total_ids, 0, null, $video_ids);
		
		$sql = "SELECT uniq_id, mp4, direct FROM pm_videos_urls 
				WHERE uniq_id IN ($in_arr)";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_assoc($result))
		{
			foreach ($video_list_data as $k => $video)
			{
				if ($video['uniq_id'] == $row['uniq_id'])
				{
					$video_list_data[$k] = array_merge($video, $row);
					break;
				}
			}
		}
		
		foreach ($video_list_data as $k => $video)
		{
			$sql = "INSERT INTO pm_videos_trash (id, uniq_id, video_title, description, yt_id, yt_length, yt_thumb, category, submitted_user_id, submitted, added, url_flv, source_id, language, age_verification, yt_views, site_views, featured, restricted, allow_comments, allow_embedding, video_slug, mp4, direct, video_type)
					VALUES ('". $video['id'] ."',
							'". $video['uniq_id'] ."', 
							'". secure_sql($video['video_title']) ."', 
							'". secure_sql($video['description']) ."', 
							'". $video['yt_id'] ."', 
							'". $video['yt_length'] ."', 
							'". $video['yt_thumb'] ."', 
							'". $video['category'] ."', 
							'". $video['submitted_user_id'] ."',
							'". $video['submitted'] ."', 
							'". $video['added'] ."', 
							'". $video['url_flv'] ."', 
							'". $video['source_id'] ."', 
							'". $video['language'] ."', 
							'". $video['age_verification'] ."', 
							'". $video['yt_views'] ."', 
							'". $video['site_views'] ."', 
							'". $video['featured'] ."', 
							'". $video['restricted'] ."', 
							'". $video['allow_comments'] ."',
							'". $video['allow_embedding'] ."',
							'". secure_sql($video['video_slug']) ."',
							'". secure_sql($video['mp4']) ."',
							'". secure_sql($video['direct']) ."',
							'". $video['video_type'] ."')";
			
			if ($result = mysql_query($sql))
			{
				$sql = "DELETE FROM pm_videos 
						WHERE id = ". $video['id'];
				$result = mysql_query($sql);
				
				if ($result)
				{
					$sql = "DELETE FROM pm_videos_urls 
							WHERE uniq_id = '". $video['uniq_id'] ."'";
					$result = mysql_query($sql);
				}
			}
		}

		// update video count for each category
		$total_published_ids = 0;
		$video_count = array();
		$video_published_count = array();
		$time_now = time();
		
		foreach ($video_list_data as $k => $row)
		{
			$buffer = explode(',', $row['category']);
			foreach ($buffer as $kk => $id)
			{
				$video_count[$id]++;
				if ($row['added'] <= $time_now)
				{
					$video_published_count[$id]++;
				}
			}
			
			if ($row['added'] <= $time_now)
			{
				$total_published_ids++;
			}
		}
							
		if (pm_count($video_count) > 0)
		foreach ($video_count as $cid => $count)
		{
			if ('' != $cid && 0 != $cid)
			{
				$sql = "UPDATE pm_categories SET total_videos = total_videos - ". $count;
				if ($video_published_count[$cid] > 0)
				{
					$sql .= ", published_videos = published_videos - ". $video_published_count[$cid];
				}
				$sql .= " WHERE id = '". $cid ."'";
				mysql_query($sql);
			}
		}

		update_config('total_videos', $config['total_videos'] - $total_ids);
		update_config('trashed_videos', $config['trashed_videos'] + $total_ids);
		
		if ($total_published_ids)
		{
			update_config('published_videos', $config['published_videos'] - $total_published_ids);
		}
		
		$videos = a_list_videos('', '', $from, $limit, $page, $filter); 
		
		echo pm_alert_success('Videos removed successfully. You can restore them from the <a href="videos.php?filter=trash&page=1">Trash</a>.');
	}
	else
	{
		echo pm_alert_warning('Please select something first.');
	}
}

if ($_POST['Submit'] == 'Restore selected')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	$total_ids = pm_count($video_ids);
	$video_list_data = array();
	
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		
		$sql = "SELECT * 
				FROM pm_videos_trash 
				WHERE uniq_id IN ($in_arr)";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_assoc($result))
		{
			$video_list_data[] = $row;
		}
		mysql_free_result($result);
		
		foreach ($video_list_data as $k => $video)
		{
			$video_id = (count_entries('pm_videos', 'id', $video['id']) > 0) ? 'NULL' : $video['id'];
			
			$sql = "INSERT INTO pm_videos (id, uniq_id, video_title, description, yt_id, yt_length, yt_thumb, category, submitted_user_id, submitted, added, url_flv, source_id, language, age_verification, yt_views, site_views, featured, restricted, allow_comments, allow_embedding, video_slug, video_type)
					VALUES ('". $video_id ."',
							'". $video['uniq_id'] ."', 
							'". secure_sql($video['video_title']) ."', 
							'". secure_sql($video['description']) ."', 
							'". $video['yt_id'] ."', 
							'". $video['yt_length'] ."', 
							'". $video['yt_thumb'] ."', 
							'". $video['category'] ."', 
							'". $video['submitted_user_id'] ."', 
							'". $video['submitted'] ."', 
							'". $video['added'] ."', 
							'". $video['url_flv'] ."', 
							'". $video['source_id'] ."', 
							'". $video['language'] ."', 
							'". $video['age_verification'] ."', 
							'". $video['yt_views'] ."', 
							'". $video['site_views'] ."', 
							'". $video['featured'] ."', 
							'". $video['restricted'] ."', 
							'". $video['allow_comments'] ."',
							'". $video['allow_embedding'] ."',
							'". secure_sql($video['video_slug']) ."',
							'". $video['video_type'] ."')";
			
			if ($result = mysql_query($sql))
			{
				$sql = "INSERT INTO pm_videos_urls (uniq_id, mp4, direct) 
						VALUES ('". $video['uniq_id'] ."', 
								'". secure_sql($video['mp4']) ."',
								'". secure_sql($video['direct']) ."')";
				$result = mysql_query($sql);
				
				$sql = "DELETE FROM pm_videos_trash 
						WHERE id = ". $video['id'];
				$result = mysql_query($sql);
			}
		}

		// update video count for each category
		$total_published_ids = 0;
		$video_count = array();
		$video_published_count = array();
		$time_now = time();
		
		foreach ($video_list_data as $k => $row)
		{
			$buffer = explode(',', $row['category']);
			foreach ($buffer as $kk => $id)
			{
				$video_count[$id]++;
				if ($row['added'] <= $time_now)
				{
					$video_published_count[$id]++;
				}
			}
			
			if ($row['added'] <= $time_now)
			{
				$total_published_ids++;
			}
		}
							
		if (pm_count($video_count) > 0)
		foreach ($video_count as $cid => $count)
		{
			if ('' != $cid && 0 != $cid)
			{
				$sql = "UPDATE pm_categories SET total_videos = total_videos + ". $count;
				if ($video_published_count[$cid] > 0)
				{
					$sql .= ", published_videos = published_videos + ". $video_published_count[$cid];
				}
				$sql .= " WHERE id = '". $cid ."'";
				mysql_query($sql);
			}
		}

		update_config('total_videos', $config['total_videos'] + $total_ids);
		update_config('trashed_videos', $config['trashed_videos'] - $total_ids);
		
		if ($total_published_ids)
		{
			update_config('published_videos', $config['published_videos'] + $total_published_ids);
		}
		
		$videos = a_list_videos('', '', $from, $limit, $page, $filter); 
		
		echo pm_alert_success('Videos successfully restored.');
	}
	else
	{
		echo pm_alert_warning('Please select something first.');
	}
}

//	Mark video(s) as featured/regular video
if ($_POST['Submit'] == 'Mark as featured' || $_POST['Submit'] == 'Mark as regular')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	$total_ids = pm_count($video_ids);
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		if(strlen($in_arr) > 0)
		{
			$sql = "UPDATE pm_videos ";
			if ($_POST['Submit'] == 'Mark as featured')
			{
				$sql .= "SET featured = '1' ";
			}
			else
			{
				$sql .= "SET featured = '0' ";
			}
			$sql .=	" WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			
			if(!$result)
			{
				echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
			}
			else
			{
				echo pm_alert_success('The selected videos have been updated.');
			}
		}
		$videos = a_list_videos('', '', $from, $limit, $page, $filter); 
	}
	else
	{
		echo pm_alert_warning('Please select something first.');
	}
}

if (($_POST['Submit'] == 'Move') && ! csrfguard_check_referer('_admin_videos_listcontrols'))
{
	echo pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['Submit'] == 'Move')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	$new_cid   = (int) $_POST['move_to_category'];
	
	$total_ids = pm_count($video_ids);
	
	if ($new_cid == '' || !array_key_exists($new_cid, $categories))
	{
		echo pm_alert_info('Please select a category first.');
	}
	else
	{
		if($total_ids == 0)
		{
			echo pm_alert_warning('Please select something first.');	
		}
		else
		{
			$in_arr = '';
			for($i = 0; $i < $total_ids; $i++)
			{
				$in_arr .= "'" . $video_ids[ $i ] . "', ";
			}
			$in_arr = substr($in_arr, 0, -2);
			
			$sql = "SELECT category, added  
					FROM pm_videos 
					WHERE uniq_id IN (". $in_arr .")";
			$result = mysql_query($sql);
			if ( !$result)
			{
				echo pm_alert_error('There was an error while retrieving your data.<br />MySQL returned: '. mysql_error());
			}
			else
			{				
				$add = $total_ids;
				$add_published = 0;
				$deduct_total = array();
				$deduct_published = array();
				$time_now = time();
				
				while ($row = mysql_fetch_assoc($result))
				{
					if (strpos($row['category'], ','))
					{
						$buff = explode(',', $row['category']);
						foreach ($buff as $k => $v)
						{
							$deduct_total[ (int) $v ]++;
							if ($row['added'] <= $time_now)
							{
								$deduct_published[ (int) $v ]++;
							}
						}
					}
					else
					{
						$deduct_total[ (int) $row['category'] ]++;
						
						if ($row['added'] <= $time_now)
						{
							$deduct_published[ (int) $row['category'] ]++;
						}
					}
					
					if ($row['added'] <= $time_now)
					{
						$add_published++;
					}
				}

				mysql_free_result($result);
				
				// update pm_videos
				$sql = "UPDATE pm_videos 
						SET category = '". $new_cid ."' 
						WHERE uniq_id IN (". $in_arr .")";
				$result = mysql_query($sql);
				if ( !$result)
				{
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				}
				
				// update pm_categories (deduct video count)
				foreach ($deduct_total as $cid => $count)
				{
					$sql = "UPDATE pm_categories 
							SET total_videos = total_videos - ". $count ;
							
					if (pm_count($deduct_published[$cid]) > 0)
					{
						$sql .= ", published_videos = published_videos - ". $count;
					}
					
					$sql .= " WHERE id = '". $cid ."'";
					
					mysql_query($sql);
				}
				
				// update pm_categories (add video count)
				$sql = "UPDATE pm_categories 
						SET total_videos=total_videos+". $add .",
							published_videos = published_videos + ". $add_published ." 
						WHERE id = '". $new_cid ."'";
				
				$result = mysql_query($sql);
				
				echo pm_alert_success('Videos successfully moved to <strong>'. $categories[$new_cid]['name'].'</strong>.');
				
				// update table
				$videos = a_list_videos('', '', $from, $limit, $page, $filter); 
			}
		}
	}
}
if ($_POST['Submit'] == 'Restrict access' || $_POST['Submit'] == 'Derestrict access')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	$total_ids = pm_count($video_ids);
	
	if ($total_ids > 0)
	{
		$access = ($_POST['Submit'] == 'Restrict access') ? '1' : '0';
		
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		
		$sql = "UPDATE pm_videos 
				SET restricted = '". $access ."'
				WHERE uniq_id IN (". $in_arr .")";
		$result = mysql_query($sql);
			
		if ( ! $result)
		{
			echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
		}
		else
		{
			echo pm_alert_success('Videos updated successfully.');
			$videos = a_list_videos('', '', $from, $limit, $page, $filter, $filter_value); 
		}
	}
	else
	{
		echo pm_alert_warning('Please select something first.');
	}
}

//	Delete all videos
if($action == 9 && is_admin())
{
	//	clear database of all videos
	if (isset($_POST['Submit']) && ! csrfguard_check_referer('_admin_videos_deleteall'))
	{
		echo pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
		echo '</div><!-- .content -->';
		echo '</div><!-- .primary -->';
		echo '</div>';
	}
	else if (isset($_POST['Submit']))
	{
		if($_POST['Submit'] == 'Yes')
		{
			$files = array();
			$sql = "SELECT url_flv FROM pm_videos WHERE source_id = '1'";
			$result = mysql_query($sql);
			
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_assoc($result))
				{
					$files[] = $row['url_flv'];
				}
				mysql_free_result($result);
				
				foreach ($files as $k => $filename)
				{
					if (file_exists(_VIDEOS_DIR_PATH . $filename) && strlen($filename) > 0)
					{
						unlink(_VIDEOS_DIR_PATH . $filename);
					}
				}
			}
			
			$sql = "TRUNCATE TABLE pm_videos";
			$result = @mysql_query($sql);
			if(!$result)
			{
				echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
			}
			else
			{
			
				update_config('total_videos', 0);
				update_config('published_videos', 0);
				update_config('trashed_videos', 0);
				
				$sql = " UPDATE pm_categories SET total_videos = 0, published_videos = 0 ";
				@mysql_query($sql);
				
				//	pm_videos extension table
				$sql = "TRUNCATE TABLE pm_videos_urls";
				$result = @mysql_query($sql);
				if(!$result)
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				
				//	comments table
				$sql = "TRUNCATE TABLE pm_comments";
				$result = @mysql_query($sql);
				if(!$result)
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				
				// handle playlists @since v2.2 
				$sql = "TRUNCATE TABLE pm_playlist_items";
				$result = @mysql_query($sql);
				if(!$result)
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				
				$sql = "DELETE FROM pm_playlists 
						WHERE type NOT IN (". PLAYLIST_TYPE_WATCH_LATER .", ". PLAYLIST_TYPE_FAVORITES .", ". PLAYLIST_TYPE_LIKED .", ". PLAYLIST_TYPE_HISTORY .")";
				$result = @mysql_query($sql);
				if(!$result)
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				
				$sql = "UPDATE pm_playlists 
						SET items_count = 0 
						WHERE type IN (". PLAYLIST_TYPE_WATCH_LATER .", ". PLAYLIST_TYPE_FAVORITES .", ". PLAYLIST_TYPE_LIKED .", ". PLAYLIST_TYPE_HISTORY .")";
				$result = @mysql_query($sql);
				if(!$result)
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
					
				//	tags table
				$sql = "TRUNCATE TABLE pm_tags";
				$result = @mysql_query($sql);
				if(!$result)
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				
				//	reports table
				$sql = "TRUNCATE TABLE pm_reports";
				$result = @mysql_query($sql);
				if(!$result)
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				
				//	chart table
				//	tags table
				$sql = "TRUNCATE TABLE pm_chart";
				$result = @mysql_query($sql);
				if(!$result)
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				
				// empty trash
				$sql = "TRUNCATE TABLE pm_videos_trash";
				$result = @mysql_query($sql);
				if(!$result)
					echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
			}

			echo pm_alert_success('Nothing like with a fresh start! ');
			echo '</div><!-- .content -->';
			include('footer.php');
			exit();
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0;URL=videos.php" />';
			exit();
		}
	}
	else
	{
		echo pm_alert_error('Are you sure you want to <strong>delete all your videos</strong>?<br /><br />This operation is <strong>not reversible</strong>. Clicking \'Yes\' will <strong>empty your entire video database</strong>.'); 
		?>
		<form name="delete" method="post" action="videos.php?action=9">
			<?php echo csrfguard_form('_admin_videos_deleteall');?>
			<input type="submit" name="Submit" value="Yes" class="btn btn-sm btn-danger"/> <input type="submit" name="Submit" value="Cancel" class="btn btn-sm btn-secondary" />
		</form>
		</div><!-- .content -->
	<?php
	include('footer.php');
	exit();
	}
}
else if ($action == 9)
{
	restricted_access(true);
}

// Empty Trash
if ($action == 10 && is_admin()) 
{
		$video_list_data = array();
		$in_arr = '';
		$sql = "SELECT id, uniq_id, category, url_flv, added, submitted, source_id 
				FROM pm_videos_trash";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_assoc($result))
		{
			$video_list_data[$row['uniq_id']] = $row;
			$in_arr .= "'" . $row['uniq_id'] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		mysql_free_result($result);
		
		mysql_query("DELETE FROM pm_comments WHERE uniq_id IN (" . $in_arr . ")");
		mysql_query("DELETE FROM pm_reports WHERE entry_id IN (" . $in_arr . ")");
		mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id IN (" . $in_arr . ")");
		mysql_query("DELETE FROM pm_chart WHERE uniq_id IN (" . $in_arr . ")");
		mysql_query("DELETE FROM pm_tags WHERE uniq_id IN (" . $in_arr . ")");
		mysql_query("DELETE FROM pm_bin_rating_meta WHERE uniq_id IN (" . $in_arr . ")");
		mysql_query("DELETE FROM pm_bin_rating_votes WHERE uniq_id IN (" . $in_arr . ")");
		
		$ids = array();
		foreach ($video_list_data as $uniq_id => $video)
		{
			$ids[] = $video['id'];
			
			// handle playlists
			$playlist_ids = array();
			
			$sql = "SELECT list_id 
					FROM pm_playlist_items 
					WHERE video_id = ". $video['id'];
			
			if ($result = @mysql_query($sql))
			{
				$in_playlists = false;
				while ($row = mysql_fetch_assoc($result))
				{
					$playlist_ids[] = (int) $row['list_id'];
					$in_playlists = true;
				}
				mysql_free_result($result);
			
				if ($in_playlists)
				{
					$sql = "DELETE FROM pm_playlist_items
							WHERE video_id = ". $video['id'];
					@mysql_query($sql);
	
					$sql = "UPDATE pm_playlists 
							SET items_count = items_count - 1 
							WHERE list_id IN (". implode(',', $playlist_ids) .")";
					@mysql_query($sql);
				}
			}
		}
		
		mysql_query("DELETE FROM pm_meta WHERE item_id IN (". implode(',', $ids) .") AND item_type = ". IS_VIDEO);
		unset($ids);
		
		foreach ($video_list_data as $uniq_id => $row)
		{
			$subtitles = a_get_video_subtitles($uniq_id);

			// delete hosted files
			if ($row['source_id'] == 1)
			{
				if (file_exists(_VIDEOS_DIR_PATH . $row['url_flv']) && strlen($row['url_flv']) > 0)
				{
					unlink(_VIDEOS_DIR_PATH . $row['url_flv']);
				}
			}
			
			if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg'))
			{
				unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg');
			}
			if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-social.jpg'))
			{
				unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-social.jpg');
			}

			if (pm_count($subtitles) > 0)
			{
				foreach ($subtitles as $k => $sub)
				{
					if (file_exists(_SUBTITLES_DIR_PATH . $sub['filename']) && strlen($sub['filename']) > 0)
					{
						unlink(_SUBTITLES_DIR_PATH . $sub['filename']);
					}
				}

				$sql = "DELETE FROM pm_video_subtitles
						WHERE uniq_id = '". secure_sql($uniq_id) ."'";
				@mysql_query($sql);
			}
		}

		update_config('trashed_videos', 0);

		if (_MOD_SOCIAL)
		{
			foreach ($video_list_data as $uniq_id => $video)
			{
				remove_all_related_activity($video['id'], ACT_OBJ_VIDEO);
			}
		}
		
		$sql = "TRUNCATE TABLE pm_videos_trash";
		$result = mysql_query($sql);
		
		if ( ! $result = mysql_query($sql))
		{
			echo pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
		}
		else
		{
			echo pm_alert_success('Videos removed successfully.');
		}
		
		$videos = a_list_videos('', '', $from, $limit, $page, $filter); 
}
else if ($action == 10)
{
	restricted_access(true);
}
?>


<?php if ( $total_videos == 0 && empty($filter)) : ?> <!--Start ifempty-->

<div class="align-middle text-center mt-3 pt-3">
	<i class="mi-video-library mi-3x text-muted mb-3"></i>
	<h6 class="font-weight-semibold text-grey mb-1">No videos yet</h6>
	<p class="text-grey mb-3 pb-1">Add a new video now.</p>
	<a href="#addVideo" data-toggle="modal" class="btn btn-sm bg-blue border-blue border-2 rounded font-weight-semibold">Add Video</a>
</div>

<?php else : ?> <!--Else ifempty-->

<?php if ( $config['trashed_videos'] == 0 && $filter == 'trash') : ?> <!--Start ifempty-->

<div class="align-middle text-center mt-3 pt-3">
	<i class="icon-bin icon-2x text-muted mb-3"></i>
	<h6 class="font-weight-semibold text-grey mb-1">Trash is empty</h6>
	<p class="text-grey mb-3 pb-1">You can restore any deleted videos from here.</p>
</div>

<?php else : ?> <!--Else ifempty-->


<div id="video_check_message" class="alert alert-info" style="display: none;"></div>
<div class="card card-blanche">	
	<div class="card-body">
		<div class="row">
			<div class="col-sm-12 col-md-8">
				<?php if ( ! empty($_POST['keywords'])) : ?>
				<h5 class="font-weight-semibold mt-2">SEARCH RESULTS FOR <mark><?php echo $_POST['keywords']; ?></mark> <a href="#" onClick="parent.location='videos.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear search results"><i class="icon-cancel-circle2"></i></a></h5>
				<?php endif; ?>

				<?php if ( ! empty($filter) && $filter != 'added' && $filter != 'trash') : ?>
				<h5 class="font-weight-semibold mt-2">FILTERED RESULTS <a href="#" onClick="parent.location='videos.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear filter"><i class="icon-cancel-circle2"></i></a></h5>
				<?php endif; ?>

				<?php if ( $filter == 'trash') : ?>
				<h5 class="font-weight-semibold mt-2">DELETED VIDEOS <a href="#" onClick="parent.location='videos.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear filter"><i class="icon-cancel-circle2"></i></a></h5>
				<?php endif; ?>

			</div><!-- .col-md-8 -->

			<div class="col-sm-12 col-md-4 d-none d-md-block">
				<div class="float-right">
					<form name="videos_per_page" action="videos.php" method="get" class="form-inline pull-right">
						<input type="hidden" name="ui_pref" value="videos_pp" />
						<label class="mr-1" for="inlineFormCustomSelectPref">Videos/page</label>
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



<form name="videos_checkboxes" id="videos_checkboxes" action="videos.php?page=<?php echo $page;?>&filter=<?php echo $filter;?>&fv=<?php echo $filter_value;?>" method="post">
	<div class="table-responsive">
		 <table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
		  <thead class="border-top-1 border-top-light">
		   <tr>
			<th align="center" class="text-center" width="3%">
				<input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/>
			</th>
			<th width="2%" class="d-sm-none d-md-table-cell"></th>
			<th width="5%" class="d-sm-none d-md-table-cell text-center"></th>
			<th width="300">Video title</th>
			<th width="190" class="d-sm-none d-md-table-cell">Category</th>
			<th width="90" class="text-center">
				<?php if ( ! $in_trash) : ?>
				<a href="videos.php?filter=added&fv=<?php echo ($filter_value == 'desc' && $filter == 'added') ? 'asc' : 'desc';?>" data-popup="tooltip" data-html="true" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'added') ? 'ascending' : 'descending';?>">Added</a>
				<?php else : ?>
				Added
				<?php endif; ?>
			</th>
			<th width="65" class="text-center">
				<?php if ( ! $in_trash) : ?>
				<a href="videos.php?filter=views&fv=<?php echo ($filter_value == 'desc' && $filter == 'views') ? 'asc' : 'desc';?>" data-popup="tooltip" data-html="true" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'views') ? 'ascending' : 'descending';?>">Views</a></th>
				<?php else : ?>
				Views
				<?php endif; ?>
			<th style="width: 110px;" class="d-sm-none d-md-table-cell text-center">Comments</th>
			<th width="3%" class="text-center">Status</th>
			<th style="width: 110px;" class="text-center">Action</th>
		   </tr>
		  </thead>

		  <tbody>

			<?php echo $videos; ?>
			
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
			<?php if ( ! $in_trash) : ?>
			<div class="float-left form-inline">
				<div class="input-group input-group-sm">
					<label class="mr-1">Move to</label>
					<?php 
					$categories_dropdown_options = array(
												'attr_name' => 'move_to_category',
												'attr_id' => 'select_move_to_category',
												'attr_id' => '',
												'attr_class' => 'form-control custom-select custom-select-sm border-right-0',
												'first_option_text' => 'category...',
												'selected' => ($_POST['move_to_category']) ? $_POST['move_to_category'] : 0
												);
					echo categories_dropdown($categories_dropdown_options);
					?>
					<div class="input-group-append">
						<button type="submit" name="Submit" value="Move" data-loading-text="Moving..." class="btn btn-sm btn-light" onClick="if ($('select[name=move_to_category] option:selected').val() == '' || $('select[name=move_to_category] option:selected').val() == '-1') {alert('Please select a category first.'); return false;}" />Move</button>
					</div>
				</div>
			</div>
			<?php endif; ?>
			</div>

			<div class="col-sm-12 col-md-6 mb-0">
				<div class="float-right">
					<?php if ( ! $in_trash) : ?>
					<div class="btn-group dropup">
						<button type="button" class="btn btn-sm btn-outline bg-primary-400 text-primary-400 border-primary-400 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mark as</button> 
						<div class="dropdown-menu">
							<button type="submit" name="Submit" value="Mark as featured" class="dropdown-item">Featured</button>
							<button type="submit" name="Submit" value="Mark as regular" class="dropdown-item">Regular (non-Featured)</button>
							<button type="submit" name="Submit_restrict" value="Restrict access" class="dropdown-item" data-popup="tooltip" data-html="true" data-placement="left" title="Private videos will be available only to registered users.">Private</button>
							<button type="submit" name="Submit_derestrict" value="Derestrict access" class="dropdown-item" data-popup="tooltip" data-html="true" data-placement="left" title="Make selected videos public. Remove any viewing restrictions.">Public</button>
						</div>
					</div>
					<?php endif; ?>
					<button type="submit" name="VideoChecker" id="VideoChecker" value="Check status" class="btn btn-sm btn-outline bg-primary-400 text-primary-400 border-primary-400 d-none d-md-inline" onclick="javascript: return false;">Check status</button>

					<?php  if ( is_admin() ) : ?>
					<div class="d-inline">
						<?php if ( ! $in_trash) : ?>
						<div class="btn-group btn-group-sm dropup">
							<button type="submit" name="Submit" value="Trash selected" class="btn btn-sm btn-danger" onClick="return confirm_delete_all();">Delete</button> 
							<button type="button" class="btn btn-sm btn-danger dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false"></button> 
							<div class="dropdown-menu border-warning border-2 text-warning">
								<a href="videos.php?action=9" class="dropdown-item font-weight-bold" data-popup="tooltip" data-html="true" data-placement="top" data-original-title="This action will remove the entire video database permanently!">DELETE ALL VIDEOS</a>
							</div>
						</div>
						<?php else : ?>
						<button type="submit" name="Submit" value="Restore selected" class="btn btn-sm btn-outline btn-outline bg-success-600 text-success-600 border-success-600">Restore</button>
						<div class="btn-group btn-group-sm dropup">
							<button type="submit" name="Submit" value="Delete selected" class="btn btn-sm btn-danger" onClick="return confirm_delete_all();">Delete Permanently</button> 
							<button type="button" class="btn btn-sm btn-danger dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false"></button> 
							<div class="dropdown-menu border-warning border-2 text-warning">
								<a href="videos.php?action=10&filter=trash" class="dropdown-item font-weight-bold" data-popup="tooltip" data-html="true" title="This action will remove all videos from your trash permanently!" onClick="return confirm_delete_all();"><i class="icon-trash-alt"></i> EMPTY TRASH</a>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<?php  endif; ?>
					<input type="hidden" name="filter" id="listing-filter" value="<?php echo $filter;?>" />
					<input type="hidden" name="fv" id="listing-filter_value"value="<?php echo $filter_value;?>" /> 
				</div>
			</div>
		</div><!-- #list-controls -->
	</div>
<?php
echo csrfguard_form('_admin_videos_listcontrols');
?>
</form>

</div><!--.card-->
<?php endif; ?> <!--End ifempty-->

<?php endif; ?> <!--End ifempty-->
</div><!-- .content -->
<?php
include('footer.php');