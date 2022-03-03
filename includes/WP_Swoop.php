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

      if(!isset($_GET["use-password"])) {
        add_action( 'login_form', array($this, 'add_swoop_login_button') );
        add_action( 'register_form', array($this, 'add_swoop_signup_button'));
        $this->remove_login_form();
      }

      add_action('wp_logout',array($this,'swoop_logout'));
      add_filter( 'allowed_http_origins', array($this, 'add_swoop_to_origins') );

      // Add hook for admin <head></head>
      add_action( 'login_head', array($this, 'add_swoop_init_to_login_head') );
      add_action( 'login_footer', array($this, 'add_swoop_to_footer') );
      add_action( 'wp_head', array($this, 'add_swoop_init_to_wp_head') );
      add_action( 'wp_footer', array($this, 'add_swoop_to_footer') );

      new WP_Swoop_Shortcodes($this->swoop);
      // new WP_Swoop_Protect($this->swoop);
    }
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
      wp_redirect( site_url() );
    }

    $user = get_user_by('email', $meta->email);
    $redirect_to = admin_url();

    if(isset($meta->{'user_meta'}) &&
    isset($meta->{'user_meta'}->{'redirect_to'}) &&
    strlen($meta->{'user_meta'}->{'redirect_to'}) > 0) {
      $redirect_to = urldecode($meta->{'user_meta'}->{'redirect_to'});
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
          echo 'User registration is disabled. Please contact the site administrator.';
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

  function add_swoop_init_to_login_head() {
    echo '
      <script>
      let swoop = new Swoop("'.$this->swoop->clientId.'", {
        session:false,
        platform: "wordpress",
        callback: () => {
          // show loading
          document.getElementById("swoop-button").innerHTML = "<p class=\"swoop-loading\"><img src=\"'.plugin_dir_url( __DIR__ ) . 'includes/assets/images/swoop-secure-shield-20px.svg'.'\"><br />Swooping in...</p>";
        }
      });

      const handleSwoopLogin = async () => {
        let user = await swoop.init();
        if(user) {
          location.href = `'.wp_login_url().'?token=${user.id_token}`;
        }
      }
      </script>
    ';
    if(isset($_GET['token'])) {
      $this->swoop_callback(['token' => $_GET['token']]);
    }
  }
  function add_swoop_init_to_wp_head() {
    $url = preg_replace("/^http:/i", "https:", wp_login_url());
    echo '
      <script>
      // Plugin Version: 1.3.3
      let swoop = new Swoop("'.$this->swoop->clientId.'", {
        session:false,
        platform: "wordpress",
        callback: () => {
          // show loading
          document.getElementById("swoop-login-button").innerHTML = "Swooping in...";
          document.getElementById("swoop-login-button").onclick = (e) => {
            e.preventDefault();
            return false;
          }
        }
      });

      const handleSwoopLogin = async () => {
        let user = await swoop.init();
        if(user) {
          location.href = `'.$url.'?token=${user.id_token}`;
        }
      }
      </script>
    ';
  }

  public function add_swoop_login_button() {
    $redirectTo = $_GET['redirect_to'];
    $redirectQuery = '';
    if($redirectTo) {
      $redirectQuery = '{redirect_to:\'' . $redirectTo . '\'}';
    }

    $url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
    if(strpos($escaped_url,'?') !== false) {
      $escaped_url = $escaped_url . '&use-password=1';
    } else {
      $escaped_url = $escaped_url . '?use-password=1';
    }

    // echo '<a href="'.$this->swoop_login_url($redirectTo).'">' .
    echo '<span id="swoop-button"><a href="#" onclick="swoop.in('.$redirectQuery.'); return false;">' .
    '<img id=\'swoop_button\' style=\'display: block; max-width: 100%; margin: 0px auto 15px;\' src=\'' . plugin_dir_url( __DIR__ ) . 'includes/assets/images/swoop-button@2x.png' . '\' alt=\'Swoop In With Email\' >
    </a><p class="swoop-login-with-password"><a href="'.$escaped_url.'">Log in with password</a></p></span>
    <p class="swoop-login-blog-name"><a href="'.site_url().'">'.get_bloginfo( 'name' ).'</a></p>';
  }

  public function add_swoop_signup_button() {
    $redirectTo = $_GET['redirect_to'];
    $redirectQuery = '';
    if($redirectTo) {
      $redirectQuery = '{redirect_to:\'' . $redirectTo . '\'}';
    }

    echo '<a href="#" onclick="swoop.in('.$redirectQuery.'); return false;">' .
    '<img id=\'swoop_button\' style=\'display: block; max-width: 100%; margin: 0px auto 15px;\' src=\'' . plugin_dir_url( __DIR__ ) . 'includes/assets/images/swoop-button@2x.png' . '\' alt=\'Swoop In With Email\' >
    </a><p class="swoop-login-with-password"><a href="#" onclick="'.$escaped_url.'">Log in with password</a></p>
    <p class="swoop-login-blog-name"><a href="'.site_url().'">'.get_bloginfo( 'name' ).'</a></p>';
  }

  public function enqueue_swoop_js($hook) {
    wp_enqueue_style( 'swoop-login', plugin_dir_url(__FILE__) . 'assets/css/swoop-login.css' );
    wp_enqueue_script( 'swoop-login-js', 'https://cdn.jsdelivr.net/npm/@swoop-password-free/swoop@1.1.5/dist/swoop.js' );
  }

  // Remove Login Form
  public function remove_login_form() {
     add_action('login_enqueue_scripts', array($this,'enqueue_swoop_js'),10);
     add_action('wp_enqueue_scripts', array($this,'enqueue_swoop_js'),10);
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

  public function add_swoop_to_footer() {
      // Add your content here
      echo '
      <script>
        handleSwoopLogin();
      </script>
      ';
  }

}
?>
