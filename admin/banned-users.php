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
$_page_title = 'Banned users';
include('header.php');

$action		= $_GET['a'];
$page		= (int) $_GET['page'];
$userid		= (int) trim($_GET['uid']);

if(empty($page))
	$page = 1;
$limit = 20;
$from = $page * $limit - ($limit);


$total_members = count_entries('pm_banlist','','');

?>
	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
	<div class="page-header-wrapper"> 
		<div class="page-header page-header-light">
			<div class="page-header-content header-elements-md-inline">
			<div class="d-flex justify-content-between w-100">
				<div class="page-title d-flex">
					<h4><?php echo $_page_title; ?></h4>
				</div>

			<div class="header-elements">
				<div class="d-flex-inline align-self-center ml-auto">
  						<a href="#banUser" role="button" class="btn btn-sm btn-outline alpha-warning text-warning-400 border-warning-400 border-2" data-toggle="modal"><i class="icon-user-block"></i> Ban user</a>
					</div>
				</div>
			</div>
			</div>
			<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
				<div class="d-flex">
					<div class="breadcrumb">
						<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
						<a href="users.php" class="breadcrumb-item">Users</a>
						<a href="blacklist.php" class="breadcrumb-item"><span class="breadcrumb-item active"><?php echo $_page_title; ?></span></a>
					</div>
				</div>
			</div>
		</div>
		<!-- /page header -->
	</div><!--.page-header-wrapper-->

		<!-- Content area -->
		<div class="content content-full-width">
	
<?php echo $info_msg; ?>

<?php
switch($action)
{

	default:
	case 'delete':
	case 'show':
		
		if ($action == 'delete' && ! csrfguard_check_referer('_admin_banlist'))
		{
			echo pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
		}
		else if($action == 'delete')
		{
			if(!empty($userid))
			{
				$sql = "DELETE FROM pm_banlist WHERE user_id = '".$userid."'";
				$result = mysql_query($sql);
				if(!$result)
				{
					$info_msg = pm_alert_error('An error occurred!<br />MySQL Reported: '.mysql_error());
				}
				else
				{
					$info_msg = pm_alert_success('The ban list was updated.');
				}
			}
			else
			{
				$info_msg = pm_alert_error('"'.$userid.'" is not a valid user ID.');
			}
			echo $info_msg;
		}
		
		if (isset($_POST['Submit']) && $action == 'ban' && ( ! csrfguard_check_referer('_admin_banlist')))
		{
			echo pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
		}
		else if (isset($_POST['Submit']) && $action == 'ban')
		{
			$username = trim($_POST['username']);
			$reason = trim($_POST['reason']);
			$reason = nl2br($reason);
			$reason = secure_sql($reason);
			
			$sql = "SELECT id, power FROM pm_users WHERE username LIKE '".secure_sql($username)."'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$info_msg = pm_alert_error('An error occurred!<br />MySQL Reported: '.mysql_error());
			}
			else
			{
				if(mysql_num_rows($result) == 0)
				{
					$info_msg = pm_alert_error('User not found');
				}
				else
				{
					$info = mysql_fetch_assoc($result);
					if ($info['id'] == $userdata['id'])
					{
						$info_msg = pm_alert_error('You can\'t do that.');
					}
					else if ($info['power'] != U_ADMIN)
					{
						$sql = "INSERT INTO pm_banlist SET user_id = '".$info['id']."', reason ='".$reason."'";
						$result = mysql_query($sql);
						if ( ! $result)
						{
							$info_msg = pm_alert_error('An error occurred while performing your request.<br />MySQL Reported: '.mysql_error());
						}
						else
						{
							$info_msg = pm_alert_success('The ban list was successfully updated.');
						}
					}
					else
					{
						$info_msg = pm_alert_error('Administrator accounts cannot be banned.');
					}
				}
			}
			echo $info_msg;
		}
		
		$banlist_nonce = csrfguard_raw('_admin_banlist');
		
		$banlist = a_list_banned($from, $limit);
		
		// generate smart pagination
		$filename = 'banned-users.php';
	
		$pagination = '';
		$pagination = a_generate_smart_pagination($page, $total_members, $limit, 1, $filename, '');
?>

<div class="card card-blanche">
	<div class="card-body">
	</div><!--.card-body-->

	<div class="datatable-scroll">
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
		 <thead>
		  <tr>
		   <th width="35">ID</th>
		   <th width="210">Username</th>
		   <th>Reason</th>
		   <th width="" style="text-align:center; width: 90px;">Action</th>
		  </tr>
		 </thead>
		 <tbody>
		  <?php if ($pagination != '') : ?>
		  <tr class="tablePagination">
			<td colspan="4" class="tableFooter">
				<div class="pagination float-right"><?php echo $pagination; ?></div>
			</td>
		  </tr>
		  <?php endif; ?>
		  
		  <?php echo $banlist; ?>
		  
		  <?php if ($pagination != '') : ?>
		  <tr class="tablePagination">
			<td colspan="4" class="tableFooter">
				<div class="pagination float-right"><?php echo $pagination; ?></div>
			</td>
		  </tr>
		  <?php endif; ?>
		 </tbody>
		</table>
	</div>

<?php
	break;
}
?>

</div><!--.card-->	 
</div>
<!-- /content area -->

<?php
include('modals/modal-ban-user.php');
?>

<?php
include('footer.php');