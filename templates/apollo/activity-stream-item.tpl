<li class="media" id="activity-{$activity_meta.activity_id}">
	<span class="pull-left mr-2"><a href="{$actor_bucket.$activity_index.0.profile_url}"><img src="{$actor_bucket.$activity_index.0.avatar_url}" alt="{$actor_bucket.$activity_index.0.username}" width="60" height="60" border="0" class="media-object"></a></span>
	<div class="media-body">
		<a href="{$actor_bucket.$activity_index.0.profile_url}" class="pm-username"><strong>{$actor_bucket.$activity_index.0.name}</strong></a>
		{if $activity_meta.actors_count > 2}
			{$lang.and}
			<a href="#">{$activity_meta.actors_count-1} {$lang.other|strtolower}</a>
			(
				{foreach from=$actor_bucket.$activity_index key=kk item=actor name=actors_foreach}
					{if $kk > 0}
						<a href="{$actor.profile_url}">{$actor.name}</a>{if ! $smarty.foreach.actors_foreach.last},{/if}
					{/if}
				{/foreach}
			)
		{elseif $activity_meta.actors_count == 2}
			{$lang.and} <a href="{$actor_bucket.$activity_index.1.profile_url}">{$actor_bucket.$activity_index.1.name}</a>
		{/if}


		{if $activity_meta.activity_type == $smarty.const.ACT_TYPE_STATUS}
			<div class="pm-social-status-text">{$activity_meta.metadata.statustext}</div>
		{/if}

		{$activity_meta.lang} 

		{if $activity_meta.objects_count == 1}
			{if $activity_meta.object_type == $smarty.const.ACT_OBJ_USER}
				<a href="{$object_bucket.$activity_index.0.profile_url}">{$object_bucket.$activity_index.0.name}</a>.
			{/if}

			{if $activity_meta.object_type == $smarty.const.ACT_OBJ_VIDEO}
				<div class="pm-social-obj-video clearfix">
					<div class="pm-video-thumb">
					{if $object_bucket.$activity_index.0.duration}<span class="pm-label-duration">{$object_bucket.$activity_index.0.duration} </span>{/if}
					<a href="{$object_bucket.$activity_index.0.video_href}"><img src="{$object_bucket.$activity_index.0.thumb_img_url}" class="img-responsive"></a>
					</div>
					<div class="pm-social-obj-title">
					<a href="{$object_bucket.$activity_index.0.video_href}">{$object_bucket.$activity_index.0.video_title}</a>
					</div>
				</div>
			{/if}

			{if $activity_meta.object_type == $smarty.const.ACT_OBJ_ARTICLE}
				<a href="{$object_bucket.$activity_index.0.link}">{$object_bucket.$activity_index.0.title}</a>
			{/if}

			{if $activity_meta.object_type == $smarty.const.ACT_OBJ_PROFILE}

			{/if}

		{elseif $activity_meta.objects_count == 2}
			<a href="{$object_bucket.$activity_index.0.profile_url}">{$object_bucket.$activity_index.0.name}</a> {$lang.and} <a href="{$object_bucket.$activity_index.1.profile_url}">{$object_bucket.$activity_index.1.name}</a>
		{else}
			<a href="{$object_bucket.$activity_index.0.profile_url}">{$object_bucket.$activity_index.0.name}</a> {$lang.and}
			<a href="#">{$activity_meta.objects_count-1} {$lang.other|strtolower}</a>
			(
			{foreach from=$object_bucket.$activity_index key=kk item=obj name=objects_foreach}
				{if $kk > 0}
					<a href="{$obj.profile_url}">{$obj.name}</a>{if ! $smarty.foreach.objects_foreach.last},{/if}
				{/if}
			{/foreach}
			)
		{/if}


		{if $activity_meta.targets_count == 1}
			{if $activity_meta.target_type == $smarty.const.ACT_OBJ_ARTICLE}
				<a href="{$target_bucket.$activity_index.0.link}">{$target_bucket.$activity_index.0.title}</a>
			{/if}

			{if $activity_meta.target_type == $smarty.const.ACT_OBJ_VIDEO}

				<div class="pm-social-obj-video clearfix">
					<div class="pm-video-thumb">
					{if $target_bucket.$activity_index.0.duration}<span class="pm-label-duration">{$target_bucket.$activity_index.0.duration} </span>{/if}
					<a href="{$target_bucket.$activity_index.0.video_href}"><img src="{$target_bucket.$activity_index.0.thumb_img_url}" class="img-responsive"></a>
					</div>
					<div class="pm-social-obj-title">
					<a href="{$target_bucket.$activity_index.0.video_href}">{$target_bucket.$activity_index.0.video_title}</a>
					</div>
				</div>
			{/if}
		{elseif $activity_meta.targets_count == 2}

		{else}

		{/if}

			<span class="media-date">{$activity_meta.time_since} {$lang.ago}</span>
	</div>
	{if $s_user_id == $actor_bucket.$activity_index.0.user_id}
		<a href="#" class="media-actions hidden-xs" id="hide-activity-{$activity_meta.activity_id}" rel="tooltip" title="{$lang.hide_from_stream}"><i class="fa fa-eye-slash"></i> </a>
	{/if}
</li>