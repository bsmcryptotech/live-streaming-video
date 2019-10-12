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
require('../config.php');
include_once(ABSPATH . 'include/user_functions.php');
include_once(ABSPATH . 'include/islogged.php');
include(ABSPATH .''. _ADMIN_FOLDER .'/functions.php');

if ( ! $logged_in || ( ! is_admin() && ! is_moderator() && ! is_editor()))
{
	header("Location: "._URL. "/". _ADMIN_FOLDER ."/login.php");
	exit();
}

if ($_GET['forcesync'] == '1' || $_GET['forcesync'] == 'true' 
	|| $_GET['force-sync'] == '1' || $_GET['force-sync'] == 'true'
	|| $_GET['sync'] == '1' || $_GET['sync'] == 'true'
) {
    autosync(true);
}

$mod_can = array();
$allowed_access = array('1');// dashboard

if (is_moderator())
{
	$mod_can = mod_can();
	
	if ($mod_can['manage_videos'])
	{
		$allowed_access[] = '2';
		$allowed_access[] = 'mod_series';
	}
	
	if ($mod_can['manage_comments'])
	{
		$allowed_access[] = '4';
		$allowed_access[] = '5';
	}
	
	if ($mod_can['manage_users'])
		$allowed_access[] = '6';
	
	if ($mod_can['manage_articles'])  
		$allowed_access[] = 'mod_article';
}

if (is_editor())
{
	$allowed_access[] = 'mod_article';
}

define('VS_UNCHECKED', 0);
define('VS_OK', 1);
define('VS_BROKEN', 2);
define('VS_RESTRICTED', 3);
define('VS_UNCHECKED_IMG', "vs_unchecked");
define('VS_OK_IMG', "vs_ok");
define('VS_BROKEN_IMG', "vs_broken");
define('VS_RESTRICTED_IMG', "vs_restricted");
define('VS_NOTAVAILABLE_IMG', "vs_na");

$upload_max_filesize = get_true_max_filesize();
get_admin_ui_prefs();

if ( ! empty($_REQUEST['results']))
{
	set_admin_ui_prefs($_REQUEST['ui_pref'], (int) $_REQUEST['results']);
}
if (in_array($_POST['data_source'], array('youtube', 'youtube-channel', 'dailymotion', 'vimeo')))
{
	set_admin_ui_prefs('import_from', $_POST['data_source']);
}

// Count important data
$vapprv = count_entries('pm_temp', '', '');
$crps = count_entries('pm_reports', 'r_type', '1');
$tab_video_total = $vapprv + $crps;
$capprv = count_entries('pm_comments', 'approved', '0');
$flagged_comments = count_entries('pm_comments', '1', '1\' AND report_count > \'0');
$pending_comments = count_entries('pm_comments', '1', '1\' AND approved = \'0');
$tab_comments = $capprv + $flagged_comments;
$tab_internallog = (int) $config['unread_system_messages'];

$sitemap_options = @unserialize(stripslashes($config['video_sitemap_options']));
if (is_array($sitemap_options))
{
	$time_now = time();
	
	if ($time_now > ($sitemap_options['sitemap_last_build'] + (86400 * 14)) && $sitemap_options['sitemap_last_build'] > 0 && $config['published_videos'] > $sitemap_options['total_videos'])
	{
		$tab_regular_sitemap = 1; // This means it is too old.
	}
	
	if ($time_now > ($sitemap_options['video-sitemap_last_build'] + (86400 * 14)) && $sitemap_options['video-sitemap_last_build'] > 0 && $config['published_videos'] > $sitemap_options['total_videos'])
	{
		$tab_video_sitemap = 1; // This means it is too old.
	}
}
?>
<!DOCTYPE html>
<!--[if IE 7 | IE 8 | IE 9]>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html dir="ltr" lang="en">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=edge,chrome=1">
<title><?php echo ($_page_title != '') ? $_page_title .' - '. $config['homepage_title'] : $config['homepage_title'] .' - Admin Area'; ?></title>

<link rel="shortcut icon" type="image/ico" href="img/favicon.ico" />
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!--[if IE]><link rel="stylesheet" type="text/css" href="css/admin-ie.css"/><![endif]-->

<?php if($load_ibutton == 1): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.ibutton.css" />
<?php endif; ?>
<?php if($load_chzn_drop == 1): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/chosen.css" />
<?php endif; ?>
<?php if($load_colorpicker == 1): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-colorpicker.css" />
<?php endif; ?>

<link href="//fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900|Cuprum:400,700" rel="stylesheet" type="text/css">
<link href="css/icon-moon.css" rel="stylesheet" type="text/css">
<link href="css/icon-material.css" rel="stylesheet" type="text/css">
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="css/bootstrap-admin.min.css" rel="stylesheet" type="text/css">
<link href="css/admin-melody.css" rel="stylesheet" type="text/css">

<script src="js/jquery.min.js"></script>	
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/ripple.min.js"></script>
<!-- /core JS files -->

<!-- Theme JS files -->
<script src="js/bootstrap_multiselect.js"></script>
<script src="js/app.js"></script>

<script src="js/fileinput.min.js"></script>
<script src="js/bootstrap-uploader.js"></script>
<!-- /theme JS files -->

<?php if ($load_datepicker) : ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datepicker.min.css" />
<?php endif; ?>

<?php if ($load_flot) : ?>
<script type="text/javascript" src="js/jquery.flot.min.js"></script>
<script type="text/javascript" src="js/jquery.flot.resize.min.js"></script>
<?php endif; ?>

<?php if ($load_scrollpane == 1) : ?>
<script type="text/javascript" src="js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="js/jquery.jscrollpane.min.js"></script>
<?php endif; ?>

<?php if ($load_dotdotdot == 1) : ?>
<script type="text/javascript" src="js/jquery.dotdotdot.min.js"></script>
<?php endif; ?>

<script type="text/javascript">
/*
 * Global js vars
 */
var pm_prevent_leaving_without_saving = false;
var pm_doing_ajax = false;
var pm_URL = '<?php echo _URL; ?>';
var pm_URL_ADMIN = '<?php echo _URL .'/'. _ADMIN_FOLDER; ?>';

var pm_prettyPop_fp_bgcolor = '<?php echo '0x' . _BGCOLOR;?>';
var pm_prettyPop_fp_timecolor = '<?php echo '0x' . _TIMECOLOR;?>';
var pm_prettyPop_fp_swf_loc = '<?php echo _URL .'/players/flowplayer2/flowplayer.swf';  ?>';

var MELODYURL = '<?php echo _URL; ?>';
var MELODYURL2 = '<?php echo _URL2; ?>';

// @since v2.7
var phpmelody = { 
	url: '<?php echo _URL; ?>',
	admin_url: '<?php echo _URL .'/'. _ADMIN_FOLDER; ?>',
	admin_ajax_url: '<?php echo _URL .'/'. _ADMIN_FOLDER .'/admin-ajax.php'; ?>',
	prevent_leaving_without_saving: false,
	doing_ajax: false,
	max_file_size_bytes: <?php echo $upload_max_filesize; ?>, 
	max_file_size_readable: '<?php echo readable_filesize(get_true_max_filesize()); ?>',
	version: '<?php echo $config["version"]; ?>',
	license_id: '<?php echo _CUSTOMER_ID; ?>'
};
</script>

</head>

<body class="<?php echo ($_COOKIE['sidebar-main-state'] == 'maxi') ? '' : 'sidebar-xs'; ?>">
<div id="loading">Working ...</div>

<!-- Masthead
================================================== -->
<a id="top" name="return-to-top"></a>
<!-- Page content -->
<div class="page-content">
<?php if( file_exists("db_update.php") && $hide_update_notification != 1) : ?>
	<?php if ((version_compare($official_version, $config['version'], '=='))) : ?>
		<div class="alert rounded-0 dbupdate-bar animated slideInDown">
			<strong>Important:</strong> Delete <code><?php echo _ADMIN_FOLDER;?>/db_update.php</code> right now.</strong> 
			<button type="button" class="close btn btn-sm font-size-xs mt-1 px-3" data-dismiss="alert"><span>close</span></button>
		</div>
	<script>
		$(document).ready(function() {
			$.notify({message: "<strong>IMPORTANT!</strong><br /> Delete <code><?php echo _ADMIN_FOLDER;?>/db_update.php</code> right now."}, {title: 'Important'}, {type: 'danger'});
		});
	</script>
	<?php else : ?>
	<div class="alert rounded-0 dbupdate-bar animated slideInDown">
		<strong>PHP Melody Update: <a href="db_update.php">Finalize the update process now</a>. Do not skip this final step.</strong>
			<button type="button" class="close btn btn-sm font-size-xs mt-1 px-3" data-dismiss="alert"><span>close</span></button>
	</div>
	<?php endif; ?>
<?php endif; ?>

<?php
include_once('menu.php');

if ( ! is_admin() && is_array($allowed_access) && ! in_array($showm, $allowed_access)) 
{
	restricted_access();
}
$official_version = cache_this('read_version', 'pm_version'); 