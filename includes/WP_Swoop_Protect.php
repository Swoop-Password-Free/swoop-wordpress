<?php
  include_once("config.php");

  class WP_Swoop_Protect {
    private $swoop;
    private $options;

    public function __construct($swoop) {
      $this->swoop = $swoop;
      add_action( 'add_meta_boxes', array($this, 'add_box') );
      add_action( 'save_post', array($this, 'save_post_data') );
      add_filter( 'template_redirect', array($this,'swoop_protect_content'), 100000 );
      add_action('pre_get_posts', array($this,'swoop_exclude_from_everywhere'));
      if(!is_admin()) {
        add_filter( 'get_pages', array($this,'filter_wp_list_pages'), 10, 3 );
      }

      $this->options = get_option( SWOOP_OPTIONS_KEY );
    }

    public function add_box() {
      $screens = [ 'page', 'post', 'wporg_cpt' ];
      foreach ( $screens as $screen ) {
        add_meta_box(
            'swoop_box',                 // Unique ID
            'Swoop: Password-Free',      // Box title
            array($this,'swoop_box_html'),  // Content callback, must be of type callable
            $screen                            // Post type
        );
      }
    }

    public function swoop_box_html( $post ) {
      $protected = get_post_meta( $post->ID, 'swoop_protected', false );
      $protected = is_array($protected) ? $protected[0] : false;

      ?>
      <input type="checkbox" name="swoop_protected" <?php echo $protected ? 'checked' : ''; ?> />
      <label for="swoop_protected">Only logged in users can view this content</label>
      <?php
    }

    public function save_post_data( $post_id ) {
      $protected = $_POST['swoop_protected'] == 'on';

      // Add/Remove from global protected posts array
      $protected_posts = $this->options['protected_posts'];
      if(!$protected_posts) {
        $protected_posts = array();
      }
      if($protected) {
        $included = false;
        foreach ($protected_posts as $pp) {
            if($pp == $post_id) {
              $included = true;
              break;
            }
        }
        if(!$included) {
          array_push($protected_posts,$post_id);
        }
      } else {
        if (($key = array_search($post_id, $protected_posts)) !== false) {
          unset($protected_posts[$key]);
        }
      }
      $this->options['protected_posts'] = $protected_posts;
      update_option( SWOOP_OPTIONS_KEY, $this->options);

      update_post_meta(
          $post_id,
          SWOOP_PROTECT_POST_META_KEY,
          $protected
      );
    }

    public function swoop_protect_content( ) {
      $post = get_post();
      $protectedPostURL = get_permalink($post->ID);
      $protected = get_post_meta( $post->ID, 'swoop_protected', false );
      $protected = is_array($protected) ? $protected[0] : false;

      // Check if we're inside the main loop in a single Post.
      if ( is_singular() && is_main_query() && $protected && !is_user_logged_in() ) {

        if($this->options[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY]) {
          $url = get_permalink( $this->options[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY] );
          $url = add_query_arg('redirect_to', $protectedPostURL, $url);

          wp_redirect( $url );
          die;
        }

        return wp_redirect(wp_login_url($_SERVER['REQUEST_URI']));
      }
    }

    function swoop_exclude_from_everywhere($query) {
      $protected_posts = $this->options['protected_posts'] ? $this->options['protected_posts'] : array();
      if($this->options[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY]) {
        array_push($protected_posts, $this->options[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY]);
      }

      if ( $query->is_home() || $query->is_feed() ||  $query->is_search() || $query->is_archive() ) {
          $query->set('post__not_in', $protected_posts);
      }
    }

    function filter_wp_list_pages( $pages, $arguments ) {
        // make filter magic happen here...
        if(!$this->options[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY]) {
          return $pages;
        }

        $whitelisted = array();

        foreach($pages as $page) {
          if($page->ID != $this->options[SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY]) {
            array_push($whitelisted, $page);
          }
        }

        return $whitelisted;
    }


}

  ?>
