<?php
/**
 * This file contains the definition of the Temporary_Access_I18n class, which
 * is used to load the plugin's internationalization.
 *
 * @package       Temporary_Access
 * @subpackage    Temporary_Access/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since    2.0.0
 */
class Temporary_Access_I18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'temporary-access',
			false,
			dirname( TEMPORARY_ACCESS_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
