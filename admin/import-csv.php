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
// | Copyright: (c) 2004-2015 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '2';

$step = (int) $_GET['step'];
$step = ( ! $step) ? 1 : $step;

$load_import_js = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$load_fileinput_upload = 1;

switch ($step)
{
	case 1: // Upload file / Files table
	break;
	
	case 2: // Process Queue
	break;
	
	case 3: // Import
	
		$load_scrolltofixed = 1;
		$load_chzn_drop = 1;
		$load_tagsinput = 1;
		$load_ibutton = 1;
		$load_prettypop = 1;
		$load_lazy_load = 1;
		
	break;
}


$_page_title = 'Import from CSV file';
include('header.php');

$sources = a_fetch_video_sources();
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
			<div class="header-elements d-none d-md-block">
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
					<a class="nav-link" id="v-pills-tab-help-two" data-toggle="pill" href="#v-pills-two" role="tab" aria-controls="v-pills-two" aria-selected="false" data-toggle="tab">About the CSV structure</a>
					<a class="nav-link" id="v-pills-tab-help-three" data-toggle="pill" href="#v-pills-three" role="tab" aria-controls="v-pills-three" aria-selected="false" data-toggle="tab">On this page</a>
				</div>
			</div>
			<div class="col-10 help-panel-content">
				<div class="tab-content" id="v-pills-tabContent">
					<div class="tab-pane show active" id="v-pills-one" role="tabpanel" aria-labelledby="v-pills-tab-help-one">
						<p>From this page you can bulk-import media files from a <a href="#v-pills-two" data-toggle="pill" role="tab" aria-controls="v-pills-two" data-toggle="tab">pre-defined CSV file</a>. Among the use cases, we'd like to mention: migrating data from a previous database/CMS, adding external videos in bulk (i.e. videos stored in the cloud or on another server, etc.) and quickly importing a specific list of YouTube videos.</p>
						<p>The CSV import process has 3 steps: uploading the CSV file, processing it's contents and finally importing the selected videos into your database. Each of these 3 steps can be resumed.</p>
						<p>For the import to work flawlessly a specific CSV format should be used. Please review the required CSV structure in the &ldquo;<a href="#v-pills-two" data-toggle="pill" role="tab" aria-controls="v-pills-two" data-toggle="tab">About the CSV structure</a>&rdquo; tab before uploading any file.</p>
						<p><strong>Warning</strong>: Loading a large amount of data from a CSV might negatively impact the import process. If performance is affected, we recommend splitting very large CSV files into smaller ones. This is especially recommended if the CSV file contains external video links from sites such as: YouTube, DailyMotion and Vimeo.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
						<p>PHP Melody will only process CSV files which are formatted in a specific way. Feel free to review the provided <a href="temp/phpmelody-csv-example.csv">example CSV file</a>. If you're unsure about your data, always test with a small batch beforehand.</p>
						<h5>Required CSV Structure:</h5>
						<ul class="list-unstyled">
							<li>Column 1: <strong>Video URL</strong> (<strong><em>mandatory</em></strong>). Can be anything from a local video to YouTube or DailyMotion links.</li>
							<li>Column 2: <strong>Video title</strong> (optional). Leave blank when you wish to use data from the API for YouTube, DailyMotion &amp; Vimeo videos.</li>
							<li>Column 3: <strong>Description</strong> (optional). Leave blank when you wish to use data from the API for YouTube, DailyMotion &amp; Vimeo videos.</li>
							<li>Column 4: <strong>Tags</strong> (optional). Tags must be comma separated (e.g. apple, pear, banana)</li>
							<li>Column 5: <strong>Thumbnail URL</strong> (optional). Leave blank when you wish to use data from the API for YouTube, DailyMotion &amp; Vimeo videos.</li>
							<li>Column 6: <strong>Duration</strong> (optional). Expressed in seconds; for example, the duration for a 2 minute video is <strong>120</strong> seconds.</li>
						</ul>
						<h5>Additionally, the CSV file must have the following format/properties:</h5>
						<ul class="list-unstyled">
							<li><strong>Field delimiter</strong>: , (comma)</li>
							<li><strong>Text delimiter</strong>: " (double quotes)</li>
							<li><strong>Wrap text fields in</strong>: " (double quotes)</li>
							<li><strong>Should NOT</strong> include column names on the first row (e.g. 'Url', 'Video title'). The CSV should only include rows with data.</li>
							<li><strong>UTF-8 encoding</strong> is recommended.</li>
						</ul>
						<div class="alert alert-info alert alert-styled-left">You can use <a href="temp/phpmelody-csv-example.csv">this example CSV</a> as a template for your own CSV.</div>
					</div>

					<div class="tab-pane" id="v-pills-three" role="tabpanel" aria-labelledby="v-pills-tab-help-three">
						<p>This page allows you to add content from sites by using only the embed code. Allowed HTML tags include &lt;iframe&gt; &lt;embed&gt; &lt;object&gt; &lt;param&gt; and &lt;video&gt;</p>
						<p>To assign a thumbnail for this submission, click on the thumbnail picture.</p>
						<p>You also have a couple of publishing options such as specifying a future publication date and time. Submissions can also be made private from unregistered users. This is a great way to increase your registration rate.</p>
						<p>Note: We highly recommend you add the video duration to each submission.</p>
						<p></p>
						<p>Learn how to use the <strong>custom fields</strong>: <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank">http://help.phpmelody.com/how-to-use-the-custom-fields/</a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content">
	
		<div class="wizard-form steps-basic wizard">
			<div class="steps clearfix">
				<ul role="tablist">
					<li role="tab" class="first <?php echo ($step == 1) ? 'current' : 'done'; ?>" aria-disabled="false" aria-selected="true"><a id="steps-uid-0-t-0" href="import-csv.php" aria-controls="steps-uid-0-p-0" class=""><span class="number">1</span> Upload CSV</a></li>
					<li role="tab" class="<?php echo ($step == 2) ? 'current' : ''; echo ($step == 3) ? 'done' : ''; ?>" aria-disabled="true"><a id="steps-uid-0-t-1" href="#" aria-controls="steps-uid-0-p-1" class="disabled"><span class="number">2</span> Process CSV Data</a></li>
					<li role="tab" class="<?php echo ($step >= 3) ? 'current' : 'disabled'; ?>" aria-disabled="true"><a id="steps-uid-0-t-2" href="#" aria-controls="steps-uid-0-p-2" class="disabled"><span class="number">3</span> Import Videos</a></li>
				</ul>
			</div>
		</div>

		<?php 		
		load_categories();
		if (pm_count($_video_categories) == 0) 
		{
			echo pm_alert_error('Please <a href="edit-category.php?do=add&type=video">create a category</a> first.');
		}
		 
		if ($step == 2 || $step == 3)
		{
			$file_id = (int) $_GET['file-id'];
			if ( ! $file_id)
			{
				echo pm_alert_error('Please select a file to process.');
				$step = 1;
			}
			else 
			{
				$sql = "SELECT * 
						FROM pm_import_csv_files
						WHERE file_id = $file_id";
				if ( ! $result = mysql_query($sql))
				{
					echo pm_alert_error('Could not retrieve file data.<br /><strong>MySQL Error</strong>: '. mysql_error());
					
					$step = 1;
				}
				else
				{
					$csv_file = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					if ( ! $csv_file)
					{
						echo pm_alert_error('The requested file was not found');
					
						$step = 1;
					}
				}
			}
		}
		
		if ($step == 1) : ?>

		<div class="card">
			<div class="card-body">
				<h5 class="mb-3 font-weight-semibold">Upload CSV</h5>
					<ol id="upload-csv-log" class="list-unstyled mb-3"></ol>
					<div class="d-block">
						<form id="import-csv-upload-file-form" class="btn-hide-upload" name="import-csv-upload-file-form" enctype="multipart/form-data" action="import-csv.php?step=1" method="post" onsubmit="return false;">
						<div class="input-group mb-3">
							<div class="form-group-feedback form-group-feedback-left">
								<input type="file" name="file" class="file-input form-control form-control-lg alpha-grey" id="upload-csv-btn" data-show-remove="false" data-show-preview="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Select CSV" data-fouc>
								<input type="hidden" name="upload-type" value="csv" />
								<input type="hidden" name="p" value="upload" /> 
								<input type="hidden" name="do" value="upload-file" />
								<span id="debugging-output-serverdata" class="form-text text-muted"></span>
							</div>
						</div>
						</form>
					</div>
			</div>
		</div>

		
		<?php
		
		$total_files = count_entries('pm_import_csv_files', '', '');
		
		if ($total_files > 0)
		{
			$sql = "SELECT * 
					FROM pm_import_csv_files 
					ORDER BY file_id DESC";
			if ( ! $result = mysql_query($sql))
			{
				echo pm_alert_error('Could not retrieve files data.<br /><strong>MySQL Error</strong>: '. mysql_error());
			}
			else
			{
				?>


		<div class="card">
			<div class="card-header">
				<h6 class="card-title font-weight-semibold">Uploaded Files</h6>
			</div>
			<div class="card-body-">
				<div class="table-responsive">
					<table cellpadding="0" cellspacing="0" width="100%" class="table table-hover table-files table-striped table-columned pm-tables">
						<thead>
							<tr>
								<th>File Name</th>
								<th width="8%" align="center" class="text-center">Upload Date</th>
								<th width="6%" align="center" class="text-center">Videos</th>
								<th width="7%" align="center" class="text-center">Processed</th>
								<th width="7%" align="center" class="text-center">Imported</th>
								<th width="15%" align="center" class="text-center" style="width:260px;">Action</th>
							</tr>
						</thead>
						<tbody>
						<?php while ($row = mysql_fetch_assoc($result)) : ?>
							<tr id="import-csv-table-row-<?php echo $row['file_id']; ?>">
								<!-- File Name -->
								<td>
									<strong><?php echo $row['filename']; ?></strong>
								</td>
								<!-- Upload Date -->
								<td align="center">
									<span data-popup="tooltip" data-container="body" data-original-title="<?php echo date('l, F j, Y g:i:s A', $row['upload_date']); ?>" class="font-size-xs"><?php echo date('M d, Y', $row['upload_date']); ?></span>
								</td>
								<!-- Items Detected -->
								<td align="center">
										<?php echo pm_number_format($row['items_detected']); ?>
								</td>
								<!-- Processed -->
								<td align="center">
										<?php 
										$processed = ($row['items_detected'] > 0 ) ? round(($row['items_processed'] / $row['items_detected']) * 100, 0) : 0;
										$processed = ($processed > 100) ? 100 : $processed;
										?>
										<div class="progress" style="height: 0.375rem;">
										<div class="progress-bar bg-success" data-popup="tooltip" data-original-title="<?php echo $processed; ?>%" style="width: <?php echo $processed; ?>%;">
											<span class="sr-only"><?php echo $processed; ?>% Complete</span>
										</div>
										</div>
								</td>
								<!-- Imported -->
								<td align="center">
										<div class="progress" style="height: 0.375rem;">
										<div class="progress-bar bg-success" data-popup="tooltip" data-original-title="<?php echo ($row['items_detected'] > 0) ? round(($row['items_imported'] / $row['items_detected']) * 100, 0) : 0; ?>%" style="width: <?php echo ($row['items_detected'] > 0) ? round(($row['items_imported'] / $row['items_detected']) * 100, 0) : 0; ?>%;">
											<span class="sr-only"><?php echo ($row['items_detected'] > 0) ? round(($row['items_imported'] / $row['items_detected']) * 100, 0) : 0; ?>% Complete</span>
										</div>
										</div>
								</td>
								<!-- action -->
								<td align="center">
									<div class="btn-group">
										<a href="import-csv.php?step=2&file-id=<?php echo $row['file_id'];?>" class="btn btn-sm btn-outline bg-primary-400 text-primary-400 border-primary-400 <?php if ($processed >= 100) echo 'disabled text-muted'; ?>" <?php if ($processed >= 100) echo 'disabled="disabled" onclick="return false;"'; ?>><?php echo ($processed > 0 && $processed < 100) ? 'Resume' : 'Process'; ?><?php if ($processed >= 100) echo 'ed'; ?></a>
										<a href="import-csv.php?step=3&file-id=<?php echo $row['file_id'];?>" class="btn btn-sm btn-outline bg-primary-600 text-primary-600 border-primary-600 <?php if ($processed < 100 || ($row['items_imported'] == $row['items_detected'])) echo 'disabled'; ?>" <?php if ($processed < 100 || ($row['items_imported'] == $row['items_detected'])) echo 'disabled="disabled"  onclick="return false;"'; ?>>Import<?php if ($processed < 100 || ($row['items_imported'] == $row['items_detected'])) echo 'ed'; ?></a>
									</div>
									<a href="#" data-file-id="<?php echo $row['file_id']; ?>" class="btn btn-sm btn-link import-csv-delete-file text-danger"><i class="icon-bin"></i></a>
								</td>
							</tr>
						<?php endwhile;
						mysql_free_result($result);
					?>
					</tbody>
					</table>
				</div>
			</div>
		</div>
				<?php
			}
		}
		endif; // end step 1 ?>
				
		<?php if ($step == 2) :
		
			$processed = 0;
			if ($csv_file['items_detected'] > 0)
			{
				$processed = round(($csv_file['items_processed'] / $csv_file['items_detected']) * 100, 0);
				$processed = ($processed > 100) ? 100 : $processed;
			}
			
			?>

			<div class="card">
				<div class="card-body">
					<h5 class="mb-3">Processing entries</h5>

						<div class="pm-csv-data">
							<div class="pm-file-data">
								<div id="import-csv-ajax-response">
									<div class="alert alert-info">
										PHP Melody will attempt to read <strong><?php echo $csv_file['filename']; ?></strong> and gather information about each media file or URL. Click &ldquo;<strong>Process entries</strong>&rdquo; to begin.
									</div>
								</div>

								<div class="progress hide" style="height: 0.375rem;">
								<div class="progress-bar bg-success" id="progressbar" data-popup="tooltip" data-original-title="<?php echo $processed; ?>%" style="width: <?php echo $processed; ?>%;">
									<span class="sr-only"><?php echo $processed; ?>% Complete</span>
								</div>
								</div>

								<div class="pm-file-icon">
									<img src="img/ico-file-csv.png" height="34" width="34" alt="" class="pull-right">
								</div>
								<div class="pm-file-attr">
									<ul class="list-unstyled">
										<li><strong>File</strong>: <?php echo $csv_file['filename']; ?> </li>
										<li><strong>Uploaded on</strong>: <?php echo date('M d, Y h:i:s A', $csv_file['upload_date']); ?></li>
										<li><strong>Entries available</strong>: <?php echo pm_number_format($csv_file['items_detected']); ?></li>
										<li><span class="hide" id="import-csv-eta"><strong>Time required to process</strong>: <span id="import-csv-eta-value">n/a</span></span></li>
									</ul>
								</div>
								<div class="pm-file-action mb-2">
									<input type="hidden" name="items_detected" value="<?php echo $csv_file['items_detected']; ?>" />
									<input type="hidden" name="file_id" value="<?php echo $csv_file['file_id']; ?>" />
									<button type="submit" name="process" value="Process Queue" id="import-csv-process-btn" class="btn btn-success"><?php echo ($processed > 0) ? 'Resume Process' : 'Process '. pm_number_format($csv_file['items_detected']) .' entries'; ?></button> 
									<br /><em class="import-ajax-loading-animation hide"><small>Please wait...</small></em>
								</div>
							</div>
						</div>
				</div>
			</div>


		<?php endif; // end step 2
		
		if ($step == 3) : 
		$items_left = $csv_file['items_detected'] - $csv_file['items_imported'];
		?>

			<div class="card">
				<div class="card-body">
					<h5 class="mb-3">Import from <mark><?php echo $csv_file['filename']; ?></mark></h5>
					<form name="import-csv-options-form" id="import-csv-options-form" action="" method="post" class="form-inline">
					<div class="pm-csv-data">
						<div class="pm-file-data">
							<div id="import-csv-ajax-response">
								<div class="alert alert-info">
									<strong><?php echo pm_number_format($items_left); ?> <?php echo ($items_left == 1) ? 'item is' : 'items are'; ?> ready to be imported</strong> into your database. 
								</div>
							</div>

							<div class="progress hide" style="height: 0.375rem;">
							<div class="progress-bar bg-success" id="progressbar" data-popup="tooltip" data-original-title="<?php echo $processed; ?>%" style="width: <?php echo $processed; ?>%;">
								<span class="sr-only"><?php echo $processed; ?>% Complete</span>
							</div>
							</div>


							<div class="pm-file-icon">
								<img src="img/ico-file-csv.png" height="34" width="34" alt="" class="pull-right">
							</div>
							<div class="pm-file-attr">
								<strong>Add to:</strong>
								<?php 
								$categories_dropdown_options = array(
																'attr_name' => 'use_this_category[]',
																'attr_id' => 'main_select_category',
																'select_all_option' => false,
																'spacer' => '&mdash;',
																'selected' => '',
																'other_attr' => 'multiple="multiple" size="3" data-placeholder="Import videos into..."',
																'option_attr_id' => 'check_ignore'
																);
								echo categories_dropdown($categories_dropdown_options);
								?>

							</div>
							<div class="pm-file-action">
								<button type="submit" name="submit" class="btn btn-success" id="import-csv-show-videos-btn">Show videos  <i class="mi-navigate-next"></i></button>
							</div>
						</div>
					<div class="clearfix"></div>
					</div>

						
				<input type="hidden" name="file_id" value="<?php echo $csv_file['file_id']; ?>" />
				<select name="data_source" class="hide">
					<option value="csv" selected="selected">CSV</option>
				</select>
				<input type="hidden" name="results" value="50" />
				</form><!-- import-csv-options-form -->
				</div>
			</div>					
			
			<form name="import-search-results-form" id="import-search-results-form" action="" method="post">
				
				<input type="hidden" name="file_id" value="<?php echo $csv_file['file_id']; ?>" />
				
				<div id="vs-grid">
					<span id="import-content-placeholder" class="row">


					</span><!-- #import-content-placeholder -->
				</div><!-- #vs-grid -->
								
				<div id="import-load-more-div" class="hide">
					<button id="import-csv-load-more-btn" name="import-load-more" class="btn btn-lg btn-primary btn-load-more w-100">Load more</button>
				</div>


				<div id="stack-controls" class="row list-controls" style="display:none">
					<div class="col-6">
						<div class="float-left">
							<label class="btn btn-outline bg-primary-400 text-primary-400 border-primary-400 mb-0">
								<input type="checkbox" name="checkall" id="checkall" /> Select All
							</label>
						</div>
					</div>
					<div class="col-6">
						<div class="float-right">
							<span class="import-ajax-loading-animation"><img src="img/ico-loader.gif" width="16" height="16" /></span> <button type="submit" name="submit" class="btn btn-success mb-0" value="Import" id="import-submit-btn" data-loading-text="Importing...">Import <span id="status"><span id="count"></span></span> videos </button>
						</div>	
					</div>
				</div><!-- #stack-controls -->

			
			</form><!-- import-search-results-form -->
						
		<?php endif; // end step 3 ?>
		
		<div id="import-ajax-message-placeholder" class="hide" style="position: fixed; left: 40%; top: 60px; width: 550px; z-index: 99999;"></div>

</div><!-- .content -->
<?php
include('footer.php');