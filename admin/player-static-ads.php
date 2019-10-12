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
$load_chzn_drop = 1;
$_page_title = 'Pre-roll static ads manager';
include('header.php');

$action = $_GET['act'];

$total_ads = count_entries('pm_preroll_ads', '', '');
 
$sources = a_fetch_video_sources('source_name');
load_categories();

?>

<?php
include('modals/modal-create-ad-static.php')
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
					<span class="text-muted font-size-sm">ad<?php echo ($total_ads) > 1 ? 's' : '';?></span>
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
					<span class="breadcrumb-item active"><?php echo $_page_title; ?></span>
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
						<p>Pre-roll static ads are ads which you can define to appear before the video player is loaded. These ads should work with any kind of video.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content content-full-width">

<?php
if ($action != '' && $action != 'addnew')
{
	$id = (int) $_GET['id'];
			
	if ( ! $id)
	{
		$action = '';
	}
}

switch($action)
{
	case 'addnew':
		if ($_GET['act'] == 'addnew')
		{
			if (isset($_POST['Submit']))
			{
				$result = create_preroll_ad($_POST);
				if ( ! $result)
				{
					echo pm_alert_error('There was a problem while inserting the new ad in your database.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					echo pm_alert_success('Your ad has been created. ');
				}
			}
		}
	break;
	
	case 'edit':
	
		if ($id)
		{
			if (isset($_POST['Submit']))
			{
				$result = update_preroll_ad($id, $_POST);
				if ( ! $result)
				{
					echo pm_alert_error('A problem was encountered while updating this ad.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					echo pm_alert_success('The ad was updated.');
				}
					
			}
		}
	
	break;
	
	case 'activate':
	case 'deactivate':
		
		if ($id)
		{	
			$sql = "UPDATE pm_preroll_ads 
					SET ";
			$sql .= ($action == "activate") ? " status='1' " : " status='0' ";
			$sql .= " WHERE id = '$id' ";
							
			if ( ! $result = mysql_query($sql))
			{
				echo pm_alert_error('A problem was encountered while updating this ad.<br />MySQL returned: '. mysql_error());
			}
			else
			{
				if ($action == "activate")
				{
					echo pm_alert_success('The ad is now active.');
				}
				else
				{
					echo pm_alert_success('The ad was deactivated.');
				}
			}
		}
		
	break;
	
	case 'delete':
	
		if ($id)
		{
			$ad = get_preroll_ad($id);

			if ( ! $ad)
			{
				$sql_err = mysql_error();
				
				if (strlen($sql_err) > 0)
				{
					echo pm_alert_error('A problem was encountered while updating this ad.<br />MySQL returned: '. $sql_err);
				}
				else
				{
					echo pm_alert_error('Could not delete this ad because it wasn\'t found in your database.');
				}

				break;
			}

			if ($_GET['key'] != md5($ad['name']))
			{
				echo pm_alert_error('Invalid key provided. Please reload the page and try again.');
				break;
			}
			$result = delete_preroll_ad($id);
			if ( ! $result)
			{
				echo pm_alert_error('A problem was encountered while updating this ad.<br />MySQL returned: '. mysql_error());
			}
			else
			{
				$sql = "DELETE FROM pm_ads_log 
						WHERE ad_id = $id 
						  AND ad_type = ". _AD_TYPE_PREROLL;
				@mysql_query($sql);
				
				echo pm_alert_success('The ad was deleted.');
			}
		}
		
	break;
}

if ($action != '')
{
	update_config('total_preroll_ads', count_entries('pm_preroll_ads', 'status', '1'));
}
?>

<div class="card card-blanche">
	<div class="card-body">
	</div><!--.card-body-->

<div class="datatable-scroll">
<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
<thead> 
<tr>
	<th>Name</th>
	<th align="center" class="text-center" width="10%">Duration</th>
	<th align="center" class="text-center" width="10%">Display to</th>
	<th align="center" class="text-center" width="15%">Status</th>
	<th align="center" style="text-align:center; width: 120px;">Action</th>
</tr>	
</thead>
<tbody>
<?php

$ads = array();

if ($total_ads > 0 || ($total_ads == 0 && $action == 'addnew'))
{
	$sql = "SELECT * FROM pm_preroll_ads 
			ORDER BY id DESC";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_assoc($result))
	{
		$options = array();
		if (strlen($row['options']) > 0)
		{
			$options = (array) unserialize($row['options']);
		}

		$ads[] = array_merge($row, $options);
		unset($options);
	}
	mysql_free_result($result);
}

if (pm_count($ads) > 0) : 
	foreach ($ads as $k => $ad) : 
?>
	<tr>
		<td>
			<a href="#modal_edit_ad_<?php echo $ad['id'];?>" onclick="location.href='#modal_edit_ad_<?php echo $ad['id'];?>';" data-toggle="modal" class="font-weight-bold text-dark"><?php echo $ad['name']; ?></a>
		</td>
		<td align="center" class="text-center"><?php echo sec2min($ad['duration']);?></td>
		<td align="center" class="text-center">
			<?php 
			switch($ad['user_group'])
			{
				case 0:
					echo 'All visitors'; 
				break;
				
				case 1:
					echo 'Logged-in users only';
				break;
				
				case 2:
					echo 'Visitors only';
				break;
			}
			?>
		</td>
		<td align="center" class="text-center"><?php echo ($ad['status'] == 1) ? '<a href="player-static-ads.php?act=deactivate&id='. $ad['id'] .'" class="badge badge-success label-clickable" rel="tooltip" title="Click to deactivate">Active</a>' : '<a href="player-static-ads.php?act=activate&id='. $ad['id'] .'" class="badge badge-secondary" rel="tooltip" title="Click to activate">Inactive</a>';?></td>
		<td align="center" class="text-center table-col-action">
<!--
			<?php if ($ad['status'] == 0) : ?>
				<a href="player-static-ads.php?act=activate&id=<?php echo $ad['id'];?>" class="list-icons-item text-success mr-1" rel="tooltip" title="Activate Ad"><i class="icon-eye"></i></a>
			<?php else : ?>
				<a href="player-static-ads.php?act=deactivate&id=<?php echo $ad['id'];?>" class="list-icons-item text-warning mr-1" rel="tooltip" title="Deactivate Ad"><i class="icon-eye-blocked"></i></a>
			<?php endif; ?>
-->
			<a href="#modal_edit_ad_<?php echo $ad['id'];?>" onclick="location.href='#modal_edit_ad_<?php echo $ad['id'];?>';" data-toggle="modal" class="list-icons-item mr-1" title="Edit"><i class="icon-pencil7"></i></a>
			<a href="#" onClick="delete_ad('<?php echo str_replace(array('"', "'"), array('', "\'"), $ad['name']);?>', 'player-static-ads.php?act=delete&id=<?php echo $ad['id'];?>&key=<?php echo md5($ad['name']);?>')" class="list-icons-item text-danger" rel="tooltip" title="Delete"><i class="icon-bin" ></i></a>
		</td>
	</tr>
<?php 
	endforeach;
else : ?>
<tr>
	<td colspan="6">
		No pre-roll ads have been defined.
	</td>
</tr>
<?php endif;?>
 </tbody>
</table>
</div>

</div><!--.card-->	 
</div>
<!-- /content area -->

<!-- edit modals -->
<?php if (pm_count($ads) > 0) : ?>
<?php foreach ($ads as $k => $ad) : ?>
<?php include('modals/modal-edit-ad-static.php'); ?>
<?php endforeach; ?>
<?php endif; ?>

<script type="text/javascript">
$(document).ready(function(){
	$('.category_dropdown').addClass("chzn-select");
	$('.source_dropdown').addClass("chzn-select");
	
	$(".chzn-select").chosen({width: "95%"});
	$(".chzn-select-deselect").chosen({allow_single_deselect:true});
	
	$('#skip_delay_seconds_new_span').hide();
	
	$('input[name="skip_delay_radio"]').change(function(){
		var selector = $(this).attr('child-input');
		if ($(this).val() == '1') {
			if (selector) {
				$('#'+ selector).fadeIn();
			} else {
				$('#skip_delay_seconds_new_span').fadeIn();
			}
		} else {
			if (selector) {
				$('#'+ selector).fadeOut();
			} else {
				$('#skip_delay_seconds_new_span').fadeOut();
			}
		}
	})
});
</script>
<?php
include('footer.php');