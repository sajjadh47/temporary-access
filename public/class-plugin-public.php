<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, other methods and
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Temporary_Access
 * @subpackage Temporary_Access/public
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Temporary_Access_Public
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin options.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      array    $options    Holds saved/default value of plugin options.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param    string    $plugin_name   The name of the plugin.
	 * @param    string    $version   The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name 	= $plugin_name;
		
		$this->version 		= $version;

		$this->options 		= get_option( 'ta_temporary_access_settings', [] );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles()
	{
		//wp_enqueue_style( $this->plugin_name, TEMPORARY_ACCESS_PLUGIN_URL . 'public/css/public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_name, TEMPORARY_ACCESS_PLUGIN_URL . 'public/js/public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'TEMPORARY_ACCESS', array(
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
		) );
	}

	/**
	 * Check if url has temporary access parameter, then login the user
	 *
	 * @since    2.0.0
	 */
	public function capture_generate_link()
	{
		if ( ! isset( $this->options['ta_temporary_access_enable'] ) || $this->options['ta_temporary_access_enable'] != 'on' )
		{
			return;
		}
		
		if ( isset( $_REQUEST['temporary_access_hash'] ) )
		{
			$hashedLink 			= sanitize_text_field( $_REQUEST['temporary_access_hash'] );

			$hashDataFromDb 		= get_option( 'ta_temporary_access_hash_data_' . $hashedLink, array() );
			
			if ( ! empty( $hashDataFromDb ) )
			{
				$hashedData 		= explode( '-', base64_decode( $hashDataFromDb ) );

				if ( count( $hashedData ) == 2 )
				{
					$user           = get_user_by( 'login', sanitize_user( $hashedData[0] ) );

					$timeLimitVal   = intval( $hashedData[1] );

					// Redirect URL
					if ( ! is_wp_error( $user ) && $timeLimitVal )
					{
						if ( get_option( 'ta_temporary_access_hash_' . $hashedLink, false ) == 'accessed' )
						{
							return;
						}
						else
						{
							update_option( 'ta_temporary_access_hash_' . $hashedLink, 'accessed', false );
						}
						
						wp_destroy_current_session();
						
						wp_clear_auth_cookie();
						
						wp_set_current_user ( $user->ID );
						
						wp_set_auth_cookie  ( $user->ID );

						update_user_meta( $user->ID, 'temporary_access_time', current_time( 'U' ) + $timeLimitVal );

						wp_safe_redirect( user_admin_url() ); exit();
					}
				}
			}
		}
	}

	/**
	 * Check if the current user is logged in as temporary access and the access time is expired
	 *
	 * @since    2.0.0
	 */
	public function check_for_temporary_access_expiry()
	{
		if ( is_user_logged_in() )
		{
			$user_id        = get_current_user_id();

			$expiry_time    = get_user_meta( $user_id, 'temporary_access_time', true );

			if ( ! empty( $expiry_time ) && current_time( 'U' ) > $expiry_time )
			{
				wp_destroy_current_session();
				
				wp_clear_auth_cookie();
				
				wp_set_current_user( 0 );
				
				wp_logout();

				delete_user_meta( $user_id, 'temporary_access_time' );

				wp_safe_redirect( user_admin_url() ); exit();
			}
		}
	}
}
