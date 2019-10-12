<div class="row">
	<div class="col-md-12 text-center">
	<ul class="pagination pagination-sm {$custom_class}">
		{foreach from=$pagination key=k item=pagination_data}
		<li{foreach from=$pagination_data.li key=attr item=attr_val} {$attr}="{$attr_val}"{/foreach}>
			<a{foreach from=$pagination_data.a key=attr item=attr_val} {$attr}="{$attr_val}"{/foreach}>{$pagination_data.text}</a>
		</li>
		{/foreach}
	</ul>
	</div>
</div>