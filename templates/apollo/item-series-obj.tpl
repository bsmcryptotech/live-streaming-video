<div class="thumbnail{if $thumbSize == 'small'} thumbnail-small{elseif $thumbSize == 'medium'} thumbnail-medium{elseif $thumbSize == 'large'} thumbnail-large{elseif $thumbSize == 'poster'} thumbnail-poster{/if}">
	<div class="pm-video-thumb ripple">
		<a href="{$item_data.url}" title="{$item_data.attr_alt}">
			{if $hideLabels != "1"}
			<div class="pm-video-labels hidden-xs">
				{if $item_data.mark_new}<span class="label label-new">{$lang._new}</span>{/if}
				{if $item_data.mark_popular}<span class="label label-pop">{$lang._popular}</span>{/if}
				{if $item_data.featured}<span class="label label-featured">{$lang._feat}</span>{/if}
			</div>
			{/if}
			<img src="{$item_data.image_url}" alt="{$item_data.attr_alt}" class="img-responsive">
			<span class="overlay"></span>
		</a>
	</div>

	<div class="caption">
		{if $item_data._item_type_ == 'episode' || $isObj == 'episode'}
			<h3><a href="{$item_data.url}" title="{$item_data.attr_alt}" class="ellipsis">{$item_data.video_title}</a></h3>
		{else}
			<h3><a href="{$item_data.url}" title="{$item_data.attr_alt}" class="ellipsis">{$item_data.title}</a></h3>
		{/if}

		{if $hideMeta != "1"}
			<div class="pm-video-meta hidden-xs">
				{if $item_data._item_type_ == 'series' || $isObj == 'series'}
					{if $item_data.episodes_count > 0}
						<p class="pm-episode-count"><span>{$item_data.episodes_count}</span>{$lang._eps|default:"EP."}</p>
					{/if}
				{/if}
			</div>
			{foreach $item_data.genres key=k item=genre_data}
				<a href="{$genre_data.url}"><small>{$genre_data.name}</small></a>
			{/foreach}
		{/if}
	</div>
</div>