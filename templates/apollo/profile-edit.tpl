{include file='header.tpl' p="general" tpl_name="profile-edit"} 
{include file="profile-header.tpl" p="profile-edit"}

<div id="content" class="content-detached content-video-handler">
	<div class="container-fluid">
	<div class="row row-page-heading">
		<div class="col-xs-7 col-sm-7 col-md-10">
			<h1>{$lang.edit_profile}</h1>
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
		{if $success == 1}
		<div class="alert alert-success">{$lang.ep_msg1}</div>
			{if $changed_pass == 1}
			<div class="alert alert-success">{$lang.ep_msg2}</div>
			<meta http-equiv="refresh" content="5;URL={$smarty.const._URL}">
			{/if}
		{include file='profile-edit-form.tpl'}
		{else}
			{if $errors.failure != ''}
				{$errors.failure}
			{/if}
		
		{if $nr_errors > 0}
		<div class="alert alert-danger">
			<ul class="list-unstyled">
			{foreach from=$errors item=error}
				<li>{$error}</li>
			{/foreach}
			</ul>
		</div>
		{/if} 
		{include file='profile-edit-form.tpl'}
		{/if}
	</div><!-- #content -->
	</div><!-- .row -->
</div><!-- .container -->
		
{include file='footer.tpl'} 