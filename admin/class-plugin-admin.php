<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, other methods and
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Temporary_Access
 * @subpackage Temporary_Access/admin
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Temporary_Access_Admin
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
	 * The plugin options api wrapper object.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      array    $settings_api    Holds the plugin settings api wrapper class object.
	 */
	private $settings_api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name 	= $plugin_name;
		
		$this->version 		= $version;

		$this->options 		= get_option( 'ta_temporary_access_settings', [] );
		
		$this->settings_api = new Sajjad_Dev_Settings_API;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, TEMPORARY_ACCESS_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_name, TEMPORARY_ACCESS_PLUGIN_URL . 'admin/js/admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'TEMPORARY_ACCESS', array(
			'ajaxurl'				=> admin_url( 'admin-ajax.php' ),
			'submit_button_text'	=> __( 'Generate Temporary Access Link', 'temporary-access' ),
			'generating_text'		=> __( 'Generating Temporary Access Link...', 'temporary-access' ),
			'ajax_error_text'		=> __( 'Something Went Wrong. Please Try Again Refresing The Page.', 'temporary-access' ),
		) );
	}

	/**
	 * Adds a settings link to the plugin's action links on the plugin list table.
	 *
	 * @since    2.0.0
	 * @param    array $links The existing array of plugin action links.
	 * @return   array The updated array of plugin action links, including the settings link.
	 */
	public function add_plugin_action_links( $links )
	{
		$links[] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=temporary-access' ), __( 'Settings', 'temporary-access' ) );
		
		return $links;
	}

	/**
	 * Adds the plugin settings page to the WordPress dashboard menu.
	 *
	 * @since    2.0.0
	 */
	public function admin_menu()
	{
		add_menu_page(
			__( 'Temporary Access', 'temporary-access' ),
			__( 'Temporary Access', 'temporary-access' ),
			'manage_options',
			'temporary-access',
			array( $this, 'menu_page' ),
			'dashicons-megaphone'
		);
	}

	/**
	 * Renders the plugin settings page form.
	 *
	 * @since    2.0.0
	 */
	public function menu_page()
	{
		$this->settings_api->show_forms( __( 'Generate Temporary Access Link', 'temporary-access' ) );
	}

	/**
	 * Register Plugin Options Via Settings API
	 *
	 * @since    2.0.0
	 */
	public function admin_init()
	{
		//set the settings
		$this->settings_api->set_sections( $this->get_settings_sections() );
		
		$this->settings_api->set_fields( $this->get_settings_fields() );

		//initialize settings
		$this->settings_api->admin_init();
	}

	/**
	 * Returns the settings sections for the plugin settings page.
	 *
	 * @since 2.0.0
	 *
	 * @return array An array of settings sections, where each section is an array
	 *               with 'id' and 'title' keys.
	 */
	public function get_settings_sections()
	{
		$sections = array(
			array(
				'id'    => 'ta_temporary_access_settings',
				'title' => __( 'Temporary Access Settings', 'temporary-access' )
			)
		);
		
		return $sections;
	}

	/**
	 * Returns all the settings fields for the plugin settings page.
	 *
	 * @since 2.0.0
	 *
	 * @return array An array of settings fields, organized by section ID.  Each
	 *               section ID is a key in the array, and the value is an array
	 *               of settings fields for that section. Each settings field is
	 *               an array with 'name', 'label', 'type', 'desc', and other keys
	 *               depending on the field type.
	 */
	public function get_settings_fields()
	{
		$settings_fields = array(
			'ta_temporary_access_settings' => array(
				array(
					'name'    => 'ta_temporary_access_enable',
					'label'   => __( 'Enable Temporary Access', 'temporary-access' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Checking this box will enable creating temporary access links.', 'temporary-access' )
				),
				array(
					'name'    => 'ta_temporary_access_select_user',
					'label'   => __( 'Select User', 'temporary-access' ),
					'type'    => 'users',
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
						'seconds' 	=> __( 'Seconds', 'temporary-access' ),
						'minutes' 	=> __( 'Minutes', 'temporary-access' ),
						'hours' 	=> __( 'Hours', 'temporary-access' ),
						'days' 		=> __( 'Days', 'temporary-access' ),
					),
				),
				array(
					'name'    => 'ta_temporary_access_temporary_link',
					'label'   => __( 'Temporary Link', 'temporary-access' ),
					'type'    => 'html',
					'desc'    => '<div class="temporary-link">'. $this->generate_temporary_access_link() .'</div>',
				),
			)
		);

		return $settings_fields;
	}

	/**
	 * Generate a temporary access link usable for only once
	 *
	 * @since    2.0.0
	 */
	public function generate_temporary_access_link()
	{
		if ( ! isset( $this->options['ta_temporary_access_enable'] ) || $this->options['ta_temporary_access_enable'] != 'on' )
		{
			return;
		}
		
		$allowedTimeLimitValType 	= [ 'seconds', 'minutes', 'hours', 'days' ];
		
		$timeLimitValType   		= isset( $this->options['ta_temporary_access_time_limit_type'] ) && in_array( $this->options['ta_temporary_access_time_limit_type'], $allowedTimeLimitValType ) ? sanitize_text_field( $this->options['ta_temporary_access_time_limit_type'] ) : false;

		$timeLimitVal 				= isset( $this->options['temporary_access_time_limit_value'] ) ? intval( $this->options['temporary_access_time_limit_value'] ) : false;

		$user           			= isset( $this->options['ta_temporary_access_select_user'] ) ? get_userdata( intval( $this->options['ta_temporary_access_select_user'] ) ) : false;

		if ( $timeLimitVal && $user && $timeLimitValType )
		{
			switch ( $timeLimitValType )
			{
				case 'seconds':

					$timeLimitVal  *= 1;
				
				break;

				case 'minutes':

					$timeLimitVal  *= 60;
				
				break;

				case 'hours':

					$timeLimitVal  *= 3600;
				
				break;

				case 'days':

					$timeLimitVal  *= 86400;
				
				break;
			}

			$sha1 					= sha1( $user->user_login . '-' . $timeLimitVal . '-' . current_time( 'U' ) );

			$link 					= add_query_arg( array( 
				'temporary_access_hash' => $sha1,
			), site_url() );

			update_option( 'ta_temporary_access_hash_data_' . $sha1, base64_encode( $user->user_login . '-' . $timeLimitVal ), false );

			update_option( 'ta_temporary_access_settings',
			[
				'ta_temporary_access_enable' 			=> sanitize_text_field( $this->options['ta_temporary_access_enable'] ),
				'ta_temporary_access_time_limit_type' 	=> sanitize_text_field( $this->options['ta_temporary_access_time_limit_type'] ),
				'temporary_access_time_limit_value' 	=> sanitize_text_field( $this->options['temporary_access_time_limit_value'] ),
			
			], false );
			
			return "<code>{$link}</code>";
		}
	}

	/**
	 * Add a admin node menu item showing how much time is left if user is logged in via temporary link
	 *
	 * @since    2.0.0
	 * @param    array $wp_admin_bar class WP_Admin_Bar object.
	 */
	public function admin_bar_menu( $wp_admin_bar )
	{
		// if the user is logged in and admin bar is not disabled
		if ( is_user_logged_in() && is_admin_bar_showing() )
		{
			$user_id        = get_current_user_id();

			$expiry_time    = get_user_meta( $user_id, 'temporary_access_time', true );

			if ( ! empty( $expiry_time ) && current_time( 'U' ) < $expiry_time )
			{
				$args 		= array(
					'id'    => 'ta_temporary_access',
					'title' => '<span>' . apply_filters( 'ta_temporary_access_login_time_left_text', __( 'Your Temporary Login Time Left : ', 'temporary-access' ) ) . '<span data-time="' . ( $expiry_time - current_time( 'U' ) ) * 1000 . '" id="ta_temporary_access_time_count">0d 0h 0m 0s</span></span>',
				);
				
				$wp_admin_bar->add_node( $args );
			}
		}
	}
}
