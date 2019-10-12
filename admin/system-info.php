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
$_page_title = 'PHP Configuration';
include('header.php');

function color_php_values($value) {

	$value = strtolower($value);

	if($value == 'enabled') {
		$value = '<span class="badge badge-success">'.$value.'</span>';
	}
	elseif($value == 'disabled') {
		$value = '<span class="badge badge-warning">'.$value.'</span>';
	}
	return $value;
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
			<div class="d-horizontal-scroll">
				<div class="breadcrumb">
					<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
					<a href="statistics.php" class="breadcrumb-item">Statistics &amp; Logs</a>
					<span class="breadcrumb-item active"><?php echo $_page_title;?></span>
				</div>
			</div>
			</div>

			<div class="header-elements d-none d-md-block"><!--d-none-->
				<div class="breadcrumb justify-content-center">
				</div>
			</div>
		</div>
	</div>
	<!-- /page header -->
</div><!--.page-header-wrapper-->

	<!-- Content area -->
	<div class="content content-full-width">


<?php
ob_start();
phpinfo();
$contents = ob_get_clean();

$phpinfo = array('phpinfo' => array());

if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', $contents, $matches, PREG_SET_ORDER))
{
	foreach($matches as $match)
	{
        if(strlen($match[1]))
		{
            $phpinfo[$match[1]] = array();
		}
        elseif(isset($match[3]))
		{
			$arr_keys = array_keys($phpinfo);
            $phpinfo[end($arr_keys)][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
		}
        else
		{
			$arr_keys = array_keys($phpinfo);
			$phpinfo[end($arr_keys)][] = $match[2];
		}
	}
}

function apache_mod_loaded($mod, $default = false) {

	if ( function_exists('apache_get_modules') ) {
		$mods = apache_get_modules();
		if ( in_array($mod, $mods) )
			return true;
	} elseif ( function_exists('phpinfo') ) {
			ob_start();
			phpinfo(8);
			$phpinfo = ob_get_clean();
			if ( false !== strpos($phpinfo, $mod) )
				return true;
	}
	return $default;
}
$phpcore = (array_key_exists('PHP Core', $phpinfo)) ? 'PHP Core' : 'Core';

if ( ! function_exists('phpinfo')) // disabled by host
{
	$info_msg .= pm_alert_warning('It looks like <code>phpinfo()</code> function has been disabled by your hosting provider. This function is required to retrieve information about your system.');
}
?>


<?php echo $info_msg; ?>

<div class="alert alert-info m-3 mb-0">Listed below are some of the most important PHP Configuration values. This data is used for debugging runtime issues.</div>

<div class="card card-blanche">
	<div class="card-header">
		<h5 class="card-title"></h5>
	</div>
	<div class="card-body"></div>
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-xs table-responsive">
		 <thead>
		  <tr>
		   <th class="w-25">Directive</th>
		   <th>Value</th>
		  </tr>
		 </thead>
		 <tbody>
			<tr>
				<td>PHP Version</td>
				<td><?php echo color_php_values("{$phpinfo[$phpcore]['PHP Version']}");?></td>
			</tr>

			<tr>
				<td>System</td>
				<td><?php echo color_php_values("{$phpinfo['phpinfo']['System']}");?></td>
			</tr>
			<tr>
				<td>Safe Mode</td>
				<td><?php echo color_php_values("{$phpinfo[$phpcore]['safe_mode'][0]}"); ?></td>
			</tr>
			<tr>
				<td>Allow URL fopen</td>
				<td><?php echo color_php_values("{$phpinfo[$phpcore]['allow_url_fopen'][0]}"); ?></td>
			</tr>
			<tr>
				<td>cURL Support</td>
		        <td><?php echo color_php_values((in_array('curl', get_loaded_extensions())) ? "enabled" : "disabled"); ?></td>
			</tr>
			<tr>
				<td>Display Errors</td>
				<td><?php  echo color_php_values("{$phpinfo[$phpcore]['display_errors'][0]}"); ?></td>
			</tr>
			<tr>
				<td>Display Startup Errors</td>
				<td><?php echo color_php_values("{$phpinfo[$phpcore]['display_startup_errors'][0]}"); ?></td>
			</tr>
			<tr>
				<td>File Uploads</td>
				<td><?php echo color_php_values("{$phpinfo[$phpcore]['file_uploads'][0]}"); ?></td>
			</tr>
			<tr>
				<td>File Post Size (post_max_size)</td>
				<td><?php echo color_php_values("{$phpinfo[$phpcore]['post_max_size'][0]}"); ?></td>
			</tr>
			<tr>
				<td>Max File Size Upload (upload_max_filesize)</td>
				<td><?php echo color_php_values("{$phpinfo[$phpcore]['upload_max_filesize'][0]}"); ?></td>
			</tr>
			<tr>
				<td>Server Name</td>
				<td><?php echo color_php_values("{$phpinfo['Apache Environment']['SERVER_NAME']}"); ?></td>
			</tr>
			<tr>
				<td>HTTP Accept charset</td>
				<td><?php echo color_php_values("{$phpinfo['Apache Environment']['HTTP_ACCEPT_CHARSET']}"); ?></td>
			</tr>
			<tr>
				<td>GD Library</td>
				<td><?php echo color_php_values("{$phpinfo['gd']['GD Support']}"); ?></td>
			</tr>
			<tr>
				<td>GD Library Version</td>
				<td><?php echo color_php_values("{$phpinfo['gd']['GD Version']}"); ?></td>
			</tr>
			<tr>
				<td>GIF Read Support</td>
				<td><?php echo color_php_values("{$phpinfo['gd']['GIF Read Support']}"); ?></td>
			</tr>
			<tr>
				<td>GIF Create Support</td>
				<td><?php echo color_php_values("{$phpinfo['gd']['GIF Create Support']}"); ?></td>
			</tr>
			<tr>
				<td>JPEG Support</td>
				<td><?php echo color_php_values("{$phpinfo['gd']['JPEG Support']}"); ?></td>
			</tr>
			<tr>
				<td>PNG Support</td>
				<td><?php echo color_php_values("{$phpinfo['gd']['PNG Support']}"); ?></td>
			</tr>
			<tr>
				<td>Session Support</td>
				<td><?php echo color_php_values("{$phpinfo['session']['Session Support']}"); ?></td>
			</tr>
			<tr>
				<td>Apache mod_rewrite</td>
				<td><?php echo color_php_values((apache_mod_loaded('mod_rewrite')) ? "enabled" : "disabled"); ?></td>
			</tr>
			<tr>
				<td>Apache modules</td>
				<td><?php print_r($phpinfo['apache2handler']['Loaded Modules']); ?></td>
			</tr>
			<tr>
				<td>MySQL Variables</td>
				<td>
				<textarea class="form-control" rows="5" cols="30"><?php
					$res = @mysql_query("SHOW VARIABLES LIKE '%'");
					while ($row = @mysql_fetch_assoc($res)) {
						echo $row['Variable_name'].':'.$row['Value']."\n";
					}
					?></textarea>
				</td>
			</tr>
		 </tbody>
		</table>
</div>
</div><!-- .content -->
<?php
include('footer.php');