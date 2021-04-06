<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

<div class="u-columns col2-set" id="customer_login">
	<div class="u-column1 col-1">
    <h2 class="swoop"><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>
    <!-- login -->
    <?php include 'swoop_login.php'; ?>
  </div>

  <div class="u-column2 col-2">
    <h2 class="swoop"><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>
    <!-- register -->
    <?php include 'swoop_register.php'; ?>
  </div>
</div>

<?php endif; ?>
