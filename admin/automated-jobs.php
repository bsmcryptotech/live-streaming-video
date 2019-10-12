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
// | Copyright: (c) 2004-2016 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = 'cron';
$load_scrolltofixed = 1;
$_page_title = 'Manage Automated Jobs';
include('header.php');
include_once(ABSPATH . 'include/cron_functions.php');

$page = (int) $_GET['page'];
$page = ( ! $page) ? 1 : $page;
$limit = get_admin_ui_prefs('cron_jobs_pp');
$from = $page * $limit - ($limit);

$filter = 'job_id';
$filter_value = 'ASC';
$filters = array('job_id', 'name', 'last_exec_time');

if (in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
}

// Edit cron job form
if ($_POST['Submit'] == 'Save' && ! csrfguard_check_referer('_admin_edit_cron_job'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['Submit'] == 'Save')
{
	$job = get_cron_job($_POST['job_id']);
	
	$job['name'] = trim($_POST['name']);
	$job['exec_frequency'] = $_POST['exec_frequency'];
	
	if ($job['type'] == 'import')
	{
		$exclude_keywords = explode(',', $_POST['exclude_keywords']);
		if (pm_count($exclude_keywords) > 0)
		{
			foreach ($exclude_keywords as $k => $kw)
			{
				$kw = trim($kw);
				$kw = str_replace('-', '', $kw);
				
				$exclude_keywords[$k] = $kw;
			}
			$job['data']['exclude_keywords'] = $exclude_keywords;
		}
		
		if ($_POST['username'] != $job['data']['username'])
		{
			$job['data']['userdata']['username'] = trim($_POST['username']);
			$job['data']['userdata']['user_id'] = username_to_id($job['data']['username']);
		}
		
		if (($uploaded_after = validate_item_date($_POST)) !== false)
		{
			$job['data']['uploaded_after'] = pm_mktime($uploaded_after);
		}
	}
	
	if ($job['type'] == 'vscheck')
	{
		if ($job['data']['video_sorting'] != $_POST['video_sorting'] 
			|| $job['data']['video_limit'] != $_POST['video_limit'])
		{
			// force restart if currently executing 
			if ($job['data']['time_started'] > 0)
			{
				$job['data']['sql_start'] = ($config['published_videos'] * 2);
			}
		}
		$job['data']['video_sorting'] = $_POST['video_sorting'];
		$job['data']['video_limit'] = $_POST['video_limit'];
	}
	
	if ($job['type'] == 'sitemap' || $job['type'] == 'video-sitemap')
	{
		$options = sitemap_load_options();
		
		$options['ping_google'] = strtolower($_POST['ping_google']);
		$options['ping_bing'] = strtolower($_POST['ping_bing']);
		if ((int) $_POST['limit'] != $options['limit'] && ! empty($_POST['limit']))
		{
			$options['limit'] = (int) $_POST['limit'];
			
			// force restart if currently executing
			if ($job['data']['time_started'] > 0)
			{
				$job['data']['sql_start'] = $config['published_videos'] * 2;
				$job['data']['time_started'] = 0;
			}
		}
		
		if ($job['type'] == 'video-sitemap')
		{
			$options['media_keywords'] = ($_POST['media_keywords'] == '1') ? true : false;
			$options['media_category'] = ($_POST['media_category'] == '1') ? true : false;
			$options['item_pubDate'] = ($_POST['item_pubDate'] == '1') ? true : false;
		}
		
		sitemap_save_options($options);
	}
	
	if ( ! update_cron_job($job))
	{
		$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
	}
	else
	{
		$info_msg = pm_alert_success('The "'. htmlentities($job['name']) .'" automated job was updated successfully.');
	}
	
	unset($_POST); 
}

// Batch actions
if ($_POST['Submit'] != '' && ! csrfguard_check_referer('_admin_cron_jobs_form_automated-jobs'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ($_POST['Submit'] == 'Delete selected')
{
	if (pm_count($_POST['job_ids']) > 0)
	{
		if ( ! mass_delete_cron_jobs($_POST['job_ids']))
		{
			$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
		}
		else
		{
			$info_msg = pm_alert_success('The selected jobs were deleted.');
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}
else if ($_POST['Submit'] == CRON_STATUS_LIVE || $_POST['Submit'] == CRON_STATUS_STOPPED)
{
	if (pm_count($_POST['job_ids']) > 0)
	{
		if ( ! mass_update_cron_jobs($_POST['job_ids'], 'status', $_POST['Submit']))
		{
			$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
		}
		else
		{
			if ($_POST['Submit'] == CRON_STATUS_LIVE)
			{
				// restart
				mass_update_cron_jobs($_POST['job_ids'], 'last_exec_time', 0);
			}
			
			foreach ($_POST['job_ids'] as $k => $job_id)
			{
				$job = array('job_id' => $job_id);
				cron_log('Job '. (($_POST['Submit'] == CRON_STATUS_LIVE) ? 'activated.' : 'deactivated.'));
			}
			
			mass_update_cron_jobs($_POST['job_ids'], 'state', CRON_STATE_READY);
			
			$info_msg = pm_alert_success('The selected jobs were '. (($_POST['Submit'] == CRON_STATUS_LIVE) ? 'started' : 'stopped') .'.');
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}

$cron_table_form_nonce = csrfguard_raw('_admin_cron_jobs_form_automated-jobs');

$cron_jobs = array();
if ($_POST['keywords'] != '')
{
	$filter = 'name';
	$cron_jobs = get_all_cron_jobs($from, $limit, $filter, trim($_POST['keywords']));
	$total_cron_jobs = pm_count($cron_jobs);
}
else
{
	$cron_jobs = get_all_cron_jobs($from, $limit, $filter, $filter_value);
	$total_cron_jobs = count_entries('pm_cron_jobs', '', '');
}

$filename = 'automated-jobs.php';
$pagination = '';

if ( ! isset($_POST['submit']))
{
	$pagination = a_generate_smart_pagination($page, $total_cron_jobs, $limit, 5, $filename, '&filter='. $filter .'&fv='. $filter_value);
}

?>
<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold">Automated Jobs</span></h4>
			</div>
			<a href="#" class="header-elements-toggle text-default d-md-none"><i class="mi-search"></i></a>
			<div class="header-elements with-search d-none">
				<div class="d-flex-inline align-self-center ml-auto">
					<form name="search" action="automated-jobs.php" method="post" class="form-search-listing form-inline float-right">
						<div class="input-group input-group-sm input-group-search">
							<div class="input-group-append">
								<input name="keywords" type="text" value="<?php echo $_POST['keywords']; ?>" class="search-query search-quez input-medium form-control form-control-sm border-right-0" placeholder="Enter keyword" id="form-search-input" />
							</div>
								<select name="search_type" class="form-control form-control-sm border-left-0 border-right-0">
									<option value="username" <?php echo ($_GET['search_type'] == "username") ? 'selected="selected"' : ''; ?> >Username</option>
								</select>
							<div class="input-group-append">
							<button type="submit" name="submit" class="btn btn-light border-left-0" value="Search" id="submitFind"><i class="mi-search"></i><span class="findLoader"><img src="img/ico-loader.gif" width="16" height="16" /></span></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="automated-jobs.php" class="breadcrumb-item"><span class="breadcrumb-item active"><?php echo $_page_title; ?></span></a>
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
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">Video Status Checker</a>
					<a class="nav-link" id="v-pills-tab-help-three" data-toggle="pill" href="#v-pills-three" role="tab" aria-controls="v-pills-three" aria-selected="false" data-toggle="tab">Video Auto-import</a>
					<a class="nav-link" id="v-pills-tab-help-four" data-toggle="pill" href="#v-pills-four" role="tab" aria-controls="v-pills-four" aria-selected="false" data-toggle="tab">Setup</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<h5>Overview</h5>
						<p>Automated jobs are designed to save you time either by automatically importing content or handling the recurring tasks associated with running a website.</p>
						<p>Supported automations included:</p>
						<ul>
							<li><strong>Video Status Checking</strong>: keeps your database of remote videos in good shape by regularly checking and reporting removed videos.</li>
							<li><strong>Video Auto-import</strong>: schedule video importing from YouTube, DailyMotion and Vimeo based on keywords or from a specific user account.</li>
							<li><strong>Video &amp; Regular Sitemap</strong>: keeps all your sitemaps up to date automatically.</li>
						</ul>
						<p><strong>Note</strong>: This feature was introduced in version 2.6 (February, 2016). If you'd like to see more automated jobs added to your PHP Melody site please <a href="http://www.phpmelody.com/feedback-features" target="_blank">suggest them here</a>.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
						<h5>Video Status Checker</h5>
						<p>When this job is enabled, the affectingly  named "PM Bot" will verify any remote videos from your database. Videos found to be problematic will be listed under the "<a href="reported-videos.php">Reported videos</a>" page. You can then choose to disable, update or delete any such videos.</p>
						<p>The "Video Status Checker" job is a default system job and cannot be deleted. If you don't wish to run this job, simply deactivate it.</p>
						<p>Note: The first time this job is set to run it may take up to a week until it completes the initial checkup of your entire database. Please be patient if the job has a 'in progress' status for a few days.</p>
						<p><strong>Important</strong>: If your database contains more than 30,000 remote videos (e.g. YouTube, DailyMotion, etc. videos), set this automated job to run on a at least on a <strong>weekly</strong> basis.</p>
					</div>
					<div class="tab-pane" id="v-pills-three" role="tabpanel" aria-labelledby="v-pills-tab-help-three">
						<h5>Video Auto-import</h5>
						<p>PHP Melody can automatically import videos from YouTube, DailyMotion or Vimeo. Based on your keywords or user selection, PHP Melody will automatically grab content on a regular basis (as defined).</p>
						<p>This feature is ideal for creating niche sites or simply importing all your published videos from YouTube, DailyMotion or Vimeo into <strong><?php echo _SITENAME; ?></strong> regularly from your own account(s).</p>
						<br />
						<h5>How to create an auto-import job?</h5>
						<p>To create an auto-importing job you must first create an <strong>import subscription</strong> from the <strong>'Videos' > '<a href="import.php">Import Videos</a>' or '<a href="import_user.php">Import Videos from User</a>'</strong> results page.</p>
						<p>Start by searching (YouTube, DailyMotion or Vimeo) for the desired keywords/user and specify the category to which you want the videos imported into. <strong>Save this search as a subscription</strong> and then enable the auto-importing job for this specific search. The automated job will then import newly added videos, based exactly on your saved subscription/search.</p>
						<p>Any auto-import job can be set to run at specified intervals and even exclude videos containing unwanted keywords from being imported into your site.</p> 
						<p><strong><a href="https://www.youtube.com/watch?v=fHwuC6ILXBU" target="_blank">Watch this instructional video</a> for a detailed step-by-step procedure.</strong></p>
					</div>
					<div class="tab-pane" id="v-pills-four" role="tabpanel" aria-labelledby="v-pills-tab-help-four">
						<h5>Automated Jobs Setup</h5>
						<p>The automated jobs are designed to run on a recurrent basis and without any user intervention. To accomplish this, PHP Melody uses <strong>cron</strong>. Most hosting providers have cron installed and available for you to use.</p>
						<p>Setting up this cronjob is easy and has to be done only once. To begin setting up your cron go to the "<strong><a href="automated-jobs-setup.php">Setup</a></strong>" page.</p>
					</div>


				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content content-full-width">
	
<?php 
if (empty($config['cron_secret_key']))
{
	echo pm_alert_warning('This feature requires a cron job. Visit the <a href="automated-jobs-setup.php">Automated Jobs Setup</a> page to generate your secret key and learn more.');

}
?>

<?php echo $info_msg; ?>

<div class="card card-blanche">
	<div class="card-body">

	<?php if ( $total_cron_jobs > 25 ) : ?>
	<div class="row">
		<div class="col-sm-12 col-md-6">
		</div><!-- .span8 -->
		<div class="col-sm-12 col-md-6 d-none d-md-block">
			<div class="float-right mb-3">
				<form name="jobs_per_page" action="automated-jobs.php" method="get" class="form-inline">
					<label class="mr-1" for="inlineFormCustomSelectPref">Jobs/page</label>
			    	<input type="hidden" name="ui_pref" value="cron_jobs_pp" />
					<select name="results" class="custom-select custom-select-sm w-auto" onChange="this.form.submit()" >
					<option value="25" <?php if($limit == 25) echo 'selected="selected"'; ?>>25</option>
					<option value="50" <?php if($limit == 50) echo 'selected="selected"'; ?>>50</option>
					<option value="75" <?php if($limit == 75) echo 'selected="selected"'; ?>>75</option>
					<option value="100" <?php if($limit == 100) echo 'selected="selected"'; ?>>100</option>
					<option value="125" <?php if($limit == 125) echo 'selected="selected"'; ?>>125</option>
					</select>
					<?php
					// filter persistency
					if (strlen($_SERVER['QUERY_STRING']) > 0)
					{
						$pieces = explode('&', $_SERVER['QUERY_STRING']);
						foreach ($pieces as $k => $val)
						{
							$p = explode('=', $val);
							if ($p[0] != 'page' && $p[0] != 'results') :	
							?>
							<input type="hidden" name="<?php echo $p[0];?>" value="<?php echo $p[1];?>" />
							<?php 
							endif;
						}
					}
					?>
				</form>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="row">
		<div class="col-5">			
			<?php if ( ! empty($_POST['keywords'])) : ?>
			<h5 class="font-weight-semibold mt-2">SEARCH RESULTS FOR <mark><?php echo $_POST['keywords']; ?></mark> <a href="#" onClick="parent.location='automated-jobs.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear search results"><i class="icon-cancel-circle2"></i></a></h5>
			<?php endif; ?>
		</div>

		<div class="col-7">
			<div class="float-right">

			</div>
		</div>
	</div> <!--.row with filters -->

	</div><!--.card-body-->
	
	
	<form name="cron_jobs_form" id="cron_jobs_form" action="automated-jobs.php?page=<?php echo $page;?>" method="post">
	<div class="datatable-scroll">
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
		<thead>
		<tr>
			<th align="center" class="text-center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
			<th width="30"></th>
			<th width="">Job Name</th>
			<th width="" class="text-center">Job Type</th>
			<th width="" class="text-center">Frequency</th>
			<th width="" class="text-center">Last Performed</th>	
			<th width="" class="text-center">Status</th>
			<th style="width: 220px;" class="text-center">Action</th>
		</tr>
		</thead>
		<tbody>
	  
		  <?php 
		  if (pm_count($cron_jobs) > 0) : 
			foreach ($cron_jobs as $job_id => $job) : 
				
				$col = ($alt % 2) ? 'table_row1' : 'table_row2';
				$alt++;
			?>
			<tr class="<?php echo $col;?> <?php if($job['status'] == "stopped") echo "table_row_inactive"; ?>" id="tr-job-id-<?php echo $job_id; ?>">
				<td align="center" class="text-center" width="3%">
					<input name="job_ids[]" type="checkbox" value="<?php echo $job_id; ?>" />
				</td>
				<td>
					<?php if ($job['type'] == 'import') : ?>
					<div class="pm-autojob-ico u-video" rel="tooltip" title="Video import job"><i class="icon-youtube2"></i></div>
					<?php elseif ($job['type'] == 'vscheck') : ?>
					<div class="pm-autojob-ico u-video-checker" rel="tooltip" title="Video checking job"><i class="icon-shield-check"></i></div>
					<?php elseif ($job['type'] == 'sitemap' || $job['type'] == 'video-sitemap') : ?>
					<div class="pm-autojob-ico u-sitemap" title="Sitemap updating job"><i class="icon-map5" rel="tooltip"></i></div>
					<?php endif; ?>
				</td>

				<td>
					<strong><a href="#edit-cron-job-modal" class="cron-edit-btn" rel="tooltip" title="Edit" data-job-id="<?php echo $job_id; ?>"><?php echo $job['name']; ?></a></strong>
				</td>
				<td align="center" class="text-center">
					<?php 
					$label = '';
					switch ($job['type'])
					{
						case 'import':
							$label = 'Video Auto-import';
						break;
						
						case 'vscheck':
							$label = 'PM Bot';
						break;
						
						case 'sitemap':
							$label = 'Regular Sitemap';
						break;
						
						case 'video-sitemap':
							$label = 'Video Sitemap';
						break;
					}
					echo $label;
					?>
				</td>
				<td align="center" class="text-center">
					<?php echo cron_frequency_sec_to_lang($job['exec_frequency']); ?>
				</td>
				<td align="center" class="text-center">
					<span rel="tooltip" title="<?php echo ($job['last_exec_time'] > 0) ? date('l, F j, Y g:i A', $job['last_exec_time']) : ''; ?>"> 
						<?php echo ($job['last_exec_time'] > 0) ? time_since($job['last_exec_time'], false) .' ago' : 'Never'; ?>
					</span>
				</td>
				<td align="center" class="text-center">
					<?php show_cron_job_state_html($job); ?>
				</td>
				<td align="center" class="text-center table-col-action">				
					<?php show_play_stop_button_html($job); ?>
					<a href="#view-cron-log-modal" class="list-icons-item mr-1 text-grey-300 cron-view-log-btn" rel="tooltip" title="View History Log" data-job-id="<?php echo $job_id; ?>"><i class="icon-terminal"></i></a>
					<a href="#edit-cron-job-modal" class="list-icons-item mr-1 cron-edit-btn" rel="tooltip" title="Edit" data-job-id="<?php echo $job_id; ?>"><i class="icon-pencil7"></i></a>
					<a href="" class="list-icons-item text-danger cron-delete-btn" rel="tooltip" title="Delete" data-job-id="<?php echo $job_id; ?>"><i class="icon-bin"></i></a>
				</td>
			</tr>
			<?php 
			endforeach;
		  else : ?>
			<tr>
				<td colspan="7" align="center" class="text-center">
					No automated jobs found.
				</td>
			</tr>
		  <?php endif; ?>
		  <?php if ($pagination != '') : ?>
			<tr class="tablePagination">
				<td colspan="8" class="tableFooter">
					<div class="pagination float-right"><?php echo $pagination; ?></div>
				</td>
			</tr>
			<?php endif; ?>
		  </tbody>
		</table>
	</div>

	<div class="datatable-footer">
		<div id="stack-controls" class="row list-controls">
			<div class="col-md-12">			
				<div class="float-right">
					<div class="btn-group btn-group-sm dropup">
						<button type="button" class="btn btn-sm btn-outline bg-primary-400 text-primary-400 border-primary-400 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button> 
						<ul class="dropdown-menu">
							<li><button type="submit" name="Submit" value="<?php echo CRON_STATUS_LIVE; ?>" class="btn btn-sm btn-link text-success w-100 rounded-0 text-left"><i class="icon-play4 mr-1"></i> Activate</button></li>
							<li><button type="submit" name="Submit" value="<?php echo CRON_STATUS_STOPPED; ?>" class="btn btn-sm btn-link text-warning w-100 rounded-0 text-left"><i class="icon-pause2 mr-1"></i> Deactivate</button></li>
						</ul>
					</div>
					<button type="submit" name="Submit" value="Delete selected" class="btn btn-sm btn-danger" id="cron-delete-selected-btn">Delete</button>
					<input type="hidden" name="filter" id="listing-filter" value="<?php echo $filter;?>" />
					<input type="hidden" name="fv" id="listing-filter_value"value="<?php echo $filter_value;?>" /> 
				</div>
			</div>
		</div><!-- .list-controls -->
	</div>
	
	<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $cron_table_form_nonce['_pmnonce'];?>" value="<?php echo $cron_table_form_nonce['_pmnonce'];?>" />
	<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $cron_table_form_nonce['_pmnonce'];?>" value="<?php echo $cron_table_form_nonce['_pmnonce_t'];?>" />
	<input type="hidden" name="filter" id="listing-filter" value="<?php echo $filter;?>" />
	<input type="hidden" name="fv" id="listing-filter_value" value="<?php echo $filter_value;?>" />
	<input type="hidden" name="keywords" id="listing-filter_keywords" value="<?php echo $keywords;?>" />
	</form>

</div><!--.card-->
</div>
<!-- /content area -->

<!-- view cron log modal -->
<?php
include('modals/modal-view-cron-log.php');
?>

<!-- edit cron modal -->
<?php
include('modals/modal-edit-cron.php');
?>


<?php 
include('footer.php');