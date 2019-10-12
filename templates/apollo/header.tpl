<!DOCTYPE html>
<!--[if IE 7 | IE 8]>
<html class="ie" dir="{if $smarty.const._IS_RTL == '1'}rtl{else}ltr{/if}">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html dir="{if $smarty.const._IS_RTL == '1'}rtl{else}ltr{/if}">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />

<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<title>{$meta_title}</title>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=edge,chrome=1">
{if $no_index == '1' || $smarty.const._DISABLE_INDEXING == '1'}
<meta name="robots" content="noindex,nofollow">
<meta name="googlebot" content="noindex,nofollow">
{/if}
<meta name="title" content="{$meta_title}" />
<meta name="keywords" content="{$meta_keywords}" />
<meta name="description" content="{$meta_description}" />
<link rel="apple-touch-icon" sizes="180x180" href="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/favicon-16x16.png">
<link rel="shortcut icon" href="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/favicon.ico">
{if $tpl_name == "video-category"}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php?c={$cat_id}" />
{elseif $tpl_name == "video-top"}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php?feed=topvideos" />
{elseif $tpl_name == "article-category"}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php?c={$cat_id}&feed=articles" />
{else}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php" />
{/if}

{if $comment_system_facebook && $fb_app_id != ''}
<meta property="fb:app_id" content="{$fb_app_id}" />
{/if}
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" href="{$smarty.const._URL}/templates/{$template_dir}/css/bootstrap.min.css">

<!--[if lt IE 9]>
<script src="//css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/apollo.css">
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/animate.min.css">
{if $smarty.const._IS_RTL == '1'}
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/bootstrap.min.rtl.css">
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/apollo.rtl.css">
{/if}
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:400,300,500,700|Open+Sans:400,500,700">
<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/custom.css">
{if $tpl_name == 'video-watch'}
<link rel="canonical" href="{$video_data.video_href}"/>
{/if}
{if $allow_google_login && (! $logged_in)}
<meta name="google-signin-client_id" content="{$oauth_google_clientid}">
<script src="https://apis.google.com/js/api:client.js"></script>
<script type="text/javascript">
var gapi_clientid = "{$oauth_google_clientid}";
</script>
{/if}
<script type="text/javascript">
var MELODYURL = "{$smarty.const._URL}";
var MELODYURL2 = "{$smarty.const._URL2}";
var TemplateP = "{$smarty.const._URL}/templates/{$template_dir}";
var _LOGGEDIN_ = {if $logged_in}true{else}false{/if};
 
{if $tpl_name == 'index' || $tpl_name == 'video-watch' || $tpl_name == 'video-watch-episode'}
{literal}
var pm_video_data = {
{/literal}	
	uniq_id: "{$video_data.uniq_id}",
	url: "{$video_data.video_href}",
	duration: {$video_data.yt_length|default:0},
	duration_str: "{$video_data.duration}",
	category: "{$video_data.category}".split(','),
	category_str: "{$video_data.category}",
	featured: {$video_data.featured|default:0},
	restricted: {$video_data.restricted|default:0},
	allow_comments: {$video_data.allow_comments|default:0},
	allow_embedding: {$video_data.allow_embedding|default:0},
	is_stream: {if $video_data.is_stream}true{else}false{/if},
	views: {$video_data.site_views|default:0},
	likes: {$video_data.likes|default:0},
	dislikes: {$video_data.dislikes|default:0},
	publish_date_str: "{$video_data.html5_datetime}",
	publish_date_timestamp: {$video_data.added_timestamp|default:0},
	embed_url: "{$video_data.embed_href}",
	thumb_url: "{$video_data.thumb_img_url}",
	preview_image_url: "{$video_data.preview_image}",
	title: '{$video_data.video_title|escape:'quotes'}',
	autoplay_next: {if $video_data.autoplay_next}true{else}false{/if},
	autoplay_next_url: "{$video_data.autoplay_next_url}"
{literal}
}
{/literal}
{/if}
</script>
{literal}
<script type="text/javascript">
 var pm_lang = {
	lights_off: "{/literal}{$lang.lights_off}{literal}",
	lights_on: "{/literal}{$lang.lights_on}{literal}",
	validate_name: "{/literal}{$lang.validate_name}{literal}",
	validate_username: "{/literal}{$lang.validate_username}{literal}",
	validate_pass: "{/literal}{$lang.validate_pass}{literal}",
	validate_captcha: "{/literal}{$lang.validate_captcha}{literal}",
	validate_email: "{/literal}{$lang.validate_email}{literal}",
	validate_agree: "{/literal}{$lang.validate_agree}{literal}",
	validate_name_long: "{/literal}{$lang.validate_name_long}{literal}",
	validate_username_long: "{/literal}{$lang.validate_username_long}{literal}",
	validate_pass_long: "{/literal}{$lang.validate_pass_long}{literal}",
	validate_confirm_pass_long: "{/literal}{$lang.validate_confirm_pass_long}{literal}",
	choose_category: "{/literal}{$lang.choose_category}{literal}",
	validate_select_file: "{/literal}{$lang.upload_errmsg10}{literal}",
	validate_video_title: "{/literal}{$lang.validate_video_title}{literal}",
	please_wait: "{/literal}{$lang.please_wait}{literal}",
	// upload video page
	swfupload_status_uploaded: "{/literal}{$lang.swfupload_status_uploaded}{literal}",
	swfupload_status_pending: "{/literal}{$lang.swfupload_status_pending}{literal}",
	swfupload_status_queued: "{/literal}{$lang.swfupload_status_queued}{literal}",
	swfupload_status_uploading: "{/literal}{$lang.swfupload_status_uploading}{literal}",
	swfupload_file: "{/literal}{$lang.swfupload_file}{literal}",
	swfupload_btn_select: "{/literal}{$lang.swfupload_btn_select}{literal}",
	swfupload_btn_cancel: "{/literal}{$lang.swfupload_btn_cancel}{literal}",
	swfupload_status_error: "{/literal}{$lang.swfupload_status_error}{literal}",
	swfupload_error_oversize: "{/literal}{$lang.swfupload_error_oversize}{literal}",
	swfupload_friendly_maxsize: "{/literal}{$upload_limit}{literal}",
	upload_errmsg2: "{/literal}{$lang.upload_errmsg2}{literal}",
	// playlist
	playlist_delete_confirm: "{/literal}{$lang.playlist_delete_confirm}{literal}",
	playlist_delete_item_confirm: "{/literal}{$lang.playlist_delete_item_confirm}{literal}",
	show_more: "{/literal}{$lang.show_more}{literal}",
	show_less: "{/literal}{$lang.show_less}{literal}",
	delete_video_confirmation: "{/literal}{$lang.delete_video_confirmation|default:'Are you sure you want to delete this video?'}{literal}",
	browse_all: "{/literal}{$lang.browse_all|default:'Browse All'}{literal}"
 }
</script>
{/literal}

{if $facebook_image_src != ''}
		<link rel="image_src" href="{$facebook_image_src}" />
		<meta property="og:url"  content="{if $tpl_name == 'article-read'}{$article.link}{elseif ! empty($episode_data)}{$episode_data.url}{else}{$video_data.video_href}{/if}" />
		{if $tpl_name == 'article-read'}
		<meta property="og:type" content="article" />
		{/if}
		<meta property="og:title" content="{$meta_title}" />
		<meta property="og:description" content="{$meta_description}" />
		<meta property="og:image" content="{$facebook_image_src}" />
		<meta property="og:image:width" content="480" />
		<meta property="og:image:height" content="360" />
		{if $video_data.source_id == $_sources.localhost.source_id}
		<link rel="video_src" href="{$smarty.const._URL}/uploads/videos/{$video_data.url_flv}"/>
		<meta property="og:video:url" content="{$smarty.const._URL}/uploads/videos/{$video_data.url_flv}" />
		<meta property="og:video:type" content="video/mp4"/>
		<link rel="video_src" href="{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}"/>
		<meta property="og:video:url" content="{$smarty.const._URL}/uploads/videos/{$video_data.url_flv_raw}" />
		<meta property="og:video" content="{$smarty.const._URL}/uploads/videos/{$video_data.url_flv_raw}"> 
		<meta property="og:video:secure_url" content="{$smarty.const._URL}/uploads/videos/{$video_data.url_flv_raw}">
		{/if}
{/if}
<style type="text/css">{$theme_customizations}</style>
</head>
{if $tpl_name == "video-category"}
<body class="video-category catid-{$cat_id} page-{$gv_pagenumber}">
{elseif $tpl_name == "video-watch"}
<body class="video-watch videoid-{$video_data.id} author-{$video_data.author_user_id} source-{$video_data.source_id}{if $video_data.featured == 1} featured{/if}{if $video_data.restricted == 1} restricted{/if}">
{elseif $tpl_name == "article-category"}
<body class="article-category catid-{$cat_id}">
{elseif $tpl_name == "article-read"}
<body class="article-read articleid-{$article.id} author-{$article.author} {if $article.featured == 1} featured{/if}{if $article.restricted == 1} restricted{/if}">
{elseif $tpl_name == "page"}
<body class="page pageid-{$page.id} author-{$page.author}">
{elseif $tpl_name == "video-watch-episode"}
<body class="video-watch-episode series-{$episode_data.series_id}{if $episode_data.featured == 1} featured{/if}{if $episode_data.restricted == 1} restricted{/if}">
{elseif $tpl_name == "video-series"}
<body class="video-series">
{else}
<body>
{/if}
{if ($tpl_name == 'article-read' || $tpl_name == 'video-watch' || $tpl_name == 'video-watch-episode') && $comment_system_facebook}
<!-- Facebook Javascript SDK -->
<div id="fb-root"></div>
{literal}
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
window.fbAsyncInit = function () {
FB.init({
xfbml:false  // Will stop the fb like button from rendering automatically
});
};
</script>
{/literal}
{/if}
{if $maintenance_mode}
	<div class="alert alert-danger" align="center"><strong>Currently running in maintenance mode.</strong></div>
{/if}

<div class="container-fluid no-padding">
<header class="header-bg">
<div class="pm-top-head">
	<div class="row">
		<div class="col-xs-7 col-sm-4 col-md-4">
			<div class="header-logo">
			{if $_custom_logo_url != ''}
				<a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" rel="home"><img src="{$_custom_logo_url}" alt="{$smarty.const._SITENAME|escape}" title="{$smarty.const._SITENAME|escape}" border="0" /></a>
			{else}
				<h3><a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" rel="home">{$smarty.const._SITENAME}</a></h3>
			{/if}
			</div>
		</div>
		<div class="hidden-xs col-sm-4 col-md-4" id="pm-top-search">
			{if $p == "article"}
			<form action="{$smarty.const._URL}/article.php" method="get" id="search" class="pm-search-form" name="search" onSubmit="return validateSearch('true');">
				<div class="input-group">
					<input class="form-control" id="pm-search" size="16" name="keywords" type="text" placeholder="{$lang.submit_search}..." x-webkit-speech speech onwebkitspeechchange="this.form.submit();">
					<span class="input-group-btn">
						<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
					</span>
				</div><!-- /input-group -->
			</form>
			{else}
			<form action="{$smarty.const._URL}/search.php" method="get" id="search" class="pm-search-form" name="search" onSubmit="return validateSearch('true');">
				<div class="input-group">
					<input class="form-control" id="pm-search" size="16" name="keywords" type="text" placeholder="{$lang.submit_search}..." x-webkit-speech="x-webkit-speech" onwebkitspeechchange="this.form.submit();" {if $smarty.const._SEARCHSUGGEST == 1}onblur="fill();" autocomplete="off"{/if}>
					<input class="form-control" id="pm-video-id" size="16" name="video-id" type="hidden">
					<span class="input-group-btn">
						<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
					</span>
				</div><!-- /input-group -->
			</form>
			<div class="pm-search-suggestions hide-me">
				<ul class="pm-search-suggestions-list list-unstyled"></ul>
			</div>
			{/if}
		</div>

		{if $logged_in} 
		<div class="col-xs-5 col-sm-4 col-md-4">
			<ul class="list-inline navbar-pmuser">
				<li class="hidden-sm hidden-md hidden-lg">
					<a href="#" id="pm-top-mobile-search-show" class="" title="{$lang.submit_search}"><i class="mico mico-search"></i></a>
				</li>
				{if $smarty.const._MOD_SOCIAL && $logged_in}
				<li>
					<a href="#" id="pm-social-notifications-show" title="{$lang.notifications}">
						<i class="mico mico-notifications_none {if $smarty.const._MOD_SOCIAL && $logged_in && $notification_count > 0}mico-notifications_active{/if}"></i></a>
						{if $smarty.const._MOD_SOCIAL && $logged_in && $notification_count > 0}
							<span class="badge pm-social-notifications-count">{$notification_count}</span>
						{/if}
					</a>
					<div class="hide-me animated fast absoluteSlideInUp" id="pm-social-notifications-container">
						<div id="pm-social-notifications-response"></div>
					</div>
				</li>
				{/if}
				<li class="nav-menu-item">
					<div class="dropdown">
						<a href="#" data-toggle="dropdown" role="button" ><img src="{$s_avatar_url}" width="30" height="30" alt="" class="header-avatar"></a>
						<ul class="dropdown-menu animated fast absoluteSlideInUp">
							<li><a href="{$current_user_data.profile_url}">{$s_name}</a> 
							<a href="{$smarty.const._URL}/edit-profile.{$smarty.const._FEXT}" rel="tooltip" title="{$lang.edit_profile}" class="btn-nav-edit-profile"><i class="mico mico-settings md-16"></i></a></li>
							{if $is_admin == 'yes' || $is_moderator == 'yes' || $is_editor == 'yes'}
							<li><a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/index.php">{$lang.admin_area}</a></li>
							{/if}
							<li><a href="{$current_user_data.profile_url}">{if $smarty.const._MOD_SOCIAL}{$lang.my_channel|default:'My Channel'}{else}{$lang.my_profile}{/if}</a></li>
							<li><a href="{$smarty.const._URL}/playlists.{$smarty.const._FEXT}">{$lang.my_playlists}</a></li>
							{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}
							<li class="visible-sm visible-xs"><a href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}">{$lang.suggest}</a></li>
							{/if}
							{if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
							<li class="visible-sm visible-xs"><a href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}">{$lang.upload_video}</a></li>
							{/if}
							<li><a href="{$smarty.const._URL}/login.{$smarty.const._FEXT}?do=logout">{$lang.logout}</a></li>
						</ul>
					</div>
				</li>
			</ul>
		</div>
		{else}
		<div class="col-xs-5 col-sm-4 col-md-4">
			<ul class="list-inline navbar-pmuser">
				<li class="hidden-sm hidden-md hidden-lg"><a href="#" id="pm-top-mobile-search-show" class="" title="{$lang.submit_search}"><i class="mico mico-search"></i></a></li>
				<li><a class="btn btn-sm btn-default ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#modal-login-form">{$lang.sign_in|default:'Sign in'}</a></li>
				{if $logged_in != '1' && $allow_registration == '1'}
					{if $allow_facebook_login || $allow_twitter_login || $allow_google_login}
					<li class="hidden-xs"><a class="btn btn-sm btn-success ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#modal-register-form">{$lang.register}</a></li>
					{else}
					<li class="hidden-xs"><a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}" class="btn btn-sm btn-success">{$lang.register}</a></li>
					{/if}
				{/if}
			</ul>
		</div>
		{/if}
	</div><!--.row-->
	</div><!--.pm-top-head-->

	<div class="pm-top-nav">
		<div class="row">
			<div class="col-xs-12 col-sm-8 col-md-8">
				<ul class="nav nav-tabs">
					<li><a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" class="wide-nav-link hidden-xs">{$lang.homepage}</a></li>
					<li class="dropdown">
					<a href="{$smarty.const._URL}/{if $smarty.const._SEOMOD}browse.{$smarty.const._FEXT}{else}category.php{/if}" class="dropdown-toggle wide-nav-link disabled hidden-xs hidden-sm" data-target="#" role="button" data-toggle="dropdown">{$lang.videos}<b class="caret"></b></a>
					<a href="#" class="dropdown-toggle wide-nav-link disabled visible-xs visible-sm" data-target="#" role="button" data-toggle="dropdown">{$lang.videos}<b class="caret"></b></a><!--mobile-->
					<ul class="dropdown-menu animated fast absoluteSlideInUp">
						<li class="visible-xs visible-sm"><a href="{$smarty.const._URL}/{if $smarty.const._SEOMOD}browse.{$smarty.const._FEXT}{else}category.php{/if}">{$lang.browse_categories|default:"Browse Categories"}</a></li><!--mobile-->
						<li><a href="{$smarty.const._URL}/newvideos.{$smarty.const._FEXT}">{$lang.new_videos}</a></li>
						<li><a href="{$smarty.const._URL}/topvideos.{$smarty.const._FEXT}">{$lang.top_videos}</a></li>
						{dropdown_menu_video_categories max_levels=3}
					</ul>
					</li>
					
					{if $smarty.const._MOD_SERIES == 1}
					<li class="dropdown">
						<a href="{$smarty.const._URL}/{if $smarty.const._SEOMOD}series/{else}series.php{/if}" class="dropdown-toggle wide-nav-link disabled hidden-xs hidden-sm" data-target="#" role="button" data-toggle="dropdown">{$lang._series|default:"Series"}<b class="caret"></b></a>
						<a href="#" class="dropdown-toggle wide-nav-link disabled visible-xs visible-sm" data-target="#" role="button" data-toggle="dropdown">{$lang._series|default:"Series"}<b class="caret"></b></a><!--mobile-->

						<ul class="dropdown-menu animated fast absoluteSlideInUp">
							<li class="visible-xs visible-sm"><a href="{$smarty.const._URL}/{if $smarty.const._SEOMOD}series/{else}series.php{/if}">{$lang.browse_series|default:"Browse Series"}</a></li><!--mobile-->
							{$list_genres}
						</ul>
					</li>
					{/if}

					{if $smarty.const._MOD_ARTICLE == 1}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle wide-nav-link" data-toggle="dropdown">{$lang.articles} <b class="caret"></b></a>
						<ul class="dropdown-menu animated fast absoluteSlideInUp">
						{dropdown_menu_article_categories max_levels=3}
						</ul>
					</li>
					{/if}

					{if $logged_in}
					<li class="dropdown">
						<a href="{$smarty.const._URL}/playlists.{$smarty.const._FEXT}" class="dropdown-toggle wide-nav-link disabled hidden-xs hidden-sm" data-target="#" role="button" data-toggle="dropdown">{$lang.my_playlists}<b class="caret"></b></a>
						<a href="#" class="dropdown-toggle wide-nav-link disabled visible-xs visible-sm" data-target="#" role="button" data-toggle="dropdown">{$lang.my_playlists}<b class="caret"></b></a><!--mobile-->
						<ul class="dropdown-menu animated fast absoluteSlideInUp">
							{foreach from=$s_user_playlists key=k item=playlist_data}
								{if $playlist_data.type == $smarty.const.PLAYLIST_TYPE_HISTORY}
								<li><a href="{$playlist_data.playlist_href}"><i class="mico mico-hourglass_full"></i>{$playlist_data.title}</a></li>
								{/if}
								{if $playlist_data.type == $smarty.const.PLAYLIST_TYPE_FAVORITES}
								<li><a href="{$playlist_data.playlist_href}"><i class="mico mico-favorite"></i>{$playlist_data.title}</a></li>
								{/if}
								{if $playlist_data.type == $smarty.const.PLAYLIST_TYPE_LIKED}
								<li><a href="{$playlist_data.playlist_href}"><i class="mico mico-thumb_up"></i>{$playlist_data.title}</a></li>
								{/if}
								{if $playlist_data.type == $smarty.const.PLAYLIST_TYPE_WATCH_LATER}
								<li><a href="{$playlist_data.playlist_href}"><i class="mico mico-watch_later"></i>{$playlist_data.title}</a></li>
								{/if}
							{/foreach}

							{if pm_count($s_user_playlists) > 4}
							{foreach from=$s_user_playlists key=k item=playlist_data}
								{if $playlist_data.type == $smary.const.PLAYLIST_TYPE_CUSTOM}
								<li><a href="{$playlist_data.playlist_href}"><i class="mico mico-playlist_play"></i>{$playlist_data.title}</a></li>
								{/if}
							{/foreach}
							{/if}
						</ul>
					</li>
					{/if}
				</ul>
			</div>

			<div class="hidden-xs col-sm-4 col-md-4">
				{if $logged_in}
					{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1' && $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
					<a class="btn btn-sm btn-primary ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#modal-addvideo">{$lang.add_video|default:'Add Video'}</a>
					{else}
						{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}
						<a href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}" class="btn btn-sm btn-primary">{$lang.suggest}</a>
						{elseif $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
						<a href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}" class="btn btn-sm btn-primary">{$lang.upload_video}</a>
						{/if}
					{/if}
				{/if}
			</div>
		</div>
	</div>
</header>


{if ! $logged_in}
	{include file="modal-user-login.tpl"}
	{if $allow_registration == '1'}
	{include file="modal-user-register.tpl"}
	{/if}
{/if}

{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1' && $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
	{include file="modal-addvideo.tpl"}
{/if}
<a id="top"></a>

<div class="mastcontent-wrap">
{if $ad_1 != ''}
<div class="pm-ads-banner" align="center">{$ad_1}</div>
{/if}