<?php
/**
 * This file contains the definition of the Temporary_Access_Admin class, which
 * is used to load the plugin's admin-specific functionality.
 *
 * @package       Temporary_Access
 * @subpackage    Temporary_Access/admin
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @since    2.0.0
 */
class Temporary_Access_Admin {
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
	 * The plugin options api wrapper object.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       array $settings_api Holds the plugin options api wrapper class object.
	 */
	private $settings_api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     string $plugin_name The name of this plugin.
	 * @param     string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->settings_api = new Sajjad_Dev_Settings_API();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, TEMPORARY_ACCESS_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, TEMPORARY_ACCESS_PLUGIN_URL . 'admin/js/admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'TemporaryAccess',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Adds a settings link to the plugin's action links on the plugin list table.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array $links The existing array of plugin action links.
	 * @return    array $links The updated array of plugin action links, including the settings link.
	 */
	public function add_plugin_action_links( $links ) {
		$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=temporary-access' ) ), __( 'Settings', 'temporary-access' ) );

		return $links;
	}

	/**
	 * Adds the plugin settings page to the WordPress dashboard menu.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Temporary Access', 'temporary-access' ),
			__( 'Temporary Access', 'temporary-access' ),
			'manage_options',
			'temporary-access',
			array( $this, 'menu_page' ),
			'dashicons-unlock'
		);
	}

	/**
	 * Renders the plugin menu page content.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function menu_page() {
		$this->settings_api->show_forms( __( 'Generate Temporary Access Link', 'temporary-access' ) );
	}

	/**
	 * Initializes admin-specific functionality.
	 *
	 * This function is hooked to the 'admin_init' action and is used to perform
	 * various administrative tasks, such as registering settings, enqueuing scripts,
	 * or adding admin notices.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function admin_init() {
		// set the settings.
		$this->settings_api->set_sections( $this->get_settings_sections() );

		$this->settings_api->set_fields( $this->get_settings_fields() );

		// initialize settings.
		$this->settings_api->admin_init();
	}

	/**
	 * Returns the settings sections for the plugin settings page.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    array An array of settings sections, where each section is an array
	 *                  with 'id' and 'title' keys.
	 */
	public function get_settings_sections() {
		$settings_sections = array(
			array(
				'id'    => 'ta_temporary_access_settings',
				'title' => __( 'Temporary Access Settings', 'temporary-access' ),
			),
		);

		/**
		 * Filters the plugin settings sections.
		 *
		 * This filter allows you to modify the plugin settings sections.
		 * You can use this filter to add/remove/edit any settings section.
		 *
		 * @since     2.0.1
		 * @param     array $settings_sections Default settings sections.
		 * @return    array $settings_sections Modified settings sections.
		 */
		return apply_filters( 'ta_temporary_access_settings_sections', $settings_sections );
	}

	/**
	 * Returns all the settings fields for the plugin settings page.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    array An array of settings fields, organized by section ID.  Each
	 *                  section ID is a key in the array, and the value is an array
	 *                  of settings fields for that section. Each settings field is
	 *                  an array with 'name', 'label', 'type', 'desc', and other keys
	 *                  depending on the field type.
	 */
	public function get_settings_fields() {
		$settings_fields = array(
			'ta_temporary_access_settings' => array(
				array(
					'name'  => 'ta_temporary_access_enable',
					'label' => __( 'Enable Temporary Access', 'temporary-access' ),
					'type'  => 'checkbox',
					'desc'  => __( 'Checking this box will enable creating temporary access links.', 'temporary-access' ),
				),
				array(
					'name'  => 'ta_temporary_access_select_user',
					'label' => __( 'Select User', 'temporary-access' ),
					'type'  => 'users',
				),
				array(
					'name'        => 'temporary_access_time_limit_value',
					'label'       => __( 'Access Time Limit', 'temporary-access' ),
					'type'        => 'number',
					'placeholder' => '120',
				),
				array(
					'name'    => 'ta_temporary_access_time_limit_type',
					'label'   => __( 'Access Time Limit Type', 'temporary-access' ),
					'type'    => 'select',
					'options' => array(
						'seconds' => __( 'Seconds', 'temporary-access' ),
						'minutes' => __( 'Minutes', 'temporary-access' ),
						'hours'   => __( 'Hours', 'temporary-access' ),
						'days'    => __( 'Days', 'temporary-access' ),
					),
				),
				array(
					'name'  => 'ta_temporary_access_temporary_link',
					'label' => __( 'Temporary Link', 'temporary-access' ),
					'type'  => 'html',
					'desc'  => '<div class="temporary-link">' . $this->generate_temporary_access_link() . '</div>',
				),
			),
		);

		/**
		 * Filters the plugin settings fields.
		 *
		 * This filter allows you to modify the plugin settings fields.
		 * You can use this filter to add/remove/edit any settings field.
		 *
		 * @since     2.0.1
		 * @param     array $settings_fields Default settings fields.
		 * @return    array $settings_fields Modified settings fields.
		 */
		return apply_filters( 'ta_temporary_access_settings_fields', $settings_fields );
	}

	/**
	 * Generate a temporary access link usable for only once
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function generate_temporary_access_link() {
		$temporary_access = Temporary_Access::get_option( 'ta_temporary_access_enable', 'ta_temporary_access_settings' );

		if ( 'on' !== $temporary_access ) {
			return;
		}

		$posted_limit_type           = Temporary_Access::get_option( 'ta_temporary_access_time_limit_type', 'ta_temporary_access_settings' );
		$allowed_time_limit_val_type = array( 'seconds', 'minutes', 'hours', 'days' );
		$time_limit_val_type         = in_array( $posted_limit_type, $allowed_time_limit_val_type, true ) ? sanitize_text_field( $posted_limit_type ) : false;
		$time_limit_val              = intval( Temporary_Access::get_option( 'temporary_access_time_limit_value', 'ta_temporary_access_settings' ) );
		$user                        = get_userdata( intval( Temporary_Access::get_option( 'ta_temporary_access_select_user', 'ta_temporary_access_settings' ) ) );

		if ( $time_limit_val && $user && $time_limit_val_type ) {
			switch ( $time_limit_val_type ) {
				case 'seconds':
					$time_limit_val *= 1;
					break;

				case 'minutes':
					$time_limit_val *= 60;
					break;

				case 'hours':
					$time_limit_val *= 3600;
					break;

				case 'days':
					$time_limit_val *= 86400;
					break;
			}

			// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			$sha1 = sha1( $user->user_login . '-' . $time_limit_val . '-' . current_time( 'U' ) );

			$link = add_query_arg(
				array(
					'temporary_access_hash' => $sha1,
				),
				site_url()
			);

			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			update_option( 'ta_temporary_access_hash_data_' . $sha1, base64_encode( $user->user_login . '-' . $time_limit_val ), false );

			update_option(
				'ta_temporary_access_settings',
				array(
					'ta_temporary_access_enable'          => $temporary_access,
					'ta_temporary_access_time_limit_type' => $time_limit_val_type,
					'temporary_access_time_limit_value'   => $time_limit_val,
				),
				false
			);

			$copied_text = __( 'Copied!', 'temporary-access' );

			return "<code>{$link}</code><svg viewBox='0 0 16 16' version='1.1' width='16' height='16' class='copy-to-clipboard'><path d='M0 6.75C0 5.784.784 5 1.75 5h1.5a.75.75 0 0 1 0 1.5h-1.5a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-1.5a.75.75 0 0 1 1.5 0v1.5A1.75 1.75 0 0 1 9.25 16h-7.5A1.75 1.75 0 0 1 0 14.25Z'></path><path d='M5 1.75C5 .784 5.784 0 6.75 0h7.5C15.216 0 16 .784 16 1.75v7.5A1.75 1.75 0 0 1 14.25 11h-7.5A1.75 1.75 0 0 1 5 9.25Zm1.75-.25a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-7.5a.25.25 0 0 0-.25-.25Z'></path></svg><label class='code-copied'>{$copied_text}</label>";
		}
	}

	/**
	 * Add a admin node menu item showing how much time is left if user is logged in via temporary link
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array $wp_admin_bar class WP_Admin_Bar object.
	 */
	public function admin_bar_menu( $wp_admin_bar ) {
		// if the user is logged in and admin bar is not disabled.
		if ( is_user_logged_in() && is_admin_bar_showing() ) {
			$user_id     = get_current_user_id();
			$expiry_time = get_user_meta( $user_id, 'temporary_access_time', true );

			// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			if ( ! empty( $expiry_time ) && current_time( 'U' ) < $expiry_time ) {
				$args = array(
					'id'    => 'ta_temporary_access',
					// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
					'title' => '<span>' . apply_filters( 'ta_temporary_access_login_time_left_text', __( 'Your Temporary Login Time Left : ', 'temporary-access' ) ) . '<span data-time="' . ( $expiry_time - current_time( 'U' ) ) * 1000 . '" id="ta_temporary_access_time_count">0d 0h 0m 0s</span></span>',
				);

				$wp_admin_bar->add_node( $args );
			}
		}
	}
}
