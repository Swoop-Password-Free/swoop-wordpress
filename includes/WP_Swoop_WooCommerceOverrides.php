<?php
/**
 * Contains Class WP_Swoop_WooCommerceOverrides class.
 *
 * @package WP-Swoop
 *
 * @since 2.0.0
 */

/**
 * Class WP_Swoop_WooCommerceOverrides.
 */
class WP_Swoop_WooCommerceOverrides {

	/**
	 * Injected WP_Swoop_Options instance.
	 *
	 * @var WP_Swoop_Options
	 */
	protected $options;

	/**
	 * WP_Swoop_WooCommerceOverrides constructor.
	 *
	 * @param WP_Swoop_Options $options - WP_Swoop_Options instance.
	 */
	public function __construct( ) {

	}

	/**
	 * Render the login form or link to ULP.
	 *
	 * @param string $redirect_page - Page slug to redirect to after logging in.
	 */
	private function render_login_form( $redirect_page ) {
    // Redirecting to WordPress login page.
    $redirect_url = get_permalink( wc_get_page_id( $redirect_page ) );
    $login_url    = wp_login_url( $redirect_url );
    printf( "<a class='button' href='%s'>%s</a>", $login_url, __( 'Login', 'wp-Swoop' ) );
	}

	/**
	 * Handle Swoop login on the checkout form if the plugin is ready.
	 *
	 * @param string $html - Original HTML passed to filter.
	 *
	 * @return mixed
	 */
	public function override_woocommerce_checkout_login_form( $html ) {
		$this->render_login_form( 'checkout' );
	}

	/**
	 * Handle Swoop login on the account form if the plugin is ready.
	 *
	 * @param string $html - Original HTML passed to filter.
	 *
	 * @return mixed
	 */
	public function override_woocommerce_login_form( $html ) {
		$this->render_login_form( 'myaccount' );
	}
}

// functions for override
include_once( plugin_dir_path( __FILE__ ) . 'functions.php' );
function wp_swoop_filter_woocommerce_checkout_login_message( $html ) {
	$wp_swoop_woocommerce = new WP_Swoop_WooCommerceOverrides( );
	return $wp_swoop_woocommerce->override_woocommerce_checkout_login_form( $html );
}
add_filter( 'woocommerce_checkout_login_message', 'wp_swoop_filter_woocommerce_checkout_login_message' );

/**
 * Add the Swoop login form to the account page.
 *
 * @param string $html - Original HTML passed to this hook.
 *
 * @return mixed
 */
function wp_swoop_filter_woocommerce_before_customer_login_form( $html ) {
	$wp_swoop_woocommerce = new WP_Swoop_WooCommerceOverrides(  );
	return $wp_swoop_woocommerce->override_woocommerce_login_form( $html );
}
add_filter( 'woocommerce_before_customer_login_form', 'wp_swoop_filter_woocommerce_before_customer_login_form' );

if(is_woocommerce_activated()) {
  function enqueue_swoop_css() {
    wp_enqueue_style( 'swoop-login',  plugin_dir_url(__FILE__) . 'css/login.css',10 );
  }
  add_action('wp_enqueue_scripts', 'enqueue_swoop_css', 10);
}



?>
