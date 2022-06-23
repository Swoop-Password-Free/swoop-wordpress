<!-- <div class="swoop-button"
data-redirect_to="<?php echo isset($_GET['redirect_to']) ? $_GET['redirect_to'] : get_admin_url(); ?>">
</div> -->

<div class="click-button">        
    <div class="swoop-button"              
              <?php echo $backgroundColor ? "data-background-color=\"$backgroundColor\"" : ""; ?>
              <?php echo $textColor ? "data-text-color=\"$textColor\"" : ""; ?>
              data-redirect_to="<?php echo isset($_GET['redirect_to']) ? $_GET['redirect_to'] : get_admin_url(); ?>">
            ></div>
</div>
<div class="tagline">Sign In with 1-Click is more secure</div>
<?php if($hide_login_with_password == null || !$hide_login_with_password) { ?>
  <div>
    <a href="<?php echo site_url(); ?>/wp-login.php?use-password=true">Login with password</a>
  </div>
<?php } ?>
