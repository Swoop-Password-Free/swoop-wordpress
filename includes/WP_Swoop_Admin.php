<?php
include_once("config.php");
// include_once("admin/controllers/WP_Swoop_Admin_Page_Protect.php");
include_once("admin/controllers/WP_Swoop_Admin_Password_Free.php");

class WP_Swoop_Admin
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $passwordFreePage;
    private $protectPage;

    /**
     * Start up
     */
    public function __construct()
    {
        $this->options = get_option( SWOOP_OPTIONS_KEY );
        $this->passwordFreePage = new WP_Swoop_Admin_Password_Free($this->options);
        // $this->protectPage = new WP_Swoop_Admin_Page_Protect($this->options);
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_menu_page(
            SWOOP_OPTIONS_MENU_TITLE,
            SWOOP_OPTIONS_MENU_NAME,
            'manage_options',
            SWOOP_PLUGIN_SLUG,
            array( $this->passwordFreePage, 'create' ),
            plugin_dir_url( __DIR__ ) . '/includes/assets/images/1-click-login-envelope-white-alone.svg'
        );

        add_submenu_page(
            SWOOP_PLUGIN_SLUG, // parent
            SWOOP_OPTIONS_MENU_TITLE,
            SWOOP_OPTIONS_MENU_TITLE,
            'manage_options',
            SWOOP_PLUGIN_SLUG,
            array( $this->passwordFreePage, 'create' )
        );
    }

}

if( is_admin() )
    $wp_swoop_admin = new WP_Swoop_Admin();
?>
