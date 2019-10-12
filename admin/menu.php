<?php
$parts = explode('/', $_SERVER['SCRIPT_NAME']);
$submenu = array_pop($parts);
$submenu = str_replace('.php', '', $submenu);


if($submenu == 'categories' || $submenu == 'edit-category') {
	if(empty($_GET['type'])) 
		$_GET['type'] = 'video';
	
	$submenu = $submenu .'-'. $_GET['type'];
}

switch ($submenu)
{
	default:
	case 'index':
		
		$menu = 'index';
	
	break;
	
	case 'videos':
	case 'edit-video':
	case 'add-video':
	case 'embed-video':
	case 'add-video-stream':
	case 'import':
	case 'import-user':
	case 'import-csv':
	case 'reported-videos':
	case 'pending-videos':
	case 'edit-pending-video':
	case 'categories-video':
	case 'edit-category-video':
		
		$menu = 'videos';

		$submenu = ($submenu == 'edit-pending-video') ? 'pending-videos' : $submenu;
		$submenu = ($submenu == 'edit-video') ? 'videos' : $submenu; 
		$submenu = ($_GET['type'] == 'video') ? 'video_categories' : $submenu;
		$submenu = ($_GET['mode'] == 'upload') ? 'add-video-upload' : $submenu;
		$submenu = (!empty($_GET['filename'])) ? 'add-video-upload' : $submenu;


	break;

	case 'series':
	case 'edit-series':
	case 'episodes':
	case 'edit-episode':
	case 'categories-genre':
	case 'edit-category-genre':

		$menu = 'series';
		$submenu = ($_GET['do'] == 'new') ? 'series' : $submenu; 
		$submenu = ($_GET['type'] == 'genre') ? 'genre_categories' : $submenu;

		
	break;

	
	case 'articles':
	case 'edit-article':
	case 'categories-article':
	case 'edit-category-article':
		
		$menu = 'articles';
		$submenu = ($_GET['do'] == 'edit') ? 'articles' : $submenu;
		$submenu = ($_GET['type'] == 'article') ? 'article_categories' : $submenu;

	break;

	
	case 'comments':
	case 'blacklist':
		
		$menu = 'comments';
		
		if ($submenu == 'blacklist')
		{
			break;
		}
		
		switch ($_GET['filter'])
		{
			default:
			
				$submenu = '';
			
			break;
			
			case 'videos':
				
				$submenu = 'comments-video';
				
			break;
			
			case 'articles':

				$submenu = 'comments-article';
	
			break;
			
			case 'flagged':
				
				$submenu = 'comments-flagged';
					
			break;
			
			case 'pending':
				
				$submenu = 'comments-pending';
			
			break;
		}
		
		if ($menu == 'blacklist')
		{
			$submenu = 'blacklist';
		}
		
	break;
	
	case 'pages':
	case 'edit-page':
		
		$menu = 'pages';
		
		$submenu = ($_GET['do'] == 'edit') ? 'pages' : $submenu;
		
	break;
	
	case 'users':
	case 'add-user':
	case 'banned-users':
	case 'activity-stream':
	case 'export-users':
	case 'edit-user':
		
		$menu = 'users';
	
	break;
	
	case 'banner-ads':
	case 'player-static-ads':
	case 'player-video-ads':
	case 'ad-report':
		
		$menu = 'ads';
		
	break;
	
	case 'statistics':
	case 'search-log':
	case 'log':
	case 'system-info':
		
		$menu = 'stats';
		
	break;

	case 'settings':
	case 'theme-settings':
	case 'backup-database':
	case 'sitemap':
	case 'video-sitemap':
		
		$menu = 'settings';

		if ($submenu == 'sitemap' && $_GET['type'] == 'video-sitemap')
		{
			$submenu = 'video-sitemap';
		}
		
	break;
	
	case 'automated-jobs':
	case 'automated-jobs-setup':
		
		$menu = 'automated-jobs';
		
	break;
}
?>
		<!-- Main sidebar -->
		<div class="sidebar sidebar-dark sidebar-pm sidebar-main sidebar-fixed sidebar-expand-md">
			<!-- Sidebar content -->
			<div class="sidebar-content">
				<!-- Main navigation -->
				<div class="card card-sidebar-mobile">
					<ul class="nav nav-sidebar nav-sidebar-main flex-nowrap" data-nav-type="accordion">
						<!-- Main -->
						<li class="nav-item nav-item-sitename">
								<a href="#" class="nav-link sidebar-control sidebar-main-toggle">
								<i class="icon-paragraph-justify3"></i>
								<span class="nav-site-name">
									<?php echo ($config['homepage_title'] == '') ? 'PHP Melody' : htmlspecialchars($config['homepage_title']); ?>
								</span>

								<a href="<?php echo _URL; ?>" class="open-frontend" target="_blank" data-popup="tooltip" data-original-title="Switch to Front-End"><i class="mi-open-in-new"></i></a>
								</a>
							</a>
						</li>

						<li class="nav-item">
							<a href="index.php" class="nav-link <?php echo ($menu == 'index') ? 'active' : ''; ?>">
								<i class="icon-home4"></i>
								<span>
									Dashboard
								</span>
							</a>
						</li>

						<?php if ( is_admin() || (is_moderator() && $mod_can['manage_videos'])) : ?>
						<li class="nav-item nav-item-submenu nav-link-parent <?php echo ($menu == 'videos') ? 'nav-item-open' : ''; ?>">

						<a href="videos.php" onClick="parent.location='videos.php'" class="nav-link d-sm-none d-md-flex d-lg-flex <?php echo ($menu == 'videos') ? 'active' : ''; ?>"><i class="icon-play3"></i> <span>Videos</span> <?php if($tab_video_total > 0) {?><span class="pm-menu-count badge badge-primary opacity-90 ml-auto"><?php echo pm_number_format($tab_video_total); ?></span><?php } ?></a>
						<a href="#" class="nav-link d-sm-flex d-md-none d-lg-none <?php echo ($menu == 'videos') ? 'active' : ''; ?>"><i class="icon-play3"></i> <span>Videos</span> <?php if($tab_video_total > 0) {?><span class="pm-menu-count badge badge-primary opacity-90 ml-auto"><?php echo pm_number_format($tab_video_total); ?></span><?php } ?></a><!--//Mobile-->

							<ul class="nav nav-group-sub" data-submenu-title="Videos">
								<li class="nav-item d-sm-flex d-md-none d-lg-none"><a href="videos.php" class="nav-link">Manage Videos</a></li> <!--//Mobile-->
								<li class="nav-item nav-item-submenu <?php echo ($submenu == 'add-video' || $submenu == 'add-video-upload' || $submenu == 'add-video-stream' || $submenu == 'embed-video') ? 'nav-item-open' : ''?>">
									<a href="#" class="nav-link">Add videos</a>
									<ul class="nav nav-group-sub">
										<li class="nav-item"><a href="add-video.php" class="nav-link<?php echo ($submenu == 'add-video') ? ' active' : ''; ?>">Add video from URL</a></li>
										<li class="nav-item"><a href="add-video-stream.php" class="nav-link <?php echo ($submenu == 'add-video-stream') ? ' active' : ''; ?>">Add video stream</a></li>
										<li class="nav-item"><a href="embed-video.php" class="nav-link<?php echo ($submenu == 'embed-video') ? ' active' : ''; ?>">Embed video</a></li>
										<li class="nav-item"><a href="add-video.php?mode=upload" class="nav-link<?php echo ($submenu == 'add-video-upload') ? ' active' : ''; ?>">Upload video</a></li>
									</ul>
								</li>

								<li class="nav-item nav-item-submenu <?php echo ($submenu == 'import' || $submenu == 'import-user' || $submenu == 'import-csv') ? 'nav-item-open' : ''?>">
									<a href="#" class="nav-link">Import videos</a>
									<ul class="nav nav-group-sub">
										<li class="nav-item"><a href="import.php" class="nav-link<?php echo ($submenu == 'import') ? ' active' : ''; ?>">Import by keyword</a></li>
										<li class="nav-item"><a href="import-user.php" class="nav-link<?php echo ($submenu == 'import-user') ? ' active' : ''; ?>">Import from user</a></li>
										<li class="nav-item"><a href="import-csv.php" class="nav-link<?php echo ($submenu == 'import-csv') ? ' active' : ''; ?>">Import from CSV</a></li>
									</ul>
								</li>


								<li class="nav-item"><a href="reported-videos.php" class="nav-link<?php echo ($submenu == 'reported-videos') ? ' active' : ''; ?>">Reported videos<?php if($crps > 0) {?><span class="pm-submenu-count badge badge-warning ml-auto"><?php echo pm_number_format($crps); ?></span><?php } ?></a></li>
								<li class="nav-item"><a href="pending-videos.php" class="nav-link<?php echo ($submenu == 'pending-videos') ? ' active' : ''; ?>">Pending approval<?php if($vapprv > 0) {?><span class="pm-submenu-count badge badge-warning ml-auto"><?php echo pm_number_format($vapprv); ?></span><?php } ?></a></li>


								<li class="nav-item"><a href="categories.php?type=video" class="nav-link<?php echo ($submenu == 'video_categories') ? ' active' : ''; ?>">Video categories</a></li>

							</ul>
						</li>
						<?php endif;?>

						
						<?php if ( is_admin() || (is_moderator() && mod_can('manage_videos')) ) : ?> 
						<li class="nav-item nav-item-submenu nav-link-parent <?php echo ($menu == 'series') ? 'nav-item-open' : ''; ?>">
							<a href="series.php" onClick="parent.location='series.php'" class="nav-link d-sm-none d-md-flex d-lg-flex"><i class="icon-tv"></i> <span>Series</span></a>
							<a href="#" class="nav-link d-sm-flex d-md-none d-lg-none"><i class="icon-tv"></i> <span>Series</span></a> <!--//Mobile-->

							<ul class="nav nav-group-sub" data-submenu-title="Series">
								<li class="nav-item d-sm-flex d-md-none d-lg-none"><a href="series.php" class="nav-link">Manage Series</a></li> <!--//Mobile-->
								<li class="nav-item"><a href="episodes.php" class="nav-link<?php echo ($submenu == 'episodes') ? ' active' : ''; ?>">Episodes</a></li>
								<li class="nav-item"><a href="categories.php?type=genre" class="nav-link <?php echo ($submenu == 'genre_categories') ? ' active' : ''; ?>">Genres</a></li>
							</ul>
						</li>
						<?php endif; ?>


						<?php if ($mod_can['manage_articles'] || is_editor() || is_admin()) : ?> 
						<li class="nav-item nav-item-submenu nav-link-parent <?php echo ($menu == 'articles') ? 'nav-item-open' : ''; ?>">
							<a href="articles.php" onClick="parent.location='articles.php'" class="nav-link d-sm-none d-md-flex d-lg-flex"><i class="icon-magazine"></i> <span>Articles</span></a>
							<a href="#" class="nav-link d-sm-flex d-md-none d-lg-none"><i class="icon-magazine"></i> <span>Articles</span></a> <!--//Mobile-->

							<ul class="nav nav-group-sub" data-submenu-title="Articles">
								<li class="nav-item"><a href="articles.php" class="nav-link <?php echo ($submenu == 'articles') ? ' active' : ''; ?>">Manage articles</a></li>
								<li class="nav-item"><a href="edit-article.php?do=new" class="nav-link<?php echo ($submenu == 'edit-article') ? ' active' : ''; ?>">Post a new article</a></li>
								<li class="nav-item"><a href="categories.php?type=article" class="nav-link<?php echo ($submenu == 'article_categories') ? ' active' : ''; ?>">Article categories</a></li>
							</ul>
						</li>
						<?php endif; ?>

						<?php if ( is_admin() ) : ?>
						<li class="nav-item nav-item-submenu nav-link-parent <?php echo ($menu == 'pages') ? 'nav-item-open' : ''; ?>">
							<a href="pages.php" onClick="parent.location='pages.php'" class="nav-link d-sm-none d-md-flex d-lg-flex"><i class="icon-stack"></i> <span>Pages</span></a>
							<a href="#" class="nav-link d-sm-flex d-md-none d-lg-none"><i class="icon-stack"></i> <span>Pages</span></a> <!--//Mobile-->

							<ul class="nav nav-group-sub" data-submenu-title="Pages">
								<li class="nav-item"><a href="pages.php" class="nav-link <?php echo ($submenu == 'pages') ? ' active' : ''; ?>">Manage pages</a></li>
								<li class="nav-item"><a href="edit-page.php?do=new" class="nav-link<?php echo ($submenu == 'edit-page') ? ' active' : ''; ?>">Create new page</a></li>
							</ul>
						</li>

						<?php endif;?>

						<?php if ( is_admin() || (is_moderator() && $mod_can['manage_comments'])) : ?>
						<li class="nav-item nav-item-submenu nav-link-parent <?php echo ($menu == 'comments') ? 'nav-item-open' : ''; ?>">
							<a href="comments.php" onClick="parent.location='comments.php'" class="nav-link d-sm-none d-md-flex d-lg-flex"><i class="icon-bubble-lines4"></i> <span>Comments</span><?php if($tab_comments > 0) {?><span class="pm-menu-count badge badge-primary ml-auto"><?php echo pm_number_format($tab_comments); ?></span><?php } ?></a>
							<a href="#" class="nav-link d-sm-flex d-md-none d-lg-none"><i class="icon-bubble-lines4"></i> <span>Comments</span><?php if($tab_comments > 0) {?><span class="pm-menu-count badge badge-primary ml-auto"><?php echo pm_number_format($tab_comments); ?></span><?php } ?></a> <!--//Mobile-->
							
							<ul class="nav nav-group-sub" data-submenu-title="Comments">
								<li class="nav-item"><a href="comments.php?filter=videos" class="nav-link<?php echo ($submenu == 'comments-video') ? ' active' : ''; ?>">Video comments</a></li>
								<?php if (is_admin() || (is_moderator() && mod_can('manage_comments'))) : ?>
								<li class="nav-item"><a href="comments.php?filter=articles" class="nav-link<?php echo ($submenu == 'comments-article') ? ' active' : ''; ?>">Article comments</a></li>
								<?php endif; ?>
								<li class="nav-item"><a href="comments.php?filter=flagged" class="nav-link<?php echo ($submenu == 'comments-flagged') ? ' active' : ''; ?>">Flagged<?php if($flagged_comments > 0) {?><span class="pm-submenu-count badge badge-warning ml-auto"><?php echo pm_number_format($flagged_comments); ?></span><?php } ?></a></li>
								<li class="nav-item"><a href="comments.php?filter=pending" class="nav-link<?php echo ($submenu == 'comments-pending') ? ' active' : ''; ?>">Pending approval<?php if($pending_comments > 0) {?><span class="pm-submenu-count badge badge-warning ml-auto"><?php echo pm_number_format($pending_comments); ?></span><?php } ?></a></li>
								<li class="nav-item"><a href="blacklist.php" class="nav-link<?php echo ($submenu == 'blacklist') ? ' active' : ''; ?>">Abuse prevention</a></li>
							</ul>
						</li>
						<?php endif;?>

						<?php if ( is_admin() || (is_moderator() && $mod_can['manage_users'])) : ?>
						<li class="nav-item nav-item-submenu nav-link-parent <?php echo ($menu == 'users') ? 'nav-item-open' : ''; ?>">
							<a href="users.php" onClick="parent.location='users.php'" class="nav-link d-sm-none d-md-flex d-lg-flex"><i class="icon-users2"></i> <span>Users</span></a>
							<a href="#" class="nav-link d-sm-flex d-md-none d-lg-none"><i class="icon-users2"></i> <span>Users</span></a> <!--//Mobile-->

							<ul class="nav nav-group-sub" data-submenu-title="Users">
								<li class="nav-item d-sm-flex d-md-none d-lg-none"><a href="users.php" class="nav-link">Manage Users</a></li> <!--//Mobile-->
								<li class="nav-item"><a href="add-user.php" class="nav-link<?php echo ($submenu == 'add-user') ? ' active' : ''; ?>">Add user</a></li> 
								<li class="nav-item"><a href="banned-users.php" class="nav-link<?php echo ($submenu == 'banned-users') ? ' active' : ''; ?>">Banned</a></li>
								<?php if (_MOD_SOCIAL) : ?>
								<li class="nav-item"><a href="activity-stream.php" class="nav-link<?php echo ($submenu == 'activity-stream') ? ' active' : ''; ?>">Activity stream</a></li>
								<?php endif;?>
							</ul>
						</li>
						<?php endif;?>

						<?php if ( is_admin() ) : ?>
						<li class="nav-item nav-item-submenu nav-link-parent <?php echo ($menu == 'ads') ? 'nav-item-open' : ''; ?>">
							<a href="banner-ads.php" onClick="parent.location='banner-ads.php'" class="nav-link d-sm-none d-md-flex d-lg-flex"><i class="icon-coin-dollar"></i> <span>Ads</span></a>
							<a href="#" class="nav-link d-sm-flex d-md-none d-lg-none"><i class="icon-coin-dollar"></i> <span>Ads</span></a> <!--//Mobile-->
							<ul class="nav nav-group-sub" data-submenu-title="Advertisments">
								<li class="nav-item"><a href="banner-ads.php" class="nav-link<?php echo ($submenu == 'banner-ads') ? ' active' : ''; ?>">Classic banners</a></li>
								<li class="nav-item"><a href="player-static-ads.php" class="nav-link<?php echo ($submenu == 'player-static-ads') ? ' active' : ''; ?>">Pre-roll static ads</a></li>
								<li class="nav-item"><a href="player-video-ads.php" class="nav-link<?php echo ($submenu == 'player-video-ads') ? ' active' : ''; ?>">Pre-roll video ads</a></li>
								<li class="nav-item"><a href="ad-report.php" class="nav-link<?php echo ($submenu == 'ad-report') ? ' active' : ''; ?>">Ad reports</a></li>
							</ul>
						</li>

						<li class="nav-item nav-item-submenu nav-link-parent <?php echo ($menu == 'stats') ? 'nav-item-open' : ''; ?>">
							<a href="statistics.php" onClick="parent.location='statistics.php'" class="nav-link d-sm-none d-md-flex d-lg-flex"><i class="icon-stats-bars2"></i> <span>Statistics &amp; Logs</span><?php if($tab_internallog > 0) {?><span class="pm-menu-count badge badge-primary ml-auto"><?php echo pm_number_format($tab_internallog); ?></span><?php } ?></a>
							<a href="#" class="nav-link d-sm-flex d-md-none d-lg-none"><i class="icon-stats-bars2"></i> <span>Statistics &amp; Logs</span><?php if($tab_internallog > 0) {?><span class="pm-menu-count badge badge-primary ml-auto"><?php echo pm_number_format($tab_internallog); ?></span><?php } ?></a> <!--//Mobile-->
							<ul class="nav nav-group-sub" data-submenu-title="Statistics">
								<li class="nav-item"><a href="search-log.php" class="nav-link<?php echo ($submenu == 'search-log') ? ' active' : ''; ?>">Search log</a></li>
								<li class="nav-item"><a href="log.php" class="nav-link<?php echo ($submenu == 'log') ? ' active' : ''; ?>">System log <?php if($tab_internallog > 0) {?><span class="pm-submenu-count badge badge-warning ml-auto"><?php echo pm_number_format($tab_internallog); ?></span><?php } ?></a></li>
								<li class="nav-item"><a href="system-info.php" class="nav-link<?php echo ($submenu == 'system-info') ? ' active' : ''; ?>">PHP configuration</a></li>
							</ul>
						</li>


						<li class="nav-item nav-item-submenu nav-link-parent <?php echo ($menu == 'automated-jobs') ? 'nav-item-open' : ''; ?>">
							<a href="automated-jobs.php" onClick="parent.location='automated-jobs.php'" class="nav-link d-sm-none d-md-flex d-lg-flex"><i class="icon-make-group"></i> <span>Automated Jobs</span><?php if($tab_cron > 0) {?><span class="pm-menu-count badge badge-primary ml-auto"><?php echo pm_number_format($tab_cron); ?></span><?php } ?></a>
							<a href="#" class="nav-link d-sm-flex d-md-none d-lg-none"><i class="icon-make-group"></i> <span>Automated Jobs</span><?php if($tab_cron > 0) {?><span class="pm-menu-count badge badge-primary ml-auto"><?php echo pm_number_format($tab_cron); ?></span><?php } ?></a> <!--//Mobile-->
							<ul class="nav nav-group-sub" data-submenu-title="Automated Jobs">
								<li class="nav-item d-sm-flex d-md-none d-lg-none"><a href="automated-jobs.php" class="nav-link<?php echo ($submenu == 'automated-jobs-setup') ? ' active' : ''; ?>">Manage Jobs</a></li><!--//Mobile-->
								<li class="nav-item"><a href="automated-jobs-setup.php" class="nav-link<?php echo ($submenu == 'automated-jobs-setup') ? ' active' : ''; ?>">Setup</a></li>
							</ul>
						</li>
						<?php endif;?>
						<!-- /main -->
					</ul>



					<ul class="nav nav-sidebar nav-sidebar-footer flex-nowrap">
							<li class="nav-item">
								<a href="settings.php" class="nav-link <?php echo ($submenu == 'settings' || $submenu == 'theme-settings' || $submenu == 'sitemap' || $submenu == 'video-sitemap') ? ' active' : ''; ?>"><i class="mi-settings"></i> <span>Settings</span></a>
							</li>

							<li class="nav-item nav-item-submenu nav-item-user-menu">

								<a href="#" class="nav-link disabled nav-link-user">
									<div class="media-avatar"><img src="<?php echo _AVATARS_DIR . $userdata['avatar']; ?>" width="22" height="22" class="rounded-circle" alt=""><span class="online-status"></span></div>
									<span><?php echo ucwords($userdata['name']);?></span>
								</a>

								<div class="nav nav-group-sub">
									<div class="card-body">
										<div class="media">
											<div class="media-avatar mr-3">
												<a href="#"><img src="<?php echo _AVATARS_DIR . $userdata['avatar']; ?>" width="38" height="38" class="rounded-circle" alt=""></a>
												<span class="online-status"></span>
											</div>
											<div class="media-body">
												<div class="media-title font-weight-semibold"><a href="edit-user.php?uid=<?php echo ucwords($userdata['id']);?>" class="dropdown-item p-0"><?php echo ucwords($userdata['name']);?></a></div>
												<div class="font-size-xs opacity-50">
													<?php if (is_admin()) : ?>Administrator<?php endif; ?>
													<?php if (is_editor()) : ?>Editor<?php endif; ?>
													<?php if (is_moderator()) : ?>Moderator<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
									<div class="dropdown-divider"></div>
									<?php if ((is_moderator() && $mod_can['manage_videos']) || is_admin()) : ?>
										<a href="#addVideo" class="dropdown-item" data-toggle="modal" data-dismiss="modal">ADD MEDIA</a>
										<?php if ( $config['mod_series'] == 1 ) : ?>
										<a href="edit-episode.php?do=new" class="dropdown-item">ADD EPISODE</a>
										<?php endif; ?>
									<?php endif; ?>
									<?php if (is_admin()) : ?>
										<?php if($tab_internallog < 0) : ?>
										<a href="log.php" class="dropdown-item"><i class="icon-bug2"></i> System Log <span class="badge badge-pill bg-warning-400 ml-auto"><?php echo $tab_internallog; ?></span></a>
										<?php endif; ?>
									<?php endif; ?>
									<div class="dropdown-divider"></div>
									<a href="#feedback" data-toggle="modal" class="text-muted dropdown-item">Help &amp; Feedback</a>
									<a href="https://www.phpsugar.com/support.html" target="_blank" class="text-muted dropdown-item">Customer Care</a>
									<?php if($config['keyboard_shortcuts'] == 1) : ?>
									<a href="#seeShortcuts" data-toggle="modal" class="text-muted dropdown-item">Keyboard Shortcuts</a>
									<?php endif; ?>
									<div class="dropdown-divider"></div>
									<a href="<?php echo _URL; ?>" class="dropdown-item" target="_blank"><i class="mi-open-in-new"></i> Switch to Front-End</a>
									<a href="<?php echo _URL; ?>/login.php?do=logout" class="dropdown-item mb-2"><i class="icon-switch2"></i>Logout</a>

								</div>
							</li>
					</ul>




				</div>
				<!-- /main navigation -->

				<!-- <a href="javascript:" id="return-to-top"><i class="icon-arrow-up5"></i></a> -->

			</div>
			<!-- /sidebar content -->
		</div>
		<!-- /main sidebar -->




<?php if ($showm == '8') : ?>

		<div class="sidebar sidebar-light sidebar-secondary sidebar-expand-md sidebar-pm-settings">

			<!-- Sidebar content -->
			<div class="sidebar-content">

			<h4><span class="font-weight-semibold ml-3 mb-3"><?php echo $_page_title;?></span></h4>
			
				<!-- Sub navigation -->
				<div class="card mb-2">

					<div class="card-body p-0">
						<ul class="nav nav-sidebar" data-nav-type="accordion" id="import-nav" role="navigation">
							<li class="nav-item nav-item-submenu <?php echo ($submenu == 'settings') ? ' nav-item-open nav-item-expanded' : ''?>">
								<a href="settings.php" onClick="parent.location='settings.php'" class="nav-link <?php echo ($submenu == 'settings') ? ' active' : ' disabled'?>"><i class="mi-settings"></i> Settings</a>
								<ul class="nav nav-group-sub">
									<li class="nav-item <?php echo ($selected_tab_view == 'tabname1' || $selected_tab_view == 't1' || $selected_tab_view == '' || $selected_tab_view == 'general') ? 'active' : '';?>"><a href="#tabname1" data-toggle="tab" class="nav-link active"><i class="icon-cogs"></i> General Settings</a></li>
									<li class="nav-item <?php echo ($selected_tab_view == 't6' || $selected_tab_view == 'modules') ? 'active' : '';?>"><a href="#t6" data-toggle="tab" class="nav-link<?php echo ($selected_tab_view == 't6' || $selected_tab_view == 'modules') ? ' active' : '';?>"><i class="mi-build"></i> Modules</a></li>
									<li class="nav-item <?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'player') ? 'active' : '';?>"><a data-toggle="tab" href="#t2" class="nav-link<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'player') ? ' active' : '';?>"><i class="mi-settings-overscan"></i> Video Player</a></li>
									<li class="nav-item <?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'video') ? 'active' : '';?>"><a data-toggle="tab" href="#t3" class="nav-link<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'video') ? ' active' : '';?>"><i class="mi-settings-brightness"></i> Video Settings</a></li>
									<li class="nav-item <?php echo ($selected_tab_view == 't5' || $selected_tab_view == 'video-ads') ? 'active' : '';?>"><a data-toggle="tab" href="#t5" class="nav-link<?php echo ($selected_tab_view == 't5' || $selected_tab_view == 'video-ads') ? ' active' : '';?>"><i class="mi-ondemand-video"></i> Video Ads</a></li>
									<li class="nav-item <?php echo ($selected_tab_view == 't10' || $selected_tab_view == 'comment') ? 'active' : '';?>"><a data-toggle="tab" href="#t10" class="nav-link<?php echo ($selected_tab_view == 't10' || $selected_tab_view == 'comment') ? ' active' : '';?>"><i class="mi-comment"></i> Comments</a></li>
									<li class="nav-item <?php echo ($selected_tab_view == 't9' || $selected_tab_view == 'user') ? 'active' : '';?>"><a data-toggle="tab" href="#t9" class="nav-link<?php echo ($selected_tab_view == 't9' || $selected_tab_view == 'user') ? ' active' : '';?>"><i class="mi-person-outline"></i> Users</a></li>
									<li class="nav-item <?php echo ($selected_tab_view == 't7' || $selected_tab_view == 'email') ? 'active' : '';?>"><a data-toggle="tab" href="#t7" class="nav-link<?php echo ($selected_tab_view == 't7' || $selected_tab_view == 'email') ? ' active' : '';?>"><i class="mi-mail-outline"></i> E-mail</a></li>
								</ul>
							</li>

							<li class="nav-item nav-item-submenu <?php echo ($submenu == 'theme-settings') ? ' nav-item-open nav-item-expanded' : ''?>">
								<a href="theme-settings.php" onClick="parent.location='theme-settings.php'" class="nav-link <?php echo ($submenu == 'theme-settings') ? ' active' : ' disabled'?>"><i class="mi-format-paint"></i> Layout Settings</a>
								<ul class="nav nav-group-sub">
									<li class="nav-item <?php echo ($selected_tab_view == 't0' || $selected_tab_view == '' || $selected_tab_view == 't1' || $selected_tab_view == 'general') ? 'active' : '';?>"><a href="#t0" data-toggle="tab" class="nav-link active">General Settings</a></li>
									<li class="nav-item <?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'customize') ? 'active' : '';?>"><a href="#t2" data-toggle="tab" class="nav-link<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'customize') ? ' active' : '';?>">Customize Theme</a></li>
									<li class="nav-item <?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'store') ? 'active' : '';?>"><a data-toggle="tab" href="#t3" class="nav-link<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'store') ? ' active' : '';?>">Theme Store</a></li>
								</ul>
							</li>

							<li class="nav-item nav-item-submenu <?php echo ($submenu == 'sitemap' || $submenu == 'video-sitemap') ? ' nav-item-open nav-item-expanded' : ''?>">
								<a href="#" class="nav-link <?php echo ($submenu == 'sitemap' || $submenu == 'video-sitemap') ? ' active' : ''?>"><i class="icon-tree6"></i> Sitemaps</a>
								<ul class="nav nav-group-sub">
									<li class="nav-item"><a href="sitemap.php?type=sitemap" class="nav-link<?php echo ($submenu == 'sitemap') ? ' active' : ''; ?>">Create Regular Sitemap</a></li>
									<li class="nav-item"><a href="sitemap.php?type=video-sitemap" class="nav-link<?php echo ($submenu == 'video-sitemap') ? ' active' : ''; ?>">Create Video Sitemap</a></li>
								</ul>
							</li>
							<li class="nav-item-divider"></li>
							<li class="nav-item"><a href="<?php echo csrfguard_url(_URL .'/'. _ADMIN_FOLDER .'/backup-database.php?restart=1', '_admin_backupdb');?>" class="nav-link pm-show-loader <?php echo ($submenu == 'backup-database') ? ' active' : ''; ?>"><i class="mi-settings-backup-restore"></i> Backup Database</a></li>
						</ul>
					</div>
				</div>
				<!-- /sub navigation -->


			</div>
			<!-- /sidebar content -->

		</div>
	
<?php 
endif; 

unset($parts, $menu, $submenu);