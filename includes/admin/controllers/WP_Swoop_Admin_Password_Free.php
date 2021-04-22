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
            add_action('admin_enqueue_scripts', array($this,'enqueue_swoopconnect'),10);
            add_action('admin_footer_text', array( $this, 'swoop_admin_footer' ));
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
      include ( plugin_dir_path( __FILE__ ) . '../views/password-free.php' );
    }

    public function swoop_admin_footer() {
        echo '
        <p id="footer-left" class="alignleft">Please rate Swoop <a href="https://wordpress.org/support/plugin/swoop-password-free-authentication/reviews/?filter=5#new-post"/><span class="gold">★★★★★</span> on WordPress.org</a> to help us spread the word. Thank you from the Swoop team!</p>
        ';
    }

    public function enqueue_swoopconnect() {
      // wp_enqueue_script('swoopconnect_js', plugin_dir_url(__FILE__) . '../../assets/js/swoopconnect.js',10);
      // wp_enqueue_script('swoop_admin_js', plugin_dir_url(__FILE__) . '../../assets/js/swoop_admin.js',10,1.3);
      wp_enqueue_style('bootstrap','https://getbootstrap.com/docs/4.1/dist/css/bootstrap.min.css', 1);
      wp_enqueue_style('swoopconnect_css', plugin_dir_url(__FILE__) . '../../assets/css/swoop-wordpress-admin.css',20);
      // wp_enqueue_style('google_font', 'https://fonts.googleapis.com/css2?family=Lato&family=Rubik:wght@700&display=swap', 3);

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
  }
?>
