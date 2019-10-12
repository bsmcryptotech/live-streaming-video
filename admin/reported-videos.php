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
$_page_title = 'Reported videos';
include('header.php');

$action	 = (int) $_GET['a'];
$id		 = (int) $_GET['rid'];

$page	 = (int) $_GET['page'];

if(empty($page) || !is_numeric($page) || $page == '')
   $page = 1;
$limit	 = get_admin_ui_prefs('videos_pp');
$from	 = $page * $limit - ($limit);

//	Batch Delete Reports
if($_POST['Submit'] == 'Delete reports')
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
			$sql = "DELETE FROM pm_reports WHERE entry_id IN (" . $in_arr . ")";
			$result = @mysql_query($sql);

			if ( ! $result)
			{
				$info_msg = pm_alert_error('An error occured while updating your database.<br />MySQL returned: '.mysql_error());
			}
			else
			{
				$info_msg = pm_alert_success('The selected reports were removed.');
			}
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}
//	Batch Delete Videos
if (($_POST['Submit'] == 'Delete videos') && ! csrfguard_check_referer('_admin_reports_deletevideos'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['Submit'] == 'Delete videos')
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
			$sql = "SELECT id, uniq_id, category, url_flv, added, source_id FROM pm_videos WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_assoc($result))
			{
				$video_list_data[$row['uniq_id']] = $row;
			}
			mysql_free_result($result);

			$sql = "DELETE FROM pm_videos WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			
			if(!$result)
			{
				$info_msg = pm_alert_error('An error occured while updating your database.<br />MySQL returned: '.mysql_error());
			}
			else
			{
				mysql_query("DELETE FROM pm_comments WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_reports WHERE entry_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id IN (" . $in_arr . ")");
				//mysql_query("DELETE FROM pm_favorites WHERE uniq_id IN (" . $in_arr . ")"); // @deprecated since v2.2
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
		
				$info_msg = pm_alert_success('The selected videos have been removed.');
			}
			
			// update video count for each category
			$video_count = array();
			$video_published_count = array();
			$time_now = time();
			foreach ($video_list_data as $uniq_id => $row)
			{
				// delete hosted files
				if ($row['source_id'] == 1)
				{
					if (file_exists(_VIDEOS_DIR_PATH . $row['url_flv']) && strlen($row['url_flv']) > 0)
					{
						unlink(_VIDEOS_DIR_PATH . $row['url_flv']);					
					}
					if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg'))
					{
						unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg');
					}
					if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-social.jpg'))
					{
						unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-social.jpg');
					}
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
			}
			
			if (pm_count($video_count) > 0)
			foreach ($video_count as $cid => $count)
			{
				if ('' != $cid)
				{
					$sql = "UPDATE pm_categories SET total_videos=total_videos-". $count;
					if ($video_published_count[$id] < 0)
					{
						$sql .= ", published_videos = published_videos - ". $video_published_count[$id];
					}
					$sql .= " WHERE id = '". $cid ."'";
					mysql_query($sql);
				}
			}
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}

//	DELETE REPORT
if($action == 1) { 
	
	@mysql_query("DELETE FROM pm_reports WHERE id = '".$id."'");
	$info_msg = pm_alert_success('The report has been removed.');
}
//	DELETE ALL REPORTS
if($action == 2) { 

	@mysql_query("TRUNCATE TABLE pm_reports");
	$info_msg = pm_alert_success('The reports were deleted.');
}

$total_rvideos = count_entries('pm_reports', 'r_type', '1');

if($total_rvideos - $from == 1)
	$page--;
$reported = a_list_vreports( '1', $from, $limit, $page); // 1-videos, 2-comments 

if($total_rvideos - $from == 1)
	$page++;
// generate smart pagination
$filename = 'reported-videos.php';
$pagination = '';

$pagination = a_generate_smart_pagination($page, $total_rvideos, $limit, 1, $filename, '');

?>
<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title; ?></span></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<h5 class="font-weight-semibold mb-0 text-center"><?php echo pm_number_format($total_rvideos); ?></h5>
					<span class="text-muted font-size-sm">report(s)</span>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="videos.php" class="breadcrumb-item">Videos</a>
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
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>This page will list all the problematic videos from your site as defined by your visitors or by the video checking bot.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content content-full-width">

<?php if ( $total_rvideos == 0 && empty($filter)) : ?> <!--Start ifempty-->

<div class="align-middle text-center mt-3 pt-3">
	<i class="icon-warning icon-3x text-muted mb-3"></i>
	<h6 class="font-weight-semibold text-grey mb-1">No reports yet</h6>
	<p class="text-grey mb-3 pb-1">Reported videos will appear here.</p>
</div>

<?php else : ?> <!--Else ifempty-->
	<div id="video_check_message" class="alert alert-info" style="display: none;"></div>
	
	<?php echo $info_msg; ?>


<div class="card card-blanche">
	<div class="card-body">
	</div><!--.card-body-->

	<form name="reported_videos_checkboxes" action="reported-videos.php?page=<?php echo $page;?>" method="post">
	<div class="datatable-scroll">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
	 <thead>
	  <tr> 	
		<th align="center" class="text-center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
		<th width="2%">&nbsp;</th>
		<th width="5%"></th>
		<th width="">Video</th>
		<th width="20%">Reason</th>
		<th width="10%" class="text-center">Added by</th>
		<th width="2%">&nbsp;</th>
		<th align="center" style="width:90px;" class="text-center">Action</th>
	  </tr>
	 </thead>
	 <tbody>
		<?php echo $reported; ?>
		
		<?php if ($pagination != '') : ?>
		<tr class="tablePagination">
			<td colspan="8" class="tableFooter">
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
			
			<div class="float-right">
				<div class="btn-group">
					<button type="submit" name="VideoChecker" id="VideoChecker" value="Check status" class="btn btn-outline bg-primary-400 text-primary-400 border-primary-400" onclick="javascript: return false;">Check status</button>
				</div>
				<div class="btn-group dropup">
					<button type="submit" name="Submit" value="Delete reports" class="btn btn-warning">Delete reports</button> 
					<button type="button" class="btn btn btn-warning dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false"></button> 
					<div class="dropdown-menu border-warning border-2 text-warning">
						<a href="#" onClick="del_allreports()" class="dropdown-item font-weight-bold">DELETE ALL REPORTS</a>
					</div>
				</div>

				<div class="btn-group">
					<button type="submit" name="Submit" value="Delete videos" class="btn btn-danger">Delete videos</button>
				</div>
			</div>
			</div>
		</div>
	</div>
	<?php echo csrfguard_form('_admin_reports_deletevideos');?>
	</form>

</div><!--.card-->
<?php endif ?> <!--End ifempty-->
</div>
<!-- /content area -->
<?php
include('footer.php');