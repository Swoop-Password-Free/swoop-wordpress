<?php
include_once("config.php");

class SwoopCore {

  private $options;

  public function __construct($file) {

    $this->options = get_option( SWOOP_OPTIONS_KEY );
    register_uninstall_hook($file, array('SwoopCore', 'uninstall'));

    add_action( 'rest_api_init', function () {
      register_rest_route(SWOOP_PLUGIN_NAMESPACE , SWOOP_PLUGIN_CALLBACK , array(
        'methods' => 'GET',
        'callback' => array('SwoopCore','swoop_callback'),
        'args' => array('code')
      ) );
    } );


    if(isset($this->options[SWOOP_CLIENT_ID_KEY])) {
      // Admin Actions
      add_action(  'login_init', array($this,'swoop_login_init')  );
      add_action('wp_logout',array($this,'swoop_logout'));
      // Filters.
      add_filter( 'allowed_http_origins', array($this, 'add_swoop_to_origins') );

      $this->remove_login_form();
    }
  }

  static function swoop_callback( $data ) {
    $options = get_option( SWOOP_OPTIONS_KEY );
    $client_id = $options[SWOOP_CLIENT_ID_KEY];
    $client_secret = $options[SWOOP_CLIENT_SECRET_KEY];
    $response = wp_remote_post( SWOOP_URL . SWOOP_TOKEN_ENDPOINT,array(
      'method' => 'POST',
      'timeout' => 45,
      'redirection' => 5,
      'httpversion' => '1.0',
      'blocking' => true,
      'headers' => array(),
      'body' => array(
          'code' => $data['code'],
          'client_id' => $client_id,
          'client_secret' => $client_secret,
          'redirect_uri' => site_url(  "wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK )
      ),
      'cookies' => array()
    ));

    if ( is_wp_error( $response ) ) {
       $error_message = $response->get_error_message();
       echo "Something went wrong: $error_message";
       exit(0);
    } else {
      if($response['response']['code'] == 401) {
        echo '<pre>';
        print_r(json_decode($response['body']));
        echo '</pre>';
        exit(0);
      }
    }

    $body = json_decode($response['body']);
    $id_token = $body->{'id_token'};
    $decoded = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $id_token)[1]))));
    $email_address = $decoded->{'email'};
    $user = get_user_by('email', $email_address);
    $redirect_to = admin_url();

    if(isset($decoded->{'user_meta'}) && isset($decoded->{'user_meta'}->{'redirect_to'})) {
      $redirect_to = $decoded->{'user_meta'}->{'redirect_to'};
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
          $user_id = wp_create_user($email_address, $random_password, $email_address);
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

  function swoop_login_init () {

    $action = isset($_GET['action']) ? $_GET['action'] : 'login';
    $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : null;

    if($action == 'logout' || $action == 'register') {
      return;
    }

    $login_url = SWOOP_URL.SWOOP_AUTH_ENDPOINT.
    '?client_id='.$this->options[SWOOP_CLIENT_ID_KEY].
    '&redirect_uri='.site_url(  "wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK ).
    '&scope=email'.
    '&response_type=code';

    if($redirect_to) {
      $login_url = $login_url . '&user_meta[redirect_to]=' . $redirect_to;
    }

    if( ! is_user_logged_in() ) {
      wp_redirect( $login_url );
      exit;
    }
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

  function enqueue_swoop_js($hook) {
    wp_enqueue_script('swoop_js', plugin_dir_url(__FILE__) . 'js/swoop.js',10);
  }

  // Remove Login Form
  public function remove_login_form() {
	  add_action('login_enqueue_scripts', array($this,'enqueue_swoop_js'),10);
  }
}
