<div class="wrap">

    <form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="protect">
        <div id="universal-message-container">
            <h2>Page Protection</h2>

            <div class="options">
                <p>
                    <label>Where should we send non-authorized users when they attempt to access a protected page?</label>
                    <br />
                    <select name='<?php echo SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY; ?>'>
                      <?php foreach($pages as $page) { ?>
                        <option <?php echo $page->ID == $swoopProtectRedirectPageID ? 'selected' : ''; ?> value="<?php echo $page->ID; ?>">
                          <?php echo $page->post_title; ?>
                        </option>
                      <?php } ?>
                    </select>
                </p>
        </div><!-- #universal-message-container -->

        <?php
            wp_nonce_field( SWOOP_PROTECT_NONCE_KEY . '-save', SWOOP_PROTECT_NONCE_KEY );
            submit_button();
        ?>

    </form>

</div><!-- .wrap -->
