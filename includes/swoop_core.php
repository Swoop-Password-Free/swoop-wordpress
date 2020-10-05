<?php
include_once("config.php");
include_once("Swoop.php");
include_once("WP_Swoop_Shortcodes.php");

class SwoopCore {

  private $options;
  private $swoop;

  public function __construct($file) {

    $this->options = get_option( SWOOP_OPTIONS_KEY );
    register_uninstall_hook($file, array('SwoopCore', 'uninstall'));

    add_action( 'rest_api_init', function () {
      register_rest_route(SWOOP_PLUGIN_NAMESPACE , SWOOP_PLUGIN_CALLBACK , array(
        'methods' => 'GET',
        'callback' => array('SwoopCore','swoop_callback'),
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

      // Admin Actions
      add_action(  'login_init', array($this,'swoop_login_init')  );
      add_action('wp_logout',array($this,'swoop_logout'));
      // Filters.
      add_filter( 'allowed_http_origins', array($this, 'add_swoop_to_origins') );

      new WP_Swoop_Shortcodes($this->swoop);
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

  function swoop_login_init () {

    $action = isset($_GET['action']) ? $_GET['action'] : 'login';
    $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : null;

    // Allows the user to get back to wp login
    if($action === 'wp-login') {
      return;
    }

    if($action == 'logout' || $action == 'register') {
      return;
    }

    $login_url = $this->swoop->loginUrl();

    if($redirect_to) {
      $login_url = $this->swoop->loginUrl(array(
        "user_meta" => array(
          "redirect_to" => $redirect_to
        )
      ));
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

}
