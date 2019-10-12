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

session_start();
require_once('../config.php');
include_once('functions.php');
include_once( ABSPATH . 'include/user_functions.php');
include_once( ABSPATH . 'include/islogged.php');

if (is_user_logged_in() && (is_admin() || is_moderator() || is_editor())) 
{
	$redir = get_last_referer();
	if( $redir === false || $redir == '/index.php' || $redir == '/index.html')
	{ 	
		$redir = '/'. _ADMIN_FOLDER .'/index.php';
	}
	header('Location: '. _URL . $redir);
	exit();
}

if ($_POST['Login'] == 'Login')
{
	$user = sanitize_user(trim($_POST['ausername']));

	if ($user != '' && $_POST['apassword'] != '')
	{
		$sql = "SELECT * FROM pm_users 
				WHERE username = '". secure_sql($user) ."'";
		$result = @mysql_query($sql);
		$count = @mysql_num_rows($result);
		$row = @mysql_fetch_assoc($result);
		@mysql_free_result($result);
		
		if ( ! confirm_login($user, $_POST['apassword']))
        {
			$error = pm_alert_error('Your username and password don\'t match.');
			@log_error('Failed attempt to log in to Admin Area. (Username used: <em>'.$user.'</em> / IP: <em>'.pm_get_ip().'</em>)', 'Admin login', 1);
		}
		else
		{
			if ( ! in_array($row['power'], array(U_ADMIN, U_MODERATOR, U_EDITOR)))
			{
				$error = pm_alert_error('You do not have permission to access this area.');
				@log_error('Admin Area: Failed login attempt (Username: <em>'.$user.'</em> / IP: <em>'.pm_get_ip().'</em>)', 'Admin login', 1);
			}
			else 
			{
				log_user_in($user, $_POST['apassword']);
			
				$redir = get_last_referer();
	
				if( $redir === false || $redir == '/index.php' || $redir == '/index.html')
				{ 	
					$redir = '/'. _ADMIN_FOLDER .'/index.php';
				}
				header("Location: ". _URL . $redir);
				
				exit();
			}
		}
	}
	else
	{
		$error = pm_alert_error('Please enter your username and password.');
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=edge,chrome=1">
<title>Admin Area - Log in</title>
<link rel="shortcut icon" type="image/ico" href="img/favicon.ico" />

<link href="//fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
<link href="css/icon-moon.css" rel="stylesheet" type="text/css">
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="css/bootstrap-admin.min.css" rel="stylesheet" type="text/css">
<link href="css/admin-melody.css" rel="stylesheet" type="text/css">
<style type="text/css">
body{background-color:#2a405e;font-family:'Helvetica Neue',Helvetica,Arial Geneva,sans-serif;font-size:12px;margin:0;padding:0}
.form-control-feedback {top:11px;}
.content-wrapper {border-radius:0;background-color:transparent;z-index:none;}
.content-wrapper .content {background-color: transparent !important;}
</style>
<script type="text/javascript">
window.onload = function() {
  document.getElementById("hocusfocus").focus();
}
</script>
</head>
<body>
<!--[if lt IE 9]>
<div class="alert alert-info alert-old-browser">The browser you are using could be limiting the potential of <?php echo _SITENAME; ?>. We strongly recommend that you upgrade to a newer/different browser.</div>
<![endif]-->

<div class="content-wrapper">
	<div class="content d-flex justify-content-center align-items-center">
		<!-- Login form -->
		<form action="login.php" method="post" name="login" class="login-form animated-fast slideInDown">
		<?php echo $error; ?>
			<div class="card mb-0">
				<div class="card-body">
					<div class="text-center mb-3">
						<i class="icon-user-lock icon-2x text-primary-300 border-primary-300 border-3 rounded-round p-3 mb-3 mt-1 opacity-50"></i>
						<h5 class="mb-0">Login to your account</h5>
					</div>

					<div class="form-group form-group-feedback form-group-feedback-left">
						<input type="text" name="ausername" class="form-control" placeholder="Username" autofocus>
						<div class="form-control-feedback">
							<i class="icon-user text-muted"></i>
						</div>
					</div>

					<div class="form-group form-group-feedback form-group-feedback-left">
						<input type="password" name="apassword" class="form-control" placeholder="Password">
						<div class="form-control-feedback">
							<i class="icon-lock2 text-muted"></i>
						</div>
					</div>

					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-block" name="Login" value="Login" id="login">Sign in</button>
					</div>

					<div class="text-center">
						<a href="<?php echo _URL."/login."._FEXT."?do=forgot_pass";?>" target="_blank">Forgot password?</a>
					</div>
				</div>
			</div>
			<div class="text-center mt-2">
				<a href="<?php echo _URL?>" class="text-light opacity-50">&larr; Return to <?php echo _SITENAME; ?></a>
				<!--<div align="center">Powered by PHP Melody <?php echo _PM_VERSION; ?></div>--> 
			</div>
		</form>
		<!-- /login form -->
	</div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script> 
<script type="text/javascript">
$('#login').click(function() {
	$(this).html('Signing in...');
});
</script>
</body>
</html>
