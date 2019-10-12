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

$showm = '4';
$_page_title = 'Abuse prevention';
include('header.php');
$list = ''; 
$content = '';
$words = '';
$words_list = array();

$list = $_GET['list'];

if($list == ''){
	$list = 'censor_words';
}

switch($list){ 
	default:
	case 'censored':
		$file = './temp/censored-words.txt';
		$title = 'Censored words';
	break;
	
	case 'blacklist':		
		$file = './temp/blacklisted-words.txt';
		$title = 'Blacklist';
	break;
}//	end switch

if($_POST['Submit'] == "Save"){
	$words = $_POST['words'];
	
	$temp_arr = explode("\n", $words);
	
	for($i = 0; $i < pm_count($temp_arr); $i++){
		if(trim($temp_arr[$i]) != '' && strlen($temp_arr[$i]) > 1) 
			$words_list[] = $temp_arr[$i];
	}	
	$fp = fopen($file, "w");
	if(!$fp) { 
		echo '<div class="alert">Sorry, file <strong>'.$file.'</strong> cannot be opened. Check if <strong>'.$file.'</strong> was uploaded and if it\'s writable (CHMOD 0777)</div>';
		include('footer.php');
		exit();
	}
	$line = '';
	for($i = 0; $i < pm_count($words_list); $i++){
		if($i != pm_count($words_list)-1)
			$line = $words_list[$i]."\n";
		else
			$line = $words_list[$i];
		fwrite($fp, $line, strlen($line));
	}
	fclose($fp);
	$info_msg = pm_alert_success('The list was updated successfully.');
}
else{
	$fp = @fopen($file, "r");
	if ( ! $fp) 
	{ 
		echo pm_alert_error('Sorry, file <code>'.$file.'</code> cannot be opened. Check if the file exists and if it\'s writable (CHMOD 0777).');
		include('footer.php');
		exit;
	}
	while ( ! feof($fp))
	{
		$content .= fread($fp, 4096);
	}
	fclose($fp);
}

function read_censored_words($filename) 
{
		$fp = @fopen($filename, "r");
		$content = '';
		if ( ! $fp) 
		{ 
			return pm_alert_error('Could not open file <code>'.$file.'</code>. Make sure the file exists and if it\'s writable (CHMOD 0777).');
		}
		while ( ! feof($fp))
		{
			$content .= fread($fp, 4096);
		}
		fclose($fp);

		return $content;
}
?>
	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
	<div class="page-header-wrapper"> 
		<div class="page-header page-header-light">
			<div class="page-header-content header-elements-md-inline">
			<div class="d-flex justify-content-between w-100">
				<div class="page-title d-flex">
					<h4><?php echo $_page_title; ?></h4>
				</div>
			</div>
			</div>

			<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
				<div class="d-flex">
					<div class="breadcrumb">
						<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
						<a href="comments.php" class="breadcrumb-item">Comments</a>
						<a href="blacklist.php" class="breadcrumb-item"><span class="breadcrumb-item active"><?php echo $_page_title; ?></span></a>
					</div>
				</div>
			</div>
		</div>
		<!-- /page header -->
	</div><!--.page-header-wrapper-->

		<!-- Content area -->
		<div class="content">

<?php
if($info_msg) {
	echo $info_msg; 
}
?>

		<?php echo pm_alert_info('Keep your site clean by filtering in any obscene or unwanted words from user comments. This also helps with SEO rankings.'); ?>


		<div class="card">

			<div class="card-header">
				<h4 class="sub-heading">Blacklisted words</h4>
			</div>


			<form name="form" method="post" action="blacklist.php?list=blacklist" class="form">
			<div class="card-body">
				
				<span class="d-block form-text mb-1">Comment containing any of the blacklisted words will be deleted automatically. They won't appear at all.</span>
				<textarea name="words" class="form-control" rows="5"><?php echo read_censored_words("./temp/blacklisted-words.txt"); ?></textarea>
				<span class="d-block form-text text-muted">Use one word per line without any punctuation</span>

				<input type="submit" name="Submit" value="Save" class="btn btn-success mt-3 mb-3" />
				</form>

				<h4 class="sub-heading">Censored words</h4>

				<form name="form" method="post" action="blacklist.php?list=censored" class="form">
					<span class="d-block form-text mb-1">Censored words will be replaced with '***' but the rest of the comment will still be posted.</span>
					<textarea name="words" class="form-control" rows="5"><?php echo read_censored_words("./temp/censored-words.txt"); ?></textarea>
					<span class="d-block form-text text-muted">Use one word per line without any punctuation</span>
				<input type="submit" name="Submit" value="Save" class="btn btn-success mt-3 mb-3" />
			</div>

			</form>









		</div>   
		</div>
		<!-- /content area -->
<?php
include('footer.php');