<?php
include_once("config.php");
include_once("util/Swoop.php");
include_once("WP_Swoop_Shortcodes.php");
// include_once("WP_Swoop_Protect.php");

class WP_Swoop {
    private $options;
    private $swoop;

    public function __construct($file) {

        $this->options = get_option( SWOOP_OPTIONS_KEY );        

        register_uninstall_hook($file, array('WP_Swoop', 'uninstall'));

        if(isset($this->options[SWOOP_CLIENT_ID_KEY])) {

          $this->swoop = new Swoop(
              isset($this->options[SWOOP_CLIENT_ID_KEY]) ? $this->options[SWOOP_CLIENT_ID_KEY] : "",
              isset($this->options[SWOOP_CLIENT_SECRET_KEY]) ? $this->options[SWOOP_CLIENT_SECRET_KEY] : "",
              site_url(  plugin_dir_url( __DIR__ ) )
          );

          add_action( 'rest_api_init', function () {
            register_rest_route(SWOOP_PLUGIN_NAMESPACE , SWOOP_PLUGIN_CALLBACK , array(
              'methods' => 'GET',
              'callback' => array('WP_Swoop','swoop_callback'),
              'args' => array('code'),
              'permission_callback' => '__return_true'
            ) );
          } );

          if(!isset($_GET["use-password"])) {
              add_action( 'login_form', array($this, 'add_swoop_login_button') );
              add_action( 'register_form', array($this, 'add_swoop_signup_button'));
              $this->add_swoop_js();
              add_action('wp_logout',array($this,'swoop_logout'));
              // Add hook for admin <head></head>
              add_action( 'login_head', array($this, 'add_swoop_init_to_login_head') );
              add_action( 'login_footer', array($this, 'add_swoop_to_login_footer') , 1000000000);
              add_action( 'wp_head', array($this, 'add_swoop_init_to_wp_head') );
              add_action( 'wp_footer', array($this, 'add_swoop_to_footer') , 1000000000);

              add_action('init', array($this, 'register_swoop_callback'),1);

              new WP_Swoop_Shortcodes($this->swoop);
          }
        }
    }

    function register_swoop_callback() {
      if(isset($_GET['token'])) {
        $this->swoop_callback(['token' => $_GET['token']]);
      }
    }

    function swoop_logout(){
        wp_redirect( site_url() );
        exit();
    }
    public static function uninstall() {
        delete_option(SWOOP_OPTIONS_KEY);
    }

    function add_swoop_init_to_login_head() {
        $this->addSwoopJSFunctions();
    }

    function add_swoop_init_to_wp_head() {
        $this->addSwoopJSFunctions();
    }

    function addSwoopJSFunctions() {
        $clientId = isset($this->options[SWOOP_CLIENT_ID_KEY]) ? $this->options[SWOOP_CLIENT_ID_KEY] : "";
        echo "<meta name=\"swoop-client-id\" content=\"$clientId\">\n";
    }

    function add_swoop_login_button() {
        $this->add_swoop_button('login');
    }

    function add_swoop_signup_button() {
        $this->add_swoop_button('signup');
    }

    function add_swoop_button($title) {
      $textColor = isset($this->options[SWOOP_LOGIN_BUTTON_TEXT_COLOR_KEY]) ? $this->options[SWOOP_LOGIN_BUTTON_TEXT_COLOR_KEY] : null;
      $backgroundColor = isset($this->options[SWOOP_LOGIN_BUTTON_BACKGROUND_COLOR_KEY]) ? $this->options[SWOOP_LOGIN_BUTTON_BACKGROUND_COLOR_KEY] : null;
      $hide_login_with_password = isset($this->options[SWOOP_HIDE_LOGIN_WITH_PASSWORD_KEY]) ? $this->options[SWOOP_HIDE_LOGIN_WITH_PASSWORD_KEY] : null;
      include 'views/swoop_wp_login.php';
    }

    // Remove Login Form
    public function add_swoop_js() {
        add_action('login_enqueue_scripts', array($this,'enqueue_login_swoop_js'),10);
        add_action('wp_enqueue_scripts', array($this,'enqueue_swoop_js'),10);

        add_filter( 'script_loader_tag', function ( $tag, $handle ) {

            if ( 'swoop-login-js' !== $handle ) {
                return $tag;
            }

            return str_replace( ' src', ' async defer src', $tag ); // defer the script

        }, 10, 2 );
    }

    public function enqueue_swoop_js($hook) {        
        wp_enqueue_script( 'swoop-login-js', 'https://cdn.jsdelivr.net/npm/@swoop-password-free/swoop@1.4.11/dist/swoop.js' );
    }

    public function enqueue_login_swoop_js($hook) {
      wp_enqueue_style( 'swoop-login', plugin_dir_url(__FILE__) . 'assets/css/swoop-login.css' );
      wp_enqueue_script( 'swoop-login-js', 'https://cdn.jsdelivr.net/npm/@swoop-password-free/swoop@1.4.11/dist/swoop.js' );
  }

    public function add_swoop_to_footer() {
        include 'views/swoop_js.php';        
    }

    public function add_swoop_to_login_footer() {
      include 'views/swoop_js.php';
      include 'views/swoop_wp_login_footer.php';
  }

    static function swoop_callback( $data ) {

        $options = get_option( SWOOP_OPTIONS_KEY );
        $swoop = new Swoop(
          $options[SWOOP_CLIENT_ID_KEY],
          $options[SWOOP_CLIENT_SECRET_KEY],
          site_url(  "wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK )
        );
        $meta = null;

        if(isset($data['code'])) {
          $meta = $swoop->callback($data['code']);
        } else if(isset($data['token'])) {
          $meta = $swoop->decodeToken($data['token']);
        }

        if(!$meta) {
          echo json_encode(['redirect_to' => site_url()]);
          exit(0);
        }

        $user = get_user_by('email', $meta->email);
        $redirect_to = admin_url();

        if(isset($meta->{'user_meta'}) &&
        isset($meta->{'user_meta'}->{'redirect_to'}) &&
        strlen($meta->{'user_meta'}->{'redirect_to'}) > 0) {
          $redirect_to = urldecode($meta->{'user_meta'}->{'redirect_to'});
        }

        $redirect_to = str_replace('&reauth=1', '', $redirect_to);

        if ($user) {
          try {
            $user_id = $user->ID;
            wp_set_auth_cookie($user_id);
          } catch (Exception $e) {
            error_log('exception');
            echo json_encode(['error' => 'Something went wrong. Please try again.']);
            exit(0);
          }
        } else {
          try {
            if (get_option('users_can_register')) {
              $random_password = wp_generate_password();

              $username = isset($meta->user_meta) && isset($meta->user_meta->user_login) ?
              $meta->user_meta->user_login :
              $meta->email;

              $user_id = wp_create_user($username, $random_password, $meta->email);

              // Update meta if registering
              if($meta->user_meta) {
                foreach ($meta->user_meta as $key => $value) {
                  update_user_meta($user_id, $key, $value);
                }
              }

              wp_set_auth_cookie($user_id);
            } else {
              //  TODO: Do something if users cant register
              // Actually it's not super important
              // echo 'User registration is disabled. Please contact the site administrator.';
              echo json_encode(['error' => "Account Not Found!\nUser registration is disabled. Please contact the site administrator."]);
              exit(0);
            }
          } catch (Exception $e) {
            error_log('exception');
            echo json_encode(['error' => 'Something went wrong. Please try again.']);
            exit(0);
          }
        }

        echo json_encode(['redirect_to' => $redirect_to]);
        exit(0);
      }
}
