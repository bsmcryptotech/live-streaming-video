<!-- Modal -->
<div class="modal" id="modal-addvideo">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{$lang.close}</span></button>
				<h4 class="modal-title">{$lang.add_video}</h4>
			</div>
			<div class="modal-body">
			<ul class="pm-addvideo-modal list-unstyled">
				{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}
				<li>
				<a href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}"><i class="mico mico-insert_link"></i> <span>{$lang.suggest}</span></a>
				</li>
				{/if}
				{if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
				<li><a href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}"><i class="mico mico-cloud_upload"></i> <span>{$lang.upload_video}</span></a></li>
				{/if}
			</ul>
			</div>
		</div>
	</div>
</div>