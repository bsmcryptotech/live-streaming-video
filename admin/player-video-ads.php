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

$showm = '9';
$_page_title = 'Pre-roll video ads manager';
include('header.php');

$action = $_GET['act'];
if( $action == '' || empty($action))
	$action = '';

$total_ads = count_entries('pm_videoads', '', '');

function manage_ad_form($action = 'addnew', $item = false)//$id = 0, $name = '', $flv_url = '', $redirect_url = '', $redirect_type = '', $active = 0)
{
	if (empty($item['id']))
	{
		$item['id'] = 0;
	}

	$target = '';
	switch($action)
	{
		case 'addnew':
			$target = 'player-video-ads.php?act=addnew';
		break;
		case 'edit':
			$target = ($id != 0) ? 'player-video-ads.php?act=edit&id='.$id : 'player-video-ads.php?act=edit';
		break;
	}
	// generate form
	?>
	<div class="card">
		<div class="card-body">
		<form name="videoad" method="post" action="player-video-ads.php?act=addnew">

			<div class="form-group">
				<label>Name</label>
				<input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" size="40" class="form-control" />
			</div>

			<div class="form-group">
				<label>Video Ad URL <a href="#" rel="tooltip" title="Supported file types: *.flv, *.mp4" class="text-muted"><i class="mi-info-outline"></i></a></label>
				<input type="text" name="flv_url" value="<?php echo $item['flv_url']; ?>" size="120" placeholder="http://" class="form-control" />
			</div>
			<div class="form-group">
				<label>Advertised URL</label>
				<input type="text" name="redirect_url" value="<?php echo $item['redirect_url'] ?>" size="120" placeholder="http://" class="form-control" />
			</div>
			<div class="form-group">
				<label>Enable Statistics</label>
				<br />
				<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="0" <?php echo ($item['status'] == 0) ? 'checked' : '';?>> Yes</label>
				<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="1" <?php echo ($item['status'] == 1) ? 'checked' : '';?>> No</label>
				<input type="hidden" name="redirect_type" value="0" />
				<input type="hidden" name="active" value="1" />
			</div>

			<button type="submit" name="Submit" value="Submit" class="btn btn-success" />Submit</button>
		</form>


	<?php
	return;
}
?>

<!-- Main content -->
<div class="content-wrapper">

<div class="page-header-wrapper">
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title; ?></span> <a href="#addNew" onclick="location.href='#addNew';" data-toggle="modal" class="badge badge-success badge-addnew font-size-sm ml-2">+ add new</a></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<h5 class="font-weight-semibold mb-0 text-center"><?php echo pm_number_format($total_ads); ?></h5>
					<span class="text-muted font-size-sm">ad<?php echo ($total_ads > 1) ? 's' : '';?></span>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
			<div class="d-horizontal-scroll">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="banner-ads.php" class="breadcrumb-item">Advertisments</a>
					<a href="player-video-ads.php" class="breadcrumb-item active"><?php echo $_page_title; ?></a>
				</div>
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
						<p>Video ads are clickable pre-roll ads that appear at specified intervals. To enable video ads, you need to provide a *.FLV or *.MP4 video (i.e. the video ad) and a URL which (i.e. the sponsored/promoted website).</p>
						<p>If you'd like to modify how often users see your pre-roll video ads, visit the <strong><a href="settings.php?view=t5">Settings > Video Ads Settings</a></strong> page.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->


	<!-- Content area -->
	<div class="content content-full-width">

<?php

switch($action)
{
	case 'addnew':
		if($_GET['act'] == 'addnew')
		{
			if(isset($_POST['Submit']))
			{

				$arr_fields = array('name' => "Name", 'flv_url' => "Video Ad location", 'redirect_url' => 'Advertised URL', 'status' => 'Status');
				$errors = array();
				foreach($_POST as $k => $v)
				{
					if(((is_array($v) && empty($v)) || (is_string($v) && trim($v) == '')) && array_key_exists($k, $arr_fields) === TRUE)
					{
						$errors[] = "The '".$arr_fields[$k]."' field shouldn't be empty.";
					}
				}
				if (pm_count($errors) > 0)
				{
					$info_msg = pm_alert_error($errors);
					echo manage_ad_form('addnew', $_POST);//0, $_POST['name'], $_POST['flv_url'], $_POST['redirect_url'], $_POST['redirect_type'], $_POST['status']);
				}
				else
				{
					$status = ($_POST['status'] == 1) ? 1 : 0;
					$redirect_url = trim($_POST['redirect_url']);
					$name = trim($_POST['name']);
					$flv_url = trim($_POST['flv_url']);
					$redirect_type = trim($_POST['redirect_type']);
					$hash = md5( rand(0,123) . time() );
					$disable_stats = (int) $_POST['disable_stats'];

					if (strpos($redirect_url, 'http') !== 0)
						$redirect_url = make_url_https("http://".$redirect_url);
					if (strpos($flv_url, 'http') !== 0)
						$flv_url = make_url_https("http://".$flv_url);

					$sql = "INSERT INTO pm_videoads SET hash = '".$hash."',
														name = '".secure_sql($name)."',
														flv_url = '".secure_sql($flv_url)."',
														redirect_url = '".secure_sql($redirect_url)."',
														redirect_type = '".secure_sql($redirect_type)."',
														status = '".$status."',
														disable_stats = '". $disable_stats ."'";

					$query = mysql_query($sql);
					if ( ! $query)
					{
						$info_msg = pm_alert_error('There was a problem while inserting new ad in database.<br />MySQL returned: '. mysql_error());
					}
					else
					{
						$new_ad_id = mysql_insert_id();

						$msg = 'Your pre-roll video ad was successfully created.';
						if($status == 0)
							$msg .= "<br /><strong>Note</strong>: New ads are not enabled by default. Remember to enable ads after creating them.";
						$msg = pm_alert_success($msg);
						$msg .= '<a href="player-video-ads.php" class="btn btn-success ml-3">&larr; Return</a>';
						echo $msg;
					}
				}
			}
			else
			{
				echo manage_ad_form('addnew');
			}
		}
	break;

	case 'edit':
		$id = $_GET['id'];
		if($id <= 0 || !is_numeric($id) || $id == '')
		{
			$info_msg = pm_alert_error('Invalid or missing ID.');
		}
		else
		{
			if(isset($_POST['Submit']))
			{
				$arr_fields = array('name' => "Name", 'flv_url' => "Video Ad location", 'redirect_url' => 'Advertised URL', 'status' => 'Status');
				$errors = array();
				foreach($_POST as $k => $v)
				{
					if(((is_array($v) && empty($v)) || (is_string($v) && trim($v) == '')) && array_key_exists($k, $arr_fields) === TRUE)
					{
						$errors[] = "The '".$arr_fields[$k]."' field shouldn't be empty.";
					}
				}
				if (pm_count($errors) > 0)
				{
					$_POST['id'] = $id;

					echo pm_alert_errors($errors);
					echo manage_ad_form('addnew', $_POST);//$id, $_POST['name'], $_POST['flv_url'], $_POST['redirect_url'], $_POST['redirect_type'], $_POST['status']);

					include('footer.php');

					exit();
				}
				$status = ($_POST['status'] == 1) ? 1 : 0;
				$redirect_url = trim($_POST['redirect_url']);
				$name = trim($_POST['name']);
				$flv_url = trim($_POST['flv_url']);
				$redirect_type = trim($_POST['redirect_type']);
				$disable_stats = (int) $_POST['disable_stats'];

				if (strpos($redirect_url, 'http') !== 0)
					$redirect_url = make_url_https("http://".$redirect_url);
				if (strpos($flv_url, 'http') !== 0)
					$flv_url = make_url_https("http://".$flv_url);

				$sql = "UPDATE pm_videoads SET name = '".secure_sql($name)."',
												flv_url = '".secure_sql($flv_url)."',
												redirect_url = '".secure_sql($redirect_url)."',
												redirect_type = '".secure_sql($redirect_type)."',
												status = '".$status."',
												disable_stats = '". $disable_stats ."'
										 WHERE id = '".$id."' ";
				$query = mysql_query($sql);
				if ( ! $query)
				{
					$info_msg = pm_alert_error('There was an error while updating your ad.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					$info_msg = pm_alert_success('<strong>'. htmlspecialchars($name) .'</strong> updated.');
					$info_msg .= '<a href="player-video-ads.php" class="btn btn-success ml-3">&larr; Return</a>';
					echo $info_msg;
				}
			}
			else
			{
				$query = mysql_query("SELECT * FROM pm_videoads WHERE id='".$id."'");
				if ( ! $query)
				{
					echo pm_alert_error('There was an error while retrieving your data.<br />MySQL returned: '. mysql_error());

					include('footer.php');
					exit();
				}

				$ad = mysql_fetch_assoc($query);
				if ($ad['id'] == '')
				{
					$info_msg = pm_alert_error('The selected as was not found in your database.');
				}
				else
				{
					echo manage_ad_form('edit', $ad['id'], $ad['name'], $ad['flv_url'], $ad['redirect_url'], $ad['redirect_type'], $ad['status']);
				}
			}
		}

	break;
	case 'delete':
	case 'activate':
	case 'deactivate':
	case 'reset':
	default:

		if($action == 'delete')
		{
			$id = $_GET['id'];
			if($id <= 0 || !is_numeric($id) || $id == '')
			{
				$info_msg = pm_alert_error('Invalid or missing ID.');
			}
			else
			{
				$query = mysql_query("DELETE FROM pm_videoads WHERE id = '".$id."'");
				if( !$query )
				{
					$info_msg = pm_alert_error('There was a problem while deleting this ad.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					$sql = "DELETE FROM pm_ads_log
							WHERE ad_id = $id
							  AND ad_type = ". _AD_TYPE_VIDEO;
					@mysql_query($sql);
					$info_msg = pm_alert_success('The ad was deleted.');
				}
			}
		}
		if($action == 'activate' || $action == 'deactivate')
		{
			$id = $_GET['id'];
			if($id <= 0 || !is_numeric($id) || $id == '')
			{
				$info_msg = pm_alert_error('Invalid or missing ID.');
			}
			else
			{
				$sql = '';
				if($action == "activate")
					$sql = "UPDATE pm_videoads SET status='1' WHERE id = '".$id."' LIMIT 1";
				else
					$sql = "UPDATE pm_videoads SET status='0' WHERE id = '".$id."' LIMIT 1";
				$query = mysql_query($sql);
				if ( ! $query )
				{
					$info_msg = pm_alert_error('A problem was encountered while activating/deactivating this ad.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					$info_msg = ($action == "activate") ? pm_alert_success('The ad is now active.') : pm_alert_success('The ad was deactivated.');
				}
			}
		}
	?>

<?php
if ($config['video_player'] == 'jwplayer')
{
	echo pm_alert_error('Sorry, this feature is only compatible with <strong>Video JS</strong> only.');
}
?>

<?php echo $info_msg; ?>

<?php
include('modals/modal-create-video-ad.php')
?>

<div class="card card-blanche">
	<div class="card-body">
	</div><!--.card-body-->


<div class="datatable-scroll">
<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
 <thead>
  <tr>
	<th>Name</th>
	<th width="8%" class="text-center">Impressions</th>
	<th width="7%" class="text-center">Clicks</th>
	<th width="7%" class="text-center">CTR</th>
	<th width="10%" class="text-center">Status</th>
	<th width="23%" style="width: 160px;" class="text-center">Action</th>
  </tr>
 </thead>
 <tbody>

<?php

	// display all ads
	$query = mysql_query("SELECT * FROM pm_videoads ORDER BY id ASC");
	$display = '';
	$i = 0;
	while($row = mysql_fetch_assoc($query))
	{
		$clean_title = str_replace(array('"', "'"), array('', "\'"), $row['name']);
		$col = ($i++ % 2) ? 'table_row1' : 'table_row2';

		$sql_tmp = "SELECT SUM(impressions) as total_impressions, SUM(clicks) as total_clicks
					FROM pm_ads_log
					WHERE ad_id = ". $row['id'] ."
					  AND ad_type = ". _AD_TYPE_VIDEO;

		$result_tmp = mysql_query($sql_tmp);
		$totals = mysql_fetch_assoc($result_tmp);
		mysql_free_result($result_tmp);

		if ($totals['total_impressions'] == '')
		{
			$totals['total_impressions'] = 0;
			$totals['total_clicks'] = 0;
			$ctr = 0;
		}
		else
		{
			$ctr = round( ((int) $totals['total_clicks'] * 100 / (int) $totals['total_impressions']), 2);
		}

		?>
		<tr class="<?php echo $col; ?>">
			<td><a href="#" class="adzone_update_<?php echo  $row['id'] ; ?> font-weight-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></a></td>
			<td align="center" class="text-center"><?php echo pm_number_format($totals['total_impressions']); ?></td>
			<td align="center" class="text-center"><?php echo pm_number_format($totals['total_clicks']); ?></td>
			<td align="center" class="text-center"><strong><?php echo $ctr; ?>%</strong></td>
			<td align="center" class="text-center table-col-action">
				<?php if ($row['status'] == 1) : ?>
					<a href="player-video-ads.php?act=deactivate&id=<?php echo $row['id']; ?>" class="badge badge-success label-clickable" rel="tooltip" title="Click to deactivate">Active</a>
				<?php else : ?>
					<a href="player-video-ads.php?act=activate&id=<?php echo $row['id']; ?>" class="badge badge-secondary" rel="tooltip" title="Click to activate">Inactive</a></span>
				<?php endif; ?>
			</td>
			<td align="center" class="text-center table-col-action">
<!--
				<?php if ($row['status'] == 0) : ?>
				 <a href="player-video-ads.php?act=activate&id=<?php echo $row['id']; ?>" class="list-icons-item text-success mr-1" rel="tooltip" title="Activate Ad"><i class="icon-eye"></i></a>
				<?php else : ?>
				 <a href="player-video-ads.php?act=deactivate&id=<?php echo $row['id']; ?>" class="list-icons-item text-warning mr-1" rel="tooltip" title="Deactivate Ad"><i class="icon-eye-blocked"></i></a>
				<?php endif; ?>
-->
				<a href="#" class="adzone_update_<?php echo  $row['id'] ; ?> list-icons-item mr-1" rel="tooltip" title="Edit"><i class="icon-pencil7"></i></a>
				<a href="#" onClick="delete_ad('<?php echo  $clean_title ; ?>', 'player-video-ads.php?act=delete&id=<?php echo $row['id']; ?>')" class="list-icons-item text-danger" rel="tooltip" title="Delete"><i class="icon-bin"></i></a>
			</td>
		</tr>

		<tr>
			<td colspan="6" class="m-0 p-0 td_expanded_row">
				<div id="adzone_update_<?php echo  $row['id'] ; ?>" name="<?php echo  $row['id'] ; ?>">
					<div class="adzone_update_form" style="padding: 10px; margin: 10px;">
					<form name="adzone_update_<?php echo  $row['id'] ; ?>" method="post" action="player-video-ads.php?act=edit&id=<?php echo $row['id']; ?>" class="form">
					<div class="form-group">
						<label>Name</label>
						<input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" size="40" class="form-control" />
					</div>
					<div class="form-group">
						<label>Video Ad URL <a href="#" rel="tooltip" title="Supported file types: *.flv, *.mp4"><i class="mi-info-outline"></i></a></label>
						<input type="text" name="flv_url" value="<?php echo $row['flv_url']; ?>" size="40" class="form-control" />
					</div>
					<div class="form-group">
						<label>Advertised URL</label>
						<input type="text" name="redirect_url" value="<?php echo $row['redirect_url']; ?>" size="40" class="form-control" />
					</div>
					<div class="form-group">
						<label>Redirect Type</label>
						<br />
						<label><input type="radio" name="redirect_type" value="0" <?php echo ($row['redirect_type'] == 0) ? 'checked' : '';?> /> <small>Open <em>Advertised URL</em> in new window</small></label>
						<label><input type="radio" name="redirect_type" value="1" <?php echo ($row['redirect_type'] == 1) ? 'checked' : '';?> /> <small>Open <em>Advertised URL</em> in the same window</small></label>
					</div>
					<div class="form-group">
						<label>Enable Statistics</label>
						<br />
						<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="0" <?php echo ($row['disable_stats'] == 0) ? 'checked="checked"' : '';?>> Yes</label>
						<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="1" <?php echo ($row['disable_stats'] == 1) ? 'checked="checked"' : '';?>> No</label>
					</div>
					<div class="form-group">
						<input type="submit" name="Submit" value="Submit" class="btn btn-sm btn-success border-radius0" />
						<a href="#" id="adzone_update_<?php echo  $row['id'] ; ?>" class="btn-sm">Cancel</a>
						<input type="hidden" name="status" value="<?php echo $row['status']; ?>" />
					</div>
					</form>
					</div>
				</div>
			</td>
		</tr>
		<?php
	}

	if($i == 0) {
		echo '<tr><td colspan="6">No video ads have been defined.</td></tr>';
	}
	mysql_free_result($query);

	$total_active_ads = count_entries('pm_videoads', 'status', '1');
	update_config('total_videoads', $total_active_ads);

	break;
}

?>
</tbody>
</table>
</div>

</div><!--.card-->
</div>
<!-- /content area -->
<?php
include('footer.php');
