<?php
function enqueue_swoopconnect() {
  wp_enqueue_script('swoopconnect_js', plugin_dir_url(__FILE__) . 'js/swoopconnect.js',10);
  wp_enqueue_script('swoop_admin_js', plugin_dir_url(__FILE__) . 'js/swoop_admin.js',10);
  wp_enqueue_style('bootstrap','https://getbootstrap.com/docs/4.1/dist/css/bootstrap.min.css', 1);
  wp_enqueue_style('swoopconnect_css', plugin_dir_url(__FILE__) . 'css/swoop-wordpress-admin.css',20);
  wp_enqueue_style('google_font', 'https://fonts.googleapis.com/css2?family=Lato&family=Rubik:wght@700&display=swap', 3);

}
 ?>
