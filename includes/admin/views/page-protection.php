<div id="swoop-wrapper">
  <img src="<?php echo plugin_dir_url( __DIR__ ) ?>../../includes/assets/images/swoop-logo-horizontal2x.png" alt="Swoop Logo" width="150px" />
    <form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="protect">

            <h2>Page Protection</h2>
            <p>This allows you to specify a page asking the user to login/register.</p>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">Page</th>
                        <td>
                            <fieldset>
                                <!-- Text Input -->
                                <select name='<?php echo SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY; ?>'>
                                  <?php foreach($pages as $page) { ?>
                                    <option <?php echo $page->ID == $swoopProtectRedirectPageID ? 'selected' : ''; ?> value="<?php echo $page->ID; ?>">
                                      <?php echo $page->post_title; ?>
                                    </option>
                                  <?php } ?>
                                </select>
                                <p class="description">Where should we send non-authorized users when they attempt to access a protected page?</p>
                            </fieldset>
                        </td>
                    </tr>
                  </body>
              </table>

        <?php
            wp_nonce_field( SWOOP_PROTECT_NONCE_KEY . '-save', SWOOP_PROTECT_NONCE_KEY );
            submit_button();
        ?>

    </form>

</div><!-- .wrap -->
