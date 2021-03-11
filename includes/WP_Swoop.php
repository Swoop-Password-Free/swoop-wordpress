<?php
include_once("config.php");
include_once("util/Swoop.php");
include_once("WP_Swoop_Shortcodes.php");
include_once("WP_Swoop_Protect.php");

class WP_Swoop {

  private $options;
  private $swoop;

  public function __construct($file) {

    $this->options = get_option( SWOOP_OPTIONS_KEY );    
    register_uninstall_hook($file, array('WP_Swoop', 'uninstall'));

    add_action( 'rest_api_init', function () {
      register_rest_route(SWOOP_PLUGIN_NAMESPACE , SWOOP_PLUGIN_CALLBACK , array(
        'methods' => 'GET',
        'callback' => array('WP_Swoop','swoop_callback'),
        'args' => array('code'),
        'permission_callback' => '__return_true'
      ) );
    } );

    if(isset($this->options[SWOOP_CLIENT_ID_KEY])) {

      $this->swoop = new Swoop(
        $this->options[SWOOP_CLIENT_ID_KEY],
        $this->options[SWOOP_CLIENT_SECRET_KEY],
        site_url(  "wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK )
      );

      add_action( 'login_form', array($this, 'add_swoop_login_button') );
      add_action( 'register_form', array($this, 'add_swoop_signup_button'));
      $this->remove_login_form();

      add_action('wp_logout',array($this,'swoop_logout'));
      add_filter( 'allowed_http_origins', array($this, 'add_swoop_to_origins') );

      new WP_Swoop_Shortcodes($this->swoop);
      new WP_Swoop_Protect($this->swoop);
    }
  }

  static function swoop_callback( $data ) {
    $options = get_option( SWOOP_OPTIONS_KEY );
    $swoop = new Swoop(
      $options[SWOOP_CLIENT_ID_KEY],
      $options[SWOOP_CLIENT_SECRET_KEY],
      site_url(  "wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK )
    );
    $meta = $swoop->callback($data['code']);

    if(!$meta) {
      wp_redirect( site_url() );
    }

    $user = get_user_by('email', $meta->email);
    $redirect_to = admin_url();

    if(isset($meta->{'user_meta'}) &&
    isset($meta->{'user_meta'}->{'redirect_to'}) &&
    strlen($meta->{'user_meta'}->{'redirect_to'}) > 0) {
      $redirect_to = $meta->{'user_meta'}->{'redirect_to'};
    }

    if ($user) {
      try {
        $user_id = $user->ID;
        wp_set_auth_cookie($user_id);
      } catch (Exception $e) {
        error_log('exception');
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
        }
      } catch (Exception $e) {
        error_log('exception');
      }
    }

    wp_redirect($redirect_to);
    exit;
  }

  function swoop_logout(){
   wp_redirect( site_url() );
   exit();
  }

  public function add_swoop_to_origins( $origins ) {
    $origins[] = 'https://auth.swoop.email';
    return $origins;
  }

  public static function uninstall() {
    delete_option(SWOOP_OPTIONS_KEY);
  }

  public function add_swoop_login_button() {
    if (isset($_GET['reauth'])) {
      echo '<div>Unable to log in. Please ensure that you have a valid account on this website or that users are allowed to register.</div><br>';
    }

    $redirectTo = $_GET['redirect_to'];
    $redirectQuery = '';
    if($redirectTo) {
      $redirectQuery = '&user_meta[redirect_to]=' . $redirectTo;
    }

    echo '<a href="'.SWOOP_URL.SWOOP_AUTH_ENDPOINT.
    '?client_id='.$this->options[SWOOP_CLIENT_ID_KEY].
    '&redirect_uri='.site_url(  "wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK ).
    '&scope=email'.
    $redirectQuery .
    '&response_type=code">'.
    '<img id=\'swoop_button\' style=\'display: block; max-width: 100%; margin: 0px auto 15px;\' src=\'' . plugin_dir_url( __DIR__ ) . 'includes/assets/images/button-swoop.svg' . '\' alt=\'Swoop In With Email\' >
    </a>';
  }

  public function add_swoop_signup_button() {
    $redirectTo = $_GET['redirect_to'];
    $redirectQuery = '';
    if($redirectTo) {
      $redirectQuery = '&user_meta[redirect_to]=' . $redirectTo;
    }

    echo '<a href="'.SWOOP_URL.SWOOP_AUTH_ENDPOINT.
    '?client_id='.$this->options[SWOOP_CLIENT_ID_KEY].
    '&redirect_uri='.site_url(  "wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK ).
    '&scope=email'.
    $redirectQuery .
    '&response_type=code">'.
    '<img id=\'swoop_button\' style=\'display: block; max-width: 100%; margin: 0px auto 15px;\' src=\'' . plugin_dir_url( __DIR__ ) . 'includes/assets/images/button-swoop.svg' . '\' alt=\'Swoop In With Email\' >
    </a>';
  }

  public function enqueue_swoop_js($hook) {
    wp_enqueue_script('swoop_js', plugin_dir_url(__FILE__) . 'assets/js/swoop-login.js',10);
  }

  // Remove Login Form
  public function remove_login_form() {
	  add_action('login_enqueue_scripts', array($this,'enqueue_swoop_js'),10);
  }

  // Theme funtions
  public function swoop_login_url($params) {
    if(is_string($params) && strlen($params) > 0) {
      return $this->swoop->loginUrl(array(
        "user_meta" => array(
          "redirect_to" => $params
        )
      ));
    } else {
      return $this->swoop->loginUrl();
    }
  }

  public function activated() {
    echo 'Foo';
  }

}
