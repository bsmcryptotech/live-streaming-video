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

$showm = '6';
$load_scrolltofixed = 1;
$_page_title = 'Activity stream';
include('header.php');

$action = $_GET['a'];
$aid = (int) $_GET['aid'];
$page = (int) $_GET['page'];

if(empty($page))
	$page = 1;
$limit = 20;	//	users per page
$from = $page * $limit - ($limit);

$filter = '';
$filters = array('type', 'user_id'); 
$filter_value = '';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
}

if ($_POST['Submit'] != '' && ! csrfguard_check_referer('_admin_members_activity'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['Submit'] != '') 
{
	$activity_ids = $_POST['activity_id'];
	if (pm_count($activity_ids) > 0)
	{
		$sql = "DELETE FROM pm_activity 
				WHERE activity_id IN (". implode(',', $activity_ids) .")";
		$result = @mysql_query($sql);
		$affected_rows = mysql_affected_rows();
		
		if ( ! $result)
		{
			$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
		}
		else
		{
			if ($affected_rows == 1)
			{
				$info_msg = pm_alert_success('1 activity deleted.');
			}
			else
			{
				$info_msg = pm_alert_success($affected_rows .' activities deleted.');
			}
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}

if ($action == 'delete' && ! csrfguard_check_referer('_admin_members_activity'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
elseif ($aid != 0 && $action == 'delete')
{
	$result = delete_activity($aid);
	
	if ( ! $result)
	{
		$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
	}
	else
	{
		$info_msg = pm_alert_success('1 activity deleted.');
	}
}

$activity_stream_nonce = csrfguard_raw('_admin_members_activity');

// Search
if($_GET['keywords'] != '')
{
	$search_query = ($_POST['keywords'] != '') ? trim($_POST['keywords']) : trim($_GET['keywords']);
	
	$filter_value = username_to_id($search_query);
	$filter = 'user_id';
} 

if ($filter != '')
{
	switch ($filter)
	{
		case 'user_id': 
			
			$total_items = count_entries('pm_activity', 'user_id', $filter_value);
		break;
		
		case 'type':
			$total_items = count_entries('pm_activity', 'activity_type', $filter_value);
		break;
	}
}
else
{
	$total_items = count_entries('pm_activity', '', '');
}

if($total_items - $from == 1)
	$page--;

$items = admin_get_activities($from, $limit, $page, $filter, $filter_value);

// generate smart pagination
$filename = 'activity-stream.php';
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('?', $uri);
$uri[1] = str_replace(array("<", ">", '"', "'", '/'), '', $uri[1]);
parse_str($uri[1], $temp);
unset($temp['_pmnonce'], $temp['_pmnonce_t'], $temp['a'], $temp['aid']);
$uri[1] = http_build_query($temp);

$pagination = '';
$pagination = a_generate_smart_pagination($page, $total_items, $limit, 1, $filename, $uri[1]);

if ($config['allow_emojis'])
{
	if ( ! class_exists('Emojione\\Client'))
	{
		include(ABSPATH .'include/emoji/autoload.php');
	} 
	$emoji_client = new Emojione\Client(new Emojione\Ruleset());
	$emoji_client->ascii = true;
	$emoji_client->unicodeAlt = false;
}

?>
<script type="text/javascript">
paused = false;
// set minutes
var mins = 1;
// calculate the seconds 
var secs = mins * 60;
var t=0;
  var flagTimer='resume';
function countdown() {
	t = setTimeout('Decrement()',1000);
}
function Decrement() {
	if (document.getElementById) {
		minutes = document.getElementById("minutes");
		seconds = document.getElementById("seconds");
		// if less than a minute remaining
		if (seconds < 59) {
			seconds.value = secs;
		} else {
			minutes.value = getminutes();
			seconds.value = getseconds();
		}
		secs--;
		t= setTimeout('Decrement()',1000);
	}
}
function getminutes() {
	// minutes is seconds divided by 60, rounded down
	mins = Math.floor(secs / 60);
	return mins;
}
function getseconds() {
	// take mins remaining (as seconds) away from total seconds remaining
	return secs-Math.round(mins *60);
}
function pause() { 
  if( flagTimer=='resume')
  {
    clearTimeout(t);
    t=0;
	document.getElementById('Pause').innerHTML="<i class='icon-play3'></i>";
    flagTimer='pause';
  }
  else
  {
  	document.getElementById('Pause').innerHTML="<i class='icon-pause'></i>";
    flagTimer='resume';
	resume();
  }
  
}
function resume() {
	t= setTimeout('Decrement()',1000);
}
</script>


<!-- Main content -->
<div class="content-wrapper">

<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title; ?></h4>
			</div>
			<a href="#" class="header-elements-toggle text-default d-md-none"><i class="mi-search"></i></a>
			<div class="header-elements with-search d-none">
				<div class=" d-flex-inline align-self-center ml-auto">
						<form name="search" action="activity-stream.php" method="get" class="form-search-listing form-inline float-right">
							<div class="input-group input-group-sm input-group-search">
								<div class="input-group-append">
									<input name="keywords" type="text" value="<?php echo $_GET['keywords']; ?>" class="search-query search-quez input-medium form-control form-control-sm border-right-0" placeholder="Enter keyword" id="form-search-input" />
								</div>
								<select name="search_type" class="form-control form-control-sm border-left-0 border-right-0">
									<option value="username" <?php echo ($_GET['search_type'] == "username") ? 'selected="selected"' : ''; ?> >Username</option>
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
					<a href="users.php" class="breadcrumb-item">Users</a>
					<a href="activity-stream.php" class="breadcrumb-item active"><?php echo $_page_title; ?></a>
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
	<div class="content content-full-width">
	
	<?php echo $info_msg; ?>

<div class="card card-blanche">

	<div class="card-body">

		<?php if ( ! empty($_GET['keywords'])) : ?>
		<h5 class="font-weight-semibold mt-2 float-left">SEARCH RESULTS FOR <mark><?php echo $_GET['keywords']; ?></mark> <a href="#" onClick="parent.location='activity-stream.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear search results"><i class="icon-cancel-circle2"></i></a></h5>
		<?php endif; ?>

		<?php if ( $filter == 'type' ) : ?>
		<h5 class="font-weight-semibold mt-2 float-left">FILTERED RESULTS <a href="#" onClick="parent.location='activity-stream.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear filter"><i class="icon-cancel-circle2"></i></a></h5>
		<?php endif; ?>

		<div class="float-right d-inline-block mt-1">
			<ul class="nav nav-pills nav-pills-bottom">
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle <?php echo ( ! empty($_GET['filter'])) ? 'active' : ''?>" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><i class="mi-filter-list"></i> Activity Filter</a>
					<ul class="dropdown-menu">
					<?php 
					$activity_types = activity_load_options();
					foreach ($activity_types as $type => $v) : ?>
					<li>
						<a class="nav-link <?php echo ($filter_value == $type) ? 'active' : ''; ?>" href="activity-stream.php?filter=type&fv=<?php echo $type;?>"><?php echo $activity_labels[$type];?><?php echo ($filter_value == $type) ? ' <span class="text-muted">('. pm_number_format($total_members) .')</span>' : ''; ?></a>
					</li>
					
					<?php endforeach;?> 
					</ul>
				</li>
			</ul>
		</div>


	</div><!--.card-body-->

		<form name="activity_checkboxes" id="activity_checkboxes" action="activity-stream.php?page=<?php echo $page;?>&filter=<?php echo $filter;?>&fv=<?php echo $filter_value;?>" method="post">
		<div class="datatable-scroll">
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
		 <thead>
		  <tr>
		   <th align="center" class="text-center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
		   <th width="10%">Username</th>
		   <th>Activity</th>
		   <th width="10%" class="text-center">Date</th>
		   <th style="width: 90px;" class="text-center">Action</th>
		  </tr>
		 </thead>
		 <tbody>
		  
		  <?php 
		  if (pm_count($items) == 0) : ?>
		  <tr>
		  	<td colspan="6" align="center" class="text-center">No activity yet.</td>
		  </tr>
		  <?php else : ?>
		  <?php 
			$banlist = get_banlist();
		  	$time_now = time();
		  ?>
		  <?php foreach ($items as $activity_id => $activity) : ?>
		  <tr id="activity-<?php echo $activity_id;?>">
		  	<td align="center" class="text-center">
		  		<input name="activity_id[]" type="checkbox" value="<?php echo $activity['activity_id'];?>" />
			</td>
			<td>
				<?php if ($activity['user_id'] != 0) : ?>
				<a href="activity-stream.php?keywords=<?php echo $activity['username'];?>&search_type=username&submit=Search" target="_blank" data-popup="tooltip" data-original-title="Show activity only form this user"><?php echo (array_key_exists($activity['user_id'], $banlist)) ? $activity['username'] .' <span class="badge badge-danger float-right">Banned</span>' : $activity['username'];?></a>

				<a href="<?php echo get_profile_url($activity);?>" target="_blank" class="text-muted" data-popup="tooltip" data-original-title="View profile"><i class="mi-open-in-new"></i></a>
				
				<?php else : ?>
				<?php echo ($activity['username'] != '') ? $activity['username'] : 'Visitor'; ?>
				<?php endif;?>
			</td>
			<td>
				<?php if ($activity['hide'] == 1) : ?>
				<a href="#" rel="tooltip" title="The user chose to hide this activity from his/her profile."><span class="badge badge-info">Hidden</span></a>
				<?php endif; ?>

				<?php
				switch ($activity['activity_type'])
				{
					case ACT_TYPE_LIKE:
					?>
						<!--<i class="icon-thumbs-up"></i>-->
					<?php
						echo ucfirst($lang['activity_'. $activity['activity_type']]);
					break;
					
					case ACT_TYPE_DISLIKE:
					?>
						<!--<i class="icon-thumbs-down"></i>-->
					<?php
						echo ucfirst($lang['activity_'. $activity['activity_type']]);
					break;
					
					case ACT_TYPE_COMMENT:
						echo ucfirst($lang['activity_'. $activity['activity_type']]) .' '. $lang['activity_obj_'.$activity['target_type']];
					break;
					
					case ACT_TYPE_STATUS:
						?>
						<strong>Updated status:</strong>
						<?php
						if (str_word_count($activity['metadata']['statustext'], 0) > 30)
						{
							preg_match('/^(.{1,255})\b/s', $activity['metadata']['statustext'], $matches);
							?>
							<span id="excerpt-<?php echo $activity_id;?>">
								<?php echo str_replace('<br />', '', $matches[1]); ?>...
							</span>
							<a href="#" id="show-more-<?php echo $activity_id;?>" title="Show more">show more</a>
							<span id="full-text-<?php echo $activity_id;?>" style="display:none;">
								<?php echo ($config['allow_emojis'] == 1) ? $emoji_client->shortnameToImage($activity['metadata']['statustext']) : $activity['metadata']['statustext']; ?>
							</span>
							<a href="#" id="show-less-<?php echo $activity_id;?>" style="display:none;" title="Show less">show less</a>
							<?php
						}
						else
						{
							echo ($config['allow_emojis'] == 1) ? $emoji_client->shortnameToImage($activity['metadata']['statustext']) : $activity['metadata']['statustext'];
						}
					break;
					default:
						echo ucfirst($lang['activity_'. $activity['activity_type']]);
					break;
				}
				
				if ($activity['object_id'] != 0)
				{
					$meta = $activity['metadata']['object'];
					
					switch ($activity['object_type'])
					{
						case ACT_OBJ_USER:
						?>
							<a href="<?php echo $meta['profile_url'];?>"><?php echo $meta['username'];?></a>
						<?php
						break;
						
						case ACT_OBJ_VIDEO:
						?>
							<a href="<?php echo $meta['video_href'];?>"><?php echo $meta['video_title'];?></a>
						<?php
						break;
						
						case ACT_OBJ_COMMENT:
						?>
						<?php
						break;
						
						case ACT_OBJ_ARTICLE:
						?>
							<a href="<?php echo $meta['link'];?>"><?php echo $meta['title'];?></a>
						<?php
						break;
						
						case ACT_OBJ_PROFILE:
						?>
						<?php
						break;
						
						case ACT_OBJ_PLAYLIST:
						?>
						<?php
						break;
						
						case ACT_OBJ_STATUS:
						?>
						<?php
						break;
						
					}
				}
				
				if ($activity['target_id'] != 0)
				{
					$meta = $activity['metadata']['target'];
					
					switch ($activity['target_type'])
					{
						case ACT_OBJ_USER:
						?>
							<a href="<?php echo $meta['profile_url'];?>"><?php echo $meta['username'];?></a>
						<?php
						break;
						
						case ACT_OBJ_VIDEO: 
						?>
							<a href="<?php echo $meta['video_href'];?>"><?php echo $meta['video_title'];?></a>
						<?php 
						break;
						
						case ACT_OBJ_COMMENT:
						?>
						<?php
						break;
						
						case ACT_OBJ_ARTICLE:
						?>
							<a href="<?php echo $meta['link'];?>"><?php echo $meta['title'];?></a>
						<?php
						break;
						
						case ACT_OBJ_PROFILE:
						?>
						<?php
						break;
						
						case ACT_OBJ_PLAYLIST:
						?>
						<?php
						break;
						
						case ACT_OBJ_STATUS:
						?>
						<?php
						break;
						
					}
				}
				
				?>
				
			</td>
			<td align="center" class="text-center" width="15%">
				<span data-popup="tooltip" data-container="body" data-original-title="<?php echo date('l, F j, Y g:i A', $activity['time']);?>" class="font-size-xs">
					<?php echo ($time_now - $activity['time'] <= (86400 * 3)) ? time_since($activity['time']) .' ago' : date('M d, Y', $activity['time']);?>
				</span>
			</td>
			<td align="center" class="text-center table-col-action">
				<a href="#" onclick="javascript: del_activity_id(<?php echo $activity_id;?>, <?php echo $page;?>)" rel="tooltip" title="Delete activity" class="list-icons-item text-danger"><i class="icon-bin"></i></a>
			</td>
		  </tr>
		  <?php endforeach; ?>
		  <?php endif; ?>
		  
		  <?php if ($pagination != '') : ?>
		  <tr class="tablePagination">
			<td colspan="6" class="tableFooter">
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
				    <button type="submit" name="Submit" value="Delete selected" class="btn btn-sm btn-danger" onClick="return confirm_delete_all();">Delete selected</button>
					</div>
				</div>
			</div>
			</div><!-- #list-controls -->
		</div>
		<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $activity_stream_nonce['_pmnonce'];?>" value="<?php echo $activity_stream_nonce['_pmnonce'];?>" />
		<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $activity_stream_nonce['_pmnonce'];?>" value="<?php echo $activity_stream_nonce['_pmnonce_t'];?>" />
		<input type="hidden" name="filter" id="listing-filter" value="<?php echo $filter;?>" />
		<input type="hidden" name="fv" id="listing-filter_value"value="<?php echo $filter_value;?>" />
		</form>


	</div><!--.card-->
</div>
<!-- /content area -->
<?php
include('footer.php');