<?php
  class WP_Swoop_Shortcodes {
    private $swoop;

    public function __construct($swoop) {
      $this->swoop = $swoop;
      add_shortcode( 'swoop_register', array($this,'registrationForm') );
      add_shortcode( 'swoop_login', array($this,'loginForm') );
      add_action( 'register_post', array($this, 'handleRegistration'), 10, 3 );
    }

    public function registrationForm($atts, $content = "") {
      ob_start();
      $registerUrl = wp_registration_url();
      $shortCodeAttributes = shortcode_atts( array(
    		'title' => 'Login',
        'target' => admin_url()
    	), $atts );
      $redirectTo = $shortCodeAttributes['target'];
      $logoutUrl = wp_logout_url();
      include 'templates/swoop_register.php';
      return ob_get_clean();
    }

    public function handleRegistration($santized_user_login, $user_email, $errors) {
      $params = array("user_meta" => $_POST);
      wp_redirect($this->swoop->loginUrl($params));
      exit(0);
    }

    public function loginForm($atts, $content = "") {
      $shortCodeAttributes = shortcode_atts( array(
    		'title' => 'Login',
        'target' => admin_url()
    	), $atts );

      $redirectTo = $shortCodeAttributes['target'];
      $loginUrl = $this->swoop->loginUrl();
      $logoutUrl = wp_logout_url();
      $title = $shortCodeAttributes['title'];

      ob_start();
      include 'templates/swoop_login.php';
      return ob_get_clean();
    }
  }
?>
