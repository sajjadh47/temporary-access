<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             2.0.0
 * @package           Temporary_Access
 *
 * Plugin Name:       Temporary Access
 * Plugin URI:        https://wordpress.org/plugins/temporary-access/
 * Description:       Give anyone a temporary access to your site for a limited amount of time with role.
 * Version:           2.0.0
 * Author:            Sajjad Hossain Sagor
 * Author URI:        https://sajjadhsagor.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       temporary-access
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

/**
 * Currently plugin version.
 */
define( 'TEMPORARY_ACCESS_VERSION', '2.0.0' );

/**
 * Define Plugin Folders Path
 */
define( 'TEMPORARY_ACCESS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'TEMPORARY_ACCESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'TEMPORARY_ACCESS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-activator.php
 * 
 * @since    2.0.0
 */
function activate_temporary_access()
{
	require_once TEMPORARY_ACCESS_PLUGIN_PATH . 'includes/class-plugin-activator.php';
	
	Temporary_Access_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_temporary_access' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-deactivator.php
 * 
 * @since    2.0.0
 */
function deactivate_temporary_access()
{
	require_once TEMPORARY_ACCESS_PLUGIN_PATH . 'includes/class-plugin-deactivator.php';
	
	Temporary_Access_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_temporary_access' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 * 
 * @since    2.0.0
 */
require TEMPORARY_ACCESS_PLUGIN_PATH . 'includes/class-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_temporary_access()
{
	$plugin = new Temporary_Access();
	
	$plugin->run();
}

run_temporary_access();
