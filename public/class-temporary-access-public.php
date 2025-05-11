<?php
/**
 * This file contains the definition of the Temporary_Access_Public class, which
 * is used to load the plugin's public-facing functionality.
 *
 * @package       Temporary_Access
 * @subpackage    Temporary_Access/public
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @since    2.0.0
 */
class Temporary_Access_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     string $plugin_name The name of the plugin.
	 * @param     string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, TEMPORARY_ACCESS_PLUGIN_URL . 'public/css/public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, TEMPORARY_ACCESS_PLUGIN_URL . 'public/js/public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'TemporaryAccess',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Check if url has temporary access parameter, then login the user
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function capture_generate_link() {
		if ( 'on' !== Temporary_Access::get_option( 'ta_temporary_access_enable', 'ta_temporary_access_settings' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['temporary_access_hash'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$hashed_link       = sanitize_text_field( wp_unslash( $_REQUEST['temporary_access_hash'] ) );
			$hash_data_from_db = get_option( 'ta_temporary_access_hash_data_' . $hashed_link, array() );

			if ( ! empty( $hash_data_from_db ) ) {
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
				$hashed_data = explode( '-', base64_decode( $hash_data_from_db ) );

				if ( count( $hashed_data ) === 2 ) {
					$user           = get_user_by( 'login', sanitize_user( $hashed_data[0] ) );
					$time_limit_val = intval( $hashed_data[1] );

					// Redirect URL.
					if ( ! is_wp_error( $user ) && $time_limit_val ) {
						if ( 'accessed' === get_option( 'ta_temporary_access_hash_' . $hashed_link, false ) ) {
							return;
						} else {
							update_option( 'ta_temporary_access_hash_' . $hashed_link, 'accessed', false );
						}

						wp_destroy_current_session();
						wp_clear_auth_cookie();
						wp_set_current_user( $user->ID );
						wp_set_auth_cookie( $user->ID );

						// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
						update_user_meta( $user->ID, 'temporary_access_time', current_time( 'U' ) + $time_limit_val );

						wp_safe_redirect( user_admin_url() );
						exit();
					}
				}
			}
		}
	}

	/**
	 * Check if the current user is logged in as temporary access and the access time is expired
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function check_for_temporary_access_expiry() {
		if ( is_user_logged_in() ) {
			$user_id     = get_current_user_id();
			$expiry_time = get_user_meta( $user_id, 'temporary_access_time', true );

			// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			if ( ! empty( $expiry_time ) && current_time( 'U' ) > $expiry_time ) {
				wp_destroy_current_session();
				wp_clear_auth_cookie();
				wp_set_current_user( 0 );
				wp_logout();

				delete_user_meta( $user_id, 'temporary_access_time' );

				wp_safe_redirect( user_admin_url() );
				exit();
			}
		}
	}
}
