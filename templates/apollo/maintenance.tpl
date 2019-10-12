<!DOCTYPE html>
<!--[if IE 7 | IE 8]>
<html class="ie" dir="{if $smarty.const._IS_RTL == '1'}rtl{else}ltr{/if}" lang="en">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html dir="{if $smarty.const._IS_RTL == '1'}rtl{else}ltr{/if}" lang="en">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1024,maximum-scale=1.0">
<title>{$meta_title}</title>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=edge,chrome=1">  
{if $no_index == '1' || $smarty.const._DISABLE_INDEXING == '1'}
<meta name="robots" content="noindex,nofollow">
<META NAME="GOOGLEBOT" CONTENT="NOINDEX, NOFOLLOW">
{/if}
<meta name="title" content="{$meta_title}" />
<meta name="keywords" content="{$meta_keywords}" />
<meta name="description" content="{$meta_description}" />
<link rel="shortcut icon" href="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/favicon.ico">
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/css/bootstrap.min.css">
<!--[if lt IE 9]>
<script src="//css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/css/apollo.css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700" rel="stylesheet" type="text/css">
<style type="text/css">{$theme_customizations}</style>
</head>
<body>

<div id="content">
    <div class="container-fluid">
        {if $_custom_logo_url != ''}
            <a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" rel="home"><img src="{$_custom_logo_url}" alt="{$smarty.const._SITENAME|escape}" title="{$smarty.const._SITENAME|escape}" border="0" /></a>
        {else}
            <h1><a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" rel="home">{$smarty.const._SITENAME}</a></h1>
        {/if}
        <p></p>
        <div class="alert alert-danger my-3" align="center">
        {$maintenance_display_message}      
        </div>
    </div><!-- .container -->
</div>

<div align="center">
<small>
    {if $smarty.const._POWEREDBY == 1}{$lang.powered_by}<br />{/if}
    &copy; {$smarty.now|date_format:'%Y'} {$smarty.const._SITENAME}. {$lang.rights_reserved}
</small>
</div>

{$smarty.const._HTMLCOUNTER}
</body>
</html>