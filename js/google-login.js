var googleUser = {};
var gapi_startApp = function() {
  $('#google-login-btn').click(function(e){
    e.preventDefault();
  })
  $('.btn-google').click(function(e){
    e.preventDefault();
  })
  $('#google-register-btn').click(function(e){
    e.preventDefault();
  })
  gapi.load('auth2', function() {
    // Retrieve the singleton for the GoogleAuth library and set up the client.
    auth2 = gapi.auth2.init({
      client_id: gapi_clientid,
      cookiepolicy: 'single_host_origin',
      // Request scopes in addition to 'profile' and 'email'
      //scope: 'additional_scope'
    });
    attachSignin(document.getElementById('google-login-btn'));
    attachSignin(document.getElementById('google-register-btn'));
    // var googleButtons = document.getElementsByClassName('btn-google');
    // for (i = 0; i < googleButtons.length; i++) {
    //   attachSignin(googleButtons[i]);
    // }
  });
};

function attachSignin(element) {
  auth2.attachClickHandler(element, {},
      function(googleUser) { // onSuccess
        var profile = googleUser.getBasicProfile();
        var id_token = googleUser.getAuthResponse().id_token;

        $('.google-login-response').addClass('alert').html(pm_lang.please_wait).show();

        $.ajax({
              url: MELODYURL2 + "/login.php?do=google&action=tokensignin",
              method: "post",
              data: { 
                "idtoken": id_token 
              },
              dataType: "json",
              success: function(data) {
                if (data.type == 'success') {
                  if (data.msg != null && data.msg.length > 0) { 
                    $('.google-login-response').addClass('alert alert-success animated pulse').html(data.msg).show();
                  }
                  if (data.redirect_url != '') {
                    window.location.href = data.redirect_url;
                  }
                  googleUser.disconnect();
                } else if (data.type == 'warning') {
                  $('.google-login-response').addClass('alert alert-warning animated pulse').html(data.msg).show();
                } else {
                  $('.google-login-response').addClass('alert alert-danger animated pulse').html(data.error_msg).show();
                }
              }
          });
      }, function(error) { // onFailure
        console.log(JSON.stringify(error, undefined, 2));
        if (error.error != 'popup_closed_by_user') {
          $('.google-login-response').addClass('alert alert-error animated pulse').html(JSON.stringify(error, undefined, 2)).show();
        }
      });
}

window.onbeforeunload = function(e) {
  var auth = gapi.auth2.getAuthInstance();
  auth.disconnect();
};