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
require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');


if ($_GET['video-id'] != '' && ctype_alnum($_GET['video-id']))
{
	header('Location: '. makevideolink($_GET['video-id']));
	exit();
}

$page = $_GET['page'];

if(empty($page) || !is_numeric($page) || $page == '')
	$page = 1;

$limit = _BROWSER_PAGE;
$from = $page * $limit - ($limit);
$total_results = 0;
$search_types = array('default', 'video', 'user');
$num_res = 0;

if(trim($_GET['keywords']) != '')
{
	$search_type = 'video';
	if (isset($_GET['t']) && in_array($_GET['t'], $search_types))
	{
		$search_type = $_GET['t'];
	}

	// cleanup search phrase
	$searchstring = trim($_GET['keywords']);
	$searchstring = urldecode($searchstring);
	$searchstring = str_replace(array("%", ",", "'", '"', '>', '<', '/'), '', $searchstring);
	$searchstring = substr($searchstring, 0, 80); // limit search phrase
	$searchstring = htmlspecialchars($searchstring, ENT_NOQUOTES);
	$searchstring = secure_sql($searchstring);
	
	// break search phrase into terms
	$terms = explode(' ', $searchstring);
	$sql_and_exclude_episodes = (_MOD_SERIES) ? '' : " AND video_type = ". IS_VIDEO;

	switch ($search_type)
	{
		default:
		case 'default':
		case 'video':
			
			// update hits for this search pharse
			if ($page == 1)
			{
				$sql = "SELECT COUNT(*) as total 
						FROM pm_searches 
						WHERE string = '". $searchstring ."'";
				$result = @mysql_query($sql);
				$row = @mysql_fetch_assoc($result);
				@mysql_free_result($sql_string_check);
				
				if ($row['total'] > 0)
				{
					@mysql_query("UPDATE pm_searches SET hits=hits+1 WHERE string = '".$searchstring."'");
				}
				else
				{
					@mysql_query("INSERT INTO pm_searches SET string = '".$searchstring."', hits = '1'");
				}
			}

			$series_list = array();
			if (_MOD_SERIES && $page == 1)
			{
				$sql = "SELECT series_id
						FROM pm_series 
						WHERE title LIKE '%". $searchstring ."%'
						LIMIT 0, 10";
				if ($result = mysql_query($sql))
				{
					while ($row = mysql_fetch_assoc($result))
					{
						$series_list[] = $row['series_id'];
					}
					if ( ! empty($series_list))
						$series_list = get_series_list($series_list);
				}
			}

			$sql = "SELECT SQL_CALC_FOUND_ROWS id, MATCH(video_title) AGAINST ('$searchstring') AS score, video_type  
					FROM pm_videos 
			        WHERE added <= '". $time_now_minute ."' AND (MATCH(video_title) AGAINST ('$searchstring')  OR ((video_title LIKE '%".$searchstring."%') OR (description LIKE '%".$searchstring."%')))
			        $sql_and_exclude_episodes
					ORDER BY score DESC 
					LIMIT ".$from.", ".$limit;

			$result = @mysql_query($sql);

			// get total found rows
			$sql = "SELECT FOUND_ROWS()";
			$result2 = mysql_query($sql);
			$num_res = mysql_fetch_array($result2);
			$num_res = (int) $num_res[0];
			$total_results = $num_res;
			
			if ($num_res == 0 && (strlen($searchstring) >= 2))
			{
				// backup mode
				$where = '';
				$and = '';
				$terms = explode(' ', $searchstring);
				$limit_terms = 10; // limit query terms
				$searched_terms = 0;
				$used_words = array();
				
				foreach ($terms as $k => $term)
				{
					$term = trim($term, "\"'\n\r.,-_()[]{} ");
					
					if (strlen($term) >= 2 && !in_array($term, $used_words))
					{
						$where .= "{$and} ((video_title LIKE '%".$term."%') OR (description LIKE '%".$term."%')) ";
						$and = ' AND ';
						$searched_terms++;
						$used_words[] = $term;
					}
					
					if ($searched_terms >= $limit_terms)
					{
						break;
					}
				}
				
				if (pm_count($terms) > 1)
				{
					$where .= " OR ((video_title LIKE '%".$searchstring."%') OR (description LIKE '%".$searchstring."%'))";
				}
				
				$sql = "SELECT SQL_CALC_FOUND_ROWS id, video_type
						FROM pm_videos
						WHERE added <= '". $time_now_minute ."' AND (". $where .") 
						$sql_and_exclude_episodes
						LIMIT ".$from.", ".$limit;
				$result = @mysql_query($sql);
				
				// get total found rows
				$sql = "SELECT FOUND_ROWS()";
				$result2 = mysql_query($sql);
				$num_res = mysql_fetch_array($result2);
				$num_res = (int) $num_res[0];
				$total_results = $num_res;
			}
			
		break;
		
		case 'user':
			
			if (username_to_id($searchstring) > 0)
			{
				$sql = "SELECT COUNT(*) as total  
						 FROM pm_videos
						WHERE submitted = '". $searchstring ."'
						  AND added <= '". $time_now_minute ."' 
						  $sql_and_exclude_episodes
						ORDER BY id DESC";

				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				$total_results = $row['total'];
				mysql_free_result($result);
				$result = false;

				if ($total_results > 0)
				{
					$sql = "SELECT id, video_type  
							FROM pm_videos
							WHERE submitted = '". $searchstring ."'
							  AND added <= '". $time_now_minute ."' 
							  $sql_and_exclude_episodes
							ORDER BY id DESC  
							LIMIT ".$from.", ".$limit;
					$result = mysql_query($sql);
				}
			}
			else 
			{
				$result = false;
			}
			
		break;
		
	} // end switch
	
	$list = array();
	
	if ($total_results > 0)
	{
		$i = 0;
		$video_ids = $episode_video_ids = $episode_list = array();
		while ($row = mysql_fetch_array($result)) 
		{
			if ($row['video_type'] == IS_VIDEO)
			{
				$video_ids[] = $row['id'];
			}
			else 
			{
				$episode_video_ids[] = $row['id'];
			}
		}
		mysql_free_result($result);
		$list = get_video_list('', '', 0, 0, 0, $video_ids);
		if ( ! empty($episode_video_ids))
		{
			$sql = "SELECT pm_episodes.episode_id 
					FROM pm_videos 
					JOIN pm_episodes ON (pm_videos.uniq_id = pm_episodes.uniq_id) 
					WHERE pm_videos.id IN (". implode(',', $episode_video_ids) .")"; 
			if ($result = mysql_query($sql))
			{
				$episode_ids = array();
				while ($row = mysql_fetch_assoc($result))
				{
					$episode_ids[] = $row['episode_id'];
				}

				if ( ! empty($episode_ids))
				{
					$list = array_merge(
						get_episode_list($episode_ids, 0, null, null, 0, null, true), 
						$list
					);
				}
			}
		}
	}
	else
	{
		$error_msg = $lang['search_results_msg1'];
	}
}
else
{
	$error_msg = $lang['search_results_msg2'];
}

if (_MOD_SERIES && ! empty($series_list))
{
	$total_results += pm_count($series_list);
}

// generate pagination
$pagination = '';
if ($total_results > $limit)
{
	$url_searchstring = stripslashes($searchstring);
	$url_searchstring = str_replace(array('"', "'", '&'), '', $url_searchstring);

	$append_url = 'keywords='. $url_searchstring;
	if ($search_type != '' && $search_type != 'video')
	{
		$append_url .= '&t='.$search_type;
	}
	$filename = 'search.php';

	$pagination = generate_smart_pagination($page, $total_results, $limit, 1, $filename, $append_url, 0);	
}

// define meta tags & common variables
$meta_title = $lang['search_results'].': "'.$searchstring.'"';
if(!empty($page)) {
	$meta_title .= ' - '.sprintf($lang['page_number'], $page);
}
$meta_title = sprintf($meta_title, _SITENAME);
$meta_description = $meta_title;
// end

$smarty->assign('error_msg', $error_msg);
$smarty->assign('searchstring', htmlspecialchars($_GET['keywords']));
$smarty->assign('series_results', $series_list);
$smarty->assign('results', $list);
$smarty->assign('pagination', $pagination);
// --- DEFAULT SYSTEM FILES - DO NOT REMOVE --- //
$smarty->assign('meta_title', htmlspecialchars($meta_title));
$smarty->assign('meta_description', htmlspecialchars($meta_description));
$smarty->assign('template_dir', $template_f);
$smarty->display('video-search.tpl');