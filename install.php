<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | phpSugar, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: phpSugar (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2016 PhpSugar.com. All rights reserved.
// +------------------------------------------------------------------------+
header("Expires: Mon, 1 Jan 1999 01:01:01 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

define('PM_DOING_INSTALL', true);
define('IGNORE_MOBILE', true);

if (file_exists('config.php'))
{
	@include_once('config.php');
}

if ( ! extension_loaded('mysql') && ! function_exists('mysql_connect'))
{
	include_once(ABSPATH .'include/mysql2i.class.php');
}

if ( ! defined('_ADMIN_FOLDER')) 
{
	define('_ADMIN_FOLDER', 'admin');
}

function pm_file_writtable($path) {

	if ( ! is_writable(ABSPATH . $path) ) {
		$icon = 'fa fa-file-o';
		$tmp_parts = explode('.', $path);
		$ext = array_pop($tmp_parts);
		$ext = strtolower($ext);
		if (strlen($ext) > 4)
		{
			$icon = 'fa fa-folder-open-o';
		}
		
		echo '<li><i class="'. $icon .'"></i> <strong>'.$path.'</strong> should be writable. Set CHMOD 0755.</li>';
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Installing PHP Melody</title>
<link rel="shortcut icon" type="image/ico" href="<?php echo _ADMIN_FOLDER; ?>/img/favicon.ico" />
<!-- <link rel="stylesheet" type="text/css" media="screen" href="templates/default/css/bootstrap.min.css" /> -->
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo _ADMIN_FOLDER; ?>/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo _ADMIN_FOLDER; ?>/css/bootstrap-admin.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo _ADMIN_FOLDER; ?>/css/admin-melody.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
<link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700|Roboto:400,300,500,700' rel='stylesheet' type='text/css' media='all' />
<link href='//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet' type='text/css' media='all' />
</head>
<body>
<style type="text/css">
body{font-family:"Roboto",Arial,Helvetica,sans-serif;background-image:none;background-color:#0060a2}header{margin:0 auto;padding:40px 0;text-align:center}#container{position:relative;margin:0 auto;padding:40px;background:#f0f7fc;border:3px solid #0e7ac4;width:650px}#container h1{text-align:left;font-weight:400;font-size:20px;text-shadow:0 1px 0 #FFF;color:#369ef1;display:block;margin-bottom:20px}#container h1 strong{font-weight:700}#container h2{font-family:"Open Sans",Verdana,Geneva,sans-serif;letter-spacing:-1px;border-bottom:0 none;color:#53a5d6;margin:10px 0;padding:0}.lead{color:#444;font-size:1.2em}.lead a{color:#53a5d6}ul{list-style-type:none;margin:0}ul li{position:relative;border-bottom:1px solid #d9e7f3;padding:5px 0;margin:2px 0;color:#555;text-shadow:0 1px 0 #FFF;-moz-text-shadow:0 1px 0 #FFF}ul li .label{position:absolute;right:0;top:2px;padding:6px;border-radius:100px}ul.file-check{color:#888;font-size:11px;margin:5px;position:relative}ul.file-check li{border-bottom:0 none;margin:0;padding:1px 0}.fa-file-o,.pm-icon-folder-open{opacity:.5}.warn{color:#c76e34;font-family:Arial,Helvetica,sans-serif;font-size:11px;display:block}.error{background-color:#ffd5d5;border:1px solid #ffacad;color:#cf3738}.allright,.warning,.error{font-size:12px;line-height:19px;margin:6px 10px;display:block}#footer{font-size:11px;color:#999;padding:15px 0 0;border-top:1px dotted #EEE;text-align:center;margin-top:20px}#footer a:link,#footer a:visited{color:#777;text-decoration:none}#footer a:hover{color:#333;text-decoration:none}#footer p{margin-top:10px;font-size:11px}.rounded{border-radius:.1875rem!important}.badge{display:inline-block;padding:.3125rem .375rem;font-size:75%;font-weight:500;line-height:1;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.125rem}.badge:empty{display:none}.btn .badge{position:relative;top:-1px}.badge-pill{padding-right:.4375rem;padding-left:.4375rem;border-radius:10rem}.badge-primary{color:#fff;background-color:#2196f3}.badge-primary[href]:focus,.badge-primary[href]:hover{color:#fff;text-decoration:none}.badge-primary[href]:focus:not(.badge-light),.badge-primary[href]:hover:not(.badge-light){-webkit-box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset;box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset}.badge-secondary{color:#fff;background-color:#777}.badge-secondary[href]:focus,.badge-secondary[href]:hover{color:#fff;text-decoration:none}.badge-secondary[href]:focus:not(.badge-light),.badge-secondary[href]:hover:not(.badge-light){-webkit-box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset;box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset}.badge-success{color:#fff;background-color:#4caf50}.badge-success[href]:focus,.badge-success[href]:hover{color:#fff;text-decoration:none}.badge-success[href]:focus:not(.badge-light),.badge-success[href]:hover:not(.badge-light){-webkit-box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset;box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset}.badge-info{color:#fff;background-color:#00bcd4}.badge-info[href]:focus,.badge-info[href]:hover{color:#fff;text-decoration:none}.badge-info[href]:focus:not(.badge-light),.badge-info[href]:hover:not(.badge-light){-webkit-box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset;box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset}.badge-warning{color:#fff;background-color:#ff7043}.badge-warning[href]:focus,.badge-warning[href]:hover{color:#fff;text-decoration:none}.badge-warning[href]:focus:not(.badge-light),.badge-warning[href]:hover:not(.badge-light){-webkit-box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset;box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset}.badge-danger{color:#fff;background-color:#f44336}.badge-danger[href]:focus,.badge-danger[href]:hover{color:#fff;text-decoration:none}.badge-danger[href]:focus:not(.badge-light),.badge-danger[href]:hover:not(.badge-light){-webkit-box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset;box-shadow:0 0 0 62.5rem rgba(0,0,0,.075) inset}.margin-warning{margin-right:6px;margin-top:2px}.circle-loader{border:1px solid rgba(0,0,0,.2);border-left-color:#5cb85c;animation:loader-spin 1.2s infinite linear;position:relative;display:inline-block;vertical-align:top;border-radius:50%;width:1.7em;height:1.7em}.load-complete{-webkit-animation:none;animation:none;border-color:#5cb85c;transition:border 500ms ease-out}.checkmark{display:none}.checkmark.draw:after{animation-duration:800ms;animation-timing-function:ease;animation-name:checkmark;transform:scaleX(-1) rotate(135deg)}.checkmark:after{opacity:1;height:.85em;width:.425em;transform-origin:left top;border-right:2px solid #5cb85c;border-top:2px solid #5cb85c;content:'';left:.3116666667em;top:.85em;position:absolute}@keyframes loader-spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}@keyframes checkmark{0%{height:0;width:0;opacity:1}20%{height:0;width:.425em;opacity:1}40%{height:.85em;width:.425em;opacity:1}100%{height:.85em;width:.425em;opacity:1}}
</style>
<script type="text/javascript">
$(document).ready(function(){
	var $this = $('.circle-loader');

	setTimeout(function() { 
		$('.circle-loader').toggleClass('load-complete');
		$('.checkmark').toggle();
	}, 1300);
});
</script>

<header>
<img src="<?php echo _ADMIN_FOLDER; ?>/img/install-logo.png" width="179" height="34" align="PHP Melody" />
</header>

<div id="container" class="border-radius5">
<?php
$step = $_GET['step'];

if ( empty($step) ) {
	$step = 1;
}

switch($step){
default:
case 1:
		$error = 0;
		echo "<h1><strong>Installation</strong>: Checking your setup...</h1>";
		echo "<ul class='m-0 p-0'>";
		echo "<li>Checking the <strong>PHP version</strong> ... ";
		if ( version_compare(PHP_VERSION, '5.4', '<'))
		{
			$error = 1;
			echo "<span class=\"warn\">PHP version 5.4 or later is required. Update your PHP version before installing PHP Melody. <br /> You should be able to change your PHP version from your webhost account.</span> <span class=\"badge badge-warning rounded pull-right float-right\"><i class='fa fa-exclamation-triangle'></i></span>";
		}
		else
		{
			echo '<div class="float-right pull-right"><div class="circle-loader"><div class="checkmark draw"></div></div></div>';
			echo '<div class="clearfix"></div>';
		}
		echo "<li>Checking if <strong>config.php</strong> exists ... ";
		if( ! file_exists('config.php')) { 
			$error = 1;
			echo '<span class="text-danger">not found</span>.';
			echo '<span class="pull-right float-right text-warning margin-warning"><i class="fa fa-exclamation-triangle"></i></span>';
		}
		else {
			echo '<div class="float-right pull-right"><div class="circle-loader"><div class="checkmark draw"></div></div></div>';
			echo '<div class="clearfix"></div>';
		}
		echo "</li>";
		
		
		require_once('include/functions.php');
		
		echo "<li>Checking if <strong>install.sql</strong> exists ... ";
		if( ! file_exists('install.sql')) { 
			$error = 1;
			echo '<span class="text-danger">not found</span>.';
			echo '<span class="pull-right float-right text-warning margin-warning"><i class="fa fa-exclamation-triangle"></i></span>';
		}
		else {
			echo '<div class="float-right pull-right"><div class="circle-loader"><div class="checkmark draw"></div></div></div>';
			echo '<div class="clearfix"></div>';
		}
		echo "</li>";
		
		echo "<li>Checking <strong>database connection</strong> ... ";
		$connection = @mysql_connect($db_host, $db_user, $db_pass);
		if( ! $connection )  {
			$error = 1;
				echo '<span class="pull-right float-right text-warning margin-warning"><i class="fa fa-exclamation-triangle"></i></span>';
				echo '<span class="text-danger warn">'. mysql_error() .'</span>';
			}
			else {
				echo '<div class="float-right pull-right"><div class="circle-loader"><div class="checkmark draw"></div></div></div>';
				echo '<div class="clearfix"></div>';
			}
			echo "</li>";
		
		echo "<li>Setting the <strong>database collation</strong> ... ";
		$connection = @mysql_connect($db_host, $db_user, $db_pass);
			if( ! $connection )  {
				$error = 1;
				echo '<span class="pull-right float-right text-warning margin-warning"><i class="fa fa-exclamation-triangle"></i></span>';
				echo '<span class="text-danger warn">'. mysql_error() .'</span>';
			}
			else {
				@mysql_query(" ALTER DATABASE `".$db_name."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ");
				echo '<div class="float-right pull-right"><div class="circle-loader"><div class="checkmark draw"></div></div></div>';
				echo '<div class="clearfix"></div>';
			}			
			echo "</li>";
		
		echo "<li>Checking the <strong>MySQL database</strong> ... ";
		$db = @mysql_select_db($db_name);
		if( ! $db ) {
			$error = 1;
			echo '<span class="pull-right float-right text-warning margin-warning"><i class="fa fa-exclamation-triangle"></i></span>';
			echo '<span class="text-danger warn">'. mysql_error() .'</span>';
		}
		else {
			echo '<div class="float-right pull-right"><div class="circle-loader"><div class="checkmark draw"></div></div></div>';
			echo '<div class="clearfix"></div>';
		}
		echo "</li>";



				
			echo '<ul class="file-check mx-0 px-0 my-3">';
					pm_file_writtable("sitemap-index.xml");
					pm_file_writtable("video-sitemap-index.xml");
					pm_file_writtable(_ADMIN_FOLDER ."/temp/");
					pm_file_writtable(_ADMIN_FOLDER ."/temp/blacklisted-words.txt");
					pm_file_writtable(_ADMIN_FOLDER ."/temp/censored-words.txt");
					pm_file_writtable(_ADMIN_FOLDER ."/temp/upload-file.tmp");
					pm_file_writtable(_ADMIN_FOLDER ."/temp/embedparams.xml");
					pm_file_writtable("uploads/");
					pm_file_writtable("uploads/articles/");
					pm_file_writtable("uploads/avatars/");
					pm_file_writtable("uploads/covers/");
					pm_file_writtable("uploads/thumbs/");
					pm_file_writtable("uploads/videos/");
					pm_file_writtable("Smarty/templates_c/");
			echo "</ul>";	
		
		
			$sql = "SELECT id FROM pm_config WHERE id = '1'";
			$result = @mysql_query($sql);
			
		if( $result ) {	
			$error = 1;
			echo "<p class=\"alert alpha-warning border-2 border-warning\">The installation cannot begin because MySQL tables from a previous PHP Melody installation were detected in this database (<strong>".$db_name."</strong>). To re-install, use another database or empty this database (<strong>".$db_name."</strong>). Click '<strong>Retry</strong>' to restart the process.</p>";
		}
		if ( _CUSTOMER_ID == '' || _CUSTOMER_ID == 'YCUSTOMER_ID' || _CUSTOMER_ID == 'your_customer_id_here' ){
			$error = 1;
			echo "<p class=\"alert alpha-warning border-2 border-warning\">The installation cannot begin because your <strong>Customer ID</strong> is missing from <strong><em>config.php</em></strong>. <br /> Add your Customer ID in <strong><em>config.php</em></strong> as described in the Installation Manual by updating the following line:<br /><br /> <code>define('_CUSTOMER_ID', 'YOUR_CUSTOMER_ID');</code></p>";
		}
		if( !$error ){
			echo "<p></p>";
			echo "<p class=\"alert alpha-success border-2 border-success text-dark\"><strong>Excellent</strong>! Everything is in order. Click <strong>Continue</strong> to build your MySQL database.</p><br />";
			echo "<div align=\"center\"><a href=\"install.php?step=2&start=1&pointer=0\" class=\"btn btn-primary\"><strong>Continue &rarr;</strong></a></div>";
		}
		else {
			echo "<br /><center><p><a href=\"install.php\" class=\"btn btn-danger\"><strong>Retry</strong></a></p></center>";
		}

break;

//Step 2 - start importing the database;
case 2:
		require_once('config.php');
		require_once('include/functions.php');

		function get_percentage($full, $part){
			$percent = ceil(($part * 100)/$full);
			return $percent;
		}

		$connection = db_connect();

		ini_set("auto_detect_line_endings", true);
	
		$filename 	 =	"install.sql";
		$linesize 	 =	65536;
		$querybatch	 =	300;
		$linesbatch  = 	2000;
		$sleep 		 = 	500;

		
		$comment[0]="#";
		$comment[1]="-- ";
		
		?>
  <p class="alert alpha-warning border-2 border-warning" id="please_wait"><img src="<?php echo _ADMIN_FOLDER; ?>/img/ico-loading.gif" width="16" height="16" border="0" align="absmiddle" /> Please wait. Your PHP Melody site is being installed...</p>
  <?php
		
		$error = 0;

		if (!$error && isset($filename)){ 
		//open the .sql file
		  if ( !$file = @fopen($filename,"rt")){
			echo "<p class=\"alert alpha-warning border-2 border-warning\">Cannot open <strong>".$filename."</strong>";
			if( !file_exists($filename) )
				echo "<br /> It seems that the file does not exist! Please check that you've uploaded all the files.";
			echo "<br />The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
			$error = 1;
		  }
			
		  elseif ( fseek($file, 0, SEEK_END)==0) {
			$filesize = ftell($file);
		  }

		  else{
			echo "<p class=\"alert alpha-warning border-2 border-warning\">Cannot get the filesize of ".$filename;
			echo "<br />The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
			$error = 1;
		  }
		}
		  if (!$error && isset($_REQUEST["start"]) && isset($_REQUEST["pointer"])){

		  if ($_REQUEST["pointer"] > $filesize){
//			If the script ended here, it means that the file pointer is somewhere after the end of file. 
			$error = 1;
			echo "<p class=\"alert alpha-warning border-2 border-warning\">The file pointer is out of bounds.";
			echo "<br />The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
		  }
		
		  if (!$error && (fseek($file, $_REQUEST["pointer"]) != 0) ){
//			If the script ended here, it means that the file pointer could not be set at $_REQUEST["pointer"];
			$error = 1;
			echo "<br /><p class=\"alert alpha-warning border-2 border-warning\">The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
		  }
		
		  if (!$error){
			$query = "";
			$queries = 0;
			$linenumber = $_REQUEST["start"];
			$querylines = 0;
			$inparents = false;

			while (($linenumber < $_REQUEST["start"] + $linesbatch || $query!="") 
			   && ($dumpline=fgets($file, $linesize)) ){ 

			  $dumpline = str_replace("\r\n", "\n", $dumpline);
			  $dumpline = str_replace("\r", "\n", $dumpline);
			  
			  if ( !$inparents ){ 
			  
				$skipline = false;
				reset($comment);
				foreach ($comment as $comment_value)
				{ if (!$inparents && (trim($dumpline)=="" || strpos ($dumpline, $comment_value) === 0))
				  { $skipline=true;
					break;
				  }
				}
				if( $skipline ){
				  $linenumber++;
				  continue; 
				}
			  }
		
			  $dumpline_deslashed = str_replace ("\\\\","",$dumpline);
		
			  $parents = substr_count($dumpline_deslashed, "'")-substr_count ($dumpline_deslashed, "\\'");
			  
			  if( $parents%2 != 0 )
				$inparents=!$inparents;
			  $query .= $dumpline;

			  if (!$inparents)
				$querylines++;
			  
			  if ( $querylines > $querybatch)
			  {
//				If the script ended here, it means that the current query includes more than $querybatch dump lines. Possible cause: missing ";" (semicolon) after every dump line.
//				This shouldn't ever happen, but is better to be safe than sorry
				echo "<br /><p class=\"alert alpha-warning border-2 border-warning\">The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
				$error = 1;
				break;
			  }
		
			  if (preg_match("/;$/",trim($dumpline)) && !$inparents)
			  { if(!mysql_query(trim($query), $connection))
				{ 
					?>
					<script type="text/javascript">
					$(document).ready(function(){
									$('#please_wait').slideUp(300);
					});
					</script>
					<?php
				  echo "<p class=\"alert alpha-warning border-2 border-warning\">There was a problem during the installation process. The reported MySQL error is: <br />";
				  echo mysql_error();
				  echo "<br />The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
				  $error = 1;
				  break;
				}
				$queries++;
				$query="";
				$querylines=0;
			  }
			  $linenumber++;
			}
		  }
		
		  if (!$error){
			  $pointer = ftell($file);
			if (!$pointer){
//			If the script ended here, it means that it cannot read the file pointer offset.		
			  $error = 1;
			  $line_of_end = __LINE__;
			}
		  }
		
		  if ( !$error ){
			if ($linenumber < $_REQUEST["start"]+ $linesbatch){
			?>
			<script type="text/javascript">
			$(document).ready(function(){
				$('#please_wait').slideUp(200);
			});
			</script>
 		 <?php
			  echo "<h1>PHP Melody is installed and ready to go!</h1>";
				if((!@unlink($filename)) || (!@unlink("install.php"))){
				
				  echo "<p class=\"alert alpha-warning border-2 border-warning\">Before anything else, please remove the following files:<br />
						<i class=\"fa fa-file-o\"></i>  <strong>install.php</strong><br />
						<i class=\"fa fa-file-o\"></i>  <strong>".$filename."</strong></p>";
				  echo "</p>";
				}
			  echo "<p class=\"lead\">You can now <a href=\""._URL."/". _ADMIN_FOLDER ."/login.php\" target=\"_blank\">login</a> as an Administrator with username '<strong>admin</strong>' and password '<strong>admin</strong>'.";
			  echo "</p>";
			  echo "<br /><div align=\"center\"><a href=\""._URL."/". _ADMIN_FOLDER ."/login.php\" target=\"_blank\" class=\"btn btn-primary\">Take me to the admin panel &rarr;</a></div>";
			  echo "";
//			  $error = 1;
			}
			else
			{ 			
			  echo "<script language=\"JavaScript\" type=\"text/javascript\">window.setTimeout('location.href=\"install.php?step=2&start=$linenumber&pointer=$pointer\";',500+$sleep);</script>\n";
			  echo "<noscript>\n";
			  echo "<p>Click <a href=\"install.php?step=2&start=$linenumber&pointer=$pointer\">continue &rarr;</a> (Please enable JavaScript to do this automatically)</p>\n";
			  echo "</noscript>\n";
			}
		  }
		}
		
		if ($error)
			echo "<p class=\"alert alpha-warning border-2 border-warning\">There was an error. Please drop the old tables before restarting!<br /></p> <br /><div class=\"text-center\"><a href=\"install.php\" class=\"btn btn-primary\">&larr; Go back</a></div>";
		// close both the connection and file;
		if ($connection) mysql_close();
		if ($file) fclose($file);

		?>
  <?php
break;
}
// end switch;
?>
  <div id="footer">
  <a href="https://www.phpsugar.com/" title="Powered by PHPSUGAR.com" target="_blank"><img src="//www.phpsugar.com/updates/phpsugar.gif&license=<?php echo _CUSTOMER_ID;?>" border="0" alt="Powered by PHPSUGAR.com" /></a><br />
  <p>Copyright &copy; <?php echo date('Y'); ?><br />
  Need help? <a href="https://www.phpsugar.com/support.html" target="_blank">Click here</a>
  </p>
  </div>
</div>
</body>
</html>