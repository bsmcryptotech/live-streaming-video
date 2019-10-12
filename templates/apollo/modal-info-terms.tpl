<!-- Modal -->
<div class="modal" id="modal-terms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">{$lang.toa}</h4>
			</div>
			<div class="modal-body">
				{if $terms_page.content != ''}
					{$terms_page.content}
				{else}
					{include file='terms.tpl'}
				{/if}
			</div>
		</div>
	</div>
</div>