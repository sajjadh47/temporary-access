<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.0
 * @package    Temporary_Access
 * @subpackage Temporary_Access/includes
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Temporary_Access_i18n
{
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'temporary-access',
			false,
			dirname( TEMPORARY_ACCESS_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
