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

// Overwrite Maintenance Mode
$strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
session_write_close();

// --- START local functions
function curl_file_get_contents($url)
{
	if (function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . $_COOKIE['PHPSESSID']); 
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$contents = curl_exec($ch);
		$errormsg = curl_error($ch);
		curl_close($ch);
		
		if($errormsg != '')
		{
			return $errormsg;
		}
		return $contents;
	}
	else if (ini_get('allow_url_fopen') == 1)
	{
		$opts = array('http' => array('header'=> 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"));
		$context = stream_context_create($opts);
		return file_get_contents($url, false, $context);
	}

	return false;
}
// --- END local functions


// Data mapping & defining default values
$default_tpl_properties = array(
	// input name attr	=> input value attr
	'ct_heading' 		=> '#333333',		//	h1,h2,h3,h4,h5,h6: color 
	'ct_container' 		=> '#FFFFFF',		//	#content: background-color
	'ct_container_txt' 	=> '#000000',		//	#content: color 
	'ct_body' 			=> '#fbfbfb',		//	body: background-color
	'ct_header' 		=> '#ffffff',		//	header: background-color
	'ct_header_user_pane' => '#505860',     //  #user-pane .greet-links a: color
	'ct_widenav' 		=> '#f5f7fb',		//	.pm-top-nav: background-color // f5fcff
	'ct_widenav_link' 	=> '#888888',		//	ul.nav.nav-tabs li a.wide-nav-link: color
	'ct_wrapper_link' 	=> '#444444',		//	#content a: color
	'ct_video_link' 	=> '#444444',		//	h3 a: color
	'ct_sitetitle' 		=> '#1b89ff',		//	.header-logo > h3 > a: color
	'ct_footer'			=> '#F7F7F7',		//	footer: background-color
	'ct_footer_link' 	=> '#777777',		//	footer, footer a: color
	'ct_container_width'	=> '1000px'			//	#content, .fixed960: width, max-width (px) 
);

if ($_POST['reset'] == 'true') // AJAX 
{
	if ( ! $logged_in || ! is_admin())
	{
		exit();
	}
	update_config('default_tpl_customizations', base64_encode(serialize(array())), true); //must use base64_encode/decode because of "," in serialized string.
	//header('Location: '. _URL .'/'. _ADMIN_FOLDER .'/customize.php');
	exit();
}

if ($_POST['action'] == 'customize-save') // AJAX
{
	if ( ! $logged_in || ! is_admin())
	{
		$ajax_msg = ($logged_in) ? 'Access denied!' : 'Please log in.';
		exit(json_encode(array('success' => false, 'msg' => pm_alert_error($ajax_msg))));
	}

	$customs = array();
	
	foreach ($default_tpl_properties as $property => $default_value)
	{
		if ((strtolower($default_value) != strtolower($_POST[$property])) && $_POST[$property] != '')
		{
			$value = trim($_POST[$property]);
			$value = stripslashes($value); // must have for servers with magic_quotes on.
			
			switch ($property) 
			{
				case 'ct_body':
					
					$customs['body']['background-image'] = 'none';
					$customs['body']['background-color'] = $value;

				break;
				
				case 'ct_heading':
				
					$customs['#content h1']['color'] = $value;
					$customs['#content h2']['color'] = $value;
					$customs['#content h3']['color'] = $value;
					$customs['#content h4']['color'] = $value;
					$customs['#content h5:not(.media-heading)']['color'] = $value;
					$customs['#content h6']['color'] = $value;

					$customs['#content h1>a']['color'] = $value;
					$customs['#content h2>a']['color'] = $value;
					$customs['#content h3>a']['color'] = $value;
					$customs['#content h4>a']['color'] = $value;
					$customs['#content h5:not(.media-heading)>a']['color'] = $value;
					$customs['#content h6>a']['color'] = $value;

					$customs['#content h1>a:hover']['color'] = $value;
					$customs['#content h2>a:hover']['color'] = $value;
					$customs['#content h3>a:hover']['color'] = $value;
					$customs['#content h4>a:hover']['color'] = $value;
					$customs['#content h5:not(.media-heading)>a:hover']['color'] = $value;
					$customs['#content h6>a:hover']['color'] = $value;

					// $customs['#content h1>a:visited']['color'] = $value;
					// $customs['#content h2>a:visited']['color'] = $value;
					// $customs['#content h3>a:visited']['color'] = $value;
					// $customs['#content h4>a:visited']['color'] = $value;
					// $customs['#content h5:not(.media-heading)>a:visited']['color'] = $value;
					// $customs['#content h6>a:visited']['color'] = $value;
					
					$customs['.pm-video-watch-featured h2>a']['color'] = $value;
					$customs['.pm-video-watch-featured h2>a:visited']['color'] = $value;

					$customs['.pm-video-heading h1']['color'] = $value;


				break;
				
				case 'ct_container':
				
					$customs['#content']['background-image'] = 'none';
					$customs['#content']['background-color'] = $value;
					$customs['.pm-section-highlighted']['background-color'] = $value;
					$customs['.pm-featured-list-row']['background-color'] = $value;

					$customs['.pm-vbwrn-list-row']['border-color'] = $value;
					$customs['.col-md-sidebar']['border-color'] = $value;

					$customs['.text-exp .show-more']['background-image'] = array(0 => 'linear-gradient(bottom, '. $value .' 0%, '. $value .' 50%, rgba(244,245,247,0)100%)',
																				 1 => '-o-linear-gradient(bottom, '. $value .' 0%, '. $value .' 50%, rgba(244,245,247,0)100%)',
																				 2 => '-moz-linear-gradient(bottom, '. $value .' 0%, '. $value .' 50%, rgba(244,245,247,0)100%)'
																				);
				break;
				
				case 'ct_container_txt':
					
					$customs['#content']['color'] = $value;
					$customs['#content .pm-video-views']['color'] = $value;
					$customs['#content .pm-video-author']['color'] = $value;
					$customs['#content .pm-autoplay-info']['color'] = $value;
					$customs['#content .publish-date']['color'] = $value;
					$customs['#pm-related ul.pm-ul-sidelist-videos li .pm-video-meta .pm-video-author']['color'] = $value;
					$customs['#pm-related ul.pm-ul-sidelist-videos li .pm-video-meta .pm-video-views']['color'] = $value;

				break;
				
				case 'ct_header':
					 
					$customs['header.header-bg']['background-image'] = 'none';
					$customs['header.header-bg']['background-color'] = $value;
					$customs['header.header-bg']['border-bottom-color'] = $value;

				break;

				case 'ct_header_user_pane':
					 
					// $customs['#user-pane']['color'] = $value;
					// $customs['#user-pane .greet-links a']['color'] = $value;
					// $customs['#user-pane .greet-links a:visited']['color'] = $value;

				break;
				
				case 'ct_widenav':
					 
					$customs['.pm-top-nav']['background-image'] = 'none';
					$customs['.pm-top-nav']['background-color'] = $value;
					$customs['.pm-top-nav']['box-shadow'] = 'none';
					$customs['.pm-top-nav']['border-top-color'] = $value;
					$customs['.pm-top-nav']['border-bottom-color'] = $value;

				break;
				
				case 'ct_widenav_link':
					
					$customs['ul.nav.nav-tabs li a.wide-nav-link']['text-shadow'] = 'none';
					$customs['ul.nav.nav-tabs li a.wide-nav-link']['color'] = $value;
					$customs['.navslide-wrap ul li a']['color'] = $value;
					$customs['.navslide-wrap ul li i']['color'] = $value;

				break;
				
				case 'ct_wrapper_link':
					
					$customs['#content a']['text-shadow'] = 'none';
					$customs['#content a']['color'] = $value;
					$customs['#content button.btn.btn-video i.mico']['color'] = $value;
					$customs['#content button.btn.btn-video span']['color'] = $value;

				break;
				
				case 'ct_video_link':
					 
					$customs['#content h3 a']['text-shadow'] = 'none';
					$customs['#content h3 a']['color'] = $value;

					$customs['#pm-related ul.pm-ul-sidelist-videos li h3 a']['color'] = $value;
					$customs['#pm-related ul.pm-ul-sidelist-videos li h3 a:visited']['color'] = $value;

					$customs['#content h5.media-heading a']['text-shadow'] = 'none';
					$customs['#content h5.media-heading a']['color'] = $value;

				break;
				
				case 'ct_sitetitle':
					 
					$customs['.header-logo > h3 > a']['text-shadow'] = 'none';
					$customs['.header-logo > h3 > a']['color'] =  $value;

				break;
				
				case 'ct_footer':
					
					$customs['.container-footer']['text-shadow'] = 'none';
					$customs['.container-footer']['background-color'] = $value;
					$customs['.container-footer']['border-color'] = $value;
					$customs['.container-footer footer .row-footer-horizontal']['border-color'] = $value;
					$customs['.pm-language .pm-language-list']['background-color'] = $value;

				break;
				
				case 'ct_footer_link':
	
					$customs['.container-footer footer .row-footer-horizontal p']['text-shadow'] = 'none';
					$customs['.container-footer footer .row-footer-horizontal p']['color'] = $value;
					$customs['.container-footer footer .row-footer-horizontal a']['text-shadow'] = 'none';
					$customs['.container-footer footer .row-footer-horizontal a']['color'] = $value;
					$customs['.container-footer footer a']['text-shadow'] = 'none';
					$customs['.container-footer footer a']['color'] = $value;
					$customs['.container-footer footer .list-social-sites i']['color'] = $value;
					$customs['.container-footer footer .row-footer-vertical .list-social-sites i']['color'] = $value;
					$customs['.container-footer footer .pm-language a.btn.btn-link']['color'] = $value;
					
				break;
				
				// case 'ct_container_width':
				// 	$value = str_replace('px', '', $value);
				// 	$customs['#content']['width'] = $value .'px';
				// 	$customs['#content']['max-width'] = $value .'px';
				// 	$customs['.fixed960']['max-width'] = $value .'px';
				// 	$customs['.video-wrapper-wide']['width'] = $value - 40 .'px';
				// 	$customs['.pm-video-head-wide']['width'] = $value - 40 .'px';
				// 	$customs['#video-wrapper.video-wrapper-wide object']['width'] = $value - 40 .'px';
				// 	$customs['#video-wrapper.video-wrapper-wide embed']['width'] = $value - 40 .'px';
				// 	$customs['#video-wrapper.video-wrapper-wide iframe']['width'] = $value - 40 .'px';
				// break;
			}
		}
	}
	
	update_config('default_tpl_customizations', base64_encode(serialize($customs)), true); //must use base64_encode/decode because of "," in serialized string.
	
	echo json_encode(array('success' => true,
							'msg' => pm_alert_success('<strong>Saved!</strong> Return to the <a href="theme-settings.php">Admin Area</a> or <a href="'._URL.'/index.'._FEXT.'">see your website</a>.')
						  ));

	exit();
}

if ( ! $logged_in || ! is_admin())
{
	header('Location: '. _URL .'/'. _ADMIN_FOLDER .'/login.php');
	exit();
}

$tpl_customizations = array();
if ($config['default_tpl_customizations'] != '')
{
	$tpl_customizations = unserialize(base64_decode($config['default_tpl_customizations']));
	 
}

$tpl_properties = array();

foreach ($default_tpl_properties as $name => $value)
{
	switch ($name)
	{
		case 'ct_heading':
			$tpl_properties[$name] = ($tpl_customizations['h1']['color'] != '') ? $tpl_customizations['h1']['color'] : $value; 
		break;
		
		case 'ct_container':
			$tpl_properties[$name] = ($tpl_customizations['#content']['background-color'] != '') ? $tpl_customizations['#content']['background-color'] : $value;
		break;
		
		case 'ct_container_txt':
			$tpl_properties[$name] = ($tpl_customizations['#content']['color'] != '') ? $tpl_customizations['#content']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['.pm-video-views']['color'] != '') ? $tpl_customizations['.pm-video-views']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['.pm-video-author']['color'] != '') ? $tpl_customizations['.pm-video-author']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['.pm-autoplay-info']['color'] != '') ? $tpl_customizations['.pm-autoplay-info']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['.publish-date']['color'] != '') ? $tpl_customizations['.publish-date']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['#pm-related ul.pm-ul-sidelist-videos li .pm-video-meta .pm-video-author']['color'] != '') ? $tpl_customizations['#pm-related ul.pm-ul-sidelist-videos li .pm-video-meta .pm-video-author']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['#pm-related ul.pm-ul-sidelist-videos li .pm-video-meta .pm-video-views']['color'] != '') ? $tpl_customizations['#pm-related ul.pm-ul-sidelist-videos li .pm-video-meta .pm-video-views']['color'] : $value;
		break;
		
		case 'ct_body':
			$tpl_properties[$name] = ($tpl_customizations['body']['background-color'] != '') ? $tpl_customizations['body']['background-color'] : $value;
		break;
		
		case 'ct_header':
			$tpl_properties[$name] = ($tpl_customizations['header.header-bg']['background-color'] != '') ? $tpl_customizations['header.header-bg']['background-color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['header.header-bg']['border-bottom-color'] != '') ? $tpl_customizations['header.header-bg']['border-bottom-color'] : $value;
		break;

		case 'ct_header_user_pane':
			$tpl_properties[$name] = ($tpl_customizations['#user-pane']['color'] != '') ? $tpl_customizations['#user-pane']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['#user-pane .greet-links a']['color'] != '') ? $tpl_customizations['#user-pane .greet-links a']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['#user-pane .greet-links a:visited']['color'] != '') ? $tpl_customizations['#user-pane .greet-links a:visited']['color'] : $value;
		break;
		
		case 'ct_widenav':
			$tpl_properties[$name] = ($tpl_customizations['.pm-top-nav']['background-color'] != '') ? $tpl_customizations['.pm-top-nav']['background-color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['.navslide-wrap']['background-color'] != '') ? $tpl_customizations['.navslide-wrap']['background-color'] : $value; 
		break;
		
		case 'ct_widenav_link':
			$tpl_properties[$name] = ($tpl_customizations['ul.nav.nav-tabs li a.wide-nav-link']['color'] != '') ? $tpl_customizations['ul.nav.nav-tabs li a.wide-nav-link']['color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['.navslide-wrap ul li a']['color'] != '') ? $tpl_customizations['.navslide-wrap ul li a']['color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['.navslide-wrap ul li i']['color'] != '') ? $tpl_customizations['.navslide-wrap ul li i']['color'] : $value; 
		break;
		
		case 'ct_wrapper_link':
			$tpl_properties[$name] = ($tpl_customizations['#content a']['color'] != '') ? $tpl_customizations['#content a']['color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['#content button.btn.btn-video']['color'] != '') ? $tpl_customizations['#content button.btn.btn-video']['color'] : $value; 
		break;
		
		case 'ct_video_link':
			$tpl_properties[$name] = ($tpl_customizations['h3 a']['color'] != '') ? $tpl_customizations['h3 a']['color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['#pm-related ul.pm-ul-sidelist-videos li h3 a']['color'] != '') ? $tpl_customizations['#pm-related ul.pm-ul-sidelist-videos li h3 a']['color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['#pm-related ul.pm-ul-sidelist-videos li h3 a:visited']['color'] != '') ? $tpl_customizations['#pm-related ul.pm-ul-sidelist-videos li h3 a:visited']['color'] : $value; 
		break;
		
		case 'ct_sitetitle':
			$tpl_properties[$name] = ($tpl_customizations['.header-logo > h3 > a']['color'] != '') ? $tpl_customizations['.header-logo > h3 > a']['color'] : $value; 
		break;
		
		case 'ct_footer':
			$tpl_properties[$name] = ($tpl_customizations['.container-footer']['background-color'] != '') ? $tpl_customizations['.container-footer']['background-color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['.container-footer']['border-color'] != '') ? $tpl_customizations['.container-footer']['border-color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['.container-footer footer .row-footer-horizontal']['border-color'] != '') ? $tpl_customizations['.container-footer footer .row-footer-horizontal']['border-color'] : $value; 
		break;
		
		case 'ct_footer_link':
			$tpl_properties[$name] = ($tpl_customizations['.container-footer footer .row-footer-horizontal p a']['color'] != '') ? $tpl_customizations['.container-footer footer .row-footer-horizontal p a']['color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['.container-footer footer .row-footer-horizontal p a:visited']['color'] != '') ? $tpl_customizations['.container-footer footer .row-footer-horizontal p a:visited']['color'] : $value; 
			$tpl_properties[$name] = ($tpl_customizations['.container-footer footer .row-footer-horizontal p']['color'] != '') ? $tpl_customizations['.container-footer footer .row-footer-horizontal p']['color'] : $value; 
		break;
		
		// case 'ct_container_width':
		// 	$tpl_properties[$name] = ($tpl_customizations['#content']['width'] != '') ? $tpl_customizations['#content']['width'] : $value; 
		// break;
	}
}

// get a random page for live preview 
if ($config['published_videos'] == 0)
{
	$preview_page = curl_file_get_contents(_URL);
}
else
{
	$rand_from = rand(0, $config['published_videos']);
	
	$sql = "SELECT uniq_id 
			FROM pm_videos 
			WHERE added <= '". time() ."'
				AND video_type = ". IS_VIDEO ."
			LIMIT $rand_from, 1";

	if ($result = mysql_query($sql))
	{
		$row = mysql_fetch_assoc($result);
		if (pm_count($row) > 0)
		{
			$preview_page = curl_file_get_contents(_URL .'/watch.php?vid='. $row['uniq_id']);
			mysql_free_result($result);
		}
	}
	
	if ( ! $result || ! $row)
	{
		$preview_page = curl_file_get_contents(_URL);
	}
}
$preview_page = preg_replace('/<a(.*)href="([^"]*)"(.*)>/','<a$1href="#"$3>',$preview_page);

echo $preview_page;

?>
<style>
header.pm-top-head,
.navmenu-default.offcanvas.in {
	margin-left: 260px; 
}


body {
	margin:0;
	padding:0;
	margin-left: 260px;
}
.pm-ad-zone {
	display:none;	
}
#preroll_placeholder, #Playerholder, object, embed, iframe {
	z-index: 100;
}
#ct_wrapper_width {
	max-width: 100%;
}
.colorpicker {
	z-index: 6666 !important;
}
.ct_sidebar {
	position: fixed;
	overflow-y: auto;
	top:0;
	left:0;
	width: 260px;
	height: 100%;
	display: block;
	padding: 15px 10px;
	margin: 0;
	background-color: #1e2123;
    box-shadow: inset 0 3px 6px rgba(0,0,0,.16), 0 3px 6px rgba(0,0,0,.23);
	border-right: 2px solid #000;
	z-index: 5000 !important;
}
.ct_title {
	font-size: 16px;
	font-weight: bold;
	line-height: 1.4em;
	text-transform:capitalize;
	padding: 5px 0;
	color: #FFF;
	text-shadow: none;
	border-bottom: none;
	margin-bottom: 18px;
	background-color: #333;
	border-radius: 4px;
	text-align: left;
	padding: 10px;
}
.ct_table {
	font-size: 11px;
	font-weight: bold;
}

.ct_table td {
	border-top: 0;
	border-bottom: 0;
	vertical-align: middle !important;
	text-shadow: 0 1px 0 #FFF;
	padding: 2px 0;
	margin:0;

	color: #969fa5;
	text-shadow: none;
	font-size: 12px;
	border-top: 1px solid #222;
	border-bottom: 1px solid #393939;

    background: #212527;
    -webkit-box-shadow: 0 1px 0 0 hsla(0,0%,100%,.035) inset, 0 -1px 2px 0 rgba(0,0,0,.12), 0 1px 0 0 hsla(0,0%,100%,.035);
    box-shadow: inset 0 1px 0 0 hsla(0,0%,100%,.035), 0 -1px 2px 0 rgba(0,0,0,.12), 0 1px 0 0 hsla(0,0%,100%,.035);
    border-top: 1px solid #1b1b1b;

}
.ct_table tr.divider td {
	border:0;
	color: #a1de41;
	font-weight: bold;
	border-bottom: 1px solid #888;
}
.ct_table .input-append {
	position: relative;
}
.ct_table .input-append input {
	border-radius: 3px;
	border: 1px solid #888;
	padding: 1px 2px;
	color: #000;
	border: 1px solid #000;
	background-color:#73787b;
}
.ct_table .reset-button {
	position: absolute;
	left:-23px;
	top:2px;
	vertical-align: middle;
	margin:0;
	padding:0;
	color: #FC0;
	box-shadow: none;
	background-image: none;
	border:0;
}
.reset-button:hover {
	color: #FFF;
	text-decoration: none;
}

.ct_submit {
	display: block;
	width: 100%;
}
.ct_sidebar button.btn-sm {
	font-size: 11px !important;
	font-weight: bold;
}
.ct_table .add-on {
	display: inline-block;
	margin-left: 2px;
    vertical-align: middle;
    width: 18px;
}
.ct_table .add-on i {
	border-radius:3px;
	width: 18px;
	height: 18px;
	border:1px solid #000;
}

.ct_submit_group a,
.ct_submit_group a:visited {
	color: #FFF;
}

.ct_submit .btn {
	border-radius: 3px;
	border: 1px solid;
	text-align: center;
	text-transform: uppercase;
	cursor: default;
	color: #bebebe;
	font-weight: 700;
	background: -webkit-gradient(linear,left top,left bottom,from(#2f3235),to(#282a2d));
	background: -o-linear-gradient(#2f3235,#282a2d);
	background: linear-gradient(#2f3235,#282a2d);
	border-color: #111 #111 #000;
	border-width: 1px;
	border-style: solid;
	-webkit-box-shadow: 0 1px 0 0 rgba(0,0,0,.5), 0 1px 0 0 hsla(0,0%,100%,.05) inset;
	box-shadow: 0 1px 0 0 rgba(0,0,0,.5), inset 0 1px 0 0 hsla(0,0%,100%,.05);
}
.ct_submit .btn-success {
	color: #a1de41;
}

</style>
<!--
ct_body
ct_sitetitle
ct_header
ct_header_user_pane
ct_widenav
ct_widenav_link
ct_video_link
ct_container
ct_container_txt
ct_wrapper_link
ct_heading
ct_footer
ct_footer_link2
-->
<div class="ct_sidebar" id="sticky">
<div class="row-fluid">
	<!-- <div class="span12 ct_title">Theme Customization</div> -->
</div>
<div id="ajax-response-container" style="display:none"></div>
<form name="theme-customization" action="" method="post">
<table width="100%" cellpadding="0" cellspacing="0" class="table ct_table">
	<tr class="divider">
		<td colspan="2"><strong>HEADER</strong></td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Site Name
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_sitetitle'];?>" data-color-format="hex" id="ct_sitetitle">
				<input id="ct_sitetitle2" name="ct_sitetitle" type="text" size="14" value="<?php echo $tpl_properties['ct_sitetitle'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_sitetitle'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_sitetitle"><i class="mico mico-cancel"></i></a>
            </div>

			<input type="hidden" name="ct_sitetitle_base_value" value="<?php echo $tpl_properties['ct_sitetitle'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Header Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_header'];?>" data-color-format="hex" id="ct_header">
				<input id="ct_header2" name="ct_header" type="text" size="14" value="<?php echo $tpl_properties['ct_header'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_header'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_header" ><i class="mico mico-cancel"></i></a>
            </div>

			<input type="hidden" name="ct_header_base_value" value="<?php echo $tpl_properties['ct_header'];?>" />
		</td>
	</tr>
<!-- 	<tr>
		<td align="left" valign="middle">
			Header Text &amp; Links
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_header_user_pane'];?>" data-color-format="hex" id="ct_header_user_pane">
				<input id="ct_header_user_pane2" name="ct_header_user_pane" type="text" size="14" value="<?php echo $tpl_properties['ct_header_user_pane'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_header_user_pane'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_header_user_pane" ><i class="mico mico-cancel"></i></a>
            </div>

			<input type="hidden" name="ct_header_user_pane_base_value" value="<?php echo $tpl_properties['ct_header_user_pane'];?>" />
		</td>
	</tr> -->
	<tr class="divider">
		<td colspan="2"><strong>MENU</strong></td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Menu Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_widenav'];?>" data-color-format="hex" id="ct_widenav">
				<input id="ct_widenav2" name="ct_widenav" type="text" size="14" value="<?php echo $tpl_properties['ct_widenav'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_widenav'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_widenav" ><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_widenav_base_value" value="<?php echo $tpl_properties['ct_widenav'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Menu Links
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_widenav_link'];?>" data-color-format="hex" id="ct_widenav_link">
				<input id="ct_widenav_link2" name="ct_widenav_link" type="text" size="14" value="<?php echo $tpl_properties['ct_widenav_link'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_widenav_link'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_widenav_link" ><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_widenav_link_base_value" value="<?php echo $tpl_properties['ct_widenav_link'];?>" />
		</td>
	</tr>
	<tr class="divider">
		<td colspan="2"><strong>CONTENT</strong></td>
	</tr>
	<tr>
		<td width="55%" align="left" valign="middle">
			Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_body'];?>" data-color-format="hex" id="ct_body">
				<input id="ct_body2" name="ct_body" type="text" size="14" value="<?php echo $tpl_properties['ct_body'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_body'];?>"></i></span>
                <a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_body"><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_body_base_value" value="<?php echo $tpl_properties['ct_body'];?>" />
		</td>
	</tr>

	<tr>
		<td align="left" valign="middle">
			Heading Color
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_heading'];?>" data-color-format="hex" id="ct_heading">
				<input id="ct_heading2" name="ct_heading" type="text" size="14" value="<?php echo $tpl_properties['ct_heading'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_heading'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_heading"><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_heading_base_value" value="<?php echo $tpl_properties['ct_heading'];?>" />
		</td>
	</tr>
<!-- 	<tr>
		<td align="left" valign="middle">
			Content Width
		</td>
		<td align="right">
        	<div class="input-append pull-right">
			<input id="ct_container_width" name="ct_container_width" type="text" size="14" value="<?php echo $tpl_properties['ct_container_width']; ?>" style="width: 55px;"/><span class="add-on">px</span>
            </div>

		</td>
	</tr>
 -->	
 	<tr>
		<td align="left" valign="middle">
			Content Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_container'];?>" data-color-format="hex" id="ct_container">
				<input id="ct_container2" name="ct_container" type="text" size="14" value="<?php echo $tpl_properties['ct_container'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_container'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_container" ><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_container_base_value" value="<?php echo $tpl_properties['ct_container'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Content Text
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_container_txt'];?>" data-color-format="hex" id="ct_container_txt">
				<input id="ct_container_txt2" name="ct_container_txt" type="text" size="14" value="<?php echo $tpl_properties['ct_container_txt'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_container_txt'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_container_txt" ><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_container_txt_base_value" value="<?php echo $tpl_properties['ct_container_txt'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Content Links (All)
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_wrapper_link'];?>" data-color-format="hex" id="ct_wrapper_link">
				<input id="ct_wrapper_link2" name="ct_wrapper_link" type="text" size="14" value="<?php echo $tpl_properties['ct_wrapper_link'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_wrapper_link'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_wrapper_link" ><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_wrapper_link_base_value" value="<?php echo $tpl_properties['ct_wrapper_link'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Video &amp; Article Links
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_video_link'];?>" data-color-format="hex" id="ct_video_link">
				<input id="ct_video_link2" name="ct_video_link" type="text" size="14" value="<?php echo $tpl_properties['ct_video_link'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_video_link'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_video_link" ><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_video_link_base_value" value="<?php echo $tpl_properties['ct_video_link'];?>" />
		</td>
	</tr>
	<tr class="divider">
		<td colspan="2"><strong>FOOTER</strong></td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Footer Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_footer'];?>" data-color-format="hex" id="ct_footer">
				<input id="ct_footer2" name="ct_footer" type="text" size="14" value="<?php echo $tpl_properties['ct_footer'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_footer'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_footer" ><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_footer_base_value" value="<?php echo $tpl_properties['ct_footer'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Footer Text
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_footer_link'];?>" data-color-format="hex" id="ct_footer_link">
				<input id="ct_footer_link2" name="ct_footer_link" type="text" size="14" value="<?php echo $tpl_properties['ct_footer_link'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_footer_link'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-sm" parent-input="ct_footer_link" ><i class="mico mico-cancel"></i></a>
			</div>

			<input type="hidden" name="ct_footer_link_base_value" value="<?php echo $tpl_properties['ct_footer_link'];?>" />
		</td>
	</tr>
</table>

<div class="ct_submit_group">
<ul class="pager ct_submit">
	<li class="previous">
		<button id="button-cancel" class="btn btn-sm btn-normal border-radius3">
			&larr; Cancel
		</button>
	</li>
	<li class="next">
		<button id="button-save" class="btn btn-sm btn-success">
			Save &amp; Apply
		</button>
		<input type="hidden" name="action" value="customize-save" />
	</li>
</ul>
<div align="center"><a href="customize.php?reset=true" id="reset-to-default">Reset all to default</a></div>
</div>

</form>
</div><!-- .ct_sidebar -->

<!-- <script src="js/bootstrap.min.js" type="text/javascript"></script> -->
<script src="js/bootstrap-colorpicker.min.js" type="text/javascript"></script>
<script src="js/jquery.nouislider.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-colorpicker.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.nouislider.css" />
<script type="application/javascript">
$("#ct_heading").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("h1").css("color", hex);
    $("h2").css("color", hex);
    $("h3").css("color", hex);
    $("h4").css("color", hex);
    $("h5").css("color", hex);
    $("h6").css("color", hex);
    $("#ct_heading2").val(hex);
	
	customize_show_reset_button("ct_heading");
});
$("#ct_container").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("#content, .pm-section-highlighted, .pm-featured-list-row").css({
        "background-image": "none",
        "background-color": hex
    });

    $(".text-exp .show-more").css({
        "background-image": "linear-gradient(bottom, " + hex + " 0%, " + hex + " 50%, rgba(244,245,247,0)100%)"
    });
    $(".text-exp .show-more").css({
        "background-image": "-o-linear-gradient(bottom, " + hex + " 0%, " + hex + " 50%, rgba(244,245,247,0)100%)"
    });
    $(".text-exp .show-more").css({
        "background-image": "-moz-linear-gradient(bottom, " + hex + " 0%, " + hex + " 50%, rgba(244,245,247,0)100%)"
    });
    $("#ct_container2").val(hex);
	
	customize_show_reset_button("ct_container");
});
$("#ct_container_txt").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("#content, .pm-video-views, .pm-video-author, .pm-autoplay-info, .publish-date").css("color", hex);
    $("#pm-related ul.pm-ul-sidelist-videos li .pm-video-meta").css("color", hex + '!important');
    $("#pm-related ul.pm-ul-sidelist-videos li .pm-video-meta .pm-video-author").css("color", hex + '!important');
    $("#pm-related ul.pm-ul-sidelist-videos li .pm-video-meta .pm-video-views").css("color", hex + '!important');

    $("#ct_container_txt2").val(hex);
	
	customize_show_reset_button("ct_container_txt");
});
$("#ct_body").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("body").css({
        "background-image": "none",
        "background-color": hex
    });
    $("#ct_body2").val(hex);
	
	customize_show_reset_button("ct_body");
});
$("#ct_header").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("header").css({
        "background-image": "none",
        "background-color": hex,
        "border-bottom-color": hex
    });
    $("#ct_header2").val(hex);
		
	customize_show_reset_button("ct_header");
});

$("#ct_header_user_pane").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $(".greet-links").css({
		"color": hex
    });
    $(".greet-links a").css({
		"color": hex
    });
    $("#ct_header_user_pane2").val(hex);
		
	customize_show_reset_button("ct_header_user_pane");
});


$("#ct_widenav").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $(".pm-top-nav, .navslide-wrap").css({
        "background-image": "none",
        "background-color": hex,
        "box-shadow": "none",
		//"border-top": "2px solid" + hex + "",
		"border-bottom": "1px solid" + hex + ""
    });


    $("ul.nav.nav-tabs li a.wide-nav-link").css({
        "text-shadow": "none",
    });
    $("#ct_widenav2").val(hex);
	
	customize_show_reset_button("ct_widenav");
});
$("#ct_widenav_link").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("ul.nav.nav-tabs li a.wide-nav-link, .navslide-wrap ul li a, .navslide-wrap ul li i").css({
        "text-shadow": "none",
        color: hex
    });
    $("#ct_widenav_link2").val(hex);
	
	customize_show_reset_button("ct_widenav_link");
});
$("#ct_wrapper_link").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("#content a").css({
        "text-shadow": "none",
        color: hex
    });
    $("#content button.btn.btn-video i, #content button.btn.btn-video span").css({
        "text-shadow": "none",
        color: hex
    });

    $("#ct_wrapper_link2").val(hex);
	
	customize_show_reset_button("ct_wrapper_link");
});
$("#ct_video_link").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $(".mastcontent-wrap h3 a").css({
        "text-shadow": "none",
        color: hex
    });
    $("#ct_video_link2").val(hex);
	
	customize_show_reset_button("ct_video_link");
});
$("#ct_sitetitle").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $(".header-logo > h3 > a").css({
        "text-shadow": "none",
        color: hex
    });
    $("#ct_sitetitle2").val(hex);
	
	customize_show_reset_button("ct_sitetitle");
});
$("#ct_footer").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $(".container-footer, .row-footer-horizontal").css({
        "text-shadow": "none",
        "border-top-color": hex,
        "background-color": hex
    });

    $("#ct_footer2").val(hex);
	
	customize_show_reset_button("ct_footer");
});
$("#ct_footer_link").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("footer p, footer a, footer i").css({
        "text-shadow": "none",
        color: hex
    });
    $("#ct_footer_link2").val(hex);
	
	customize_show_reset_button("ct_footer_link");
});

$('#ct_container_width').change(function() {

	var pixels = $(this).val();
	$("#content").css({
		width: pixels + "px",
		"max-width":pixels + "px"
	});
	$("#content").css("max-width", "" + pixels + "px");
	$("#ct_container_width").val(pixels);
});

function customize_show_reset_button(for_input_name) {
	var old_value = $('input[name="'+ for_input_name +'_base_value"]').val();
	var new_value = $('input[name="'+ for_input_name +'"]').val();
	
	if (new_value != old_value) {
		$('a[parent-input="'+ for_input_name +'"]').fadeIn();
	}
}

function customize_update_colorpicker(for_input_name, new_hex) {
	
	switch (for_input_name) {
		case "ct_body":
			$("#ct_body").colorpicker("setValue", new_hex); 
		break;
		case "ct_heading":
			$("#ct_heading").colorpicker("setValue", new_hex); 
		break;
		case "ct_container":
			$("#ct_container").colorpicker("setValue", new_hex); 
		break;
		case "ct_container_txt":
			$("#ct_container_txt").colorpicker("setValue", new_hex); 
		break;
		case "ct_header":
			$("#ct_header").colorpicker("setValue", new_hex); 
		break;
		case "ct_header_user_pane":
			$("#ct_header_user_pane").colorpicker("setValue", new_hex); 
		break;
		case "ct_widenav":

			if (new_hex == 'transparent') {
				$(".pm-top-nav").css({
			        "background-image": "#f0f0f0",
			        "background-color": new_hex,
			        "box-shadow": "#f1f1f1",
					"border-top": "1px solid #d2d2d2",
					"border-bottom": "1px solid #d2d2d2"
			    });
			    $("#ct_widenav2").val(new_hex);
				
				customize_show_reset_button("ct_widenav");
			} else {
				$("#ct_widenav").colorpicker("setValue", new_hex);
			} 
			
			
		break;
		case "ct_widenav_link":
			if (new_hex == 'inherit') {
				 $("ul.nav.nav-tabs li a.wide-nav-link, .navslide-wrap ul li a, .navslide-wrap ul li i").css({
			        "text-shadow": "none",
			        color: new_hex
			    });
			    $("#ct_widenav_link2").val(new_hex);
				
				customize_show_reset_button("ct_widenav_link");
			} else {
				$("#ct_widenav_link").colorpicker("setValue", new_hex);
			}
		break;
		case "ct_wrapper_link":
			$("#ct_wrapper_link").colorpicker("setValue", new_hex); 
		break;
		case "ct_video_link":
			$("#ct_video_link").colorpicker("setValue", new_hex); 
		break;
		case "ct_sitetitle":
			$("#ct_sitetitle").colorpicker("setValue", new_hex); 
		break;
		case "ct_footer":
			if (new_hex == 'inherit') {
			    $(".container-footer").css({
			        "background-color": new_hex
			    });
			    $("#ct_footer2").val(new_hex);

				customize_show_reset_button("ct_footer");
			} else {
				$("#ct_footer").colorpicker("setValue", new_hex);
			} 
		break;
		case "ct_footer_link":
			$("#ct_footer_link").colorpicker("setValue", new_hex); 
		break;
	}
	//
}
$(document).ready(function(){
	
	// hide all reset buttons
	$(".reset-button").hide();
	
	// bind to change event to inputs 
	$('input[name^="ct_"]').change(function(){
		customize_show_reset_button($(this).attr("name"));
		customize_update_colorpicker($(this).attr("name"), $(this).val());
	});

	// bind reset action
	$('a[class^="reset-button"]').click(function(){
		var name = $(this).attr("parent-input");
		var old_value = $('input[name="'+ name +'_base_value"]').val();
		
		customize_update_colorpicker(name, old_value);
		
		$(this).hide();
		
		return false;
	});
	
	// prevent form submission
	$("form").submit(function(event){
		 event.preventDefault();
	});
	
	$("#button-cancel").click(function(){
		window.location = "<?php echo _URL .'/'. _ADMIN_FOLDER .'/theme-settings.php';?>";
	});
	
	$("#button-save").click(function(){
		
		$("#ajax-response-container").html("");
		$(this).attr("disabled", "disabled").addClass("disabled");

		var_form_data = $('form[name="theme-customization"]').serialize();
		
		$.ajax({
			url: "<?php echo _URL.'/'. _ADMIN_FOLDER .'/customize.php';?>",
			data: var_form_data,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#ajax-response-container").html(data["msg"]).show();
				
				$("#button-save").removeAttr("disabled").removeClass("disabled");
			}
		});
	});
	
	$('#reset-to-default').click(function(){
		
		if (confirm('Are you sure you want to reset all values to default?')) { 
			$.ajax({
				url: '<?php echo _URL .'/'. _ADMIN_FOLDER .'/customize.php'?>',
				data: {
					reset: 'true'
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					window.location = "<?php echo _URL .'/'. _ADMIN_FOLDER .'/customize.php';?>";
				}
			});
		}
		return false;
	});
});

</script>