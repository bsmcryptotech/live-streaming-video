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
$_page_title = 'Users';
include('header.php');

$action = (int) $_GET['a'];
$userid = (int) trim($_GET['uid']);

$page = (int) $_GET['page'];

$page = ( ! $page) ? 1 : $page;
$limit = get_admin_ui_prefs('users_pp');
$from = $page * $limit - ($limit);

$filter = $filter_value = $search_type = $search_query = '';
$filters = array('power', 'register', 'followers', 'following', 'lastlogin', 'register', 'id'); 

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
}

// Action buttons
if ($_POST['Submit'] != '' && ! csrfguard_check_referer('_admin_members'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['Submit'] == 'Activate' && (is_admin() || is_moderator()))
{

	$user_ids = $_POST['user_ids'];
	if (pm_count($user_ids) > 0)
	{
		$sql = "UPDATE pm_users 
				SET power = '". U_ACTIVE ."' 
				WHERE id IN (". implode(',', $user_ids) .") 
				  AND power = '". U_INACTIVE ."'";
		$result = @mysql_query($sql);
		
		if ( ! $result)
		{
			$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
		}
		else
		{
			$info_msg = pm_alert_success('The selected user accounts were updated.');
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}
else if ($_POST['Submit'] == 'Delete' && (is_admin() || is_moderator()))
{
	$user_ids = $_POST['user_ids'];
	$total = pm_count($user_ids);
	
	// exclude self;
	if ($total > 0)
	{
		foreach ($user_ids as $k => $id)
		{
			if ($userdata['id'] == $id)
			{
				unset($user_ids[$k]);
				$total--;
				break;
			}
		}
	}
	
	if ($total > 0)
	{
		$sql_in_user_ids = implode(',', $user_ids);
		if (is_admin())
		{
			$sql = "DELETE FROM pm_users 
					WHERE id IN (". $sql_in_user_ids .") 
					  AND power != '". U_ADMIN ."'";
		}
		else // is moderator
		{
			$sql = "SELECT id, power FROM pm_users WHERE id IN (". $sql_in_user_ids .")";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_assoc($result))
			{
				if ( ! in_array($row['power'], array(U_ACTIVE, U_INACTIVE)))
				{
					if(($key = array_search($row['id'], $user_ids)) !== false) 
					{
						unset($user_ids[$key]);
					}
				}
			}
			mysql_free_result($result);
			
			if(($key = array_search($userdata['id'], $user_ids)) !== false) 
			{
				unset($user_ids[$key]);
			}
			
			$sql_in_user_ids = implode(',', $user_ids);
			
			$sql = "DELETE FROM pm_users 
					WHERE id IN (". $sql_in_user_ids .")";
		}
		
		$user_ids_count = pm_count($user_ids);
		
		// get accounts data @since v2.6
		if ($user_ids_count > 0)
		{
			$accounts_data = array();
			$sql_2 = "SELECT * FROM pm_users 
						WHERE id IN (". $sql_in_user_ids .")";
			if ($result_2 = mysql_query($sql_2))
			{
				while ($row = mysql_fetch_assoc($result_2))
				{
					$accounts_data[$row['id']] = $row;
				}
				
				mysql_free_result($result_2);
			}
		}
		
		$result = ($user_ids_count > 0) ? @mysql_query($sql) : true;

		if ( ! $result)
		{
			$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
		}
		else if ($user_ids_count > 0)
		{
			$affected_rows = mysql_affected_rows();

			@mysql_query('DELETE FROM pm_comments WHERE user_id IN ('. $sql_in_user_ids .')');
			@mysql_query('DELETE FROM pm_comments_reported WHERE user_id IN ('. $sql_in_user_ids .')');
			//@mysql_query('DELETE FROM pm_favorites WHERE user_id IN ('. $sql_in_user_ids .')'); // @deprecated since v2.2
			
			$sql = "SELECT list_id FROM pm_playlists WHERE user_id  IN (". $sql_in_user_ids .")";
			$result = @mysql_query($sql);

			$playlists = array();
			while ($row = @mysql_fetch_assoc($result))
			{
				$playlists[] = $row['list_id'];
			}
			@mysql_free_result($result);
			
			if (pm_count($playlists) > 0)
			{
				@mysql_query("DELETE FROM pm_playlist_items WHERE list_id IN (". implode(',', $playlists) .")"); 
				@mysql_query("DELETE FROM pm_playlists WHERE list_id IN (". implode(',', $playlists) .")"); 
			}
			
			if (_MOD_SOCIAL && $affected_rows > 0)
			{
				foreach ($user_ids as $k => $id)
				{
					remove_all_related_activity($id, ACT_OBJ_USER);
				}
				
				// handle followers and following too
				follow_delete_user($user_ids);
				
				// handle notifications
				foreach ($user_ids as $k => $uid)
				{
					notifications_delete_user($uid);
				}
			}
			
			if (pm_count($accounts_data) > 0)
			{
				foreach ($accounts_data as $id => $account)
				{
					if ($account['avatar'] != '' && $account['avatar'] != 'default.gif' && file_exists(_AVATARS_DIR_PATH . $account['avatar']))
					{
						@unlink(_AVATARS_DIR_PATH . $account['avatar']);
					}
					
					if ($account['channel_cover'] != '')
					{
						delete_channel_cover_files($account['channel_cover']);
					}
				}
			}
			
			if ($affected_rows == 0)
			{
				$info_msg = pm_alert_success('No accounts were removed.');
			}
			else if ($affected_rows > 1)
			{
				$info_msg = pm_alert_success($affected_rows .' user accounts deleted.');
			}
			else
			{
				$info_msg = pm_alert_success('1 user account deleted.');
			}
		}
		else
		{
			$info_msg = pm_alert_success('You can only delete accounts belonging to <em>Active</em> and <em>Inactive</em> groups.');
		}
	}
	else
	{	
		$info_msg = pm_alert_warning('Please select something first.');
	}
}
else if ($_POST['Submit'] == 'Delete' && !(is_admin() || is_moderator()))
{
	$info_msg = pm_alert_warning('Sorry, only Administrators are allowed to perform this action.');
}


// DELETE A USER
if ($action == 1 && ! csrfguard_check_referer('_admin_members'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if($action == 1) 
{ 
	$query = mysql_query("SELECT * FROM pm_users WHERE id = '".$userid."'");
	$account = mysql_fetch_assoc($query);
	
	if (is_moderator() && in_array($account['power'], array(U_ADMIN, U_MODERATOR, U_EDITOR)))
	{
		$info_msg = pm_alert_info('Sorry, you can\'t delete this account.');
	}
	else if ($account['power'] == U_ADMIN) 
	{
		$info_msg = pm_alert_info('Nice sense of humour, but the administrator\'s account cannot be removed.');
	} 
	else 
	{
		$result = @mysql_query("DELETE FROM pm_users WHERE id = '".$userid."'");
		if(!$result)
		{
			$info_msg = pm_alert_error('An error occurred: '. mysql_error());
		}
		else
		{
			@mysql_query("DELETE FROM pm_comments WHERE user_id = '". $userid ."'");
			@mysql_query("DELETE FROM pm_comments_reported WHERE user_id = '". $userid ."'");
			//@mysql_query("DELETE FROM pm_favorites WHERE user_id = '". $userid ."'"); // @deprecated since v2.2
			
			$sql = "SELECT list_id FROM pm_playlists WHERE user_id = $userid";
			$result = @mysql_query($sql);
			
			$playlists = array();
			while ($row = @mysql_fetch_assoc($result))
			{
				$playlists[] = $row['list_id'];
			}
			@mysql_free_result($result);
			
			if (pm_count($playlists) > 0)
			{
				@mysql_query("DELETE FROM pm_playlist_items WHERE list_id IN (". implode(',', $playlists) .")"); 
				@mysql_query("DELETE FROM pm_playlists WHERE list_id IN (". implode(',', $playlists) .")");
			}
			
			if ($account['avatar'] != '' && $account['avatar'] != 'default.gif' && file_exists(_AVATARS_DIR_PATH . $account['avatar']))
			{
				@unlink(_AVATARS_DIR_PATH . $account['avatar']);
			}
			
			if ($account['channel_cover'] != '')
			{
				delete_channel_cover_files($account['channel_cover']);
			}
			
			if (_MOD_SOCIAL)
			{
				remove_all_related_activity($userid, ACT_OBJ_USER);
				
				// handle followers and following too
				follow_delete_user($userid);
				
				// handle notifications too
				notifications_delete_user($userid);
			}
			
			$info_msg = pm_alert_success('Account <strong>'. htmlentities($account['username']) .'</strong> was deleted from your database.');
		}
	}
}

$members_nonce = csrfguard_raw('_admin_members');


if( $_POST['Submit'] == 'Activate' || $_POST['Submit'] == 'Delete' ) {
	$members = a_list_users('', '', $from, $limit, $page, $filter, $filter_value);
	$total_members = count_entries('pm_users', $filter, $filter_value);
}
//	Search
elseif(!empty($_POST['submit']) || !empty($_GET['submit']) || !empty($_POST['_pmnonce']) || !empty($_POST['_pmnonce_t']))
{
	$search_query = ($_POST['keywords'] != '') ? trim($_POST['keywords']) : trim($_GET['keywords']);
	$search_type = ($_POST['search_type'] != '') ? $_POST['search_type'] : $_GET['search_type'];
	$members = a_list_users($search_query, $search_type,  $from, $limit, $page);
	$total_members = $members['total'];
}
else 
{	
	if (in_array($filter, array('register', 'followers', 'following', 'lastlogin', 'id'))) // sorters
	{
		$total_members = count_entries('pm_users', '', '');
	}
	else
	{
		$total_members = count_entries('pm_users', $filter, $filter_value);
	}
	if($total_members - $from == 1)
		$page--;
		
	$members = a_list_users('', '', $from, $limit, $page, $filter, $filter_value);
	if($total_members - $from == 1)
		$page++;
}

// generate smart pagination
$filename = 'users.php';
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('?', $uri);
$uri[1] = str_replace(array("<", ">", '"', "'", '/'), '', $uri[1]);
parse_str($uri[1], $temp);
unset($temp['_pmnonce'], $temp['_pmnonce_t'], $temp['a'], $temp['a'], $temp['uid']);
$uri[1] = http_build_query($temp);

$pagination = '';
$pagination = a_generate_smart_pagination($page, $total_members, $limit, 1, $filename, $uri[1]);
?>
<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><a href="<?php echo _URL; ?>/memberlist.php" class="open-in-new" target="_blank" data-popup="tooltip" data-placement="right" data-original-title="Jump to Users in Front-End"><span class="font-weight-semibold"><?php echo $_page_title; ?></span> <i class="mi-open-in-new"></i></a> <a href="add-user.php" class="badge badge-success badge-addnew font-size-sm ml-2">+ add new</a></h4>
			</div>
			<a href="#" class="header-elements-toggle text-default d-md-none"><i class="mi-search"></i></a>
			<div class="header-elements with-search d-none">
				<div class="d-flex-inline align-self-center ml-auto">
				<form name="search" action="users.php" method="get" class="form-search-listing form-inline float-right">
					<div class="input-group input-group-sm input-group-search">
						<div class="input-group-append">
							<input name="keywords" type="text" value="<?php echo $search_query; ?>" class="search-query search-quez input-medium form-control form-control-sm border-right-0" placeholder="Enter keyword" id="form-search-input" />
						</div>
						<select name="search_type" class="form-control form-control-sm border-left-0 border-right-0">
							<option value="username" <?php echo ($search_type == "username") ? 'selected="selected"' : ''; ?> >Username</option>
							<option value="fullname" <?php echo ($search_type == "fullname") ? 'selected="selected"' : ''; ?> >Name</option>
							<option value="email" <?php echo ($search_type == "email") ? 'selected="selected"' : ''; ?> >Email</option>
							<option value="ip" <?php echo ($search_type == "ip") ? 'selected="selected"' : ''; ?> >IP Address</option>
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
					<span class="breadcrumb-item active"><?php echo $_page_title; ?></span>
				</div>


			</div>

			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
					<a href="#" id="show-help-assist" class="breadcrumb-elements-item"><i class="mi-help-outline text-muted"></i></a>
				</div>
			</div>
		</div>
		<div class="row p-0 mt-1 ml-0 mr-2">
			<div class="col-sm-12 col-md-10">
				<div class="d-horizontal-scroll">
					<ul class="nav nav-sm nav-pills nav-pills-bottom m-0">
						<li class="nav-item">
							<a class="nav-link <?php echo (!is_numeric($filter_value)) ? 'active' : ''; ?>" href="users.php">All <?php echo (!is_numeric($filter_value)) ? '<span class="text-muted">('. pm_number_format($total_members) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter_value == '1') ? 'active' : ''; ?>" href="users.php?filter=power&fv=1">Admin <?php echo ($filter_value == '1') ? '<span class="text-muted">('. pm_number_format($total_members) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter_value == '3') ? 'active' : '';?>" href="users.php?filter=power&fv=3">Moderators <?php echo ($filter_value == '3') ? '<span class="text-muted">('. pm_number_format($total_members) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter_value == '4') ? 'active' : '';?>" href="users.php?filter=power&fv=4">Editors <?php echo ($filter_value == '4') ? '<span class="text-muted">('. pm_number_format($total_members) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter_value == '0') ? 'active' : '';?>" href="users.php?filter=power&fv=0">Regular users <?php echo ($filter_value == '0') ?'<span class="text-muted">('. pm_number_format($total_members) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter_value == '2') ? 'active' : '';?>" href="users.php?filter=power&fv=2">Inactive <?php echo ($filter_value == '2') ? '<span class="text-muted">('. pm_number_format($total_members) .')</span>' : ''; ?></a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-md-2">
				<div class="float-right  d-none d-md-block d-lg-block">
					<?php if (is_admin()) : ?>
						<a href="export-users.php" class="btn btn-sm btn-link text-grey-400 font-size-sm font-weight-semibold pm-show-loader" data-popup="tooltip" data-original-title="Click to download">Export to CSV</a>
					<?php endif; ?>
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
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">Export to CSV</a>
					<a class="nav-link" id="v-pills-tab-help-three" data-toggle="pill" href="#v-pills-three" role="tab" aria-controls="v-pills-three" aria-selected="false" data-toggle="tab">Filtering</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>This page provides a quick overview of your users. Listings below contain each user's details such name, registration date, last login date, IP address and user rank/group.</p>
						<p>If the site requires you to approve each registered user, you will have to do so from this page. You can also approve registrations in bulk. To approve a user click the &quot;check&quot; icon from the &quot;Actions&quot; column.</p>
						<p>Note: Banned users will have a strikeout username.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
						<p>A sub-menu of this area, &quot;Export to CSV&quot; generates a CSV file compatible with Microsoft Outlook, GMAIL Contacts, Facebook Friends Import and so on.<br />You can then import this CSV to your favorite service and use the list to get in touch with your users.</p>
					</div>
					<div class="tab-pane" id="v-pills-three" role="tabpanel" aria-labelledby="v-pills-tab-help-three">
						<p>Listing pages such as this one contain a filtering area which comes in handy when dealing with a large number of entries. The filtering options is always represented by a gear icon positioned on the top right area of the listings table. Clicking this icon usually reveals a  search form and one or more drop-down filters.</p>
					</div>

				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content content-full-width">

	<?php if ( $total_members == 0) : ?> <!--Start ifempty-->

	<div class="align-middle text-center mt-3 pt-3">
		<i class="mi-people mi-3x text-muted mb-3"></i>
		<h6 class="font-weight-semibold text-grey mb-1">No users <?php echo (!empty($filter) || !empty($_GET['keywords']) || $_GET['vid']) ? 'matching these criteria found' : 'yet';?></h6>
		<p class="text-grey mb-3 pb-1"></p>
		<a href="add-user.php" class="btn btn-sm bg-blue border-blue border-2 rounded font-weight-semibold">Add new user</a>

	</div>

	<?php else : ?> <!--Else ifempty-->

	<?php echo $info_msg; ?>    

	<div class="card card-blanche">
		<div class="card-body">

		<div class="row">
			<div class="col-sm-12 col-md-6">
				<?php if ( ! empty($_GET['keywords'])) : ?>
				<div class="float-left">
					<h5 class="font-weight-semibold mt-2">SEARCH RESULTS FOR <mark><?php echo $_GET['keywords']; ?></mark> <a href="#" onClick="parent.location='users.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear search results"><i class="icon-cancel-circle2"></i></a></h5>
				</div>
				<?php endif; ?>
			</div>
			<div class="col-sm-12 col-md-6 d-none d-md-block">
				<div class="float-right mb-3">
					<form name="users_per_page" action="users.php" method="get" class="form-inline float-right">
						<input type="hidden" name="ui_pref" value="users_pp" />
						<label class="mr-1" for="inlineFormCustomSelectPref">Users/page</label>
						<select name="results" class="custom-select custom-select-sm w-auto" onChange="this.form.submit()">
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
		</div> <!--.row -->

	</div><!--.card-body-->



<form name="users_checkboxes" id="users_checkboxes" action="users.php?page=<?php echo $page;?>&filter=<?php echo $filter;?>&fv=<?php echo $filter_value;?>" method="post">
	<div class="datatable-scroll">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
	<thead>
	<tr>
		<th align="center" class="text-center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
		<th width="50"><a href="users.php?filter=id&fv=<?php echo ($filter_value == 'desc' && $filter == 'id') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'id') ? 'ascending' : 'descending';?>">ID</a></th>
		<th>Username</th>
		<th>Name</th>
		<th>Email</th>
		<th class="text-center"><a href="users.php?filter=register&fv=<?php echo ($filter_value == 'desc' && $filter == 'register') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'register') ? 'ascending' : 'descending';?>">Register date</a></th>
		<th class="text-center"><a href="users.php?filter=followers&fv=<?php echo ($filter_value == 'desc' && $filter == 'followers') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'followers') ? 'ascending' : 'descending';?>">Followers</a></th>
		<th class="text-center"><a href="users.php?filter=following&fv=<?php echo ($filter_value == 'desc' && $filter == 'following') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'following') ? 'ascending' : 'descending';?>">Following</a></th>
		<th class="text-center"><a href="users.php?filter=lastlogin&fv=<?php echo ($filter_value == 'desc' && $filter == 'lastlogin') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'lastlogin') ? 'ascending' : 'descending';?>">Last seen</a></th>
		<th class="text-center">Last logged IP</th>
		<th class="text-center">Group</th>
		<th style="width: 90px;" class="text-center">Action</th>
	</tr>
	</thead>
	<tbody>

	<?php echo $members['users']; ?>

	<?php if ($pagination != '') : ?>
	<tr class="tablePagination">
		<td colspan="12" class="tableFooter">
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
				<button type="submit" name="Submit" value="Activate" class="btn btn-sm btn-success">Activate</button>
			</div>
			<div class="btn-group">
				<button type="submit" name="Submit" value="Delete" class="btn btn-sm btn-danger" onClick="return confirm_delete_all();">Delete</button>
			</div>
		</div>
	</div>
	</div><!-- #list-controls -->
</div>
<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $members_nonce['_pmnonce'];?>" value="<?php echo $members_nonce['_pmnonce'];?>" />
<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $members_nonce['_pmnonce'];?>" value="<?php echo $members_nonce['_pmnonce_t'];?>" />
<input type="hidden" name="filter" id="listing-filter" value="<?php echo $filter;?>" />
<input type="hidden" name="fv" id="listing-filter_value" value="<?php echo $filter_value;?>" />
<input type="hidden" name="search_type" id="listing-filter_search_type" value="<?php echo $search_type;?>" />
<input type="hidden" name="keywords" id="listing-filter_keywords" value="<?php echo $search_query;?>" />
</form>


</div><!--.card-->
<?php endif ?> <!--End ifempty-->
</div>
<!-- /content area -->
<?php
include('footer.php');