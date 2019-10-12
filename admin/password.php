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

//$showm = '8';
$_page_title = 'Update password';
include('header.php');

if(!empty($_POST['submit']) && $_POST['submit'] == 'Update') 
{
	$username = $userdata['username'];
	$query = mysql_query("SELECT * FROM pm_users where username = '".$username."' AND power = '".U_ADMIN."'");
	$rows = mysql_num_rows($query);
	$r = mysql_fetch_array($query);
	
	// ** IF USER DOESN'T EXIST REDIRECT TO HOMEPAGE ** //
	if($rows == 0) {
		header("Location: "._URL.""); 
		exit;
	}
	
	if(isset($_POST['submit']) && $_POST['submit'] == "Update" ) 
	{
		if (pm_password_verify($_POST['pass'], $r['password']))
        {
			$new_pass = trim($_POST['new_pass']);
			if(!empty($new_pass)) 
			{
				$new_pass = pm_password_hash($new_pass);
				@mysql_query("UPDATE pm_users SET password = '". secure_sql($new_pass) ."' WHERE username = '". $r['username'] ."' AND power = '1'");
				$info_msg = pm_alert_success('The admin password was updated. You will be asked shortly to log in with the new password.');
			} 
			else 
			{
				$info_msg = pm_alert_error('Your new password must be at least 5 characters long. Safety before anything else.');
			}
		} 
		else
		{
			$info_msg = pm_alert_error('Try again! This is not your current password.');
		}
	}	
} 
else 
{
	$info_msg = pm_alert_info('Use a strong and memorable password.');
}
?>
<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title;?></span></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="settings.php" class="breadcrumb-item">Settings</a>
					<span class="breadcrumb-item active"><?php echo $_page_title; ?></span>

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
	<div class="content">

	<?php echo $info_msg; ?>

	<div class="card">
	<form name="register-form" method="post" action="password.php" class="form-horizontal">
		<table width="100%" border="0" cellspacing="2" cellpadding="5" class="table table-xs table-responsive">
		  <tr>
			<td>
			<div class="form-group row">
			<label class="col-form-label col-md-5">Current Password</label>
			<div class="col-md-7">
				<input name="pass" class="form-control" type="password" maxlength="32" />
			</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-md-5">New Password</label>
				<div class="col-md-7">
					<input name="new_pass" class="form-control" type="password" maxlength="32" />
				</div>
			</div>
			</td>
			</tr>
		</table>
		<div class="card-footer">
			<input type="submit" name="submit" value="Update" class="btn btn-success" />
		</div>
	</form>
	</div>
</div><!-- .content -->
<?php
include('footer.php');