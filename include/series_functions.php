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

function get_series($series_id)
{
    $series = get_series_list(array($series_id));

    return $series[0];
}

function get_series_by_slug($series_slug)
{
    $series_slug = filter_series_slug($series_slug);

    $sql = "SELECT series_id 
            FROM pm_series 
            WHERE series_slug = '". secure_sql($series_slug) ."'";

    if ($result = mysql_query($sql))
    {
        if (mysql_num_rows($result) > 0)
        {
            $row = mysql_fetch_assoc($result);
            mysql_free_result($result);
            
            return get_series($row['series_id']);
        }
    }

    return array();
}

function get_series_list($series_ids = array(), $order_by = 'series_id', $sort = 'DESC', $start = 0, $limit = 20, $genre_ids = array(), $episode_ids = array(), $filter = '', $filter_value = '')
{
    $ids = array();
    $series_id_count = (is_array($series_ids)) ? pm_count($series_ids) : 0;
    $genre_ids_count = (is_array($genre_ids)) ? pm_count($genre_ids) : 0;
    $episode_ids_count = (is_array($episode_ids)) ? pm_count($episode_ids) : 0;
    $series_genre_rel = array();

    if ($series_id_count > 0)
    {
        $ids = $series_ids;
    }
    else if ($episode_ids_count > 0)
    {
        $sql = "SELECT series_id 
                FROM pm_episodes 
                WHERE episode_id IN (". implode(',', $episode_ids) .")";

        $result = mysql_query($sql);
        if (mysql_num_rows($result) > 0)
        {
            while ($row = mysql_fetch_assoc($result))
            {
                if ( ! in_array($row['series_id'], $ids))
                {
                    $ids[] = $row['series_id'];
                }
            }
            mysql_free_result($result);
        }
        else 
        {
            return array();
        }
    }
    else if ($genre_ids_count > 0)
    {
        $sql = "SELECT item_id, genre_id 
                FROM pm_genre_relationships
                WHERE genre_id IN (". implode(',', $genre_ids) .") 
                  AND item_type = ". IS_SERIES;
        $result = mysql_query($sql);

        if (mysql_num_rows($result) > 0)
        {
            while ($row = mysql_fetch_assoc($result))
            {
                if ( ! in_array($row['item_id'], $ids))
                {
                    $ids[] = $row['item_id'];
                    $series_genre_rel[$row['item_id']][] = $row['genre_id'];
                }
            }
            mysql_free_result($result);
        }
        else 
        {
            return array();
        }
    }

    $sql_where = (pm_count($ids) > 0) ? " WHERE series_id IN(". implode(',', $ids) .") " : '';

    if ($filter == 'featured')
    {
        $sql_where = (empty($sql_where)) ? ' WHERE featured = 1 ' : ' AND featured = 1 ';
    }

    $sql = "SELECT * 
            FROM pm_series 
            $sql_where ";
    $sql .= ( ! empty($order_by)) ? " ORDER BY $order_by " : " ORDER BY series_id ";
    $sql .= ( ! empty($sort)) ? " $sort " : " DESC ";
    $sql .= ( ! empty($limit)) ? " LIMIT $start, $limit " : " LIMIT 0, 20 ";

    $list = array(); 
    if ($result = mysql_query($sql))
    {
        while ($row = mysql_fetch_assoc($result))
        {
            $row['url'] = series_url($row);
            $row['image_url'] = series_image_url($row);
            $row['genres'] = get_genres(array('item_id' => $row['series_id'], 'item_type' => IS_SERIES));
            $row['genre_ids'] = array();
            if (pm_count($row['genres']) > 0)
            {
                foreach ($row['genres'] as $genre_id => $g_arr)
                {
                    if ($genre_id > 0)
                        $row['genre_ids'][] = $genre_id;
                }
            }
            $row['genre_ids_str'] = implode(',', $row['genre_ids']);

            $row['seasons'] = ($row['seasons'] == 0) ? $row['seasons_count'] : $row['seasons'];
            $row['episodes'] = ($row['episodes'] == 0) ? $row['episodes_count'] : $row['episodes'];
            $row['seasons_formatted'] = pm_number_format($row['seasons']);
            $row['episodes_formatted'] = pm_number_format($row['episodes']);
            $row['views_formatted'] = pm_number_format($row['views']);
            $row['views_compact'] = pm_compact_number_format($row['views']);

            $metadata = get_meta($row['series_id'], IS_SERIES);
            $row['metadata'] = $metadata;
            $row['meta_description'] = $metadata['_meta_description'];
            $row['meta_keywords'] = $metadata['_meta_keywords'];

            $list[] = $row;
        }
        mysql_free_result($result);
    }

    return $list; 
}

function search_series($search_term, $search_type = 'title', $start = 0, $limit = 20, $order_by = null, $sort = null, &$total_results) 
{
    $total_results = 0;

    switch ($search_type)
    {
        default:
        case 'title':
            $sql = "SELECT SQL_CALC_FOUND_ROWS pm_series.*, 
                        MATCH(title) AGAINST('". secure_sql($search_term) ."') AS score 
                    FROM pm_series 
                    WHERE MATCH(title) AGAINST('". secure_sql($search_term) ."')
                    ORDER BY score DESC 
                    LIMIT $start, $limit";
        break;
    }
    
    $list = array(); 
    if ($result = mysql_query($sql))
    {
        $sql_rows = "SELECT FOUND_ROWS()";
        $result_rows = mysql_query($sql_rows);
        $row = mysql_fetch_row($result_rows);
        $total_results = (int) $row[0];

        while ($row = mysql_fetch_assoc($result))
        {
            $row['url'] = series_url($row);
            $row['image_url'] = series_image_url($row);
            $row['genres'] = get_genres(array('item_id' => $row['series_id'], 'item_type' => IS_SERIES));
            $row['genre_ids'] = array();
            if (pm_count($row['genres']) > 0)
            {
                foreach ($row['genres'] as $genre_id => $g_arr)
                {
                    $row['genre_ids'][] = $genre_id;
                }
            }
            $row['genre_ids_str'] = implode(',', $row['genre_ids']);
            
            $row['seasons'] = ($row['seasons'] == 0) ? $row['seasons_count'] : $row['seasons'];
            $row['episodes'] = ($row['episodes'] == 0) ? $row['episodes_count'] : $row['episodes'];
            $row['seasons_formatted'] = pm_number_format($row['seasons']);
            $row['episodes_formatted'] = pm_number_format($row['episodes']);
            $row['views_formatted'] = pm_number_format($row['views']);
            $row['views_compact'] = pm_compact_number_format($row['views']);

            $metadata = get_meta($row['series_id'], IS_SERIES);
            $row['metadata'] = $metadata;
            $row['meta_description'] = $metadata['_meta_description'];
            $row['meta_keywords'] = $metadata['_meta_keywords'];

            $list[] = $row;
        }
    }


    return $list;
}

function series_url($series_data)
{
    return (_SEOMOD) 
        ? _URL .'/series/'. $series_data['series_slug'] .'/'
        : _URL .'/series.php?s='. $series_data['series_slug'];
}

function series_image_url($series_data)
{
    if (empty($series_data['image']))
    {
        return make_url_https(_NOTHUMB);
    }

    if (strpos($series_data['image'], 'http') !== 0 && strpos($series_data['image'], '//') !== 0)
    {
        return make_url_https(_THUMBS_DIR . $series_data['image']);
    }

    return make_url_https($series_data['image']); 
}

function get_genres($args = array())
{
    $args['db_table'] = 'pm_genres';
    $genres = load_categories($args);
    
    if (pm_count($genres) > 0)
    {
        foreach ($genres as $id => $genre)
        {
            $genres[$id]['url'] = genre_url($genre);
        }
    }

    if (isset($args['item_id']) && isset($args['item_type']))
    {
        $genre_arr = array();

        $sql = "SELECT genre_id 
                FROM pm_genre_relationships 
                WHERE item_id = ". $args['item_id'] ."
                  AND item_type = ". $args['item_type'];
        if ($result = mysql_query($sql))
        {
            while ($row = mysql_fetch_assoc($result))
            {
                $genre_arr[$row['genre_id']] = $genres[$row['genre_id']];
            }
            mysql_free_result($result);
        }

        return $genre_arr;
    }
 
    return $genres;
}
 
function genre_url($genre)
{
    return (_SEOMOD)
        ? _URL .'/series/'. $genre['tag'] .'/'
        : _URL .'/series.php?s='. $genre['tag'];
}

function get_unique_series_slug($series_slug)
{
    $found = false;
    $slug_append = '';
    do {
        $found = false;
        $sql = "SELECT COUNT(*) as total_found
                FROM pm_series 
                WHERE series_slug = '". secure_sql($series_slug . $slug_append) ."'";
        $result = mysql_query($sql);
        $row = mysql_fetch_assoc($result);
        if ((int) $row['total_found'] > 0)
        {
            $slug_append = (empty($slug_append)) ? '-0' : $slug_append;
            $slug_append = preg_replace_callback(
                "|(\d+)|", 
                function($match) {
                    return ++$match[1];
                },
                $slug_append);
            $found = true;
        }
        // note: looking up pm_videos_trash is not necessary since an episode never ends up here
    } while ($found);

    return $series_slug . $slug_append;
}

function insert_new_series($postarr)
{
    global $config; 

    foreach ($postarr as $k => $v)
    {
        if (is_string($v))
        {
            $postarr[$k] = stripslashes($v);
        }
    }

    $postarr['title'] = trim($postarr['title']);
    $postarr['series_slug'] = ($postarr['series_slug'] != '') ? 
        filter_series_slug($postarr['series_slug']) : 
        filter_series_slug($postarr['title']);
    $postarr['series_slug'] = get_unique_series_slug($postarr['series_slug']);

    if (strlen($postarr['title']) == 0)
    {
        return array(
            'type' => 'error', 
            'msg' => 'Please provide a title first.'
        );
    }

    $sql = "INSERT INTO pm_series 
                (title, description, series_slug, seasons, episodes, seasons_count, episodes_count, views, release_year, date, image, featured) 
            VALUES ('". secure_sql($postarr['title']) ."', 
                    '". secure_sql($postarr['description']) ."', 
                    '". secure_sql($postarr['series_slug']) ."', 
                    '". (int) $postarr['seasons_input'] ."', 
                    '". (int) $postarr['episodes_input'] ."', 
                    '0',
                    '0',
                    '". (int) $postarr['views_input'] ."', 
                    '". (int) $postarr['release_year_input'] ."', 
                    '". time() ."', 
                    '". secure_sql($postarr['image']) ."',
                    '". (int) $postarr['featured'] ."')";
    if ( ! $result = @mysql_query($sql))
    {
        return array(
            'type' => 'error', 
            'msg' => 'MySQL Error: '. mysql_error()
        );
    }
    $last_id = mysql_insert_id();

    // handle series-genre relationships
    if (empty($postarr['genres']) || (is_array($postarr['genres']) && pm_count($postarr['genres']) == 0))
    {
        $postarr['genres'][] = 0;
    }

    if (is_array($postarr['genres']) && pm_count($postarr['genres']) > 0)
    {
        foreach ($postarr['genres'] as $k => $genre_id)
        {
            $sql = "INSERT INTO pm_genre_relationships 
                        (item_id, item_type, genre_id) 
                    VALUES (". $last_id .", 
                            ". IS_SERIES .",
                            ". (int) $genre_id .")";
            mysql_query($sql);

            $sql = "UPDATE pm_genres 
                    SET total_series = total_series + 1 
                    WHERE id = ". $genre_id;
            mysql_query($sql);
        }
    }

    $postarr['meta_keywords'] = htmlspecialchars($postarr['meta_keywords'],  ENT_QUOTES);
    $postarr['meta_description'] = htmlspecialchars($postarr['meta_description'],  ENT_QUOTES);
    $postarr['meta_description'] = str_replace(array("\n", "\r"), '', $postarr['meta_description']);
    
    update_meta($last_id, IS_SERIES, '_meta_keywords', $postarr['meta_keywords']);
    update_meta($last_id, IS_SERIES, '_meta_description', $postarr['meta_description']);

    update_config('total_series', $config['total_series'] + 1, true);

    return array(
        'type' => 'ok', 
        'msg' => 'Series created.', 
        'series_id' => $last_id
    );
}

function filter_series_slug($series_slug)
{
    return sanitize_title(trim($series_slug));
}

function update_series($series_id, $postarr)
{
    $series_data = get_series($series_id);

    foreach ($postarr as $k => $v)
    {
        if (is_string($v))
        {
            $postarr[$k] = stripslashes($v);
        }
    }

    $postarr['title'] = trim($postarr['title']);
    $postarr['series_slug'] = ($postarr['series_slug'] != '') ? 
        filter_series_slug($postarr['series_slug']) : 
        filter_series_slug($postarr['title']);
    if ($postarr['series_slug'] != $series_data['series_slug'])
    {
        $postarr['series_slug'] = get_unique_series_slug($postarr['series_slug']);
    }

    $postarr['image'] = str_replace(_THUMBS_DIR, '', $postarr['image']);

    $sql = "UPDATE pm_series 
            SET title = '". secure_sql($postarr['title']) ."', 
                description = '". secure_sql($postarr['description']) ."', 
                series_slug = '". secure_sql($postarr['series_slug']) ."', 
                seasons = '". (int) $postarr['seasons_input'] ."', 
                episodes = '". (int) $postarr['episodes_input'] ."', 
                views = '". (int) $postarr['views_input'] ."', 
                release_year = '". (int) $postarr['release_year_input'] ."', 
                image = '". secure_sql($postarr['image']) ."',
                featured = '". (int) $postarr['featured'] ."' 
            WHERE series_id = ". $series_id;
    $result = mysql_query($sql);
    if ( ! $result)
    {
        return array(
            'type' => 'error', 
            'msg' => 'MySQL Error: '. mysql_error()
        );
    }

    if (empty($postarr['genres']) || (is_array($postarr['genres']) && pm_count($postarr['genres']) == 0))
    {
        $postarr['genres'][] = 0;
    }

    if (is_array($postarr['genres']) && pm_count($postarr['genres']) > 0)
    {
        // add new genres
        foreach ($postarr['genres'] as $k => $genre_id)
        {
            if ( ! array_key_exists($genre_id, $series_data['genres']))
            {
                $sql = "INSERT INTO pm_genre_relationships 
                            (item_id, item_type, genre_id) 
                        VALUES (". $series_id .", 
                                ". IS_SERIES .",
                                ". (int) $genre_id .")";
                mysql_query($sql);
                $series_data['genres'][$genre_id] = array();

                $sql = "UPDATE pm_genres 
                        SET total_series = total_series + 1 
                        WHERE id = ". $genre_id;
                mysql_query($sql);
            }
        }
        // remove genres
        foreach($series_data['genres'] as $genre_id => $genre_arr)
        {
            if ( ! in_array($genre_id, $postarr['genres']))
            {
                $sql = "DELETE FROM  pm_genre_relationships 
                        WHERE item_id = ". $series_id ." 
                          AND item_type = ". IS_SERIES ." 
                          AND genre_id = ". $genre_id;

                mysql_query($sql);
                unset($series_data['genres'][$genre_id]); 

                $sql = "UPDATE pm_genres 
                        SET total_series = total_series - 1 
                        WHERE id = ". $genre_id;
                mysql_query($sql);
            }
        }
    }

    $postarr['meta_keywords'] = htmlspecialchars($postarr['meta_keywords'],  ENT_QUOTES);
    $postarr['meta_description'] = htmlspecialchars($postarr['meta_description'],  ENT_QUOTES);
    $postarr['meta_description'] = str_replace(array("\n", "\r"), '', $postarr['meta_description']);
    
    update_meta($series_id, IS_SERIES, '_meta_keywords', $postarr['meta_keywords']);
    update_meta($series_id, IS_SERIES, '_meta_description', $postarr['meta_description']);

    return array(
        'type' => 'ok', 
        'msg' => 'Series updated.'
    );
}

function delete_series($series_id)
{
    global $config; 

    if ( ! $series_id)
    {
        return array(
            'type' => 'error', 
            'msg' => 'Missing series ID.'
        );
    }
    $series_data = get_series($series_id);

    $sql = "DELETE FROM pm_series
            WHERE series_id = ". $series_id; 
    if ( ! $result = mysql_query($sql))
    {
        return array(
            'type' => 'error', 
            'msg' => 'Could not delete this series. An error occurred: '. mysql_error()
        );
    }

    $sql = "DELETE FROM pm_genre_relationships 
            WHERE item_id = ". $series_id ." 
              AND item_type = ". IS_SERIES;
    mysql_query($sql);

    if (file_exists(_THUMBS_DIR_PATH . $series_data['image']))
    {
        unlink(_THUMBS_DIR_PATH . $series_data['image']);
    }

    delete_meta($series_id, IS_SERIES);
    update_config('total_series', $config['total_series']-1, true);
    
    if ( ! empty($series_data['genre_ids_str']))
    {
        $sql = "UPDATE pm_genres 
                SET total_series = total_series - 1 
                WHERE id IN (". $series_data['genre_ids_str'] .")";
        mysql_query($sql);
    }

    return array(
        'type' => 'ok', 
        'msg' => 'Series deleted.'
    );
}

function mass_delete_series($series_ids)
{
    $series_ids_str = implode(', ', $series_ids);
    
    $series = get_series_list($series_ids);
    $sql = "DELETE FROM pm_series 
            WHERE series_id IN (" . secure_sql($series_ids_str) . ")";
    $result = mysql_query($sql);
    if ( ! $result)
    {
        return array(
            'type' => 'error', 
            'msg' => 'Could not delete these pages. An error occurred: '. mysql_error()
        );
    }

    $sql = "DELETE FROM pm_genre_relationships 
            WHERE item_id IN (" . secure_sql($series_ids_str) . ")
              AND item_type = ". IS_SERIES;
    mysql_query($sql);

    foreach ($series_ids as $k => $id)
    {
        delete_meta($id, IS_SERIES);
    }
    
    update_config('total_pages', count_entries('pm_series', '', ''), true);

    foreach ($series as $k => $series_data)
    {
        if ( ! empty($series_data['genre_ids_str']))
        {
            $sql = "UPDATE pm_genres 
                    SET total_series = total_series - 1 
                    WHERE id IN (". $series_data['genre_ids_str'] .")";
            mysql_query($sql);

            if (file_exists(_THUMBS_DIR_PATH . $series_data['image']))
            {
                unlink(_THUMBS_DIR_PATH . $series_data['image']);
            }
        }
    }
    
    return array(
        'type' => 'ok', 
        'msg' => 'The selected series have been deleted.'
    );
}

function insert_genre($postarr)
{
    return insert_category($postarr, 'genre');
}

function update_genre($genre_id, $postarr)
{
    return update_category($genre_id, $postarr, 'genre');
}

function delete_genre($genre_id)
{
    return delete_category($genre_id, 'genre');
}

function get_episode($episode_id)
{
    $episode_arr = get_episode_list(array($episode_id), null, null, null, null, null, true);

    return $episode_arr[0];
}

function get_episode_by_uniq_id($uniq_id)
{
    $sql = "SELECT episode_id 
            FROM pm_episodes 
            WHERE uniq_id = '". secure_sql($uniq_id) ."'";
    $result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);

    return get_episode($row['episode_id']);
}

function get_episode_by_url_slug($slug)
{
    $sql = "SELECT pm_videos.uniq_id, pm_episodes.episode_id
            FROM pm_videos 
            JOIN pm_episodes ON (pm_videos.uniq_id = pm_episodes.uniq_id)
            WHERE pm_videos.video_slug = '". secure_sql(sanitize_title($slug)) ."'
              AND video_type = ". IS_EPISODE;
    $result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);

    return get_episode($row['episode_id']);
}

/**
 * Helper function for retrieving the full list of episodes grouped by season for a particular series, 
 * ordered by episode number in ascending order.
 * @param  integer $series_id 
 * @param  integer $season_no limit the data set to a particular season
 * @return array              array of episodes grouped by season number (key = season_no)
 */
function get_series_seasons($series_id, $season_no = null)
{
    $episodes = get_episode_list(array(), $series_id, 'episode_no', 'asc', null, null, true);
    $seasons = array();
    if (pm_count($episodes) > 0)
    {
        foreach ($episodes as $k => $episode_data)
        {
            if (isset($season_no))
            {
                if ($season_no == $episode_data['season_no'])
                {
                    $seasons[$episode_data['season_no']][] = $episode_data;
                }
            }
            else
            {
                $seasons[$episode_data['season_no']][] = $episode_data;
            }
        }
    }

    return $seasons;
}

function get_featured_episodes_list($order_by = null, $sort = null, $start = 0, $limit = 20, &$total_results)
{
    global $config;

    $sql = "SELECT SQL_CALC_FOUND_ROWS pm_episodes.episode_id
            FROM pm_episodes 
            LEFT JOIN pm_videos ON (pm_episodes.uniq_id = pm_videos.uniq_id) ";
    $sql_where = " pm_videos.featured = '1' ";

    if (in_array($order_by, array('episode_id', 'release_date', 'season_no', 'episode_no', 'series_id')))
    {
        $order_by = ($order_by == 'episode_no')
            ? 'pm_episodes.season_no '. $sort .', '. $order_by
            : 'pm_episodes.'. $order_by;
    }
    else if ( ! empty($order_by) && strtolower($order_by) != 'rand()')
    {
        $order_by = 'pm_videos.'. $order_by;
    }
    $sql .= (strlen($sql_where) > 0) ? 'WHERE '. $sql_where : '';
    $sql .= ( ! empty($order_by)) ? " ORDER BY $order_by " : " ORDER BY episode_id ";
    $sql .= ( ! empty($sort)) ? " $sort " : " DESC ";
    $sql .= ( ! empty($limit)) ? " LIMIT $start, $limit " : " ";

    $episode_ids = array();

    if ($result = mysql_query($sql))
    {
        while ($row = mysql_fetch_assoc($result))
        {
            $episode_ids[] = $row['episode_id'];
        }
    }

    $sql = "SELECT FOUND_ROWS()";
    $result = mysql_query($sql);
    $row = mysql_fetch_row($result);
    $total_results = (int) $row[0];

    return (pm_count($episode_ids) > 0) ? get_episode_list($episode_ids) : array();
}

function get_restricted_episodes_list($order_by = null, $sort = null, $start = 0, $limit = 20, &$total_results)
{
    global $config;

    $sql = "SELECT SQL_CALC_FOUND_ROWS pm_episodes.episode_id
            FROM pm_episodes 
            LEFT JOIN pm_videos ON (pm_episodes.uniq_id = pm_videos.uniq_id) ";
    $sql_where = " pm_videos.restricted = '1' ";

    if (in_array($order_by, array('episode_id', 'release_date', 'season_no', 'episode_no', 'series_id')))
    {
        $order_by = ($order_by == 'episode_no')
            ? 'pm_episodes.season_no '. $sort .', '. $order_by
            : 'pm_episodes.'. $order_by;
    }
    else if ( ! empty($order_by) && strtolower($order_by) != 'rand()')
    {
        $order_by = 'pm_videos.'. $order_by;
    }
    $sql .= (strlen($sql_where) > 0) ? 'WHERE '. $sql_where : '';
    $sql .= ( ! empty($order_by)) ? " ORDER BY $order_by " : " ORDER BY episode_id ";
    $sql .= ( ! empty($sort)) ? " $sort " : " DESC ";
    $sql .= ( ! empty($limit)) ? " LIMIT $start, $limit " : " ";

    $episode_ids = array();

    if ($result = mysql_query($sql))
    {
        while ($row = mysql_fetch_assoc($result))
        {
            $episode_ids[] = $row['episode_id'];
        }
    }

    $sql = "SELECT FOUND_ROWS()";
    $result = mysql_query($sql);
    $row = mysql_fetch_row($result);
    $total_results = (int) $row[0];

    return (pm_count($episode_ids) > 0) ? get_episode_list($episode_ids) : array();
}

function get_top_episodes_list($sortby = 'views', $limit = 7)
{
    global $config;

    switch ($sortby)
    {
        case 'views':
            return get_episode_list(array(), 0, 'site_views', 'DESC', 0, $limit, true);
        break;

        case 'rating':
            $sql = "SELECT pm_episodes.episode_id 
                    FROM pm_bin_rating_meta 
                    JOIN pm_episodes ON (pm_bin_rating_meta.uniq_id = pm_episodes.uniq_id)
                    WHERE pm_bin_rating_meta.score > 0 
                    ORDER BY score DESC
                    LIMIT ".$limit;
        break;

        default:
        case 'chart':
            $sql = "SELECT pm_episodes.episode_id 
                    FROM pm_chart 
                    JOIN pm_episodes ON (pm_chart.uniq_id = pm_episodes.uniq_id)
                    ORDER BY pm_chart.views DESC 
                    LIMIT ". $limit;
        break;
    }

    $episode_data = array();
    if ($result = mysql_query($sql))
    {
        $episode_ids = array();
        while ($row = mysql_fetch_assoc($result))
        {
            $episode_ids[] = $row['episode_id'];
        }
        mysql_free_result($result);

        $episode_data = get_episode_list($episode_ids, 0, null, null, null, null, true);
        
        // rearrange to respect order by score
        $tmp = array();
        foreach ($episode_ids as $k => $id)
        {
            foreach ($episode_data as $kk => $episode)
            {
                if ($id == $episode['episode_id'])
                {
                    $tmp[] = $episode_data[$kk];
                    unset($episode_data[$kk]);
                }
            }
        }
        $episode_data = $tmp;
    }

    return $episode_data;
}

function get_episode_list($episode_ids = array(), $series_id = 0, $order_by = null, $sort = null, $start = 0, $limit = 20, $extended_data = false)
{
    global $config;

    $episode_ids_count = (is_array($episode_ids)) ? pm_count($episode_ids) : 0;

    $sql = "SELECT pm_episodes.*, pm_videos.* 
            FROM pm_episodes 
            LEFT JOIN pm_videos ON (pm_episodes.uniq_id = pm_videos.uniq_id) ";
    $sql_where = ($series_id) ? ' pm_episodes.series_id = '. $series_id : ''; 

    if ($episode_ids_count > 0)
    {
        $sql_where = ' episode_id IN ('. implode(',', $episode_ids) .')';
    }

    if (in_array($order_by, array('episode_id', 'release_date', 'season_no', 'episode_no', 'series_id')))
    {
        $order_by = ($order_by == 'episode_no')
            ? 'pm_episodes.season_no '. $sort .', '. $order_by
            : 'pm_episodes.'. $order_by;
    }
    else if ( ! empty($order_by))
    {
        $order_by = 'pm_videos.'. $order_by;
    }

    $sql .= (strlen($sql_where) > 0) ? 'WHERE '. $sql_where : '';
    $sql .= ( ! empty($order_by)) ? " ORDER BY $order_by " : " ORDER BY episode_id ";
    $sql .= ( ! empty($sort)) ? " $sort " : " DESC ";
    $sql .= ( ! empty($limit)) ? " LIMIT $start, $limit " : " ";

    $list = array(); 
    $series_ids = array();

    if ($result = mysql_query($sql))
    {
        while ($row = mysql_fetch_assoc($result))
        {
            $series_ids[] = $row['series_id'];
            $row['video_id'] = $row['id'];

            if ($row['media_sources'] = json_decode($row['media_sources'], true))
            {
                foreach ($row['media_sources'] as $k => $ms)
                {
                    $row['media_sources'][$k]['player_src'] = _URL .'/episode-player.php?id='. $row['episode_id'] .'&ms_id='. $ms['id'];
                    switch ($ms['type'])
                    {
                        case 'file':
                        case 'url':

                            $row['media_sources'][$k]['player_html'] = sprintf(
                                '<iframe src="%s" width="%s" height="%s" frameborder="0" allowfullscreen seamless></iframe>',
                                 _URL .'/episode-player.php?id='. $row['episode_id'] .'&ms_id='. $ms['id'], 
                                 _PLAYER_W, 
                                 _PLAYER_H
                             );

                        break;
                        case 'url':
                        break;
                        case 'embed code':

                            $row['media_sources'][$k]['player_html'] = str_replace(
                                array('%%player_w%%', '%%player_h%%'),
                                array(_PLAYER_W, _PLAYER_H),
                                $ms['data']
                            );
                            
                        break;
                    }

                }
            }

            $row['duration'] = sec2hms($row['yt_length']);
            $row['url'] = episode_url($row);
            $row['url_urldecoded'] = urldecode($row['url']);
            $row['image_url'] = episode_image_url($row);

            $row['video_title'] = stripslashes($row['video_title']);
            $row['added_timestamp'] = (int) $row['added'];
            $row['added'] = time_since($row['added_timestamp']);
            $row['submitted'] = ($row['submitted'] == 'bot') ? 'admin' : $row['submitted'];
            $row['site_views_formatted'] = pm_number_format($row['site_views']);

            if ($row['yt_thumb'] != '')
            {
                $row['preview_image'] = episode_image_url($row);
            }

            if ($extended_data)
            {
                $row['description'] = filter_text_https_friendly($row['description']);
                $row['video_player'] = $config['video_player'];
                $row['video_href'] = $row['url'];

                $rating_meta = bin_rating_get_item_meta($row['uniq_id']);
                $balance = bin_rating_calc_balance($rating_meta['up_vote_count'], $rating_meta['down_vote_count']);
                
                $row['up_vote_count'] = (int) $rating_meta['up_vote_count'];
                $row['likes'] = $row['up_vote_count'];
                $row['down_vote_count'] = (int) $rating_meta['down_vote_count'];
                $row['dislikes'] = $row['down_vote_count'];
                
                $row['up_vote_count_formatted'] = pm_number_format($row['up_vote_count']);
                $row['down_vote_count_formatted'] = pm_number_format($row['down_vote_count']);
                $row['up_vote_count_compact'] = pm_compact_number_format($row['up_vote_count']);
                $row['down_vote_count_compact'] = pm_compact_number_format($row['down_vote_count']);
                
                $row['likes_formatted'] = $row['up_vote_count_formatted'];
                $row['dislikes_formatted'] = $row['down_vote_count_formatted'];
                $row['likes_compact'] = $row['up_vote_count_compact'];
                $row['dislikes_compact'] = $row['down_vote_count_compact'];

                $row['attr_alt'] = htmlspecialchars(stripslashes($row['video_title']));
                $row['excerpt'] = generate_excerpt($row['description'], 255);

                $author_data = fetch_user_info($row['submitted']);

                $row['duration'] = sec2hms($row['yt_length']);
                $row['thumb_img_url'] = (isset($row['preview_image'])) ? $row['preview_image'] : $row['image_url'];
                $row['author_data'] = $author_data;
                $row['author_username'] = $row['submitted'];
                $row['author_user_id'] = $author_data['id'];
                $row['author_power'] = $author_data['power'];
                $row['author_name'] = $author_data['name'];
                $row['author_avatar_url'] = $author_data['avatar_url'];
                $row['author_profile_href'] = ($row['submitted'] != 'bot') ? $author_data['profile_url'] : '#';
                $row['html5_datetime'] = date('Y-m-d\TH:i:sO', $row['added_timestamp']); // ISO 8601
                $row['full_datetime'] = date('l, F j, Y g:i A', $row['added_timestamp']); 
                $row['time_since_added'] = $row['added'];
                $row['views_compact'] = pm_compact_number_format($row['site_views']);
                $row['iso8601_duration'] = iso8601_duration($row['yt_length']); // ISO 8601
                $row['embed_href'] = generate_embed_code($row['uniq_id'], $row, false, 'link');
                
                $row = array_merge($row, $balance);

                if (_THUMB_FROM == 2 && $row['thumb_img_url'] != '') // 2 = local
                {
                    $ext = pm_get_file_extension($row['thumb_img_url']);

                    if(_SEOMOD == 1)
                    {
                        $row['social_share_img_url'] = _THUMBS_DIR . $row['uniq_id'] .'-social.'. $ext;
                    } 
                    else 
                    {
                        $row['social_share_img_url'] = _URL .'/social-thumb.php?vid='. $row['uniq_id'];
                    }
                }
            }

            $list[] = $row;
        }
        mysql_free_result($result);
    }

    // get series data
    if (pm_count($series_ids) > 0)
    {
        $sql = "SELECT series_id, title, series_slug, release_year, image 
                FROM pm_series 
                WHERE series_id IN (". implode(',', $series_ids ) .")";
        if ($result = mysql_query($sql))
        {
            while ($row = mysql_fetch_assoc($result))
            {
                foreach ($list as $k => $episode)
                {
                    if ($episode['series_id'] == $row['series_id'])
                    {
                        $row['url'] = series_url($row);
                        $row['image_url'] = series_image_url($row);
                        $list[$k]['series_data'] = $row;
                    }
                }
            }
            mysql_free_result($result);
        }
    }

    // get meta data and tags
    foreach ($list as $k => $episode)
    {
        $list[$k]['metadata'] = get_all_meta_data($episode['episode_id'], IS_EPISODE);
        $list[$k]['tags'] = get_video_tags($episode['uniq_id']);
        foreach($list[$k]['tags'] as $tag_k => $tag_arr)
        {
            $list[$k]['tags_compact'] .= $tag_arr['tag'] . ",";
        }
        $list[$k]['tags_compact'] = trim($list[$k]['tags_compact'], ",");
    }

    return $list;
}

function search_episode($search_term, $search_type = 'video_title', $start = 0, $limit = 20, $order_by = null, $sort = null, &$total_results)
{
    $total_results = 0; 
    $episode_ids = array();

    switch ($search_type)
    {
        default:
        case 'title':

            if ($search_term[0] == '"' && $search_term[strlen($search_term)-1] == '"') // 'exact' match
            {
                $sql = "SELECT SQL_CALC_FOUND_ROWS episode_id 
                        FROM pm_episodes 
                        LEFT JOIN pm_videos ON (pm_episodes.uniq_id = pm_videos.uniq_id)
                        WHERE pm_videos.video_title LIKE '%". secure_sql(trim($search_term, '" ')) ."%' 
                          AND pm_videos.video_type = ". IS_EPISODE ." 
                        ORDER BY episode_id DESC 
                        LIMIT $start, $limit";
            }
            else
            {
                $sql = "SELECT SQL_CALC_FOUND_ROWS episode_id,
                            MATCH(pm_videos.video_title) AGAINST('". secure_sql($search_term) ."') AS score
                        FROM pm_episodes 
                        LEFT JOIN pm_videos ON (pm_episodes.uniq_id = pm_videos.uniq_id)
                        WHERE MATCH(pm_videos.video_title) AGAINST('". secure_sql($search_term) ."') 
                          AND pm_videos.video_type = ". IS_EPISODE ." 
                        ORDER BY score DESC 
                        LIMIT $start, $limit";
            }

        break;
    }
    // $sql .= ( ! empty($order_by)) ? " ORDER BY $order_by " : " ORDER BY episode_id ";
    // $sql .= ( ! empty($sort)) ? " $sort " : " DESC ";
    // $sql .= ( ! empty($limit)) ? " LIMIT $start, $limit " : " LIMIT 0, 20 ";

    $result = mysql_query($sql);
    if ($result = mysql_query($sql))
    {
        while ($row = mysql_fetch_assoc($result))
        {
            $episode_ids[] = $row['episode_id'];
        }
    }

    $sql = "SELECT FOUND_ROWS()";
    $result = mysql_query($sql);
    $row = mysql_fetch_row($result);
    $total_results = (int) $row[0];

    if (pm_count($episode_ids) == 0)
    {
        // search series too
        $series = search_series($search_term, $search_type, 0, 1, null, null, $total_results); 
        if ( ! empty($series[0]['series_id']))
        {
            $episodes = get_episode_list(array(), $series[0]['series_id'], $order_by, $sort, $start, $limit);
            $total_results = pm_count($episodes);

            return $episodes;
        }
    }
    else 
    {
        return get_episode_list($episode_ids, 0, $order_by, $sort);
    }

    return array();
}

function episode_url($episode_data)
{
    if (_SEOMOD && (empty($episode_data['video_slug']) || empty($episode_data['series_data'])))
    {
        $sql = "SELECT series_slug 
                FROM pm_episodes 
                JOIN pm_series ON (pm_episodes.series_id = pm_series.series_id)
                WHERE pm_episodes.episode_id = ". $episode_data['episode_id'];
        if ($result = mysql_query($sql))
        {
            $row = mysql_fetch_assoc($result);
            $episode_data['series_data']['series_slug'] = $row['series_slug'];
        }
    }

    return (_SEOMOD)
        ? _URL .'/series/'. $episode_data['series_data']['series_slug'] .'/'. $episode_data['video_slug']
        : _URL .'/episode.php?id='. $episode_data['episode_id'];
}

function episode_image_url($episode_data)
{
    return show_thumb($episode_data['uniq_id'], 1, $episode_data);
}

function insert_new_episode($postarr) 
{
    global $config;

    $defaults = array(
        'source_id' => 0,
        'season_no' => 0,
        'episode_no' => 0,
        'release_date' => 0,
        'media_sources' => array(),
        'video_type' => IS_EPISODE
    );

    $postarr = array_merge($defaults, $postarr);
    $is_new_video = (empty($postarr['uniq_id'])) ? true : false;
    $postarr['uniq_id'] = ($is_new_video) ? generate_video_uniq_id() : $postarr['uniq_id'];
    $postarr['yt_length'] = ($postarr['yt_min'] * 60) + $postarr['yt_sec'];
    $added = validate_item_date($postarr);
    $postarr['release_date'] = pm_mktime($added);
    $postarr['site_views'] = $postarr['site_views_input'];
    $postarr['allow_comments'] = (int) $postarr['allow_comments'];
    
    if ($postarr['description'] != '')
    {
        if ((strlen($postarr['description']) == 4) && ($postarr['description'] == "<br>"))
        {
            $postarr['description'] = '';
        }
    }

    if ($postarr['yt_thumb'] == 'https://' || $postarr['yt_thumb'] == 'http://')
    {
        $postarr['yt_thumb'] = '';
    }

    if ($postarr['yt_thumb_local'] != '')
    {
        $tmp_parts = explode('/', $postarr['yt_thumb_local']);
        $thumb_filename = array_pop($tmp_parts);
        $tmp_parts = explode('.', $thumb_filename);
        $thumb_ext = array_pop($tmp_parts);
        $thumb_ext = strtolower($thumb_ext);
        $renamed = false;

        if (file_exists(_THUMBS_DIR_PATH . $thumb_filename))
        {
            if (rename(_THUMBS_DIR_PATH . $thumb_filename, _THUMBS_DIR_PATH . $postarr['uniq_id'] . '-1.'. $thumb_ext))
            {
                $postarr['yt_thumb'] = $postarr['uniq_id'] . '-1.'. $thumb_ext;
                $renamed = true;
            }
        }

        if ( ! $renamed)
        {
            $postarr['yt_thumb'] = $postarr['yt_thumb_local'];
        }

        generate_social_thumb(_THUMBS_DIR_PATH . $postarr['yt_thumb']);
    }

    if ($is_new_video)
    {
        if (insert_new_video($postarr, $video_id) !== true)
        {
            return array(
                'type' => 'error', 
                'msg' => 'MySQL Error: '. $new_video[0]
            );
        }
    }
    
    if ( ! empty($postarr['tags']))
    {
        $tags = explode(",", $_POST['tags']);
        foreach($tags as $k => $tag)
        {
            $tags[$k] = stripslashes(trim($tag));
        }
        //  remove duplicates and 'empty' tags
        $temp = array();
        for($i = 0; $i < pm_count($tags); $i++)
        {
            if($tags[$i] != '')
                if($i <= (pm_count($tags)-1))
                {
                    $found = 0;
                    for($j = $i + 1; $j < pm_count($tags); $j++)
                    {
                        if(strcmp($tags[$i], $tags[$j]) == 0)
                            $found++;
                    }
                    if($found == 0)
                        $temp[] = $tags[$i];
                }
        }
        $tags = $temp;
        //  insert tags
        if(pm_count($tags) > 0)
            insert_tags($postarr['uniq_id'], $tags);
    }

    $media_sources = array();
    if (pm_count($postarr['media_source']) > 0)
    {
        foreach ($postarr['media_source'] as $k => $data)
        {
            if (empty($data))
                continue;

            if ($postarr['media_source_type'][$k] == 'embed code')
            {
                $data = parse_video_embed_code($data);
            }
            $media_sources[] = array(
                'id' => ( ! empty($postarr['media_source_id'][$k])) 
                    ? $postarr['media_source_id'][$k] 
                    : md5($postarr['uniq_id'] . uniqid(rand(), true)),
                'type' => $postarr['media_source_type'][$k],
                'yt_id' => ( ! empty($postarr['media_source_yt_id'][$k])) ? $postarr['media_source_yt_id'][$k] : '',
                'data' => expand_common_short_urls($data)
            );
        }
    }

    $sql = "INSERT INTO pm_episodes 
                (uniq_id, series_id, season_no, episode_no, release_date, media_sources)
            VALUES ('". $postarr['uniq_id'] ."',
                    '". secure_sql($postarr['series_id']) ."',
                    '". secure_sql($postarr['season_no']) ."',
                    '". secure_sql($postarr['episode_no']) ."',
                    '". secure_sql($postarr['release_date']) ."',
                    '". secure_sql(json_encode($media_sources)) ."')";
    if ( ! $result = mysql_query($sql))
    {
        return array(
            'type' => 'error', 
            'msg' => 'MySQL Error: '. mysql_error()
        );
    }

    $episode_id = mysql_insert_id();

    // update custom meta info with episode_id 
    if (is_array($postarr['meta']))
    {
        $meta_ids = array();
        foreach ($postarr['meta'] as $meta_id => $arr)
        {
            $meta_ids[] = $meta_id;
        }
        
        if (pm_count($meta_ids) > 0)
        {
            $sql = "UPDATE pm_meta 
                    SET item_id = $episode_id,  
                        item_type = ". IS_EPISODE ."
                    WHERE id IN (". implode(',', $meta_ids) .")";
            mysql_query($sql);
        }
    }

    // update Series internal season and episode counters
    $series_data = get_series($postarr['series_id']);
    if ($series_data['seasons_count'] < $postarr['season_no'])
    {
        $sql_mid = "seasons_count = '". secure_sql($postarr['season_no']) ."', ";
    }

    $sql = "UPDATE pm_series 
            SET ". $sql_mid ." 
                episodes_count = episodes_count + 1
            WHERE series_id = '". secure_sql($postarr['series_id']) ."'";
    mysql_query($sql);

    return array(
        'type' => 'ok',
        'msg' => 'Episode added.',
        'episode_id' => $episode_id
    );
}

function update_episode($episode_id, $postarr)
{
    $episode_data = get_episode($episode_id);
    $sources = a_fetch_video_sources();

    $postarr['video_title'] =   html_entity_decode($postarr['video_title']);
    $postarr['video_title'] =   str_replace( array("<", ">"), array("&lt;", "&gt;"), $postarr['video_title']);
    $postarr['yt_length'] = ($postarr['yt_min'] * 60) + $postarr['yt_sec'];

    if ($postarr['description'] != '')
    {
        if ((strlen($postarr['description']) == 4) && ($postarr['description'] == "<br>"))
        {
            $postarr['description'] = '';
        }
    }

    if ($postarr['video_slug'] == '')
    {
        $postarr['video_slug'] = $postarr['video_title'];
    }
    $postarr['video_slug'] = sanitize_title($postarr['video_slug']);
    if ($postarr['video_slug'] != $episode_data['video_slug'])
    {
        $postarr['video_slug'] = get_unique_video_slug($postarr['video_slug']);
    }

    $my_tags = $episode_data['tags'];
    $my_tags_str = $episode_data['tags_compact'];

    if($postarr['tags'] != '')
    {
        $tags = explode(",", $postarr['tags']);

        //  remove duplicate tags and 'empty' tags
        $temp = array();
        for($i = 0; $i < pm_count($tags); $i++)
        {
            if($tags[$i] != '')
                if($i <= (pm_count($tags)-1))
                {
                    $found = 0;
                    for($j = $i + 1; $j < pm_count($tags); $j++)
                    {
                        if(strcmp($tags[$i], $tags[$j]) == 0)
                            $found++;
                    }
                    if($found == 0)
                        $temp[] = $tags[$i];
                }
        }
        $tags = $temp;

        $tags_insert = array();
        foreach($tags as $k => $tag)
        {
            //  handle mistakes
            $tag = stripslashes(trim($tag));
            $tags[$k] = $tag;
            if($tag != '' && (strlen($tag) > 0))
            {
                //  new tags vs old tags
                $found = 0;
                $safe_tag = safe_tag($tag);
                
                foreach($my_tags as $key => $arr)
                {
                    if(in_array($safe_tag, $arr))
                        $found++;
                }
                if($found == 0)
                    $tags_insert[] = $tag;
            }
        }
        //  were there any tags changed or removed?
        $remove_tags = array();
        foreach($my_tags as $k => $v)
        {
            if(in_array($v['tag'], $tags) === false)
            {
                $remove_tags[] = $v['tag_id'];
            } 
        }
        //  insert new tags in database
        if(pm_count($tags_insert) > 0)
        {
            insert_tags($postarr['uniq_id'], $tags_insert);
        }
        
        //  remove left-out tags
        if(pm_count($remove_tags) > 0)
        {
            $this_arr = '';
            $this_arr = implode(",", $remove_tags);
            $sql2 = "DELETE FROM pm_tags WHERE tag_id IN(".$this_arr.")";
            $result2 = mysql_query($sql2);
        }
    }
    elseif(($postarr['tags'] == '') && (strlen($my_tags_str) > 0))
    {
        //  remove all tags for this video
        $sql = "DELETE FROM pm_tags WHERE uniq_id = '".$postarr['uniq_id']."'";
        @mysql_query($sql);
    }

    $sql = "UPDATE pm_videos 
            SET video_title = '". secure_sql($postarr['video_title']) ."', 
                submitted_user_id = '". (int) username_to_id($postarr['submitted']) ."',
                submitted = '". secure_sql($postarr['submitted']) ."',  
                description = '". secure_sql($postarr['description']) ."',
                language = '". $postarr['language'] ."',
                video_slug = '". secure_sql($postarr['video_slug']) ."'";

    if ($postarr['site_views'] != $postarr['site_views_input'])
    {
        $postarr['site_views_input'] = abs((int) $postarr['site_views_input']); // positive values only     
        $sql .= ", site_views = '". $postarr['site_views_input'] ."'";
    }
    if ($postarr['yt_length'])
    {
        $sql .= ", yt_length = '". $postarr['yt_length'] ."'";
    }
    $sql .= ", url_flv = ''";
    $sql .= ", source_id = '0'";
    
    if ($postarr['yt_thumb'] == 'https://' || $postarr['yt_thumb'] == 'http://')
    {
        $postarr['yt_thumb'] = '';
    }

    if ($postarr['yt_thumb'] != $postarr['yt_thumb_old'] && strpos($postarr['yt_thumb'], 'http') !== false)
    {
        if (pm_get_file_extension($postarr['yt_thumb']) == 'webp')
        {
            // replace .webp and _webp from URLs for youtube image URLs shown in Chrome
            $postarr['yt_thumb'] = str_replace(array('_webp', '.webp'), array('', '.jpg'), $postarr['yt_thumb']);
        }
        
        $download_thumb = $sources['localhost']['php_namespace'] .'\download_thumb';

        if ( ! function_exists($download_thumb))
        {
            require_once(ABSPATH . _ADMIN_FOLDER .'/src/localhost.php');
        }
        $img = $download_thumb($postarr['yt_thumb'], _THUMBS_DIR_PATH, $postarr['uniq_id'], true);
        generate_social_thumb($img);
    }

    if ($postarr['yt_thumb_local'] != '')
    {
        $postarr['yt_thumb'] = $postarr['yt_thumb_local'];
    }

    if ($postarr['yt_thumb'] == '' && $postarr['yt_thumb_old'] != '' && $postarr['yt_thumb_local'] == '')
    {
        if (file_exists(_THUMBS_DIR_PATH . $postarr['uniq_id'] .'-1.jpg'))
        {
            unlink(_THUMBS_DIR_PATH . $postarr['uniq_id'] .'-1.jpg');
        }

        if (file_exists(_THUMBS_DIR_PATH . $postarr['uniq_id'] .'-social.jpg'))
        {
            unlink(_THUMBS_DIR_PATH . $postarr['uniq_id'] .'-social.jpg');
        }
    }

    $added = validate_item_date($postarr);
    $added = pm_mktime($added);
    $sql .= ", yt_thumb = '". secure_sql($postarr['yt_thumb']) ."'";
    $sql .= ", featured = '". (int) $postarr['featured'] ."'";
    $sql .= ", restricted = '". (int) $postarr['restricted'] ."'";
    $sql .= ", allow_comments = '". (int) $postarr['allow_comments'] ."'";
    $sql .= ", allow_embedding = '". (int) $postarr['allow_embedding'] ."'";
    $sql .= ( ! empty($postarr['video_type']) && $postarr['video_type'] != $episode_data['video_type']) 
        ? ", video_type = '". $postarr['video_type'] ."'"
        : '';
    $sql .= " WHERE uniq_id = '". secure_sql($postarr['uniq_id']) ."' LIMIT 1";
    if ( ! $result = mysql_query($sql))
    {
        return array(
            'type' => 'error', 
            'msg' => 'MySQL Error: '. mysql_error()
        );
    }

    // update custom meta info with episode_id 
    if (is_array($postarr['meta']))
    {
        $meta_ids = array();
        foreach ($postarr['meta'] as $meta_id => $arr)
        {
            $meta_ids[] = $meta_id;
        }
        
        if (pm_count($meta_ids) > 0)
        {
            $sql = "UPDATE pm_meta 
                    SET item_id = $episode_id,  
                        item_type = ". IS_EPISODE ."
                    WHERE id IN (". implode(',', $meta_ids) .")";
            mysql_query($sql);
        }
    }

    $media_sources = array();
    if (pm_count($postarr['media_source']) > 0)
    {
        foreach ($postarr['media_source'] as $k => $data)
        {
            if (empty($data))
                continue;
            
            if ($postarr['media_source_type'][$k] == 'embed code')
            {
                $data = parse_video_embed_code($data);
            }
            $media_sources[] = array(
                'id' => ( ! empty($postarr['media_source_id'][$k])) 
                    ? $postarr['media_source_id'][$k] 
                    : md5($postarr['uniq_id'] . uniqid(rand(), true)),
                'type' => $postarr['media_source_type'][$k],
                'yt_id' => ( ! empty($postarr['media_source_yt_id'][$k])) ? $postarr['media_source_yt_id'][$k] : '',
                'data' => expand_common_short_urls($data)
            );
        }
    }

    $sql = "UPDATE pm_episodes 
            SET series_id = '". secure_sql($postarr['series_id']) ."',
                season_no = '". secure_sql($postarr['season_no']) ."',
                episode_no = '". secure_sql($postarr['episode_no']) ."',
                release_date = '". secure_sql($added) ."',
                media_sources = '". secure_sql(json_encode($media_sources)) ."'
            WHERE episode_id = ". $episode_id;
    
    if ( ! $result = mysql_query($sql))
    {
        return array(
            'type' => 'error', 
            'msg' => 'MySQL Error: '. mysql_error()
        );
    }

    // update Series internal season and episode counters
    if ($episode_data['series_id'] != $postarr['series_id'] 
        || $episode_data['season_no'] < $postarr['season_no']
    ) {
        $series_data = get_series($postarr['series_id']);
        
        if ($episode_data['series_id'] != $postarr['series_id'])
        {
            // $prev_series_data = get_series($episode_data['series_id']);
            $sql = "UPDATE pm_series 
                    SET episodes_count = episodes_count - 1
                    WHERE series_id = '". secure_sql($episode_data['series_id']) ."'";
            mysql_query($sql);

            $sql = "UPDATE pm_series 
                    SET episodes_count = episodes_count + 1
                    WHERE series_id = '". secure_sql($postarr['series_id']) ."'";
            mysql_query($sql);
        }

        if ($series_data['seasons_count'] < $postarr['season_no']) 
        {
            $sql = "UPDATE pm_series 
                    SET seasons_count = '". secure_sql($postarr['season_no']) ."' 
                    WHERE series_id = '". secure_sql($postarr['series_id']) ."'";
            mysql_query($sql);
        }
    }

    return array(
        'type' => 'ok',
        'msg' => 'Episode updated.'
    );
}

function episode_add_media_source($episode_id, $type, $data, $ms_id = '')
{
    $episode_data = get_episode($episode_id);
    $ms = array(
        'id' => ( ! empty($ms_id))  
            ? $ms_id
            : md5($episode_data['uniq_id'] . uniqid(rand(), true)),
        'type' => $type,
        'data' => $data
    );
    $episode_data['media_sources'][] = $ms;

    $sql = "UPDATE pm_episodes
            SET media_sources = '". secure_sql(json_encode($episode_data['media_sources'])) ."'
            WHERE episode_id = ". $episode_id;
    
    $result = mysql_query($sql);

    return $ms;
}

function episode_update_media_source($episode_id, $ms_id, $ms_data = array(), $episode_data = array())
{
    if (empty($episode_data))
    {
        $episode_data = get_episode($episode_id);
    }

    foreach ($episode_data['media_sources'] as $k => $ms)
    {
        if ($ms['id'] == $ms_id)
        {
            $episode_data['media_sources'][$k] = $ms_data;
        }
    }
    $sql = "UPDATE pm_episodes
            SET media_sources = '". secure_sql(json_encode($episode_data['media_sources'])) ."'
            WHERE episode_id = ". $episode_id;

    return $result = mysql_query($sql);
}

function episode_remove_media_source($episode_id, $ms_id = null, $type = null, $data = null)
{
    $episode_data = get_episode($episode_id);

    if ($episode_id > 0)
    {
        foreach ($episode_data['media_sources'] as $k => $ms)
        {
            if (($ms['id'] == $ms_id && ! empty($ms_id)) 
                || ($ms['type'] == $type && $ms['data'] == $data)
                || ($ms_id === null && $type === null && $data === null))  // remove all
            {
                if ($ms['type'] == 'file' && file_exists(_VIDEOS_DIR_PATH . $ms['data']))
                {
                    
                    $unlink = unlink(_VIDEOS_DIR_PATH . $ms['data']);
                }

                unset($episode_data['media_sources'][$k]);
            }
        }
    }
    else // on 'Add new episode'
    {
        if ($type == 'file' && file_exists(_VIDEOS_DIR_PATH . $data))
        {
            $unlink = unlink(_VIDEOS_DIR_PATH . $data);
        }
    }

    if ( ! $type && ! $data)
    {
        $episode_data['media_sources'] = array();
    }

    $sql = "UPDATE pm_episodes
            SET media_sources = '". secure_sql(json_encode($episode_data['media_sources'])) ."'
            WHERE episode_id = ". $episode_id;
    return ($result = mysql_query($sql)) ? true : false;
}

function delete_episode($episode_id) 
{
    global $config; 

    if ( ! $episode_id)
    {
        return array(
            'type' => 'error', 
            'msg' => 'Missing episode ID.'
        );
    }

    $episode_data = get_episode($episode_id);

    if (pm_count($episode_data) == 0)
    {
        return array(
            'type' => 'error', 
            'msg' => 'The requested episode was not found.'
        );
    }
    
    episode_remove_media_source($episode_id);
    
    $sql = "DELETE FROM pm_episodes 
            WHERE episode_id = ". secure_sql($episode_id);

    if ( ! $result = mysql_query($sql))
    {
        return array(
            'type' => 'error', 
            'msg' => 'Could not delete this episode. An error occurred: '. mysql_error()
        );
    }

    delete_video($episode_data['uniq_id']);

    $sql = "UPDATE pm_series 
            SET episodes_count = episodes_count - 1
            WHERE series_id = '". secure_sql($episode_data['series_id']) ."'";
    mysql_query($sql);

    return array(
        'type' => 'ok',
        'msg' => 'Episode successfully removed.'
    );
}



function list_genres_display_item($item, &$all_children, $level = 0, $options)
{
    $li_class =  $output = $li_item = '';

    if ( ! $item)
        return;
    
    $padding = str_repeat($options['spacer'], $level);
    $sub_cats = '';

    if (isset($all_children[$item['id']]) && ($level < $options['max_levels'] || $options['max_levels'] == 0))
    {
        $sub_cats .= "\n";
        
        foreach ($all_children[$item['id']] as $k => $child)
        {
            if ( ! isset($newlevel))
            {
                $newlevel = true;
//              $li_class .= 'topcat'; // @deprecated since v2.3
                $li_class .= 'dropdown-submenu';
//              $subcats_ul_class = ($child['id'] == $options['selected'] || $options['expand_items'] == true) ? 'visible_li' : 'hidden_li'; // @deprecated since v2.3
                $subcats_ul_class = ($child['id'] == $options['selected'] || $options['expand_items'] == true) ? 'dropdown-menu' : 'dropdown-menu';
                $sub_cats .= $padding."<ul class='".$subcats_ul_class."'>\n";
            }
            $sub_cats .= list_genres_display_item($child, $all_children, $level+1, $options);
        }
        unset($all_children[$item['id']]);
    }
    
    // li class
    if ($item['id'] == $options['selected'])
    {
        if ($item['parent_id'] == 0)
        {
            $li_class .= ' selectedcat';
        }
        else 
        {
            $li_class .= ' selectedsubcat';
        }
    }
    else 
    {
        $li_class .= '';
    }
    
    if ($options['selected_grandfather'] > 0)
    {
        if ($item['id'] == $options['selected_grandfather'])
        {
            if ($item['parent_id'] == 0)
            {
                $li_class .= ' selectedcat';
            }
            else 
            {
                $li_class .= ' selectedsubcat';
            }
        }
    }
        
    // li
    $output .= $padding .'<li class="'. $li_class .'"><a href="'. $item['url'] .'" class="'.$li_class.'">'. htmlentities($item['name'],ENT_COMPAT,'UTF-8') .'</a>';
    $output .= $sub_cats;
    
    if (isset($newlevel) && $newlevel)
    {
        $output .= $padding."</ul>\n";
    }
        
    $output .= $padding."</li>\n";
    
    return $output;
}

function list_genres($selected = 0, $args = array())
{
    $output = '';
    
    $defaults = array(
        'db_table' => 'pm_genres',
        'selected' => 0, 
        'order_by' => 'position',
        'sort' => 'ASC',
        'selected_grandfather' => 0, 
        'spacer' => "\t",
        'max_levels' => 1,
        'ul_wrapper' => true
    );
    
    $options = array_merge($defaults, $args);
    $options['selected'] = ( ! is_object($selected)) ? $selected : 0;
    extract($options);
    
    $parents = $parent_ids = $children = array();
    $genres = get_genres($options);
    
    foreach ($genres as $c_id => $c)
    {
        if ($c['parent_id'] == 0)
        {
            $parents[] = $c;
            $parent_ids[] = $c['id'];
        }
        else
        {
            $children[$c['parent_id']][] = $c;
        }
    }

    // find "grandfather" of selected child category
    if (pm_count($parent_ids) > 0 && $selected > 0 && ( ! in_array($selected, $parent_ids)))
    {
        $options['selected_grandfather'] = $selected;

        $counter = 0;
        $exit_limit = pm_count($parent_ids) * 3;
        while (( ! in_array($options['selected_grandfather'], $parent_ids)) && $counter < $exit_limit)
        {
            $find = $options['selected_grandfather'];
            foreach ($children as $pid => $children_arr)
            {
                $found = false;
            
                if (pm_count($children_arr) > 0)
                {
                    foreach ($children_arr as $k => $child)
                    {
                        if ($child['id'] == $find)
                        {
                            $found = true;
                            $options['selected_grandfather'] = $child['parent_id'];
                            break;
                        }
                    }
                    if ($found)
                    {
                        break;
                    }
                }
            }
            
            $counter++;
        }
    }
    
    foreach ($parents as $k => $p)
    {
        $options['expand_items'] = ($options['selected_grandfather'] == $p['id']) ? true : false;
        $output .= list_genres_display_item($p, $children, 0, $options);
    }

    if (pm_count($children) > 0 && $options['max_levels'] == 0)
    {
        foreach ($children as $parent_id => $orphans)
        {
            foreach ($orphans as $k => $orphan)
            {
                $orphan['parent_id'] = 0;
                $output .= list_genres_display_item($orphan, $empty, 0, $options);
            }
        }
    }
    
    return $output;
}

function smarty_html_list_genres($params, $smarty)
{
    $selected = ($params['selected']) ? $params['selected'] : 0;
    unset($params['selected']);
    return list_genres(0, $selected, $params);
}

function update_series_views($series_id)
{
    $sql = "UPDATE pm_series 
            SET views = views + 1 
            WHERE series_id IN(". secure_sql($series_id) .")";
    return (mysql_query($sql)) ? true : false;
}