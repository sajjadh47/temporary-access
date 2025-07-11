<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           Temporary_Access
 * @author            Sajjad Hossain Sagor <sagorh672@gmail.com>
 *
 * Plugin Name:       Temporary Access
 * Plugin URI:        https://wordpress.org/plugins/temporary-access/
 * Description:       Give anyone a temporary access to your site for a limited amount of time with role.
 * Version:           2.0.2
 * Requires at least: 5.6
 * Requires PHP:      8.0
 * Author:            Sajjad Hossain Sagor
 * Author URI:        https://sajjadhsagor.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       temporary-access
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'TEMPORARY_ACCESS_PLUGIN_VERSION', '2.0.2' );

/**
 * Define Plugin Folders Path
 */
define( 'TEMPORARY_ACCESS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'TEMPORARY_ACCESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'TEMPORARY_ACCESS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-temporary-access-activator.php
 *
 * @since    2.0.0
 */
function on_activate_temporary_access() {
	require_once TEMPORARY_ACCESS_PLUGIN_PATH . 'includes/class-temporary-access-activator.php';

	Temporary_Access_Activator::on_activate();
}

register_activation_hook( __FILE__, 'on_activate_temporary_access' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-temporary-access-deactivator.php
 *
 * @since    2.0.0
 */
function on_deactivate_temporary_access() {
	require_once TEMPORARY_ACCESS_PLUGIN_PATH . 'includes/class-temporary-access-deactivator.php';

	Temporary_Access_Deactivator::on_deactivate();
}

register_deactivation_hook( __FILE__, 'on_deactivate_temporary_access' );

/**
 * The core plugin class that is used to define admin-specific and public-facing hooks.
 *
 * @since    2.0.0
 */
require TEMPORARY_ACCESS_PLUGIN_PATH . 'includes/class-temporary-access.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_temporary_access() {
	$plugin = new Temporary_Access();

	$plugin->run();
}

run_temporary_access();
