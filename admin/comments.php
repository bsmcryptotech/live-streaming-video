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

$filter = '';
$filters = array('articles', 'videos', 'flagged', 'pending');
$_page_title = '';

if(isset($_GET['filter']) && in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter = rtrim($filter, 's');
	$_page_title .= ucfirst($filter).' ';
	$_page_title .= 'comments';
}
else
{
	$_page_title = 'Comments';
}

$showm = '5';
$load_scrolltofixed = 1;


include('header.php');

$vid 		= trim($_GET['vid']);
$action 	= $_GET['a'];
$comment_id = (int) trim($_GET['cid']);
$page 		= $_GET['page'];

$filter = '';
$filters = array('articles', 'videos', 'flagged', 'pending');

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
}

$page = ( ! $page) ? 1 : $page;
$limit = get_admin_ui_prefs('comments_pp');
$from 		= $page * $limit - ($limit);


//	Batch Delete/Approve Comments/Remove flag
if (($_POST['Submit'] == 'Delete' || $_POST['Submit'] == 'Approve' || $_POST['Submit'] == 'Remove flag') &&  ! csrfguard_check_referer('_admin_comments'))
{	
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if($_POST['Submit'] == 'Delete' || $_POST['Submit'] == 'Approve' || $_POST['Submit'] == 'Remove flag')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	
	$total_ids = pm_count($video_ids);
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= $video_ids[ $i ] . ", ";
		}
		$in_arr = substr($in_arr, 0, -2);
		if(strlen($in_arr) > 0)
		{
			if($_POST['Submit'] == 'Approve')
			{
				$sql = "UPDATE pm_comments SET approved = '1' WHERE id IN (" . $in_arr . ")";
				$result = @mysql_query($sql);
	
				if(!$result)
					$info_msg = pm_alert_error('An error occured while updating your database.<br />MySQL returned: '. mysql_error());
				else
					$info_msg = pm_alert_success('The selected comments have been approved.');
				
				if (_MOD_SOCIAL)
				{
					$sql = "SELECT id, uniq_id, user_id 
							FROM pm_comments WHERE id IN (" . $in_arr . ")";
					$result = mysql_query($sql);
					while ($row = mysql_fetch_assoc($result))
					{
						if (strpos($row['uniq_id'], 'article-') !== false)
						{
							$tmp_parts = explode('-', $row['uniq_id']);
							$id = array_pop($tmp_parts);
							$article = get_article($id);
							log_activity(array(
									'user_id' => $row['user_id'],
									'activity_type' => ACT_TYPE_COMMENT,
									'object_id' => $row['id'],
									'object_type' => ACT_OBJ_COMMENT,
									'object_data' => array(),
									'target_id' => $id,
									'target_type' => ACT_OBJ_ARTICLE,
									'target_data' => $article
									)
								);
						}
						else
						{
							$video = request_video($row['uniq_id']);
							log_activity(array(
									'user_id' => $row['user_id'],
									'activity_type' => ACT_TYPE_COMMENT,
									'object_id' => $row['id'],
									'object_type' => ACT_OBJ_COMMENT,
									'object_data' => array(),
									'target_id' => $video['id'],
									'target_type' => ACT_OBJ_VIDEO,
									'target_data' => $video
									)
								);
						}
					}
				}
			}
			else if ($_POST['Submit'] == 'Remove flag')
			{
				$sql = "UPDATE pm_comments SET report_count = '0' WHERE id IN (" . $in_arr . ")";
				$result = @mysql_query($sql);
				
				if ( ! $result)
				{
					$info_msg = pm_alert_error('An error occured while updating your database.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					@mysql_query("DELETE FROM pm_comments_reported WHERE comment_id IN (" . $in_arr . ")");
					$info_msg = pm_alert_success('The selected flags have been removed.');
				}
			}
			else
			{
				if (_MOD_SOCIAL)
				{
					$sql = "SELECT id, uniq_id, user_id 
							FROM pm_comments WHERE id IN (" . $in_arr . ")";
					if ($result = mysql_query($sql))
					{
						while (	$row = mysql_fetch_assoc($result))
						{
							$sql = "DELETE FROM pm_activity 
									WHERE user_id = '". $row['user_id'] ."' 
									  AND activity_type = '". ACT_TYPE_COMMENT ."'
									  AND object_id = '". $row['id'] ."' 
									  AND object_type = '". ACT_OBJ_COMMENT ."'";
							@mysql_query($sql);
						}
						mysql_free_result($result);
					}
				}
				
				$sql = "DELETE FROM pm_comments WHERE id IN (" . $in_arr . ")";
				$result = @mysql_query($sql);
				
				if(!$result)
				{
					$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					// remove reports
					$sql = "DELETE FROM pm_comments_reported WHERE comment_id IN (" . $in_arr . ")";
					$result = @mysql_query($sql);
					
					$in_arr = '';
					for($i = 0; $i < $total_ids; $i++)
					{
						if ($video_ids[ $i ] > 0)
						{
							$in_arr .= "'com-". $video_ids[ $i ] . "', ";
						}
					}
					$in_arr = substr($in_arr, 0, -2);
					
					// remove likes/dislikes
					$sql = "DELETE FROM pm_bin_rating_votes WHERE uniq_id IN (". $in_arr .")";
					$result = @mysql_query($sql);
					
					$info_msg = pm_alert_success('The selected comments have been deleted.');
				}
			}
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}

switch($action)
{
	case 1:
		if (csrfguard_check_referer('_admin_comments'))
		{
			if (_MOD_SOCIAL)
			{
				$sql = "SELECT id, uniq_id, user_id 
						FROM pm_comments WHERE id = '" . $comment_id . "'";
				if ($result = mysql_query($sql))
				{
					$row = mysql_fetch_assoc($result);
					$sql = "DELETE FROM pm_activity 
							WHERE user_id = '". $row['user_id'] ."' 
							  AND activity_type = '". ACT_TYPE_COMMENT ."'
							  AND object_id = '". $row['id'] ."' 
							  AND object_type = '". ACT_OBJ_COMMENT ."'";
					@mysql_query($sql);
					mysql_free_result($result);
				}
			}
			@mysql_query("DELETE FROM pm_comments WHERE id = '".$comment_id."'");
			@mysql_query("DELETE FROM pm_comments_reported WHERE comment_id = '".$comment_id."'");
			@mysql_query("DELETE FROM pm_bin_rating_votes WHERE uniq_id = 'com-".$comment_id."'");
			$info_msg = pm_alert_success('Comment(s) deleted.');
		}
		else
		{
			$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
		}
	break;
	case 2:
		if (csrfguard_check_referer('_admin_comments'))
		{
			@mysql_query("UPDATE pm_comments SET approved='1' WHERE id = '".$comment_id."'");
			
			if (_MOD_SOCIAL)
			{
				$sql = "SELECT id, uniq_id, user_id 
						FROM pm_comments WHERE id = '" . $comment_id . "'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				if (strpos($row['uniq_id'], 'article-') !== false)
				{
					$tmp_parts = explode('-', $row['uniq_id']);
					$id = array_pop($tmp_parts);
					$article = get_article($id);
					log_activity(array(
							'user_id' => $row['user_id'],
							'activity_type' => ACT_TYPE_COMMENT,
							'object_id' => $row['id'],
							'object_type' => ACT_OBJ_COMMENT,
							'object_data' => array(),
							'target_id' => $id,
							'target_type' => ACT_OBJ_ARTICLE,
							'target_data' => $article
							)
						);
				}
				else
				{
					$video = request_video($row['uniq_id']);
					log_activity(array(
							'user_id' => $row['user_id'],
							'activity_type' => ACT_TYPE_COMMENT,
							'object_id' => $row['id'],
							'object_type' => ACT_OBJ_COMMENT,
							'object_data' => array(),
							'target_id' => $video['id'],
							'target_type' => ACT_OBJ_VIDEO,
							'target_data' => $video
							)
						);
				}
			}
			$info_msg = pm_alert_success('Comment(s) approved.');
		}
		else
		{
			$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
		}
	break;
}

$comments_nonce = csrfguard_raw('_admin_comments');

//	Search
if(!empty($_GET['submit']) || !empty($vid))
{
	if(!empty($vid))
	{
		$comments = a_list_comments($vid, 'uniq_id', $from, $limit, $page);
	}
	else
	{
		$search_query = ($_POST['keywords'] != '') ? trim($_POST['keywords']) : trim($_GET['keywords']);
		$search_type = ($_POST['search_type'] != '') ? $_POST['search_type'] : $_GET['search_type'];
		$search_query = urldecode($search_query);
		$comments = a_list_comments($search_query, $search_type, $from, $limit, $page);
	}
	$total_comments = $comments['total'];
}
else 
{
	$total_comments = count_entries('pm_comments', '', '');
	
	if($total_comments - $from == 1)
		$page--;
		
	$comments = a_list_comments('', '', $from, $limit, $page, $filter);

	if($total_comments - $from == 1)
		$page++;
	
	$total_comments = $comments['total'];
}

// generate smart pagination
$filename = 'comments.php';
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('?', $uri);
$uri[1] = str_replace(array("<", ">", '"', "'", '/'), '', $uri[1]);

$pagination = '';
$pagination = a_generate_smart_pagination($page, $total_comments, $limit, 1, $filename, $uri[1]);


?>
<!-- Main content -->
<div class="content-wrapper">

<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><?php echo $_page_title; ?></h4>
			</div>
			<a href="#" class="header-elements-toggle text-default d-md-none"><i class="mi-search"></i></a>
			<div class="header-elements with-search d-none">
				<div class="d-flex-inline align-self-center ml-auto">
					<form name="search" action="comments.php" method="get" class="form-search-listing form-inline float-right">
						<div class="input-group input-group-sm input-group-search">
							<div class="input-group-append">
								<input name="keywords" type="text" value="<?php echo $_GET['keywords']; ?>" class="search-query search-quez input-medium form-control form-control-sm border-right-0" placeholder="Enter keyword" id="form-search-input" />
							</div>
							<select name="search_type" class="form-control form-control-sm border-left-0 border-right-0">
								<option value="comment" <?php echo ($_GET['search_type'] == "comment") ? 'selected="selected"' : ''; ?> >Comment</option>
								<option value="uniq_id" <?php echo ($_GET['search_type'] == "uniq_id") ? 'selected="selected"' : ''; ?> >Video Unique ID</option>
								<option value="username" <?php echo ($_GET['search_type'] == "username") ? 'selected="selected"' : ''; ?> >Username</option>
								<option value="ip" <?php echo ($_GET['search_type'] == "ip") ? 'selected="selected"' : ''; ?> >IP Address</option>
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
			<div class="d-horizontal-scroll">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="comments.php" class="breadcrumb-item">Comments</a>
					<span class="breadcrumb-item active"></span>

					<a href="#" class="breadcrumb-elements-item dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo $_page_title; ?></a>
					<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(282px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
						<a href="comments.php" class="dropdown-item"> All comments</a>
						<a href="comments.php?filter=videos" class="dropdown-item"> Video comments</a>
						<a href="comments.php?filter=articles" class="dropdown-item"> Article comments</a>
						<a href="comments.php?filter=flagged" class="dropdown-item"> Flagged</a>
						<a href="comments.php?filter=pending" class="dropdown-item"> Pending approval</a>
					</div>
				</div>
			</div>
			</div>


			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
					<a href="#" id="show-help-assist" class="breadcrumb-elements-item"><i class="mi-help-outline text-muted"></i></a>
				</div>
			</div>
		</div>
		<div class="row p-0 mt-1 ml-0 mr-2">
			<div class="col-md-12">
				<div class="d-horizontal-scroll">
					<ul class="nav nav-pills nav-pills-bottom m-0">
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == '') ? 'active' : ''; ?>" href="comments.php">All <?php echo ($filter == '') ? '<span class="text-muted">('. pm_number_format($total_comments) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'videos') ? 'active' : ''; ?>" href="comments.php?filter=videos">Video comments <?php echo ($filter == 'videos') ? '<span class="text-muted">('. pm_number_format($total_comments) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'articles') ? 'active' : ''; ?>" href="comments.php?filter=articles">Article comments <?php echo ($filter == 'articles') ? '<span class="text-muted">('. pm_number_format($total_comments) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'flagged') ? 'active' : ''; ?>" href="comments.php?filter=flagged">Flagged <?php echo ($filter == 'flagged') ? '<span class="text-muted">('. pm_number_format($total_comments) .')</span>' : ''; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo ($filter == 'pending') ? 'active' : ''; ?>" href="comments.php?filter=pending">Pending <?php echo ($filter == 'pending') ? '<span class="text-muted">('. pm_number_format($total_comments) .')</span>' : ''; ?></a>
						</li>
					</ul>
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
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>Comments posted on your site, are organized into &quot;video comments&quot; and &quot;article comments&quot;. An icon will represent the comment type. Selecting the &quot;COMMENTS&quot; item from the menu will list all existing comments with the latest ones showing first.</p>
						<p>If the site has comment moderation enabled, pending comments will also appear in the list. To approve a comment click the &quot;check&quot; icon from the &quot;Actions&quot; column.</p>
						<p>Hovering any existing message, both published and pending approval allows for easy in-line editing. This is helpful when it comes to removing unsolicited advertising, sensitive data and so on.</p>
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

	<?php if ( $total_comments == 0) : ?> <!--Start ifempty-->

	<div class="align-middle text-center mt-3 pt-3">
		<i class="icon-comments icon-3x text-muted mb-3"></i>
		<h6 class="font-weight-semibold text-grey mb-1">No comments <?php echo (!empty($filter) || !empty($_GET['keywords']) || $_GET['vid']) ? 'matching these criteria found' : 'yet';?></h6>
		<p class="text-grey mb-3 pb-1">Comments posted by users will appear here.</p>
	</div>

	<?php else : ?> <!--Else ifempty-->

	<?php echo $info_msg; ?>

	<div class="card card-blanche">
		<div class="card-body">

		<div class="row">
			<div class="col-sm-12 col-md-6">

			<?php if ( ! empty($_GET['keywords'])) : ?>
			<h5 class="font-weight-semibold mt-2">SEARCH RESULTS FOR <mark><?php echo $_GET['keywords']; ?></mark> <a href="#" onClick="parent.location='articles.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear search results"><i class="icon-cancel-circle2"></i></a></h5>
			<?php endif; ?>

			</div>
			<div class="col-sm-12 col-md-6 d-none d-md-block">
				<div class="float-right mb-3">
					<form name="comments_per_page" action="comments.php" method="get" class="form-inline float-right">
						<input type="hidden" name="ui_pref" value="comments_pp" />
						<label class="mr-1" for="inlineFormCustomSelectPref">Comments/page</label>
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
		</div> <!--.row -->

	<?php 
	/*
	 * */
	$form_action = 'comments.php?page='. $page;
	
	$form_action .= ($filter != '') ? '&filter='. $filter : '';
	$form_action .= ($_GET['vid'] != '') ? '&vid='. $_GET['vid'] : '';
	$form_action .= ($_GET['keywords'] != '') ? '&keywords='. $_GET['keywords'] .'&search_type='. $_GET['search_type'] .'&submit=Search' : '';
	?>

	</div><!--.card-body-->

	<form name="comments_checkboxes" action="<?php echo $form_action;?>" method="post">
	<div class="datatable-scroll">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
	<thead>
		<tr>
			<th align="center" width="20"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
			<th align="center" class="text-center" width="20"> </th>
			<th width="32%">Comment for</th>
			<th>Comment</th>
			<th width="150" class="text-center">Added</th>
			<th width="120" class="text-center">Posted by</th>
			<th width="200" class="text-center">IP</th>
			<th width="" style="width: 120px" class="text-center">Action</th>
		</tr>
	</thead>
		<tbody>
		<?php if ($pagination != '') : ?>
		<tr class="tablePagination">
			<td colspan="8" class="tableFooter">
				<div class="table table-md table-striped table-columned pm-tables tablesorter"><?php echo $pagination; ?></div>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php echo $comments['comments']; ?>
		
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
					<button type="submit" name="Submit" value="Remove flag" class="btn btn-sm btn-outline bg-primary-400 text-primary-400 border-primary-400">Remove flag</button>
				</div>
				<div class="btn-group">
					<button type="submit" name="Submit" value="Approve" class="btn btn-sm btn-success">Approve</button>
				</div>
				<div class="btn-group">
					<button type="submit" name="Submit" value="Delete" class="btn btn-sm btn-danger">Delete</button>
				</div>
			</div>
		</div>
		</div><!-- #list-controls -->
	</div>

	<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $comments_nonce['_pmnonce'];?>" value="<?php echo $comments_nonce['_pmnonce'];?>" />
	<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $comments_nonce['_pmnonce'];?>" value="<?php echo $comments_nonce['_pmnonce_t'];?>" />
	</form>

</div><!--.card-->
<?php endif ?> <!--End ifempty-->
</div>
<!-- /content area -->
<?php
include('footer.php');