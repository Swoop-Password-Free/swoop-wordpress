<?php if(!is_user_logged_in()) { ?>
  <a class='button swoop-login'
  href='<?php echo $loginUrl.'&user_meta[redirect_to]='.$redirectTo; ?>'>
  <?php echo $title; ?>
  </a>
<?php } else { ?>
  <a class='button swoop-logout'
  href="<?php echo $logoutUrl; ?>">Logout</a>
<?php } ?>
