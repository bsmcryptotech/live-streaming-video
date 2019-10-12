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
// | Copyright: (c) 2004-2019 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

session_start();
require('config.php');
require_once('include/islogged.php');

if ( ! _MOD_SERIES && ! (is_admin() || (is_moderator() && mod_can('manage_videos'))))
{
    redirect_404();
}

$sortby = ( ! in_array($_GET['sortby'], array('views', 'date', 'rating', 'title'))) ? '' : $_GET['sortby'];
$order = ( ! in_array($_GET['order'], array('desc', 'asc', 'DESC', 'ASC'))) ? '' : $_GET['order'];
$page = (int) $_GET['page'];
$page = ( ! $page) ? 1 : $page;
$is_single = false;
$genre_id = 0;
$genres = get_genres();

if ( ! empty($_GET['s']))
{
    foreach ($genres as $k => $genre)
    {
        if ($_GET['s'] == $genre['tag'])
        {
            $genre_id = $genre['id'];
            break;
        }
    }
}

if ( ! empty($_GET['s']) && ! $genre_id)
{
    $series = get_series_by_slug($_GET['s']);
    $is_single = true;
}
else 
{
    $start = ($page * $config['series_per_page']) - $config['series_per_page'];
    $series = get_series_list(array(), $sortby, $order, $start, $config['series_per_page'], (($genre_id) ? array($genre_id) : null));
}

$total_items = ($genre_id) ? $genres[$genre_id]['total_series'] : $config['total_series'];
if ($total_items > $config['series_per_page'] && ! $is_single)
{
    $filename = (_SEOMOD)
        ? 'series/'. (($genre_id) ? $genres[$genre_id]['tag'] .'/' : '') .'page-'. $page .'/'
        : 'series.php';
    $append_url = (_SEOMOD)
        ? ''
        : 's='. $genres[$genre_id]['tag'];

    $pagination = generate_smart_pagination($page, $total_items, $config['series_per_page'], 1, $filename, $append_url, _SEOMOD);
}

$featured_items = $new_episodes = $top_episodes = array(); 
if ( ! $is_single && $page <= 1)
{
    // get featured series and episodes
    $featured_items = get_series_list(array(), 'RAND()', null, 0, (int) $config['homepage_featured_limit'], (($genre_id) ? array($genre_id) : null), array(), 'featured', '');
    foreach ($featured_items as $k => $v)
    {
        $featured_items[$k]['_item_type_'] = 'series';
    }
    $total_featured_episodes = 0;

    $featured_episodes = get_featured_episodes_list('RAND()', null, 0, (int) $config['homepage_featured_limit'], $total_featured_episodes);
    foreach ($featured_items as $k => $v)
    {
        $v['_item_type_'] = 'episode';
        $featured_items[] = $v;
    }
    unset($total_featured_episodes, $featured_episodes);

    if ( ! $genre_id)
    {
        $new_episodes = get_episode_list(array(), 0, 'added', 'DESC', 0, 10, true);
        $top_episodes = get_top_episodes_list($config['top_episodes_sort'], $config['top_videos']);

        // pull out featured categories data
        $featured_genres_data = array();
        $featured_genres = ($config['featured_genres'] != '') ? unserialize($config['featured_genres']) : array();
        if (pm_count($featured_genres) > 0)
        {
            foreach ($featured_genres as $k => $f_genre_id)
            {
                // $featured_genres_data[$f_genre_id] = get_series_list(array(), 'date', 'DESC', 0, 10, array($f_genre_id));
                $featured_genres_data[$f_genre_id] = get_series_list(array(), 'series_id', 'DESC', 0, 10, array($f_genre_id));
            }
        }
    }
}

$smarty->assign('genre_id', $genre_id);
$smarty->assign('genres', $genres);
$smarty->assign('list_genres', list_genres());

if ($is_single)
{
    $smarty->assign('series_data', $series);
    $smarty->assign('seasons_data', get_series_seasons($series['series_id']));
    $smarty->assign('related_series',  get_series_list(array(), 'RAND()', null, 0, 5));
}
else
{
    $smarty->assign('featured_items', $featured_items);
    $smarty->assign('new_episodes', $new_episodes);
    $smarty->assign('top_episodes', $top_episodes);
    $smarty->assign('series', $series);
    $smarty->assign('pagination', $pagination);
    $smarty->assign('featured_genres_data', $featured_genres_data);
}

$meta_title = _SITENAME; // EDITME review
$meta_description = $meta_keywords = '';

if ($is_single || $genre_id)
{
    if ($genre_id)
    {
        $meta_title = ( ! empty($genres[$genre_id]['meta_title']))
            ? $genres[$genre_id]['meta_title'] 
            : $genres[$genre_id]['name'];
        $meta_description = ( ! empty($genres[$genre_id]['meta_description']))
            ? $genres[$genre_id]['meta_description']
            : generate_excerpt(str_replace('"', '&quot;', $genres[$genre_id]['description']), 150) .'...';
        $meta_keywords = $genres[$genre_id]['meta_keywords'];
    }
    else
    {
        $meta_title = $series['title'];
        $meta_description = ( ! empty($series['meta_description'])) 
            ? $series['meta_description'] 
            : generate_excerpt(str_replace('"', '&quot;', $series['description']), 150) .'...';
        $meta_keywords = $series['meta_keywords'];
    }
    $meta_description = ($meta_description == '...') ? '' : $meta_description;
}
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_keywords', $meta_keywords);
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);

$smarty->display(($is_single) ? 'video-series-page.tpl' : 'video-series.tpl');