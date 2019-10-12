		<!-- Footer -->
<!-- 		<div class="navbar navbar-light" id="navbar-footer">
			<div class="navbar-text">

			</div>
			<div class="ml-lg-auto text-right pt-2 pb-2 text-muted font-size-xs">
			Powered by <a href="http://www.phpsugar.com/phpmelody.html" target="_blank" class="text-muted">PHP Melody v<?php echo _PM_VERSION; ?></a> <?php if (version_compare($official_version, $config['version']) == 1) : ?> (<em><a href="https://www.phpsugar.com/customer/" target="_blank" class="text-success">Newer version available</a>!</em>) <?php endif; ?><br />
				<a href="#feedback" data-toggle="modal" class="text-muted">Help &amp; Feedback</a> / <a href="http://www.phpsugar.com/support.html" target="_blank" class="text-muted">Customer Care</a> / <a href="https://www.phpsugar.com/forum/" target="_blank" class="text-muted">Support Forums</a>
			</div>
		</div>
 -->

		<?php if (version_compare($official_version, $config['version']) == 1 && $hide_update_notification != 1) : ?>
		<div class="navbar navbar-light bg-success" id="navbar-footer">
			<div class="text-center py-1 text-white font-size-xs">
				A newer version of <strong>PHP Melody</strong> is available! <a href="https://www.phpsugar.com/customer/" class="text-white" target="_blank">Click here to download the v<?php echo $official_version; ?> update</a>.
			</div>
		</div>
		<?php endif; ?>


		<?php if ($config['maintenance_mode']==1) : ?>
		<div class="navbar navbar-light bg-warning" id="navbar-footer">
			<div class="text-center py-1 text-white font-size-sm animated-fast flash">
				<strong>Visitors cannot access any page on your site because it's currently in maintenance mode</strong>. Disable maintenance mode from the '<strong><a href="settings.php" class="text-white">Settings</a></strong>' page.
			</div>
		</div>
		<?php endif; ?>


		<!-- /footer -->
	</div>
	<!-- /main content -->
</div>
<!-- /page content -->

<div id="loading-large">
	<div class="loading-animation">
		<div class="bounce1"></div>
		<div class="bounce2"></div>
		<div class="bounce3"></div>
	</div>
	<div class="loading-msg">Please wait</div>
</div>


<?php include('modals/modal-feedback.php');?>
<?php include('modals/modal-add-video.php');?>

<?php if($config['keyboard_shortcuts'] == 1) : ?>
<?php include('modals/modal-view-shortcuts.php');?>
<?php endif; ?>
<script type="text/javascript" src="js/jquery.typewatch.js"></script>
<script type="text/javascript" src="js/bootstrap-hover-dropdown.min.js"></script>
<script type="text/javascript" src="js/jquery.ajaxmanager.js"></script>
<script type="text/javascript" src="js/jquery.cookee.js"></script>
<script type="text/javascript" src="js/jquery.ba-dotimeout.min.js"></script>
<?php if ($load_datepicker) : ?>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<?php endif;?>
<?php if($load_tagsinput == 1): ?>
<script type="text/javascript" src="js/jquery.tagsinput.js"></script>
<?php endif; ?>
<script type="text/javascript" src="js/melody.js"></script>
<script type="text/javascript" src="js/vscheck.js"></script>

<?php if($load_colorpicker == 1): ?>
<script type="text/javascript" src="js/bootstrap-colorpicker.min.js"></script>
<?php endif; ?>
<?php if($load_tinymce == 1): ?>
<script type="text/javascript" src="js/tiny_mce/jquery.tinymce.min.js"></script>

<script type="text/javascript">
// Initializes all textareas with the tinymce class
$(document).ready(function () {
	 $('textarea.tinymce').tinymce({
			script_url: 'js/tiny_mce/tinymce.min.js',
			disk_cache: true,
			skin: "lightgray",
			theme: 'modern',
			plugins: 'autosave preview autolink directionality visualblocks visualchars image link hr pagebreak nonbreaking anchor lists textcolor wordcount imagetools media code',
			toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat',
			language:"en",
			branding: false,
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,hr,|,formatselect,fontselect,fontsizeselect,|,pdw_toggle,",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,",
			theme_advanced_buttons3 : "preview,|,forecolor,backcolor,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,advhr,|,ltr,rtl,|,media,fullscreen",
			theme_advanced_font_sizes: "12px,13px,14px,15px,16px,18px,20px",
			font_size_style_values : "12px,13px,14px,15px,16px,18px,20px",
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false,
			relative_urls : false,
			browser_spellcheck : true,
			content_css : "css/frontend-look.css",
			paste_data_images: true,
			relative_urls : false,
			remove_script_host : false,
			convert_urls : true,
			autosave_ask_before_unload: true,
	 });
});
</script>
<?php endif; ?>
<?php if ($load_jquery_ui) : ?>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<?php endif; ?>

<?php if ($load_sortable) : ?>
<script type="text/javascript" src="js/jquery.mjs.nestedSortable.js"></script>
<?php endif; ?>
<?php if ($showm == 'mod_article' || $showm == 'mod_pages'):  ?>
<script type="text/javascript" src="js/article.js"></script>
<?php endif; ?>
<script type="text/javascript" src="<?php echo _URL; ?>/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?php echo _URL; ?>/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?php echo _URL; ?>/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="js/fileupload-handlers.js"></script>

<?php if($load_ibutton == 1): ?>
<script type="text/javascript" src="js/jquery.ibutton.js"></script>
<?php endif; ?>
<?php if($load_prettypop == 1): ?>
<link rel="stylesheet" href="css/prettyPop.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="js/jquery.prettyPhoto.js"></script>
<?php endif; ?>
<?php if($load_scrolltofixed == 1): ?>
<script type="text/javascript" src="js/jquery-scrolltofixed-min.js"></script>
<?php endif; ?>
<script type="text/javascript" src="js/admin.js"></script>
<?php if($load_chzn_drop == 1): ?>
<script type="text/javascript" src="js/chosen.jquery.min.js"></script>
<?php endif; ?>
<script type="text/javascript" src="js/jquery.gritter.js"></script>
<script type="text/javascript" src="js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
// Global settings for Admin Area notifications
$.notifyDefaults({
	// settings
	element: 'body',
	position: null,
	type: "primary",
	allow_dismiss: true,
	newest_on_top: true,
	showProgressbar: false,
	placement: {
		from: "top", // top, bottom
		align: "right" // left, right, center
	},
	offset: {
			x: 20,
			y: 100
		},
	spacing: 10,
	z_index: 1031,
	delay: 0,
	timer: 5000, 
	url_target: '_blank',
	mouse_over: "pause",
	animate: {
		enter: 'animated fadeInUp',
		exit: 'animated fadeInUp',
	},
	onShow: null,
	onShown: null,
	onClose: null,
	onClosed: null,
	template: '<div data-notify="container" class="growl alert bg-{0}" role="alert">' +
				'<button type="button" aria-hidden="true" class="close text-white opacity-80 p-0" data-notify="dismiss">&times;</button>' +
				'<span data-notify="icon" class="growl-icon"></span> ' +
				'<span data-notify="title" class="growl-title font-weight-semibold">{1}</span> ' +
				'<span data-notify="message" class="growl-message">{2}</span>' +
				'<div class="progress progress-custom growl-progress" data-notify="progressbar">' +
					'<div class="progress-bar bg-custom progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
				'</div>' +
				'<a href="{3}" target="{4}" data-notify="url"></a>' +
			'</div>',
	// PHP Melody custom settings
	PM_exitAnimationTimeout: 0 
});

var show_pm_notes = $.cookie('showNotice');
if (show_pm_notes != 'off') {
	$(document).ready(function () {
		<?php show_pm_notes(); ?>
	});
}
</script>

<?php if($load_import_js == 1): ?>
<script type="text/javascript" src="js/unserialize.jquery.latest.js"></script>
<script type="text/javascript" src="js/import.js"></script>
<?php endif; ?>


<?php if (($showm == '5' || $showm == '6') && $config['allow_emojis'] == 1) : ?>
<script type="text/javascript" src="<?php echo _URL; ?>/js/jquery.textcomplete.min.js"></script>
<script type="text/javascript" src="<?php echo _URL; ?>/js/melody.emoji.js"></script>
<?php endif; ?>

<?php if ($showm == 'cron' || $showm == 2) : ?>
<script type="text/javascript" src="js/cron.js"></script> 
<?php endif; ?>

<?php include('footer-js.php'); ?>
<?php

if ($conn_id)
{
		mysql_close($conn_id);
}
?>
</body>
</html>