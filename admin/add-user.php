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
$_page_title = 'Add User';
include('header.php');

$inputs = array();

load_countries_list();

$errors = array();
if ($_POST['Submit'] != '')
{
	$required_fields = array('email' => 'Email',
							 'username' => 'Username', 
							 'pass' => 'Password', 
							 'confirm_pass' => 'Re-type password', 
							 'name' => 'Name'
							 );
	foreach ($_POST as $key => $value) 
	{
		$value = trim($value);
		if(array_key_exists(strtolower($key), $required_fields) && empty($value) )
		{
			$errors[$key] = '<em>'. $required_fields[$key]. '</em> is a required field.';
		}
	}
	
	if ($_POST['country'] == '-1' || $_POST['country'] == '')
	{
		$errors['country'] = 'Please select a country.';
	}
	
	foreach($_POST as $key => $val)
	{
		$val = trim($val);
		$val = specialchars($val, 1);
		$inputs[$key] = $val;
	}
	
	// password, email & username validation
	if (pm_count($errors) == 0)
	{
		$email = trim($_POST['email']);
		$username =	trim($_POST['username']);
		$username = sanitize_user($username, 0); // Since v2.0
		$pass =	$_POST['pass'];
		$conf_pass = $_POST['confirm_pass'];
		
		if (strcmp($pass, $conf_pass) != 0) 
		{ 
			$errors['pass'] = 'Password and Confirmation Password don\'t match';
		}
		
		if ($var = validate_email($email)) 
		{
			if ($var == 1) 
			{
				$errors['email'] = 'Email address is not valid';
			}
			
			if ($var == 2)
			{
				$sql = "SELECT username FROM pm_users WHERE email LIKE '". str_replace("\'", "''", $email) ."'";
				$result = mysql_query($sql);
				$u = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				$errors['email'] = 'This email address is already in use by <a href="'. get_profile_url($u) .'"  target="_blank">'. $u['username'] .'</a>.';
			}
		}
		
		if ($var = check_username($username)) 
		{ 
			if ($var == 1)
			{
				$errors['username'] = 'Username should be at least 4 characters long.';
			}
			
			if ($var == 2)
			{
				$errors['username'] = 'Username contains invalid characters. It should contain letters and numbers only. You can enable "non-latin" usernames from <strong>Settings</strong> > <strong>User Settings</strong>.';
			}
			
			if ($var == 3)
			{
				$errors['username'] = 'This Username is already taken. View <a href="'. get_profile_url(array('username' => $username)) .'" target="_blank">profile</a>.';
			}
		}
	}
	
	if (pm_count($errors) == 0)
	{
		$aboutme = removeEvilTags($_POST['aboutme']);
		$aboutme = word_wrap_pass($aboutme);
		$aboutme = secure_sql($aboutme);
		$aboutme = specialchars($aboutme, 1);
		$aboutme = str_replace('\n', "<br />", $aboutme);
		
		$links = array('website' => trim($_POST['website']),
					   'youtube' => trim($_POST['youtube']),
					   'facebook' => trim($_POST['facebook']),
					   'twitter' => trim($_POST['twitter']),
					   'instagram' => trim($_POST['instagram']),
					   'google_plus' => trim($_POST['google_plus'])
					);
		
		$sql = "INSERT INTO pm_users (username, password, email, name, country, reg_date, last_signin, reg_ip, power, about, social_links,  
									  channel_slug, channel_verified, channel_featured)
				VALUES ('". secure_sql($username) ."', 
						'". secure_sql(pm_password_hash($pass)) ."', 
						'". $email ."', 
						'". secure_sql( trim($_POST['name']) ) ."', 
						'". secure_sql($_POST['country']) ."', 
						'". time() ."', 
						'". time() ."', 
						'127.0.0.1', 
						'". secure_sql($_POST['power']) ."',
						'". $aboutme ."',
						'". secure_sql(serialize($links)) ."',
						'". secure_sql(sanitize_title(trim($_POST['channel_slug']))) ."',
						'". secure_sql($_POST['channel_verified']) ."',
						'". secure_sql($_POST['channel_featured']) ."')";
		if ( ! $result = mysql_query($sql))
		{
			$errors[] = 'An error occurred while adding this user: '. mysql_error(); 
		}
		else
		{
			$user_id = mysql_insert_id();
			$success = 'User account created. <a href="'. _URL .'/'. _ADMIN_FOLDER .'/edit-user.php?uid='. $user_id .'">Edit</a> or <a href="'. get_profile_url(array('username' => $username)) .'">view profile</a>.';
			
			insert_playlist($user_id, PLAYLIST_TYPE_WATCH_LATER, array());
			insert_playlist($user_id, PLAYLIST_TYPE_FAVORITES, array());
			insert_playlist($user_id, PLAYLIST_TYPE_LIKED, array());
			insert_playlist($user_id, PLAYLIST_TYPE_HISTORY, array());
			
			if (_MOD_SOCIAL)
			{
				log_activity(array('user_id' => $user_id, 'activity_type' => ACT_TYPE_JOIN));
			}
		}
	}
	else
	{
		
	}
}
?>
<!-- Main content -->
<div class="content-wrapper">

<div class="page-header-wrapper page-header-edit"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title; ?></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<button type="submit" name="Submit" value="Submit" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.forms[0].submit({return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')});" form="edit_profile_form"><i class="mi-check"></i> Add user</button>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="users.php" class="breadcrumb-item">Users</a>
					<a href="add-user.php" class="breadcrumb-item active"><?php echo $_page_title; ?></a>
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
	<div class="content content-edit-page">



	
	<?php if ($success != '') : ?>
		<?php echo pm_alert_success($success);?>
		<hr />
		<a href="users.php" class="btn btn-sm btn-success">&larr; Manage Users</a> 
		<a href="add-user.php" class="btn btn-sm btn-success">Add another user</a>
	
	<?php else: ?>
	
		<?php 
		if (pm_count($errors) > 0)
		{
			echo pm_alert_error($errors);
		}
		?>


<form name="edit_profile_form" id="edit_profile_form" method="POST" action="add-user.php" class="form-horizontal" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')">
<div class="row">
	<div class="col-md-7">
		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Account Details</h6>
				<div class="header-elements">
					<div class="list-icons">
					</div>
				</div>
			</div>

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-xs">
				<tr>
					<td class="w-25">Name</td>
					<td class="w-75">
						<input name="name" type="text" class="form-control form-required" value="<?php echo $inputs['name']; ?>" />
					</td>
				</tr>
				<tr>
					<td>Username</td>
					<td>
						<input type="text" name="username" class="form-control form-required" value="<?php echo $inputs['username']; ?>" />
					</td>
				</tr>
				<tr>
					<td>Password</td>
					<td>
						<input name="pass" type="password" class="form-control form-required" value="<?php echo $inputs['pass'];?>" maxlength="32" />
					</td>
				</tr>
				<tr>
					<td>Re-type Password</td>
					<td>
						<input name="confirm_pass" type="password" class="form-control form-required" value="<?php echo $inputs['confirm_pass'];?>" maxlength="32" />
					</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>
						<input type="text" name="email" class="form-control form-required" value="<?php echo $inputs['email']; ?>" />
					</td>
				</tr>
				<tr>
					<td>User Group</td>
					<td>
						<select name="power" class="custom-select form-control">
							<option value="<?php echo U_ACTIVE;?>" <?php if($inputs['power'] == U_ACTIVE) echo 'selected="selected"';?> >Regular User</option>
							<?php if(is_admin()) : ?>
							<option value="<?php echo U_EDITOR;?>" <?php if($inputs['power'] == U_EDITOR) echo 'selected="selected"';?> >Editor</option>
							<option value="<?php echo U_MODERATOR;?>" <?php if($inputs['power'] == U_MODERATOR) echo 'selected="selected"';?> >Moderator</option>
							<option value="<?php echo U_ADMIN;?>" <?php if($inputs['power'] == U_ADMIN) echo 'selected="selected"';?> >Administrator</option>
							<?php endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Country</td>
					<td>
						<select name="country" class="custom-select form-control">
							<option value="-1">Select one</option>
							<?php
									$opt = '';
									foreach($_countries_list as $k => $v)
									{
										$opt = "<option value=\"".$k."\"";
										if( $inputs['country'] == $k )
											$opt .= " selected ";
										$opt .= ">".$v."</option>";
										echo $opt;
									}
									?>
						</select>
					</td>
				</tr>
				<tr>
					<td>About</td>
					<td>
						<textarea name="aboutme" rows="4" class="form-control"><?php echo $inputs['aboutme']; ?></textarea>
					</td>
				</tr>
			</table>


		</div><!--.card-->
	</div><!--. col-md-9 main-->

	<div class="col-md-5">

		<?php if (_MOD_SOCIAL) : ?>
		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Channel Settings</h6>
				<div class="header-elements">
					<div class="list-icons">
					</div>
				</div>
			</div>
			<table class="table table-xs">
				<tr> 
					<td class="w-25">Channel URL</td> 
					<td class="w-75">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text font-size-sm" id="">.../user/</span>
							</div>
							<input name="channel_slug" type="text" value="<?php echo $inputs['channel_slug']; ?>" class="form-control col-md-4" placeholder="channel-name" />
							
							<span class="input-group-text bg-transparent border-0"><a href="#" class="text-dark opacity-50" rel="popover" data-placement="top" data-trigger="hover" data-content="Allow users to create easy-to-remember custom URLs for their channel."><i class="mi-info-outline"></i></a></span>
						</div>
					</td>
				</tr>
				<tr>
					<td>Verified Channel</td>
					<td>
						<label><input name="channel_verified" type="radio" value="1" <?php if($inputs['channel_verified'] == "1") echo "checked"; ?> /> Yes</label>
						<label><input name="channel_verified" type="radio" value="0" <?php if(empty($inputs['channel_verified'])) echo "checked"; ?> /> No</label>
						<a href="http://help.phpmelody.com/verified-channels/" class="text-dark opacity-50" target="_blank" rel="popover" data-html="true" data-placement="top" data-trigger="hover" data-content="Users with the 'Verified Channel' mark represent a trusted source of content and their identity has been verified by a real person.<br /><strong>Click the icon to learn more.</strong>"><i class="mi-info-outline"></i></a>
					</td>
				</tr>
				<tr>
					<td>Featured Channel</td>
					<td>
						<label><input name="channel_featured" type="radio" value="1" <?php if($inputs['channel_featured'] == "1") echo "checked"; ?> /> Yes</label>
						<label><input name="channel_featured" type="radio" value="0" <?php if(empty($inputs['channel_featured'])) echo "checked"; ?> /> No</label>
						<a href="http://help.phpmelody.com/featured-channels/" class="text-dark opacity-50" target="_blank" rel="popover" data-html="true" data-placement="top" data-trigger="hover" data-content="The 'Featured Channel' option allows you to promote and/or provide a higher visibility to certain accounts from your user base. <br /><strong>Click the icon to learn more.</strong>"><i class="mi-info-outline"></i></a>
					</td>
				</tr>
			</table>

		</div><!--.card-->
		<?php else : ?>
		<input name="channel_slug" type="hidden" value="" />
		<input name="channel_verified" type="hidden" value="0" />
		<input name="channel_featured" type="hidden" value="0" />
		<?php endif; ?>

		<div class="card">
			<div class="card-header bg-white header-elements-inline">
				<h6 class="card-title font-weight-semibold">Social Links</h6>
				<div class="header-elements">
					<div class="list-icons">
					</div>
				</div>
			</div>
			<table class="table pm-tables-settings">
			<tr>
				<td class="w-25">Website URL</td>
				<td>
					<input type="text" name="website" size="45" placeholder="https://" value="<?php echo $inputs['website']; ?>" class="form-control" />
				</td>
				</tr>
			<tr>
				<td>FaceBook URL</td>
				<td>
				<input type="text" name="facebook" size="45" placeholder="https://" value="<?php echo $inputs['facebook']; ?>" class="form-control" />
				</td>
				</tr>
			<tr>
				<td>Twitter URL</td>
				<td>
					<input type="text" name="twitter" size="45" placeholder="https://" value="<?php echo $inputs['twitter']; ?>" class="form-control" />
				</td>
				</tr>
			<tr>
				<td>Instagram URL</td>
				<td>
					<input type="text" name="instagram" size="45" placeholder="https://" value="<?php echo $inputs['instagram']; ?>" class="form-control" />
				</td>
			  </tr>
			</table>
		</div><!--.card-->
	</div>
</div>
<?php endif; // form  ?>

	<div id="stack-controls-disabled" class="list-controls">
		<div class="float-right">
			<div class="btn-group">
			<?php if ($success == '') : ?>
			<button type="submit" name="Submit" value="Submit" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2"><i class="mi-check"></i> Add user</button>
			<?php endif; ?>
			</div>
		</div>
	</div><!-- #list-controls -->

</form>
</div>
<!-- /content area -->
<?php
include('footer.php');