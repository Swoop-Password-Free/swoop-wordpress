
<div id="swoop-wrapper">

<img src="<?php echo plugin_dir_url( __DIR__ ) ?>../../includes/assets/images/1-Click-Login-Swoop.png" alt="1-Click Login logo" class="one-click-login-logo"/>

<h2>1-Click Login Setup & Extra Functionality</h2>
<p>Detailed setup instructions are listed in our docs at <a href="https://docs.swoopnow.com/docs/wordpress" target="_blank">docs.swoopnow.com</a>. We also outline extra functionality including Login Buttons or Links and Register Buttons.

<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="swoop">
    <h2>1-Click Login Credentials</h2>
    <p>These credentials are generated in your 1-Click Login Dashboard at <a href="https://dashboard.swoop.email" target="_blank">dashboard.swoop.email</a>. Follow the setup process at <a href="https://docs.swoopnow.com/docs/wordpress" target="_blank">docs.swoopnow.com</a>.</p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Client ID</th>
                <td>
                    <fieldset>
                        <!-- Text Input -->
                        <input name="swoop_client_id" type="text" id="swoop_client_id" value="<?php echo $client_id; ?>" class="regular-text" />
                        <p class="description">A public identifier for your property and application using that property.</p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Secret</th>
                <td>
                    <fieldset>
                        <!-- Text Input -->
                        <input name="swoop_client_secret" type="password" id="swoop_client_secret" value="<?php echo $client_secret; ?>" class="regular-text" />
                        <p class="description">A cryptographically secure secret, used in combination with your Client ID to verify end users.
                        </p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Hide <b>Log in with password</b></th>
                <td>
                    <fieldset>
                        <input type="checkbox" name="swoop_hide_login_with_password" value="true" <?php echo $hide_login_with_password ? "checked" : ""; ?> />
                        <p class="description">Hides the <b>Log in with password</b> link on the login page. It can still be accessed by adding <code>&use-password=true</code> to the URL.
                        </p>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>
    <h2>Login Button Display Options</h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <!-- Color Picker For Login Button Text Color -->
                <th scope="row">Text Color</th>
                <td>
                    <fieldset>
                        <input name="swoop_login_button_text_color" type="color" id="swoop_login_button_text_color" value="<?php echo $login_button_text_color; ?>" class="short-text" />
                        <p class="description">The color of the text on the login button.</p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <!-- Color Picker For Login Button Background Color -->
                <th scope="row">Background Color</th>
                <td>
                    <fieldset>
                        <input name="swoop_login_button_background_color" type="color" id="swoop_login_button_background_color" value="<?php echo $login_button_background_color; ?>" class="short-text" />
                        <p class="description">The background color of the login button.</p>
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
