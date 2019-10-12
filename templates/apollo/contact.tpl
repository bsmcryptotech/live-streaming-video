{include file='header.tpl' p="general"} 
<div id="content" class="content-detached">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8">
          <h1>{$lang.contact_us}</h1>
          <hr />
            {if isset($err_captcha)}
            <div class="alert alert-danger">{$err_captcha}</div>
            {/if}
            {if isset($err_email)}
            <div class="alert alert-danger">{$err_email}</div>
            {/if}
            {if isset($err_msg)}
            <div class="alert alert-danger">{$err_msg}</div>
            {/if}
            {if isset($confirm_send)}
            <div class="alert alert-success">{$lang.contact_msg1}</div>
            {/if}
            {if !isset($confirm_send)}
          	<form class="form-horizontal" method="post" action="{$smarty.const._URL}/contact.php">
              <div class="form-group">
                <label for="your_name" class="col-md-2 control-label">{$lang.your_name}</label>
                <div class="col-md-10"><input type="text" class="form-control" name="your_name" value="{if $logged_in}{$s_name}{else}{$smarty.post.your_name}{/if}"></div>
              </div>
              <div class="form-group">
                <label for="your_email" class="col-md-2 control-label">{$lang.your_email}</label>
                <div class="col-md-10">
                  <input type="email" class="form-control" name="your_email" value="{if $logged_in}{$s_email}{else}{$smarty.post.your_email}{/if}">
                </div>
              </div>
              <div class="form-group">
                <label for="importance" class="col-md-2 control-label">{$lang.importance}</label>
                <div class="col-md-10">
                  <select name="importance" class="form-control">
                    <option value="{$lang.low}">{$lang.low}</option>
                    <option value="{$lang.normal}" selected="selected">{$lang.normal}</option>
                    <option value="{$lang.high}">{$lang.high}</option>
                    <option value="{$lang.urgent}">{$lang.urgent}</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="select" class="col-md-2 control-label">{$lang.in_regard}</label>
                <div class="col-md-10">
                  <select name="select" class="form-control">
                    <option selected="selected">{$lang.select}</option>
                    <option>{$lang.bug_report}</option>
                    <option>{$lang.suggestions}</option>
                    <option>{$lang.general_q}</option>
                    <option>{$lang.other}</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="msg" class="col-md-2 control-label">{$lang.your_message}</label>
                <div class="col-md-10">
                  <textarea name="msg" rows="4" class="form-control" placeholder="">{$smarty.post.msg}</textarea>
                </div>
              </div>
          		{if $logged_in == 0}
          		{if $spambot_prevention == 'securimage'}
          		<div class="form-group">
          			<div class="col-md-offset-2 col-md-7">
          				<input type="text" name="imagetext" class="form-control" autocomplete="off" placeholder="{$lang.enter_captcha}">

          			</div>
                <div class="col-md-3">
                  <img src="{$smarty.const._URL}/include/securimage_show.php?sid={echo_securimage_sid}" id="captcha_image" align="absmiddle" alt="" class="img-rounded">
                  <button class="btn btn-sm btn-link btn-refresh" onclick="document.getElementById('captcha_image').src = '{$smarty.const._URL}/include/securimage_show.php?sid=' + Math.random(); return false"><i class="fa fa-refresh"></i></button>
                </div>
          		</div>
          		{/if}
          		{if $spambot_prevention == 'recaptcha'}
          		<div class="form-group">
          			<div class="col-md-offset-2 col-md-10">
          				{$recaptcha_html}
          			</div>
          		</div>
          		{/if}
          		{/if}
              <div class="form-group">
                <div class="col-md-offset-2 col-md-10">
                  <button type="submit" name="Submit" value="{$lang.submit_send_msg}" class="btn btn-success" data-loading-text="{$lang.submit_send_msg}">{$lang.submit_send_msg}</button>
                </div>
              </div>
            </form>
            {/if}
      </div>
      <div class="col-sm-5 col-md-5"></div>
    </div>
  </div>
{include file='footer.tpl'}