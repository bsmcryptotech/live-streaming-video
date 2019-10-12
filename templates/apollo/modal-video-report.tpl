<!-- Modal -->
<div class="modal" id="modal-video-report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<form name="reportvideo" action="" method="POST">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">{$lang.report_video}</h4>
			</div>
			<div class="modal-body">
				<div id="report-confirmation" class="hide-me alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button></div>
					<input type="hidden" id="name" name="name" class="form-control" value="{if $logged_in}{$s_name}{/if}">
					<input type="hidden" id="email" name="email" class="form-control" value="{if $logged_in}{$s_email}{/if}">

				<div class="form-group">
						<label for="exampleInputEmail1">{$lang.report_form1}</label>
						<select name="reason" class="form-control">
						<option value="{$lang.report_form1}" selected="selected">{$lang.select}</option>
						<option value="{$lang.report_form4}">{$lang.report_form4}</option>
						<option value="{$lang.report_form5}">{$lang.report_form5}</option>
						<option value="{$lang.report_form6}">{$lang.report_form6}</option>
						<option value="{$lang.report_form7}">{$lang.report_form7}</option>
						</select>
				</div>
					
				{if ! $logged_in}
				<div class="form-group">
					<div class="row">
						<div class="col-xs-6 col-sm-5 col-md-2">
							<input type="text" name="imagetext" class="form-control" autocomplete="off" placeholder="{$lang.confirm_comment}">
						</div>
						<div class="col-xs-6 col-sm-7 col-md-10">
							<img src="{$smarty.const._URL}/include/securimage_show.php?sid={echo_securimage_sid}" id="securimage-report" alt="" width="100" height="35">
							<button class="btn btn-sm btn-link btn-refresh" onclick="document.getElementById('securimage-report').src = '{$smarty.const._URL}/include/securimage_show.php?sid=' + Math.random(); return false;"><i class="fa fa-refresh"></i> </button>
						</div>
					</div>
				</div>
				{/if}
					
				<input type="hidden" name="p" value="detail">
				<input type="hidden" name="do" value="report">
				<input type="hidden" name="vid" value="{$video_data.uniq_id}">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-link" data-dismiss="modal">{$lang.submit_cancel}</button>
				<button type="submit" name="Submit" class="btn btn-sm btn-danger" value="{$lang.submit_send}">{$lang.report_video}</button>
			</div>
		</div>
		</form>
	</div>
</div>