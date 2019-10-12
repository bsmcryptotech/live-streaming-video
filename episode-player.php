<?php
// header('X-Frame-Options: sameorigin');
session_start();
define('IGNORE_MOBILE', true);
require('config.php');
require_once(ABSPATH .'include/islogged.php');

if ( ! _MOD_SERIES && ! (is_admin() || (is_moderator() && mod_can('manage_videos'))))
{
    redirect_404();
}

$episode_id = (int) $_GET['id'];
$ms_id = filter_input(INPUT_GET, 'ms_id', FILTER_SANITIZE_STRING);

$episode_data = $media_source_found = false;
if ( ! empty($episode_id) && ctype_alnum($ms_id))
{
    $episode_data = get_episode($episode_id);
}
if (is_array($episode_data) && is_array($episode_data['media_sources']))
{
    foreach ($episode_data['media_sources'] as $k => $ms)
    {
        if ($ms['id'] == $ms_id)
        {
            $media_source_found = true; 
            break;
        }
    }
}

$preroll_ad_data = false;
$display_preroll_ad = (is_array($preroll_ad_data)) ? true : false;
?>
<!DOCTYPE html>
<!--[if IE 7 | IE 8]>
<html class="ie" dir="ltr" lang="en">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html dir="ltr" lang="en">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="robots" content="noindex">
<meta name="googlebot" content="noindex">
<title><?php echo $episode_data['video_title'];?></title>
<link rel="canonical" href="<?php echo $episode_data['url'];?>">
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo _URL; ?>/templates/apollo/css/apollo.css">
<style type="text/css">
html{overflow:hidden}
body{margin:0;padding:0;border:0;font-size:100%;font:12px Arial,sans-serif;background-color:#000;color:#fff;height:100%;width:100%;overflow:hidden;position:absolute;-webkit-tap-highlight-color:rgba(0,0,0,0)}
a{color:#fff}
p{padding:5px 10px}
object,embed,iframe{margin:0;padding:0;border:0;font-size:100%;background:transparent}
.full-frame{width:100%;height:100%}
h1,h2,h3,h4,h5{text-align:center;color:#fff}
#preroll_placeholder{position:relative;display:block;width:100%;text-align:center}
#preroll_placeholder embed,iframe{max-width:99%;}
.preroll_countdown{padding:3px 0}
.embed_logo img{max-width:95%}
.restricted-video{display:block;height:100%;background:url('<?php echo $episode_data['image_url']; ?>') no-repeat center center;text-shadow: #000 1px 0 10px;}
.btn {font-family: Arial, Helvetica, sans-serif;border: 1px solid #c6c6c6;outline: 0;}
.btn-success{margin:0 auto;display:block;width:130px;font-size:11px;font-weight:bold;text-align:center;text-decoration:none;padding:5px 10px;color:#fff;text-shadow:0 -1px 0 rgba(0,0,0,0.25);background-color:#77a201;border-width:2px;border-style:solid;border-color:#688e00 #688e00 #8eaf33;border-color:rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);-moz-border-radius:4px;-webkit-border-radius:4px;border-radius:4px;box-shadow:0 1px 3px #000}
.btn-success:hover,.btn-success:active,.btn-success.active,.btn-success.disabled,.btn-success[disabled]{color:#fff;background-color:#8eaf33;box-shadow:none}
.btn-success:active,.btn-success.active{background-color:#3C0}
.btn-blue {  color: #fff; text-shadow: 0 1px 0 #2d8fc4; background-color: #359ad1;}
@-o-viewport{width:device-width}
@-moz-viewport{width:device-width}
@-ms-viewport{width:device-width}
@-webkit-viewport{width:device-width}
@viewport{width:device-width}
</style>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<?php if ($display_preroll_ad) : ?>
    <script src="<?php echo _URL; ?>/js/jquery.timer.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#video_player_container').hide();
        });
    </script>
<?php endif; ?>
</head>
<body>
    
    <?php if ( ! $episode_data || ! $media_source_found) : // video not found ?>
        <h1><?php echo $lang['sorry'];?></h1>
        <h3><?php echo $lang['video_not_found'];?></h3>
        <p>
            <?php if ($config['custom_logo_url'] != '') : ?> 
                <div class="embed_logo" align="center"><a href="<?php echo _URL;?>" target="_blank"><img src="<?php echo make_url_https($config['custom_logo_url']); ?>" alt="<?php echo htmlspecialchars(_SITENAME);?>" title="<?php echo htmlspecialchars(_SITENAME);?>" border="0"></a></div>
            <?php else : ?>
                <h3><a href="<?php echo _URL;?>" target="_blank"><?php echo _SITENAME;?></a></h3>
            <?php endif; ?>
        </p>
</body>
</html>
    <?php
            exit(); 
        endif;
    ?>
    
    <?php if ( ! $logged_in && $episode_data['restricted'] == '1') : ?>
    <div class="restricted-video">
        <h2><?php echo $episode_data['video_title'];?></h2>
        <h3><?php echo $lang['restricted_sorry'];?></h3>
        <p>
            <a href="<?php echo _URL .'/register.' ._FEXT; ?>" target="_blank" class="btn-success"><?php echo $lang['proceed'];?></a>
        </p>
    </div>
</body>
</html>
    <?php
            exit(); 
        endif;
    ?>
    <?php if ($display_preroll_ad) : ?>
        <div id="preroll_placeholder">
            <div class="preroll_countdown">
                <?php echo $lang['preroll_ads_timeleft']; ?> <span class="preroll_timeleft"><?php echo $preroll_ad_data['timeleft_start'];?></span>
            </div>
            <?php echo $preroll_ad_data['code']; ?>

            <?php if ($preroll_ad_data['skip']) : ?>
                <div class="preroll_skip_countdown">
                    <?php echo $lang['preroll_ads_skip_msg']; ?> <span class="preroll_skip_timeleft"><?php echo $preroll_ad_data['skip_delay_seconds']; ?></span>
                </div>
                <br />
                <button class="btn btn-blue" id="preroll_skip_btn"><?php echo $lang['preroll_ads_skip']; ?></button>
            <?php endif; ?>
            <?php if ($preroll_ad_data['disable_stats'] == 0) : ?>
                <img src="<?php echo _URL; ?>/ajax.php?p=stats&do=show&aid=<?php echo $preroll_ad_data['id']; ?>&at=<?php echo _AD_TYPE_PREROLL; ?>" width="1" height="1" border="0" />
            <?php endif; ?>
        </div>
    <?php 
        endif;
    ?>

    <div class="pm-section-highlighted">
    <div id="video-wrapper">
    <div id="Playerholder">
        <div id="video_player_container">
            <?php 
            foreach ($episode_data['media_sources'] as $k => $ms)
            {
                if ($ms['id'] == $ms_id)
                {
                    switch ($ms['type'])
                    {
                        case 'file':
                        case 'url':

                            $video_sources = fetch_video_sources();
                            $file_vs_ids = array(
                              (int) $video_sources['localhost']['source_id'],
                              (int) $video_sources['other']['source_id'],
                              (int) $video_sources['windows media player']['source_id'],
                              (int) $video_sources['quicktime']['source_id'],
                              (int) $video_sources['divx']['source_id'],
                              (int) $video_sources['mp3']['source_id'],
                              (int) $video_sources['localhost']['source_id']
                            );
                            $source_id = ($ms['type'] == 'file') ? $video_sources['localhost']['source_id'] : $video_sources['other']['source_id'];
                            $file_ext = pm_get_file_extension($ms['data']);
                            $file_type = pm_ext_to_type($file_ext);

                            $video_player = $config['video_player'];
                            $source_id = (int) $source_id;
                            
                            if ($ms['type'] == 'url')
                            {
                                foreach($video_sources as $src_id => $source)
                                {
                                    if (@preg_match($source['source_rule'], $ms['data']))
                                    {
                                        $source_id = (int) $source['source_id'];
                                        break;
                                    }
                                }
                            }

                            if ($config['video_player'] == 'embed')
                            {
                                $video_player  = 'videojs';
                            }

                            if ( ! in_array($source_id, $file_vs_ids))
                            {
                                if ( ! file_exists(ABSPATH . _ADMIN_FOLDER .'/src/' . $video_sources[$source_id]['source_name'] . '.php'))
                                {
                                    report_video($video['uniq_id'], '1', secure_sql('A problem ocurred while resolving the following media source URL: '. $ms['data'] .'<br />Please check the URL again and make sure it\'s valid and from a supported 3rd party source.'), 'PM Bot');
                                    ?>
                                    <h1><?php echo $lang['sorry'];?></h1>
                                    <h3><?php echo $lang['video_not_found'];?></h3>
                                    <p>
                                        <?php if ($config['custom_logo_url'] != '') : ?> 
                                            <div class="embed_logo" align="center"><a href="<?php echo _URL;?>" target="_blank"><img src="<?php echo make_url_https($config['custom_logo_url']); ?>" alt="<?php echo htmlspecialchars(_SITENAME);?>" title="<?php echo htmlspecialchars(_SITENAME);?>" border="0"></a></div>
                                        <?php else : ?>
                                            <h3><a href="<?php echo _URL;?>" target="_blank"><?php echo _SITENAME;?></a></h3>
                                        <?php endif; ?>
                                    </p>
                                    <?php 

                                    break;
                                }
                                else
                                {
                                    if (empty($ms['yt_id']))
                                    {
                                        $temp = array();
                                        $do_main = $video_sources[$source_id]['php_namespace'] .'\do_main';
                                        if ( ! function_exists($do_main))
                                        {
                                            require_once(ABSPATH . _ADMIN_FOLDER .'/src/' . $video_sources[$source_id]['source_name'] . '.php');
                                        }
                                        $do_main($temp, $ms['data']);
                                        $episode_data['media_sources'][$k]['yt_id'] = $ms['yt_id'] = $temp['yt_id'];
                                        episode_update_media_source($episode_data['episode_id'], $ms['id'], $ms, $episode_data);
                                    }
                                }

                                $embed_code = $video_sources[$source_id]['embed_code'];

                                if ($source_id == $video_sources['sevenload']['source_id'] && strlen($ms['yt_id']) > 7)
                                {
                                    $ms['yt_id'] = substr($ms['yt_id'], 0, 7);
                                }
                                $embed_code = str_replace(
                                    array(
                                        '%%yt_id%%', '%%player_h%%', '%%player_w%%',
                                        '%%player_timecolor%%', '%%player_bgcolor%%', '%%player_timecolor%%'
                                    ),
                                    array(
                                        $ms['yt_id'], '100%', '100%', 
                                        _TIMECOLOR, _BGCOLOR, _TIMECOLOR
                                    ),
                                    $embed_code
                                );
                                $embed_code = str_replace("%%url_flv%%", make_url_https(str_replace("&", "&amp;", $ms['data'])), $embed_code);
                                $embed_code = str_replace("%%direct%%", '', $embed_code);
                                $embed_code = str_replace("%%use_hq_vids%%", $config['use_hq_vids'], $embed_code);
                                $embed_code = str_replace("%%yt_thumb%%", urlencode($episode_data['preview_image']), $embed_code);
                                $embed_code = str_replace("%%player_wmode%%", 'opaque', $embed_code);
                                $embed_code = str_replace("%%player_autoplay%%", '0', $embed_code);
                                $embed_code = str_replace("%%player_autoplay%%", '0', $embed_code);
                                $embed_code = str_replace( array("\n", "\r", "'"), array(' ', ' ', '"'), $embed_code);

                                // custom params
                                if ($source_id == $video_sources['dailymotion']['source_id'] && $config['dailymotion_syndication_key'] != '') 
                                {
                                    $embed_code = str_replace('%%player_extra_params%%', 'syndication='. $config['dailymotion_syndication_key'], $embed_code);
                                }
                                else
                                {
                                    $embed_code = str_replace('%%player_extra_params%%', '', $embed_code);
                                }
                            }
                            else
                            {
                                // uploaded file or file URL
                                switch ($file_ext)
                                {
                                    case 'mov':
                                    case '3gp':
                                    case '3g2':
                                    case 'm4a':
                                        
                                        $video_player  = 'quicktime';
                                        
                                    break;

                                    case 'mp3':
                                        
                                        // $video_player = 'videojs';

                                    break; 

                                    case 'wmv':
                                    case 'asf':
                                    case 'wma':
                                        
                                        $video_player  = 'windows media player';
                                        
                                    break;
                                    
                                    case 'mkv':
                                    case 'divx':
                                    case 'avi':
                                        
                                        $video_player  = 'divx';
                                        
                                    break;
                                }

                                $player_src = ($ms['type'] == 'file') 
                                    ? _VIDEOS_DIR . $ms['data']
                                    : $ms['data'];

                                switch ($video_player)
                                {
                                    case 'flvplayer': ?> 
                                        <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="100%" height="100%">
                                            <param name="allowFullScreen" value="true" />
                                            <param name="allowScriptAccess" value="always" />
                                            <param name="allowNetworking" value="all" />
                                            <param name="bgcolor" value="#<?php echo _BGCOLOR; ?>" />
                                            <param name="movie" value="<?php echo $player_src; ?>" />
                                            <embed src="<?php echo $player_src; ?>" width="100%" height="100%" bgcolor="#<?php echo _BGCOLOR; ?>" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always"  allowNetworking="all" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="window"></embed>
                                        </object>

                                    <?php break;

                                    case 'jwplayer': 

                                        $jw_flashvars = '';
                                        if ($file_ext == 'mp3')
                                        {
                                            $jw_flashvars .= '&file='. urlencode($player_src);
                                            $jw_flashvars .= '&type=audio';
                                        }
                                        else
                                        {
                                            $jw_flashvars .= '&file='. urlencode($player_src);
                                            $jw_flashvars .= '&type=video';
                                        }
                                        $jw_flashvars .= '&config='. urlencode(_URL ."/players/jwplayer5/jwembed.xml");
                                        $jw_flashvars .= '&backcolor='. _BGCOLOR;
                                        $jw_flashvars .= '&frontcolor='. _TIMECOLOR;
                                        $jw_flashvars .= '&screencolor=000000';
                                        $jw_flashvars .= '&image='. urlencode( make_url_https($episode_data['preview_image']) ); 
                                        $jw_flashvars .= '&logo='. urlencode(_WATERMARKURL);
                                        $jw_flashvars .= '&link='. urlencode(_WATERMARKLINK);
                                        $jw_flashvars .= '&skin='. urlencode(_URL) .'/players/jwplayer5/skins/'. _JWSKIN;
                                        $jw_flashvars .= '&bufferlength=5'; 
                                        $jw_flashvars .= '&plugins=timeslidertooltipplugin-2'; 
                                        ?>
                                        <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="100%" height="100%">
                                            <param name="allowFullScreen" value="true" />
                                            <param name="allowScriptAccess" value="always" />
                                            <param name="allowNetworking" value="all" />
                                            <param name="bgcolor" value="#<?php echo _BGCOLOR; ?>" />
                                            <param name="movie" value="<?php echo _URL .'/players/jwplayer5/jwplayer.swf'; ?>" />
                                            <param name="flashVars" value="<?php echo $jw_flashvars; ?>" />
                                            <embed src="<?php echo _URL; ?>/players/jwplayer5/jwplayer.swf"  width="100%" height="100%" bgcolor="<?php echo _BGCOLOR; ?>" type="application/x-shockwave-flash" allowFullScreen="true"  allowScriptAccess="always" wmode="window"  flashvars="<?php echo $jw_flashvars; ?>"></embed>
                                        </object>
                                    <?php break;
                                    
                                    case 'jwplayer6': ?> 
                                        <div id="Playerholder"></div>
                                        <script type="text/javascript" src="<?php echo _URL; ?>/players/jwplayer6/jwplayer.js"></script>
                                        <script type="text/javascript">
                                            jwplayer.key="";
                                        </script>
                                        <style>
                                            .jwplayer .jwcontrolbar {display: inline-block !important;opacity: 1 !important;}
                                        </style>
                                        <script type="text/javascript">
                                            var flashvars = {
                                                flashplayer: '<?php echo _URL; ?>/players/jwplayer6/jwplayer.flash.swf',
                                                file: '<?php echo $player_src;?>',
                                                <?php if ($file_ext == 'mp3') : ?>
                                                    type: 'mp3',
                                                <?php endif; ?>
                                                primary: 'HTML5',
                                                width: '100%',
                                                height: '100%',
                                                image: '<?php echo make_url_https($episode_data['preview_image']); ?>',
                                                logo: {
                                                    file: '<?php echo _WATERMARKURL; ?>',
                                                    link: '<?php echo _WATERMARKLINK; ?>'
                                                },
                                                tracks: []
                                            };
                                            jwplayer('Playerholder').setup(flashvars);
                                        </script>
                                    <?php break;
                                    
                                    case 'jwplayer7': ?> 
                                        <div id="Playerholder"></div>
                                        <script type="text/javascript" src="<?php echo _URL; ?>/players/jwplayer7/jwplayer.js"></script>
                                        <script type="text/javascript">
                                            jwplayer.key="<?php echo $config['jwplayer7key']; ?>";
                                        </script>
                                        <script type="text/javascript">
                                            var flashvars = {
                                                flashplayer: '<?php echo _URL; ?>/players/jwplayer7/jwplayer.flash.swf',
                                                file: '<?php echo $player_src;?>',
                                                <?php if ($file_ext == 'mp3') : ?>
                                                    type: 'mp3',
                                                <?php endif; ?>
                                                primary: 'flash',
                                                width: '100%',
                                                height: '100%',
                                                image: '<?php echo make_url_https($episode_data['preview_image']); ?>',
                                                logo: {
                                                    file: '<?php echo _WATERMARKURL; ?>',
                                                    link: '<?php echo _WATERMARKLINK; ?>'
                                                },
                                                // sharing: {
                                                // },
                                                tracks: []
                                            };
                                            jwplayer('Playerholder').setup(flashvars);
                                        </script>
                                    <?php break;

                                    case 'jwplayer8': ?> 
                                        <div id="Playerholder"></div>
                                        <script type="text/javascript" src="<?php echo _URL; ?>/players/jwplayer8/jwplayer.js"></script>
                                        <script type="text/javascript">
                                            jwplayer.key="<?php echo $config['jwplayer8key']; ?>";
                                        </script>
                                        <script type="text/javascript">
                                            var flashvars = {
                                                file: '<?php echo $player_src;?>',
                                                <?php if ($file_ext == 'mp3') : ?>
                                                    type: 'mp3',
                                                <?php endif; ?>
                                                width: '100%',
                                                height: '100%',
                                                image: '<?php echo make_url_https($episode_data['preview_image']); ?>',
                                                logo: {
                                                    file: '<?php echo _WATERMARKURL; ?>',
                                                    link: '<?php echo _WATERMARKLINK; ?>'
                                                },
                                            };
                                            jwplayer('Playerholder').setup(flashvars);
                                        </script>
                                    <?php break;

                                    case 'videojs': 
                                    ?> 
                                        <div id="Playerholder">
                                        <link href="<?php echo _URL; ?>/players/video-js/video-js.min.css" rel="stylesheet">
                                        <link href="<?php echo _URL; ?>/players/video-js/video-js.pm.css" rel="stylesheet">
                                        <script type="text/javascript" src="<?php echo _URL; ?>/players/video-js/video.js"></script>
                                        <video src="" id="video-js" class="video-js vjs-default-skin" poster="<?php echo make_url_https($episode_data['preview_image']); ?>" preload="" data-setup='{ "techOrder": ["html5"], "controls": true }' width="100%" height="100%"></video>
                                        <script type="text/javascript">
                                            var video = videojs('video-js').ready(function() {
                                                var player = this;
                                                player.on('loadedmetadata', function() {
                                                    $('.vjs-big-play-button').addClass('vjs-pm-show-big-play');
                                                });

                                                player.src([{
                                                    src: "<?php echo $player_src; ?>",
                                                    <?php if ($file_type) : ?>
                                                        <?php if ($file_ext == 'mp3') : ?>
                                                            type: "audio/mp3"
                                                        <?php else : ?>
                                                            type: "<?php echo $file_type; ?>"
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        type: "video/x-flv"
                                                    <?php endif; ?>
                                                }]);
                                            });
                                        </script>
                                        </div>
                                    <?php break;

                                    case 'divx':
                                    case 'windows media player':
                                    case 'quicktime':

                                        $embed_code = $video_sources[$video_player]['embed_code']; 
                                        $embed_code = str_replace("%%player_w%%", '100%', $embed_code);
                                        $embed_code = str_replace("%%player_h%%", '100%', $embed_code);
                                        $embed_code = str_replace("%%yt_id%%", '', $embed_code);
                                        $embed_code = str_replace("%%player_bgcolor%%", _BGCOLOR, $embed_code);
                                        $embed_code = str_replace("%%player_timecolor%%", _TIMECOLOR, $embed_code);
                                        $embed_code = str_replace("%%url_flv%%", make_url_https(str_replace("&", "&amp;", $player_src)), $embed_code);
                                        $embed_code = str_replace("%%direct%%", '', $embed_code);
                                        $embed_code = str_replace("%%use_hq_vids%%", $config['use_hq_vids'], $embed_code);
                                        $embed_code = str_replace("%%yt_thumb%%", urlencode($episode_data['preview_image']), $embed_code);
                                        $embed_code = str_replace("%%player_wmode%%", 'opaque', $embed_code);
                                        $embed_code = str_replace("%%player_autoplay%%", '0', $embed_code);

                                    break; 
                                }
                            } // else source = localhost or other

                            echo $embed_code; 
                            
                        break;

                        case 'embed code':
                            
                            echo str_replace(
                                array('%%player_h%%', '%%player_w%%'),
                                array('100%', '100%'),
                                $ms['data']
                            );

                        break;
                    }

                    break; // exit foreach loop
                }
            }
            ?>
        </div>
    </div>
    </div>
    </div>

    <!-- Footer -->
    
    <?php if ($display_preroll_ad) : ?>
    <script type="text/javascript">
    
    function timer_pad(number, length) {
        var str = '' + number;
        while (str.length < length) {str = '0' + str;}
        return str;
    }
    
    var preroll_timer;
    var preroll_player_called = false;
    var skippable = <?php echo ($preroll_ad_data['skip'] != 1) ? 0 : 1; ?>; 
    var skippable_timer_current = <?php echo ($preroll_ad_data['skip_delay_seconds']) ? $preroll_ad_data['skip_delay_seconds'] : 0; ?> * 1000;
    
    $(document).ready(function(){
        if (skippable == 1) {
            $('#preroll_skip_btn').hide();
        }
        
        var preroll_timer_current = <?php echo $preroll_ad_data['duration']; ?> * 1000;
        
        preroll_timer = $.timer(function(){
        
            var seconds = parseInt(preroll_timer_current / 1000);
            var hours = parseInt(seconds / 3600);
            var minutes = parseInt((seconds / 60) % 60);
            var seconds = parseInt(seconds % 60);
            
            var output = "00";
            if (hours > 0) {
                output = timer_pad(hours, 2) +":"+ timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
            } else if (minutes > 0) { 
                output = timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
            } else {
                output = timer_pad(seconds, 1);
            }
            
            $('.preroll_timeleft').html(output);
            
            if (preroll_timer_current == 0 && preroll_player_called == false) {
                                
                //$('#preroll_placeholder').replaceWith(video_embed_code);
                $('#preroll_placeholder').hide();
                $('#video_player_container').show();

                preroll_player_called = true;
                preroll_timer.stop();
            } else {
                preroll_timer_current -= 1000;
                if(preroll_timer_current < 0) {
                    preroll_timer_current = 0;
                }
            }
        }, 1000, true);
        if (skippable == 1) {
        
            skippable_timer = $.timer(function(){
        
                var seconds = parseInt(skippable_timer_current / 1000);
                var hours = parseInt(seconds / 3600);
                var minutes = parseInt((seconds / 60) % 60);
                var seconds = parseInt(seconds % 60);
                
                var output = "00";
                if (hours > 0) {
                    output = timer_pad(hours, 2) +":"+ timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
                } else if (minutes > 0) { 
                    output = timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
                } else {
                    output = timer_pad(seconds, 1);
                }
                
                $('.preroll_skip_timeleft').html(output);
                
                if (skippable_timer_current == 0 && preroll_player_called == false) {
                    $('#preroll_skip_btn').show();
                    $('.preroll_skip_countdown').hide();
                    skippable_timer.stop();
                } else {
                    skippable_timer_current -= 1000;
                    if(skippable_timer_current < 0) {
                        skippable_timer_current = 0;
                    }
                }
            }, 1000, true);
            
            $('#preroll_skip_btn').click(function(){
                preroll_timer_current = 0;
                skippable_timer_current = 0;
                
                <?php if ($preroll_ad_data['disable_stats'] == 0) : ?>
                $.ajax({
                    type: "GET",
                    url: "<?php echo _URL .'/ajax.php';?>",
                    dataType: "html",
                    data: {
                        "p": "stats",
                        "do": "skip",
                        "aid": "<?php echo $preroll_ad_data['id']; ?>",
                        "at": "<?php echo _AD_TYPE_PREROLL; ?>",
                    },
                    dataType: "html",
                    success: function(data){}
                });
                <?php endif; ?>
                return false;
            });
            
        }
        
    });
    </script>
    <?php endif; ?>
</body>
</html>
<?php 
