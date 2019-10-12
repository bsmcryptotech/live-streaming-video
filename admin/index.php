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
// | Copyright: (c) 2004-2014 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '1';
$_page_title = 'Dashboard';

$load_scrollpane = 1;
$load_dotdotdot = 1;
include('header.php');
include_once('syndicate-news.php');

if ($_GET['close-welcome'] != '' || (($time_now - (86400 * 14) > (int) $config['firstinstall']) && $config['admin_welcome'] == 1))
{
	update_config('admin_welcome', 0, true);
}

$widget_items_limit = 10;

?>
<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><a href="<?php echo _URL; ?>" class="open-in-new" target="_blank" data-popup="tooltip" data-placement="right" data-original-title="Switch to Front-End"><span class="font-weight-semibold"><?php echo $_page_title; ?></span> <i class="mi-open-in-new"></i></a></h4>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<span class="breadcrumb-item active"><?php echo $_page_title; ?></span>
				</div>


			</div>

			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
					<a href="#" id="show-help-assist" class="breadcrumb-elements-item"><i class="mi-help-outline text-muted"></i></a>
				</div>
			</div>
		</div>
	</div><!--.page-header -->
</div><!--.page-header-wrapper-->	
<div class="page-help-panel" id="help-assist"> 
		<div class="row">
			<div class="col-2 help-panel-nav">
				<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<a class="nav-link active" id="v-pills-tab-help-one" data-toggle="pill" href="#v-pills-one" role="tab" aria-controls="v-pills-one" aria-selected="true" data-toggle="tab">Overview</a>
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">Navigation</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>Welcome to your dashboard! This page contains a quick overview of your excellent video site.</p>
						<p>A brief glance at the <strong>Quick Stats</strong> panel below will reveal all the important numbers. Actions requiring your attention will appear in dark orange, while critical items will appear in red. Such actions include comments and/or videos awaiting approval, reported videos, etc..</p>
						<p>The <strong>Quick Stats</strong> blocks of information are also clickable and act as shortcuts to the various areas of this administration panel.</p>
						<p>The <strong>Updates</strong> section is will keep you informed about critical PHP MELODY updates as well as current developments. New notifications will appear highlighted and they will stay so for 14 days.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
						<p>The right hand top corner acts as a shortcuts area as well as an information center. Regardless of the page you're browsing, you'll always be able to see items which require your immediate attention. These notifications will appear as an item in your personal menu.</p>
						<p>Regardless of the page you're browsing the &quot;Add Video&quot; button allows you to post, search and upload videos with a single click. These three forms will appear in the &quot;Add Video&quot; window. The first form (Youtube Import) allows you to quickly search the Youtube API for any sort of video imaginable. It's by far the easiest and most efficient way to add content to your site. The second form (Direct Input) allows you to simply paste any video URL, both of local and remotely hosted videos from any of the 50+ supported sources (Vimeo, DailyMotion, etc.). The third form allows you to upload your latest creations.</p>
						<p>The left hand side navigation is partitioned by content type: videos, articles, pages, categories, comments, users and so on. Each of these menu items is clickable and most contain submenus which will appear on hover. The left hand side navigation also displays a notification in the form of a small icon containing a number with a red background. Those numbers indicate the required actions demanding your attention. Such notifications will include reported videos, videos pending approval and so on.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content">
<!--[if lt IE 9]>
<div class="alert alert-danger alert-styled-left alert-dismissible"><strong>Your browser may be too old</strong>. We strongly recommend using a newer/different browser such as <strong><a href="https://www.google.com/intl/en/chrome/browser/" target="_blank">Chrome</a></strong> or <strong><a href="http://www.mozilla.org/en-US/firefox/new" target="_blank">Firefox</a></strong>.</div>
<![endif]-->
<?php if ( $config['admin_welcome']) : ?>
<div class="card">
	<div class="card-header alpha-success text-success-800 d-flex justify-content-between">
		<span class="font-weight-semibold">Welcome to your new video website!</span>
		<div class="d-flex justify-content-between">
			<div class="list-icons">
				<a href="index.php?close-welcome=yes" class="list-icons-item" data-action="remove"></a>
			</div>
		</div>
	</div>
	<div class="card-body alpha-success">
		<div class="row">
			<div class="col-md-4">
				<h6 class="mb-2"><strong>Make it your own</strong></h6>
				<?php if ($config['template_f'] == 'default') : ?>
				<a href="customize.php" class="btn btn-large btn-success" target="_blank"><i class="mi-format-paint"></i> Customize the layout</a>
				<?php else : ?>
				<a href="theme-settings.php" class="btn btn-large btn-success bg-success mb-2" target="_blank"><i class="mi-format-paint"></i> Customize the layout</a>
				<?php endif; ?>
				<p><a href="theme-settings.php" class="text-grey-800">Change your site&rsquo;s title</a> or <a href="theme-settings.php" class="text-grey-800">upload your logo</a>.</p>
			</div>
			<div class="col-md-4">
				<h6 class="mb-2">Publish content</h6>
				<ul class="list-unstyled">
					<li class="mb-1"><i class="icon-play3 text-grey-300"></i> <a href="#addVideo" class="ml-1 text-grey-800" data-toggle="modal">Add your first video</a></li>
					<li class="mb-1"><i class="icon-magazine text-grey-300"></i> <a href="edit-article.php?do=new" class="ml-1 text-grey-800">Write your first article</a></li>
					<li class="mb-1"><i class="icon-files-empty text-grey-300"></i> <a href="edit-page.php?do=new" class="ml-1 text-grey-800">Create your first page</a></li>
				</ul>
			</div>
			<div class="col-md-4">
				<h6 class="mb-2">More actions</h6>
				<ul class="list-unstyled">
					<li class="mb-1"><i class="icon-users text-grey-300"></i> <a href="edit-user.php?uid=1" class="ml-1 text-grey-800">Customize your profile</a></li>
					<li class="mb-1"><i class="icon-film text-grey-300"></i> <a href="player-static-ads.php" class="ml-1 text-grey-800">Create some pre-roll ads</a></li>
					<li class="mb-1"><i class="icon-cogs text-grey-300"></i> <a href="settings.php" class="ml-1 text-grey-800">Update existing settings</a></li>
				</ul>
			</div>
		</div>

	</div>
</div>
<?php endif; ?>


<div class="row">
	<div class="col-xl-6">
		<div class="card">
			<div class="card-header bg-white py-3">
				<h6 class="card-title font-weight-semibold">Quick Stats</h6>
			</div>
			<div class="card-body card-body-index-stats">
				<div class="row text-left my-3 pb-3">
						<div class="col-md-3">
							<a href="videos.php">
								<h5 class="font-weight-semibold mb-0 text-dark"><?php echo pm_number_format($config['total_videos']); ?></h5>
								<span class="text-muted font-size-sm text-uppercase font-weight-semibold font-size-xs">Video<?php echo ($config['total_videos'] == 1) ? '' : 's'; ?></span>
							</a>
						</div>
						<div class="col-md-3">
							<a href="pending-videos.php">
								<?php
								$vapprv = ($vapprv > 0) ? $vapprv : count_entries('pm_temp', '', '');
								?>
								<h5 class="font-weight-semibold mb-0 text-dark <?php if($vapprv > 0) {?>qspending<?php } ?>"><?php echo pm_number_format($vapprv); ?></h5>
								<span class="text-muted font-size-sm text-uppercase font-weight-semibold font-size-xs <?php if($vapprv > 0) {?>qspending<?php } ?>">Pending videos</span>
							</a>
						</div>
						<div class="col-md-3">
							<a href="reported-videos.php">
								<h5 class="font-weight-semibold mb-0 text-dark <?php if($crps > 0) {?>qsreported<?php } ?>"><?php echo pm_number_format($crps); ?></h5>
								<span class="text-muted font-size-sm text-uppercase font-weight-semibold font-size-xs <?php if($crps > 0) {?>qsreported<?php } ?>">Reported video<?php echo ($crps == 1) ? '' : 's';?></span>
							</a>
						</div>
						<div class="col-md-3">
							<a href="comments.php">
								<h5 class="font-weight-semibold mb-0 text-dark"><?php echo pm_number_format($comments_count = count_entries('pm_comments', '', '')); ?></h5>
								<span class="text-muted font-size-sm text-uppercase font-weight-semibold font-size-xs">Comment<?php echo ($comments_count == 1) ? '' : 's';?></span>
							</a>
						</div>
				</div>
				<div class="row text-left mb-3 mt-3 pt-3">

						<?php if ($config['comment_system'] == 'on') : ?>
						<div class="col-md-3">
							<a href="comments.php?filter=pending">
								<h5 class="font-weight-semibold mb-0 text-dark <?php if($capprv > 0) {?>qspending<?php } ?>"><?php echo pm_number_format($capprv); ?></h5>
								<span class="text-muted font-size-sm text-uppercase font-weight-semibold font-size-xs <?php if($capprv > 0) {?>qspending<?php } ?>">New Comment<?php echo ($capprv == 1) ? '' : 's';?></span>
							</a>
						</div>
						<?php endif; ?>
						<div class="col-md-3">
							<a href="users.php">
								<h5 class="font-weight-semibold mb-0 text-dark"><?php echo pm_number_format($member_count = count_entries('pm_users', '', '')); ?></h5>
								<span class="text-muted font-size-sm text-uppercase font-weight-semibold font-size-xs">User<?php echo ($member_count == 1) ? '' : 's';?></span>
							</a>
						</div>
						<?php if (_MOD_ARTICLE == 1) : ?>
						<div class="col-md-3">
							<a href="articles.php">
								<h5 class="font-weight-semibold mb-0 text-dark"><?php echo pm_number_format($config['total_articles']); ?></h5>
								<span class="text-muted font-size-sm text-uppercase font-weight-semibold font-size-xs">Article<?php echo ($config['total_articles'] == 1) ? '' : 's';?></span>
							</a>
						</div>
						<?php endif; ?>
						<div class="col-md-3">
							<a href="pages.php">
								<h5 class="font-weight-semibold mb-0 text-dark"><?php echo pm_number_format($config['total_pages']); ?></h5>
								<span class="text-muted font-size-sm text-uppercase font-weight-semibold font-size-xs">Page<?php echo ($config['total_pages'] == 1) ? '' : 's';?></span>
							</a>
						</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-6">
		<div class="card">
			<div class="card-header header-elements-inline">
				<h6 class="card-title font-weight-semibold">PHP Melody News</h6>

				<div class="header-elements">
					<a href="https://www.phpsugar.com/blog/" target="_blank" class="text-dark opacity-50"><i class="mi-open-in-new"></i></a>
				</div>
			</div>

			<ul class="media-list media-list-bordered">
				<?php echo cache_this('get_rss_news', 'home_news'); ?>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xl-6">
		<div class="card">
			<div class="card-header header-elements-inline">
				<h6 class="card-title font-weight-semibold">Latest Videos</h6>

				<div class="header-elements">
					<a href="videos.php" data-popup="tooltip" data-original-title="Manage all videos" class="text-dark opacity-50 badge badge-flat text-grey-600">manage</a>
				</div>
			</div>
			<ul class="media-list media-list-bordered media-scroll">

					<?php 
					$latest_videos = array();
					if ($crps > 0)
					{
						$sql_limit = ($crps > 5) ? 5 : $crps; 
						$sql = "SELECT r.added, r.reason, v.uniq_id, v.video_title, v.yt_id, v.yt_thumb, v.source_id  
								FROM pm_reports r
								JOIN pm_videos v
								  ON (r.entry_id = v.uniq_id) 
								WHERE r.r_type = '1' 
								ORDER BY r.id DESC 
								LIMIT 0, $sql_limit";
						if ($result = mysql_query($sql))
						{
							while ($row = mysql_fetch_assoc($result))
							{
								$i = (int) $row['added'];
								while (array_key_exists($i, $latest_videos))
								{
									$i++;
								}
								$latest_videos[$i] = $row;
								$latest_videos[$i]['thumb_url'] = show_thumb($row['uniq_id'], 1, $row);
								$latest_videos[$i]['_type'] = 'flagged';
							}
							mysql_free_result($result);
						}
					}
					if ($vapprv > 0 && pm_count($latest_videos) < $widget_items_limit)
					{
						$sql_limit = ($vapprv > 5) ? 5 : $vapprv;
						$sql = "SELECT * 
								FROM pm_temp 
								ORDER BY id DESC 
								LIMIT 0, $sql_limit";
						if ($result = mysql_query($sql))
						{
							while ($row = mysql_fetch_assoc($result))
							{
								$i = (int) $row['added'];
								while (array_key_exists($i, $latest_videos))
								{
									$i++;
								}
								$latest_videos[$i] = $row;
								$latest_videos[$i]['thumb_url'] = $row['thumbnail'];
								$latest_videos[$i]['_type'] = 'pending';
							}
							mysql_free_result($result);
						}
					}
					
					if (($sql_limit = pm_count($latest_videos)) < $widget_items_limit)
					{
						$sql_limit = $widget_items_limit - $sql_limit;
						$sql = "SELECT uniq_id, video_title, yt_id, yt_thumb, added, source_id 
								FROM pm_videos 
								WHERE added < $time_now 
								ORDER BY added DESC
								LIMIT 0, $sql_limit";
						if ($result = mysql_query($sql))
						{
							while ($row = mysql_fetch_assoc($result))
							{
								$i = (int) $row['added'];
								while (array_key_exists($i, $latest_videos))
								{
									$i++;
								}
								$latest_videos[$i] = $row;
								$latest_videos[$i]['thumb_url'] = show_thumb($row['uniq_id'], 1, $row);
								$latest_videos[$i]['_type'] = false;
							}
							mysql_free_result($result);
						}
					}
					
					if (pm_count($latest_videos) > 0)
					{
						foreach ($latest_videos as $time => $video)
						{
							if ($video['_type'] == 'pending') : 
								$video_link = 'edit-pending-video.php?id='. $video['id'];
							else :
								$video_link = _URL .'/watch.php?vid='. $video['uniq_id'];
							endif;
							?>
							<li class="media pt-2 pb-2">
								<div class="mr-3">
									<a href="<?php echo $video_link;?>" target="_blank"><img src="<?php echo (strpos($video['thumb_url'], 'http') === 0 || strpos($video['thumb_url'], '//') === 0) ? make_url_https($video['thumb_url']) : _THUMBS_DIR . $video['thumb_url']; ?>" width="60" /></a>
								</div>
								<div class="media-body">
									<span><a href="<?php echo $video_link;?>" target="_blank" class="item-url" title="<?php echo $video_title;?>"><?php echo $video['video_title']; ?></a></span>
									<small class="text-muted d-block"><?php echo date('M d', $video['added']); ?></small>


								<?php if ($video['_type'] == 'pending') : ?>
								<div class=""><a href="pending-videos.php" data-popup="tooltip" title="Manage videos pending approval"><span class="badge badge-warning rounded">Waiting Approval</span></a></div>
								<?php elseif ($video['_type'] == 'flagged') : ?>
								<div class=""><a href="reported-videos.php" data-popup="tooltip" title="Manage reported videos"><span class="badge badge-danger rounded">Reported</span></a></div>
								<?php endif; ?>

								</div><!--.media-body-->


								<?php if ($video['_type'] == 'pending') : ?>
								<div class="ml-2"><a href="edit-pending-video.php?id=<?php echo $video['id']; ?>" class="badge badge-flat border-primary text-primary-600" data-popup="tooltip" title="Edit video">Edit</a></div>
								<?php elseif ($video['_type'] == 'flagged') : ?>
								<div class="ml-2"><a href="edit-video.php?vid=<?php echo $video['uniq_id']; ?>" class="badge badge-flat border-primary text-primary-600" data-popup="tooltip" title="Edit video">Edit</a></div>
								<?php else : ?>
								<div class="ml-2"><a href="edit-video.php?vid=<?php echo $video['uniq_id']; ?>" class="badge badge-flat border-primary text-primary-600" data-popup="tooltip" title="Edit video">Edit</a></div>
								<?php endif; ?>
							</li>
						<?php
						}
					}
					else
					{
						?>
						<li class="media pt-2 pb-2">
							<div class="media-body text-center">
							<p class="mt-1 mb-2">No videos, yet. Add your first video</p>
							<a href="#addVideo" data-toggle="modal" class="btn btn-sm bg-success">Add Video</a>
							</div>
						</li>
						<?php
					}
					?>
			</ul>
		</div>
	</div><!--.col-6-->

	<div class="col-xl-6">
		<div class="card">
			<div class="card-header header-elements-inline">
				<h6 class="card-title font-weight-semibold">Newest Users</h6>

				<div class="header-elements">
				<a href="users.php" data-popup="tooltip" data-original-title="Manage all members" class="text-dark opacity-50 badge badge-flat text-grey-600">manage</a>
				</div>
			</div>
			<ul class="media-list media-list-bordered media-scroll">
					<?php 
					$sql = "SELECT id, username, name, country, reg_date, avatar  
							FROM pm_users 
							ORDER BY id DESC
							LIMIT 0, $widget_items_limit";
					if ($result = mysql_query($sql))
					{
						while ($row = mysql_fetch_assoc($result))
						{
							?>
							<li class="media pt-2 pb-2">
								<div class="mr-3">
									<img src="<?php echo get_avatar_url($row['avatar']);?>" class="rounded-circle" width="45" height="45" />
								</div>
								<div class="media-body">
									<span><a href="<?php echo get_profile_url($row); ?>" target="_blank" class="item-user-url"><?php echo $row['username']; ?></a> from <?php echo countryid2name($row['country']); ?></span>
									<small class="text-muted d-block"><?php echo date('M d', $row['reg_date']); ?></small>
								</div><!--.media-body-->

								<div class="ml-2"><a href="edit-user.php?uid=<?php echo $row['id']; ?>" class="badge badge-flat border-primary text-primary-600" data-popup="tooltip" title="Edit user profile">Edit</a></div>
							</li>
							<?php
						}
						mysql_free_result($result);
					}
					?>
			</ul>
		</div>
	</div><!--.col-6-->
</div>
<div class="row">
	<?php if ($config['comment_system'] == 'on') : ?>
	<div class="col-xl-6">
		<div class="card">
			<div class="card-header header-elements-inline">
				<h6 class="card-title font-weight-semibold">Newest Comments</h6>

				<div class="header-elements">
					<a href="comments.php" data-popup="tooltip" data-original-title="Manage all comments" class="text-dark opacity-50 badge badge-flat text-grey-600">manage</a>
				</div>
			</div>

			<ul class="media-list media-list-bordered media-scroll">
					<?php if ($comments_count > 0) : ?>
					<?php
					$sql = "SELECT c.id, c.username, c.comment, c.approved, c.added, c.user_id, v.uniq_id, v.video_title 
							FROM pm_comments c
							LEFT JOIN pm_videos v 
							  ON (c.uniq_id = v.uniq_id)
							ORDER BY id DESC
							LIMIT 0, $widget_items_limit";

					$user_ids = array();

					if ($result = mysql_query($sql))
					{
						while ($row = mysql_fetch_assoc($result))
						{
							if ($row['uniq_id'] !== null) 
							{
								$user_ids[] = $row['user_id'];
								$comments_data[] = $row;
							}
						}
						mysql_free_result($result);
						
						if (pm_count($user_ids) > 0)
						{					
							$sql = "SELECT id, avatar, username 
									FROM pm_users 
									WHERE id IN (". implode(',', $user_ids) .")";
							$result = mysql_query($sql);
							while ($row = mysql_fetch_assoc($result))
							{
								$commenters[$row['id']] = $row['avatar'];
							}
							mysql_free_result($result);
							
							foreach ($comments_data as $k => $c)
							{
								$comment_excerpt = strip_tags($c['comment']);
								$comment_excerpt = fewchars($comment_excerpt, 70);
								if ($c['user_id'] != 0)
								{
									$profile_url = get_profile_url($c);
								}
								else 
								{
									$profile_url = '#';
								}
								?>

							<li class="media pt-2 pb-2">
								<div class="mr-3">
									<img src="<?php echo get_avatar_url($commenters[$c['user_id']]); ?>" class="rounded-circle" width="45" height="45" />
								</div>
								<div class="media-body">
									<span><a href="<?php echo $profile_url; ?>"  target="_blank" class="item-user-url"><?php echo $c['username']; ?></a> commented on <a href="<?php echo _URL .'/watch.php?vid='. $c['uniq_id']; ?>" target="_blank" class="item-url" title="<?php echo $c['video_title']; ?>"><?php echo $c['video_title']; ?></a></span>
									<p class="font-size-sm pt-0 pb-0 mb-0"><?php echo $comment_excerpt;?></p>
									<small class="text-muted d-block"><?php echo date('M d', $c['added']); ?></small>
								</div><!--.media-body-->

								<?php if ($c['approved'] == 0) : ?>
									<div class="ml-2"><a href="comments.php?filter=pending" data-popup="tooltip" title="Manage comments pending approval"><span class="badge badge-warning rounded">Waiting Approval</span></a></div>
								<?php endif; ?>
							</li>
								<?php
							}
						}
						else
						{
							?>
							<li class="media pt-2 pb-2">
								<div class="media-body text-center">
								<p class="mt-1 mb-2">No new comments have been posted recently.</p>
								</div>
							</li>
							<?php
						}
					}
					?>
					<?php else : ?>
					<li class="media pt-2 pb-2">
						<div class="media-body text-center">
						<p class="mt-1 mb-2">No new comments have been posted recently.</p>
						</div>
					</li>
					<?php endif; ?>
			</ul>
		</div>
	</div><!--.col-6-->
	<?php endif; ?>

	<?php if (_MOD_ARTICLE) : ?>
	<div class="col-xl-6" pb-mi-arrow-forward>
		<div class="card">
			<div class="card-header header-elements-inline">
				<h6 class="card-title font-weight-semibold">Latest Articles</h6>

				<div class="header-elements">
					<a href="articles.php" data-popup="tooltip" data-original-title="Manage all articles" class="text-dark opacity-50 badge badge-flat text-grey-600">manage</a>
				</div>
			</div>

			<ul class="media-list media-list-bordered media-scroll">



					<?php if ($config['total_articles'] > 0) : ?>
					<?php
					$articles = list_articles('', '', 0 , $widget_items_limit, 'public'); 
					
					foreach ($articles as $k => $article) :
					?>
					<li class="media">
						<div class="media-body">
							<span><a href="<?php echo _URL.'/article-read.php?a='. $article['id']; if ($article['status'] == 0 || $article['date'] > $time_now) echo '&mode=preview'; ?>" target="_blank" class="item-user-url" title="Read: <?php echo $article['title']; ?>"><?php echo $article['title']; ?></a>
							in 
							<?php
							$str = '';
							foreach ($article['category_as_arr'] as $id => $name)
							{
								if ($id != '' && $name != '')
								{
									$str .= '<a href="articles.php?filter=category&fv='. $id .'" title="List articles from '. $name .' only">'. $name .'</a>, ';
								}
								
								if ($id == 0)
								{
									$name = 'Uncategorized';
									$str .= '<a href="articles.php?filter=category&fv='. $id .'" title="List articles from '. $name .' only">'. $name .'</a>, ';
								}
							}
							echo substr($str, 0, -2);
							?>
							</span>
							<small class="text-muted d-block"><?php echo date('M d', $article['date']); ?></small>
						</div><!--.media-body-->
						<div class="ml-2"><a href="edit-article.php?do=edit&id=<?php echo $article['id'];?>" class="badge badge-flat border-primary text-primary-600" data-popup="tooltip" title="Edit article">Edit</a></div>
					</li>
					<?php endforeach; ?> 
					<?php else : ?>
					<li class="media">
						<div class="media-body text-center">
						<p class="mt-1 mb-2">No articles, found. Write your first article today.</p>
						<a href="edit-article.php?do=new" data-toggle="modal" class="btn btn-sm bg-success">Post an article</a>
						</div>
					</li>
					<?php endif; ?>
			</ul>
		</div>
	</div><!--.col-6-->
	<?php endif; ?>
</div>


<div class="ml-lg-auto text-right pt-2 pb-2 text-muted font-size-xs">
Powered by <a href="http://www.phpsugar.com/phpmelody.html" target="_blank" class="text-muted">PHP Melody v<?php echo _PM_VERSION; ?></a> <?php if (version_compare($official_version, $config['version']) == 1) : ?> (<em><a href="https://www.phpsugar.com/customer/" target="_blank" class="text-success">Newer version available</a>!</em>) <?php endif; ?><br />
<a href="#feedback" data-toggle="modal" class="text-muted">Help &amp; Feedback</a> / <a href="http://www.phpsugar.com/support.html" target="_blank" class="text-muted">Customer Care</a> / <a href="https://www.phpsugar.com/forum/" target="_blank" class="text-muted">Support Forums</a>
</div>

</div>
<!-- /content area -->
<?php
include('footer.php');