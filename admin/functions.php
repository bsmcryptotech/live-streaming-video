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

function get_catnamefromid($id) 
{
	$id = (int) $id;
	if ($id == 0)
		return '- Root -';

	$categories = load_categories();
	return $categories[$id]['name'];
}

function vnamefromvid($vid) 
{
	global $_pm_cache;
	
	$type = 'video';
	$cache_key = __FUNCTION__ . $vid;
	
	if (($title = $_pm_cache->get($cache_key)) !== false)
	{
		return $title;
	}
	
	if (strpos($vid, 'article') !== false)
	{
		$pieces = explode('-', $vid);
		$id = (int) $pieces[1];
		$q = mysql_query("SELECT title FROM art_articles WHERE id = '". $id ."'");
		$type = 'article';
	}
	else
	{
		$q = mysql_query("SELECT video_title FROM pm_videos WHERE uniq_id = '".$vid."'");
	}
	
	$r = mysql_fetch_assoc($q);

	if ($type == 'video')
	{
		$title = $r['video_title'];
	}
	else if ($type == 'article')
	{
		$title = $r['title'];
	}
	
	mysql_free_result($q);
	
	if ($title === null)
	{
		if ($type == 'video')
		{
			$q = mysql_query("SELECT video_title FROM pm_videos_trash WHERE uniq_id = '".$vid."'");
			$r = mysql_fetch_assoc($q);
			$title = $r['video_title'];
			mysql_free_result($q);
		}
		else if ($type == 'article')
		{
			// @todo when a Trash for Articles exists
		}
	}
	
	$title = (strlen($title) > 0) ? $title : 'n/a';
	
	$_pm_cache->add($cache_key, $title);
	
	return $title;
}


function a_list_videos($search_term, $search_type = 'video_title', $from = 0, $to = 20, $page = 1, $filter = "", $filter_value = "") 
{
	global $userdata;
	
	if( ! $page)	$page = 1;
	
	if(!empty($search_term) && $search_type == 'video_title' ) 
	{
		$query = mysql_query("SELECT * FROM pm_videos WHERE video_type = ". IS_VIDEO ." AND video_title LIKE '%".$search_term."%' ORDER BY added DESC");
	} 
	else if(!empty($search_term) && $search_type == 'yt_id' )
	{
		$query = mysql_query("SELECT * FROM pm_videos WHERE video_type = ". IS_VIDEO ." AND yt_id LIKE '".$search_term."' ORDER BY added DESC");
	} 
	else if(!empty($search_term) && $search_type == 'uniq_id' )
	{
		$query = mysql_query("SELECT * FROM pm_videos WHERE video_type = ". IS_VIDEO ." AND uniq_id LIKE '".$search_term."' ORDER BY added DESC");
	}
	else if(!empty($search_term) && $search_type == 'submitted' )
	{
		$query = mysql_query("SELECT * FROM pm_videos WHERE video_type = ". IS_VIDEO ." AND submitted LIKE '".$search_term."' ORDER BY added DESC");
	}
	else 
	{
		$sql = '';
		$orderby = 'added';
		$order = 'DESC';
		
		if($filter != '')
		{
			$sql = "SELECT * FROM pm_videos ";
			switch($filter)
			{
				case 'broken':
				
					$sql .= " WHERE video_type = ". IS_VIDEO ." AND status='".VS_BROKEN."' ";
					
				break;
				
				case 'restricted':
				
					$sql .= " WHERE video_type = ". IS_VIDEO ." AND status='".VS_RESTRICTED."' ";
					
				break;
				
				case 'unchecked':
				
					$sql .= " WHERE video_type = ". IS_VIDEO ." AND status='".VS_UNCHECKED."' AND source_id = '3' ";
					
				break;
				
				case 'localhost':
				
					$sql .= " WHERE video_type = ". IS_VIDEO ." AND source_id='1' ";
					
				break;
				
				case 'featured':
				
					$sql .= " WHERE video_type = ". IS_VIDEO ." AND featured='1' ";
					
				break;
				
				case 'category':
					
					if ($filter_value == 0)
					{
						$sql .= " WHERE video_type = ". IS_VIDEO ." AND category LIKE '' "; 
					}
					else
					{
						$sql .= " WHERE video_type = ". IS_VIDEO ." AND category LIKE '". $filter_value ."' 
								   OR category LIKE '". $filter_value .",%' 
								   OR category LIKE '%,". $filter_value ."' 
								   OR category LIKE '%,". $filter_value .",%' ";
					}
						   
				break;
				
				case 'source':
				
					$sql .= " WHERE video_type = ". IS_VIDEO ." AND source_id='". $filter_value ."' ";
					
				break;
				
				case 'access':
					
					$sql .= " WHERE video_type = ". IS_VIDEO ." AND restricted = '". $filter_value ."' ";
					
				break;
				
				case 'added': // sorting
					
					$sql .= " WHERE video_type = ". IS_VIDEO;
					$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
					$orderby =  'added';

				break;
				
				case 'views': // sorting
					
					$sql .= " WHERE video_type = ". IS_VIDEO;
					$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
					$orderby =  'site_views';
					
				break;
				
				case 'mostviewed': // sorting

					$sql .= " WHERE video_type = ". IS_VIDEO;
					$orderby = 'site_views';
					$order = 'DESC'; 
					
				break;

				case 'addedactive': // sorting
				
					$sql .= " WHERE video_type = ". IS_VIDEO ." AND added  < '". time() ."' ";
					$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
					$orderby =  'added';

				break;

				case 'scheduled': // sorting
				
					$sql .= " WHERE video_type = ". IS_VIDEO ." AND added  > '". time() ."' ";
					$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
					$orderby =  'added';

				break;
				
				case 'trash':
					$sql = "SELECT * FROM pm_videos_trash ";
				break;

				default:
					$sql .= " WHERE video_type = ". IS_VIDEO;
				break;
			}
			
			$sql .= " ORDER BY ". $orderby .' '. $order;
			$sql .= " LIMIT ".$from.", ".$to;
		}
		else
		{
			$sql = "SELECT * FROM pm_videos WHERE video_type = ". IS_VIDEO ." ORDER BY added DESC LIMIT ".$from.", ".$to;
		}
		$query = mysql_query($sql);
	}
	$count = mysql_num_rows($query);	
	$categories = load_categories();

	// LIST VIDEOS
	if($count >= 1) 
	{
		$videos = '';
		$sources = a_fetch_video_sources();
		
		$alt = 1;
		while($r = mysql_fetch_array($query)) 
		{
			$bin_rating_meta = false;
			if (function_exists('bin_rating_get_item_meta'))
			{
				$bin_rating_meta = bin_rating_get_item_meta($r['uniq_id']);
			}
			
			$alt++;
			$r['last_check'] = (int) $r['last_check'];
			$last_check = ($r['last_check']) ? time_since($r['last_check']) .' ago' : 'never';
			$status = '';
			$status_img = '';
			switch($r['status'])
			{
				default:
				case VS_UNCHECKED: 	$status = "Video Status: Unchecked";		$status_img = VS_UNCHECKED_IMG;		break;
				case VS_OK: 		$status = "Video Status: OK";				$status_img = VS_OK_IMG; 			break;
				case VS_BROKEN: 	$status = "Video Missing";					$status_img = VS_BROKEN_IMG; 		break;
				case VS_RESTRICTED:	$status = "Video Status: Geo-restricted";	$status_img = VS_RESTRICTED_IMG;	break;
			}
			//$status_img .= ".png";

			// Get video subtitles
			$video_subtitles = array();
			$video_subtitles = get_video_subtitles($r['uniq_id']);

			// Thumb
			$video_thumbnail = show_thumb($r['uniq_id'], 1, $r);

			//	Video row
			$tr_class = '';
			if ($r['added'] > time())
			{
				$tr_class = 'scheduled';
			} 
			else if ($r['restricted'] == '1') 
			{
				$tr_class = 'private';
			} 
			
			// "data-*" attributes @since 2.4 very useful as row identifiers
			$videos .= '<tr class="'. $tr_class .'" data-uniq-id="'. $r['uniq_id'] .'" data-video-id="'. $r['id'] .'">';

			//	checkbox
			$videos .= '<td align="center" class="text-center" width="3%">';
			
			if(in_array($r['source_id'], array(1, 2, 3, 5, 16)))
			{
				$videos .= "<input name=\"video_ids[]\" type=\"checkbox\" value=\"".$r['uniq_id']."\" id=\"".$r['id']."\" />";
//				$videos .= '<div class="custom-control custom-checkbox"><input type="checkbox" name="video_ids[]" value="'.$r["uniq_id"].'" id="'.$r["id"].'" class="custom-control-input form-check-input-styled-primary" /><label class="custom-control-label" for="'.$r["id"].'"><strong>select</strong></label></div>';
			}
			else
			{

				$videos .= "<input name=\"video_ids[]\" type=\"checkbox\" value=\"".$r['uniq_id']."\" />";
//				$videos .= '<div class="custom-control custom-checkbox"><input type="checkbox" name="video_ids[]" value="'.$r["uniq_id"].'" class="custom-control-input form-check-input-styled-primary" /><label class="custom-control-label"><strong>select</strong></label></div>';

				$status_img = VS_NOTAVAILABLE_IMG;
				$status = "Not Available";
			}
			
			//	Video Source Icon
			$source_icon = strtolower($sources[$r['source_id']]['source_name']);
			if ($r['source_id'] == 0)
			{
				$source_icon = 'embed';
			}
			else if ($r['source_id'] == 1 || $r['source_id'] == 2)
			{
				$tmp_parts = explode('.', $r['url_flv']);
				$source_icon = array_pop($tmp_parts);
				$source_icon = strtolower($source_icon);
			}
			else
			{
				$source_icon = str_replace('.', '', $source_icon);
			}
			
			$source_icon_title = ucfirst($sources[$r['source_id']]['source_name']);
			if ($sources[$r['source_id']]['source_name'] == '')
			{
				$source_icon_title = 'Embedded';
			}

			$videos .= '</td>';

			//	video source icon
			$videos .= '<td align="center" class="d-sm-none d-md-table-cell text-center" width="2%">';
			$videos .= ($filter != 'trash') ? ' <a href="videos.php?page=1&filter=source&fv='. $r['source_id'] .'" data-popup="tooltip" data-container="body" data-html="true" data-original-title="Filter by this source (<strong>'. $source_icon_title .'</strong>)">' : '';
			$videos .= '<div class="sprite '. $source_icon .'"></div>';
			($filter != 'trash') ? $videos .= ' </a>' : '';
			$videos .= '</td>';

			//	unique id
			$videos .= '<td align="center" class="d-sm-none d-md-table-cell text-center"><img src="'. $video_thumbnail .'" class="img-responsive" width="80"></td>';

			//	Video title
			$videos .= '<td><span style="visibility:hidden; display:none;">'.stripslashes($r['video_title']).'</span>';
			if ($r['featured'] == '1')
			{
				$videos .= '<a href="series.php?filter=featured" data-popup="tooltip" data-container="body" data-original-title="This video is Featured. Click to show only featured videos."><span class="badge badge-primary">FEATURED</span></a> ';
			}
			if ($r['added'] > time())
			{
					$videos .= ' <a href="videos.php?filter=scheduled" data-popup="tooltip" data-container="body" data-original-title="Show only SCHEDULED"><span class="badge badge-secondary">SCHEDULED</span></a>'; 
			}
			if ($r['restricted'] == '1')
			{
					$videos .= ' <a href="videos.php?filter=access" data-popup="tooltip" data-container="body" data-original-title="Show only PRIVATE"><span class="badge badge-dark">PRIVATE</span></a>'; 
			}

			$videos .= ' <a href="'. _URL.'/watch.php?vid='. $r['uniq_id'] .'" target="blank">'; 
			$videos .= stripslashes($r['video_title']);
			$videos .= ' </a>';

			$videos .= '<div class="float-right">';

			if ($bin_rating_meta)
			{
				$videos .= '<i class="mi-thumb-up text-muted opacity-50 mr-1"></i> <span class="text-success">'. pm_number_format($bin_rating_meta['up_vote_count']) .'</span>';
				$videos .= '&nbsp;&nbsp;';
				$videos .= '<i class="mi-thumb-down text-muted opacity-50 mr-1 ml-2"></i> <span class="text-danger">'. pm_number_format($bin_rating_meta['down_vote_count']) .'</span>';
			}
			
			if ( pm_count($video_subtitles) > 0 )
			{
				$videos .= '<span class="pm-sprite icon-subtitles ico-subtitles mt-1 opacity-50" data-popup="tooltip" data-container="body" data-original-title="This video has subtitles"></span>';
			}			
			
			$videos .= '</div>';
			
			$videos .= '</td>';

			
			//	category
			$videos .= '<td class="d-sm-none d-md-table-cell font-size-sm">';
			$video_cats = explode(',', $r['category']);
			foreach ($video_cats as $k => $cid)
			{
				$cid = (int) $cid;
				$videos .= ($k >= 1) ? ' / ' : '';
				if ($filter != 'trash')
				{
					$videos .= '<a href="videos.php?page=1&filter=category&fv='. $cid .'" data-popup="tooltip" ';
					if ($cid == 0)
					{
						$videos .= ' title="List uncategorized videos only">';
						$videos .= 'Uncategorized';
					}
					else
					{
						$videos .= ' title="List videos from '. $categories[ $cid ]['name'] .' only">';

						$videos .= $categories[ $cid ]['name'];
					}
					$videos .= '</a> ';
				}
				else
				{
					if ($cid == 0)
					{
						$videos .= 'Uncategorized';
					}
					else
					{
						$videos .= $categories[ $cid ]['name'];
					}
				}
			}
			$videos .= '</td>';

			//	date
			$videos .= '<td align="center" class="text-center">';
			$videos .= ' <span style="font-size:0.1pt; position:absolute; color:#fff; display:none;" class="font-size-sm">'. $r['added'] .'</span>';
			$videos .= '<span data-popup="tooltip" data-container="body" data-original-title="'. date('l, F j, Y g:i A', $r['added']) .'" class="font-size-sm">';
			$videos .=  date('M d, Y', $r['added']);
			$videos .= '</span>';
			$videos .= '</td>';
			//	views
			$videos .= '<td align="center" class="text-center">';
			$videos .= pm_number_format($r['site_views']);
			$videos .= '</td>';

			//	comments control
			$total_comments = count_entries('pm_comments', 'uniq_id', $r['uniq_id']);
			
			$videos .= '<td align="center" class="d-sm-none d-md-table-cell text-center">';
			$videos .= '<span class="font-size-sm">';
			$videos .= ' <a href="comments.php?vid='. $r['uniq_id'] .'">';
			$videos .= ($filter != 'trash') ? 'View' : 'View ('. $total_comments .')';
			$videos .= ' </a>';
						
			if ((is_admin() || (is_moderator() && mod_can('manage_comments'))) && $filter != 'trash')
			{
				$videos .= ' | ';
				$videos .= ' <a href="#" ';
				$videos .= ' onClick=\'del_video_comments("'. $r['uniq_id'] .'", "'. $page .'", "'. $filter .'")\'>';
				$videos .= 'Delete ('. $total_comments .')';
			}
			$videos .= '</span>';
			$videos .= '</td>';

			//	video status icon
			$videos .= '<td align="center" style="text-align:center; width: 12px;">';
			if ( $status_img == VS_BROKEN_IMG )
			{
				$videos .= '<a href="videos.php?filter=broken&page=1">';
			} else {
				$videos .= '<a href="#">';
			}
			$videos .= '<div class="pm-sprite '. $status_img .'" id="status_'. $r['id'] .'" alt="" data-popup="tooltip" data-container="body" data-html="true" data-original-title="'.$status.' <br> Last checked: '.$last_check.'"></div>';
			$videos .= '</a>';
			$videos .= '</td>';

			//	actions
			$videos .= '<td align="center" class="text-center table-col-action">';
			$videos .= '<div class="list-icons">';
			
			if ($filter == 'trash')
			{
				$videos .= '  <a href="edit-video.php?vid='. $r['uniq_id'] .'&a=4&page='. $page .'&filter='. $filter .'&fv='. $filter_value .'" class="text-success font-weight-semibold mr-2" data-popup="tooltip" data-container="body" data-original-title="Restore form Trash"><i class="icon-undo"></i></a>';
				$videos .= '  <a href="#" class="text-danger font-weight-semibold" onClick=\'del_video_id("'. $r['uniq_id'] .'", "'. $page .'", "'. $filter .'")\' data-popup="tooltip" data-container="body" data-original-title="Delete forever"><i class="icon-bin2"></i></a>'; 
			}
			else
			{
				$videos .= ' <a href="edit-video.php?vid='. $r['uniq_id'] .'" class="list-icons-item mr-2"  data-popup="tooltip" data-container="body" data-original-title="Edit video"><i class="icon-pencil7"></i></a> ';
				$videos .= ' <a href="edit-video.php?vid='. $r['uniq_id'] .'&a=3&page='. $page .'&filter='. $filter .'&fv='. $filter_value .'" class="list-icons-item text-warning" data-popup="tooltip" data-container="body" data-original-title="Move to trash"><i class="icon-bin"></i></a>';
			}
			$videos .= '</div>';
			$videos .= '</td>';
			
			$videos .= '</tr>';
		}
	} 
	elseif($count == 0) 
	{
		$videos .= '<tr>';
		$videos .= ' <td colspan="11" align="center" class="text-center">';
		$videos .= 'No videos found. <a href="#addVideo" data-toggle="modal"><strong>Add a video now</strong></a>.';
		$videos .= ' </td>';	
		$videos .= '</tr>';
	}
	return $videos;
}

function a_list_temp($search_term, $search_type = 'video_title', $from = 0, $to = 20, $page = 1) {
	
	global $approve_nonce;
	
	$mimetype = array(	'flv' => 'video/x-flv', 
						'mov' => 'video/quicktime', 
						//'avi' => 'video/x-msvideo', 
						'divx' => 'video/x-divx', 
						'mp4' => 'video/mp4', 
						'wmv' => 'video/x-ms-wmv', 
						'bin' => 'application/octet-stream', 
						'avi' => 'video/avi',
						'mkv' => 'video/x-matroska',
						'asf' => 'video/x-ms-asf', 
						'wma' => 'audio/x-ms-wma', 
						'mp3' => 'audio/mpeg', 
						'm4v' => 'video/mp4', 
						'm4a' => 'audio/mp4', 
						'3gp' => 'video/3gpp', 
						'3g2' => 'video/3gpp2' 
						);

	if(!$page)	$page = 1;
	
	$query = mysql_query("SELECT * FROM pm_temp ORDER BY added DESC LIMIT ".$from.", ".$to);
	$count = mysql_num_rows($query);

	// LIST VIDEOS
	if($count >= 1) 
	{
		$videos = '';
		$alt	= 1;
		$sources = a_fetch_video_sources();
		while($r = mysql_fetch_assoc($query)) 
		{
			$col = ($alt % 2) ? 'table_row1' : 'table_row2';
			$alt++;
			
			$status = '';
			$status_img = '';
			switch($r['status'])
			{
				default:
				case VS_UNCHECKED: 	$status = "Video Status: Unchecked";		$status_img = VS_UNCHECKED_IMG;		break;
				case VS_OK: 		$status = "Video Status: OK";				$status_img = VS_OK_IMG; 			break;
				case VS_BROKEN: 	$status = "Video Missing";					$status_img = VS_BROKEN_IMG; 		break;
				case VS_RESTRICTED:	$status = "Video Status: Geo-restricted";	$status_img = VS_RESTRICTED_IMG;	break;
			}
			//$status_img .= ".png";
			
			//	video row
			$videos .= '<tr class="'. $col .'">';
			//	checkbox
			$videos .= '<td>';
			$videos .= ' <input name="video_ids[]" type="checkbox" value="'. $r['id'] .'" />';
			$videos .= '</td>';

			//	video source icon
			$videos .= '<td align="center" class="text-center" width="2%">';
			$videos .= '<div class="sprite '. strtolower($sources[$r['source_id']]['source_name']) .'" data-popup="tooltip" data-container="body" data-original-title="Source: '. ucfirst($sources[$r['source_id']]['source_name']).'"></div>';
			$videos .= '</td>';

			$videos .= '<td valign="top" style="vertical-align: top">';

			$thumb_url = _URL .'/'. _ADMIN_FOLDER .'/img/no-thumbnail.jpg';
			if (strpos($r['thumbnail'], 'http') === 0 || strpos($r['thumbnail'], '//') === 0)
			{
				$thumb_url = $r['thumbnail'];
			}
			elseif ($r['thumbnail'] != '')
			{
				$thumb_url = _THUMBS_DIR . $r['thumbnail'];
			}
			
			if ($r['source_id'] == $sources['localhost']['source_id'])
			{
				$filesize = readable_filesize( @filesize(_VIDEOS_DIR_PATH . $r['url']));
				$tmp_parts = explode('.', $r['url']);
				$buff_ext = array_pop($tmp_parts);
				$buff_ext = strtolower($buff_ext);
				$thumb_url = ($r['thumbnail'] != '') ? _THUMBS_DIR . $r['thumbnail'] : _URL .'/'. _ADMIN_FOLDER .'/img/no-thumbnail.jpg';
				$videos .= '
					<div class="stack-thumb">
					<img src="'.$thumb_url.'" alt="" border="0" name="video_thumbnail" class="img-responsive" width="80" />
					</div>';

				// $videos .= ' <strong>Size</strong>: '. $filesize .' / ';
				// $videos .= ' <strong>Type</strong>: '. $mimetype[$buff_ext];
			}
			else if ($r['source_id'] == $sources['youtube']['source_id'])
			{
				preg_match("/v=([^(\&|$)]*)/", $r['url'], $matches);
				$yt_id = $matches[1];
				$videos .= '
					<div class="stack-thumb">
					<img src="'. make_url_https($thumb_url) .'" alt="" border="0" name="video_thumbnail" class="img-responsive" width="80" />
					</div>';
			}
			else
			{
				$videos .= '<div class="stack-thumb">
					<img src="'. make_url_https($thumb_url) .'" alt="" border="0" name="video_thumbnail" class="img-responsive" width="80" />
					</div>';
			}
			$videos .= '</td>';
			
			//	video title
			$videos .= '<td valign="top" style="vertical-align: top">';
			$videos .= stripslashes($r['video_title']);

			$videos .= '<br><span class="font-size-sm cursor-pointer" data-toggle="collapse" data-target="#collapse-'. $r['id'] .'">Show details</span>
							<div class="collapse" id="collapse-'. $r['id'] .'">
								<div class="mt-3">';

				if ($r['source_id'] == $sources['localhost']['source_id'])
				{
					$videos .= '<div class="d-block mt-2 mb-2"><span class="text-uppercase font-weight-semibold d-block">VIDEO URL:</span> ';
					$videos .= '<a href="'. _VIDEOS_DIR . $r['url'] .'" rel="prettyPop[flash]" title="'. htmlentities($r['video_title']) .'" target="_blank"><small>'. _VIDEOS_DIR . $r['url'] .'</small></a>';
					$videos .= '</div>';
				} 
				else if ($r['source_id'] == $sources['youtube']['source_id'])
				{
					$videos .= '<div class="d-block mt-2 mb-2"><span class="text-uppercase font-weight-semibold d-block">VIDEO URL:</span> ';
					$videos .= '<span class="stack-preview"><a href="https://www.youtube.com/watch?v='. $yt_id .'&autoplay=1&v='. $yt_id .'" rel="prettyPop[flash]" title="'. htmlentities($r['video_title']) .'"><small>https://www.youtube.com/watch?v='. $yt_id .'</small></a></span>';
					$videos .= '</div>';
				}

			$videos .= '<span class="text-uppercase font-weight-semibold d-block">DESCRIPTION:</span> ';

			if (str_word_count($r['description'], 0) > 30)
			{
				preg_match('/^(.{1,255})\b/s', $r['description'], $matches);
				$excerpt = $matches[1];

				if(substr_count($excerpt, '<') != substr_count($excerpt, '>'))
				{
					$excerpt .= '>';
				}
				
				$videos .= '<span id="excerpt-'. $r['id'] .'">'. $excerpt .' ... </span>';
				$videos .= '<a href="#" id="show-more-'. $r['id'] .'" title="Show more">show more</a>';
				$videos .= '<span id="full-text-'. $r['id'] .'" style="display:none;">'. $r['description'] .'</span>';
				$videos .= '<br /><a href="#" id="show-less-'. $r['id'] .'" style="display:none;" title="Show less">show less</a>';
			}
			else
			{
				$videos .= ' '. $r['description'];
			}

			//	tags

			if( ! empty($r['tags'])) {
			$videos .= '<div class="d-block mt-2"><span class="text-uppercase font-weight-semibold d-block">TAGS:</span> ';
			$videos .= str_replace(",", ", ", $r['tags']);
			$videos .= '</div>';
			}

			$videos .= '</div></div>'; // collapsible

			$videos .= '</td>';
			//	date
			$videos .= '<td align="center" class="text-center">';
			//$videos .= '<small>';
			$videos .= '<span data-popup="tooltip" data-container="body" data-original-title="'. date('l, F j, Y g:i A', $r['added']) .'">';
			$videos .=  date('M d, Y', $r['added']);
			$videos .= '</span>';
			//$videos .= '</small>';
			$videos .= '</td>';
			//	submitted by 
			$videos .= '<td align="center" width="5%" class="text-center">';
			$videos .= ' <a href="'. get_profile_url($r) .'">'. $r['username'] .'</a>';
			$videos .= '</td>';
			//	category
			$videos .= '<td>';
			$videos .= make_cats($r['category']); 
			$videos .= '</td>';
			//	actions
			$videos .= '<td align="center" class="text-center table-col-action">';
			$videos .= ' <a href="pending-videos.php?a=approve&vid='. $r['id'] .'&page='. $page .'&_pmnonce='. $approve_nonce['_pmnonce'] .'&_pmnonce_t='. $approve_nonce['_pmnonce_t'] .'" class="list-icons-item text-success mr-2" data-popup="tooltip" data-container="body" data-original-title="Approve"><i class="icon-checkmark"></i></a> ';
			$videos .= ' <a href="edit-pending-video.php?id='. $r['id'] .'" class="list-icons-item mr-2"  data-popup="tooltip" data-container="body" data-original-title="Edit video"><i class="icon-pencil7"></i></a> ';
			$videos .= ' <a href="#" class="list-icons-item text-warning" data-popup="tooltip" data-container="body" data-original-title="Delete" onClick=\'del_temp_video_id("'. $r['id'] .'", "'. $page .'")\'><i class="icon-bin"></i></a>';

			$videos .= '</td>';
			
			$videos .= '</tr>';
		}
	} 
	else if($count == 0) 
	{
		$videos .= '<tr>';
		$videos .= ' <td colspan="9" align="center" class="text-center">';
		$videos .= 'No videos pending your approval.';
		$videos .= ' </td>';	
		$videos .= '</tr>';
	}
	return $videos;
}

function a_list_comments($search_term = '', $search_type = 'comment', $from = 0, $limit = 20, $page = 1, $filter = '') 
{
	global $config, $comments_nonce;
	
	if(!$from)	$from = 0;
	if(!$limit)	$limit = 20;
	if(!$page)	$page = 1;
	
	if ($config['allow_emojis'])
	{
		if ( ! class_exists('Emojione\\Client'))
		{
			include(ABSPATH .'include/emoji/autoload.php');
		} 
		$emoji_client = new Emojione\Client(new Emojione\Ruleset());
		$emoji_client->ascii = true;
		$emoji_client->unicodeAlt = false;
	}
	
	if($search_term != '') 
	{
		$sql = 'SELECT * FROM pm_comments WHERE ';
		switch($search_type)
		{
			default:
			case 'comment' : $sql .= 'comment';  break;
			case 'username' : $sql .= 'username'; break;
			case 'ip' : $sql .= 'user_ip'; break;
			case 'uniq_id' : $sql .= 'uniq_id'; break;
		}
			$sql .= " LIKE '%".secure_sql($search_term)."%' ORDER BY added DESC";
		$query = mysql_query($sql);
		$total = mysql_num_rows($query);
	}
	else 
	{
		$sql = '';
		if($filter != '')
		{
			$sql = "SELECT * FROM pm_comments ";
			$sql_count = "SELECT COUNT(*) as total_found FROM pm_comments ";

			switch($filter)
			{
				case 'articles':
				
					$sql .= " WHERE uniq_id LIKE 'article-%' ";
					$sql_count .= " WHERE uniq_id LIKE 'article-%' ";
					
				break;
				
				case 'videos':
				
					$sql .= " WHERE uniq_id NOT LIKE 'article-%' ";
					$sql_count .=  " WHERE uniq_id NOT LIKE 'article-%' ";
					
				break;
				
				case 'flagged':
					$sql .= " WHERE report_count > 0 ";
					$sql_count .= " WHERE report_count > 0 ";
				break;

				case 'pending':
					$sql .= " WHERE approved='0' ";
					$sql_count .= " WHERE approved='0' ";
				break;
			}
			$sql .= " ORDER BY added DESC LIMIT ".$from.", ".$limit;
		}
		else
		{
			$sql = "SELECT * FROM pm_comments ORDER BY added DESC LIMIT ".$from.", ".$limit;
			$sql_count = "SELECT COUNT(*) as total_found FROM pm_comments";
		}
		
		//	First, count all entries
		if (strlen($sql_count) > 0)
		{
			$result_count = @mysql_query($sql_count);
			if ( ! $result_count)
			{
				$total = $limit;
			}
			else
			{
				$row_count = mysql_fetch_assoc($result_count);
				mysql_free_result($result_count);
				
				$total = $row_count['total_found'];
				
				unset($sql_count, $result_count, $row_count);
			}
		}
		else
		{
			$total = $limit;
		}

		$query = mysql_query($sql);
	}
	$count = mysql_num_rows($query);

	// LIST COMMENTS
	if($count > 0) 
	{
		$res_arr = array();
		while($r = @mysql_fetch_array($query)) 
		{
			$res_arr[] = $r;
		}
		$res_arr_len = pm_count($res_arr);
		
		if($from == 0)
			$start = 0;
		elseif($from >= $res_arr_len)
			//$start = $from - $limit;
			$start = 0;
		else
			$start = $from;
		
		if( ($start + $limit) >= $res_arr_len)
			$to = $res_arr_len;
		else $to = ($limit + $start);
		
		$comments = '';
		$alt = 1;
		for($i = $start; $i < $to; $i++)
		{
			$col = ($alt % 2) ? 'even' : 'odd';//'odd' : 'even';
			$alt++;


			$comments .= "
			  <tr id=\"category_update\">
				<td align=\"center\" style=\"text-align: center\" width=\"20\"><input name=\"video_ids[]\" type=\"checkbox\" class=\"checkbox\" value=\"".$res_arr[$i]['id']."\" /></td>
				<td  align=\"center\" style=\"text-align: center\" width=\"10\">";
				
			if (strpos($res_arr[$i]['uniq_id'], 'article') !== false)
			{
				$comments .= '<i class="icon-magazine"></i> ';

			} else {

				$comments .= '<i class="icon-play3"></i> ';

			}
				$comments .= "</td>";

			if ($res_arr[$i]['report_count'] > 0) 
			{
				$flag_title = 'This comment has been flagged';
				$flag_title .= ($res_arr[$i]['report_count'] > 1) ? ' by '. $res_arr[$i]['report_count'] .' different users' : '';
				$flag_title .= ' as inappropriate.';
				$comments .= '<td class="comment-flagged">';
				$comments .= "<a href=\"comments.php?filter=flagged&page=1\" class=\"list-icons-item text-danger mr-1\" rel=\"tooltip\" title=\"". $flag_title ."\"><i class=\"icon-warning22\"></i></a>";
			} else {
				$comments .= '<td>';
			}

			
			if (strpos($res_arr[$i]['uniq_id'], 'article') !== false)
			{
				$article_id = str_replace('article-', '', $res_arr[$i]['uniq_id']);
				$comments .= '<a href="'. _URL .'/article-read.php?a='.$article_id.'&mode=preview#comments">';
			}
			else
			{
				$comments .= '<a href="'. _URL .'/watch.php?vid='.$res_arr[$i]['uniq_id'].'#comments">';
			}
			
			$comments .= vnamefromvid($res_arr[$i]['uniq_id'])."</a>";
			$comments .= "</td>";
			
			$comments .= '<td>';
			$comments .= '<div id="comment_update_'. $i .'" name="'. $i .'">';
			if($res_arr[$i]['approved'] == 0)
				$comments .= '<span class="badge badge-warning float-right">Pending Approval</span><br />';
				
			$comments .= '<span class="comment_update_hover" id="comment_span_'. $i .'">'. (($config['allow_emojis']) ? $emoji_client->shortnameToImage($res_arr[$i]['comment']) : $res_arr[$i]['comment']) .'</span>'; 
			$comments .= '<div class="comment_update_form" id="comment_update_form_'. $i .'">'; 
			$comments .= '<div style="display:inline; margin:0;padding:0;">';
			$comments .= '<textarea id="commenttxt_'. $i .'" name="comment_txt" rows="3" class="form-control mb-1">'. str_replace('<br />', '', $res_arr[$i]['comment']) .'</textarea>';
			$comments .= '<input type="hidden" name="comment_id" id="commentid_'. $i .'" value="'. $res_arr[$i]['id'] .'" />';
			$comments .= '<input name="update" type="submit" value="Update" class="btn btn-sm btn-success border-radius0" id="comment_update_btn_'. $i .'" />';
			$comments .= ' <a href="#" id="comment_update_'. $i .'" class="btn-sm">Cancel</a>';
			$comments .= '</div>';
			
			$comments .= '</div></div>';
			if ($res_arr[$i]['up_vote_count'] > 0 || $res_arr[$i]['down_vote_count'] > 0)
			{
				$comments .= '<div class="float-right">';
				$comments .= '<i class="icon-thumbs-up3"></i>  <small>'. pm_number_format($res_arr[$i]['up_vote_count']) .'</small>';
				$comments .= '&nbsp;&nbsp;';
				$comments .= '<i class="icon-thumbs-down3"></i> <small>'. pm_number_format($res_arr[$i]['down_vote_count']) .'</small>';
				$comments .= '</div>';
			}
			$comments .= "</td>";
			
			$comments .= "<td align=\"center\" style=\"text-align: center\">";
			$comments .= '<span data-popup="tooltip" data-container="body" data-original-title="'. date('l, F j, Y g:i A', $res_arr[$i]['added']) .'">';
			//$comments .= '<small>';
			$comments .=  date('M d, Y', $res_arr[$i]['added']);
			//$comments .= '</small>';
			$comments .= '</span>';
			$comments .= '</td>';
				
			if($res_arr[$i]['user_id'] == 0 || $res_arr[$i]['user_id'] == 1)
				$comments .= "<td align=\"center\" style=\"text-align: center\"><strong>".($res_arr[$i]['username'])."</strong></td>";
			else
				$comments .= "<td align=\"center\" style=\"text-align:center\"><a href=\"". get_profile_url($res_arr[$i]) ."\">".($res_arr[$i]['username'])."</a></td>";
				
			$comments .= "<td align=\"center\" style=\"text-align: center\"><small>".$res_arr[$i]['user_ip']."</small></td>";
			$comments .= "<td align=\"center\" class=\"table-col-action\" style=\"text-align:center;\">";
			
			$append_url = ($filter != '') ? '&filter='. $filter : '';
			$append_url .= ($_GET['vid'] != '') ? '&vid='. $_GET['vid'] : '';
			$append_url .= ($_GET['keywords'] != '') ? '&keywords='. $_GET['keywords'] .'&search_type='. $_GET['search_type'] .'&submit=Search' : '';
			
			if($res_arr[$i]['approved'] == 0)
			{
				$approve_url = 'comments.php?a=2&cid='. $res_arr[$i]['id'] .'&page='. $page . $append_url;
				$approve_url .= '&_pmnonce='. $comments_nonce['_pmnonce'] .'&_pmnonce_t='. $comments_nonce['_pmnonce_t'];
				$comments .= '<a href="'. $approve_url .'" class="list-icons-item text-success mr-1" data-popup="tooltip" data-container="body" data-original-title="Approve"><i class="icon-checkmark" ></i></a>';
				//$comments .= "<a href=\"comments.php?a=2&cid=".$res_arr[$i]['id']."&page=".$page."&filter=".$filter."&_pmnonce=". $comments_nonce['_pmnonce'] ."&_pmnonce_t=". $comments_nonce['_pmnonce_t'] ."\" class=\"btn btn-sm btn-success\" rel=\"tooltip\" title=\"Approve\"><i class=\"icon-ok\" ></i></a>";
			}
			else
			{
				$comments .= "";
			}
			
			$comments .= '<a href="#" class="list-icons-item mr-2" data-popup="tooltip" data-container="body" data-original-title="Edit comment" id="comment_update_pencil_'. $i .'"><i class="icon-pencil7"></i></a>';
			$comments .= "<a href=\"#\" onClick=\"del_comment_id('".$res_arr[$i]['id']."', '".$page."', '". $filter . $append_url ."')\" class=\"list-icons-item text-warning\" rel=\"tooltip\" title=\"Delete Comment\"><i class=\"icon-bin\" ></i></a>";
			$comments .= '</tr>';
		}
	} 
	elseif($count == 0 && ($_GET['keywords'] != '' || $_GET['vid'] != '')) 
	{
		$comments .= "
		  <tr>
			<td colspan=\"8\" align=\"center\" style=\"text-align:center\">No comments matching this criteria.</td>
		  </tr>";
	}
	else
	{
		if ($filter == 'flagged')
		{
			$comments .= "
			  <tr>
				<td colspan=\"8\" align=\"center\" style=\"text-align:center\">No comments have been flagged yet.</td>
			  </tr>";			
		}
		elseif ($filter == 'pending')
		{
			$comments .= "
			  <tr>
				<td colspan=\"8\" align=\"center\" style=\"text-align:center\">No comments pending approval.</td>
			  </tr>";			
		}
		else
		{
			$comments .= "
			  <tr>
				<td colspan=\"8\" align=\"center\" style=\"text-align:center\">No comments have been posted yet.</td>
			  </tr>";
		}
	}
	return array('comments' => $comments, 'total' => $total);
}

/**
 * @todo merge all DB category tables into one (-> pm_categories) and separate with a new 'type' column
 */
function insert_category($postarr, $type = 'video')
{
	global $_video_categories, $_article_categories, $_genres;

	$sql_table = ($type == 'article') ? 'art_categories' : 'pm_categories';
	$sql_table = ($type == 'genre') ? 'pm_genres' : $sql_table;
	$all_categories = load_categories(array('db_table' => $sql_table));
	
	foreach ($postarr as $k => $v)
	{
		if ( ! is_array($v))
		{
			$postarr[$k] = stripslashes( trim($v) );
		}
	}
	
	$pattern = '/(^[a-z0-9_-]+)$/i';
	$name	 = $postarr['name'];
	$tag 	 = str_replace(" ", "-", $postarr['tag']);
	$parent_id = ( ! empty($postarr['category'])) ?  (int) $postarr['category'] : (int) $postarr['parent_id'];
	$description = $postarr['description'];
	$meta_title = $postarr['meta_title'];
	$meta_title = str_replace('"', '&quot;', $meta_title);
	$meta_keywords = $postarr['meta_keywords'];
	$meta_keywords = str_replace('"', '&quot;', $meta_keywords);
	$meta_description = $postarr['meta_description'];
	$meta_description = str_replace('"', '&quot;', $meta_description);
	$image_filename = secure_sql($postarr['image']);
	
	if ($parent_id < 0)
	{
		$parent_id = 0;
	}
	
	if ( ! empty($tag) && ! empty($name))
	{
		if (@preg_match($pattern, $tag))
		{
			$tags_count = 0;
			foreach ($all_categories as $id => $c)
			{
				if ($c['tag'] == $tag)
				{
					$tags_count++;
				}
			}
			
			if ($tags_count == 0)
			{
				$position = 0;
				foreach ($all_categories as $id => $c)
				{
					if ($c['parent_id'] == $parent_id && $position < $c['position'])
					{
						$position = (int) $c['position'];
					}
				}
				$position++;
				
				$meta_tags = '';
				if ($meta_title != '' || $meta_keywords != '' || $meta_description != '')
				{
					$meta_tags = array('meta_title' => $meta_title,
									   'meta_keywords' => $meta_keywords,
									   'meta_description' => $meta_description
									  );
					$meta_tags = serialize($meta_tags);
				}
				
				if ($type == 'article')
				{
					$table_cols = 'parent_id, tag, name, published_articles, total_articles, position, description, meta_tags';
					$sql_values = "'". $parent_id ."', '". $tag ."', '". secure_sql($name) ."', '0', '0', '". $position ."', '". secure_sql($description) ."', '". secure_sql($meta_tags) ."'";
				}
				else
				{
					$table_cols = 'parent_id, tag, name, published_videos, total_videos, position, description, meta_tags, image';
					$sql_values = "'". $parent_id ."', '". $tag ."', '". secure_sql($name) ."', '0', '0', '". $position ."', '". secure_sql($description) ."', '". secure_sql($meta_tags) ."', '". $image_filename ."'";
				}
				
				$sql = "INSERT INTO $sql_table ($table_cols) 
						VALUES 	($sql_values)";

				if ( ! ($result = mysql_query($sql)))
				{
					return array('type' => 'error', 'msg' => 'An error occurred while creating this category: '. mysql_error());
				}
				
				$last_id = mysql_insert_id();
				
				load_categories(array('db_table' => $sql_table, 'reload' => true));
				
				return ($type == 'genre') ?
					array( 'type' => 'ok', 'msg' => 'Genre '. $name .' was created.', 'id' => $last_id) :
					array( 'type' => 'ok', 'msg' => 'Category '. $name .' was created.', 'id' => $last_id);
			}
			else
			{
				return array('type' => 'error', 'msg' => 'This Slug is already in use. The value of this property must be unique.');
			}
		}
		else
		{
			return array('type' => 'error', 'msg' => 'Please make sure that the Slug is typed properly (no spaces, just latin characters [a-z, A-Z], numbers [0-9], "_" and "-").');
		}
	}
	else
	{
		return ($type == 'genre') ? 
			array('type' => 'error', 'msg' => '\'Genre name\' and \'Slug\' are both required.') :
			array('type' => 'error', 'msg' => '\'Category name\' and \'Slug\' are both required.');
	}
	
	return false;
}

function update_category($category_id, $postarr, $type = 'video')
{
	global $_video_categories, $_article_categories;
	
	$sql_table = ($type == 'article') ? 'art_categories' : 'pm_categories';
	$sql_table = ($type == 'genre') ? 'pm_genres' : $sql_table;
	$all_categories = load_categories(array('db_table' => $sql_table));
	$current_data = $all_categories[$category_id];

	foreach ($postarr as $k => $v)
	{
		if ( ! is_array($v))
		{
			$postarr[$k] = stripslashes( trim($v) );
		}
	}
	
	$pattern = '/(^[a-z0-9_-]+)$/i';
	$name	 = $postarr['name'];
	$tag 	 = $postarr['tag'];
	$parent_id = ( ! empty($postarr['category'])) ?  (int) $postarr['category'] : (int) $postarr['parent_id'];
	$parent_id = ($parent_id < 0) ? 0 : $parent_id;
	$parent_id = ($parent_id == $category_id) ?  $current_data['parent_id'] : $parent_id;
	$old_tag = ($postarr['old_tag'] != '') ?  $postarr['old_tag'] : $current_data['tag'];
	$position = ($postarr['position'] != '') ? $postarr['position'] : $current_data['position'];
	$description = $postarr['description'];
	$meta_title = $postarr['meta_title'];
	$meta_title = str_replace('"', '&quot;', $meta_title);
	$meta_keywords = $postarr['meta_keywords'];
	$meta_keywords = str_replace('"', '&quot;', $meta_keywords);
	$meta_description = $postarr['meta_description'];
	$meta_description = str_replace('"', '&quot;', $meta_description);
	$image_filename = secure_sql($postarr['image']);
	
	if ( ! empty($tag) && ! empty($name) && $category_id != 0)
	{
		if (@preg_match($pattern, $tag))
		{
			if (strcmp($old_tag, $tag) != 0)
			{
				$tags_count = 0;
				foreach ($all_categories as $id => $c)
				{
					if ($c['tag'] == $tag)
					{
						$tags_count++;
					}
				}
				
				if ($tags_count > 0)
				{
					return array('type' => 'error', 'msg' => 'This Slug is already in use. The value of this property must be unique.');
				}
			}
			
			if (($parent_id != $current_data['parent_id']) && ($position == $current_data['position']))
			{
				$position = 0;
				foreach ($all_categories as $id => $c)
				{
					if ($c['parent_id'] == $parent_id && $position < $c['position'])
					{
						$position = (int) $c['position'];
					}
				}
				$position++;
			}
			
			$meta_tags = '';
			if ($meta_title != '' || $meta_keywords != '' || $meta_description != '')
			{
				$meta_tags = array('meta_title' => $meta_title,
								   'meta_keywords' => $meta_keywords,
								   'meta_description' => $meta_description
								  );
				$meta_tags = serialize($meta_tags);
			}
			
			$sql =  "UPDATE $sql_table 
						SET parent_id = '". $parent_id ."', 
							tag = '". secure_sql($tag) ."',
							name = '". secure_sql($name) ."',
							position = '". $position ."',
							description = '". secure_sql($description) ."',
							meta_tags = '". secure_sql($meta_tags) ."'";
			$sql .= ($type == 'video') ? ", image = '". $image_filename ."' " : '';
			$sql .= " WHERE id = '". $category_id ."'";
			if ( ! ($result = mysql_query($sql)))
			{
				return array('type' => 'error', 'msg' => 'An error occurred while updating this category: '. mysql_error());
			}
			
			if ($parent_id != $current_data['parent_id'])
			{
				$sql = "UPDATE $sql_table 
						SET position = position - 1
						WHERE parent_id = '". $current_data['parent_id'] ."' 
						  AND position > '". $current_data['position'] ."'";
				mysql_query($sql);
			}
			
			if ($current_data['image'] != '' && $current_data['image'] != $image_filename 
				&& $image_filename != '' && file_exists(_THUMBS_DIR_PATH . $image_filename))
			{
				// delete previous file
				@unlink(_THUMBS_DIR_PATH . $current_data['image']);
			}
			
			load_categories(array('db_table' => $sql_table, 'reload' => true));
			
			return ($type == 'genre') ?
				array( 'type' => 'ok', 'msg' => 'Genre '. $name .' was updated.') :
				array( 'type' => 'ok', 'msg' => 'Category '. $name .' was updated.');
		}
		else
		{
			return array('type' => 'error', 'msg' => 'Please make sure that the Slug is typed properly (no spaces, just latin characters [a-z, A-Z], numbers [0-9], "_" and "-").');
		}
	}
	else
	{
		return ($type == 'genre') ?
			array('type' => 'error', 'msg' => '\'Genre name\' and \'Slug\' are both required.') : 
			array('type' => 'error', 'msg' => '\'Category name\' and \'Slug\' are both required.');
	}
	
	return true;
}

function delete_category($category_id, $type = 'video')
{
	global $_video_categories, $_article_categories, $_genres, $config;
 
	$sql_table = ($type == 'article') ? 'art_categories' : 'pm_categories';
	$sql_table = ($type == 'genre') ? 'pm_genres' : $sql_table;
	$all_categories = load_categories(array('db_table' => $sql_table));
	$current_data = $all_categories[$category_id];
	
	$grandparent_id = (int) $current_data['parent_id'];

	$children_ids = array();
	foreach ($all_categories as $id => $c)
	{
		if ($c['parent_id'] == $category_id)
		{
			$children_ids[] = $id;
		}
	}
	
	$sql = "DELETE FROM $sql_table 
			WHERE id = $category_id";
	if ( ! ($result = mysql_query($sql)))
	{
		return array('type' => 'error', 'msg' => 'An error occurred while updating this category: '. mysql_error());
	}

	if ($type == 'genre')
	{
		$sql = "DELETE FROM pm_genre_relationships 
				WHERE genre_id = ". $category_id; 
		$result = mysql_query($sql);
	}
	
	if ($current_data['image'] != '' && file_exists(_THUMBS_DIR_PATH . $current_data['image']))
	{
		@unlink(_THUMBS_DIR_PATH . $current_data['image']);
	}
	
	$update_pos_ids = array();
	foreach ($all_categories as $id => $c)
	{
		if (($c['parent_id'] == $current_data['parent_id']) && ($c['position'] > $current_data['position']))
		{
			$update_pos_ids[] = (int) $id;
		}
	}

	if (pm_count($update_pos_ids) > 0)
	{
		$update_pos_ids = implode(',', $update_pos_ids);
		$sql = "UPDATE $sql_table 
				   SET position = position - 1 
				 WHERE id IN (". $update_pos_ids .")";
				
		mysql_query($sql);
	}
	
	if (pm_count($children_ids) > 0)
	{
		$position = 0;
		$sql = "SELECT MAX(position) as max  
				 FROM $sql_table 
				WHERE parent_id = $grandparent_id";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		$position = ($row['max'] > 0) ? ($row['max'] + 1) : 1;

		foreach ($children_ids as $k => $id)
		{
			$sql = "UPDATE $sql_table 
					   SET parent_id = $grandparent_id, position = $position 
					 WHERE id = $id";
			mysql_query($sql);
			$position++;
		}
	}
	
	load_categories(array('db_table' => $sql_table, 'reload' => true));
	
	if ($config['homepage_featured_categories'] != '' && $type == 'video')
	{
		$featured_categories = unserialize($config['homepage_featured_categories']);
		foreach ($featured_categories as $k => $id)
		{
			if ($id == $category_id)
			{
				unset($featured_categories[$k]);
				update_config('homepage_featured_categories', serialize($featured_categories));
				
				break;
			}
		}
	}
	if ($config['featured_genres'] != '' && $type == 'genre')
	{
		$featured_genres = unserialize($config['featured_genres']);
		foreach ($featured_genres as $k => $id)
		{
			if ($id == $category_id)
			{
				unset($featured_genres[$k]);
				update_config('featured_genres', serialize($featured_genres));
				
				break;
			}
		}
	}

	return ($type == 'genre') ?
		array('type' => 'ok', 'msg' => 'Genre <em>'. $current_data['name'] .'</em> has been removed.') :
		array('type' => 'ok', 'msg' => 'Category <em>'. $current_data['name'] .'</em> has been removed.');
}

function a_category_table_row($item, &$all_children, $all_categories, $level = 0, $options, &$alternate)
{
	global $featured_categories, $featured_genres;
	
	$output = '';	
	
	if ($level > 1)
	{
		$padding = str_repeat($options['spacer'], $level-1);
	}
	
	// build output here.
	$col = ($alternate++ % 2) ? 'table_row1' : 'table_row2'; 

	$url_glue = (strpos($options['page'], '?') !== false) ? '&' : '?';
	
	$move_up_href = '<a href="'. $options['page'] . $url_glue .'move=up&id='. $item['id']. '" data-popup="tooltip" data-container="body" data-original-title="Move up"><i class="icon-chevron-up"></i></a>'; 
	$move_down_href = '<a href="'. $options['page'] . $url_glue .'move=down&id='. $item['id']. '" data-popup="tooltip" data-container="body" data-original-title="Move down"><i class="icon-chevron-down"></i></a>';

	$output .= '<form name="'. $item['id'] .'" action="'. $options['form_action'] .'" method="post" class="form-inline">';
	$output .= "\n";

	$output .= ' <tr id="category_update" title="category-'. $item['id'] .'"';
	$output .= ($level == 0) ?  ' class="category_parent"> ' : '>';
	if ($options['type'] == 'video' || $options['type'] == 'genre')
	{
		$featured_var = ($options['type'] == 'video') ? 'featured_categories' : 'featured_genres';
		$output .= '<td align="center"  style="text-align: center"><a href="#" class="'. ((in_array($item['id'], $$featured_var)) ? 'category_mark_featured is_featured_category' : 'category_mark_featured') .'" data-category-id="'. $item['id'] .'" data-category-type="'. $options['type'] .'" rel="tooltip" title="'. ((in_array($item['id'], $$featured_var)) ? 'Un-mark' : 'Mark') .' as featured. Featured categories appear on homepage."><i class="icon icon-star-full2"></i></a></td>'; 
	}
//	$output .= '  <td align="center" class="text-center">'. $item['id'] .'</td>';$output .= "\n";
	$output .= '  <td>';$output .= "\n";
	$output .= '   <div class="category_update_name">';$output .= "\n";
	$output .= ($level > 0) ? $padding .' &#8212; ' : '';$output .= "\n";
	$output .= '    <strong>'. htmlentities($item['name'],ENT_COMPAT,'UTF-8') .'</strong>';$output .= "\n";
	$output .= '   </div>';$output .= "\n";
	$output .= '   <div class="category_update_form">';$output .= "\n";
	$output .= '    <div class="category_update_form form-inline"><input name="name" type="text" size="22" value="'. $item['name'] .'" class="form-control form-control-sm" />';$output .= "\n";
	$output .= '   </div></div>';$output .= "\n";
	$output .= '   <input name="id" type="hidden" value="'. $item['id'] .'" />';$output .= "\n";
	$output .= '   <input name="parent_id" type="hidden" value="'. $item['parent_id'] .'" />';
	$output .= '   <input name="old_tag" type="hidden" value="'. $item['tag'] .'" />';
	$output .= '  </td>';$output .= "\n";
	$output .= '  <td>';$output .= "\n";
	$output .= '   <div class="category_update_name">'. $item['tag'] .'</div>';$output .= "\n";
	$output .= '   <div class="category_update_form form-inline"><input name="tag" size="15" type="text" value="'. $item['tag'] .'" class="form-control form-control-sm d-inline-block w-auto" /> <!--<a href="#" rel="tooltip" data-placement="left" title="Changing the Slug alters the URL structure. <br>Not recommended if this category has already been indexed by the Search Engines."> <i class="icon-warning-sign" style="opacity:0></i> </a>--> ';$output .= "\n";
	$output .= '    <button name="update" type="submit" value="Update" class="btn btn-sm btn-outline alpha-success text-success-800 border-success-600" />Save</button>';$output .= "\n";
	$output .= '   </div>';$output .= "\n";
	$output .= '  </td>';$output .= "\n";
	//$output .= '  <td class="text-center">'. $all_categories[$item['parent_id']]['name'] .'</td>';
	$output .= "\n";
	$output .= '  <td align="center" class="text-center">';
	$output .= ($options['type'] == 'video') 
		? $item['total_videos'] 
		: (($options['type'] == 'article') ? $item['total_articles'] : $item['total_series']);
	$output .= '  </td>';
	$output .= "\n"; 
	$output .= '  <td align="center" class="table-col-action text-center d-none d-md-table-cell">'. $move_up_href .' '. $move_down_href .'</td>';$output .= "\n";
	$output .= '  <td align="center" class="table-col-action text-center">';$output .= "\n";
	$output .= '<a href="edit-category.php?mode=edit&type='. $options['type'] .'&id='. $item['id'] .'" class="list-icons-item mr-2" data-popup="tooltip" data-container="body" data-original-title="Edit category"><i class="icon-pencil7"></i> </a> ';
	$output .= '<a href="#" onClick="onpage_delete_category('. $item['id'] .', \''. $options['type'] .'\', \'#display_result\', \'tr[title=category-'. $item['id'] .']\')" class="list-icons-item text-warning" data-popup="tooltip" data-container="body" data-original-title="Delete"><i class="icon-bin"></i></a>';
	$output .= "\n"; 
	$output .= '  </td>';
	$output .= "\n";
	$output .= ' </tr>';
	$output .= "\n";
	$output .= '</form>';
	$output .= "\n";$output .= "\n";
	
	
	if (isset($all_children[$item['id']]))
	{
		foreach ($all_children[$item['id']] as $k => $child)
		{
			$output .= a_category_table_row($child, $all_children, $all_categories, $level+1, $options, $alternate);
		}
		unset($all_children[$item['id']]);
	}
	
	return $output;
}

function a_category_table_body($categories = false, $args = array())
{
	$output = '';
	$empty = array();
	$defaults = array(
		'page' => 'categories.php',
		'col_span' => 7,
		'form_action' => 'categories.php',
		'spacer' => '&nbsp;&nbsp;&nbsp;&nbsp;',		
	);
	
	if ( ! is_array($categories))
		return;
	
	$options = array_merge($defaults, $args);
	
	extract($options);
	
	$parents = $parent_ids = $children = array();
	
	if (pm_count($categories) == 0)
	{
		return '<tr><td colspan="'. $col_span .'" align="center" style="text-align:center;">No '. (($options['type'] == 'genre') ? 'genres' : 'categories') .' have been defined.</td></tr>';
	}
	
	foreach ($categories as $k => $row)
	{
		if ($row['parent_id'] == 0)
		{
			$parents[] = $row;
			$parent_ids[] = $row['id'];
		}
		else
		{
			$children[$row['parent_id']][] = $row;
		}
	}
	
	$alt = 1;
	
	foreach ($parents as $k => $p)
	{
		$output .= a_category_table_row($p, $children, $categories, 0, $options, $alt);
	}
	
	foreach ($children as $parent_id => $orphans)
	{
		foreach ($orphans as $k => $orphan)
		{
			$orphan['parent_id'] = 0;
			$output .= a_category_table_row($orphan, $empty, $categories, 0, $options, $alt);
		}
	}
	
	
	return $output; 
}

function a_category_sortable_list_item($item, &$all_children, $all_categories, $level = 0)
{
	$output = '';	
	
	$output .= "\n";
	$output .= '<li data-id="'. $item['id'] .'"><div>'. $item['name'] .'</div>';
	$output .= "\n";
	if (isset($all_children[$item['id']]))
	{
		$output .= '<ol>';
		$output .= "\n";
		foreach ($all_children[$item['id']] as $k => $child)
		{
			$output .= a_category_sortable_list_item($child, $all_children, $all_categories, $level+1);
		}
		$output .= '</ol>';
		$output .= "\n";
		unset($all_children[$item['id']]);
	}
	$output .= '</li>';
	
	return $output;
}

function a_category_sortable_list($categories = false)
{
	if ( ! $categories)
	{
		$categories = load_categories();
	}
	
	$output = '<ol class="sortable">';
	
	$parents = $parent_ids = $children = array();
	
	foreach ($categories as $k => $row)
	{
		if ($row['parent_id'] == 0)
		{
			$parents[] = $row;
			$parent_ids[] = $row['id'];
		}
		else
		{
			$children[$row['parent_id']][] = $row;
		}
	}
	
	foreach ($parents as $k => $p)
	{
		$output .= a_category_sortable_list_item($p, $children, $categories, 0);
	}
	
	foreach ($children as $parent_id => $orphans)
	{
		foreach ($orphans as $k => $orphan)
		{
			$orphan['parent_id'] = 0;
			$output .= a_category_sortable_list_item($orphan, $empty, $categories, 0);
		}
	}
	
	
	$output .= '</ol>';
	
	return $output; 
}

function a_list_cats()
{	
	$categories = load_categories();
	return a_category_table_body($categories, array());
}

// LISTING USERS 
function a_list_users($search_term, $search_type = 'username', $from = 0, $limit = 20, $page = 1, $filter = '', $filter_value = '') 
{
	global $members_nonce, $time_now;
	
	if(!$page)	$page = 1;
	
	if($search_term != '') 
	{
		$sql = 'SELECT * FROM pm_users WHERE ';
		switch($search_type)
		{
			default:
			case 'username' : $sql .= 'username'; break;
			case 'fullname' : $sql .= 'name';  break;
			case 'email' : $sql .= 'email'; break;
			case 'ip' : 
				$sql .= " reg_ip LIKE '%".secure_sql($search_term)."%' OR last_signin_ip "; 
			break;
		}
		$sql .= " LIKE '%".secure_sql($search_term)."%' ORDER BY id DESC";
		$query = mysql_query($sql);
		$total = mysql_num_rows($query);
	}
	else 
	{
		$where = '';
		$orderby = ' ORDER BY id DESC '; // default
		
		switch ($filter)
		{
			case 'power':
				$where = " WHERE power = '". $filter_value ."' ";
			break;
			
			case 'id':
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY id '. $order;
			break;
			
			case 'register': // sorting
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY reg_date '. $order;
			break;
			
			case 'lastlogin': // sorting
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY last_signin '. $order;
			break;
			
			case 'followers': // sorting
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY followers_count '. $order;
			break;
			
			case 'following': // sorting
				$order = (in_array($filter_value, array('DESC', 'ASC', 'desc', 'asc'))) ? $filter_value : 'DESC';
				$orderby = ' ORDER BY following_count '. $order;
			break;
			
		}
		
		$sql = 'SELECT * FROM pm_users ';
		$sql .= $where;
		$sql .= $orderby;
		$sql .= ' LIMIT '. $from .', '. $limit;

		$query = mysql_query($sql);
	}

	$count = mysql_num_rows($query);
	// LIST USERS
	if($count > 0) 
	{
		$banlist = get_banlist();
		
		$res_arr = array();
		while($r = @mysql_fetch_array($query)) 
		{
			$res_arr[] = $r;
		}
		$res_arr_len = pm_count($res_arr);
		
		if($from == 0)
			$start = 0;
		elseif($from >= $res_arr_len)
			//$start = $from - $limit;
			$start = 0;
		else
			$start = $from;
		
		if( ($start + $limit) >= $res_arr_len)
			$to = $res_arr_len;
		else $to = ($limit + $start);
		
		$col = '';
		$alt = 1;
		for($i = $start; $i < $to; $i++)
		{
			//$username = (array_key_exists($res_arr[$i]['id'], $banlist)) ? '<s>'.$res_arr[$i]['username'].'</s>' : $res_arr[$i]['username'];
			$username = $res_arr[$i]['username'];
			$alt++;
			
			// checkbox
			$checkbox = "<td align=\"center\" style=\"text-align:center\">";
			if ($res_arr[$i]['power'] != U_ADMIN)
			{
				$checkbox .= "<input name=\"user_ids[]\" type=\"checkbox\" value=\"".$res_arr[$i]['id']."\" />";
			}
			$checkbox .= '</td>';
			
			$users .= "
			  <tr>
				". $checkbox ."
				<td align=\"center\" style=\"text-align:center\">".$res_arr[$i]['id']."</td>";

			$users .="<td>";

			$users .= '<a href="'. get_profile_url($res_arr[$i]) .'" class="list-icons-item mr-2">'.stripslashes($username).'</a> ';
//			$users .= '<a href="'. get_profile_url($res_arr[$i]) .'" class="open-in-new float-right" target="_blank"><i class="mi-open-in-new"></i></a>';
			
			if( $res_arr[$i]['channel_verified'] == 1 )
			{
				$users .= ' <a href="#" data-popup="tooltip" data-container="body" data-original-title="Verified Channel"><img src="' . _URL .'/'. _ADMIN_FOLDER .'/img/ico-verified.png" width="12" height-"12" alt="" border="0" /></a>';
			}
			$users .= "</td>";

			$users .= "<td>".stripslashes($res_arr[$i]['name'])."</td>
				<td><a href=\"mailto:".$res_arr[$i]['email']."\">".$res_arr[$i]['email']."</a></td>";

			$users .= "<td align=\"center\" style=\"text-align: center\">";
			$users .= '<span data-popup="tooltip" data-container="body" data-original-title="'. date('l, F j, Y g:i A', $res_arr[$i]['reg_date']) .'">';
			//$users .= '<small>';
			$users .=  date('M d, Y', $res_arr[$i]['reg_date']);
			//$users .= '</small>';
			$users .= '</span>';
			$users .= '</td>';

			$users .="
				<td style=\"text-align:center\">". pm_number_format($res_arr[$i]['followers_count']) ."</td>
				<td style=\"text-align:center\">". pm_number_format($res_arr[$i]['following_count']) ."</td>";

				//if (time_since($res_arr[$i]['last_signin']) == "0 seconds")
				if (($time_now - $res_arr[$i]['last_signin']) <= 60 && $res_arr[$i]['last_signin'] > 0)
				{
					$users .= "<td align=\"center\" style=\"text-align:center\"><span class=\"badge badge-success\">Online now</span></td>";
				} 
				else 
				{
					$users .= "<td align=\"center\" style=\"text-align:center\">";
					if ($res_arr[$i]['last_signin'] == 0)
					{
						$users .= 'Never';
					}
					else
					{
						$users .= time_since($res_arr[$i]['last_signin'])." ago";
					}
					$users .= '</td>';
				}
				
				$users .= "
				<td align=\"center\" style=\"text-align:center\">". (($res_arr[$i]['last_signin_ip'] != '') ? $res_arr[$i]['last_signin_ip'] : 'No IP') ."</td>
				<td style=\"text-align:center\">";

			if ( array_key_exists($res_arr[$i]['id'], $banlist) )
			{
				$users .= '<span class="badge badge-danger">Banned user</span>';
			}
			else if ($res_arr[$i]['power'] == U_INACTIVE)
			{

				$users .= '<a href="edit-user.php?uid='.$res_arr[$i]['id'].'&action=1&filter='. $filter .'&fv='. $filter_value .'&_pmnonce='. $members_nonce['_pmnonce'] .'&_pmnonce_t='. $members_nonce['_pmnonce_t'] .'" class=""><span class="badge badge-warning bg-warning-400">Activate</span></a>';
			}
			else if ($res_arr[$i]['power'] == U_ADMIN)
			{
				$users .= '<span class="badge badge-dark"><strong>Admin</strong></span>';
			}
			else if ($res_arr[$i]['power'] == U_MODERATOR)
			{
				$users .= '<span class="badge badge-flat border-success text-success-600"><strong>Moderator</strong></span>';
			}
			else if ($res_arr[$i]['power'] == U_EDITOR)
			{
				$users .= '<span class="badge badge-flat border-success text-success-600"><strong>Editor</strong></span>';
			}
			else
			{	
				$users .= '<span class="badge badge-flat border-info text-info-600">Active</span>';
			}
			$users .= '</td><td align="center" class="table-col-action text-center">';

			$users .= '<a href="edit-user.php?uid='.$res_arr[$i]['id'].'" rel="tooltip" title="Edit profile" class="list-icons-item mr-2"><i class="icon-pencil7"></i></a>';
			$users .= '<a href="#" onClick="del_member_id(\''.$res_arr[$i]['id'].'\', \''.$page.'\')" rel="tooltip" title="Delete account" class="list-icons-item text-warning"><i class="icon-bin"></i></a></td>
			  </tr>';
			}
	} 
	elseif($count == 0) 
	{
		$users .= "
		  <tr>
			<td colspan=\"12\" align=\"center\">No users found.</td>
		  </tr>";
		$total = $count;
	}
	return array('users' => $users, 'total' => $total);
}


// LIST VIDEO REPORTS

function a_list_vreports($r_type, $from = 0, $to = 50, $page = 1) {

	if(!$page)	$page = 1;
	$sql = "SELECT pm_reports.*, pm_videos.id as vid, pm_videos.status, pm_videos.last_check, pm_videos.source_id, pm_videos.category, pm_videos.yt_thumb 
			FROM pm_reports JOIN pm_videos 
							ON (pm_reports.entry_id = pm_videos.uniq_id) 
			WHERE r_type = '".$r_type."' 
			ORDER BY pm_reports.id DESC 
			LIMIT ".$from.", ".$to;
			
	$query = mysql_query($sql);
	$count = mysql_num_rows($query);	

	// LIST REPORTS
	if($count >= 1) {
		$reports = '';
		$sources = a_fetch_video_sources();
		
		$i = 1;
		$alt = 1;
		while($r = mysql_fetch_assoc($query)) {
		$col = ($alt % 2) ? 'table_row1' : 'table_row2';
		$alt++;
		
		$r['last_check'] = (int) $r['last_check'];
		$last_check = ($r['last_check']) ? time_since($r['last_check']) .' ago' : 'never';
		
		$status = '';
		$status_img = '';
		switch($r['status'])
		{
			default:
			case VS_UNCHECKED: 	$status = "Video Status: Unchecked";		$status_img = VS_UNCHECKED_IMG;		break;
			case VS_OK: 		$status = "Video Status: OK";				$status_img = VS_OK_IMG; 			break;
			case VS_BROKEN: 	$status = "Video Missing";					$status_img = VS_BROKEN_IMG; 		break;
			case VS_RESTRICTED:	$status = "Video Status: Geo-restricted";	$status_img = VS_RESTRICTED_IMG;	break;
		}
		//$status_img .= ".png";
		
		$tr_class = '';
		if ($r['added'] > time())
		{
			$tr_class = 'scheduled';
		} 
		else if ($r['restricted'] == '1') 
		{
			$tr_class = 'private';
		} 

		$thumb_url = _URL .'/'. _ADMIN_FOLDER .'/img/no-thumbnail.jpg';
		if (strpos($r['yt_thumb'], 'http') === 0 || strpos($r['yt_thumb'], '//') === 0)
		{
			$thumb_url = $r['yt_thumb'];
		}
		elseif ($r['yt_thumb'] != '')
		{
			$thumb_url = _THUMBS_DIR . $r['yt_thumb'];
		}

		
		// "data-*" attributes @since 2.4 very useful as row identifiers
		$reports .= '<tr class="'. $tr_class .'" data-uniq-id="'. $r['entry_id'] .'" data-video-id="'. $r['vid'] .'">';

		$reports .= "<td style=\"text-align:center\">";
		  
			 if(in_array($r['source_id'], array(1, 2, 3, 5, 16)))
			 {
				$reports .= "<input name=\"video_ids[]\" type=\"checkbox\" class=\"checkbox\" value=\"".$r['entry_id']."\" id=\"".$r['vid']."\" />";
			 }
			 else
			 {
				$reports .= "<input name=\"video_ids[]\" type=\"checkbox\" class=\"checkbox\" value=\"".$r['entry_id']."\" />";
				$status_img = VS_NOTAVAILABLE_IMG;
				$status = "Not Available";
			 }
		$reports .= '<input name="video_cat_ids[]" type="hidden" value="'. $r['category'] .'" />';
		$reports .= "
			</td>
			<td style=\"text-align:center\">";

		$reports .= '<div class="sprite '. strtolower($sources[$r['source_id']]['source_name']) .'" data-popup="tooltip" data-container="body" data-original-title="Source: '. ucfirst($sources[$r['source_id']]['source_name']).'"></div>';

		$reports .= '</td><td align="center" class="text-center"><img src="'. make_url_https($thumb_url) .'" alt="" border="0" name="video_thumbnail" class="img-responsive" width="80" /></td>';


		$reports .= '<td><a href="'._URL.'/watch.php?vid='.$r['entry_id'].'" target="_blank">'.vnamefromvid($r["entry_id"]).'</a></td>';
		$reports .= '<td>'. htmlentities($r['reason']) .'</td>';
		$reports .= '<td style="text-align:center">'.$r["submitted"].'</td>';
		$reports .= '<td align="center" style="text-align:center; width: 12px;">';
		$reports .= '';
		$reports .= '<div class="pm-sprite '. $status_img .'" id="status_'. $r['vid'] .'" alt="" data-popup="tooltip" data-container="body" data-html="true" data-original-title="'.$status.' <br> Last checked: '.$last_check.'"></div>';
		$reports .= '';
		$reports .= '</td>';
		$reports .= '<td align="center" class="table-col-action text-center">
						<a href="edit-video.php?vid='.$r['entry_id'].'" class="list-icons-item mr-2" rel="tooltip" title="Update Video"><i class="icon-pencil7"></i></a>
						<a href="#" onClick="del_report(\''.$r['id'].'\', \''.$page.'\')" class="list-icons-item text-warning" rel="tooltip" title="Delete Report"><i class="icon-bin"></i></a>
					<!--<a href="#"" onClick="del_video_id("'.$r['entry_id'].'")">Delete Video</a> -->
				</td>
		  </tr>';
		  $i++;
		}
	} elseif($count == 0) {

		$reports .= "
		  <tr>
			<td colspan=\"8\" align=\"center\" style=\"text-align: center;\">No videos have been reported.</td>
		  </tr>";
	}
	return $reports;
}
function unhtmlspecialchars( $string ) {
		$string = str_replace ( '&amp;', '&', $string );
		$string = str_replace ( '&#039;', '\'', $string );
		$string = str_replace ( '&quot;', '\"', $string );
		$string = str_replace ( '&lt;', '<', $string );
		$string = str_replace ( '&gt;', '>', $string );
		$string = str_replace(array('<![CDATA[', ']]>'), array('',''), $string);

	   
		return $string;
}

function get_rss_news($limit = 3) {
	$rss = new lastRSS; 
	$rssurl = "http://feeds.feedburner.com/pmFeed";
	$nowTime = strtotime(date('F jS, Y'));
		
	if ($rs = $rss->get($rssurl)) { 
		for( $i = 0; $i < $limit; $i++){
			$lastTime = strtotime($rs['items'][$i]['pubDate']);
			if($i == 0) {
				$ret .= "<li class='media pt-2 pb-2 media-new'>\n";
			} else {
				$ret .= "<li class='media pt-2 pb-2'>\n";
			}

			if(($nowTime-$lastTime) < 2625998) { 
				$ret .= '<div class="mr-3 mt-1">';
				$ret .= '<span class="badge bg-success-400 ml-auto">New</span>';
				$ret .= '</div>';
			}

				$ret .= '<div class="media-body">';
				$ret .= '<span class="media-title"><a href="'. $rs["items"][$i]["link"] .'" target="_blank">'.$rs["items"][$i]["title"].'</a></span>';
				$ret .= '<p class="text-muted font-size-sm mb-0">'. fewchars(unhtmlspecialchars($rs['items'][$i]['description']), 140) .'</p>';
				$ret .= "</div>";


			$ret .= "\n";
			$ret .= "</li>\n";
		}
	} else { 
		$ret = '<li class="media">
					<div class="media-body text-center">
					<p class="mt-1 mb-2">We cannot fetch the latest announcements. Please join us at <a href="https://www.phpsugar.com/blog/" target="_blank">www.phpsugar.com</a> to stay up-to-date.</p>
					</div>
				</li>';
	} 
	return $ret;
}

function show_pm_notes() {

	global $config, $userdata;
	
	if ($userdata['power'] != U_ADMIN)
		return '';

	$txt_notes  = array();
	$i = 0;
	
	if ( ! is_array($config))
	{
		$config = get_config();
	}
	// check for new versions
	$official_version = cache_this('read_version', 'pm_version'); 
	if (version_compare($official_version, $config['version']) == 1) 
	{
		$txt_notes[$i]['title'] = 'New Update Available!';
		$txt_notes[$i]['desc']  = '<strong>PHP Melody '.$official_version.'</strong> is available for download.<br /> Access your <a href="https://www.phpsugar.com/customer/" target="_blank">Customer Account</a> to download the update pack. ';
		$txt_notes[$i]['ico']  = 'gritter-ico_warn.png';
		$txt_notes[$i]['bgcolor'] = 'green';
		$i++;
	}
	// check for default password
	if (pm_password_verify('admin', $userdata['password']))
	{
		$txt_notes[$i]['title'] = 'Protect Your Website';
		$txt_notes[$i]['desc']  = 'Please change the default admin password. <br /><a href="password.php">Click here to secure your site</a>.';
		$txt_notes[$i]['ico']  = 'gritter-ico_pass.png';
		$txt_notes[$i]['bgcolor'] = 'red';
		$i++;
	}

	if (_CUSTOMER_ID == 'YOUR_CUSTOMER_ID' || _CUSTOMER_ID == 'YOUR_CUSTOMER_I' || _CUSTOMER_ID == '_CUSTOMER_ID') 
	{
		$txt_notes[$i]['title'] = 'Critical Error';
		$txt_notes[$i]['desc']  = 'The Customer ID in <strong>config.php</strong> is <strong>invalid</strong>. To use PHP Melody, update <strong>config.php</strong> with your real Customer ID which is available in your <a href="https://www.phpsugar.com/customer/">Customer Account</a>.';
		$txt_notes[$i]['ico']  = 'gritter-ico_warn.png';
		$txt_notes[$i]['bgcolor'] = 'blue';
		$i++;
	}
	if(file_exists("db_update.php"))
	{
		$txt_notes[$i]['title'] = 'Update Database';
		$txt_notes[$i]['desc']  = 'The update process is not yet complete. Click here to <a href="db_update.php">update the MySQL database</a> If you already tooks this step, simply remove "db_update.php" from the "/admin" folder.';
		$txt_notes[$i]['ico']  = 'gritter-ico_warn.png';
		$txt_notes[$i]['bgcolor'] = 'red';
		$i++;
	}	
	if ($config['mail_server'] == 'mail.domain.com')
	{
		$txt_notes[$i]['title'] = 'Email Settings';
		$txt_notes[$i]['desc']  = 'PHP Melody requires an email account to send emails. Go to <a href="settings.php"><strong>Settings > E-mail Settings</strong></a> to configure the email account.';
		$txt_notes[$i]['ico']  = 'gritter-ico_warn.png';
		$txt_notes[$i]['bgcolor'] = 'red';
		$i++;
	}

	if (pm_count($txt_notes) == 0) 
	{
		$txt_notes = false;
	}
	
	$result = '';
	if (is_array($txt_notes))
	{
		foreach ($txt_notes as $k => $arr) 
		{
			$result .= "show_pm_note('".$arr['title']."', '".secure_sql($arr['desc'])."', 'img/".$arr['ico']."','".$arr['bgcolor']."');\n\r"; 
		}
	}
	
	echo $result;
}


function read_version() {

	global $config;
	
	// You like?
	$ww = 'ht';
	$ww .= 'tp:/';
	$ww .= '/w';
	$ww .= 'ww';	
	$ww .= '.php';
	$ww .= 'sug';
	$ww .= 'ar.co';
	$ww .= 'm/updates/';
	
	$url = $ww.'pm_version.txt';

	if (function_exists('curl_init'))
	{
		$ch = @curl_init();
		@curl_setopt($ch, CURLOPT_URL, $url);
		@curl_setopt($ch, CURLOPT_HEADER, 0);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] .' ('. $_SERVER['SERVER_SOFTWARE'].')');
		@curl_setopt($ch, CURLOPT_REFERER, _URL . '?ver=' . $config['version'] . '&from=' . $_SERVER['REMOTE_ADDR']);

		$content = @curl_exec($ch);
		@curl_close($ch);		
	} 
	else 
	{
		$content = @file_get_contents($url);
	}

	return $content;
}

function a_generate_smart_pagination($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "&page=")
{		
	if(!$adjacents) $adjacents = 1;
	if(!$limit) $limit = 15;
	if(!$page) $page = 1;
	if(!$targetpage) $targetpage = "/";
	
		
	$prev = $page - 1;
	$next = $page + 1;
	$lastpage = ceil($totalitems / $limit);
	$lpm1 = $lastpage - 1;
	
	if(strpos($pagestring, 'page=', 0) === FALSE)
		$pagestring .= "&page=";
	
	$pagestring1 = preg_replace('/page=([0-9]*)/', 'page=1', $pagestring);
	$pagestring2 = preg_replace('/page=([0-9]*)/', 'page=2', $pagestring);
	$pagestringlpm1 = preg_replace('/page=([0-9]*)/', 'page='.$lpm1, $pagestring);
	$pagestringlast = preg_replace('/page=([0-9]*)/', 'page='.$lastpage, $pagestring);

	$pagination = "";
	if($lastpage > 1)
	{	
		$pagination .= '<ul class="pagination pagination-sm pagination-flat bg-transparent float-right"';
		$pagination .= ">";

		//previous button
		if ($page > 1)
		{
			$url_query = preg_replace('/page=([0-9]*)/', 'page='.$prev, $pagestring); 
			$pagination .= '<li class="page-item"><a href="'. $targetpage .'?'. $url_query .'" class="page-link"><i class="mi-keyboard-arrow-left"></i></a><li>';

		}
		else
			$pagination .= '<li class="page-item disabled"><a href="#" class="page-link"><i class="mi-keyboard-arrow-left"></i></a><li>';

		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination .= '<li class="page-item active"><a href="#" class="page-link">'.$counter.'</a></li>';
				else
				{
					$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
					$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$url_query.'" class="page-link">'.$counter.'</a></li>';
				}					
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 2 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{					
					if ($counter == $page)
						$pagination .= '<li class="page-item active"><a href="#" class="page-link">'.$counter.'</a></li>';
					else
					{
						$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
						$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$url_query.'" class="page-link">'.$counter.'</a></li>';
					}				
				}
				$pagination .= '<li class="page-item"><a href="#" class="page-link">...</a></li>';
				$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$pagestringlpm1.'" class="page-link">'.$lpm1.'</a></li>';
				$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$pagestringlast.'" class="page-link">'.$lastpage.'</a></li>';	
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{	
				$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$pagestring1.'" class="page-link">1</a></li>';
				$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$pagestring2.'" class="page-link">2</a></li>';
				$pagination .= '<li class="page-item"><a href="#" class="page-link">...</a></li>';
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination .= '<li class="page-item active"><a href="#" class="page-link">'.$counter.'</a></li>';
					else
					{
						$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
						$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$url_query.'" class="page-link">'.$counter.'</a></li>';
					}
				}
				$pagination .= '<li class="page-item"><a href="#" class="page-link">...</a></li>';
				$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$pagestringlpm1.'" class="page-link">'.$lpm1.'</a></li>';
				$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$pagestringlast.'" class="page-link">'.$lastpage.'</a></li>';
			}
			//close to end; only hide early pages
			else
			{
				$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$pagestring1.'" class="page-link">1</a></li>';
				$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$pagestring2.'" class="page-link">2</a></li>';
				$pagination .= '<li class="page-item"><a href="#" class="page-link">...</a></li>';

				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination .= '<li class="page-item active"><a href="#" class="page-link">'.$counter.'</a></li>';
					else
					{
						$url_query = preg_replace('/page=([0-9]*)/', 'page='.$counter, $pagestring);
						$pagination .= '<li class="page-item"><a href="'.$targetpage.'?'.$url_query.'" class="page-link">'.$counter.'</a></li>';
					}
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
		{
			$url_query = preg_replace('/page=([0-9]*)/', 'page='.$next, $pagestring);
			$pagination .= '<li class="page-item"><a href="'. $targetpage .'?'. $url_query .'" class="page-link"><i class="mi-keyboard-arrow-right"></i></a><li>';
		}
		else
			$pagination .= '<li class="page-item disabled"><a href="#" class="page-link"><i class="mi-keyboard-arrow-right"></i></a><li>';
		$pagination .= "</ul>\n";
	}
	
	return $pagination;

}

function a_fetch_video_sources($sort = '')
{
	if ($sort != '')
	{
		$sql = "SELECT * FROM pm_sources ORDER BY ". $sort ." DESC";
	}
	else
	{
		$sql = "SELECT * FROM pm_sources";
	}
	
	$result = mysql_query($sql);
	if(!$result)
		return false;
	$src = array();
	$id = 0;

	while($row = mysql_fetch_assoc($result))
	{
		if ($row['source_name'] == 'mp3')
		{
			$row['source_rule'] = '/(.*?)\.mp3/i';	
		}
		
		if ($row['source_name'] == 'other')
		{
			$row['source_rule'] = '/(.*?)\.(flv|mp4|mov|avi|divx|mp3|wmv|mkv|asf|wma|m4v|m4a|3gp|3g2)/i';
		}
		
		if (in_array($row['source_name'], array('divx', 'windows media player', 'quicktime', 'mp3')))
		{
			$row['php_namespace'] = '\phpmelody\sources\src_localhost';
		}
		else
		{
			$row['php_namespace'] = '\phpmelody\sources\src_'. str_replace(array('.', ' '), '', $row['source_name']);
		}
		
		$src[ $row['source_id'] ] = $row;
	}
	foreach($src as $id => $source)
	{
		$src[$source['source_name']] = $source;
	}
	
	return array_reverse($src, true);
}


function is_url($url)
{
	$url_regex = "/^((((ht|f)tp(s?))\:)?\/\/)?(www\.|[a-zA-Z]+\.)*[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,6})(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\;\?\'\\\+:&%\$#\=~_\-]+))*$/";
	if(preg_match($url_regex, $url))
		return true;

	return false;
}

function is_ip_url($url)
{
	$url_ip_regex = '/^(((ht|f)tp(s?))\:\/\/)?\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/';
	if(preg_match($url_ip_regex, $url))
		return true;

	return false;
}

/**
 * @deprecated since 1.7
 * @return 
 */
function fetch_languages() 
{
	return array();
}

function a_list_cats_simple()
{
	return load_categories(); // @since 2.2
}

function a_list_banned($from = 0, $limit = 20)
{
	global $banlist_nonce;
	
	$sql = "SELECT * FROM pm_banlist ORDER BY id DESC LIMIT ".$from.", ".$limit;
	$result = mysql_query($sql);
	if(!$result)
		return "There was an error while processing this request. <br /><strong>MySQL returned:</strong> ".mysql_error();
	$banlist = array();
	while($row = mysql_fetch_assoc($result))
	{
		$banlist[ $row['user_id'] ] = $row;
	}
	mysql_free_result($result);
	
	$total = pm_count($banlist);
	if($total > 0)
	{
		$entries = '';
		$alt = 1;
		foreach($banlist as $user_id => $info)
		{
			$col = ($alt % 2) ? 'table_row1' : 'table_row2';
			$alt++;
			
			$sql = "SELECT username FROM pm_users WHERE id = '".$user_id."'";
			$result = mysql_query($sql);
			$username = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			if($info['reason'] == '')
				$info['reason'] = "None";
			$entries .= "
			  <tr class=\"".$col." text-warning\">
				<td align=\"center\" style=\"text-align:center\">".$user_id."</td>
				<td><a href=\""._URL."/". _ADMIN_FOLDER ."/edit-user.php?uid=".$user_id."\" class='text-warning'>".$username['username']."</a></td>
				<td>".$info['reason']."</td>
				<td align=\"center\" class=\"table-col-action\" style=\"text-align:center\">";

			$entries .= '<a href="banned-users.php?a=delete&uid='.$user_id.'&_pmnonce='. $banlist_nonce['_pmnonce'] .'&_pmnonce_t='. $banlist_nonce['_pmnonce_t'] .'" rel="tooltip" title="Remove ban" class="btn btn-sm btn-outline alpha-success text-success-400 border-success-400 border-1 mr-1 font-size-sm">Remove ban</a>';
			$entries .= '</td>';
			$entries .= '</tr>';
		}
	} 
	elseif($count == 0) 
	{
		$entries .= "
		  <tr>
			<td colspan=\"4\" align=\"center\">No banned users found.</td>
		  </tr>";
	}
	return $entries;
}

function is_user_banned($user_id)
{
	$sql = "SELECT COUNT(*) as total_found 
			FROM pm_banlist 
			WHERE user_id = '". $user_id ."'";
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return false;
	}
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	if ($row['total_found'] > 0)
	{
		return true;
	}
	
	return false;
}

function get_banlist()
{
	$banlist = array();
	
	$sql = "SELECT * 
			FROM pm_banlist";
	$result = @mysql_query($sql);
	if ( ! $result)
	{
		return array();
	}
	
	while ($row = mysql_fetch_assoc($result))
	{
		$banlist[ $row['user_id'] ] = $row;
	}
	mysql_free_result($result);
	
	return $banlist;
}

if (!function_exists('json_encode'))
{
  function json_encode($a = false)
  {
	if (is_null($a)) return 'null';
	if ($a === false) return 'false';
	if ($a === true) return 'true';
	if (is_scalar($a))
	{
	  if (is_float($a))
	  {
		// Always use "." for floats.
		return floatval(str_replace(",", ".", strval($a)));
	  }

	  if (is_string($a))
	  {
		static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
		return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
	  }
	  else
		return $a;
	}
	$isList = true;
	for ($i = 0, reset($a); $i < pm_count($a); $i++, next($a))
	{
	  if (key($a) !== $i)
	  {
		$isList = false;
		break;
	  }
	}
	$result = array();
	if ($isList)
	{
	  foreach ($a as $v) $result[] = json_encode($v);
	  return '[' . join(',', $result) . ']';
	}
	else
	{
	  foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
	  return '{' . join(',', $result) . '}';
	}
  }
}

function cache_this($type, $signature) {

	$cacheFile = './temp/'. md5($signature) .'-'. date('Ymd');
	$cacheTime = 24 * 3600;
	$now = time();
	$last_update = 0;

	if ($file_exists = file_exists($cacheFile))
	{
		$last_update = filemtime($cacheFile);
	}
	
	// Serve the cached content if present
	if ($file_exists &&  ($now - $cacheTime) < $last_update) 
	{
		return file_get_contents($cacheFile);
	}

	$date = getdate();
	$last_mo = mktime(0, 0, 0, $date['mon']-1, 1, $date['year']);
	
	$prev_cache = './temp/'. md5($signature) .'-'. date('Ym', $last_mo);
	if (file_exists($prev_cache))
	{
		@unlink($prev_cache);
	}

	// Cache the contents to a file
	$cached = @fopen($cacheFile, 'w');
	if ($type == 'read_version')
	{
		$content = read_version();
	}
	else if ($type == 'get_rss_news')
	{
		$content = get_rss_news(3);
	}
	else if ($type == 'get_theme_store_data')
	{
		$content = get_theme_store_data();
	}
	@fwrite($cached, $content, strlen($content));
	@fclose($cached);
	return $content;
}

function mass_delete_videos($uniq_ids = array())
{
	$delete_ids_str = '';
	$total_videos = pm_count($uniq_ids);
	
	if ($total_videos > 0)
	{
		if ($total_videos > 20)
		{
			$start  = 0;
			$inc	= 15;

			while ($start <= $total_videos)
			{	
				$delete_ids_str = '';
				$i = 0;
				
				for ($i = $start; $i < $start + $inc; $i++)
				{
					$delete_ids_str .= "'". $uniq_ids[$i] ."', ";
				}

				$delete_ids_str = substr($delete_ids_str, 0, -2);
			
				if (strlen($delete_ids_str) > 2)
				{
					// handle playlists @since v2.2
					for ($i = $start; $i < $start + $inc; $i++)
					{
						$video_id = uniq_id_to_video_id($uniq_ids[$i]);
						$playlist_ids = array();
						
						$sql = "SELECT list_id 
								FROM pm_playlist_items 
								WHERE video_id = ". $video_id;
					
						if ($result = @mysql_query($sql))
						{
							$in_playlists = false;
							while ($row = mysql_fetch_assoc($result))
							{
								$playlist_ids[] = (int) $row['list_id'];
								$in_playlists = true;
							}
							mysql_free_result($result);
						
							if ($in_playlists)
							{
								$sql = "DELETE FROM pm_playlist_items
										WHERE video_id = ". $video_id;
								@mysql_query($sql);
				
								$sql = "UPDATE pm_playlists 
										SET items_count = items_count - 1 
										WHERE list_id IN (". implode(',', $playlist_ids) .")";
								@mysql_query($sql);
							}
						}
					}
					
					@mysql_query("DELETE FROM pm_videos		 WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_comments 	 WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_reports 	 WHERE entry_id IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id  IN (". $delete_ids_str .")");
					//@mysql_query("DELETE FROM pm_favorites 	 WHERE uniq_id  IN (". $delete_ids_str .")"); // @deprecated since v2.2
					@mysql_query("DELETE FROM pm_chart 		 WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_tags 		 WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_embed_code  WHERE uniq_id  IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_bin_rating_meta  WHERE uniq_id IN (". $delete_ids_str .")");
					@mysql_query("DELETE FROM pm_bin_rating_votes  WHERE uniq_id IN (". $delete_ids_str .")");
				}
				$start = $start + $inc;
			}
		}
		else
		{
			$delete_ids_str = '';
			foreach ($uniq_ids as $k => $uniq_id)
			{
				$delete_ids_str .= "'". $uniq_id ."', ";
			}
			$delete_ids_str = substr($delete_ids_str, 0, -2);
			
			foreach ($uniq_ids as $k => $uniq_id)
			{
				$video_id = uniq_id_to_video_id($uniq_id);
				$playlist_ids = array();
				
				$sql = "SELECT list_id 
						FROM pm_playlist_items 
						WHERE video_id = ". $video_id;
			
				if ($result = @mysql_query($sql))
				{
					$in_playlists = false;
					while ($row = mysql_fetch_assoc($result))
					{
						$playlist_ids[] = (int) $row['list_id'];
						$in_playlists = true;
					}
					mysql_free_result($result);
				
					if ($in_playlists)
					{
						$sql = "DELETE FROM pm_playlist_items
								WHERE video_id = ". $video_id;
						@mysql_query($sql);
		
						$sql = "UPDATE pm_playlists 
								SET items_count = items_count - 1 
								WHERE list_id IN (". implode(',', $playlist_ids) .")";
						@mysql_query($sql);
					}
				}
			}

			@mysql_query("DELETE FROM pm_videos		 WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_comments 	 WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_reports 	 WHERE entry_id IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id  IN (". $delete_ids_str .")");
			//@mysql_query("DELETE FROM pm_favorites 	 WHERE uniq_id  IN (". $delete_ids_str .")"); // @deprecated since v2.2
			@mysql_query("DELETE FROM pm_chart 		 WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_tags 		 WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_embed_code  WHERE uniq_id  IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_bin_rating_meta  WHERE uniq_id IN (". $delete_ids_str .")");
			@mysql_query("DELETE FROM pm_bin_rating_votes  WHERE uniq_id IN (". $delete_ids_str .")");
		}
		return true;
	}
	
	return false;
}

function add_config($name, $value)
{
	global $config;
	
	if (array_key_exists($name, $config))
	{
		update_config($name, $value, true);
		return true;
	}
	$value = trim($value);
	$value = secure_sql($value);
	$name = secure_sql($name);
	
	$sql = "INSERT INTO pm_config (name, value) 
			VALUES ('". $name ."', '". $value ."')";
	$result = mysql_query($sql);
	if ( ! $result)
	{
		return array(mysql_error(), mysql_errno());
	}
	
	$config[$name] = $value;
	
	return true;
}

function autosync($force = false)
{
	global $config;
	$now = time();
	
	$config['last_autosync'] = (int) $config['last_autosync'];
	
	if (($config['last_autosync'] < ($now - 2592000)) || $force === true) 
	{
		@ini_set('max_execution_time', 180);
		
		// Total videos
		$total = 0;
		$query = "SELECT COUNT(*) as total 
				  FROM pm_videos
				  WHERE added <= '". $now ."'
				    AND video_type = ". IS_VIDEO;
		$result =  mysql_query($query);
		$total = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'published_videos'";
		
		$total = 0;
		$query = "SELECT COUNT(*) as total 
				  FROM pm_videos
				  WHERE video_type = ". IS_VIDEO;
		$result =  mysql_query($query);
		$total = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'total_videos'";
		
		$query = "SELECT COUNT(*) as total 
				  FROM pm_videos_trash";
		$result =  mysql_query($query);
		$total = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'trashed_videos'";
		
		$categories = load_categories();
		
		if ($total['total'] > 0 && pm_count($categories) > 0)
		{
			// Count total videos for each category	
			$k = 1;
			foreach ($categories as $cid => $arr)
			{
				$total = 0;
				$query = "SELECT COUNT(*) as total 
							FROM pm_videos 
							WHERE video_type = ". IS_VIDEO ." 
							  AND (category LIKE '". $cid ."' 
							   OR category LIKE '". $cid .",%' 
							   OR category LIKE '%,". $cid ."' 
							   OR category LIKE '%,". $cid .",%')";
	
				$result =  mysql_query($query);
				$total = mysql_fetch_assoc($result);
				mysql_free_result($result);
	
				$sql[] = "UPDATE pm_categories SET total_videos = '". $total['total'] ."' WHERE id = '". $cid ."'";
				
				$total = 0;
				$query = "SELECT COUNT(*) as total 
							FROM pm_videos 
							WHERE added <= '". $now ."' 
							  AND video_type = ". IS_VIDEO ." 
							  AND (category LIKE '". $cid ."' 
							   OR category LIKE '". $cid .",%' 
							   OR category LIKE '%,". $cid ."' 
							   OR category LIKE '%,". $cid .",%')";
	
				$result =  mysql_query($query);
				$total = mysql_fetch_assoc($result);
				mysql_free_result($result);
	
				$sql[] = "UPDATE pm_categories SET published_videos = '". $total['total'] ."' WHERE id = '". $cid ."'";
				
				if ($k % 3 == 0)
				{
					sleep(1);
				}
				
				$k++;
			}
		}
		
		// Total articles
		$total = 0;
		$query = "SELECT COUNT(*) as total 
				  FROM art_articles
				  WHERE date <= '". $now ."'
					AND status = '1'";
		$result =  mysql_query($query);
		$total = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'published_articles'";
		
		$total = 0;
		$query = "SELECT COUNT(*) as total 
				  FROM art_articles";
		$result =  mysql_query($query);
		$total = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'total_articles'";
		
		// Count total articles for each category
		$categories = art_get_categories();
		
		$k = 0;
		foreach ($categories as $cid => $arr)
		{
			$total = 0;
			$query = "SELECT COUNT(*) as total 
						FROM art_articles 
						WHERE category LIKE '". $cid ."' 
						   OR category LIKE '". $cid .",%' 
						   OR category LIKE '%,". $cid ."' 
						   OR category LIKE '%,". $cid .",%'";
	
			$result =  mysql_query($query);
			$total = mysql_fetch_assoc($result);
			mysql_free_result($result);
	
			$sql[] = "UPDATE art_categories SET total_articles = '". $total['total'] ."' WHERE id = '". $cid ."'";
			
			$total = 0;
			$query = "SELECT COUNT(*) as total 
						FROM art_articles 
						WHERE date <= '". $now ."'  
						  AND status = '1' 
						  AND (category LIKE '". $cid ."' 
						   OR category LIKE '". $cid .",%' 
						   OR category LIKE '%,". $cid ."' 
						   OR category LIKE '%,". $cid .",%')";
	
			$result =  mysql_query($query);
			$total = mysql_fetch_assoc($result);
			mysql_free_result($result);
	
			$sql[] = "UPDATE art_categories SET published_articles = '". $total['total'] ."' WHERE id = '". $cid ."'";
			
			if ($k % 3 == 0)
			{
				sleep(1);
			}
			$k++;
		}

		update_config('total_series', count_entries('pm_series', '', ''));
		update_config('total_episodes', count_entries('pm_episodes', '', ''));

		$all_series = get_series_list();
		foreach ($all_series as $k => $series_data)
		{
			$query = "SELECT COUNT(*) as total_episodes, MAX(season_no) as max_season_number 
						FROM pm_episodes WHERE 
						series_id = ". $series_data['series_id'];
			$result =  mysql_query($query);
			$total = mysql_fetch_assoc($result);
			mysql_free_result($result);

			$sql[] = "UPDATE pm_series 
						 SET seasons_count = ". $total['max_season_number'] .", 
						 	 episodes_count = ". $total['total_episodes'] ." 
					   WHERE series_id = ". $series_data['series_id'];
		}

		$updated_genre_ids = array();
		$query = "SELECT genre_id, COUNT(*) as total 
				  FROM pm_genre_relationships 
				  WHERE item_type = ". IS_SERIES ."
				  GROUP BY genre_id";
		if ($result =  mysql_query($query))
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$sql[] = "UPDATE pm_genres SET total_series = ". $row['total'] ." WHERE id = ". $row['genre_id'];
				$updated_genre_ids[] = (int) $row['genre_id'];
			}
		}
		$genres = get_genres();
		foreach ($genres as $genre_id => $genre)
		{
			if ( ! in_array((int) $genre_id, $updated_genre_ids))
			{
				$sql[] = "UPDATE pm_genres SET total_series = 0 WHERE id = ". $genre_id;
			}
		}
		
		$sql[] = "UPDATE pm_config SET value='". $now ."' WHERE name='last_autosync'";
		
		// Total pages
		$total = 0;
		$query = "SELECT COUNT(*) as total 
				  FROM pm_pages";
		$result =  @mysql_query($query);
		$total = @mysql_fetch_assoc($result);
		@mysql_free_result($result);
		
		$sql[] = "UPDATE pm_config SET value='". $total['total'] ."' WHERE name = 'total_pages'";
		
		$total = pm_count($sql);
		$errors = array();
		
		for($i = 0; $i < $total; $i++)
		{
			$result = @mysql_query($sql[ $i ]);
			if(!$result)
			{
				$errors[] = mysql_error();
			}
		}
		
		if (pm_count($errors) > 0)
		{
			return false;
		}
	}
	
	return true;
}

function restricted_access($exit = true)
{
	echo '
	<div class="content-wrapper">
	<div class="content">
	<h2>Restricted access</h2>
	<div class="alert alert-warning">
		Sorry, you do not have access to this area.
	</div>
	<hr />
	<a href="index.php" class="btn btn-secondary">&larr; Dashboard</a>
	</div></div>';

	include('footer.php');
	if ($exit) exit();	
	return true;
}
function dropdown_jwskins() {

//$path = ABSPATH ."/skins";
$path = ABSPATH ."/players/jwplayer5/skins"; // @since v2.2
$dh = opendir($path);
$form_file = '';
while (($file = readdir($dh)) !== false) {
	if($file != "." && $file != ".."  && $file != "..") {
		if (strpos(strtolower($file), ".zip"))
			$form_file .= "<option value=\"".$file."\">".ucfirst(trim($file, ".zip"))."</option> \n";
	}
}
closedir($dh);
return $form_file;
}

function show_form_item_date($timestamp = 0, $with_hh_mm = true) 
{
	if ( ! $timestamp)
		$timestamp = time();
	
	$months = array(1 => 'Jan',
					2 => 'Feb',
					3 => 'Mar',
					4 => 'Apr',
					5 => 'May',
					6 => 'Jun',
					7 => 'Jul',
					8 => 'Aug',
					9 => 'Sep',
					10 => 'Oct',
					11 => 'Nov',
					12 => 'Dec' 
				);	
	
	$date = date('n,d,Y,h,i,s,A', $timestamp);
	$date = explode(',', $date);
	
	$sel_mon = $date[0];
	$sel_day = $date[1];
	$sel_year = $date[2];
	$sel_hour = $date[3];
	$sel_min = $date[4];
	$sel_sec = $date[5];
	$sel_ampm = $date[6];
	
	
	$return = '<div class="input-group input-group-sm custom-date-form">';
	
	$return .= '<select name="date_month" class="form-control form-control-sm custom-select border-right-0">' . "\n";
	for ($i = 1; $i <= 12; $i++)
	{
		$selected = ($i == $sel_mon) ? 'selected="selected"' : '';
		$return .= '<option value="'. $i .'" '. $selected .'>'. $months[$i] .'</option>' . "\n";
	}
	$return .= '</select>' . "\n";
	
	$return .= '<input type="text" name="date_day" value="'. $sel_day .'" size="2" maxlength="2" class="form-control form-control-sm border-right-0" data-popup="tooltip" data-html="true" data-original-title="Day" /> ' . "\n";
	$return .= '<input type="text" name="date_year" value="'. $sel_year .'" size="4" maxlength="4" class="form-control form-control-sm" data-popup="tooltip" data-html="true" data-original-title="Year" /> ' . "\n";
	//$return .= '<div class="input-group-prepend bg-transparent"><span class="input-group-text"> at: </span></div>';
	if ($with_hh_mm)
	{
		$return .= '<input type="text" name="date_hour" value="'. $sel_hour .'"  size="2" maxlength="2" class="form-control form-control-sm border-left-0 border-right-0" data-popup="tooltip" data-html="true" data-original-title="Hour" />' . "\n";
		$return .= '<div class="input-group-prepend bg-transparent"><span class="input-group-text bg-transparent">:</span></div>';
		$return .= '<input type="text" name="date_min" value="'. $sel_min .'" size="2" maxlength="2" class="form-control form-control-sm border-right-0" data-popup="tooltip" data-html="true" data-original-title="Minutes" />' . "\n";
		$return .= '<select name="date_ampm" class="form-control form-control-sm custom-select">' . "\n";
		$return .= ' <option value="am"';
		$return .= ($sel_ampm == 'AM') ? ' selected="selected" ' : '';
		$return .= '>AM</option>';
		$return .= ' <option value="pm"';
		$return .= ($sel_ampm == 'PM') ? ' selected="selected" ' : '';
		$return .= '>PM</option>';
		$return .= '</select>' . "\n";
		$return .= '</div>';
	}
	else
	{
		$return .= '</div>';
		$return .= '<input type="hidden" name="date_hour" value="'. $sel_hour .'"  size="2" maxlength="2" /> ' . "\n";
		$return .= '<input type="hidden" name="date_min" value="'. $sel_min .'" size="2" maxlength="2" />' . "\n";
		$return .= '<input type="hidden" name="date_ampm" value="'. (($sel_ampm == 'PM') ? 'PM' : 'AM') .'" size="2" maxlength="2" />' . "\n";
	}
	$return .= '<input type="hidden" name="date_sec" value="'. $sel_sec .'" size="2" maxlength="2" />' . "\n";
	
	// explain
	$return .= "\n\n";
	$return .= '';
	return $return;
}

function validate_item_date($post)
{
	$mon = (int) $post['date_month'];
	$day = (int) $post['date_day'];
	$year = (int) $post['date_year'];
	$hour = (int) $post['date_hour'];
	$min = (int) $post['date_min'];
	$sec = (int) $post['date_sec'];
	$ampm = strtoupper($post['date_ampm']);
	$ampm = (empty($ampm)) ? 'AM' : $ampm;
	
	if (($mon > 12 || $mon < 1) || ($day > 31 || $day < 1) || ($year < 1970 || $year > 9999) || ($hour > 12 || $hour < 0) || ($min > 60 || $min < 0) || ($sec > 60 || $sec < 0))
	{
		return false;
	}
	
	$days_in_month = date('t', $mm = mktime(1, 0, 0, $mon, 1, $year));
	 
	// the user meant the last day of the month for sure. autofix if mistake was made
	if ($day > $days_in_month)
	{
		$day = $days_in_month;
	}
	
	if ($ampm == 'AM')
	{
		if ($hour == 12)
		{
			$hour = 0;
		}
	}
	
	if ($ampm == 'PM')
	{
		$hour += 12;
		if ($hour == 24)
		{
			$hour = 12;
		}
	}
	
	return array('date_month' => $mon, 
				 'date_day' => $day, 
				 'date_year' => $year, 
				 'date_hour' => $hour, 
				 'date_min' => $min,
				 'date_sec' => $sec,
				 'date_ampm' => $ampm);
}

// wrapper for mktime() - uses data from $_POST
function pm_mktime($post = array())
{
	return mktime((int) $post['date_hour'], (int) $post['date_min'], (int) $post['date_sec'], (int) $post['date_month'], (int) $post['date_day'], (int) $post['date_year']);
}

function array_sort($array, $on, $order=SORT_ASC)
{
	$new_array = array();
	$sortable_array = array();

	if (pm_count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}

		switch ($order) {
			case SORT_ASC:
				asort($sortable_array);
			break;
			case SORT_DESC:
				arsort($sortable_array);
			break;
		}

		foreach ($sortable_array as $k => $v) {
			$new_array[$k] = $array[$k];
		}
	}

	return $new_array;
}

function create_preroll_ad($ad_data)
{	
	$defaults = array('name' => '',
					  'duration' => 30,
					  'user_group' => 0, // 0 = everyone; 1 = logged only; 2 = guests only
					  'impressions' => 0,
					  'status' => 1,
					  'code' => '',
					  'skip' => 0,
					  'skip_delay_seconds' => 5,
					  'ignore_category' => array(),
					  'ignore_source' => array(),
					  'disable_stats' => 0,
					);
	
	$ad_data['duration'] = abs((int) $ad_data['duration']);
	$ad_data['skip'] = abs((int) $ad_data['skip_delay_radio']);
	$ad_data['skip_delay_seconds'] = abs((int) $ad_data['skip_delay_seconds']);
	$ad_data['disable_stats'] = abs((int) $ad_data['disable_stats']);

	$ad_data = array_merge($defaults, $ad_data);
	
	if ($ad_data['name'] == '')
	{
		$ad_data['name'] = date('F j, Y g:i A');
	}
	
	$ad_data['duration'] = (int) $ad_data['duration'];
	if ($ad_data['duration'] == 0)
	{
		$ad_data['duration'] = 30;
	}
	
	$ad_options = array();
	
	$options = array('skip' => (int) $ad_data['skip_delay_radio'],
					 'skip_delay_seconds' => (int) $ad_data['skip_delay_seconds'],
					 'ignore_category' => (array) $ad_data['ignore_category'],
					 'ignore_source' => (array) $ad_data['ignore_source'],
					 'disable_stats' => (int) $ad_data['disable_stats']
					);
	$options = serialize($options);
	
	$sql = "INSERT INTO pm_preroll_ads 
					(name, duration, user_group, impressions, status, code, options)
			VALUES ('". secure_sql(trim($ad_data['name'])) ."',
					'". $ad_data['duration'] ."',
					'". $ad_data['user_group'] ."',
					'0',
					'". $ad_data['status'] ."',
					'". secure_sql($ad_data['code']) ."',
					'". secure_sql($options) ."')";
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	return true;
}

function update_preroll_ad($ad_id, $ad_data)
{
	$ad_id = (int) $ad_id;
	
	if ( ! $ad_id)
		return false;

	$ad_data['name'] = trim($ad_data['name']);
	$ad_data['duration'] = abs( (int) $ad_data['duration']);
	$ad_data['user_group'] = (int) $ad_data['user_group'];
	$ad_data['skip_delay_seconds'] = abs( (int) $ad_data['skip_delay_seconds']);
	
	if ($ad_data['duration'] > 0 && $ad_data['duration'] < $ad_data['skip_delay_seconds'])
	{
		$ad_data['skip_delay_seconds'] = $ad_data['duration'] - 1;
	}
	
	$options = array('skip' => (int) $ad_data['skip_delay_radio'],
					 'skip_delay_seconds' => (int) $ad_data['skip_delay_seconds'],
					 'ignore_category' => (array) $ad_data['ignore_category'],
					 'ignore_source' => (array) $ad_data['ignore_source'],
					 'disable_stats' => (int) $ad_data['disable_stats']
					);
	$options = serialize($options);
	
	$sql = "UPDATE pm_preroll_ads 
			SET name = '". secure_sql($ad_data['name']) ."', 
				duration = ". $ad_data['duration'] .",
				user_group = ". $ad_data['user_group'] .", 
				status = '". $ad_data['status'] ."',
				code = '". secure_sql($ad_data['code']) ."',
				options = '". secure_sql($options) ."'
			WHERE id = $ad_id";

	return mysql_query($sql);
}

function delete_preroll_ad($ad_id)
{
	$ad_id = (int) $ad_id;
	
	if ( ! $ad_id)
		return false;
	
	$sql = "DELETE FROM pm_preroll_ads 
			WHERE id = '". $ad_id ."'";

	return mysql_query($sql);
}

function get_theme_store_data()
{
	$data = array();
	$rss = new lastRSS;
	$rssurl = "http://feeds.feedburner.com/PMThemes";

	if ( ! $data = $rss->get($rssurl))
	{
		$data = array();
	}
	
	return serialize($data);
}
function detect_russian($text) {
	return preg_match('/[А-Яа-яЁё]/u', $text);
}

function admin_custom_fields_row($meta_id, $meta)
{
	if (is_meta_key_reserved($meta['meta_key']))
		return '';
	$html = '
	<div id="meta-row-'. $meta_id .'">
		<span id="update-response-'. $meta_id .'"></span>
		<div class="form-row">
			<div class="col-md-3">
				<div class="form-group">
					<input type="text" name="meta['. $meta_id .'][key]" value="'. htmlspecialchars($meta['meta_key'], ENT_QUOTES) .'" maxlength="255" class="form-control form-control-sm" placeholder="Custom name" />
				</div>
			</div>
			<div class="col-6">
				<div class="form-group">
					<input type="text" name="meta['. $meta_id .'][value]" value="'. $meta['meta_value'] .'" class="form-control form-control-sm" id="meta_value_field" />
				</div>
			</div>
		<div class="col-md-3">
			<div class="form-group">
				<button name="update_meta_btn" id="update_meta_btn_'. $meta_id .'" value="Update" class="btn btn-sm btn-light">Update</button>
				<button name="delete_meta_btn" id="delete_meta_btn_'. $meta_id .'" value="Delete" class="btn btn-sm btn-link text-danger ml-0 pl-1 pr-1"><i class="icon-bin"></i></button>
			</div>
		</div>
		</div>
	</div>';
	
	return $html;
}

function admin_custom_fields_add_form($item_id, $item_type)
{
	$select_html = '';
	$item_type = (int) $item_type;
	
	$sql_where = ($item_type)  ? " WHERE item_type = $item_type " : '';
	
	$sql = "SELECT meta_key 
			FROM pm_meta 
			$sql_where
			GROUP BY meta_key
			HAVING meta_key NOT LIKE '\_%' 
			ORDER BY meta_key ASC
			LIMIT 30";
	
	$keys = array();
	
	if ( $result = mysql_query($sql))
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$keys[] = $row['meta_key'];
		}
		
		mysql_free_result($result);
		
		if (pm_count($keys) > 0)
		{
			$select_html .= '<select id="meta_key_select" name="meta_key_select" class="form-control custom-select custom-select-sm">';
			$select_html .= "\n\t";
			$select_html .= '<option value="_nokey">Select existing field...</option>';
			foreach ($keys as $k => $key)
			{
				$select_html .= "\n\t";
				$select_html .= '<option value="'. htmlspecialchars($key, ENT_QUOTES) .'">'. $key .'</option>';
			}
			$select_html .= "\n";
			$select_html .= '</select>';
		}
	}


	
	$html = '
	<div id="new-meta-placeholder"></div>
	<div id="new-meta-error" class="d-block"></div>
	<div class="form-row">';
	$html .= '<div class="col-md-3">';
		
	if ($select_html != '')
	{
		$html .= $select_html;
		$html .= '<input type="text" name="meta_key" value="" maxlength="255" class="form-control form-control-sm hide" placeholder="Custom name" />';
	}
	else
	{
		$html .= '<input type="text" name="meta_key" value="" maxlength="255" class="form-control form-control-sm" placeholder="Custom name" />';
	}
	$html .= '</div>';

		$html .= '<div class="col-6">';
		$html .= '<input type="text" name="meta_value" class="form-control form-control-sm" value="" placeholder="Custom value" />
					<input type="hidden" name="meta_item_id" value="'. $item_id .'" />
					<input type="hidden" name="meta_item_type" value="'. $item_type .'" />';
		$html .= '</div>';
	
	$html .= '<div class="col-md-3">';
	$html .= '<button name="add_meta_btn" id="add_meta_btn" value="Update" class="btn btn-sm btn-success" /><i class="mi-control-point"></i> Add</button>';
	$html .= '</div>';

	if ($select_html != '')
	{
		$html .= '<div class="">';
		$html .= '<a href="#" id="meta_switch_input_select" class="btn btn-sm btn-link text-muted text-uppercase font-weight-semibold pl-2 hide">Select Existing Field</a>';
		$html .= '<a href="#" id="meta_switch_select_input" class="btn btn-sm btn-link text-muted text-uppercase font-weight-semibold pl-2">Add New Field</a>'; 
		$html .= '</div>';
	}

	$html .= '</div>';

	return $html;
}

/**
 * Checks the freshness of cached result for search subscriptions
 *  
 * @param int $last_query_time UNIX timestamp
 * @return boolean TRUE if fresh, FALSE if old 
 */
function import_subscription_cache_fresh($last_query_time)
{
	global $time_now;
	
	$freshness = 3600; // seconds
	$time_now = ( ! $time_now) ? time() : $time_now;
	
	return (($time_now - $last_query_time) <= $freshness) ? true : false;
}

function get_import_subscription($sub_id)
{
	$sql = "SELECT * FROM pm_import_subscriptions 
			WHERE sub_id = ". $sub_id;
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	$sub = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	$sub['data'] = unserialize($sub['data']);
	
	return $sub;
}

function get_import_subscriptions($type = 'search', $start = 0, $limit = 0)
{
	global $userdata;
	
	$sql = "SELECT pm_import_subscriptions.*, pm_users.username, pm_users.channel_slug, pm_users.channel_verified  
			FROM pm_import_subscriptions 
			JOIN pm_users ON (pm_import_subscriptions.user_id = pm_users.id) 
			WHERE ";

	switch ($type)
	{
		default:
		case 'search':
			
			$sql .= " sub_type IN ('search') ";
			
		break;
		
		case 'user':
			
			$sql .= " sub_type IN ('user', 'user-favorites', 'user-playlist') ";
			
		break;
	}
	$sql .= ($userdata['power'] != U_ADMIN) ? ' AND user_id = '. $userdata['id'] : '';		
	$sql .= " ORDER BY pm_import_subscriptions.sub_id DESC"; 
	
	if ($limit)
	{
		$sql .= " LIMIT $start, $limit";
	}

	if ($result = mysql_query($sql))
	{
		$data = array();
		while ($row = mysql_fetch_assoc($result))
		{
			$data[] = $row;
		}
		mysql_free_result($result);
		
		return array('total_results' => pm_count($data),
					 'data' => $data
					);
	}
	
	return array('total_results' => 0, // useful for pagination
				 'data' => array()
				);
}


function a_get_video_subtitles($uniq_id = '')
{
	$sql = "SELECT * FROM pm_video_subtitles WHERE uniq_id = '".$uniq_id."' ORDER BY language ASC";
	$result = mysql_query($sql);
	$subtitles = array();
	while($row = mysql_fetch_assoc($result))
	{
		$subtitles[] = $row;
	}
	return $subtitles;
}


function a_get_languages()
{
	$sql = "SELECT label, tag
		FROM pm_languages
		ORDER BY id ASC"; // already sorted in alphabetical order
	if ( ! $result = mysql_query($sql))
	{
		return array();
	}
	$languages = array();
	while ($row = mysql_fetch_assoc($result))
	{
		$languages[$row['tag']] = $row['label'];
	}
	mysql_free_result($result);

	return $languages;
}

function get_micro_time()
{
	list($microsec, $sec) = explode(" ", microtime());
	return ((float)$microsec + (float)$sec);
}

function get_exec_time($end, $start)
{
	return round($end - $start, 2);
}

/**
 * Returns a user-friendly list for timezone select.
 * Adapted from WP.
 * 
 * @since 2.5
 * @param string $selected_zone
 * @return string 
 */
function pm_timezone_select($selected_zone)
{
	$html_select = '';
	$continents = array('Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');
	$zonen = array();
	
	foreach (timezone_identifiers_list() as $zone)
	{
		//		$html_select .= '<option>'. $zone .'</option>';
		
		$zone = explode('/', $zone);
		if (!in_array($zone[0], $continents))
		{
			continue;
		}
		
		$exists = array(0=> (isset ($zone[0]) && $zone[0]), 1=> (isset ($zone[1]) && $zone[1]), 2=> (isset ($zone[2]) && $zone[2]), );
		$exists[3] = ($exists[0] && 'Etc' !== $zone[0]);
		$exists[4] = ($exists[1] && $exists[3]);
		$exists[5] = ($exists[2] && $exists[3]);
		
		$zonen[] = array('continent'=> ($exists[0] ? $zone[0] : ''), 'city'=> ($exists[1] ? $zone[1] : ''), 'subcity'=> ($exists[2] ? $zone[2] : ''), );
	}
	
	$structure = array();
	
	if ( empty ($selected_zone))
	{
		$html_select .= '<option selected="selected" value="">Select a city...</option>';
	}
	
	foreach ($zonen as $key => $zone)
	{
		// Build value in an array to join later
		$value = array($zone['continent']);
		
		if ( empty ($zone['city']))
		{
			// It's at the continent level (generally won't happen)
			$display = $zone['continent'];
		}
		else
		{
			// It's inside a continent group
			
			// Continent optgroup
			if ( ! isset ($zonen[$key - 1]) || $zonen[$key - 1]['continent'] !== $zone['continent'])
			{
				$label = $zone['continent'];
				$html_select .= '<optgroup label="'. $label .'">';
			}
			
			// Add the city to the value
			$value[] = $zone['city'];
			
			$display = $zone['city'];
			if (! empty ($zone['subcity']))
			{
				// Add the subcity to the value
				$value[] = $zone['subcity'];
				$display .= ' - '.$zone['subcity'];
			}
		}
		
		// Build the value
		$value = join('/', $value);
		$selected = '';
		if ($value === $selected_zone)
		{
			$selected = 'selected="selected" ';
		}
		$html_select .= '<option '.$selected.'value="'. $value .'">'. $display .'</option>';
		
		// Close continent optgroup
		if (! empty ($zone['city']) && (!isset ($zonen[$key + 1]) || (isset ($zonen[$key + 1]) && $zonen[$key + 1]['continent'] !== $zone['continent'])))
		{
			$html_select .= '</optgroup>';
		}
	}
	
	// Do UTC
	$html_select .= '<optgroup label="'. 'UTC' .'">';
	$selected = '';
	if ('UTC' === $selected_zone)
		$selected = 'selected="selected" ';
	$html_select .= '<option '.$selected.'value="'. 'UTC' .'">'. 'UTC' .'</option>';
	$html_select .= '</optgroup>';
	
	return $html_select;
}

/**
 * Load sitemap options
 * 
 * @since 2.7 moved from sitemap.php
 * @return array
 */
function sitemap_load_options()
{
	global $config;
	return unserialize(stripslashes($config['video_sitemap_options']));
}

/**
 * Save sitemap options
 * 
 * @since 2.7 moved from sitemap.php
 * @return bool update_config()
 */
function sitemap_save_options($args = array()) 
{
	global $config;
	
	$defaults = array('media_keywords' => 0,
					  'media_category' => 0,
					  'item_pubDate' => 0,
					  'ping_google' => 'no',
					  'ping_google' => 'no',
					  'total_videos' => (int) $config['published_videos'] // required for showing a reminder  
					);
	
	if ($config['video_sitemap_options'] != '')
	{
		$defaults = sitemap_load_options();
	}
	
	$o = array_merge($defaults, $args);

	return update_config('video_sitemap_options', serialize($o), true);
}

function mass_delete_episodes($episode_ids) 
{
	$episodes = get_episode_list($episode_ids);
	
	foreach ($episodes as $k => $episode_data)
	{
		foreach ($episode_data['media_sources'] as $k => $ms)
		{
			if ($ms['type'] == 'file' && file_exists(_VIDEOS_DIR_PATH . $ms['data']))
			{
				$unlink = unlink(_VIDEOS_DIR_PATH . $ms['data']);
			}
		}
	}

	$episode_ids_str = implode(',', $episode_ids);
	$sql = "DELETE FROM pm_episodes 
			WHERE episode_id IN (". secure_sql($episode_ids_str) .")";

	if ( ! $result = mysql_query($sql))
	{
		return array(
			'type' => 'error', 
			'msg' => 'Could not delete these episodes. An error occurred:'. mysql_error()
		);
	}

	$series_ids = array();
	foreach ($episodes as $k => $episode_data)
	{
		delete_video($episode_data['uniq_id']); // handles total_episodes-- too

		$sql = "UPDATE pm_series 
				SET episodes_count = episodes_count - 1
				WHERE series_id = '". secure_sql($episode_data['series_id']) ."'";
		mysql_query($sql);
	}

	return array(
		'type' => 'ok',
		'msg' => 'The selected episodes have been removed.'
	);
}

/**
 * Permanently delete a video
 * 
 * @param  string $uniq_id 
 * @param  int $video_id 
 * @return boolean 
 */
function delete_video($uniq_id = null, $video_id = null)
{
	global $config, $time_now;

	$video_data = (isset($uniq_id)) 
		? request_video($uniq_id) 
		: request_video(video_id_to_uniq_id($video_id));

	$in_trash = false;
	if (empty($video_data)) 
	{
		// check trash
		$video_data = (isset($uniq_id)) 
		? request_video($uniq_id, 'detail', false, true) 
		: request_video(video_id_to_uniq_id($video_id, true), 'detail', false, true);

		$in_trash = true;
	}

	if (empty($video_data))
	{
		return false;
	}

	if ( ! $in_trash)
	{
		if ($video_data['video_type'] == IS_VIDEO)
		{
			$video_data['category'] = trim($video_data['category'], ',');
		
			$sql = "UPDATE pm_categories SET total_videos = total_videos - 1 ";
			if ($video_data['added'] <= $time_now)
			{
				$sql .= ", published_videos = published_videos - 1 ";
				update_config('published_videos', $config['published_videos'] - 1);
			}
			$sql .= " WHERE id IN (". $video_data['category'] .")";
			mysql_query($sql);
			update_config('total_videos', $config['total_videos'] - 1);
		}
		else if ($video_data['video_type'] == IS_EPISODE)
		{
			$sql = "UPDATE pm_series 
					SET episodes = episodes - 1 
					WHERE series_id = ". $video_data['series_id'];
			mysql_query($sql);
			update_config('total_episodes', $config['total_episodes'] - 1);
		}
		$sql_table = 'pm_videos';
	}
	else
	{
		update_config('trashed_videos', $config['trashed_videos'] - 1);
		$sql_table = 'pm_videos_trash';
	}

	mysql_query("DELETE FROM $sql_table WHERE uniq_id = '". $video_data['uniq_id'] ."'");
	mysql_query("DELETE FROM pm_comments WHERE uniq_id = '". $video_data['uniq_id'] ."'");
	mysql_query("DELETE FROM pm_reports WHERE entry_id = '". $video_data['uniq_id'] ."'");
	mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id = '". $video_data['uniq_id'] ."'");
	mysql_query("DELETE FROM pm_chart WHERE uniq_id = '". $video_data['uniq_id'] ."'");
	mysql_query("DELETE FROM pm_tags WHERE uniq_id = '". $video_data['uniq_id'] ."'");
	mysql_query("DELETE FROM pm_embed_code WHERE uniq_id = '". $video_data['uniq_id'] ."'");
	mysql_query("DELETE FROM pm_bin_rating_meta WHERE uniq_id = '". $video_data['uniq_id'] ."'");
	mysql_query("DELETE FROM pm_bin_rating_votes WHERE uniq_id = '". $video_data['uniq_id'] ."'");
	mysql_query("DELETE FROM pm_meta WHERE item_id = ". $video_data['id'] ." AND item_type = ". $video_data['video_type']);

	if ($video_data['source_id'] == 1)
	{
		if (file_exists(_VIDEOS_DIR_PATH . $video_data['url_flv']) && strlen($video_data['url_flv']) > 0)
		{
			unlink(_VIDEOS_DIR_PATH . $video_data['url_flv']);
		}
	}

	if (file_exists(_THUMBS_DIR_PATH . $video_data['uniq_id'] .'-1.jpg'))
	{
		unlink(_THUMBS_DIR_PATH . $video_data['uniq_id'] .'-1.jpg');
	}

	if (file_exists(_THUMBS_DIR_PATH . $video_data['uniq_id'] .'-social.jpg'))
	{
		unlink(_THUMBS_DIR_PATH . $video_data['uniq_id'] .'-social.jpg');
	}

	if (_MOD_SOCIAL)
	{
		remove_all_related_activity($video_data['id'], ACT_OBJ_VIDEO);
	}

	$subtitles = a_get_video_subtitles($video_data['uniq_id']);
	if (pm_count($subtitles) > 0)
	{
		foreach ($subtitles as $k => $sub)
		{
			if (file_exists(_SUBTITLES_DIR_PATH . $sub['filename']) && strlen($sub['filename']) > 0)
			{
				unlink(_SUBTITLES_DIR_PATH . $sub['filename']);
			}
		}

		$sql = "DELETE FROM pm_video_subtitles
				WHERE uniq_id = '". $video_data['uniq_id'] ."'";
		mysql_query($sql);
	}

	$playlist_ids = array();
	$sql = "SELECT list_id 
			FROM pm_playlist_items 
			WHERE video_id = ". $video_data['id'];
	if ($result = mysql_query($sql))
	{
		$in_playlists = false;
		while ($row = mysql_fetch_assoc($result))
		{
			$playlist_ids[] = $row['list_id'];
			$in_playlists = true;
		}
		mysql_free_result($result);
		
		if ($in_playlists)
		{
			$sql = "DELETE FROM pm_playlist_items
					WHERE video_id = ". $video_data['id'];
			mysql_query($sql);

			$sql = "UPDATE pm_playlists 
					SET items_count = items_count - 1 
					WHERE list_id IN (". implode(',', $playlist_ids) .")";
			mysql_query($sql);
		}
	}

	return true;
}

function get_admin_ui_prefs($name = '')
{
	global $config, $userdata;

	$defaults = array(
        'import_from'   => 'youtube', // 'youtube', 'youtube-channel', 'dailymotion', 'vimeo'
        // items per page (pp)
        'videos_pp'     => ( ! empty($_COOKIE['aa_videos_per_page'])) ? (int) $_COOKIE['aa_videos_per_page'] : 25,
        'pages_pp'      => ( ! empty($_COOKIE['aa_pages_per_page'])) ? (int) $_COOKIE['aa_pages_per_page'] : 25,
        'articles_pp'   => ( ! empty($_COOKIE['aa_articles_per_page'])) ? (int) $_COOKIE['aa_articles_per_page'] : 25,
        'cron_jobs_pp'  => ( ! empty($_COOKIE['aa_cron_jobs_per_page'])) ? (int) $_COOKIE['aa_cron_jobs_per_page'] : 25,
        'series_pp'     => ( ! empty($_COOKIE['aa_series_per_page'])) ? (int) $_COOKIE['aa_series_per_page'] : 50,
        'episodes_pp'   => ( ! empty($_COOKIE['aa_episodes_per_page'])) ? (int) $_COOKIE['aa_episodes_per_page'] : 25,
        'users_pp'      => ( ! empty($_COOKIE['aa_users_per_page'])) ? (int) $_COOKIE['aa_users_per_page'] : 25,
        'comments_pp'   => ( ! empty($_COOKIE['aa_comments_per_page'])) ? (int) $_COOKIE['aa_comments_per_page'] : 25
    );

	if (array_key_exists('admin_ui_prefs', $userdata))
	{
		if ($name != '')
		{
			return $userdata['admin_ui_prefs'][$name];
		}
		return $userdata['admin_ui_prefs'];
	}

	$ui_prefs = json_decode($config['admin_ui_prefs'], true);

	if ( ! $ui_prefs)
	{
		if ($ui_prefs === null)
		{
			add_config('admin_ui_prefs', json_encode(
				array($userdata['id'] => $defaults)
			));
		}
		$ui_prefs[$userdata['id']] = $defaults;
	}
	else
	{
		if (empty($ui_prefs[$userdata['id']]))
		{
			$ui_prefs[$userdata['id']] = $defaults;
			add_config('admin_ui_prefs', json_encode($ui_prefs));
		}
	}

	$userdata['admin_ui_prefs'] = $ui_prefs[$userdata['id']];

	if ($name != '')
	{
		return $ui_prefs[$userdata['id']][$name]; 
	}

	return $ui_prefs[$userdata['id']];
}

function set_admin_ui_prefs($name, $value)
{
	global $config, $userdata;

	$current_user_ui_prefs = get_admin_ui_prefs();
	$current_user_ui_prefs[$name] = $value; 
	$ui_prefs = json_decode($config['admin_ui_prefs'], true);
	$ui_prefs[$userdata['id']] = $current_user_ui_prefs;
	$userdata['admin_ui_prefs'] = $current_user_ui_prefs;

	return update_config('admin_ui_prefs', json_encode($ui_prefs));
}

if ( ! function_exists('ngettext'))
{
	function ngettext($singular, $plural, $n)
	{
		return ($n == 1) ? $singular : $plural; 
	}
}