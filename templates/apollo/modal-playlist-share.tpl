<div class="modal" id="playlist-share" role="dialog" aria-labelledby="playlist-share-form-label">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{$lang.close}</span></button>
			 <h4 class="modal-title" id="myModalLabel">{$lang._share}</h4>
		</div>
		<div class="modal-body">
			<p>{$lang.playlist_share_msg}</p>
				<div class="col-md-3">
					<a href="https://www.facebook.com/sharer.php?u={$facebook_like_href}&amp;t={$facebook_like_title}" onclick="javascript:window.open(this.href,
			'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Facebook"><i class="pm-vc-sprite facebook-icon"></i></a>
					<a href="https://twitter.com/home?status=Watching%20{$facebook_like_title}%20on%20{$facebook_like_href}" onclick="javascript:window.open(this.href,
			'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Twitter"><i class="pm-vc-sprite twitter-icon"></i></a>
				</div>
				<div class="col-md-9">
					<div class="input-group"><span class="input-group-addon">URL</span><input name="share_link" id="share_link" type="text" value="{$share_link}" class="form-control" onClick="SelectAll('share_link');"></div>
				</div>
				<div class="clearfix"></div>
		</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->