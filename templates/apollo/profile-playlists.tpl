{include file='header.tpl' no_index='1' p="playlists" tpl_name="profile-playlists"}
{include file="profile-header.tpl" p="playlists"}
<div id="content">
	<div class="container-fluid">
	<div class="row row-page-heading">
		<div class="col-xs-8 col-sm-8 col-md-10"><h1>{$lang.manage_playlists|default:'Manage Playlists'}</h1></div>
		<div class="col-xs-4 col-sm-4 col-md-2">
		{if $allow_playlists}
		<a href="#modal-new-playlist" data-toggle="modal" data-backdrop="true" data-keyboard="true" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> {$lang.new_playlist}</a>
		{/if}
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
		{include file='profile-playlists-ul.tpl' playlists=$playlists}
		</div>
	</div>

			{if $allow_playlists}
				{include file="modal-playlist-createnew.tpl"}
			{/if}
	</div><!-- .row -->
	</div><!-- .container-fluid -->
</div><!-- #content -->
{include file='footer.tpl'} 