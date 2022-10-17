<?php

/**
 * Bail if uninstall constant is not defined
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Remove plugin options on uninstall/delete
 */

delete_option( 'ta_temporary_access_settings' );

/**
 * Remove users meta on uninstall/delete
 */

global $wpdb;

$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = 'temporary_access_time'" );

/**
 * Remove options on uninstall/delete
 */

$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%ta_temporary_access_hash_%'" );
