<?php if(!is_user_logged_in()) { ?>
<form name="swoop_registerform" id="swoop_registerform" action="<?php echo $registerUrl; ?>" method="post" novalidate="novalidate">
	<p>
		<label for="user_login">Username</label>
		<input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" />
	</p>
	<p>
		<label for="user_email">First Name</label>
		<input type="text" name="first_name" id="first_name" class="input" value="" size="25" />
	</p>
	<p>
		<label for="user_email">Last Name</label>
		<input type="text" name="last_name" id="last_name" class="input" value="" size="25" />
	</p>
	<br class="clear" />
	<input type="hidden" name="redirect_to" value="<?php echo $redirectTo; ?>" />
	<input type="hidden" name="swoop_register" value="true" />
	<!-- <p class="submit"> -->
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Register" />
	<!-- </p> -->
</form>
<?php } else { ?>
  <a class='button swoop-logout'
  href="<?php echo $logoutUrl; ?>">Logout</a>
<?php } ?>
