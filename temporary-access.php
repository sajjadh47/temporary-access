<?php
/**
 * Plugin Name: Temporary Access
 * Plugin URI: https://github.com/sajjadh47/temporary-access
 * Description: Give anyone a temporary access to your site for a limited amount of time with role.
 * Author: Sajjad Hossain Sagor
 * Text Domain: temporary-accesss
 * Domain Path: /languages/
 * Version: 1.0.0
 * Author URI: https://sajjadhsagor.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! defined( 'TA_TEMPORARY_ACCESS_ROOT_DIR' ) )
{
    define( 'TA_TEMPORARY_ACCESS_ROOT_DIR', dirname( __FILE__ ) ); // Plugin root dir
}

if( ! defined( 'TA_TEMPORARY_ACCESS_ROOT_URL' ) )
{
    define( 'TA_TEMPORARY_ACCESS_ROOT_URL', plugin_dir_url( __FILE__ ) ); // Plugin root url
}

if( ! defined( 'TA_TEMPORARY_ACCESS_VERSION' ) )
{
    define( 'TA_TEMPORARY_ACCESS_VERSION', '1.0.0' ); // Plugin current version
}

/**
 * Plugin Activation Hook
 */
register_activation_hook( __FILE__, 'ta_temporary_access_plugin_activated' );

if ( ! function_exists( 'ta_temporary_access_plugin_activated' ) )
{
    function ta_temporary_access_plugin_activated() {}
}

/**
 * Plugin Deactivation Hook
 */
register_deactivation_hook( __FILE__, 'ta_temporary_access_plugin_deactivated' );

if ( ! function_exists( 'ta_temporary_access_plugin_deactivated' ) )
{
    function ta_temporary_access_plugin_deactivated() {}
}

/**
 * Plugin Uninstalled / Deleted Hook
 */
register_uninstall_hook( __FILE__, 'ta_temporary_access_plugin_uninstalled' );

if ( ! function_exists( 'ta_temporary_access_plugin_uninstalled' ) )
{
    function ta_temporary_access_plugin_uninstalled() {}
}

/**
 * This gets the plugin ready for translation
 */
add_action( 'plugins_loaded', 'ta_temporary_access_load_textdomain' );

if ( ! function_exists( 'ta_temporary_access_load_textdomain' ) )
{
    function ta_temporary_access_load_textdomain()
    {
        load_plugin_textdomain( 'temporary-access', '', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
}

/**
 * Add Go To Settings Page in Plugin List Table
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ta_temporary_access_goto_settings_page_link' );

if ( ! function_exists( 'ta_temporary_access_goto_settings_page_link' ) )
{
    function ta_temporary_access_goto_settings_page_link ( $links )
    {   
        $goto_settings_link = array( '<a href="' . admin_url( 'admin.php?page=temporary-access' ) . '">'. __( 'Settings', 'temporary-access' ) .'</a>' );
        
        return array_merge( $links, $goto_settings_link );
    }
}

/**
 * Enqueue Admin Side Scripts & Stylesheets
 */
add_action( 'admin_enqueue_scripts', 'ta_temporary_access_admin_enqueue_scripts' );

if ( ! function_exists( 'ta_temporary_access_admin_enqueue_scripts' ) )
{
    function ta_temporary_access_admin_enqueue_scripts()
    {    
        // plugin admin area css
        wp_enqueue_style ( 'ta_temporary_access_admin_select2_stylesheet', TA_TEMPORARY_ACCESS_ROOT_URL . 'assets/admin/css/select2.min.css', false );

        wp_enqueue_style ( 'ta_temporary_access_admin_stylesheet', TA_TEMPORARY_ACCESS_ROOT_URL . 'assets/admin/css/admin.css', false );
        
        // plugin admin area script  
        wp_enqueue_script ( 'ta_temporary_access_admin_select2_script', TA_TEMPORARY_ACCESS_ROOT_URL . 'assets/admin/js/select2.min.js', array( 'jquery' ), '', true );
        
        wp_enqueue_script ( 'ta_temporary_access_admin_script', TA_TEMPORARY_ACCESS_ROOT_URL . 'assets/admin/js/admin.js', array( 'ta_temporary_access_admin_select2_script' ), '', true );

        wp_localize_script( 'ta_temporary_access_admin_script', 'TA_TEMPORARY_ACCESS', array(
            'submit_button_text' => __( 'Generate Temporary Access Link', 'temporary-access' ),
            'generating_text'    => __( 'Generating Temporary Access Link...', 'temporary-access' ),
            'ajax_error_text'    => __( 'Something Went Wrong. Please Try Again Refresing The Page.', 'temporary-access' ),
        ) );
    }
}

/**
 * Enqueue public Side Scripts & Stylesheets
 */
add_action( 'wp_enqueue_scripts', 'ta_temporary_access_wp_enqueue_scripts' );

if ( ! function_exists( 'ta_temporary_access_wp_enqueue_scripts' ) )
{
    function ta_temporary_access_wp_enqueue_scripts()
    {    
        // plugin public area script
        wp_enqueue_script ( 'ta_temporary_access_public_script', TA_TEMPORARY_ACCESS_ROOT_URL . 'assets/public/js/public.js', array( 'jquery' ), '', true );
    }
}

/**
 * Add Plugin Settings page to WP Admin Dashboard
 */
add_action( 'admin_menu', 'ta_temporary_access_add_plugin_settings_page' );

if ( ! function_exists( 'ta_temporary_access_add_plugin_settings_page' ) )
{
    function ta_temporary_access_add_plugin_settings_page()
    {
        add_menu_page( __( 'Temporary Access', 'temporary-access' ), __( 'Temporary Access', 'temporary-access' ), 'manage_options' , 'temporary-access', 'ta_temporary_access_plugin_settings_page_view', 'dashicons-megaphone' );
    }
}

/**
 * Plugin Settings page view
 */
if ( ! function_exists( 'ta_temporary_access_plugin_settings_page_view' ) )
{
    function ta_temporary_access_plugin_settings_page_view()
    {
        ?>

        <div class="wrap">
            <h1><?php echo __( 'Temporary Access Settings', 'temporary-access' ); ?></h1>
            <form action="options.php" method="post" id="temporary-access-form">
                <?php
                
                    settings_fields( 'ta_temporary_access_settings_group' );

                    do_settings_sections( 'ta_temporary_access_settings_section' );

                    submit_button( __( 'Generate Temporary Access Link', 'temporary-access' ) );

                    wp_nonce_field( basename( __FILE__ ), 'ta_temporary_access_settings' );
                ?>
            </form>
        </div>

    <?php }
}

/**
 * Add Plugin Settings Fields
 */
add_action( 'admin_init', 'ta_temporary_access_register_settings' );

if ( ! function_exists( 'ta_temporary_access_register_settings' ) )
{
    function ta_temporary_access_register_settings()
    {
        // add_settings_section( $id, $title, $callback, $page )
        add_settings_section(
            'ta_temporary_access_general_settings_section',
            '',
            'ta_temporary_access_settings_section_description_callback',
            'ta_temporary_access_settings_section'
        );

        // add_settings_field( $id, $title, $callback, $page, $section, $args )
        add_settings_field(
            'ta_temporary_access_enable',
            __( 'Enable Temporary Access', 'temporary-access' ),
            'ta_temporary_access_enable_callback',
            'ta_temporary_access_settings_section',
            'ta_temporary_access_general_settings_section'
        );

        // add_settings_field( $id, $title, $callback, $page, $section, $args )
        add_settings_field(
            'ta_temporary_access_select_user',
            __( 'Select User', 'temporary-access' ),
            'ta_temporary_access_select_user_callback',
            'ta_temporary_access_settings_section',
            'ta_temporary_access_general_settings_section'
        );

        // add_settings_field( $id, $title, $callback, $page, $section, $args )
        add_settings_field(
            'ta_temporary_access_user_role',
            __( 'User Role', 'temporary-access' ),
            'ta_temporary_access_user_role_callback',
            'ta_temporary_access_settings_section',
            'ta_temporary_access_general_settings_section'
        );

        // add_settings_field( $id, $title, $callback, $page, $section, $args )
        add_settings_field(
            'ta_temporary_access_time_limit',
            __( 'Access Time Limit', 'temporary-access' ),
            'ta_temporary_access_time_limit_callback',
            'ta_temporary_access_settings_section',
            'ta_temporary_access_general_settings_section'
        );

        // add_settings_field( $id, $title, $callback, $page, $section, $args )
        add_settings_field(
            'ta_temporary_access_temporary_link',
            __( 'Temporary Link', 'temporary-access' ),
            'ta_temporary_access_temporary_link_callback',
            'ta_temporary_access_settings_section',
            'ta_temporary_access_general_settings_section'
        );

        // register_setting( $option_group, $option_name, $sanitize_callback )
        register_setting( 'ta_temporary_access_settings_group', 'ta_temporary_access_settings' );
    }
}

if ( ! function_exists( 'ta_temporary_access_settings_section_description_callback' ) )
{
    function ta_temporary_access_settings_section_description_callback() {}
}

/**
 * Enable temporary access
 */
if ( ! function_exists( 'ta_temporary_access_enable_callback' ) )
{
    function ta_temporary_access_enable_callback()
    {
        $options = get_option( 'ta_temporary_access_settings' );

        $value   = isset( $options['ta_temporary_access_enable'] ) ? sanitize_text_field( $options['ta_temporary_access_enable'] ) : '';

        ?>

        <input type="checkbox" id="ta_temporary_access_enable" value="1" <?php checked( $value, 'true' ); ?>>

    <?php }
}

/**
 * Temporary access user list
 */
if ( ! function_exists( 'ta_temporary_access_select_user_callback' ) )
{
    function ta_temporary_access_select_user_callback()
    {
        $users = get_users(); ?>
        
        <select id="temporary-access-user-list" class="temporary-access-user-list">
            <option value="" data-userrole=''><?php echo __( 'Choose User', 'temporary-access' ); ?></option>
            <?php
                foreach ( $users as $user ) :

                    $roles = implode( ', ', $user->roles );
                    
                    echo "<option value='". esc_attr( $user->user_login ) ."' data-userrole='". esc_attr( $roles ) ."'>". esc_html( $user->user_login ) ."</option>";

                endforeach;
            ?>
        </select>

    <?php }
}

/**
 * Temporary access user role
 */
if ( ! function_exists( 'ta_temporary_access_user_role_callback' ) )
{
    function ta_temporary_access_user_role_callback()
    {
        $roles = ta_temporary_access_get_editable_roles();

        if ( $roles )
        {
            ?>

            <select id="temporary-access-user-role-list" class="temporary-access-user-list" multiple="multiple" disabled="disabled">
                <?php
                    foreach ( $roles as $roleId => $roleName ) :
                        
                        echo "<option value='". esc_attr( $roleId ) ."'>". esc_html( $roleName['name'] ) ."</option>";

                    endforeach;
                ?>
            </select>

            <?php
        }
    }
}

/**
 * Time limit for the temporary access
 */
if ( ! function_exists( 'ta_temporary_access_time_limit_callback' ) )
{
    function ta_temporary_access_time_limit_callback()
    {
        ?>
        
        <input type="number" id="temporary-access-time-limit-value">
        
        <select id="temporary-access-time-limit">
            <option value="seconds"><?php echo __( 'Seconds', 'temporary-access' ); ?></option>
            <option value="minutes"><?php echo __( 'Minutes', 'temporary-access' ); ?></option>
            <option value="hours"><?php echo __( 'Hours', 'temporary-access' ); ?></option>
            <option value="days"><?php echo __( 'Days', 'temporary-access' ); ?></option>
        </select>

    <?php }
}

/**
 * Show the generated temporary link
 */
if ( ! function_exists( 'ta_temporary_access_temporary_link_callback' ) )
{
    function ta_temporary_access_temporary_link_callback()
    {
        echo '<div class="temporary-link"></div>';
    }
}

/**
 * Get all available user roles
 */
if ( ! function_exists( 'ta_temporary_access_get_editable_roles' ) )
{
    function ta_temporary_access_get_editable_roles()
    {
        global $wp_roles;

        $all_roles = $wp_roles->roles;
        
        $editable_roles = apply_filters( 'editable_roles', $all_roles );

        return $editable_roles;
    }
}

/**
 * Generate a temporary access link usable for only once
 */
add_action( 'wp_ajax_ta_temporary_access_generate_link', 'ta_temporary_access_generate_link' );

if ( ! function_exists( 'ta_temporary_access_generate_link' ) )
{
    function ta_temporary_access_generate_link()
    {        
        $is_valid_nonce = ( isset( $_POST[ 'ta_temporary_access_settings' ] ) && wp_verify_nonce( $_POST[ 'ta_temporary_access_settings' ], basename( __FILE__ ) ) ) ? true : false;

        $allowedTimeLimitValType = [ 'seconds', 'minutes', 'hours', 'days' ];
        
        $timeLimitVal   = isset( $_POST['timeLimitVal'] ) && isset( $_POST['timeLimitValType'] ) && in_array( $_POST['timeLimitValType'], $allowedTimeLimitValType ) ? intval( $_POST['timeLimitVal'] ) : false;

        $user           = isset( $_POST['user'] ) && username_exists( sanitize_user( $_POST['user'] ) ) ? sanitize_user( $_POST['user'] ) : false;

        if ( isset( $_POST['ta_temporary_access_enable'] ) )
        {
            $options['ta_temporary_access_enable'] = sanitize_text_field( $_POST['ta_temporary_access_enable'] );

            update_option( 'ta_temporary_access_settings', $options, false );
        }

        if ( $is_valid_nonce && $timeLimitVal && $user )
        {
            $timeLimitValType = sanitize_text_field( $_POST['timeLimitValType'] );

            switch ( $timeLimitValType )
            {
                case 'seconds':

                    $timeLimitVal *= 1;
                
                break;

                case 'minutes':

                    $timeLimitVal *= 60;
                
                break;

                case 'hours':

                    $timeLimitVal *= 3600;
                
                break;

                case 'days':

                    $timeLimitVal *= 86400;
                
                break;
            }

            $sha1 = sha1( $user . '-' . $timeLimitVal . '-' . current_time( 'U' ) );

            update_option( 'ta_temporary_access_hash_data_' . $sha1, base64_encode( $user . '-' . $timeLimitVal ), false );

            $response['link'] = add_query_arg( array( 
                'temporary_access_hash' => $sha1,
            ), site_url() );
            
            wp_send_json_success( $response );
        }
        else
        {
            wp_send_json_error();
        }
    }
}

/**
 * Check if url has temporary access parameter, then login the user
 */
add_action( 'init', 'ta_temporary_access_capture_generate_link' );

if ( ! function_exists( 'ta_temporary_access_capture_generate_link' ) )
{
    function ta_temporary_access_capture_generate_link()
    {
        $options    = get_option( 'ta_temporary_access_settings' );

        $value      = isset( $options['ta_temporary_access_enable'] ) ? sanitize_text_field( $options['ta_temporary_access_enable'] ) : '';

        if ( $value !== 'true' ) return;
        
        if ( isset( $_REQUEST['temporary_access_hash'] ) )
        {
            $hashedLink = sanitize_text_field( $_REQUEST['temporary_access_hash'] );

            $hashDataFromDb = get_option( 'ta_temporary_access_hash_data_' . $hashedLink, array() );
            
            if ( ! empty( $hashDataFromDb ) )
            {
                $hashedData = explode( '-', base64_decode( $hashDataFromDb ) );

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
}

/**
 * Check if the current user is logged in as temporary access and the access time is expired
 */
add_action( 'init', 'ta_temporary_access_check_for_temporary_access_expired' );

if ( ! function_exists( 'ta_temporary_access_check_for_temporary_access_expired' ) )
{
    function ta_temporary_access_check_for_temporary_access_expired()
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

/**
 * Add a admin node menu item showing how much time is left if user logged in via temporary link
 */
add_action( 'admin_bar_menu', 'ta_temporary_access_add_admin_node', 999 );

if ( ! function_exists( 'ta_temporary_access_add_admin_node' ) )
{
    function ta_temporary_access_add_admin_node( $wp_admin_bar )
    {
        if ( is_user_logged_in() )
        {
            // Don't display notification in admin bar if it's disabled or the current user isn't an administrator
            if( ! is_admin_bar_showing() )
            {
                return;
            }
            
            $user_id        = get_current_user_id();

            $expiry_time    = get_user_meta( $user_id, 'temporary_access_time', true );

            if ( ! empty( $expiry_time ) && current_time( 'U' ) < $expiry_time )
            {
                $args = array(
                    'id'    => 'ta_temporary_access',
                    'title' => '<span>' . apply_filters( 'ta_temporary_access_login_time_left_text', __( 'Your Temporary Login Time Left : ', 'temporary-access' ) ) . '<span data-time="' . ( $expiry_time - current_time( 'U' ) ) * 1000 . '" id="ta_temporary_access_time_count">0d 0h 0m 0s</span></span>',
                );
                
                $wp_admin_bar->add_node( $args );
            }
        }
    }
}
