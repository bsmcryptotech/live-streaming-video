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
$_page_title = 'Classic banners';
include('header.php');

function manage_ad_form($action = 'addnew', $item = false)
{
	global $modframework;
	if (empty($item['id']))
	{
		$item['id'] = 0;
	}
	
	$target = '';
	switch($action)
	{
		case 'addnew':
			$target = 'banner-ads.php?act=addnew';
		break;
		case 'edit':
			$target = ($id != 0) ? 'banner-ads.php?act=edit&id='.$item['id'] : 'banner-ads.php?act=edit';
		break;
	}
	
	?>

	<div class="card card-blanche">
		<div class="card-body">
			<form name="ad_manager" method="post" action="<?php echo $target; ?>" enctype="application/x-www-form-urlencoded">
				<div class="form-group">
					<label>Ad Name</label>
					<input type="text" name="position" value="<?php echo htmlspecialchars($item['position']); ?>" placeholder="" class="form-control">
				</div>
				<div class="form-group">
					<label>Description</label>
					<input type="text" name="description" value="<?php echo htmlspecialchars($item['description']); ?>" placeholder="" class="form-control">
				</div>
			
				<div class="form-group">
					<label>HTML Code for your Ad</label>
					<textarea name="code" cols="60" rows="3" class="form-control"><?php echo $item['code']; ?></textarea>
				</div>
				<div class="form-group">
					<label>Enable Statistics</label>
					<br />
					<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="0" <?php echo ($item['disable_stats'] == 0) ? 'checked="checked"' : '';?>> Yes</label> 
					<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="1" <?php echo ($item['disable_stats'] == 1) ? 'checked="checked"' : '';?>> No</label>
				</div>

				<input type="hidden" name="active" value="1" />
				<div class="float-right">
					<a href="banner-ads.php" class="btn btn-outline bg-grey text-grey-800 btn-icon">Cancel</a>
					<button type="submit" name="Submit" value="Submit" class="btn btn-success" />Save</button>
				</div>
			</form>
		</div>
	</div>

	</div>
	</div>
	<?php
	return;
}


$action = $_GET['act'];

$total_ads = count_entries('pm_ads', '', '');
?>
<?php
include('modals/modal-create-ad-zone.php')
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
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="banner-ads.php" class="breadcrumb-item">Advertisments</a>
					<a href="banner-ads.php" class="breadcrumb-item"><span class="breadcrumb-item active"><?php echo $_page_title; ?></span></a>
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
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">TPL Code</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>The build-in ad manager allows you to define ads zones and assign advertisements within those ad zones.<br />An ad zone is an area on your site where you intend to place advertisements (e.g. header, under the video, registration page, under article, etc.). Once an ad zone is created all you have to do is insert the ad code. By using ad zones you can easily replace obsolete or low performing ads.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
						<p>The TPL code is the assigned variable you can use in your current template. There are several presets that come with every installation of PHP Melody, as listed below.<br />In this case, no template modifications are required. Just paste in your ad code and you're ready to go.</p>
						<p>For more details on how to include your own ad zones into the template files, please visit <a href="http://help.phpmelody.com/how-to-add-banners-manually/" target="_blank">http: //help.phpmelody.com/how-to-add-banners-manually/
</a></p>
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
				$arr_fields = array('position' => "Name", 'code' => "Code", 'active' => 'Status');
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
					echo pm_alert_error($errors);
					echo manage_ad_form('addnew', $_POST);// 0, $_POST['position'], $_POST['code'], $_POST['active']);
				}
				else
				{
					$position = secure_sql($_POST['position']);
					$code = secure_sql($_POST['code']);
					$description = secure_sql($_POST['description']);
					$active = ($_POST['active'] == 1) ? 1 : 0;
					$disable_stats = (int) $_POST['disable_stats'];
					$query = mysql_query("INSERT INTO pm_ads SET position = '".$position."', description = '".$description."', code = '".$code."', active = '".$active."', disable_stats = '". $disable_stats ."'");
					if ( ! $query)
					{
						echo pm_alert_error('There was an error while inserting the new ad in database.<br />MySQL returned: '. mysql_error());
					}	
					else
					{
						$new_ad_id = mysql_insert_id();
						$msg = '
						<h4>Done!</h4>
						<p>Your ad zone has been created. Since this is a new <strong>ad zone</strong>, you have manually add this ad zone to the desired location within the template.</p>
						<ol>
						<li>Pick the location for this new ad zone (e.g. header.tpl, index.tpl, footer.tpl)</li>
						<li>Paste the following code wherever you wish to display ads associate with this ad zone: <strong>{$ad_'.$new_ad_id.'}</strong></li>
						</ol>';
						if($_POST['active'] == 0)
							$msg .= "<br />New ads are not enabled by default. Remember to enable ads after creating them.";
						$msg = pm_alert_success($msg);
						$msg .= '<a href="banner-ads.php" class="btn btn-success ml-3">&larr; Return</a>';
						
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
		if ($id <= 0 || !is_numeric($id) || $id == '')
		{
			echo pm_alert_error('ID is not a valid value or it is missing.');
		}
		else
		{
			if(isset($_POST['Submit']))
			{
				$arr_fields = array('position' => "Name");
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
					
					echo pm_alert_error($errors);
					echo manage_ad_form('edit', $_POST);//, $id, $_POST['position'], $_POST['description'], $_POST['code'], $_POST['active']);
					
					include('footer.php');
					
					exit();
				}
				$position = secure_sql($_POST['position']);
				$code = secure_sql($_POST['code']);
				$description = secure_sql($_POST['description']);
				$active = ($_POST['active'] == 1) ? 1 : 0;
				$disable_stats = (int) $_POST['disable_stats']; 
				$query = mysql_query("UPDATE pm_ads SET position = '".$position."',
														description = '".$description."',
														code = '".$code."',
														active = '".$active."',
														disable_stats = '". $disable_stats ."' 
													WHERE id='".$id."'");
				if ( ! $query)
				{
					echo pm_alert_error('There was an error while inserting the new ad in database.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					echo pm_alert_success('The ad zone was updated.');
					echo '<a href="banner-ads.php" class="btn btn-success ml-3">&larr; Return</a>';
				}
					
			}
			else
			{
				$query = mysql_query("SELECT * FROM pm_ads WHERE id='".$id."'");
				if ( ! $query)
				{
					echo pm_alert_error('There was an error while retrieving your data.<br />MySQL returned: '. mysql_error());

					include('footer.php');
					exit();
				}
				
				$ad = mysql_fetch_assoc($query);
				if ($ad['id'] == '')
				{
					echo pm_alert_error('The selected as was not found in your database.');
				}
				else
				{
					echo manage_ad_form('edit', $ad); //$ad['id'], $ad['position'], $ad['description'], $ad['code'], $ad['active']);
				}
			}
		}
	
	break;
	
	case 'delete':
	case 'activate':
	case 'deactivate':
	case '':
	default:
	
		$total_ads = count_entries('pm_ads', '', '');

		if($action == 'delete')
		{
			$id = (int) $_GET['id'];
			if ($id <= 0 || !is_numeric($id) || $id == '')
			{
				echo pm_alert_error('Invalid or missing ID.');
			}
			else
			{
				$sql = "SELECT * 
						FROM pm_ads 
						WHERE id = ". $id; 
				$result = mysql_query($sql);
				$ad = mysql_fetch_assoc($result);
				
				if ($ad['protected'] == 1)
				{
					echo pm_alert_error('Sorry, the default ad spots cannot be removed. You can choose to disable them or create new ad zones.');
				}
				else 
				{
					$query = mysql_query("DELETE FROM pm_ads WHERE id = '".$id."'");
					if ( ! $query )
					{
						echo pm_alert_error('There was a problem while deleting this ad zone.<br />MySQL returned: '. mysql_error());
					}
					else
					{
						$sql = "DELETE FROM pm_ads_log 
								WHERE ad_id = $id 
								  AND ad_type = ". _AD_TYPE_CLASSIC;
						@mysql_query($sql); 

						echo pm_alert_success('The ad zone was deleted.');
					}
				}
			}
		}
		if($action == 'activate' || $action == 'deactivate')
		{
			$id = $_GET['id'];
			if ($id <= 0 || !is_numeric($id) || $id == '')
			{
				echo pm_alert_error('Invalid or missing ID.');
			}
			else
			{	
				
				$sql = '';
				if($action == "activate")
					$sql = "UPDATE pm_ads SET active='1' WHERE id = '".$id."' LIMIT 1";
				else
					$sql = "UPDATE pm_ads SET active='0' WHERE id = '".$id."' LIMIT 1";
				
				$query = mysql_query($sql);
				if ( ! $query )
				{
					echo pm_alert_error('A problem was encountered while activating/deactivating this ad zone.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					echo ($action == "activate") ? pm_alert_success('The ad zone is now active.') : pm_alert_success('The ad zone was deactivated.');
				}
			}
		}
?>


<div class="card">
	<div class="card-body">
	</div><!--.card-body-->

		<div class="datatable-scroll">
			<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
			<thead> 
				<tr>
					<th>Name</th>
					<th align="center" class="text-center" width="10%">TPL Code</th>
					<th align="center" class="text-center" width="15%">Status</th>
					<th align="center" style="text-align:center; width: 120px;">Action</th>
				</tr>	
			</thead>
			<tbody>
			<?php
				 
				// display all ads
				$query = mysql_query("SELECT * FROM pm_ads ORDER BY id DESC");
				$i = 0;
				while($row = mysql_fetch_assoc($query))
				{	
					$clean_title = str_replace(array('"', "'"), array('', "\'"), $row['position']);
					$row_class = ($i++ % 2) ? 'table_row1' : 'table_row2';
					
					?>
					<tr class="<?php echo $row_class;?>">
						<td>
							<a href="#" class="adzone_update_<?php echo  $row['id'] ; ?> font-weight-bold text-dark"><?php echo htmlspecialchars($row['position']); ?></a> 
							<span class="d-block text-muted font-size-xs text-italic"><?php echo $row['description']; ?></span>
						</td>
						<td align="center" class="text-center">
							{$ad_<?php echo $row['id']; ?>}
						</td>
						<td align="center" class="text-center">
							<?php if ($row['active'] == 1) :?>
								<a href="banner-ads.php?act=deactivate&id=<?php echo $row['id']; ?>" class="badge badge-success label-clickable" rel="tooltip" title="Click to deactivate">Active</a>
							<?php else : ?>
								<a href="banner-ads.php?act=activate&id=<?php echo $row['id']; ?>" class="badge badge-secondary" rel="tooltip" title="Click to activate">Inactive</a></span>
							<?php endif; ?>
						</td>
						<td align="center" class="text-center table-col-action">
	<!--
							<?php if ($row['active'] == 0) :?>
							 <a href="banner-ads.php?act=activate&id=<?php echo $row['id']; ?>" class="list-icons-item text-success mr-1" rel="tooltip" title="Activate Ad"><i class="icon-eye"></i></a>
							<?php else : ?>
							 <a href="banner-ads.php?act=deactivate&id=<?php echo $row['id']; ?>" class="list-icons-item text-warning mr-1" rel="tooltip" title="Deactivate Ad"><i class="icon-eye-blocked"></i></a>
							<?php endif; ?>
	-->
							<a href="#" class="adzone_update_<?php echo  $row['id'] ; ?> list-icons-item mr-1" rel="tooltip" title="Edit"><i class="icon-pencil7"></i></a> 
							<a href="#" onClick="delete_ad('<?php echo  $clean_title ; ?>', 'banner-ads.php?act=delete&id=<?php echo $row['id']; ?>')" class="list-icons-item text-danger" rel="tooltip" title="Delete"><i class="icon-bin"></i></a>
						</td>
					</tr>
					<tr>
						<td colspan="5" class="m-0 p-0 td_expanded_row">
							<div id="adzone_update_<?php echo  $row['id'] ; ?>" name="<?php echo  $row['id'] ; ?>">
								<div class="adzone_update_form" style="padding: 10px; margin: 10px;">
								<form name="adzone_update_<?php echo  $row['id'] ; ?>" method="post" action="banner-ads.php?act=edit&id=<?php echo $row['id']; ?>" enctype="application/x-www-form-urlencoded">
									<div class="form-group">
									<label>Name</label>
										<input type="text" name="position" value="<?php echo htmlspecialchars($row['position']); ?>" size="40" class="form-control" />
									</div>
									<div class="form-group">
										<label>Description</label>
										<input type="text" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" size="40" class="form-control" />
									</div>
									<div class="form-group">
										<label>HTML Code</label>
										<textarea name="code" cols="60" rows="2" class="form-control" ><?php echo $row['code']; ?></textarea>
									</div>
									<div class="form-group">
										<label>Enable Statistics</label>
										<br />
										<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="0" <?php echo ($item['disable_stats'] == 0) ? 'checked="checked"' : '';?>> Yes</label> 
										<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="1" <?php echo ($item['disable_stats'] == 1) ? 'checked="checked"' : '';?>> No</label>
									</div>

									<div class="form-group">
									<input type="hidden" name="active" value="<?php echo $row['active']; ?>" />
									<input type="submit" name="Submit" value="Save" class="btn btn-sm btn-success border-radius0" />
									<a href="#" id="adzone_update_<?php echo  $row['id'] ; ?>" class="btn-sm">Cancel</a>
									</div>
								</form>
								</div>
							</div>
						</td>
					</tr>
					<?php
				}
				mysql_free_result($query);
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