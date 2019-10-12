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

$episode_data = ( ! empty($_GET['s'])) ? get_episode_by_url_slug($_GET['s']) : get_episode((int) $_GET['id']);
if (empty($episode_data))
{
    redirect_404();
}
$seasons = get_series_seasons($episode_data['series_id']);
$video_is_restricted = ( ! $logged_in && $episode_data['restricted'] == '1') ? true : false;
if (update_view_count($episode_data['id'], $episode_data['site_views']))
{
    add_to_chart($episode_data['uniq_id'], IS_EPISODE);
    update_series_views($episode_data['series_id']);
}

$tags = '';
if (pm_count($episode_data['tags']) > 0) 
foreach ($episode_data['tags'] as $k => $tag)
{
    $tags .= $tag['href'].", ";
}
$tags = substr($tags, 0, -2);

if(isset($_COOKIE[COOKIE_AUTHOR]) && $_COOKIE[COOKIE_AUTHOR] != '')
    $smarty->assign('guestname', str_replace( array('"', '>', '<'), "", $_COOKIE[COOKIE_AUTHOR]) );
else
    $smarty->assign('guestname', '');
$must_sign_in = sprintf($lang['must_sign_in'], _URL."/login."._FEXT, _URL."/register."._FEXT);
$smarty->assign('must_sign_in', $must_sign_in);

$player_html_json = array();
foreach ($episode_data['media_sources'] as $index => $ms)
{
    $player_html_json[$index] = $ms['player_html'];
}

$twitter_status  = '';
$twitter_status  = $episode_data['video_title'];
$twitter_status .= ' '. makevideolink($episode_data['uniq_id'], $episode_data['video_title'], $episode_data['video_slug']);
$twitter_status = urlencode($twitter_status);

$most_liked_comment = false;
if ( ! $video_is_restricted && $episode_data['allow_comments'] == 1 && $config['comment_system'] == 'on')
{   
    $comment_list = get_comment_list($episode_data['uniq_id'], 1);
    $comment_count = count_entries('pm_comments', 'uniq_id', $episode_data['uniq_id']."' AND approved='1");
    $mod_can = mod_can();
    
    if ($userdata['power'] == U_ADMIN || ($userdata['power'] == U_MODERATOR && $mod_can['manage_comments']))
    {
        $smarty->assign('can_manage_comments', true);
    }
    else
    {
        $smarty->assign('can_manage_comments', false);
    }
    
    $comment_pagination_obj = '';
    if ($comment_count > $config['comments_page'])
    {
        $comment_pagination_obj = generate_comment_pagination_object($episode_data['uniq_id'], 1, $comment_count, $config['comments_page']);
    }
    if ($comment_count > 0)
    {
        $most_liked_comment = get_most_liked_comment($episode_data['uniq_id']);
        $most_liked_comment = (array) $most_liked_comment[0];
        
        if ($most_liked_comment['up_vote_count'] <= 2)
        {
            $most_liked_comment = false;
        }
        
        // remove duplicate
        if ($config['comment_default_sort'] == 'score' && is_array($most_liked_comment))
        {
            unset($comment_list[0]);
        }
    }
}
else
{
    if ($config['comment_system'] == 'off')
    {
        $episode_data['allow_comments'] = 0;
    }
    $comment_list = array();
    $comment_count = 0;
    $smarty->assign('can_manage_comments', false);
}

$facebook_image_src = ($episode_data['social_share_img_url'] != '') ? $episode_data['social_share_img_url'] : str_replace('mqdefault','0', show_thumb($episode_data['uniq_id']));

// Previous and Next episodes 
$prev_episode_data = $next_episode_data = false;
foreach ($seasons[$episode_data['season_no']] as $k => $season_episode)
{
    if ($season_episode['episode_no'] == $episode_data['episode_no'] - 1)
    {
        $prev_episode_data = $season_episode;
    }

    if ($season_episode['episode_no'] == $episode_data['episode_no'] + 1)
    {
        $next_episode_data = $season_episode;
    }

    if ($prev_episode_data && $next_episode_data)
    {
        break;
    }
}
// if currently playing is the last episode for this season, provide next season's first episode
if ( ! $next_episode_data && is_array($seasons[$episode_data['season_no'] + 1]))
{
    $next_episode_data = $seasons[$episode_data['season_no'] + 1][0];
}

$smarty->assign('player_html_json', json_encode($player_html_json));
$smarty->assign('seasons_data', $seasons);
$smarty->assign('episode_data', $episode_data);
$smarty->assign('prev_episode_data', $prev_episode_data);
$smarty->assign('next_episode_data', $next_episode_data);
$smarty->assign('uniq_id', $episode_data['uniq_id']);
$smarty->assign('tags', $tags);
// comments
$smarty->assign('guests_can_comment', ($video_is_restricted) ? 0 : $config['guests_can_comment']);
$smarty->assign('comment_list', $comment_list);
$smarty->assign('most_liked_comment', $most_liked_comment);
$smarty->assign('comment_count', $comment_count);
$smarty->assign('comment_pagination_obj', $comment_pagination_obj);
// rating
$smarty->assign('bin_rating_vote_value', bin_rating_user_has_voted($episode_data['uniq_id']));

// social sharing
$smarty->assign('twitter_status', $twitter_status);
$smarty->assign('facebook_image_src', $facebook_image_src);
$smarty->assign('facebook_like_title', urlencode($episode_data['video_title']));
$smarty->assign('facebook_like_href', urlencode($episode_data['url']));
$smarty->assign('show_addthis_widget', $config['show_addthis_widget']);
// user
$smarty->assign('user_id', $userdata['id']);

// HTML meta tags
$smarty->assign('meta_title', htmlspecialchars($episode_data['video_title']));
$smarty->assign('meta_keywords', htmlspecialchars($episode_data['tags_compact']));
$smarty->assign('meta_description', generate_excerpt(str_replace('"', '&quot;', $episode_data['excerpt']), 150) .'...');
$smarty->assign('template_dir', $template_f);
$smarty->display('video-watch-episode.tpl');
