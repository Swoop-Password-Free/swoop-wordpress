<?php
  class WP_Swoop_Shortcodes {
    private $swoop;

    public function __construct($swoop) {
      $this->swoop = $swoop;
      add_shortcode( 'swoop_register', array($this,'registrationForm') );
      add_action( 'register_post', array($this, 'handleRegistration'), 10, 3 );
    }

    public function registrationForm($atts, $content = "") {
      ob_start();
      include 'templates/swoop_register.php';
      return ob_get_clean();
    }

    public function handleRegistration($santized_user_login, $user_email, $errors) {
      $params = array("user_meta" => $_POST);
      wp_redirect($this->swoop->loginUrl($params));
      exit(0);
    }
  }
?>
