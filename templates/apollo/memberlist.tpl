{include file='header.tpl' p="general"} 
{include file="profile-header.tpl" p="members"}
<div id="content" class="content-detached content-video-handler">
	<div class="container-fluid">
	<div class="row row-page-heading">
		<div class="col-xs-7 col-sm-7 col-md-10">
			<h1>{$lang.members}</h1>
		</div>
		<div class="col-xs-5 col-sm-5 col-md-2">
			<div class="pull-right">
				<div>
					<small><div id="uploadProgressBar"></div></small>
					<div id="divStatus"></div>
					<ol id="uploadLog" class="list-unstyled"></ol>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
        <div class="col-md-12">
			<div class="pm-section-head">
		    <div class="btn-group btn-group-sort">
		    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
            {if $gv_sortby == ''}{$lang.sorting}{/if} {if $gv_sortby == 'name'}{$lang.name}{/if}{if $gv_sortby == 'lastseen'}{$lang.last_seen}{/if}{if $gv_sortby == 'online'}{$lang.whois_online}{/if}
		    <span class="caret"></span>
		    </a>
		    <ul class="dropdown-menu pull-right">
		        <li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?page={$gv_pagenumber}&sortby=name" rel="nofollow" class="{if $gv_sortby == 'name'}selected{/if}">{$lang.name}</a></li>
		        <li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?page={$gv_pagenumber}&sortby=lastseen" rel="nofollow" class="{if $gv_sortby == 'lastseen'}selected{/if}">{$lang.last_seen}</a></li>
		        <li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?do=online&sortby=online" rel="nofollow" class="{if $gv_sortby == 'online'}selected{/if}">{$lang.whois_online}</a></li>
		    </ul>
		    </div>
			</div>

			<div class="row">
				<div class="col-md-12 text-center">
				<ul class="pagination pagination-sm">
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}" rel="nofollow">{$lang.memberlist_all}</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=a" rel="nofollow">A</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=b" rel="nofollow">B</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=c" rel="nofollow">C</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=d" rel="nofollow">D</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=e" rel="nofollow">E</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=f" rel="nofollow">F</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=g" rel="nofollow">G</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=h" rel="nofollow">H</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=i" rel="nofollow">I</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=j" rel="nofollow">J</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=k" rel="nofollow">K</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=l" rel="nofollow">L</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=m" rel="nofollow">M</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=n" rel="nofollow">N</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=o" rel="nofollow">O</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=p" rel="nofollow">P</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=q" rel="nofollow">Q</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=r" rel="nofollow">R</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=s" rel="nofollow">S</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=t" rel="nofollow">T</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=u" rel="nofollow">U</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=v" rel="nofollow">V</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=w" rel="nofollow">W</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=x" rel="nofollow">X</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=y" rel="nofollow">Y</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=z" rel="nofollow">Z</a></li>
					<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=other" rel="nofollow">#</a></li>
				</ul>
				</div>
			</div>

			<div class="clearfix"></div>
			<ul class="row pm-channels-list list-unstyled">
			{foreach from=$user_list key=k item=user_data}
			<li class="col-sm-6 col-md-4">
				<div class="pm-channel">
					<div class="pm-channel-header">
						<div class="pm-channel-cover">
							{if $user_data.channel_cover.max}
							<img src="{$user_data.channel_cover.450}" alt="{$user_data.username}"  border="0" class="img-responsive">
							{/if}
						</div>
						<div class="pm-channel-profile-pic">
							<a href="{$user_data.profile_url}"><img src="{$user_data.avatar_url}" alt="{$user_data.username}"  border="0" class="img-responsive"></a>
						</div>
					</div>
					<div class="pm-channel-body">
						<h3><a href="{$user_data.profile_url}" class="ellipsis {if $user_data.user_is_banned}pm-user-banned{/if}">{$user_data.name}</a></h3>
						<p></p>
						{if $logged_in}
						<div class="pm-channel-buttons">
							{if $smarty.const._MOD_SOCIAL && $logged_in == '1' && $user_data.id != $s_user_id}
								{include file="user-subscribe-button.tpl" current_user_data=$user_data profile_user_id=$user_data.id}
							{else if $user_data.id == $s_user_id}
								<a href="{$user_data.profile_url}" class="btn btn-sm btn-success">{$lang.this_is_you}</a>
							{/if}
						</div>
						{/if}
					</div>
				</div>
			</li>
			{foreachelse}
				{if $problem != ''}
				<li class="col-xs-12 col-sm-12 col-md-12">
					<div class="text-center">{$problem}</div>
				</li>
				{else}
				<li class="col-xs-12 col-sm-12 col-md-12">
					<div class="text-center">{$lang.memberlist_msg2}</div>
				</li>
				{/if}
			{/foreach}
			</ul>
			<div class="clearfix"></div>
			{if is_array($pagination)}
				{include file='item-pagination-obj.tpl' custom_class=''}
			{/if}
        </div><!-- #content -->
      </div><!-- .row -->
    </div><!-- .container -->
{include file='footer.tpl'}