<?php
  include_once( plugin_dir_path( __FILE__ ) . '../../config.php' );

  class WP_Swoop_Admin_Password_Free {
    private $options;

    public function __construct($options) {
      $this->options = $options;
      add_action( 'admin_init', array( $this, 'page_init' ) );

      if ( is_admin() ){ // for Admin Dashboard Only
         // Embed the Script on our Plugin's Option Page Only
         if ( isset($_GET['page']) && $_GET['page'] == 'swoop' ) {
            add_action('admin_footer_text', array( $this, 'swoop_admin_footer' ));
            add_action('admin_enqueue_scripts', array($this,'enqueue_css'),10);
          }
          // add_action( 'admin_post', array( $this, 'save' ) );
          add_action( 'admin_post_swoop',array( $this, 'save' ) );
          add_action( 'admin_post_nopriv_swoop', array( $this, 'save' ) );
      }

    }

    public function create() {
      $this->init_admin_ui();
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            SWOOP_OPTIONS_GROUP, // Option group
            SWOOP_OPTIONS_KEY // Option name
        );
    }

    /**
     * Print the Section text
     */
    public function init_admin_ui()
    {
      $options = get_option( SWOOP_OPTIONS_KEY, array() );
      $client_id = array_key_exists(SWOOP_CLIENT_ID_KEY, $options) ? $options[SWOOP_CLIENT_ID_KEY] : "";
      $client_secret = array_key_exists(SWOOP_CLIENT_SECRET_KEY, $options) ? $options[SWOOP_CLIENT_SECRET_KEY] : "";
      $login_button_text_color = array_key_exists(SWOOP_LOGIN_BUTTON_TEXT_COLOR_KEY, $options) ? $options[SWOOP_LOGIN_BUTTON_TEXT_COLOR_KEY] : "#ffffff";
      $login_button_background_color = array_key_exists(SWOOP_LOGIN_BUTTON_BACKGROUND_COLOR_KEY, $options) ? $options[SWOOP_LOGIN_BUTTON_BACKGROUND_COLOR_KEY] : "#000000";
        $hide_login_with_password = array_key_exists(SWOOP_HIDE_LOGIN_WITH_PASSWORD_KEY, $options) ? $options[SWOOP_HIDE_LOGIN_WITH_PASSWORD_KEY] : "";

      include ( plugin_dir_path( __FILE__ ) . '../views/password-free.php' );
    }

    public function save() {

        // First, validate the nonce and verify the user as permission to save.
        if ( ! ( $this->has_valid_nonce() && current_user_can( 'manage_options' ) ) ) {
            // TODO: Display an error message.
            alert('Invalid nonce');
            return;
        }

        // If the above are valid, sanitize and save the option.
        if ( null !== wp_unslash( $_POST[SWOOP_CLIENT_ID_KEY] ) ) {
            $value = sanitize_text_field( $_POST[SWOOP_CLIENT_ID_KEY] );
            $this->options[SWOOP_CLIENT_ID_KEY] = $value;
            update_option( SWOOP_OPTIONS_KEY, $this->options );
        }

        if ( null !== wp_unslash( $_POST[SWOOP_CLIENT_SECRET_KEY] ) ) {
            $value = sanitize_text_field( $_POST[SWOOP_CLIENT_SECRET_KEY] );
            $this->options[SWOOP_CLIENT_SECRET_KEY] = $value;
            update_option( SWOOP_OPTIONS_KEY, $this->options );
        }

        if ( null !== wp_unslash( $_POST[SWOOP_LOGIN_BUTTON_TEXT_COLOR_KEY] ) ) {
            $value = sanitize_text_field( $_POST[SWOOP_LOGIN_BUTTON_TEXT_COLOR_KEY] );
            $this->options[SWOOP_LOGIN_BUTTON_TEXT_COLOR_KEY] = $value;
            update_option( SWOOP_OPTIONS_KEY, $this->options );
        }

        if ( null !== wp_unslash( $_POST[SWOOP_LOGIN_BUTTON_BACKGROUND_COLOR_KEY] ) ) {
            $value = sanitize_text_field( $_POST[SWOOP_LOGIN_BUTTON_BACKGROUND_COLOR_KEY] );
            $this->options[SWOOP_LOGIN_BUTTON_BACKGROUND_COLOR_KEY] = $value;
            update_option( SWOOP_OPTIONS_KEY, $this->options );
        }

        if ( null !== wp_unslash( $_POST[SWOOP_HIDE_LOGIN_WITH_PASSWORD_KEY] ) ) {
            $value = sanitize_text_field( $_POST[SWOOP_HIDE_LOGIN_WITH_PASSWORD_KEY] );
            $this->options[SWOOP_HIDE_LOGIN_WITH_PASSWORD_KEY] = $value;
            update_option( SWOOP_OPTIONS_KEY, $this->options );
        } else {
            $this->options[SWOOP_HIDE_LOGIN_WITH_PASSWORD_KEY] = false;
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

    public function enqueue_css() {
      wp_enqueue_style('swoop_css', plugin_dir_url(__FILE__) . '../../assets/css/swoop-admin.css',20);
    }

    public function swoop_admin_footer() {
        echo '<hr />
        <ul id="swoop-footer">
            <li><a href="https://docs.swoopnow.com/docs/wordpress" target="_blank">Documentation</li>
            <li><a href="https://swoopnow.com/support" target="_blank">Support</li>
            <li><a href="https://dashboard.swoop.email/" target="_blank">1-Click Login Dashboard</a></li>
            <li><a href="https://swoopnow.com/" target="_blank">Learn more about 1-Click Login</a></li>
        </ul>';
    }
  }
?>
