<?php
/**
* Check if WooCommerce is activated
*/
function is_woocommerce_activated() {
  return in_array(
    'woocommerce/woocommerce.php',
    apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
  );
}
?>
