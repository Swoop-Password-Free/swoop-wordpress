
<div id="swoop-wrapper">

<img src="<?php echo plugin_dir_url( __DIR__ ) ?>../../includes/assets/images/swoop-logo-horizontal2x.png" alt="Swoop Logo" width="150px" />

<h2>Swoop Setup & Extra Functionality</h2>
<p>Detailed setup instructions are listed in our docs at <a href="https://docs.swoopnow.com/docs/wordpress" target="_blank">docs.swoopnow.com</a>. We also outline extra functionality including Login Buttons or Links and Register Buttons.

<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="swoop">
    <h2>Swoop Credentials</h2>
    <p>These credentials are generated in your Swoop Dashboard at <a href="https://dashboard.swoop.email" target="_blank">dashboard.swoop.email</a>. Follow the setup process at <a href="https://docs.swoopnow.com/docs/wordpress" target="_blank">docs.swoopnow.com</a>.</p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Swoop Client ID</th>
                <td>
                    <fieldset>
                        <!-- Text Input -->
                        <input name="swoop_client_id" type="text" id="swoop_client_id" value="<?php echo $client_id; ?>" class="regular-text" />
                        <p class="description">A public identifier for your property and application using that property.</p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Swoop Secret</th>
                <td>
                    <fieldset>
                        <!-- Text Input -->
                        <input name="swoop_client_secret" type="password" id="swoop_client_secret" value="<?php echo $client_secret; ?>" class="regular-text" />
                        <p class="description">A cryptographically secure secret, used in combination with your Client ID to verify end users.
                        </p>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit">
      <?php wp_nonce_field( SWOOP_PROTECT_NONCE_KEY . '-save', SWOOP_PROTECT_NONCE_KEY ); ?>
      <input type="submit" name="submit" id="submit" class="button button-primary"
            value="Save Settings">
    </p>
</form>
</div>
