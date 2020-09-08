<form name="" id="" action="http://localhost:8888/wordpress/wp-login.php?action=register" method="post" novalidate="novalidate">
	<p>
		<label for="user_login">Username</label>
		<input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" />
	</p>
	<p>
		<label for="user_email">Email</label>
		<input type="email" name="user_email" id="user_email" class="input" value="" size="25" />
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
	<!-- <input type="hidden" name="redirect_to" value="" /> -->
	<input type="hidden" name="swoop_register" value="true" />
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Register" />
	</p>
</form>
