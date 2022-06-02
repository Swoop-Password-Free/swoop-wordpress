<!-- <div class="swoop-button"
data-redirect_to="<?php echo isset($_GET['redirect_to']) ? $_GET['redirect_to'] : get_admin_url(); ?>">
</div> -->

<div class="click-button">
    <a href="#" onclick="swoop.in({'redirect_to': '<?php echo isset($_GET['redirect_to']) ? $_GET['redirect_to'] : get_admin_url(); ?>'})">
        <img src="<?php echo plugin_dir_url( __DIR__ ) . 'assets/images/swoop-button-with-icon@2x.png'; ?>" alt="Sign In with 1-Click" />
    </a>
</div>
<div class="tagline">Sign In with 1-Click is more secure</div>
