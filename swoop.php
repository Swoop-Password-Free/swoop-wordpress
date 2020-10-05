<?php
/*
Plugin Name: Swoop: Password-Free Authentication
Description: The Swoop WordPress plugin is a simple and secure password-free authentication service. To get started, go to your <a href='./options-general.php?page=swoop'>Swoop Settings page</a> to connect the Swoop service to your property.
Version: 1.0.12
Author: Swoop
Author URI: https://swoopnow.com
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/*
This file is part of Swoop: Password-Free Authentication

Swoop: Swoop: Password-Free Authentication is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Swoop: Password-Free Authentication is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

include_once( plugin_dir_path( __FILE__ ) . '/includes/swoop_core.php' );
include_once( plugin_dir_path( __FILE__ ) . '/includes/swoop_options.php' );

$swoop = new SwoopCore(__FILE__);

/*
 * WooCommerce hooks
 */
include_once( plugin_dir_path( __FILE__ ) . '/includes/WP_Swoop_WooCommerceOverrides.php' );
