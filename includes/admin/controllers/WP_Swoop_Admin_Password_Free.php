<?php
  include_once( plugin_dir_path( __FILE__ ) . '../../config.php' );

  class WP_Swoop_Admin_Password_Free {
    private $options;

    public function __construct($options) {
      $this->options = $options;
      add_action( 'admin_init', array( $this, 'page_init' ) );
      add_action( 'admin_init', array( $this, 'myPlugin_admin_scripts' ));
      // Ajax hooks
      add_action('wp_ajax_swoop_connected', array($this, 'swoop_connected'));
      add_action('wp_ajax_nopriv_swoop_connected', array($this, 'swoop_connected'));
      add_action('wp_ajax_swoop_disconnect', array($this, 'swoop_disconnect'));
      add_action('wp_ajax_nopriv_swoop_disconnect', array($this, 'swoop_disconnect'));

      if ( is_admin() ){ // for Admin Dashboard Only
         // Embed the Script on our Plugin's Option Page Only
         if ( isset($_GET['page']) && $_GET['page'] == 'swoop' ) {
            add_action('admin_enqueue_scripts', array($this,'enqueue_swoopconnect'),10);
            add_action('admin_footer_text', array( $this, 'swoop_admin_footer' ));
          }
      }

    }

    public function create() {
      $this->init_admin_ui();
    }

    function myPlugin_admin_scripts() {
       if ( is_admin() ){ // for Admin Dashboard Only
          // Embed the Script on our Plugin's Option Page Only
          if ( isset($_GET['page']) && $_GET['page'] == 'swoop' ) {
             wp_enqueue_script('jquery');
             wp_enqueue_script( 'jquery-form' );
          }
       }
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
        $blogname = html_entity_decode(get_option('blogname'));
        $blogname = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $blogname);
        ?>
        <script>
          jQuery(document).ready(() => {
            window.state = {};
            window.state.site = {
              siteRoot: '<?php echo site_url(); ?>',
              title: '<?php echo addslashes($blogname); ?>',
              url: '<?php echo site_url(); ?>',
              redirect_uri: '<?php echo site_url(  "wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK ); ?>'
            }

            window.state.swoop = {};
            window.state.swoop.pluginRoot = '<?php echo plugin_dir_url( __DIR__ ) . '../../'; ?>';
            window.state.swoop.siteRoot = '<?php echo site_url(); ?>';

            <?php if(isset($this->options[SWOOP_CLIENT_ID_KEY])) { ?>
              window.state.swoop = {
                ...window.state.swoop,
                organizationId: '<?php echo $this->options[SWOOP_ORGANIZATION_ID_KEY]; ?>',
                organizationName: '<?php echo $this->options[SWOOP_ORGANIZATION_NAME_KEY]; ?>',
                propertyId: '<?php echo $this->options[SWOOP_PROPERTY_ID_KEY]; ?>',
                propertyName: '<?php echo $this->options[SWOOP_PROPERTY_NAME_KEY]; ?>',
                adminName: '<?php echo $this->options[SWOOP_WP_ADMIN_KEY]; ?>',
                adminEmail: '<?php echo $this->options[SWOOP_WP_ADMIN_EMAIL_KEY]; ?>',
                connectedDate: '<?php echo $this->options[SWOOP_CONNECTED_DATE_KEY]; ?>'
              };

              swoop_render('#swoop',
              'Swoop there it is. Passwords are officially over!',
              swoop_connected());
            <?php } else { ?>
              swoop_render(
                '#swoop',
                'Welcome to a future free of passwords.',
                swoop_buttons()
              );
            <?php } ?>
          });
        </script>

      <?php
        include ( plugin_dir_path( __FILE__ ) . '../views/password-free.php' );
    }

    public function swoop_connected(){
      $current_user = wp_get_current_user();
      $options = get_option( SWOOP_OPTIONS_KEY );
      $options[SWOOP_CLIENT_ID_KEY] = $_POST['client_id'];
      $options[SWOOP_CLIENT_SECRET_KEY] = $_POST['client_secret'];
      $options[SWOOP_ORGANIZATION_NAME_KEY] = $_POST['organization_name'];
      $options[SWOOP_ORGANIZATION_ID_KEY] = $_POST['organization_id'];
      $options[SWOOP_PROPERTY_NAME_KEY] = $_POST['property_name'];
      $options[SWOOP_PROPERTY_ID_KEY] = $_POST['property_id'];
      $options[SWOOP_WP_ADMIN_KEY] = $current_user->user_login;
      $options[SWOOP_WP_ADMIN_EMAIL_KEY] = $current_user->user_email;
      $options[SWOOP_CONNECTED_DATE_KEY] = date("F j, Y");
      update_option( SWOOP_OPTIONS_KEY, $options);
      exit();
    }

    public function swoop_disconnect() {
      $options = get_option( SWOOP_OPTIONS_KEY );
      $options = [];
      update_option( SWOOP_OPTIONS_KEY, $options);
      exit();
    }

    public function swoop_admin_footer() {
        echo '
        <p id="footer-left" class="alignleft">Please rate Swoop <a href="https://wordpress.org/support/plugin/swoop-password-free-authentication/reviews/?filter=5#new-post"/><span class="gold">★★★★★</span> on WordPress.org</a> to help us spread the word. Thank you from the Swoop team!</p>
        ';
    }

    public function enqueue_swoopconnect() {
      wp_enqueue_script('swoopconnect_js', plugin_dir_url(__FILE__) . '../../assets/js/swoopconnect.js',10);
      wp_enqueue_script('swoop_admin_js', plugin_dir_url(__FILE__) . '../../assets/js/swoop_admin.js',10,1.3);
      wp_enqueue_style('bootstrap','https://getbootstrap.com/docs/4.1/dist/css/bootstrap.min.css', 1);
      wp_enqueue_style('swoopconnect_css', plugin_dir_url(__FILE__) . '../../assets/css/swoop-wordpress-admin.css',20);
      wp_enqueue_style('google_font', 'https://fonts.googleapis.com/css2?family=Lato&family=Rubik:wght@700&display=swap', 3);

    }
  }
?>
