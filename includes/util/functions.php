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

function swoop_login_url($redirect_to = "") {
  global $wp_swoop;
  if($wp_swoop) {
    return $wp_swoop->swoop_login_url($redirect_to);
  } else {
    return "Swoop not defined";
  }
}
?>
