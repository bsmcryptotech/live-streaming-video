<?php
// +------------------------------------------------------------------------+
// | PHP Melody version 1.7 ( www.phpsugar.com )
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
$_page_title = 'Statistics';
include('header.php');

function count_avatars() {
	$q = mysql_query("SELECT avatar FROM pm_users WHERE avatar NOT LIKE '' AND avatar NOT LIKE 'default.gif'");
	$count = mysql_num_rows($q);
	return $count;
}

function is_today($timestamp){
	return date('Y-m-d',$timestamp) == date('Y-m-d');
}

function is_yesterday($timestamp){
	$yesterday  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
	return date('Y-m-d',$timestamp) == date('Y-m-d', $yesterday);
}

function is_thismonth($timestamp){
	$this_month  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
	return date('Y-m',$timestamp) == date('Y-m', $this_month);
}

function top1_search() {
	$query = mysql_query("SELECT string, hits FROM pm_searches ORDER BY hits DESC LIMIT 1")or die(" Error: ".mysql_error());
	$r = mysql_fetch_array($query);
	if(!empty($r['string'])) {
		$keyword = $r['string'];
	return $keyword .' ('. pm_number_format($r['hits']).')';
	} else {
	return 'N/A yet.';
	}
}

function top1_rated() 
{
	$sql = "SELECT uniq_id, up_vote_count, down_vote_count, score  
			FROM pm_bin_rating_meta
			ORDER BY score DESC 
			LIMIT 1";
	$result = mysql_query($sql);
	$r = mysql_fetch_array($result);
	mysql_free_result($result);
	
	$r['total_votes'] = $r['up_vote_count'] + $r['down_vote_count'];
	
	if ($r['uniq_id'] && $r['total_votes'] > 0) 
	{
		$vote = ($r['total_votes'] > 1) ? 'votes' : 'vote';

		return '<a href="'. _URL .'/watch.php?vid='. $r['uniq_id'] .'">'. vnamefromvid($r['uniq_id']) .' ('. pm_number_format($r['total_votes']) ." $vote)</a>";
	} 
	else 
	{
		return 'N/A yet.';
	}
}

function top1_commented() 
{
	
	$sql = "SELECT uniq_id, COUNT(*) as total  
			FROM pm_comments 
			WHERE uniq_id NOT LIKE 'article-%' 
			GROUP BY uniq_id 
			ORDER BY total DESC 
			LIMIT 1";
	$result = mysql_query($sql);
	$r = mysql_fetch_array($result);
	mysql_free_result($result);
	
	if ($r['uniq_id']) 
	{
		return '<a href="comments.php?vid='. $r['uniq_id']. '">'. vnamefromvid($r['uniq_id']) .' ('. pm_number_format($r['total']) .')</a>';
	} 
	else 
	{
		return 'N/A yet.';
	}
}
function top1_commentor() 
{
	
	$sql = "SELECT pm_comments.user_id, pm_users.username, COUNT(*) as total 
			FROM pm_comments
			JOIN pm_users ON ( pm_comments.user_id = pm_users.id )
			WHERE pm_comments.user_id != '0' 
			GROUP BY pm_comments.user_id
			LIMIT 1 ";

	$result = mysql_query($sql);
	$r = mysql_fetch_array($result);
	mysql_free_result($result);
	
	if ($r['username']) 
	{
		return $r['username'] .' (<a href="comments.php?keywords='. $id .'&search_type=username&submit=Search">'. pm_number_format($r['total']) .'</a>)';
	} 
	else 
	{
		return 'N/A yet.';
	}
}
function member_searches() {
	$query = mysql_query("SELECT user FROM pm_searches WHERE user != 'guest'");
	$r = mysql_num_rows($query);
	return $r;
}


function recent_dates($date, $table, $orderby, $datefield) {

	$query = mysql_query("SELECT ".$datefield." FROM ".$table." ORDER BY ".$orderby." DESC")or die(" Error: ".mysql_error());
	$count=0;
	while ($row = mysql_fetch_array($query)) 
	{ 
			if($date($row[$datefield])) {
				$count++;
			} 
	}
	return $count;
}

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
				</div>
			</div>
		</div>
		</div>

		<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
			<div class="d-flex">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="statistics.php" class="breadcrumb-item active">Statistics &amp; Logs</a>
				</div>
			</div>

			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
				</div>
			</div>
		</div>
	</div><!--.page-header -->
</div><!--.page-header-wrapper-->	

	<!-- Content area -->
	<div class="content">
	
	<div class="row">
		<div class="col-sm-12 col-md-6">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title">Videos</h5>
				</div>

				<table class="table table-xs">
					  <tr>
						<td class="w-25"><strong>Total videos:</strong></td>
						<td class="w-75"><?php echo pm_number_format($config['total_videos']); ?> <a href="videos.php" class="text-grey opacity-80"><i class="mi-input"></i></a></td>
					  </tr>
					  <tr>
						<td class="w-25"><strong>Added today:</strong></td>
						<td class="w-75"><?php echo pm_number_format(recent_dates('is_today', 'pm_videos', 'id', 'added')); ?></td>
					  </tr>
					  <tr>
						<td class="w-25"><strong>Added yesterday:</strong></td>
						<td class="w-75"><?php echo pm_number_format(recent_dates('is_yesterday', 'pm_videos', 'id', 'added')); ?></td>
					  </tr>
					  <tr>
						<td class="w-25"><strong>Added this month: </strong></td>
						<td class="w-75"><?php echo pm_number_format(recent_dates('is_thismonth', 'pm_videos', 'id', 'added')); ?></td>
					  </tr>
					  <tr>
						<td class="w-25"><strong>Most comments:</strong></td>
						<td class="w-75"><?php echo top1_commented(); ?></td>
					  </tr>
					  <tr>
						<td class="w-25"><strong>Most rated:</strong></td>
						<td class="w-75"><?php echo top1_rated(); ?></td>
					  </tr>
					  <tr>
						<td class="w-25"><strong>Reported videos:</strong></td>
						<td class="w-75"><?php echo pm_number_format(count_entries('pm_reports', 'r_type', '1')); ?> <a href="reported-videos.php" class="text-grey opacity-80"><i class="mi-input"></i></a></td>
					  </tr>
				</table>
			</div>

			<div class="card">
				<div class="card-header">
					<h5 class="card-title">Video Comments</h5>
				</div>

				<table cellpadding="0" cellspacing="0" width="100%" class="table table-xs">
				  <tr>
					<td class="w-25"><strong>Total comments:</strong></td>
					<td class="w-75"><?php echo pm_number_format(count_entries('pm_comments', '', '')); ?> <a href="comments.php?filter=videos" class="text-grey opacity-80"><i class="mi-input"></i></a></td>
				  </tr>
				  <tr>
					<td class="w-25"><strong>Posted today:</strong></td>
					<td class="w-75"><?php echo pm_number_format(recent_dates('is_today', 'pm_comments', 'id', 'added')); ?></td>
				  </tr>
				  <tr>
					<td class="w-25"><strong>Posted yesterday:</strong></td>
					<td class="w-75"><?php echo pm_number_format(recent_dates('is_yesterday', 'pm_comments', 'id', 'added')); ?></td>
				  </tr>
				  <tr>
					<td class="w-25"><strong>#1 Comment Poster: </strong></td>
					<td class="w-75"><?php echo top1_commentor(); ?></td>
				  </tr>
				</table>
			</div>
		</div>

		<div class="col-sm-12 col-md-6">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title">Articles</h5>
				</div>
				<table cellpadding="0" cellspacing="0" width="100%" class="table table-xs">
				  <tr>
					<td class="w-25"><strong>Total articles:</strong></td>
					<td class="w-75"><?php echo $config['total_articles']; ?> <a href="articles.php" class="text-grey opacity-80"><i class="mi-input"></i></a></td>
				  </tr>
				  <tr>
					<td class="w-25"><strong>Most commented:</strong></td>
					<td class="w-75">
						<?php
						$sql = "SELECT uniq_id, COUNT(*) as total 
								 FROM pm_comments 
								 WHERE uniq_id LIKE 'article-%' 
								 GROUP BY uniq_id 
								 ORDER BY total DESC 
								 LIMIT 1";
						$result = mysql_query($sql);
						$row = mysql_fetch_assoc($result);
						mysql_free_result($result);
				
						if ($row['uniq_id'])
						{
							$row['article_id'] = str_replace('article-', '', $row['uniq_id']);
							$article = get_article($row['article_id']);
							
							echo '<a href="comments.php?vid=article-'. $article['id']. '">'. $article['title'] .' ('. $row['total'] .')</a>';
						}
						else
						{
							echo 'N/A yet.';
						}
						?>
					</td>
				  </tr>
				</table>
			</div>

			<div class="card">
				<div class="card-header">
					<h5 class="card-title">Members</h5>
				</div>
				<table cellpadding="0" cellspacing="0" width="100%" class="table table-xs">
				  <tr>
					<td class="w-25"><strong>Total members:</strong></td>
					<td class="w-75"><?php echo pm_number_format(count_entries('pm_users', '', '')); ?> <a href="users.php" class="text-grey opacity-80"><i class="mi-input"></i></a></td>
				  </tr>
				  <tr>
					<td class="w-25"><strong>Joined today:</strong></td>
					<td class="w-75"><?php echo pm_number_format(recent_dates('is_today', 'pm_users', 'id', 'reg_date')); ?></td>
				  </tr>
				  <tr>
					<td class="w-25"><strong>Joined yesterday:</strong></td>
					<td class="w-75"><?php echo pm_number_format(recent_dates('is_yesterday', 'pm_users', 'id', 'reg_date')); ?></td>
				  </tr>
				  <tr>
					<td class="w-25"><strong>Joined this month:</strong></td>
					<td class="w-75"><?php echo pm_number_format(recent_dates('is_thismonth', 'pm_users', 'id', 'reg_date')); ?></td>
				  </tr>
				  <tr>
					<td class="w-25"><strong>Avatars uploaded:</strong></td>
					<td class="w-75"><?php echo pm_number_format(count_avatars()); ?></td>
				  </tr>
				</table>
			</div>

			<div class="card">
				<div class="card-header"><h5 class="card-title">Searches</h5></div>
				<table cellpadding="0" cellspacing="0" width="100%" class="table table-xs">
				  <tr>
					<td class="w-25"><strong>Searches:</strong></td>
					<td class="w-75"><?php echo pm_number_format(count_entries('pm_searches', '', '')); ?> <a href="search-log.php" class="text-grey opacity-80"><i class="mi-input"></i></a></td>
				  </tr>
				  <tr>
					<td class="w-25"><strong>Most popular query:</strong></td>
					<td class="w-75"><?php echo top1_search(); ?></td>
				  </tr>
				</table>
			</div>
		</div>
	</div>
</div><!-- .content -->
<?php
include('footer.php');