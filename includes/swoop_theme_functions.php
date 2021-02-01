<?php
  function swoop_login_url($redirect_to = "") {
    global $wp_swoop;
    if($wp_swoop) {
      return $wp_swoop->swoop_login_url($redirect_to);
    } else {
      return "Swoop not defined";
    }
  }
?>
