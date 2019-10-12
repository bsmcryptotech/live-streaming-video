<form name="forgot-pass" id="reset-form" method="post" action="{$smarty.const._URL}/login.php?do=forgot_pass">
    <div class="form-group">
      <label>{$lang.your_username_or_email}</label>
      <div class="controls"><input type="text" class="form-control" name="username_email" placeholder="" value="{$inputs.username_email}"></div>
    </div>
    <div class="form-group">
    <button type="submit" name="Send" value="{$lang.submit_send}" class="btn btn-success btn-with-loader" data-loading-text="{$lang.submit_send}">{$lang.submit_send}</button>
    </div>
</form>