<?php
  include_once( plugin_dir_path( __FILE__ ) . '../../config.php' );

  class WP_Swoop_Admin_page_Protect {
    private $options;

    public function __construct($options) {
      $this->options = $options;
      add_action( 'admin_post', array( $this, 'save' ) );
    }

    public function create() {
      $pages = get_pages();
      $swoopProtectRedirectPageID = $this->options[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY];
      include( plugin_dir_path( __FILE__ ) . '../views/page-protection.php' );
    }

    public function save() {

        // First, validate the nonce and verify the user as permission to save.
        if ( ! ( $this->has_valid_nonce() && current_user_can( 'manage_options' ) ) ) {
            // TODO: Display an error message.
            alert('Invalid nonce');
            return;
        }

        // If the above are valid, sanitize and save the option.
        if ( null !== wp_unslash( $_POST[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY] ) ) {
            $value = sanitize_text_field( $_POST[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY] );            
            $this->options[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY] = $value;
            update_option( SWOOP_OPTIONS_KEY, $this->options );
        }

        $this->redirect();

    }

    /**
     * Determines if the nonce variable associated with the options page is set
     * and is valid.
     *
     * @access private
     *
     * @return boolean False if the field isn't set or the nonce value is invalid;
     *                 otherwise, true.
     */
    private function has_valid_nonce() {

        // If the field isn't even in the $_POST, then it's invalid.
        if ( ! isset( $_POST[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY] ) ) { // Input var okay.
            return false;
        }

        $field  = wp_unslash( $_POST[SWOOP_PROTECT_NONCE_KEY] );
        $action = SWOOP_PROTECT_NONCE_KEY . '-save';

        return wp_verify_nonce( $field, $action );

    }

    private function redirect() {

        // To make the Coding Standards happy, we have to initialize this.
        if ( ! isset( $_POST['_wp_http_referer'] ) ) { // Input var okay.
            $_POST['_wp_http_referer'] = wp_login_url();
        }

        // Sanitize the value of the $_POST collection for the Coding Standards.
        $url = sanitize_text_field(
                wp_unslash( $_POST['_wp_http_referer'] ) // Input var okay.
        );

        // Finally, redirect back to the admin page.
        wp_safe_redirect( urldecode( $url ) );
        exit;

    }
  }
?>
