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

$showm = '7';
$_page_title = 'Search log';
include('header.php');

$errors = array();

switch ($_GET['do'])
{
	case 'delete-ok':
		
		$info_msg = 'The search log has been cleared.';
		
	break;
}

$page	= (int) $_GET['page'];
$page 	= ($page == 0) ? 1 : $page;
$limit 	= 30;
$from = $page * $limit - ($limit);

$sql = "SELECT * 
		FROM pm_searches 
		ORDER BY hits DESC 
		LIMIT $from, $limit";

if ($result = mysql_query($sql))
{
	while ($row = mysql_fetch_assoc($result))
	{
		$data[] = $row;
	}
	mysql_free_result($result);
}
else
{
	$errors[] = 'An error occurred while retrieving data.<br /><strong>MySQL reported:</strong> '. mysql_error();
}

$total_items = count_entries('pm_searches', '', '');

$pagination = a_generate_smart_pagination($page, $total_items, $limit, 5, 'search-log.php', '');

$rank = $from + 1;
?>
<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">
			<div class="page-title d-flex">
				<h4><span class="font-weight-semibold"><?php echo $_page_title;?></span></h4>
			</div>
			<div class="header-elements d-flex-inline align-self-center ml-auto">
				<div class="">
					<?php if ($total_items > 0) : ?>
					<a href="#" id="delete_all" class="btn btn-sm btn-outline alpha-warning text-warning-400 border-warning-400 border-2"><i class="icon-bin"></i> Clear log</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="statistics.php" class="breadcrumb-item">Statistics &amp; Logs</a>
					<span class="breadcrumb-item active"><?php echo $_page_title;?></span>
				</div>
			</div>

			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
				</div>
			</div>
		</div>
	</div><!-- /page header -->
</div><!--.page-header-wrapper-->	
	<!-- Content area -->
	<div class="content content-full-width">

<?php if ( $total_items == 0 && empty($filter)) : ?> <!--Start ifempty-->

<div class="align-middle text-center mt-3 pt-3">
	<i class="icon-folder-search icon-3x text-muted mb-3"></i>
	<h6 class="font-weight-semibold text-grey mb-1">No searches yet</h6>
	<p class="text-grey mb-3 pb-1">Searches performed by your visitors will appear here.</p>
</div>

<?php else : ?> <!--Else ifempty-->


<?php
if ($info_msg != '')
{
	echo pm_alert_success($info_msg);
}
 
if ( pm_count($errors) > 0)
{
	echo pm_alert_error($errors);
}
?>

<div id="ajax_response_placeholder"></div>
	
<div class="card card-blanche">
	<div class="card-body">
		<div class="float-right">
			<div class="btn-group input-prepend">
			</div><!-- .btn-group -->
		</div>
	</div><!--.card-body-->

	<div class="datatable-scroll">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
	<thead>
	<tr> 
	<th width="40" class="text-center">Rank</th>
	<th>Search keywords</th>
	<th width="10%" class="text-center">Hits</th>
	</tr>
	</thead>
	<tbody>
		<?php if ($pagination != '') : ?> 
		<tr class="tablePagination">
			<td colspan="3" class="tableFooter">
				<div class="pagination float-right"><?php echo $pagination; ?></div>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php if (pm_count($data)) : ?>
		<?php foreach ($data as $k => $row) : ?>
		<tr>
			<td class="text-center">#<?php echo pm_number_format($rank++); ?></td>
			<td><?php echo stripslashes($row['string']); ?></td>
			<td class="text-center"><?php echo pm_number_format( (int) $row['hits']); ?></td>
		</tr>
		<?php endforeach; ?>
		<?php else : ?>
		<tr>
			<td colspan="3" style="text-align:center">The search log is empty.</td>
		</tr>
		<?php endif; ?>
		
		<?php if ($pagination != '') : ?> 
		<tr class="tablePagination">
			<td colspan="3" class="tableFooter">
				<div class="pagination float-right"><?php echo $pagination; ?></div>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
	</table>
	</div>

</div><!--.card-->
<?php endif ?> <!--End ifempty-->
</div><!-- .content -->

<?php echo csrfguard_form('_admin_searchlog');?>

<script type="text/javascript">
$(document).ready(function(){

	$('#ajax_response_placeholder').hide();
	
	<?php if ($total_items > 0) : ?>
	$('#delete_all').click(function(){
		
		if (confirm('Are you sure you want to clear the entire search log?')) {

			$.ajax({
				url: 'admin-ajax.php',
				data: {
					"p": 'searchlog',
					"do": 'delete-all',
					"_pmnonce": $('input[name=_pmnonce]').val(),
					"_pmnonce_t": $('input[name=_pmnonce_t]').val()
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if (data['success'] == false) {
						$('#ajax_response_placeholder').html(data['msg']).show();
					} else {
						window.location.href = "<?php echo _URL .'/'. _ADMIN_FOLDER .'/search-log.php?do=delete-ok'; ?>";
					}
				}
			});
		}

		return false;
	});
	<?php endif; ?>
});
</script>
<?php
include('footer.php');