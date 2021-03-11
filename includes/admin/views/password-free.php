<div class="wrapper">
  <div class="d-flex flex-column flex-md-row mb-3 top-nav">
    <div class="logo-tagline">
      <img class="logo" src="<?php echo plugin_dir_url( __DIR__ ) ?>../../includes/assets/images/Swoop-logo-green.svg">
      <div class="tagline">Password-Free Authentication</div>
    </div>
    <nav class="my-2 my-md-2 mr-md-3">
      <a class="p-2 text-dark" href="https://docs.swoopnow.com/docs/features" target="_blank">Docs</a>
      <a class="p-2 text-dark" href="https://swoopnow.com/support/" target="_blank">Support</a>
      <a class="p-2 text-dark" href="https://dashboard.swoop.email/" target="_blank">Swoop Dashboard</a>
      <a class="p-2 text-dark" href="https://swoopnow.com/" target="_blank">Learn more about Swoop</a>
    </nav>
  </div>
  <main role="main" class="container">

    <div class="swoop-wordpress-admin">

      <div class="row">
        <div class="col-md-12 col-lg-8">
          <div class="iframe-holder" id="swoop">
            <div class="row col-lg-12 welcome-screen">
              <h2>Welcome to a future free of passwords.</h2>
              <!-- Start Form -->
              <form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
                    <table class="form-table" role="presentation">
                    <tr>
                      <th scope="row"><span for="swoop_client_id">Swoop Client ID</span></th>
                      <td><input name="swoop_client_id" type="text" id="swoop_client_id" value="<?php echo $options[SWOOP_CLIENT_ID_KEY]; ?>" class="regular-text" /></td>
                    </tr>

                    <tr>
                      <th scope="row"><span for="swoop_client_secret">Swoop Client Secret</span></th>
                      <td><input name="swoop_client_secret" type="text" id="swoop_client_secret" value="<?php echo $options[SWOOP_CLIENT_SECRET_KEY]; ?>" class="regular-text" /></td>
                    </tr>

                    </table>


                  <?php
                      wp_nonce_field( SWOOP_PROTECT_NONCE_KEY . '-save', SWOOP_PROTECT_NONCE_KEY );
                      submit_button();
                  ?>

              </form>
              <!-- End Form -->

            </div>
            <div class="user-copy">
              <h4>Setup Instructions</h4>
              <ol>
                <li>Sign up for Free at <a target="_blank" href="https://swoopnow.com/pricing/">swoopnow.com</a></li>
                <li>Sign in to your <a target="_blank" href="https://dashboard.swoop.email">Swoop Dashboard</a></li>
                <li>
                  Click Add Property
                  <ol type="a">
                    <li>Add your Site Name: The name displayed to your users during authorization to your site (<code><?php echo get_bloginfo('name'); ?></code>).</li>
                    <li>Add your Homepage URL: Home page of your website. (<code><?php echo get_bloginfo('url'); ?></code>)</li>
                    <li>Set the Redirect URL to be <code><?php echo site_url(  "wp-json/" . SWOOP_PLUGIN_NAMESPACE . "/" . SWOOP_PLUGIN_CALLBACK ); ?></code></li>
                    <li>Click Save.</li>
                  </ol>
                </li>

                <li>In the OAuth2 setttings, copy the Client ID and paste it in the input field above.</li>
                <li>In the OAuth2 setttings, copy the Secret and paste it in the input field above.</li>
                <li>Click <strong>Save Changes</strong> and enter a password-free paradise</li>
              </ol>
            </div>
          </div>
        </div>

        <div class="col-md-12 col-lg-4 features">

          <div class='row row-bottom-margin'>
            <div class='column icon'>
              <img class="feature-icon" src="<?php echo plugin_dir_url( __DIR__ ) ?>../../includes/assets/images/magic-link.svg">
            </div>
            <div class='column copy'>
              <span class="feature-header">Magic Link™</span>
              <p>Swoop emails a Magic Link that provides highly secure password-free authentication.</p>
            </div>
          </div>

          <div class='row row-bottom-margin'>
            <div class='column icon'>
              <img class="feature-icon" src="<?php echo plugin_dir_url( __DIR__ ) ?>../../includes/assets/images/magic-message-sparkles.svg">
            </div>
            <div class='column copy'>
              <span class="feature-header">Magic Message™</span>
              <p>Just open a mailto link and press “send” on the unique Magic Message waiting for them.</p>
            </div>
          </div>

          <div class='row row-bottom-margin'>
            <div class='column icon'>
              <img class="feature-icon" src="<?php echo plugin_dir_url( __DIR__ ) ?>../../includes/assets/images/quick-authorization.svg">
            </div>
            <div class='column copy'>
              <span class="feature-header">1-Click Connect</span>
              <p>Swoop provides simple and seamless 1-click connection after your first authentication.</p>
            </div>
          </div>
        </div>
      </div>
  </main><!-- /.container -->

</div>
