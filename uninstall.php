<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      2.0.0
 * @package    Temporary_Access
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/**
 * Remove plugin options on uninstall/delete
 */
delete_option( 'ta_temporary_access_settings' );

global $wpdb;

/**
 * Remove users meta on uninstall/delete
 */
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->usermeta} WHERE meta_key = %s",
		'temporary_access_time'
	)
);

/**
 * Remove options on uninstall/delete
 */
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		'%ta_temporary_access_hash_%'
	)
);
