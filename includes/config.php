<?php
define("SWOOP_URL","https://staging.auth.swoop.email");
define("SWOOP_AUTH_ENDPOINT","/oauth2/authorize");
define("SWOOP_TOKEN_ENDPOINT","/oauth2/token");

define("SWOOP_PLUGIN_NAMESPACE",'swoop/v1');
define("SWOOP_PLUGIN_CALLBACK",'callback');
define("SWOOP_PLUGIN_SLUG",'swoop');

// Options
define("SWOOP_OPTIONS_MENU_NAME",'1-Click Login');
define("SWOOP_OPTIONS_MENU_TITLE",'Password-Free Login');
define("SWOOP_OPTIONS_KEY", "swoop_options");
define("SWOOP_OPTIONS_GROUP",'swoop_options_group');

// Keys
define("SWOOP_CLIENT_ID_KEY", "swoop_client_id");
define("SWOOP_CLIENT_SECRET_KEY", "swoop_client_secret");
define("SWOOP_LOGIN_BUTTON_TEXT_COLOR_KEY", "swoop_login_button_text_color");
define("SWOOP_LOGIN_BUTTON_BACKGROUND_COLOR_KEY", "swoop_login_button_background_color");
define("SWOOP_HIDE_LOGIN_WITH_PASSWORD_KEY", "swoop_hide_login_with_password");

define("SWOOP_ORGANIZATION_NAME_KEY", "swoop_organization_name");
define("SWOOP_ORGANIZATION_ID_KEY", "swoop_organization_id");
define("SWOOP_PROPERTY_NAME_KEY", "swoop_property_name");
define("SWOOP_PROPERTY_ID_KEY", "swoop_property_id");
define("SWOOP_WP_ADMIN_KEY", "swoop_wp_admin");
define("SWOOP_WP_ADMIN_EMAIL_KEY", "swoop_wp_admin_email");
define("SWOOP_CONNECTED_DATE_KEY", "swoop_wp_connected_date");

// Protect
define("SWOOP_PROTECT_PLUGIN_SLUG",'swoop-protect');
define("SWOOP_PROTECT_MENU_NAME",'Page Protection');
define("SWOOP_PROTECT_MENU_TITLE",'Swoop: Password-Free Page Protection');
define("SWOOP_PROTECT_POST_META_KEY", 'swoop_protected');
define("SWOOP_PROTECT_REDIRECT_PAGE_ID_KEY", 'swoop_protect_redirect_page_id');
define("SWOOP_PROTECT_NONCE_KEY", 'swoop_protect_nonce');
?>
