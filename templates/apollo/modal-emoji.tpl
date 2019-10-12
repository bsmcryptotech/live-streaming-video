<!-- Modal -->
<div id="modalEmojiHelp"></div>
<div class="modal" id="modalEmojiList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">{$lang.emoji_help|default:'Emoji Finder'}</h4>
			</div>
			<div class="modal-body modal-content">
			<span id="loading"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" alt="{$lang.please_wait}" align="absmiddle" border="0" width="16" height="16" /> {$lang.please_wait}</span>
			</div>
			<div class="modal-footer">
				<button class="btn btn-sm btn-default" data-dismiss="modal" aria-hidden="true">{$lang.close}</button>
			</div>
		</div>
	</div>
</div>