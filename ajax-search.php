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

error_reporting(0);
session_start();
@header('Content-Type: text/html; charset=UTF-8;');
require('config.php');
require_once('include/functions.php');
ob_end_clean();

$output 	 = '';

$kw = trim($_POST['queryString']);
// Is there a posted query string?
if ($kw != '') 
{
	$kw = secure_sql($kw);
	$kw = str_replace(array('%', ','), '', $kw);
	$items = array();
	$sql_and_exclude_episodes = (_MOD_SERIES) ? '' : " AND video_type = ". IS_VIDEO;
	
	if(strlen($kw) >= 3)
	{
		$num_res = 0;
		if(strlen($kw) > 3)
		{
			if (_MOD_SERIES)
			{
				$sql = "SELECT pm_series.* 
						FROM pm_series 
						WHERE title LIKE '%". $kw ."%'
						LIMIT 0, 3";
				if ($result = mysql_query($sql))
				{
					while ($row = mysql_fetch_assoc($result))
					{
						$row['video_type'] = IS_SERIES;
						$items[] = $row;
					}
				}
			}

			$sql = "SELECT pv.video_title, pv.uniq_id, pv.yt_id, pv.yt_thumb, pv.source_id, pv.video_slug, pv.video_type, pe.episode_id, 
						pe.series_id, pe.season_no, pe.episode_no, pe.release_date 
					FROM pm_videos pv 
					LEFT JOIN pm_episodes pe ON (pv.uniq_id = pe.uniq_id) 
					WHERE pv.video_title LIKE '%". $kw ."%'
					  AND pv.added <= '". $time_now_minute ."' 
					  $sql_and_exclude_episodes
					ORDER BY id DESC 
					LIMIT 0, 10";
			if ($result = mysql_query($sql))
			{
				while ($row = mysql_fetch_assoc($result))
				{
					$items[] = $row;
				}
			}
		}

		if ( ! empty($items))
		{
			foreach ($items as $k => $item)
			{
				if ($item['video_type'] == IS_SERIES)
				{
					$output .= '<li onClick="fill(\''.$item['title'].'\');" data-video-id="' . $item['uniq_id'] . '">';
					$output .= '<a href="'. series_url($item) .'">';
					if (_THUMB_FROM == 2)	//	Localhost
					{
						$output .= '<img src="'. series_image_url($item) .'" width="45" align="absmiddle" class="pm-sl-thumb opac7" alt="'. htmlentities($item['title']).'" />';
					}
					$output .= $item['title'];
					$output .= ( ! empty($item['release_year'])) ? ' ('. $item['release_year'] .')' : '';
					$output .= '</a>';
					$output .= '</li>';
				}
				else
				{
					$output .= '<li onClick="fill(\''.$item['video_title'].'\');" data-video-id="' . $item['uniq_id'] . '">';
					if ($item['video_type'] == IS_EPISODE)
					{
						$output .= '<a href="'. episode_url($item) .'">';
					}
					else
					{
						$output .= '<a href="'. makevideolink($item['uniq_id'], $item['video_title'], $item['video_slug']) .'">';
					}
					
					if (_THUMB_FROM == 2)	//	Localhost
					{
						if ($item['video_type'] == IS_EPISODE)
						{
							$output .= '<img src="'. episode_image_url($item) .'" width="45" align="absmiddle" class="pm-sl-thumb opac7" alt="'. htmlentities($item['video_title']).'" />';
						}
						else
						{
							$output .= '<img src="'. show_thumb($item['uniq_id'], 1, $item) .'" width="45" align="absmiddle" class="pm-sl-thumb opac7" alt="'. htmlentities($item['video_title']).'" />';
						}
					}
					$output .= $item['video_title'] .'</a>';
					$output .= '</li>';
				}
			}
		} 
		else 
		{
			$output = $lang['search_results_msg3'];
		}
	}
}
echo $output;
exit();