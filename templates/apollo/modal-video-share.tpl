<!-- Modal -->
<div class="modal" id="modal-video-share" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">{$lang._share}</h4>
	  </div>
	  <div class="modal-body">

		<div class="row pm-modal-share">
			<div class="col-md-12 hidden-xs hidden-sm">
				<h5>{$meta_title}</h5>
				<div id="share-confirmation" class="hide-me alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button></div>
			</div>
			<div class="col-md-3 hidden-xs hidden-sm">
				<div class="pm-modal-video-info">
					<img src="{$facebook_image_src}" width="480" height="360" class="img-responsive" />
					{if $meta_description}
						<p>{$meta_description}</p>
					{/if}
				</div>
			</div>

			<div class="col-md-9">
				<h6>{$lang.share_on_social}</h6>
				<a href="https://www.facebook.com/sharer.php?u={$facebook_like_href}&amp;t={$facebook_like_title}" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Facebook"><i class="pm-vc-sprite facebook-icon"></i></a>
				<a href="https://twitter.com/home?status=Watching%20{$facebook_like_title}%20on%20{$facebook_like_href}" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Twitter"><i class="pm-vc-sprite twitter-icon"></i></a>

				<h6>{$lang._embed}</h6>
				<form>
				<div class="form-group">
					<div class="input-group"><span class="input-group-addon" onClick="SelectAll('pm-share-link');"><i class="fa fa-link"></i></span><input name="pm-share-link" id="pm-share-link" type="text" value="{$video_data.video_href}{$episode_data.video_href}" class="form-control" onClick="SelectAll('pm-share-link');"></div>
				</div>

				{if $embedcode_to_share}
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon" onClick="SelectAll('pm-embed-code');"><i class="fa fa-code"></i></span>
						<textarea name="pm-embed-code" id="pm-embed-code" rows="1" class="form-control" onClick="SelectAll('pm-embed-code');">{$embedcode_to_share}</textarea>
					</div>
				</div>
				{/if}
				</form>

				<form name="sharetofriend" action="" method="POST" class="">
				<h6>Share via Email</h6>
					<div class="form-group">
						<input type="text" id="name" name="name" class="form-control" value="{$s_name}" placeholder="{$lang.your_name}" size="40">
					</div>
					<div class="form-group">
						<input type="text" id="email" name="email" class="form-control" placeholder="{$lang.friends_email}" size="50">
					</div>
						{if ! $logged_in}
						<div class="form-group">
							<div class="row">
								<div class="col-xs-6 col-sm-5 col-md-2">
									<input type="text" name="imagetext" class="form-control" autocomplete="off" placeholder="{$lang.confirm_comment}">
								</div>
								<div class="col-xs-6 col-sm-7 col-md-10">
									<img src="{$smarty.const._URL}/include/securimage_show.php?sid={echo_securimage_sid}" id="securimage-share" alt="" width="100" height="35">
									<button class="btn btn-sm btn-link btn-refresh" onclick="document.getElementById('securimage-share').src = '{$smarty.const._URL}/include/securimage_show.php?sid=' + Math.random(); return false;">
									<i class="fa fa-refresh"></i>
									</button>
								</div>
							</div>
						</div>
						{/if}
						<input type="hidden" name="p" value="detail">
						<input type="hidden" name="do" value="share">
						<input type="hidden" name="vid" value="{$video_data.uniq_id}{$episode_data.uniq_id}">
					<div class="form-group">
						<button type="submit" name="Submit" class="btn btn-sm btn-success">{$lang.submit_send}</button>
					</div>
				</form>
			</div>
		</div>
	  </div>
	</div>
  </div>
</div>