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
// | Copyright: (c) 2004-2016 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '8';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_colorpicker = 1;
$load_scrolltofixed = 1;
$_page_title = 'Settings';
include('header.php');

//$config	= get_config();

$inputs = array();
$info_msg = '';
$video_sources = a_fetch_video_sources();

if (_MOD_SOCIAL && ! array_key_exists('activity_options', $config))
{
	add_config('activity_options', serialize($default_activity_options));
}

if ($_POST['submit'] == "Save" && ( ! csrfguard_check_referer('_admin_settings')))
{
	$info_msg .= pm_alert_warning('Invalid token or session expired. Please load this page from the menu and try again.');
}
else if ($_POST['submit'] == "Save")
{
	$req_fields = array("contact_mail" => "Contact mail",
						"isnew_days" => "Mark video as 'new' for",
						"ispopular" => "Mark video as 'popular' for",
						"comments_page" => "Comments per page"
					);
	$num_fields = array('isnew_days', 'ispopular', 'comments_page', 'account_activation', 'issmtp', 'player_autoplay', 'player_autobuff', 'default_lang', 
						'player_w', 'player_h', 'player_w_index', 'player_h_index', 'player_w_favs', 'player_h_favs', 'player_w_embed', 'player_h_embed', 
						'mod_article', 'bin_rating_allow_anon_voting', 'maintenance_mode', 'featured_autoplay', 'keyboard_shortcuts', 'disable_indexing', 
						'allow_playlists', 'playlists_limit', 'playlists_items_limit', 'comment_system_native', 'comment_system_facebook', 'comment_system_disqus',
						'stopbadcomments', 'guests_can_comment', 'comm_moderation_level', 'use_hq_vids', 'mod_social', 'allow_user_uploadvideo', 'allow_user_suggestvideo',
						'auto_approve_suggested_videos', 'register_time_to_submit', 'allow_embedding', 'eu_cookie_warning', 'auto_approve_suggested_videos_verified',
						'allow_user_edit_video', 'allow_user_delete_video', 'allow_emojis', 'oauth_facebook', 'oauth_twitter', 'csrfguard'
					);
	
	// set unchecked checkboxes
	$_POST['comment_system_native'] = ( ! array_key_exists('comment_system_native', $_POST)) ? 0 : $_POST['comment_system_native'];
	$_POST['comment_system_facebook'] = ( ! array_key_exists('comment_system_native', $_POST)) ? 0 : $_POST['comment_system_facebook'];
	$_POST['comment_system_disqus'] = ( ! array_key_exists('comment_system_native', $_POST)) ? 0 : $_POST['comment_system_disqus'];
	
	foreach($_POST as $k => $v)
	{
		if($_POST[$k] == '' && in_array($k, $req_fields))
		{
			$info_msg .= pm_alert_warning("'".$req_fields[$k] . "' field cannot be left blank!");
		}
		if(in_array($k, $num_fields))
		{
			$v = (int) $v;
			$v = abs($v);
			$inputs[$k] = $v;
		}
		else if ( ! is_array($v))
		{
			$inputs[$k] = stripslashes($v);
		}
	}
	
	if ( ! $inputs['comment_system_'. $inputs['comment_system_primary']])
	{
		if ($inputs['comment_system_native'])
		{
			$inputs['comment_system_primary'] = 'native';
		}
		else if ($inputs['comment_system_facebook']) 
		{
			$inputs['comment_system_primary'] = 'facebook';
		}
		else if ($inputs['comment_system_disqus']) 
		{
			$inputs['comment_system_primary'] = 'disqus';
		}
	}
	
	
	if (($inputs['comment_system_native'] + $inputs['comment_system_facebook'] + $inputs['comment_system_disqus']) == 0)
	{
		$inputs['comment_system'] = 'off';
	}
	
	$inputs['mail_pass'] = str_replace('&quot;', '"', $inputs['mail_pass']);

	if($inputs['videoads_delay'] == '')
		$inputs['videoads_delay'] = 0;
	switch($inputs['videoads_delay_timespan'])
	{
		case 'minutes':
			$inputs['videoads_delay'] = $inputs['videoads_delay'] * 60;
		break;
		case 'hours':
			$inputs['videoads_delay'] = $inputs['videoads_delay'] * 60 * 60;
		break;
	}

	//preroll_ads_delay
	if($inputs['preroll_ads_delay'] == '')
		$inputs['preroll_ads_delay'] = 0;
	switch($inputs['preroll_ads_delay_timespan'])
	{
		case 'minutes':
			$inputs['preroll_ads_delay'] = $inputs['preroll_ads_delay'] * 60;
		break;
		case 'hours':
			$inputs['preroll_ads_delay'] = $inputs['preroll_ads_delay'] * 60 * 60;
		break;
	}

	//	Template has changed? Clear the Smarty Cache & Compile directories
	if ($inputs['jwplayerskin'] != $config['jwplayerskin'])
	{
		//	empty compile directory
		$dir = @opendir($smarty->compile_dir);
		if ($dir)
		{
			while (false !== ($file = readdir($dir)))
			{
				if(strlen($file) > 2)
				{
					$tmp_parts = explode('.', $file);
					$ext = array_pop($tmp_parts);
					$ext = strtolower($ext);

					if ($ext == 'php' && strpos($file, '%') !== false)
					{
						unlink($smarty->compile_dir .'/'. $file);
					}
				}
			}
			closedir($dir);
		}

		//	empty cache directory
		$dir = @opendir($smarty->cache_dir);
		if ($dir)
		{
			while (false !== ($file = readdir($dir)))
			{
				if(strlen($file) > 2)
				{
					$tmp_parts = explode('.', $file);
					$ext = array_pop($tmp_parts);
					$ext = strtolower($ext);

					if ($ext == 'php' && strpos($file, '%') !== false)
					{
						unlink($smarty->cache_dir .'/'. $file);
					}
				}
			}
			closedir($dir);
		}
	}

	// moderator permissions
	$perms = '';
	// mod_can_manage_users
	$perms .= 'manage_users:';
	$perms .= ($_POST['mod_can_manage_users'] == "1") ? '1' : '0';
	$perms .= ';';
	// mod_can_manage_comments
	$perms .= 'manage_comments:';
	$perms .= ($_POST['mod_can_manage_comments'] == "1") ? '1' : '0';
	$perms .= ';';
	// mod_can_manage_videos
	$perms .= 'manage_videos:';
	$perms .= ($_POST['mod_can_manage_videos'] == "1") ? '1' : '0';
	$perms .= ';';
	$perms .= 'manage_articles:';
	$perms .= ($_POST['mod_can_manage_articles'] == "1") ? '1' : '0';
	$perms .= ';';

	if($info_msg == '')
	{
		update_config('moderator_can', $perms, true);

		if ($inputs['allow_user_uploadvideo_unit'] == 'GB')
		{
			$inputs['allow_user_uploadvideo_bytes'] = (int)$inputs['allow_user_uploadvideo_bytes'] .'G';
		}
		else if ($inputs['allow_user_uploadvideo_unit'] == 'MB')
		{
			$inputs['allow_user_uploadvideo_bytes'] = (int)$inputs['allow_user_uploadvideo_bytes'] .'M';
		}
		else if ($inputs['allow_user_uploadvideo_unit'] == 'KB')
		{
			$inputs['allow_user_uploadvideo_bytes'] = (int)$inputs['allow_user_uploadvideo_bytes'] .'K';
		}
		$inputs['allow_user_uploadvideo_bytes'] = return_bytes($inputs['allow_user_uploadvideo_bytes']);

		$upload_max_filesize = return_bytes(ini_get('upload_max_filesize'));
		$post_max_size = return_bytes(ini_get('post_max_size'));

		if (_MOD_SOCIAL)
		{
			$loggables = activity_load_options();

			foreach ($loggables as $activity => $v)
			{
				if (array_key_exists('loggable_activity_'.$activity, $inputs))
				{
					$loggables[$activity] = 1;
					unset($inputs['loggable_activity_'. $activity]);
				}
				else
				{
					$loggables[$activity] = 0;
				}
			}
			update_config('activity_options', serialize($loggables), true);

			unset($loggables);
		}
		$inputs['player_timecolor'] = str_replace('#', '', $inputs['player_timecolor']);
		$inputs['player_bgcolor'] = str_replace('#', '', $inputs['player_bgcolor']);

		foreach ($inputs as $config_name => $config_value)
		{
			if ($config_name != 'submit' && $config_name != 'allow_user_uploadvideo_unit')
			{
				update_config($config_name, $config_value, true);
			}
		}

		if((int) readable_filesize($config['allow_user_uploadvideo_bytes']) != $inputs['allow_user_uploadvideo_bytes']) {


			if ($inputs['allow_user_uploadvideo_bytes'] > $upload_max_filesize || $inputs['allow_user_uploadvideo_bytes'] > $post_max_size)
			{
				//$info_msg = 'It appears that your <strong>Max. upload size</strong> (Under "User Settings") is greater than your <a href="system-info.php">PHP configuration</a> allows.<strong>Contact your hosting provider and ask them to increase "<em>upload_max_filesize</em>" and "<em>post_max_size</em>" to match your requirements.</strong>';

				// change back to old value
				$inputs['allow_user_uploadvideo_bytes'] = $config['allow_user_uploadvideo_bytes'];
			}
		}

		$player_config = "{embedded: true,
							showOnLoadBegin: true,
							useHwScaling: false,
							menuItems: [false, false, true, true, true, false, false],
							timeDisplayFontColor: '0x". $inputs['player_timecolor'] ."',
							controlBarBackgroundColor: '0x". $inputs['player_bgcolor'] ."',
							progressBarColor2: '0x000000',
							progressBarColor1: '0xFFFFFF',
							controlsOverVideo: 'locked',
							controlBarGloss: 'high',
							initialScale: 'fit',
							hideControls: false,
							autoPlay: false,
							autoBuffering: true,
							watermarkLinkUrl: '". $inputs['player_watermarklink'] ."',
							showWatermark: '". $inputs['player_watermarkshow'] ."',
							watermarkUrl: '". make_url_https($inputs['player_watermarkurl'])  ."',
							playList: [ { overlayId: 'play',
									  name: 'ClickToPlay'
									 },
									 {  linkWindow: '_blank',
										linkUrl: '". _URL ."/watch.php?vid=___UNIQ___',
										url: '". _URL ."/videos.php?vid=___UNIQ___',
										name: ''
									 }]}";

		$player_config = rawurlencode($player_config);
		$player_config = _URL .'/players/flowplayer2/flowplayer.swf?config='. $player_config;

		@chmod(ABSPATH .''. _ADMIN_FOLDER .'/temp/embedparams.xml', 0755);
		if (is_writable(ABSPATH .''. _ADMIN_FOLDER .'/temp/embedparams.xml'))
		{
			$fp = fopen('./temp/embedparams.xml', 'w');
			fwrite($fp, $player_config, strlen($player_config));
			fclose($fp);
		}
		else
		{
			$info_msg .= pm_alert_warning('File "/'. _ADMIN_FOLDER .'/temp/embedparams.xml" is not writable. Please CHMOD this file to 0777 and retry.');
		}
		
		if ($config['video_player'] == 'jwplayer' || $_POST['video_player'] == 'jwplayer')
		{
			//@chmod(ABSPATH .'jwembed.xml', 0755);
			@chmod(ABSPATH .'players/jwplayer5/jwembed.xml', 0755); // @since v2.2
			if (file_exists(ABSPATH .'players/jwplayer5/jwembed.xml') && is_writable(ABSPATH .'players/jwplayer5/jwembed.xml'))
			{
				$write_this = '';
				$write_this .= "<config>\n";
				$write_this .= " <backcolor>". $inputs['player_bgcolor'] ."</backcolor>\n";
				$write_this .= " <frontcolor>". $inputs['player_timecolor'] ."</frontcolor>\n";
				$write_this .= " <screencolor>000000</screencolor>\n";
				$write_this .= " <controlbar>over</controlbar>\n";
				$write_this .= " <bufferlength>5</bufferlength>\n";
				$write_this .= " <autostart>false</autostart>\n";
				$write_this .= " <logo>". make_url_https($inputs['player_watermarkurl']) ."</logo>\n";
				$write_this .= " <link>". $inputs['player_watermarklink'] ."</link>\n";
				$write_this .= '</config>';

				$fp = fopen(ABSPATH .'players/jwplayer5/jwembed.xml', 'w');
				fwrite($fp, $write_this, strlen($write_this));
				fclose($fp);
			}
			else
			{
				$info_msg .= pm_alert_warning('File "/players/jwplayer5/jwembed.xml" is not writable. Please CHMOD this file to 0777 and retry.');
			}
		}
	}

	//	Update video sources too.
	foreach ($_POST['user_choice'] as $source_id => $user_choice)
	{
		if ($user_choice != $video_sources[$source_id]['user_choice'])
		{
			$sql = "UPDATE pm_sources
					SET user_choice = '". $user_choice ."'
					WHERE source_id = '". $source_id ."'";
			mysql_query($sql);
		}
	}

	// refresh display data
	$video_sources = a_fetch_video_sources();

	if ($inputs['spambot_prevention'] == 'recaptcha' && (empty($inputs['recaptcha_public_key']) || empty($inputs['recaptcha_private_key'])))
	{
		$info_msg .= pm_alert_warning('reCAPTCHA requires both a public and a private key. You can get them for free by signing up at <a href="http://www.google.com/recaptcha/intro/index.html" target="_blank">http://www.google.com/recaptcha/intro/index.html</a>.');
	}
	
	$allowed_zones = timezone_identifiers_list();
	if ($inputs['timezone'] != '' && ! in_array( $inputs['timezone'], $allowed_zones ))
	{
		$info_msg .= pm_alert_warning('The timezone you have entered is not valid. Please select a valid timezone.');
	}
	else
	{
		date_default_timezone_set($inputs['timezone']);
	}

	//	Update HTML COUNTER / Analytics
	if (!empty($_POST['htmlcode']))
	{
		$htmlcode = (get_magic_quotes_gpc()) ? stripslashes($_POST['htmlcode']) : $_POST['htmlcode'];
		
		$result = update_config('counterhtml', $htmlcode);// update_config does secure_sql()
		$current_counter = stripslashes($htmlcode);
		$config['counterhtml'] = $current_counter;
	} else {
		$result = update_config('counterhtml', $htmlcode);// update_config does secure_sql()
		$config['counterhtml'] = $htmlcode;
	}
	$config['mail_pass'] = stripslashes($config['mail_pass']);
}

$mod_can = mod_can();

$selected_tab_view = '';
$page_tab_views = array('tabname1', 't1', 't2', 't3', 't4', 't5', 't6', 't7', 't8', 't9', 't10',
						'general', 'modules', 'player', 'video', 'sources', 'video-ads', 'comment', 'email', 'user');
if ($_POST['settings_selected_tab'] != '' || $_GET['view'] != '')
{
	$selected_tab_view = ($_POST['settings_selected_tab'] != '') ? $_POST['settings_selected_tab'] : $_GET['view'];
	if ( ! in_array($selected_tab_view, $page_tab_views)) 
	{
		$selected_tab_view = '';
	}
}

$highlight_fields = array();	// @todo
if ($_GET['highlight'] != '')
{
	$highlight_fields = explode(',', $_GET['highlight']);
}

?>
<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper page-header-edit"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><a href="<?php echo _URL; ?>" class="open-in-new" target="_blank" data-popup="tooltip" data-placement="right" data-original-title="Switch to Front-End"><span class="font-weight-semibold">Update Settings</span> <i class="mi-open-in-new"></i></a></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<button type="submit" name="submit" value="Save" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2" onclick="document.forms[0].submit({return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted)')});" form="sitesettings"><i class="mi-check"></i> Save changes</button>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline d-none">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="settings.php" class="breadcrumb-item active">Settings</a>
				</div>
			</div>
		</div>
	</div><!-- /page header -->
</div><!--.page-header-wrapper-->	

	<!-- Content area -->
	<div class="content content-full-width">
		<div class="d-sm-block d-md-none d-lg-none d-xlg-none">
			<a href="theme-settings.php" class="btn btn-sm btn-link btn-block my-2"><i class="mi-format-paint"></i> Go to Layout Settings</a>
		</div>

	<div id="settings-jump"></div>
	<div class="d-horizontal-scroll">
		<nav id="import-nav" class="tabbable d-sm-block d-md-none d-lg-none d-xlg-none" role="navigation">
			<ul class="nav nav-md nav-pills nav-pills-bottom bg-white rounded justify-content">
				<li class="nav-item <?php echo ($selected_tab_view == 'tabname1' || $selected_tab_view == 't1' || $selected_tab_view == '' || $selected_tab_view == 'general') ? 'active' : '';?>"><a href="#tabname1" data-toggle="tab" class="nav-link active">General Settings</a></li>
				<li class="nav-item <?php echo ($selected_tab_view == 't6' || $selected_tab_view == 'modules') ? 'active' : '';?>"><a href="#t6" data-toggle="tab" class="nav-link<?php echo ($selected_tab_view == 't6' || $selected_tab_view == 'modules') ? ' active' : '';?>">Modules</a></li>
				<li class="nav-item <?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'player') ? 'active' : '';?>"><a data-toggle="tab" href="#t2" class="nav-link<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'player') ? ' active' : '';?>">Video Player</a></li>
				<li class="nav-item <?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'video') ? 'active' : '';?>"><a data-toggle="tab" href="#t3" class="nav-link<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'video') ? ' active' : '';?>">Video Settings</a></li>
				<li class="nav-item <?php echo ($selected_tab_view == 't5' || $selected_tab_view == 'video-ads') ? 'active' : '';?>"><a data-toggle="tab" href="#t5" class="nav-link<?php echo ($selected_tab_view == 't5' || $selected_tab_view == 'video-ads') ? ' active' : '';?>">Video Ads</a></li>
				<li class="nav-item <?php echo ($selected_tab_view == 't10' || $selected_tab_view == 'comment') ? 'active' : '';?>"><a data-toggle="tab" href="#t10" class="nav-link<?php echo ($selected_tab_view == 't10' || $selected_tab_view == 'comment') ? ' active' : '';?>">Comments</a></li>
				<li class="nav-item <?php echo ($selected_tab_view == 't9' || $selected_tab_view == 'user') ? 'active' : '';?>"><a data-toggle="tab" href="#t9" class="nav-link<?php echo ($selected_tab_view == 't9' || $selected_tab_view == 'user') ? ' active' : '';?>">Users</a></li>
				<li class="nav-item <?php echo ($selected_tab_view == 't7' || $selected_tab_view == 'email') ? 'active' : '';?>"><a data-toggle="tab" href="#t7" class="nav-link<?php echo ($selected_tab_view == 't7' || $selected_tab_view == 'email') ? ' active' : '';?>">E-mail</a></li>
			</ul>
		</nav>
	</div>
	<div class="card card-blanche">
	<div class=""></div><!--.card-body-->


<form name="sitesettings" id="sitesettings" method="post" action="settings.php">
<?php echo csrfguard_form('_admin_settings'); ?>

<?php if ($info_msg != '') : ?>
	<br />
	<?php echo $info_msg; ?>
<?php endif; ?>

<?php if ($_POST['submit'] == "Save" && $info_msg == '') : ?>
	<br />
	<?php echo pm_alert_success('The new settings have been saved and applied.'); ?>
<?php endif; ?>

<?php if ($config['mod_article'] == '0' && $config['mod_social'] == '0' && $config['firstinstall'] > ($time_now - 259200)) : // display info message only in the first 3 days ?>
	<br />
	<?php echo pm_alert_info('The "Article Module" and "Social Module" are disabled by default. To enable any available modules, see <a href="settings.php?view=modules"><strong>Modules</strong>.</a>'); ?>
<?php endif; ?>

<div>




	<div class="tab-content">
	<div class="tab-pane fade<?php echo ($selected_tab_view == 'tabname1' || $selected_tab_view == 't1' || $selected_tab_view == '' || $selected_tab_view == 'general') ? ' show active' : '';?>" id="tabname1">

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
		<tr class="border-top-1 border-bottom-0 bg-transparent">
			<td colspan="2">
				<h5 class="p-0 m-0 text-dark font-weight-semibold">General Settings</h5>
			</td>
		</tr>
		<tr>
			<td class="w-30">Site title</td>
			<td>
				<input name="homepage_title" type="text" class="form-control" value="<?php echo htmlspecialchars(stripslashes($config['homepage_title'])); ?>" />
			</td>
		</tr>
	  <tr>
		<td>Default language</td>
		<td>
			<select class="custom-select w-auto rounded" name="default_lang">
			<?php
			 foreach($langs as $lang_id => $lang_arr)
			 {
				if($lang_id == $config['default_lang'])
				{
					echo '<option value="'.$lang_id.'" selected="selected">'.$lang_arr['title'].'</option>';
				}
				else
				{
					echo '<option value="'.$lang_id.'">'.$lang_arr['title'].'</option>';
				}
			 }
			?>
			</select>
		  </td>
		</tr>
	  <tr>
		<td>Default timezone</td>
		<td>
			<select class="custom-select w-auto rounded" name="timezone">
				<?php  echo pm_timezone_select($config['timezone']); ?>
			</select>
			<br />
			<span class="text-muted">Server time: <?php echo date('Y-m-d H:i:s'); ?></span>
		  </td>
	  </tr>
	  <tr>
		<td>Use SEO friendly URLs</td>
		<td>
		<div class="form-check form-check-inline">
			<div class="form-check form-check-inline"><label><input name="seomod" type="radio" value="1" <?php echo ($config['seomod']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
		</div>
		<div class="form-check form-check-inline">
			<div class="form-check form-check-inline"><label><input name="seomod" type="radio" value="0" <?php echo ($config['seomod']==0) ? 'checked="checked"' : '';?> /> No</label></div>
		</div>
		<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Your server must support <strong>mod_rewrite</strong> commands. Once enabled, all the URLs will transform from a dynamic appearance to a static one. This may improve the search engine rankings. <br><br><strong>Warning:</strong> don't update this setting once your website has been indexed into the search engines."><i class="mi-info-outline"></i></a>
		</td>
	  </tr>
	  <tr>
		<td>Discourage search engines from indexing this site</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="disable_indexing" type="radio" value="1" <?php echo ($config['disable_indexing']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="disable_indexing" type="radio" value="0" <?php echo ($config['disable_indexing']==0) ? 'checked="checked"' : '';?> /> No</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="It is up to search engines to honor this request."><i class="mi-info-outline"></i></a>
		</td>
	  </tr>

	  <tr>
		<td>Show video thumbnails from</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="thumb_from" type="radio" value="1" <?php echo ($config['thumb_from']==1) ? 'checked="checked"' : '';?> /> Remote</label></div>
			<div class="form-check form-check-inline"><label><input name="thumb_from" type="radio" value="2" <?php echo ($config['thumb_from']==2) ? 'checked="checked"' : '';?> /> Local</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="'<strong>Local</strong>' = thumbnails from imported videos <strong>are saved</strong> locally.<br><br>'<strong>Remote</strong>' = thumbnails from imported videos <strong>are NOT saved</strong> locally.<br><br> To avoid missing thumbnails, once this option is set to '<strong>Remote</strong>', <strong>it should not be changed again</strong> because videos imported while the 'Remote' option was active will not have local thumbnails. <br><br><strong>Recommended:</strong> Use the 'Local' option."><i class="mi-info-outline"></i></a>
		</td>
	</tr>
	  <tr> 
		<td>Use thumbnail size</td>
		<td>
		<div class="input-group">
			
			<select class="custom-select w-auto rounded" name="download_thumb_res">
				<option value="small" <?php echo ($config['download_thumb_res'] == 'small') ? 'selected="selected"' : ''; ?>>Small (120x90 pixels)</option>
				<option value="medium" <?php echo ($config['download_thumb_res'] == 'medium' || $config['download_thumb_res'] == '') ? 'selected="selected"' : ''; ?>>Medium (320x180 pixels)</option>
				<option value="large" <?php echo ($config['download_thumb_res'] == 'large') ? 'selected="selected"' : ''; ?>>Large (480x360 pixels)</option>
				<option value="extra-large" <?php echo ($config['download_thumb_res'] == 'extra-large') ? 'selected="selected"' : ''; ?>>Extra Large (640x480 pixels)</option>
				<option value="original" <?php echo ($config['download_thumb_res'] == 'original') ? 'selected="selected"' : ''; ?>>Maximum (1280x720 pixels)</option>
			</select>
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Assign the preferred thumbnail resolution/size for imported videos (Youtube, Dailymotion and Vimeo).<br /><strong>Note:</strong> If the 'Extra Large' thumbnail is not available, PHP Melody will use the next best thumbnail available. <br><br><strong>Default:</strong> 'Medium'"><i class="mi-info-outline"></i></a> 
				</span>	
		</div>
		</td>
	  </tr>
	  <tr>
		  <td>Maintenance mode</td>
		  <td>
			<div class="form-check form-check-inline <?php echo ($config['maintenance_mode']==1) ? 'text-danger font-weight-semibold' : '';?>"><label><input name="maintenance_mode" type="radio" value="1" <?php echo ($config['maintenance_mode']==1) ? 'checked="checked"' : '';?> /> Enabled</label></div>
			<div class="form-check form-check-inline"><label><input name="maintenance_mode" type="radio" value="0" <?php echo ($config['maintenance_mode']==0) ? 'checked="checked"' : '';?> /> Disabled</label></div> <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Put your site in 'Maintenance mode' if you want to perform updates or layout changes. Users will see a short 'Maintenance mode' message but you can define a custom message below. Once your site is ready to be made available again, simple check the 'Disabled' box.<br><strong>Note</strong>: All administrator and moderators will be able to browse the site when it is in 'Maintenance mode' (as usual)."><i class="mi-info-outline"></i></a>
		  </td>
	  </tr>
	  <tr>
		  <td>Maintenance mode message</td>
		  <td>
			<div class="input-group">
			<input name="maintenance_display_message" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['maintenance_display_message']; ?>" placeholder="We'll be back in 5" /> 
			<span class="input-group-text bg-transparent border-0">
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Define a custom message for your visitors while your site is in 'Maintenance mode'. If left empty a generic 'Maintenance mode' message will be provided instead."><i class="mi-info-outline"></i></a>
			</span>
			</div>
		  </td>
	  </tr>

	  <tr>
		  <td>EU cookie notification</td>
		  <td>
			<div class="form-check form-check-inline"><label><input name="eu_cookie_warning" type="radio" value="1" <?php echo ($config['eu_cookie_warning']==1) ? 'checked="checked"' : '';?> /> Enabled</label></div>
			<div class="form-check form-check-inline"><label><input name="eu_cookie_warning" type="radio" value="0" <?php echo ($config['eu_cookie_warning']==0) ? 'checked="checked"' : '';?> /> Disabled</label></div> <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="All websites owned in the EU or targeted towards EU citizens, are now expected to comply with the 'EU Cookie' law."><i class="mi-info-outline"></i></a>
		  </td>
	  </tr>

	<?php if ($config['eu_cookie_warning']==1) : ?>
	  <tr>
		  <td>EU cookie notification placement</td>
		  <td>
			<div class="form-check form-check-inline"><label><input name="eu_cookie_warning_position" type="radio" value="floating" <?php echo ($config['eu_cookie_warning_position']=='floating') ? 'checked="checked"' : '';?> /> Floating</label></div>
			<div class="form-check form-check-inline"><label><input name="eu_cookie_warning_position" type="radio" value="top" <?php echo ($config['eu_cookie_warning_position']=='top') ? 'checked="checked"' : '';?> /> Top</label></div> 
			<div class="form-check form-check-inline"><label><input name="eu_cookie_warning_position" type="radio" value="bottom" <?php echo ($config['eu_cookie_warning_position']=='bottom') ? 'checked="checked"' : '';?> /> Bottom</label></div>
		  </td>
	  </tr>
	<?php endif; ?>
	  <tr>
		  <td>CSRF form protection</td>
		  <td>
			<div class="form-check form-check-inline"><label><input name="csrfguard" type="radio" value="1" <?php echo ($config['csrfguard']==1) ? 'checked="checked"' : '';?> /> Enabled</label></div>
			<div class="form-check form-check-inline"><label><input name="csrfguard" type="radio" value="0" <?php echo ($config['csrfguard']==0) ? 'checked="checked"' : '';?> /> Disabled</label></div> <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Protects forms and URLs from several types of malicious attacks including cross-site request forgery (CSRF)."><i class="mi-info-outline"></i></a>
		  </td>
	  </tr>	
	</table>
	
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Admin Area Settings</h5>
		</td>
	</tr>

	  <tr>
		  <td class="w-30" valign="top">Keyboard Shortcuts</td>
		  <td>
			<div class="form-check form-check-inline"><label><input name="keyboard_shortcuts" type="radio" value="1" <?php echo ($config['keyboard_shortcuts']==1) ? 'checked="checked"' : '';?> /> Enabled</label></div> 
			<div class="form-check form-check-inline"><label><input name="keyboard_shortcuts" type="radio" value="0" <?php echo ($config['keyboard_shortcuts']==0) ? 'checked="checked"' : '';?> /> Disabled</label></div> <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Once enabled, press SHIFT+/ to see a list of the available keyboard shortcuts commands."><i class="mi-info-outline"></i></a>
		  </td>
	  </tr>
	</table>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Analytics/Tracking Code</h5>
		</td>
	</tr>
	  <tr>
		  <td class="w-30" valign="top">HTML code</td>
		  <td>
			 <textarea name="htmlcode" class="form-control" rows="3" cols="55"><?php echo $config['counterhtml']; ?></textarea>
			 <span class="text-muted">This tracking code is placed in the footer.</span>
		  </td>
	  </tr>
	</table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'player') ? ' show active' : '';?>" id="t2">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Video Player Settings</h5>
		</td>
	</tr>
	  <tr>
		<td>Default video player</td>
		<td>
			<select class="custom-select w-auto rounded" name="video_player" id="player_select">
				<option value="embed" <?php echo ($config['video_player']=='embed') ? 'selected="selected"' : '';?>>Remote Player (e.g. YouTube.com)</option>
				<!-- <option value="flvplayer" <?php echo ($config['video_player']=='flvplayer') ? 'selected="selected"' : '';?>>FlowPlayer</option> -->
				<option value="jwplayer" <?php echo ($config['video_player']=='jwplayer') ? 'selected="selected"' : '';?>>JW Player 5</option>
				<option value="jwplayer6" <?php echo ($config['video_player']=='jwplayer6') ? 'selected="selected"' : '';?>>JW Player 6</option>
				<option value="jwplayer7" <?php echo ($config['video_player']=='jwplayer7') ? 'selected="selected"' : '';?>>JW Player 7</option>
				<option value="jwplayer8" <?php echo ($config['video_player']=='jwplayer8') ? 'selected="selected"' : '';?>>JW Player 8</option>
				<option value="videojs" <?php echo ($config['video_player']=='videojs') ? 'selected="selected"' : '';?>>Video JS (recommended)</option>
			</select>
		</td>
	  </tr>

	  <tr id="show_jwplayer8" class="hide-player-opt" style="<?php echo ($config['video_player'] != 'jwplayer8') ? 'display:none;' : '';?>">
		<td class="w-30">JW Player 8 license key</td>
		<td>
			<div class="input-group">
				<input id="jwplayer8key" name="jwplayer8key" type="text" class="form-control col-md-4" size="8" value="<?php echo $config['jwplayer8key']; ?>" />
				<span class="input-group-text bg-transparent border-0">
				<?php if( ! $config['jwplayer8key']) : ?>
					<span class="badge badge-warning">Required</span>
				<?php endif; ?>
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="JW Player 8 requires a license key. Create your own account and key on www.jwplayer.com."><i class="mi-info-outline"></i></a>

				</span>

				<span class="input-group-text bg-transparent border-0 pt-0 pb-0">
					<a href="https://dashboard.jwplayer.com/" target="_blank">Get a key</a>
				</span>
			</div>
			<span class="badge badge-info w-auto">JW Player 8 can only play locally hosted media (No longer works for YouTube videos).</span>

		</td>
	  </tr>

	  <?php // if($config['video_player'] == 'jwplayer7') : ?>
	  <tr id="show_jwplayer7" class="hide-player-opt" style="<?php echo ($config['video_player'] != 'jwplayer7') ? 'display:none;' : '';?>">
		<td class="w-30 <?php echo ( ! $config['jwplayer7key']) ? 'text-danger' : '' ?>">JW Player 7 license key</td>
		<td>
			<div class="input-group">
				<input id="jwplayer7key" name="jwplayer7key" type="text" class="form-control col-md-4" size="8" value="<?php echo $config['jwplayer7key']; ?>" />
				<span class="input-group-text bg-transparent border-0">
				<?php if( ! $config['jwplayer7key']) : ?>
					<span class="badge badge-warning">Required</span>
				<?php endif; ?>
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="JW Player 7 requires a license key. Create your own account and key on www.jwplayer.com."><i class="mi-info-outline"></i></a>

				</span>

				<span class="input-group-text bg-transparent border-0 pt-0 pb-0">
					<a href="https://dashboard.jwplayer.com/" target="_blank">Get a key</a>
				</span>
			</div>
		</td>
	  </tr>
	  <?php //endif; ?>
	  <?php //if($config['video_player'] == 'jwplayer6') : ?>
	  <tr id="show_jwplayer6" class="hide-player-opt" style="<?php echo ($config['video_player'] != 'jwplayer6') ? 'display:none;' : '';?>">
		<td class="w-30 <?php echo ( ! $config['jwplayer6key']) ? 'text-danger' : '' ?>">JW Player 6 license key</td>
		<td>
			<div class="input-group">
				<input id="jwplayerkey" name="jwplayerkey" type="text" class="form-control col-md-4" size="8" value="<?php echo $config['jwplayerkey']; ?>" />
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="If you purchased the Pro, Premium or Ads edition of JW Player 6, unlock its features of the by inserting your JW Player license key. Otherwise, leave this field blank."><i class="mi-info-outline"></i></a>
				</span>
			</div>
			</td>
	  </tr>
	  <?php //endif; ?>
	  <?php if($config['video_player'] == 'jwplayer') : ?> 
	  <tr>
		<td class="w-30">JW Player 5 skin</td>
		<td>
			<div class="input-group">
				<select class="custom-select w-auto rounded" name="jwplayerskin">
					<option value="<?php echo $config['jwplayerskin']; ?>" selected="selected"><?php echo ucfirst(trim($config['jwplayerskin'], ".zip")); ?></option>
					<option></option>
					<?php echo dropdown_jwskins(); ?>
				</select>
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="JW Player skins come with their own color scheme which cannot be edited below."><i class="mi-info-outline"></i></a>
				</span>
			</div>

		</td>
		</tr>
	  <?php endif; ?>
	  <tr>
		<td>Progress bar background color</td>
		<td><input id="bg_bar" name="player_bgcolor" type="text" class="form-control col-md-2 rounded" value="#<?php echo $config['player_bgcolor'];?>" /></td>
	  </tr>
	  <tr>
		<td>Video text color</td>
		<td><input id="play_timer" name="player_timecolor" type="text" class="form-control col-md-2 rounded" size="8" value="#<?php echo $config['player_timecolor']; ?>"  /></td>
	  </tr>
	  <tr>
		<td>Default player size</td>
		<td><div class="input-group"><input type="text" name="player_w" size="4" maxlength="4" class="form-control col-md-2 rounded" value="<?php echo $config['player_w'];?>" /> <span class="input-group-text bg-transparent border-0">x</span> <input type="text" name="player_h" size="4" maxlength="4" class="form-control col-md-2 rounded" value="<?php echo $config['player_h'];?>" /> <span class="input-group-text bg-transparent border-0">px</span></div></td>
	  </tr>
	  <tr>
		<td>Hompage player size</td>
		<td><div class="input-group"><input type="text" name="player_w_index" size="4" maxlength="4" class="form-control col-md-2 rounded" value="<?php echo $config['player_w_index'];?>" /> <span class="input-group-text bg-transparent border-0">x</span> <input type="text" name="player_h_index" size="4" maxlength="4" class="form-control col-md-2 rounded" value="<?php echo $config['player_h_index'];?>" /> <span class="input-group-text bg-transparent border-0">px</span></div></td>
	  </tr>
	  <tr>
		<td>Embed player size</td>
		<td><div class="input-group"><input type="text" name="player_w_embed" size="4" maxlength="4" class="form-control col-md-2 rounded" value="<?php echo $config['player_w_embed'];?>" /> <span class="input-group-text bg-transparent border-0">x</span> <input type="text" name="player_h_embed" size="4" maxlength="4" class="form-control col-md-2 rounded" value="<?php echo $config['player_h_embed'];?>" /> <span class="input-group-text bg-transparent border-0">px</span></div></td>
	  </tr>
	  <tr>
		<td>Play videos in</td>
		<td><div class="form-check form-check-inline"><label><input name="use_hq_vids" type="radio" value="1" <?php echo ($config['use_hq_vids']==1) ? 'checked="checked"' : '';?> /> High Quality</label></div>
		<div class="form-check form-check-inline"><label><input name="use_hq_vids" type="radio" value="0" <?php echo ($config['use_hq_vids']==0) ? 'checked="checked"' : '';?> /> Low Quality</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="This feature applies selectively depending on the video source."><i class="mi-info-outline"></i></a>
		</td>
	  </tr>
	  <tr>
		<td class="w-30">Autoplay videos</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="player_autoplay" type="radio" value="1" <?php echo ($config['player_autoplay']==1) ? 'checked="checked"' : '';?> /> On</label></div>
		<div class="form-check form-check-inline"><label><input name="player_autoplay" type="radio" value="0" <?php echo ($config['player_autoplay']==0) ? 'checked="checked"' : '';?> /> Off</label></div>
		<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Recent Chrome versions will disable auto-play of videos as a security measure."><i class="mi-info-outline"></i></a>
		</td>
		</tr>
		<tr>
		<td class="w-30">Autoplay featured videos</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="featured_autoplay" type="radio" value="1" <?php echo ($config['featured_autoplay']==1) ? 'checked="checked"' : '';?> /> On</label></div>
		<div class="form-check form-check-inline"><label><input name="featured_autoplay" type="radio" value="0" <?php echo ($config['featured_autoplay']==0) ? 'checked="checked"' : '';?> /> Off</label></div>
		<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="This feature allows you to disable/enable autoplay for videos on your homepage separately"><i class="mi-info-outline"></i></a>
		</td>
		</tr>
	  <tr>
		<td>Video pre-buffering</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="player_autobuff" type="radio" value="1" <?php echo ($config['player_autobuff']==1) ? 'checked="checked"' : '';?> /> On</label></div>
		<div class="form-check form-check-inline"><label><input name="player_autobuff" type="radio" value="0" <?php echo ($config['player_autobuff']==0) ? 'checked="checked"' : '';?> /> Off</label></div>		</td>
		</tr>
	  <tr>
		<td>Use watermark</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="player_watermarkshow" type="radio" value="always" <?php echo ($config['player_watermarkshow']=="always") ? 'checked="checked"' : '';?> /> Always</label></div>
		<div class="form-check form-check-inline"><label><input name="player_watermarkshow" type="radio" value="fullscreen" <?php echo ($config['player_watermarkshow']=="fullscreen") ? 'checked="checked"' : '';?> />
		Only when fullscreen</label></div>
		<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Watermarks can only be shown in Flowplayer and JW Player (<em>paid version</em>). Watermarks cannot be applied to external players."><i class="mi-info-outline"></i></a>		</td>
	  </tr>
	  <tr>
		<td>Watermark image URL</td>
		<td><div class="input-group"><input name="player_watermarkurl" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['player_watermarkurl']; ?>" placeholder="http://" /> <span class="input-group-text bg-transparent border-0"><a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Insert the full URL to the image you want to use as a watermark (Image types supported: JPG, GIF, PNG). To disable the watermark please leave this field empty. <br> Note: this works for JW Player Commercial Edition or Flowplayer"><i class="mi-info-outline"></i></a></span></div></td>
	  </tr>
	  <tr>
		<td>Watermark link</td>
		<td>
			<div class="input-group">
			<input name="player_watermarklink" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['player_watermarklink']; ?>" placeholder="http://" /> 
			<span class="input-group-text bg-transparent border-0"><a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Clicking the watermark can take the visitor to a desired location. Please enter that location (Complete URL)."><i class="mi-info-outline"></i></a></span>
			</div>
		</td>
	  </tr>
	</table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'video') ? ' show active' : '';?>" id="t3">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Video Settings</h5>
		</td>
	</tr>
	  <tr>
		<td class="w-30">Allow video embedding (site-wide setting)</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="allow_embedding" type="radio" value="1" <?php echo ($config['allow_embedding'] == '1') ? 'checked="checked"' : '';?> /> Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="allow_embedding" type="radio" value="0" <?php echo ($config['allow_embedding'] == '0') ? 'checked="checked"' : '';?> /> No</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="This setting allows you to control video embedding site-wide. If enabled, anyone can embed your videos on 3rd party sites."><i class="mi-info-outline"></i></a>
		</td>
	  </tr>
	  <tr>
		<td class="w-30">Mark video as 'new' for the first</td>
		<td>
		<div class="input-group input-group-sm">
			<input name="isnew_days" type="text" class="form-control col-md-2" size="8" value="<?php echo $config['isnew_days']; ?>" /> 
			<span class="input-group-append">
				<span class="input-group-text border-left-0 bg-transparent">days</span>
			</span>
		</div>
		</td>
		</tr>
	  <tr>
		<td>Mark video as 'popular' after</td>
		<td>
		<div class="input-group input-group-sm">
			<input name="ispopular" type="text" size="8" class="form-control col-md-2" value="<?php echo $config['ispopular']; ?>" /> 
			<span class="input-group-append">
				<span class="input-group-text border-left-0 bg-transparent">views</span>
			</span>
		</div>
		</td>
		</tr>
		<tr>
		<td>Mark video as 'featured' after</td>
		<td>
		<div class="input-group input-group-sm">
			<input name="auto_feature" type="text" class="form-control col-md-2" size="8" value="<?php echo ($config['auto_feature'] != '') ? $config['auto_feature'] : 0; ?>" /> 
			<span class="input-group-append">
				<span class="input-group-text border-left-0 bg-transparent">views</span>
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Automatically mark a video as 'Featured' when reaching this number of views. Set to 0 (zero) to disable this feature."><i class="mi-info-outline"></i></a>
				</span>
			</span>
		</div>
		</td>
	</tr>
	</table>

	<?php if ( empty($config['youtube_api_key']) ) : ?>
		<div class="alert alert-warning alert-styled-left mt-3 ml-2 mr-2">
			<strong>Before importing videos from YouTube.com...</strong> 
			<p>To import videos from YouTube.com, an API key is required. <strong><a href="http://help.phpmelody.com/how-to-create-a-youtube-api-key/" target="_blank">Watch the video</a></strong> and see how to create your API key.</p>
			<p>Enable YouTube importing by adding your API key below.</p>
		</div>
	<?php endif; ?>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Video Importing API Keys</h5>
		</td>
	</tr>

		<tr>
			<td class="w-30 <?php  echo (empty($config['youtube_api_key'])) ? 'text-danger' : ''; ?>">
				<strong>YouTube.com</strong> API Key for Video Importing
			</td>
			<td>
			<div class="input-group">
				<input name="youtube_api_key" type="text" class="form-control col-md-6 rounded" value="<?php echo $config['youtube_api_key']; ?>" /> 
				<span class="input-group-text bg-transparent border-0">
					<a href="http://help.phpmelody.com/how-to-create-a-youtube-api-key/" target="_blank" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Click on this icon to learn how to create your own YouTube API key."><i class="mi-info-outline"></i></a>
				</span>
				<span class="input-group-text bg-transparent border-0 pt-0 pb-0">
					<a href="https://console.developers.google.com" target="_blank" class="btn btn-sm btn-link">Get key</a>
				</span>
			</div>
			</td>
		</tr>
		<tr>
			<td class="w-30 <?php  echo (empty($config['vimeo_api_token'])) ? 'text-danger' : ''; ?>">
				<strong>Vimeo.com</strong> API Access Token
			</td>
			<td>

			<div class="input-group">
				<input name="vimeo_api_token" type="text" class="form-control col-md-6 rounded" value="<?php echo $config['vimeo_api_token']; ?>" /> 
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="To use the Vimeo video importing features you need an API key from Vimeo.com. Please visit https://developer.vimeo.com/api for more details."><i class="mi-info-outline"></i></a>
				</span>
				<span class="input-group-text bg-transparent border-0 pt-0 pb-0">
					 <a href="https://developer.vimeo.com/api" target="_blank" class="btn btn-sm btn-link">Get key</a>
				</span>
			</div>
			</td>
		</tr>
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Video Sources Player</h5>
		</td>
	</tr>
	</table>

	<div style="position: relative; overflow: auto; height: 300px; width: 100%;">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<?php
	$video_sources = array_reverse($video_sources);
	$video_sources = array_sort($video_sources, 'source_name', SORT_ASC);
	foreach ($video_sources as $id => $source)
	{
		$disabled = 1;
		if (is_int($id))
		{
			if ($source['flv_player_support'] == 1 && $source['embed_player_support'] == 1)
			{
				$disabled = 0;
			}
		?>
		<tr>
			<td class="w-30">
				<?php
				if ($disabled)
				{
					echo ucfirst($source['source_name']);
				}
				else
				{
					echo '<strong>'. ucfirst($source['source_name']) .'</strong>';
				}
				?>
			</td>
			<td width="80%">
			<label>
			<input name="user_choice[<?php echo $source['source_id'];?>]" value="flvplayer" type="radio" <?php if($source['user_choice'] == 'flvplayer') echo 'checked="checked"'; if($disabled) echo 'disabled="true"'; ?> /> <span rel="tooltip" title="Choose this option if you want to use your existing default player (e.g. JW Player, Flowplayer, etc.).">Use my video player</span>
			</label>

			<label>
			<input name="user_choice[<?php echo $source['source_id'];?>]"  value="embed"  type="radio" <?php if($source['user_choice'] == 'embed') echo 'checked="checked"'; if($disabled) echo 'disabled="true"'; ?>  /> <span rel="tooltip" title="Choose this option if you want to use the <?php echo ucfirst($source['source_name']); ?> video player.">Use the <strong><?php echo ucfirst($source['source_name']); ?></strong> player</span>
			</label>
			</td>
			</td>
			</tr>
		<?php
		}
	}
	?>
	</table>
	</div>


	</div>
	<div class="tab-pane fade<?php echo ($selected_tab_view == 't10' || $selected_tab_view == 'comment') ? ' show active' : '';?>" id="t10">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Comment Settings</h5>
		</td>
	</tr>
	 <tr>
		<td class="w-30">Allow comments (site-wide setting)</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="comment_system" type="radio" value="on" <?php echo ($config['comment_system'] == 'on') ? 'checked="checked"' : '';?> /> Yes</label></div>
		<div class="form-check form-check-inline"><label><input name="comment_system" type="radio" value="off" <?php echo ($config['comment_system'] == 'off') ? 'checked="checked"' : '';?> /> No</label></div>
		<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="This setting allows you to turn the commenting system ON or OFF site-wide."><i class="mi-info-outline"></i></a>
	 </tr>
	 <tr>
		<td class="w-30">Enabled comment systems</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="comment_system_native" type="checkbox" value="1" <?php echo ($config['comment_system_native']) ? 'checked="checked"' : '';?> /> PHP Melody</label></div>
		<div class="form-check form-check-inline"><label><input name="comment_system_facebook" type="checkbox" value="1" <?php echo ($config['comment_system_facebook']) ? 'checked="checked"' : '';?> /> Facebook</label></div>
		<div class="form-check form-check-inline"><label><input name="comment_system_disqus" type="checkbox" value="1" <?php echo ($config['comment_system_disqus']) ? 'checked="checked"' : '';?> /> Disqus</label></div>
		<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Enable 3rd party comment services such as Disqus and Facebook Comments."><i class="mi-info-outline"></i></a>
	 </tr>
	 <tr>
		<td class="w-30">Primary comment system</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="comment_system_primary" type="radio" value="native" <?php echo ($config['comment_system_primary'] == 'native') ? 'checked="checked"' : '';?> /> PHP Melody</label></div>
		<div class="form-check form-check-inline"><label><input name="comment_system_primary" type="radio" value="facebook" <?php echo ($config['comment_system_primary'] == 'facebook') ? 'checked="checked"' : '';?> /> Facebook</label></div>
		<div class="form-check form-check-inline"><label><input name="comment_system_primary" type="radio" value="disqus" <?php echo ($config['comment_system_primary'] == 'disqus') ? 'checked="checked"' : '';?> /> Disqus</label></div>
		<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="The primary commenting system will always appear first on your website."><i class="mi-info-outline"></i></a>
	 </tr>
	 <tr>
		<td class="w-30">Allow emojis</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="allow_emojis" type="radio" value="1" <?php echo ($config['allow_emojis'] == 1) ? 'checked="checked"' : '';?> /> Yes</label></div>
		<div class="form-check form-check-inline"><label><input name="allow_emojis" type="radio" value="0" <?php echo ($config['allow_emojis'] == 0) ? 'checked="checked"' : '';?> /> No</label></div>
		<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="This setting allows you to turn the Emoji support ON or OFF site-wide."><i class="mi-info-outline"></i></a>
	 </tr>
	 <tr>
		<td>Comments per page</td>
		<td>
			<div class="input-group">
				<input name="comments_page" type="text" class="form-control col-md-2 rounded" value="<?php echo $config['comments_page']; ?>" /> 
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Limit the number of comments displayed per page."><i class="mi-info-outline"></i></a>
				</span>
			</div>
		</td>
	 </tr>
	 <tr class="comment-options-tr">
		<td class="w-30">Block bad comments</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="stopbadcomments" type="radio" value="1" <?php echo ($config['stopbadcomments']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="stopbadcomments" type="radio" value="0" <?php echo ($config['stopbadcomments']==0) ? 'checked="checked"' : '';?> /> No</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Filter out the bad comments by editing the 'Blacklist' of unallowed words. Comments containing those words won't be added to the database."><i class="mi-info-outline"></i></a>
			<span class="bg-transparent border-0 pt-0 pb-0 d-inline-block">
				<a href="blacklist.php" target="_blank" class="btn btn-sm btn-link">Manage</a>
			</span>
		</td>
	 </tr>
	 <tr class="comment-options-tr">
		<td valign="top">Allow comments from</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="guests_can_comment" type="radio" value="1" <?php echo ($config['guests_can_comment']==1) ? 'checked="checked"' : '';?> /> Anyone</label></div>
		<div class="form-check form-check-inline"><label><input name="guests_can_comment" type="radio" value="0" <?php echo ($config['guests_can_comment']==0) ? 'checked="checked"' : '';?> /> Registered users only</label></div>	</td>
	 </tr>
	 <tr class="comment-options-tr">
		<td valign="top">Comment moderation</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="comm_moderation_level" type="radio" value="0" <?php echo ($config['comm_moderation_level']==0) ? 'checked="checked"' : '';?> /> Disabled</label></div>
		<div class="form-check form-check-inline"><label><input name="comm_moderation_level" type="radio" value="1" <?php echo ($config['comm_moderation_level']==1) ? 'checked="checked"' : '';?> /> Moderate guest comments only</label></div>
		<div class="form-check form-check-inline"><label><input name="comm_moderation_level" type="radio" value="2" <?php echo ($config['comm_moderation_level']==2) ? 'checked="checked"' : '';?> /> Moderate all comments</label></div>	</td>
	 </tr>
	 <tr class="comment-options-tr">
		<td valign="top">Default sorting</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="comment_default_sort" type="radio" value="added" <?php echo ($config['comment_default_sort']=='added') ? 'checked="checked"' : '';?> /> Most recent first</label></div>
		<div class="form-check form-check-inline"><label><input name="comment_default_sort" type="radio" value="score" <?php echo ($config['comment_default_sort']=='score') ? 'checked="checked"' : '';?> /> Most liked first</label></div>
		</td>
	 </tr>
	 <tr class="comment-options-tr">
		<td valign="top">Mute comments after</td>
		<td>
			<div class="input-group">
				<input type="text" class="form-control col-md-2" size="8" name="comment_rating_hide_threshold" value="<?php echo $config['comment_rating_hide_threshold']; ?>" /> 
				<span class="input-group-append">
					<span class="input-group-text border-left-0 bg-transparent">dislikes</span>
				</span>
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Minimum number of dislikes to mute a comment."><i class="mi-info-outline"></i></a>
				</span>
			</div>
		</td>
	 </tr> 
	</table>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Disqus Comment Settings</h5>
		</td>
	</tr>
	 <tr class="disqus-comment-options-tr">
		<td valign="top" class="w-30">Disqus shortname</td>
		<td>
			<div class="input-group">
				<input type="text" class="form-control col-md-6 rounded" name="disqus_shortname"  value="<?php echo $config['disqus_shortname']; ?>" />
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="This is required to enable the Disqus comments on your website.<br />To find out your shortname, log in your Disqus account and go to Settings / General / Site Identity."><i class="mi-info-outline"></i></a>
				</span>
			</div>
		</td>
	 </tr>
	</table>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Facebook Comment Settings</h5>
		</td>
	</tr>
	 <tr class="fb-comment-options-tr">
		<td valign="top" class="w-30">Facebook comment sorting</td>
		<td>
			<select class="custom-select w-auto rounded" name="fb_comment_sorting">
				<option value="social" <?php echo ($config['fb_comment_sorting']=='social') ? 'selected="selected"' : '';?>>Social (default)</option>
				<option value="time" <?php echo ($config['fb_comment_sorting']=='time') ? 'selected="selected"' : '';?>>Oldest - Newest</option>
				<option value="reverse_time" <?php echo ($config['fb_comment_sorting']=='reverse_time') ? 'selected="selected"' : '';?>>Newest - Oldest</option>
			</select>
		</td>
		</td>
	 </tr>
	 <tr class="fb-comment-options-tr">
		<td valign="top">Facebook APP ID</td>
		<td>
			<div class="input-group">
				<input type="text" class="form-control col-md-6 rounded" name="fb_app_id" value="<?php echo $config['fb_app_id']; ?>" />
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Define your Facebook App ID to moderate comments right on your website."><i class="mi-info-outline"></i></a>
				</span>
			</div>
		</td>
		</td>
	 </tr>
	</table>
	</div>

   <div class="tab-pane fade<?php echo ($selected_tab_view == 't5' || $selected_tab_view == 'video-ads') ? ' show active' : '';?>" id="t5">
   <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Video Ads Settings</h5>
		</td>
	</tr>

	  <tr>
		<td class="w-30">Set <a href="player-video-ads.php">pre-roll video ads</a> recurrence</td>
		<td>
			<div class="input-group">
				<input name="videoads_delay" type="text" class="form-control col-md-2" size="8" class="form-control" value="<?php echo $config['videoads_delay']; ?>" />
				<span class="input-group-append">
					<select class="custom-select w-auto border-left-0" name="videoads_delay_timespan">
						<option value="seconds" <?php if($config['videoads_delay'] > 0) echo 'selected="selected"'; ?>>Seconds</option>
						<option value="minutes" <?php if($config['videoads_delay'] == 0) echo 'selected="selected"'; ?>>Minutes</option>
						<option value="hours">Hours</option>
					</select>
				</span>
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Sets the delay between two video ads. If you set the delay to 2 minutes, your visitors will see a video ad every 2 minutes.  Insert <strong>0 (zero)</strong> to disable the limit and show the ads each time a video is played."><i class="mi-info-outline"></i></a></td>
				</span>
			</div>
	  </tr>
	  <tr>
		<td class="w-30">Set <a href="player-static-ads.php">pre-roll static ads</a> recurrence</td>
		<td>
			<div class="input-group">
				<input name="preroll_ads_delay" type="text" class="form-control col-md-2" size="8" class="form-control" value="<?php echo $config['preroll_ads_delay']; ?>" />
				<span class="input-group-append">
					<select class="custom-select w-auto" name="preroll_ads_delay_timespan">
					<option value="seconds" <?php if($config['preroll_ads_delay'] > 0) echo 'selected="selected"'; ?>>Seconds</option>
					<option value="minutes" <?php if($config['preroll_ads_delay'] == 0) echo 'selected="selected"'; ?>>Minutes</option>
					<option value="hours">Hours</option>
					</select>
				</span>
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Sets the delay between two pre-roll static ads. If you set the delay to 2 minutes, your visitors will see a pre-roll static ad every 2 minutes. Insert <strong>0 (zero)</strong> to disable the limit and show the ads each time a video is played."><i class="mi-info-outline"></i></a></td>
				</span>
			</div>
	  </tr>
	</table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't6' || $selected_tab_view == 'modules') ? ' show active' : '';?>" id="t6">
	
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
		<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
			<td colspan="2">
				<h5 class="p-0 m-0 text-dark font-weight-semibold">Article Module Settings</h5>
			</td>
		</tr>
		<tr>
			<td class="w-30">Articles Module</td>
			<td>
				<div class="form-check form-check-inline"><label><input name="mod_article" type="radio" value="1" <?php echo ($config['mod_article']==1) ? 'checked="checked"' : '';?> /> Enabled</label></div>
				<div class="form-check form-check-inline"><label><input name="mod_article" type="radio" value="0" <?php echo ($config['mod_article']==0) ? 'checked="checked"' : '';?> /> Disabled</label></div> <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="This module allows you to publish news or articles on your site."><i class="mi-info-outline"></i></a>
			</td>
		</tr>
	</table>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
		<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
			<td colspan="2">
				<h5 class="p-0 m-0 text-dark font-weight-semibold">Series Module Settings</h5>
			</td>
		</tr>
		<tr>
			<td class="w-30">Series Module</td>
			<td>
				<div class="form-check form-check-inline"><label><input name="mod_series" type="radio" value="1" <?php echo ($config['mod_series']==1) ? 'checked="checked"' : '';?> /> Enabled</label></div>
				<div class="form-check form-check-inline"><label><input name="mod_series" type="radio" value="0" <?php echo ($config['mod_series']==0) ? 'checked="checked"' : '';?> /> Disabled</label></div> <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="This module allows you to organize videos as episodes within seasons."><i class="mi-info-outline"></i></a>
			</td>
		</tr>
		<tr class="mod-series-options <?php echo ( ! $config['mod_series']) ? 'hide' : ''; ?>">
			<td class="w-30">Series per browsing page</td>
			<td>
				<div class="input-group input-group-sm">
					<input name="series_per_page" type="text" size="8" class="form-control col-md-2 w-auto" value="<?php echo $config['series_per_page']; ?>" />
					<span class="input-group-append"><span class="input-group-text border-left-0 bg-transparent">series</span></span>
					<span class="input-group-text bg-transparent border-0">
						<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-html="true" data-placement="right" data-trigger="hover" data-content="This value must be greater than 0 (zero)."><i class="mi-info-outline"></i></a>
					</span>
				</div>
			</td>
		</tr>
		<tr class="mod-series-options <?php echo ( ! $config['mod_series']) ? 'hide' : ''; ?>">
			<td>Popular episodes widget: sort by</td>
			<td>
				<select name="top_episodes_sort" class="custom-select form-control w-auto">
				<option value="views" <?php if ($config['top_episodes_sort'] == "views") echo ' selected="selected" ';?>>Most viewed</option>
				<option value="chart" <?php if ($config['top_episodes_sort'] == "chart") echo ' selected="selected" ';?>>Most viewed (last <?php echo $config['chart_days'];?> days)</option>
				<option value="rating"<?php if ($config['top_episodes_sort'] == "rating") echo ' selected="selected" ';?>>Most liked</option>
				</select>
			</td>
		</tr>
	</table>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
		<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
			<td colspan="2">
				<h5 class="p-0 m-0 text-dark font-weight-semibold">Social Module Settings</h5>
			</td>
		</tr>
		<tr>
			<td class="w-30">Social Module</td>
			<td>
				<div class="form-check form-check-inline"><label><input name="mod_social" type="radio" value="1" <?php echo ($config['mod_social']==1) ? 'checked="checked"' : '';?> /> Enabled</label></div>
				<div class="form-check form-check-inline"><label><input name="mod_social" type="radio" value="0" <?php echo ($config['mod_social']==0) ? 'checked="checked"' : '';?> /> Disabled</label></div>
				 <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="This module allows your users to engage in social interactions on your site and increase engagement."><i class="mi-info-outline"></i></a>
			</td>
		</tr>
		<?php //if ($config['mod_social']) :
		if ( ! function_exists('activity_load_options'))
		{
			include_once(ABSPATH .'include/social_settings.php');
			include_once(ABSPATH .'include/social_functions.php');
		}
		?>
		<tr class="mod-social-options <?php echo ( ! $config['mod_social']) ? 'hide' : ''; ?>">
			<td>
				Following limit
			</td>
			<td>
			<div class="input-group">
				<input name="user_following_limit" type="text" class="form-control col-md-3 rounded" value="<?php echo $config['user_following_limit']; ?>" /> 
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Maximum number of users someone can follow."><i class="mi-info-outline"></i></a>
				</span>
			</div>
			</td>
		</tr>
		<tr class="mod-social-options <?php echo ( ! $config['mod_social']) ? 'hide' : ''; ?>">
			<td>Log the following user actions</td>
			<td>
				<?php


				$loggables = activity_load_options();
				foreach ($loggables as $activity => $value)
				{
					?>
					<div class="form-check form-check-inline"><label><input type="checkbox" name="loggable_activity_<?php echo $activity;?>" value="1" <?php echo ($value == 1) ? 'checked="checked"' : '';?> /> <?php echo $activity_labels[$activity];?></label></div>
					<br />

					<?php
				}
				?>
			</td>
		</tr>
		<?php //endif;?>
	</table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't7' || $selected_tab_view == 'email') ? ' show active' : '';?>" id="t7">
	<?php
	if( $config['mail_server'] == 'mail.domain.com' ) {
		echo '<div class="card-body">';
		echo pm_alert_danger( "<strong>" . _SITENAME . " cannot send any emails at this time because no email account appears to be set up.</strong>");
		echo pm_alert_info( "For optimal results, use your <em>local mail server</em> instead of 3rd party servers such as Gmail, Hotmail, etc.");
		echo '</div>';
	}
	?>
	<div id="mail_preset_warn"></div>
	<table id="mail_settings" cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">E-mail Settings</h5>
		</td>
	</tr>

	 <tr>
		<td>Choose from existing presets</td>
		<td>
			<select class="custom-select w-auto" id="mail_presets">
				<option id="none">- none -</option>
				<option id="gmail">Gmail</option>
				<option id="godaddy">GoDaddy</option>
				<option id="yahoo">Yahoo</option>
			</select>
		</td>
	 </tr>
	 <tr>
		<td>Mail server</td>
		<td>
			<div class="input-group">
				<input name="mail_server" id="mail_server" type="text" class="form-control col-md-4 rounded" size="25" value="<?php echo $config['mail_server']; ?>" /> 
				<span class="input-group-text bg-transparent border-0">Port</span>
				<input name="mail_port" id="mail_port" type="text" size="5" class="form-control col-md-2 rounded" value="<?php echo $config['mail_port']; ?>" /> 
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="The mail server port is most likely to be 110 but it can also to be: 25, 26, 465 (GMAIL) and 587 (Yahoo). Please ask your host if you're not sure about this."><i class="mi-info-outline"></i></a>
				</span>
			</div>
		</td>
	 </tr>
	 <tr>
		<td>Account login</td>
		<td>
			<div class="input-group">
				<input name="mail_user" id="mail_user" type="text" class="form-control col-md-4 rounded" size="25" value="<?php echo $config['mail_user']; ?>" /> 
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="The account login is most likely to be your email address. Please ask your host for details if you need to"><i class="mi-info-outline"></i></a>
				</span>
			</div>
		</td>
	 </tr>
	 <tr>
		<td>Account password</td>
		<td>
			<div class="input-group">
				<input name="mail_pass" id="mail_pass" type="password" class="form-control col-md-4 rounded" size="25" value="<?php echo str_replace('"', '&quot;', $config['mail_pass']); ?>" />
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Please avoid using quotation marks ( ' or &quot; ) in your password."><i class="icon-warning-sign"></i></a>
				</span>
			</div>
		</td>
	 </tr>
	 <tr>
		<td>Use SMTP protocol for mail</td>
		<td>
		<div class="form-check form-check-inline"><label><input name="issmtp" type="radio" value="1" <?php echo ($config['issmtp']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
		<div class="form-check form-check-inline"><label><input name="issmtp" type="radio" id="nosmtp" value="0" <?php echo ($config['issmtp']==0) ? 'checked="checked"' : '';?> /> No</label></div>		</td>
	 </tr>
	 <tr>
		<td class="w-30">Contact e-mail</td>
		<td>
			<div class="input-group">
				<input name="contact_mail" id="contact_mail" type="text" class="form-control col-md-4 rounded" value="<?php echo $config['contact_mail']; ?>" size="30" />
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Contact page submissions will be delivered to this address. We highly recommend this email is associated with the account above."><i class="mi-info-outline"></i></a>
				</span>
			</div>
		</td>
	 </tr>
	 <tr>
		<td>
		</td>
		<td>
		<button type="submit" name="test-email" value="Test this email account" class="btn btn-sm btn-success mb-1" id="test-email" />Test this email account</button>
		<div class="hide" id="loader"><img src="img/ico-loading.gif" width="16" height="16" border="0" /> <em>Please wait...</em></div>
		</td>
	 </tr>
	</table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't9' || $selected_tab_view == 'user') ? ' show active' : '';?>" id="t9">
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
		<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
			<td colspan="2">
				<h5 class="p-0 m-0 text-dark font-weight-semibold">General User Settings</h5>
			</td>
		</tr>
		 <tr>
			<td class="w-30">Allow users to upload videos</td>
			<td>
			<div class="form-check form-check-inline"><label><input name="allow_user_uploadvideo" type="radio" value="1" <?php echo ($config['allow_user_uploadvideo']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="allow_user_uploadvideo" type="radio" value="0" <?php echo ($config['allow_user_uploadvideo']==0) ? 'checked="checked"' : '';?> /> No</label></div>
			</td>
		 </tr>
		 <tr>
			<td class="w-30">Max. video uploads/user/day</td>
			<td>
				<input name="user_upload_daily_limit" type="text" class="form-control col-md-2" size="8" class="form-control" value="<?php echo (int) $config['user_upload_daily_limit']; ?>" />
			</td>
		 </tr>
		 <tr>
			<td class="w-30">Allow users to suggest videos</td>
			<td>
			<div class="form-check form-check-inline"><label><input name="allow_user_suggestvideo" type="radio" value="1" <?php echo ($config['allow_user_suggestvideo']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="allow_user_suggestvideo" type="radio" value="0" <?php echo ($config['allow_user_suggestvideo']==0) ? 'checked="checked"' : '';?> /> No</label></div>
			</td>
		 </tr>
		 <tr> 
			<td class="w-30">Allow users to edit their videos</td>
			<td>
			<div class="form-check form-check-inline"><label><input name="allow_user_edit_video" type="radio" value="1" <?php echo ($config['allow_user_edit_video']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="allow_user_edit_video" type="radio" value="0" <?php echo ($config['allow_user_edit_video']==0) ? 'checked="checked"' : '';?> /> No</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="When set to 'Yes', users can edit the title, description, category, duration, tags, thumbnail and source URL or uploaded media file."><i class="mi-info-outline"></i></a>
			</td>
		 </tr>
		 <tr> 
			<td class="w-30">Allow users to delete their videos</td>
			<td>
			<div class="form-check form-check-inline"><label><input name="allow_user_delete_video" type="radio" value="1" <?php echo ($config['allow_user_delete_video']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="allow_user_delete_video" type="radio" value="0" <?php echo ($config['allow_user_delete_video']==0) ? 'checked="checked"' : '';?> /> No</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Allow your users to delete their videos."><i class="mi-info-outline"></i></a>
			</td>
		 </tr>
		 <tr>
			<td class="w-30">Auto-approve videos submissions from all users</td>
			<td>
			<div class="form-check form-check-inline"><label><input name="auto_approve_suggested_videos" type="radio" value="1" <?php echo ($config['auto_approve_suggested_videos']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="auto_approve_suggested_videos" type="radio" value="0" <?php echo ($config['auto_approve_suggested_videos']==0) ? 'checked="checked"' : '';?> /> No</label></div>
			</td>
		 </tr>
		 <tr> 
			<td class="w-30">Auto-approve videos submissions from 'Verified' users</td>
			<td>
			<div class="form-check form-check-inline"><label><input name="auto_approve_suggested_videos_verified" type="radio" value="1" <?php echo ($config['auto_approve_suggested_videos_verified']==1) ? 'checked="checked"' : '';?> /> Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="auto_approve_suggested_videos_verified" type="radio" value="0" <?php echo ($config['auto_approve_suggested_videos_verified']==0) ? 'checked="checked"' : '';?> /> No</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Submissions from users marked as having a 'Verified channel' will be auto-approved automatically. <br /> This applies even if you have a manual approval process for the rest of your user base. <br /><strong>Requires 'Social Module'</strong>"><i class="mi-info-outline"></i></a> 
			</td>
		 </tr>
		 <tr>
			<td class="w-30">Max. upload size</td>
			<td>
			<div class="input-group">
				<input name="allow_user_uploadvideo_bytes" type="text" class="form-control col-md-2" size="8" class="form-control" value="<?php echo (float) readable_filesize($config['allow_user_uploadvideo_bytes']); ?>" />
				<span class="input-group-append">
					<?php
					$unit = readable_filesize($config['allow_user_uploadvideo_bytes']);
					$unit = explode(' ', $unit);
					$unit = trim($unit[1]);
					?>
					<select class="custom-select" name="allow_user_uploadvideo_unit">
						<option value="GB" <?php if ($unit == 'GB') echo 'selected="selected"'; ?>>GB</option>
						<option value="MB" <?php if ($unit == 'MB') echo 'selected="selected"'; ?>>MB</option>
						<option value="KB" <?php if ($unit == 'KB') echo 'selected="selected"'; ?>>KB</option>
					</select>
				</span>
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Set the maximum upload limit allowed (per video). Ask your hosting provider to increase the limit if it's too low."><i class="mi-info-outline"></i></a>
				</span>
			</div>
			<?php
			if((int) readable_filesize($config['allow_user_uploadvideo_bytes']) > (int)readable_filesize(get_true_max_filesize())) {
				echo '<span class="text text-warning mt-1"><small>The hosting provider has a limit of <strong>'.readable_filesize(get_true_max_filesize()).'</strong> per upload. Contact the hosting provider to increase this limit.</small></span>';
			}
			?>

			</td>
		 </tr>
		 <tr>
			<td>Allow users to Like/Dislike</td>
			<td>
				<div class="form-check form-check-inline"><label><input name="bin_rating_allow_anon_voting" type="radio" value="1" <?php echo ($config['bin_rating_allow_anon_voting'] == 1) ? 'checked="checked"' : '';?> /> Everyone</label></div>
				<div class="form-check form-check-inline"><label><input name="bin_rating_allow_anon_voting" type="radio" value="0" <?php echo ($config['bin_rating_allow_anon_voting']==0) ? 'checked="checked"' : '';?> /> Registered users only</label></div>
			</td>
		 </tr>
		 <tr>
			<td>Allow users to create playlists</td>
			<td>
				<div class="form-check form-check-inline"><label><input name="allow_playlists" type="radio" value="1" <?php echo ($config['allow_playlists'] == 1) ? 'checked="checked"' : '';?> /> Yes</label></div>
				<div class="form-check form-check-inline"><label><input name="allow_playlists" type="radio" value="0" <?php echo ($config['allow_playlists']==0) ? 'checked="checked"' : '';?> /> No</label></div>
			</td>
		 </tr>
		 <tr>
			<td>Max. playlists/user</td>
			<td>
				<input name="playlists_limit" type="text" class="form-control col-md-2" size="8" class="form-control" value="<?php echo (int) $config['playlists_limit']; ?>" />
			</td>
		 </tr>
		 <tr>
			<td>Max. videos/playlist</td>
			<td>
				<input name="playlists_items_limit" type="text" class="form-control col-md-2" size="8" class="form-control" value="<?php echo (int) $config['playlists_items_limit']; ?>" />
			</td>
		 </tr>
		</table>
		
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
		<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
			<td colspan="2">
				<h5 class="p-0 m-0 text-dark font-weight-semibold">Registration Settings</h5>
			</td>
		</tr>
		 <tr>
			<td class="w-30">Allow registration</td>
			<td>
			<div class="form-check form-check-inline"><label><input name="allow_registration" type="radio" value="1" <?php echo ($config['allow_registration']=='1') ? 'checked="checked"' : '';?> />
			Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="allow_registration" type="radio" value="0" <?php echo ($config['allow_registration']=='0') ? 'checked="checked"' : '';?> /> No</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Set to '<em>No</em>' to disable all public registrations. This will not disable the 'Login' procedure in the front-end. <br> Note: the default setting is '<strong>Yes</strong>'."><i class="mi-info-outline"></i></a>
			</td>
		 </tr>
		 <tr>
			<td class="w-30">Allow non-latin usernames</td>
			<td>
			<div class="form-check form-check-inline"><label><input name="allow_nonlatin_usernames" type="radio" value="1" <?php echo ($config['allow_nonlatin_usernames']=='1') ? 'checked="checked"' : '';?> />
			Yes</label></div>
			<div class="form-check form-check-inline"><label><input name="allow_nonlatin_usernames" type="radio" value="0" <?php echo ($config['allow_nonlatin_usernames']=='0') ? 'checked="checked"' : '';?> /> No</label></div>
			<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Set to '<em>Yes</em>' if you want to let your users register with usernames containing non-latin characters too.<br> Note: the default setting is '<strong>Yes</strong>'."><i class="mi-info-outline"></i></a>
			</td>
		 </tr>
		 <tr>
			<td class="w-30">New accounts activation method</td>
			<td>
				<div class="form-check form-check-inline"><label><input name="account_activation" type="radio" value="0" <?php echo ($config['account_activation']==0) ? 'checked="checked"' : '';?> /> None</label></div>
				<div class="form-check form-check-inline"><label><input name="account_activation" type="radio" value="1" <?php echo ($config['account_activation']==1) ? 'checked="checked"' : '';?> /> User e-mail</label></div>
				<div class="form-check form-check-inline"><label><input name="account_activation" type="radio" value="2" <?php echo ($config['account_activation']==2) ? 'checked="checked"' : '';?> /> Admin/Moderator</label></div>
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Ask new users to verify their email by clicking a link provided upon registration. The account will remain inactive until they verify their identity."><i class="mi-info-outline"></i></a>
			</td>
		 </tr>
		 <tr>
			<td>Form protection</td>
			<td>
				<label>
					<input name="spambot_prevention" type="radio" value="none" <?php echo ($config['spambot_prevention'] == 'none') ? 'checked="checked"' : '';?> /> None</lable>
				</label>
				<label>
					<input name="spambot_prevention" type="radio" value="securimage" <?php echo ($config['spambot_prevention'] == 'securimage') ? 'checked="checked"' : '';?> /> SecurImage</lable>
				</label>
				<label>
					<input name="spambot_prevention" type="radio" value="recaptcha" <?php echo ($config['spambot_prevention'] == 'recaptcha') ? 'checked="checked"' : '';?> id="" /> reCAPTCHA <span class="badge badge-success">Recommended</span></lable>
				</label>
			</td>
		 </tr>
	 <tr class="recaptcha_public_key_tr">
		<td class="w-30">reCAPTCHA public key (Site key)</td>
		<td>
			<input name="recaptcha_public_key" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['recaptcha_public_key'];?>" /> 
		</td>
	 </tr>
	 <tr class="recaptcha_private_key_tr">
		<td class="w-30">reCAPTCHA private key (Secret key)</td>
		<td>
			<div class="input-group">
				<input name="recaptcha_private_key" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['recaptcha_private_key'];?>" /> 
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="You need a reCAPTCHA/Google account to use reCAPTCHA on your site. Click '<strong>Get keys</strong>' to get started."><i class="mi-info-outline"></i></a>
				</span>
				<span class="input-group-text bg-transparent border-0 pt-0 pb-0">
					<a href="http://www.google.com/recaptcha/intro/index.html" target="_blank" class="btn btn-sm btn-link">Get keys</a>
				</span>
			</div>
		</td>
	 </tr>
		 <tr>
			<td class="w-30">Prevent new account creation in under</td>
			<td>
			<div class="input-group">
				<input name="register_time_to_submit" type="text" size="5" class="form-control col-md-2 rounded" value="<?php echo $config['register_time_to_submit'];?>" />
				<span class="input-group-text bg-transparent border-0">	seconds	</span>
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Prevent SPAM bots from creating accounts on your website. Define how many seconds should pass before the user can submit the registration form. Default value is 3 seconds."><i class="mi-info-outline"></i></a>
				</span>
			</div>			
			</td>
		 </tr>
		</table>
	
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Facebook Login</h5>
		</td>
	</tr>
	 <tr>
		<td class="w-30">Facebook Login</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="oauth_facebook" type="radio" value="1" <?php echo ($config['oauth_facebook'] == 1) ? 'checked="checked"' : '';?> /> Enabled</label></div>
			<div class="form-check form-check-inline"><label><input name="oauth_facebook" type="radio" value="0" <?php echo ($config['oauth_facebook'] == 0) ? 'checked="checked"' : '';?> /> Disabled</label></div>
		</td>
	 </tr>
	 <tr>
		<td class="w-30">Facebook App ID</td>
		<td>
			<div class="input-group">
				<input name="oauth_fb_app_id" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['oauth_fb_app_id'];?>" />
				<span class="input-group-text bg-transparent border-0">
				<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="To enable this kind of registration/login you need to create an App within Facebook. Click '<strong>Get help</strong>' to get started."><i class="mi-info-outline"></i></a>
				</span>
				<span class="input-group-text bg-transparent border-0 pt-0 pb-0">
					<a href="http://help.phpmelody.com/how-to-setup-facebook-login/" target="_blank" class="btn btn-sm btn-link">Get help</a>
				</span>
			</div>
		</td>
	 </tr>
	 <tr>
		<td class="w-30">Facebook App Secret</td>
		<td>
			<input name="oauth_fb_app_secret" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['oauth_fb_app_secret'];?>" />
		</td>		
	 </tr>

<!-- 	 <tr>
		<td>
		</td>
		<td>
			<button type="submit" name="test-fb-app" value="Test this facebook app" class="btn btn-sm btn-success mb-1" id="test-fb-app" />Check App Status</button>
			<div class="hide" id="fb-loader"><img src="img/ico-loading.gif" width="16" height="16" border="0" /> <em>Please wait...</em></div>
		</td>
	 </tr> -->

	</table>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Google Login</h5>
		</td>
	</tr>
	 <tr>
		<td class="w-30">Google Login</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="oauth_google" type="radio" value="1" <?php echo ($config['oauth_google'] == 1) ? 'checked="checked"' : '';?> /> Enabled</label></div>
			<div class="form-check form-check-inline"><label><input name="oauth_google" type="radio" value="0" <?php echo ($config['oauth_google'] == 0) ? 'checked="checked"' : '';?> /> Disabled</label></div>
		</td>
	 </tr>
	 <tr>
		<td class="w-30">Client ID</td>
		<td>
			<div class="input-group">
				<input name="oauth_google_clientid" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['oauth_google_clientid'];?>" /> 
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="To enable this kind of registration/login you need to get a Client ID. Click '<strong>Get help</strong>' to get started."><i class="mi-info-outline"></i></a>
				</span>
				<span class="input-group-text bg-transparent border-0 pt-0 pb-0">
					<a href="http://help.phpmelody.com/how-to-setup-google-login/" target="_blank" class="btn btn-sm btn-link">Get help</a>
				</span>
			</div>
		</td>
	 </tr>
	</table>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Twitter Login</h5>
		</td>
	</tr>
	 <tr>
		<td class="w-30">Twitter Login</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="oauth_twitter" type="radio" value="1" <?php echo ($config['oauth_twitter'] == 1) ? 'checked="checked"' : '';?> /> Enabled</label></div>
			<div class="form-check form-check-inline"><label><input name="oauth_twitter" type="radio" value="0" <?php echo ($config['oauth_twitter'] == 0) ? 'checked="checked"' : '';?> /> Disabled</label></div>
		</td>
	 </tr>
	 <tr>
		<td class="w-30">Consumer Key</td>
		<td>
			<div class="input-group">
				<input name="oauth_twitter_consumer_key" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['oauth_twitter_consumer_key'];?>" /> 
				<span class="input-group-text bg-transparent border-0">
					<a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="To enable this kind of registration/login you need to create an App within Twitter. Click '<strong>Get help</strong>' to get started."><i class="mi-info-outline"></i></a>
				</span>
				<span class="input-group-text bg-transparent border-0 pt-0 pb-0">
					<a href="http://help.phpmelody.com/how-to-setup-twitter-login/" target="_blank" class="btn btn-sm btn-link">Get help</a>
				</span>
			</div>
		</td>
	 </tr>
	 <tr>
		<td class="w-30">Consumer Secret</td>
		<td>
			<input name="oauth_twitter_consumer_secret" type="text" class="form-control col-md-8 rounded" value="<?php echo $config['oauth_twitter_consumer_secret'];?>" /> 
		</td>
	 </tr>
	</table>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-columned pm-tables pm-tables-settings">
	<tr class="border-top-1 border-bottom-0 font-weight-bold bg-transparent">
		<td colspan="2">
			<h5 class="p-0 m-0 text-dark font-weight-semibold">Moderators Access</h5>
		</td>
	</tr>
	 <tr>
		<td class="w-30">Videos</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="mod_can_manage_videos" type="checkbox" value="1" <?php if ($mod_can['manage_videos']) echo 'checked="checked"'; ?> /> Allow access <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Moderators will be able to <strong>add</strong>, <strong>embed</strong>, <strong>import</strong>, <strong>edit</strong>, <strong>delete</strong>, <strong>approve</strong> and <strong>manage reported videos</strong>"><i class="mi-info-outline"></i></a>
			</label></div>
		</td>
	 </tr>
	 <tr>
		<td>Comments</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="mod_can_manage_comments" type="checkbox" value="1" <?php if ($mod_can['manage_comments']) echo 'checked="checked"'; ?> /> Allow access <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Moderators will be able to <strong>approve</strong>, <strong>edit</strong> and <strong>delete</strong> comments"><i class="mi-info-outline"></i></a>
			</label></div>
		</td>
	 </tr>
	 <tr>
		<td>Manage users</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="mod_can_manage_users" type="checkbox" value="1" <?php if ($mod_can['manage_users']) echo 'checked="checked"'; ?> /> Allow access <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Moderators will be able to <strong>activate new accounts</strong>, <strong>ban</strong> and <strong>unban</strong> other users"><i class="mi-info-outline"></i></a>
			</label></div>
		</td>
	 </tr>
	 <tr>
		<td>Manage articles</td>
		<td>
			<div class="form-check form-check-inline"><label><input name="mod_can_manage_articles" type="checkbox" value="1" <?php if ($mod_can['manage_articles']) echo 'checked="checked"'; ?> /> Allow access <a href="#" class="text-grey-300 alpha-grey" data-popup="popover" data-placement="right" data-trigger="hover" data-html="true" data-content="Moderators will be able to <strong>add</strong>, <strong>edit</strong> and <strong>delete</strong> articles. <strong>Please note</strong> that there is a special user rank for managing only the articles: the <strong>Editor</strong> rank."><i class="mi-info-outline"></i></a>
			</label></div>
		</td>
	 </tr>
	</table>
	</div>
	</div>


</div>

<div class="datatable-footer">
	<div id="stack-controls-disabled" class="row list-controls">
		<div class="col-md-12">		
			<input name="views_from" type="hidden" value="2"  />
			<input type="hidden" name="settings_selected_tab" value="<?php echo ($selected_tab_view != '') ? $selected_tab_view:  't1';?>" />
			<div class="float-right">
				<div class="btn-group">
					<button type="submit" name="submit" value="Save" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-2"><i class="mi-check"></i> Save changes</button>
				</div>
			</div>
		</div>
	</div><!-- #list-controls -->
</div>
</form>

</div><!--.card-->
</div><!-- .content -->


<script type="text/javascript">
var up_to_date_check = true;
$(document).ready(function(){
	
	$('form[name="sitesettings"]').change(function(){
		phpmelody.prevent_leaving_without_saving = true;
	}).submit(function(){
		phpmelody.prevent_leaving_without_saving = false;
	});
  
  $('input[name="mod_series"]').click(function(){
	if ($(this).val() == '0') {
		$('.mod-series-options').hide();
	} else {
		$('.mod-series-options').fadeIn();
	}
  });

  $('input[name="mod_social"]').click(function(){
	if ($(this).val() == '0') {
		$('.mod-social-options').hide();
	} else {
		$('.mod-social-options').fadeIn();
	}
  });
  
  $('#mail_presets').change(function() {
	var $this = $(this).find('option:selected').attr('id');

	if($this == 'gmail') {
		$('#mail_settings').find('#mail_server').val('ssl://smtp.gmail.com');
		$('#mail_settings').find('#mail_port').val('465');
		$('#mail_settings').find('#mail_user').val('you@gmail.com');
		$('#mail_settings').find('#mail_pass').val('');
		$('#mail_settings').find('#contact_mail').val('you@gmail.com');
		//$('#mail_preset_warn').html('<div class="alert alert-info">GMAIL is an excellent choice if the following  conditions are met: <ol><li>Your site sends less than 500 emails per day</li><li>Your hosting provider allows outgoing SSL connections</li><li>Your GMAIL account is set to allow SMTP connections</li></ol></div>');
	}
	if($this == 'godaddy') {
		$('#mail_settings').find('#mail_server').val('relay-hosting.secureserver.net');
		$('#mail_settings').find('#mail_port').val('25');
		$('#mail_settings').find('#mail_user').val('username and password are not required');
		$('#mail_settings').find('#mail_pass').val('none');
		$('#mail_settings').find('#contact_mail').val('you@your-godaddy-account.com');
		$('#mail_settings').find('#nosmtp').attr('checked', 'checked');


		//$('#mail_preset_warn').html('<div class="alert alert-danger"><small>Using <strong>GoDaddy</strong>\'s server to send emails is a bit problematic. For example they don\'t permit email delivery to @aol.com, @gmail.com, @hotmail.com, @msn.com, or @yahoo.com addresses. That makes their service almost unusable from PHP scripts. We recommend using a different provider if possible.</small></div>').css('display','block');
	} 	if($this == 'yahoo') {
		$('#mail_settings').find('#mail_server').val('smtp.mail.yahoo.com');
		$('#mail_settings').find('#mail_port').val('587');
		$('#mail_settings').find('#mail_user').val('you@yahoo.com');
		$('#mail_settings').find('#mail_pass').val('');
		$('#mail_settings').find('#contact_mail').val('you@yahoo.com');
		$('#mail_preset_warn').css('display','none');
	} 	if($this == 'none') {
		$('#mail_settings').find('#mail_server').val('mail.yourdomain.com');
		$('#mail_settings').find('#mail_port').val('25');
		$('#mail_settings').find('#mail_user').val('user+yourdomain.com');
		$('#mail_settings').find('#mail_pass').val('');
		$('#mail_settings').find('#contact_mail').val('user@yourdomain.com');
		$('#mail_preset_warn').css('display','none');
	}
  });
});
</script>
<?php
include('footer.php');