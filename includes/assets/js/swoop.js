function show_wordpress_login() {
  jQuery(function($) {
    // Code goes here
    $('#loginform > p:first-child').css('display', '');
    $('#loginform > .user-pass-wrap').css('display', '');
    $('#loginform > .forgetmenot').css('display', '');
    $('#wp-submit').css('display', '');
    $('#login_with_wordpress').css('display', 'none');
    $('#swoop_button').css('display', 'none');

    $('#user_pass').removeAttr("disabled");
  });
}

function show_wordpress_register() {
  jQuery(function($) {
    // Code goes here
    $('#registerform > p:first-child').css('display', '');
    $('#registerform > p').css('display', '');
    $('#registerform > .user-pass-wrap').css('display', '');
    $('#registerform > .forgetmenot').css('display', '');
    $('#user_email').css('display', '');
    $('#reg_passmail').css('display', '');
    $('#wp-submit').css('display', '');

    $('#register_with_wordpress').css('display', 'none');
    $('#swoop_button').css('display', 'none');

    $('#user_pass').removeAttr("disabled");
  });
}

document.addEventListener("DOMContentLoaded", function(event) {
  jQuery(document).ready(function($) {
    $('#loginform > p:first-child').css('display', 'none');
    $('#loginform > .user-pass-wrap').css('display', 'none');
    $('#loginform > .forgetmenot').css('display', 'none');
    $('#wp-submit').css('display', 'none');

    $('#registerform > p:first-child').css('display', 'none');
    $('#registerform > p').css('display', 'none');
    $('#user_email').css('display', 'none');
    $('#reg_passmail').css('display', 'none');
    $('#registerform > .user-pass-wrap').css('display', 'none');
    $('#registerform > .forgetmenot').css('display', 'none');
    $('#wp-submit').css('display', 'none');

    var show = document.createElement("p");
    show.innerHTML = '<div id="login_with_wordpress"><center>- OR - <br><a href="#" onClick=\"show_wordpress_login()\">Log In With Wordpress</a></cente></div>';
    $('#loginform').append(show);

    var show = document.createElement("p");
    show.innerHTML = '<div id="register_with_wordpress"><center>- OR - <br><a href="#" onClick=\"show_wordpress_register()\">Register With Wordpress</a></cente></div>';
    $('#registerform').append(show);
  });
})
