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

$showm = '2';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_ibutton = 1;
$load_prettypop = 1;
$load_import_js = 1;
$load_googlesuggests = 1;
$load_lazy_load = 1;
$load_jquery_ui = 1;

$_page_title = 'Import Videos from User';
include('header.php');
include_once(ABSPATH . 'include/cron_functions.php');

$action = trim($_GET['action']);
$page = (empty($_GET['page'])) ? 1 : (int) $_GET['page'];

$post_n_get = 0;
$post_n_get = pm_count($_POST) + pm_count($_GET);
$curl_error = '';
$sources = a_fetch_video_sources();

$subscription_id = (int) $_GET['sub_id'];

$data_source = 'youtube';

if (in_array($_COOKIE['aa_import_from'], array('youtube', 'youtube-channel', 'dailymotion', 'vimeo')))
{
	$data_source = $_COOKIE['aa_import_from'];
}

if ($_GET['data_source'] != '' || $_POST['data_source'] != '')
{
	$data_source = ($_GET['data_source'] != '') ? $_GET['data_source'] : $_POST['data_source'];
}

if ($_POST['username'] != '' && ! $subscription_id)
{
	$_POST['username'] = trim($_POST['username']);
	
	$sql = "SELECT sub_id, data
			FROM pm_import_subscriptions 
			WHERE sub_name = '". secure_sql($_POST['username']) ."'
			  AND sub_type = 'user'";
	if ( $result = @mysql_query($sql))
	{
		$row = mysql_fetch_assoc($result);
		$row['data'] = unserialize($row['data']);
		
		if ($row['data']['data_source'] == $data_source)
		{
			$subscription_id = (int) $row['sub_id'];
		}
		mysql_free_result($result);
		unset($row, $result);
	}
}

$cron_jobs_nonce = csrfguard_raw('_admin_cron_jobs_form_import-user');

?>
<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title; ?></span></h4>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
			<div class="d-horizontal-scroll">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="videos.php" class="breadcrumb-item">Videos</a>
					<a href="import.php" class="breadcrumb-item">Import Videos</a>
					<span class="breadcrumb-item active"></span>
					<a href="#" class="breadcrumb-elements-item dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo $_page_title; ?></a>
					<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(282px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
						<a href="import.php" class="dropdown-item"> Import by Keyword</a>
						<a href="import-user.php" class="dropdown-item"> Import from User</a>
						<a href="import-csv.php" class="dropdown-item"> Import from CSV</a>
					</div>
				</div>
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
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">Filtering</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>This page allows you import videos from a particular channel/user from the following websites: YouTube.com, DailyMotion.com and Vimeo.com. <br />
						Enter the desired username below and start importing.</p>
						<p>The results will also include any available playlists and favorites belonging to the user.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
						<p>Each result is organized in a stack containing thumbnails, the video title, category, description and tags. Data such as video duration, original URL and more will be imported automatically.</p>
						
						<p>Youtube provides three thumbnails for each video and PHP MELODY allows you to choose the best one for your site. By default, the chosen thumbnail is the largest one, but changing it will be represented by a blue border.
						You can also do a quality control by using the video preview. Just click the play button overlaying the large thumbnail image and the video will be loaded in a window.</p>
						
						<p>By default none of the results is selected for import. Clicking on the top right switch from each stack will select it for importing. This is indicated by a green highlight of the stack. If you’re satisfied with all the results and wish to import them all at once, you can do that as well by selecting the &quot;SELECT ALL VIDEOS&quot; checkbox (bottom left).</p>
						<p>Enjoy!</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content">
	
		<?php if ( empty($config['youtube_api_key']) ) : ?>
			<div class="alert alert-warning alert-styled-left">
				<strong>Before importing videos from YouTube.com...</strong> 
				<p>To import videos from YouTube.com, an API key is required. <strong><a href="http://help.phpmelody.com/how-to-create-a-youtube-api-key/" target="_blank">Watch the video</a></strong> and see how to create your API key.</p>
				<p>Enter your API key in the <strong><a href="settings.php?highlight=youtube_api_key&view=video">Settings > Video Settings</a></strong> page (under "<em>Youtube Public API Key</em>" ).</p>
			</div>
		<?php endif; ?>	
		<?php 
	
		echo $info_msg;
		
		load_categories();
		if (pm_count($_video_categories) == 0)
		{
			echo pm_alert_error('Please <a href="edit-category.php?do=add&type=video">create a category</a> first.');
		}
	
		// if (empty($_GET['action']))
		// {
		// 	echo pm_alert_info('Import playlists, favorites and videos from any YouTube, DailyMotion or Vimeo user.<br /> <small>Please note that <strong>private</strong> playlists will appear as being empty.</small>');
		// }
		?>

		<!-- Search form -->
		<div class="card">
			<div class="card-body">
				<h5 class="mb-3">Username</h5>
					<div class="d-block">
					<form name="import-user-search-form" id="import-user-search-form" action="" method="post" class="">					
					<div class="input-group mb-3">
						<div class="form-group-feedback form-group-feedback-left">
							<input name="username" type="text" class="form-control form-control-lg alpha-grey gautocomplete" value="<?php if($_POST['username'] != '') echo $_POST['username']; elseif($_GET['username'] != '') echo $_GET['username']; else echo '';?>" placeholder="Enter username or Channel ID" autocomplete="yt-username" />
							<div class="form-control-feedback form-control-feedback-lg">
								<i class="icon-search4 text-muted"></i>
							</div>
						</div>
						<div class="input-group-append">
							<select name="data_source" class="form-field alpha-grey custom-select custom-select-lg">
								<option value="youtube" <?php echo ($data_source == 'youtube' || empty($data_source)) ? 'selected="selected"' : ''; ?>>Youtube User</option>
								<option value="youtube-channel" <?php echo ($data_source == 'youtube-channel') ? 'selected="selected"' : ''; ?>>Youtube Channel</option>
								<option value="dailymotion" <?php echo ($data_source == 'dailymotion') ? 'selected="selected"' : ''; ?>>Dailymotion User</option>
								<option value="vimeo" <?php echo ($data_source == 'vimeo') ? 'selected="selected"' : ''; ?>>Vimeo User</option>
							</select>
						</div>
						<div class="input-group-append">
							<button type="submit" name="submit" class="btn btn-primary btn-lg" id="search-user-btn">Search</button>
						</div>
					</div>

					<input type="hidden" name="results" value="50" />
					</div>

					<div class="d-md-flex align-items-md-center flex-md-wrap text-center text-md-left">
						<ul class="list-inline mb-0 ml-md-auto">
							<li class="list-inline-item"><a href="#" class="btn btn-link text-default" data-toggle="button" id="import-options" aria-pressed="false" autocomplete="off"><i class="icon-menu7 mr-2"></i> Advanced search</a></li>
						</ul>
					</div>

					<div class="d-md-block">
						<div id="import-opt-content">
							<div class="row">
								<div class="col-md-12">
									<hr />
									<h6>Autocomplete Results</h6>

									<!--
									<input type="checkbox" name="autofilling" id="autofilling" value="1" <?php if($_POST['autofilling'] == "1" || $_GET['autofilling'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> />
									<label for="autofilling">Auto-populate the video title</label>-->
									<label>Autocomplete results with this category</label>
									<?php 
									$selected_categories = array();
									if (is_array($_POST['use_this_category']))
									{
										$selected_categories = $_POST['use_this_category'];
									}
									else if (is_string($_POST['use_this_category']) && $_POST['use_this_category'] != '') 
									{
										$selected_categories = (array) explode(',', $_POST['use_this_category']);
									}
									if ($_GET['utc'] != '')
									{
										$selected_categories = (array) explode(',', $_GET['utc']);
									}
								
									$categories_dropdown_options = array(
																	'attr_name' => 'use_this_category[]',
																	'attr_id' => 'main_select_category',
																	'select_all_option' => false,
																	'spacer' => '&mdash;',
																	'selected' => $selected_categories,
																	'other_attr' => 'multiple="multiple" size="3" data-placeholder="Import videos into..."',
																	'option_attr_id' => 'check_ignore'
																	);
									echo categories_dropdown($categories_dropdown_options);
									?>
									<br>
									<label for="autodata">
									<input type="checkbox" name="autodata" id="autodata" value="1" <?php if($_POST['autodata'] == "1" || $_GET['autodata'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> />
									Autocomplete all other data from API</label> <i class="mi-info-outline" rel="tooltip" title="Retrieve and include the video description, tags or any other information the API may provide."></i>
								</div>
							</div><!-- .row --> 
						</div><!-- #import-opt-content -->
					</div>
					</form>
			</div>
		</div>

		<div class="card hide" id="import-ui-control">
			<div class="row">
				<div class="col-md-8">
					<div class="nav-tabs-responsive import-user-nav shadow-0 hide d-horizontal-scroll" id="import-nav">
						<ul class="nav nav-tabs nav-tabs-bottom border-bottom-0 flex-nowrap mb-0">
							<li class="nav-item"><a href="#" id="import-user-nav-latest-uploads" class="nav-link active"><i class="mi-new-releases mr-2"></i> Latest Uploads</a></li>
							<li class="nav-item"><a href="#" id="import-user-nav-playlists" class="nav-link"><i class="mi-playlist-play mr-2"></i> Playlists</a></li>
							<li class="nav-item"><a href="#" id="import-user-nav-favorites" class="nav-link"><i class="mi-favorite-border mr-2"></i> Favorites</a></li>
						</ul>
					</div>
				</div>
				<div class="col-md-4">
					<div class="float-right mt-1 mr-1 mb-1">
						<a href="#modal_subscribe" data-toggle="modal" class="btn btn-sm btn-primary" rel="tooltip" title="Save this search for quick access and auto-importing." id="btn-subscribe"><i class="mi-save"></i> Save this search</a>
						<a href="#unsubscribe" data-subscription-id="0" class="btn btn-sm bg-orange-600" id="btn-unsubscribe" title="Click to Unsubscribe" data-popup="tooltip" data-original-title="Click to Unsubscribe"><i class="mi-star"></i> Subscribed</a>
					</div>
				</div>
			</div>
		</div>

		<form name="import-search-results-form" id="import-search-results-form" action="" method="post">
			<div id="vs-grid">
				<span id="import-content-placeholder" class="row">
					<?php
					if (empty($_GET['action'])) 
					{
						$subscriptions = get_import_subscriptions('user');
						if ($subscriptions['total_results'] > 0)
						{
							$subscriptions_count = $subscriptions['total_results'];
							$subscriptions = $subscriptions['data'];
							
							foreach ($subscriptions as $k => $sub)
							{
								$subscriptions[$k] = unserialize($sub['data']);
								$subscriptions[$k]['sub_id'] = $sub['sub_id'];
								$subscriptions[$k]['sub_name'] = $sub['sub_name'];
								$subscriptions[$k]['last_query_time'] = (int) $sub['last_query_time'];
								$subscriptions[$k]['last_query_results'] = (int) $sub['last_query_results'];
								$subscriptions[$k]['sub_user_id'] = $sub['user_id'];
								$subscriptions[$k]['sub_username'] = $sub['username'];	
								$subscriptions[$k]['page'] = 1;
								$subscriptions[$k]['action'] = 'search'; // @since v2.3.1
								unset($subscriptions[$k]['playlistid']);  // @since v2.3.1
							}
						?>						
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title font-weight-semibold">Subscriptions</h5>
								</div>
								<div class="card-body-">
									<div class="table-responsive">
										<table class="table table-striped table-columned pm-tables">
											<thead>
												<th width="5"></th>
												<th width="50"></th>
												<th class="text-left">Username</th>
												<th width="110" class="text-center d-none d-md-table-cell">Saved by</th>
												<th width="220" class="text-center d-none d-md-table-cell">Videos added this week</th>
												<th width="260" class="text-center" style="min-width:200px; width: 260px;">Action</th>
											</thead>
											<tbody>
												<?php foreach ($subscriptions as $k => $sub) : ?>
												<tr id="row-subscription-<?php echo $sub['sub_id']; ?>">
													<td>
														<div class="sprite <?php echo ( ! empty($sub['data_source'])) ? strtolower($sub['data_source']) : 'youtube'; ?>" rel="tooltip" title="Source: <?php echo ( ! empty($sub['data_source'])) ? ucfirst($sub['data_source']) : 'Youtube'; ?>"></div>
													</td>
													<td>
														<?php if ($sub['profile_avatar_url'] != '') : ?>
														<img src="<?php echo $sub['profile_avatar_url'];?>" width="36" height="36" />
														<?php endif; ?>
													</td>
													<td>
														<?php 
														$url_params = $sub;
														unset($url_params['profile_avatar_url'], $url_params['sub_name'], $url_params['last_query_time'], $url_params['last_query_results'], $url_params['sub_user_id'], $url_params['sub_username'], $url_params['title']);
														?>
														<strong><a href="import-user.php?<?php echo http_build_query($url_params);?>" class="row-user-subscription-link" data-sub-id="<?php echo $sub['sub_id']; ?>" data-query="<?php echo http_build_query($url_params);?>"><?php echo $sub['sub_name'];?></a></strong>
													</td>
													<td align="center" class="text-center d-none d-md-table-cell">
														<a href="<?php echo get_profile_url($sub);?>" target="_blank"><?php echo $sub['sub_username'];?></a>
													</td>
													<td align="center" class="text-center d-none d-md-table-cell">
														<?php if (import_subscription_cache_fresh($sub['last_query_time'])) :
															echo ($sub['last_query_results'] > 0) ? number_format($sub['last_query_results']) : '0';
														else : ?>
														<span class="row-subscription-get-results" data-subscription-id="<?php echo $sub['sub_id']; ?>">
															<img src="img/ico-loading.gif" class="row-subscription-loading-gif" width="16" height="16" />
														</span>
														<?php endif; ?>
													</td>
													<td align="center">
														<?php 
														$sub_is_scheduled = false; 
														show_cron_schedule_button($sub['sub_id'], 'import', $sub_is_scheduled, array('data-cron-ui' => 'import-user', 'data-pmnonce-t' => $cron_jobs_nonce['_pmnonce_t'])); 
														?>
														<a href="#" data-subscription-id="<?php echo $sub['sub_id'];?>" class="link-search-unsubscribe btn btn-sm btn-link text-danger float-right" title="Unsubscribe"><i class="icon-bin"></i></a>
													</td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								</div><!--.card-body-->
							</div><!--.card-->
						</div>
						<?php 
						} // end if ($subscriptions['total_results'] > 0)
					}// end if (empty($_GET['action'])) 
					?>
					
				</span><!-- #import-content-placeholder --> 
			

				<div id="import-ajax-message-placeholder" class="hide" style="position: fixed; left: 40%; top: 60px; width: 550px; z-index: 99999;"></div>
			
				<div id="import-load-more-div" class="hide">
					<button id="import-user-load-more-btn" name="import-user-load-more" class="btn btn-lg btn-primary btn-load-more w-100">Load more</button>
				</div>
			
				<div id="stack-controls" class="row list-controls" style="display:none">
					<div class="col-6">
						<div class="float-left">
							<label class="btn btn-outline bg-primary-400 text-primary-400 border-primary-400 mb-0 checkbox import-all">
								<input type="checkbox" name="checkall" id="checkall" /> Select All
							</label>
						</div>
					</div>
					<div class="col-6">
						<div class="float-right">
							<button type="submit" name="submit" class="btn btn-success mb-0" value="Import" id="import-submit-btn" data-loading-text="Importing...">Import <span id="status"><span id="count"></span></span> videos </button>
						</div>	
					</div>
				</div><!-- #stack-controls -->


		</form><!-- import-user-search-results-form -->
	</div><!-- .content -->
</div><!-- .primary -->

<?php include('modals/modal-subscribe-import-user.php');?>
<?php include('modals/modal-add-cron-job-modal.php');?>

<?php
include('footer.php');