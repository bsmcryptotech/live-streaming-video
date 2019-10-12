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

$showm = 'mod_article';
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$_page_title = 'Manage articles';
include('header.php');

$action	= (int) $_GET['action'];
$page	= (int) $_GET['page'];

if($page == 0)
	$page = 1;


$total_articles = 0;
//	articles per page
$limit = get_admin_ui_prefs('articles_pp');
$from = $page * $limit - ($limit);

$filter = '';
$filters = array('public', 'private', 'mostviewed', 'category', 'sticky', 'restricted');
$filter_value = '';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
	
	if ($filter == 'category' && $filter_value == '')
	{
		$filter = '';
	}
}


//	Batch delete
if ($_POST['submit'] == "Delete" && ! csrfguard_check_referer('_admin_articles'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
else if ('' != $_POST['submit'] && $_POST['submit'] == "Delete")
{
	$total_checkboxes = pm_count($_POST['checkboxes']);
	if($total_checkboxes > 0 && (is_admin() || (is_moderator() && mod_can('manage_articles'))))
	{
		$article_ids = array();
		foreach ($_POST['checkboxes'] as $k => $id)
		{
			$article_ids[] = (int) $id;
		}
		
		$result = mass_delete_articles($article_ids);
		
		if ($result['type'] == 'error')
		{
			$info_msg = pm_alert_error($result['msg']);
		}
		else
		{
			$info_msg = pm_alert_success($result['msg']);
		}
	}
	else if ($total_checkboxes > 0 && (is_editor() || (is_moderator() && mod_cannot('manage_articles'))))
	{
		$info_msg = pm_alert_error('You are not allowed delete articles.');
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}

if ('' != $_POST['submit'] && $_POST['submit'] == 'Search')
{
	$articles = list_articles($_POST['keywords'], $_POST['search_type'], $from , $limit); 
	$total_articles = pm_count($articles);
}
else
{
	switch ($filter)
	{
		default:
		case 'mostviewed':
		
			$total_articles = $config['total_articles'];
		
		break;
		
		case 'private':
		
			$total_articles = count_entries('art_articles', 'status', '0');
		
		break;

		case 'public':
		
			$total_articles = count_entries('art_articles', 'status', '1');
		
		break;

		case 'sticky':
		
			$total_articles = count_entries('art_articles', 'featured', '1');
		
		break;

		case 'restricted':
		
			$total_articles = count_entries('art_articles', 'restricted', '1');
		
		break;
		
		case 'category':
		
			$filter_value = (int) $filter_value;
			if ($filter_value > 0)
			{
				$sql = "SELECT COUNT(*) as total_found 
						FROM art_articles  
						WHERE category LIKE '". $filter_value ."' 
						   OR category LIKE '". $filter_value .",%' 
						   OR category LIKE '%,". $filter_value ."' 
						   OR category LIKE '%,". $filter_value .",%'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				$total_articles = $row['total_found'];
				unset($row, $result, $sql);
			}
			else if ($_GET['fv'] == '0')
			{
				$total_articles = count_entries('art_articles', 'category', '0');
			}
			else
			{
				$total_articles = 0;
			}
		
		break;
	}
	$articles = list_articles('', '', $from , $limit, $filter, $filter_value); 
}

// generate smart pagination
$filename = 'articles.php';
$pagination = '';

if(!isset($_POST['submit'])) 
	$pagination = a_generate_smart_pagination($page, $total_articles, $limit, 5, $filename, '&filter='. $filter .'&fv='. $filter_value);


if ($_GET['action'] == "deleted") 
{
	$info_msg = pm_alert_success('Comments were deleted.');
}

if ($_GET['action'] == "badtoken") 
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please revisit this page and try again.');
}
?>

<!-- Main content -->
<div class="content-wrapper">
<div class="page-header-wrapper"> 
	<div class="page-header page-header-light">
		<div class="page-header-content header-elements-md-inline">
		<div class="d-flex justify-content-between w-100">		
			<div class="page-title d-flex">
				<h4><a href="<?php echo _URL; ?>/article.php" class="open-in-new" target="_blank" data-popup="tooltip" data-placement="right" data-original-title="Jump to Articles in Front-End"><span class="font-weight-semibold">Articles</span> <i class="mi-open-in-new"></i> </a> <a href="edit-article.php?do=new" class="badge badge-success badge-addnew font-size-sm ml-2">+ add new</a></h4>
			</div>
			<a href="#" class="header-elements-toggle text-default d-md-none"><i class="mi-search"></i></a>
			<div class="header-elements with-search d-none">
				<div class="d-flex-inline align-self-center ml-auto">
					<form name="search" action="articles.php" method="post" class="form-search-listing form-inline float-right">
						<div class="input-group input-group-sm input-group-search">
							<div class="input-group-append">
								<input name="keywords" type="text" value="<?php echo $_POST['keywords']; ?>" class="search-query search-quez input-medium form-control form-control-sm border-right-0" placeholder="Enter keyword" id="form-search-input" />
							</div>
							<select name="search_type" class="form-control form-control-sm border-left-0 border-right-0">
								<option value="title" <?php echo ($_POST['search_type'] == 'title') ? 'selected="selected"' : '';?>>Title</option>
								<option value="content" <?php echo ($_POST['search_type'] == 'content') ? 'selected="selected"' : '';?>>Description</option>
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
					<a href="articles.php" class="breadcrumb-item"><span class="breadcrumb-item active">Articles</span></a>
				</div>
			</div>

			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
					<a href="#" id="show-help-assist" class="breadcrumb-elements-item"><i class="mi-help-outline text-muted"></i></a>
				</div>
			</div>
		</div>

<?php if ( $total_articles > 0 || ! empty($filter)) : ?> <!--Start ifempty-->
		<div class="row p-0 mt-1 ml-0 mr-2">
			<div class="col-md-12">
			<div class="d-horizontal-scroll">
				<ul class="nav nav-pills nav-pills-bottom m-0">
					<li class="nav-item">
						<a class="nav-link <?php echo (empty($filter) || $filter == 'public' || $filter == 'mostviewed') ? 'active' : ''; ?>" href="articles.php">All <?php echo (empty($filter) || $filter == 'public' || $filter == 'mostviewed') ? '<span class="text-muted">('. pm_number_format($total_articles) .')</span>' : ''; ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo ($filter == 'private') ? 'active' : ''; ?>" href="articles.php?filter=private">Drafts <?php echo ($filter == 'private') ? '<span class="text-muted">('. pm_number_format($total_articles) .')</span>' : ''; ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo ($filter == 'sticky') ? 'active' : '';?>" href="articles.php?filter=sticky">Sticky <?php echo ($filter == 'sticky') ? '<span class="text-muted">('. pm_number_format($total_articles) .')</span>' : ''; ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo ($filter == 'restricted') ? 'active' : '';?>" href="articles.php?filter=restricted">Restricted <?php echo ($filter == 'restricted') ? '<span class="text-muted">('. pm_number_format($total_articles) .')</span>' : ''; ?></a>
					</li>

					<li class="nav-item dropdown">
						<a class="nav-link font-weight-semibold dropdown-toggle <?php echo ($filter == 'category') ? 'active' : ''?>" data-toggle="dropdown" href="#" id="dd-category" role="button" aria-haspopup="true" aria-expanded="false">Category</a>
						<ul class="dropdown-menu dropdown-menu-scroll" aria-labelledby="dd-category">
							<?php
							$categories = art_get_categories();
							foreach ($categories as $id => $cat)
							{
								$option = '<li class="nav-item">';
								$option = '<a href="articles.php?filter=category&fv='. $id .'" class="dropdown-item ';
								if ($filter_value == $id && $filter == 'category')
								{
								  $option .= ' active ';
								}
								$option .= '">'. $cat['name'] .'</a>';
								$option .= '</li>';
								echo $option;
							}
							?>
						</ul>
					</li>
				</ul>
				</div>
			</div>
		</div>
<?php endif; ?> <!--End ifempty-->
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
						<p>This module can be enabled or disabled depending on your needs (see settings page to disable it) . You can use the articles module as a blog or an article database depending on your needs. Using the built-in user ranks, you can assign Editors which can administer your articles/blog.</p>
						<p>Note: Posting unique and relevant content regularly will help your SEO efforts.</p>
					</div>
					<div class="tab-pane" id="v-pills-two" role="tabpanel" aria-labelledby="v-pills-tab-help-two">
						<p>Listing pages such as this one contain a filtering area which comes in handy when dealing with a large number of entries. The filtering options is always represented by a gear icon positioned on the top right area of the listings table. Clicking this icon usually reveals a search form and one or more drop-down filters.</p>
					</div>

				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->

	<!-- Content area -->
	<div class="content content-full-width">

<?php 
if ( ! _MOD_ARTICLE)
{
	echo pm_alert_info('The Article Module is currently disabled. You can enable it from <a href="settings.php?view=t6">Settings / Available Modules</a>.');
}

echo $info_msg;

if ( $total_articles == 0 && empty($filter)) : ?> <!--Start ifempty-->

<div class="align-middle text-center mt-3 pt-3">
	<i class="icon-stack-text icon-3x text-muted mb-3"></i>
	<h6 class="font-weight-semibold text-grey mb-1"><?php echo (!empty($_POST['keywords'])) ? 'No such articles found' : 'No articles yet';?></h6>
	<p class="text-grey mb-3 pb-1">Post your first article now.</p>
	<a href="edit-article.php?do=new" class="btn btn-sm bg-blue border-blue border-2 rounded font-weight-semibold">Write Article</a>
</div>

<?php else : ?> <!--Else ifempty-->

<div id="display_result" style="display:none;"></div>

<div class="card card-blanche">
	<div class="card-body">
		<div class="row">
			<div class="col-sm-12 col-md-6">

			<?php if ( ! empty($_POST['keywords'])) : ?>
			<h5 class="font-weight-semibold mt-2">SEARCH RESULTS FOR <mark><?php echo $_POST['keywords']; ?></mark> <a href="#" onClick="parent.location='articles.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear search results"><i class="icon-cancel-circle2"></i></a></h5>
			<?php endif; ?>

			<?php if ( ! empty($filter) ) : ?>
			<h5 class="font-weight-semibold mt-2">FILTERED RESULTS <a href="#" onClick="parent.location='articles.php'" class="text-muted opacity-50" data-popup="tooltip" data-original-title="Clear filter"><i class="icon-cancel-circle2"></i></a></h5>
			<?php endif; ?>

			</div>

			<div class="col-sm-12 col-md-6 d-none d-md-block">
				<div class="float-right mb-3">
					<form name="articles_per_page" action="articles.php" method="get" class="form-inline float-right">
						<input type="hidden" name="ui_pref" value="articles_pp" />
						<label class="mr-1" for="inlineFormCustomSelectPref">Articles/page</label>
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
		</div> <!--.row with filters -->
	</div><!--.card-body-->

		<div class="datatable-scroll">
		<form name="articles_checkboxes" id="articles_checkboxes" action="articles.php?page=<?php echo $page;?>" method="post">
			<table cellpadding="0" cellspacing="0" width="100%" class="table table-md table-striped table-columned pm-tables tablesorter">
				<thead>
				<tr>
					<th align="center" class="text-center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
					<th width="40%">Title</th>
					<th width="5%" class="text-center">
						<a href="articles.php?filter=mostviewed&fv=<?php echo ($filter_value == 'desc' && $filter == 'views') ? 'asc' : 'desc';?>" data-popup="tooltip" data-html="true" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'views') ? 'ascending' : 'descending';?>">Views</a></th>
					</th>
					<th width="16%">Categories</th>
					<th width="7%" class="text-center">Author</th>	
					<th width="90" class="text-center">
						<a href="articles.php?filter=public&fv=<?php echo ($filter_value == 'desc' && $filter == 'public') ? 'asc' : 'desc';?>" data-popup="tooltip" data-html="true" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'public') ? 'ascending' : 'descending';?>">Added</a></th>
					</th>
					<th width="150" class="text-center">Comments</th>
					<th width="" style="width:90px;" class="text-center">Action</th>
				</tr>
				</thead>
					<tbody>
						<?php 
						/*
						 *  List articles
						 */ 
						if ( ! array_key_exists('type', $articles) && $total_articles > 0)
						{
							$alt = 1;
							
							foreach ($articles as $k => $article)
							{
								$col = ($alt % 2) ? 'table_row1' : 'table_row2';
								$alt++;
								
								$total_comments = count_entries('pm_comments', 'uniq_id', 'article-'.$article['id']);

								//	Table row
								$table_row = '';
								if ($article['status'] == 0)
								{
									$table_row .= '<tr class="scheduled '. $col .'" id="article-'. $article['id'] .'">';
								} elseif ($article['restricted'] == '1') {
									$table_row .= '<tr class="private '. $col .'" id="article-'. $article['id'] .'">';
								} else {
									$table_row .= '<tr class="'. $col .'" id="article-'. $article['id'] .'">';
								}
								echo $table_row;
								?>
								
								 <td align="center" class="text-center" width="3%">
									<input name="checkboxes[]" type="checkbox" value="<?php echo $article['id']; ?>" />
								 </td>
								 <td>
									<?php if ($article['featured'] == '1') : ?>
										<a href="articles.php?filter=sticky" rel="tooltip" title="Click to list only sticky articles"><span class="badge badge-primary">STICKY</span></a> 
									<?php endif; ?>
									<?php if ($article['status'] == 0) : ?>
										<a href="articles.php?filter=private" rel="tooltip" title="Click to list only drafts. This is a private article (Draft). Only Administrators and Editors can see this article"><span class="badge badge-secondary mr-1">DRAFT</span></a>
									<?php endif; ?>
									<?php if ($article['restricted'] == '1') : ?>
										<a href="articles.php?filter=restricted" rel="tooltip" title="Click to list only private articles. Only registered users can read this article."><span class="badge badge-dark mr-1">PRIVATE</span></a>
									<?php endif; ?>
									<a href="<?php echo _URL.'/article-read.php?a='. $article['id']; if ( ! _MOD_ARTICLE || $article['status'] == 0 || $article['date'] > time()) echo '&mode=preview'; ?>" target="_blank"><?php echo htmlspecialchars($article['title']); ?></a>
									<?php if ($article['date'] > time()): ?>
										&mdash; <small>Not published yet</small>
									<?php endif;?>
								 </td>
								 <td align="center" class="text-center"><?php echo pm_number_format($article['views']); ?></td>
								 <td>
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
								</td>
								<td align="center" class="text-center">
								  <?php 
									$author = fetch_user_advanced($article['author']);
									
									echo '<a href="edit-user.php?uid='. $author['id'] .'" title="Edit">'. $author['username'] .'</a>';
								  ?>
								</td>
								<td align="center" class="text-center">
								<span rel="tooltip" title="<?php echo date('l, F j, Y g:i A', $article['date']); ?>">
								<?php echo date('M d, Y', $article['date']); ?>
								</span>
								</td>
								<td align="center" class="text-center"> 
										 <a href="comments.php?vid=<?php echo 'article-'.$article['id'];?>" title="View comments" class="b_view">View</a> 
								  <?php 
								  if (is_admin() || (is_moderator() && mod_can('manage_comments')))
								  {
									?>
									| <a href="#" title="Delete all comments" onClick='del_video_comments("article-<?php echo $article['id'];?>", "<?php echo $page;?>")'>Delete (<?php echo $total_comments; ?>)</a>
									<?php
								  }
								  ?>
								</td>
								<td align="center" class="table-col-action" style="text-align:center; width: 90px;">
									<div class="list-icons">
										<a href="edit-article.php?do=edit&id=<?php echo $article['id'];?>" class="list-icons-item mr-2" rel="tooltip" title="Edit"><i class="icon-pencil7"></i></a>
										<a href="#" onclick="onpage_delete_article('<?php echo $article['id']; ?>', '#display_result', '#article-<?php echo $article['id'];?>')" class="list-icons-item text-warning" rel="tooltip" title="Delete"><i class="icon-bin"></i></a>
									</div>
								</td>
								</tr>
								
								<?php
							}
						}
						else	//	Error?
						{
							if (strlen($articles['msg']) > 0)
							{
								echo pm_alert_error($articles['msg']);
							}
							
							if ($total_articles == 0)
							{
								?>
								<tr>
								 <td colspan="9" align="center" class="text-center">
								 No articles found.
								 </td>
								</tr>
								<?php
							}
						}
						?>
						
						<?php if ($pagination != '') : ?>
						<tr class="tablePagination">
							<td colspan="9" class="tableFooter">
								<div class="table table-md table-striped table-columned pm-tables tablesorter"><?php echo $pagination; ?></div>
							</td>
						</tr>
						<?php endif; ?>
					</tbody>
			</table>
		</div><!--.datatable-scroll-->

		<div class="datatable-footer">
			<div id="stack-controls" class="row list-controls">
				<div class="col-md-12">
					<div class="float-right form-inline">
						<button type="submit" name="submit" value="Delete" class="btn btn-sm btn-danger" onClick="return confirm_delete_all();">Delete</button>
					</div>
				</div>
			</div><!-- #list-controls -->
			<?php echo csrfguard_form('_admin_articles'); ?>
		</div>
		</form>

</div><!--.card-->
<?php endif ?> <!--End ifempty-->
</div><!-- .content -->
<?php
include('footer.php');