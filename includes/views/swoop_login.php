<?php if(!is_user_logged_in()) { ?>
  <?php if($custom) { ?>
    <a class='button swoop-login' id='swoop-login-button'
    href='#' onclick="swoop.in({redirect_to: '<?php echo $redirectTo; ?>'}); return false;">
    <?php echo $title; ?>
    </a>
  <?php } else { ?>
    <div class="swoop-button"></div>
  <?php } ?>
<?php } else { ?>
  <a class='button swoop-logout'
  href="<?php echo $logoutUrl; ?>">Logout</a>
<?php } ?>
