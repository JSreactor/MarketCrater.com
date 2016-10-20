<?php
/**
 * The template for displaying the footer.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This file used to call almost all other PHP scripts and libraries needed.
 * The file contains some of the primary functions to set main theme settings.
 * All bundled plugins are also called from here using TGMPA class.
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    GNU GPL, Version 3
 * @link       http://lumbermandesigns.com
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * -----------------------------------------------------------------------------
 * Theme PHP scripts and libraries includes
 */

$theme_dir = get_template_directory();
$plugins_integration_dir = $theme_dir . '/inc/plugins-integration';

require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );           // 1
require_once ( $theme_dir . '/design/functions-themedefaults.php'); // 2
require_once ( $theme_dir . '/inc/functions-extras.php' );         // 3

// Plugin integrations
require_once ( $plugins_integration_dir . '/class-tgmpa.php');           // 4
require_once ( $plugins_integration_dir . '/metaboxes.php' );           // 6
require_once ( $plugins_integration_dir . '/livecomposer.php' );       // 7

require_once ( $plugins_integration_dir . '/megamainmenu.php' );     // 9
require_once ( $plugins_integration_dir . '/masterslider.php' );    // 10
require_once ( $plugins_integration_dir . '/rankie.php' ); 		    // 10.1
require_once ( $plugins_integration_dir . '/nex-forms.php' );     // 10.2
require_once ( $plugins_integration_dir . '/ninja-forms.php' );  // 10.3

require_once ( $theme_dir . '/inc/customizer/headerpresets.class.php');      // 11
require_once ( $theme_dir . '/inc/customizer/customized-css.php');          // 12
require_once ( $theme_dir . '/inc/functions-themeinstall.php' );           // 13
require_once ( $theme_dir . '/inc/functions-ini.php' );                   // 14
require_once ( $theme_dir . '/all-icons-page.php');                      // 15
require_once ( $theme_dir . '/inc/customizer/customizer.php' );         // 16
require_once ( $theme_dir . '/inc/importer/widgets-importer.php' );    // 17
require_once ( $theme_dir . '/inc/functions-nopluginsinstalled.php' );// 18
require_once ( $theme_dir . '/inc/functions-big-updates.php' );     // 20
require_once ( $theme_dir . '/inc/beacon-helper/beacon.php' );     // 21
require_once ( $theme_dir . '/inc/themeinstallation-activate/theme-activation.php' );  // 22

/**
 *  1. Need this to have is_plugin_active()
 *  2. Import theme default settings ( make sure theme defaults are the first
 *     among other files to include !!! )
 *  3. Some extra functions that can be used by any of theme template files
 *
 *  4. TGMP class for plugin install and updates (modified http://goo.gl/frBZcL)
 *  5. ---
 *  6. Framework used to create custom meta boxes
 *  7. LiveComposer plugin integration
 *  9. Mega Main Menu plugin integration
 *  10. Master Slider plugin integration
 *  10.1. WordPress Rankie plugin integration
 *  10.2. NEX-Forms plugin integration
 *  10.3. Ninja-Forms plugin integration
 *
 *  11. Header design presets class (used in Theme Customizer)
 *  12. Custom CSS generator (based on Theme Customizer options)
 *  13. On theme activation installation functions
 *  14. Functions called on theme initialization
 *  15. Creates admin page used by modal window with all custom icons listed
 *  16. All Theme Customizer magic
 *
 *  17. Widget Importer Functions (based on Widget_Importer_Exporter plugin)
 *  18. Functions to be used when not all required plugins installed
 *  20. Custom functions that do some stuff during complex/big theme updates
 *  21. Beacon Helper and Documentation search tool by Help Scout
 */

/**
* ----------------------------------------------------------------------
* Setup some of the theme settings
*
* http://codex.wordpress.org/Plugin_API/Action_Reference
*
* Generally used to initialize theme settings/options.
* This is the first action hook available to themes,
* triggered immediately after the active theme's functions.php
* file is loaded. add_theme_support() should be called here,
* since the init action hook is too late to add some features.
* At this stage, the current user is not yet authenticated.
*/

add_action( 'after_setup_theme', 'lbmn_setup' ); // Bind theme setup callback

if ( ! function_exists( 'lbmn_setup' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which runs
	 * before the init hook. The init hook is too late for some features, such as indicating
	 * support post thumbnails.
	 */
	function lbmn_setup() {
		load_theme_textdomain( 'lbmn', get_template_directory() . '/languages' );
		// Make theme available for translation

		// add_theme_support( 'post-formats', array( 'aside', 'quote', 'link' ) );
		// Enable support for Post Formats

		add_theme_support( 'post-thumbnails' );
		// Enable support for Post Thumbnails on posts and pages

		add_theme_support( 'automatic-feed-links' );
		// Add default posts and comments RSS feed links to head

		// This theme uses wp_nav_menu()
		// Here we define menu locations available
		register_nav_menus( array(
			'topbar'	=> __( 'Top Bar', 'lbmn' ),
			'header-menu'	=> __( 'Header', 'lbmn' ),
			// Please note: Mobile off-canvas menu is widget area no menu locaiton
		) );
	}

endif; // lbmn_setup


/**
 * ------------------------------------------------------------------------------
 * Register the required plugins for this theme.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */

define( 'THEME_LC_VER', '1.2.4.1.0.6.1' );
// Latest compatible Live Composer version
// It overrides the LC dashboard updates feature to make sure LC do not
// update itself until we test and permit it


// Delete the redirect transient to not allow Ninja Forms to redirect
// theme users to their welcome page ont he first plugin install
delete_transient( '_nf_activation_redirect' );


add_action( 'tgmpa_register', 'lbmn_register_required_plugins' );
function lbmn_register_required_plugins() {
	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	// there is a bug in tgmpa that prevents me from using 'default_path' in config
	$plugins_paths = LBMN_INSTALLER . LBMN_PLUGINS;
	//get_template_directory_uri() . '/inc/plugins-integration/plugin-installables/';
	$plugins = array(

		// Include amazing 'Live Composer' plugin pre-packaged with a theme
		// http://codecanyon.net/item/live-composer-frontend-wordpress-page-builder/6594028?ref=lumbermandesigns
		array(
			'name'     				=> 'Live Composer',
			'slug'     				=> 'ds-live-composer',
			'source'   				=> $plugins_paths . 'live-composer-page-builder.1.2.4.1.0.6.1.zip',
			'required' 				=> true,
			'version' 				=> '1.2.4.1.0.6.1', // make sure to update THEME_LC_VER constant too
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

		// Include 'Mega Main Menu' plugin pre-packaged with a theme
		// http://codecanyon.net/item/mega-main-menu-wordpress-menu-plugin/6135125?ref=lumbermandesigns
		array(
			'name'     				=> 'Mega Main Menu',
			'slug'     				=> 'mega_main_menu',
			'source'   				=> $plugins_paths . 'mega-main-menu.20.1.2.zip',
			'required' 				=> true,
			'version' 				=> '20.1.2',
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

		// Include 'Meta Box' plugin pre-packaged with a theme
		// http://wordpress.org/plugins/meta-box/
		array(
			'name'     				=> 'Meta Box',
			'slug'     				=> 'meta-box',
			'required' 				=> true,
			'version' 				=> '',
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

		// Include 'MasterSlider' plugin pre-packaged with a theme
		// http://codecanyon.net/item/master-slider-wordpress-responsive-touch-slider/7467925?ref=lumbermandesigns
		array(
			'name'     				=> 'MasterSlider',
			'slug'     				=> 'masterslider',
			'source'   				=> $plugins_paths . 'masterslider.20.29.0.zip',
			'required' 				=> false,
			'version' 				=> '20.29.0',
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

		// Include 'Easy Social Share Buttons for WordPress' plugin pre-packaged with a theme
		// http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=lumbermandesigns
		array(
			'name'     				=> 'Easy Social Share Buttons for WordPress',
			'slug'     				=> 'easy-social-share-buttons3',
			'source'   				=> $plugins_paths . 'easy-social-share-buttons3.30.5.zip',
			'required' 				=> false,
			'version' 				=> '30.5',
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

		// Include 'Ninja Forms' plugin pre-packaged with a theme
		// https://wordpress.org/plugins/ninja-forms/
		array(
			'name'     				=> 'Ninja Forms',
			'slug'     				=> 'ninja-forms',
			'required' 				=> false,
			'version' 				=> '',
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

		// Include 'Ninja Forms - Layout Master' premium plugin pre-packaged with our theme
		// http://codecanyon.net/item/ninja-forms-layout-master/9372347/?ref=lumbermandesigns
		array(
			'name'     				=> 'Ninja Forms - Layout Master',
			'slug'     				=> 'ninja-forms-layout-master',
			'source'   				=> $plugins_paths . 'ninja-forms-layout-master.10.7.2.zip',
			'required' 				=> false,
			'version' 				=> '10.7.2',
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

		// Include 'Ninja Forms Newsletter Opt-ins' premium plugin pre-packaged with our theme
		// http://codecanyon.net/item/ninja-forms-newsletter-optins/10789725/?ref=lumbermandesigns
		array(
			'name'     				=> 'Ninja Forms MailChimp Opt-ins',
			'slug'     				=> 'ninja-forms-mailchimp-optins',
			'source'   				=> $plugins_paths . 'ninja-forms-mailchimp-optins.1.2.1.zip',
			'required' 				=> false,
			'version' 				=> '1.2.1',
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

		// Include 'Ninja Forms PayPal Standard Payment Gateway' premium plugin pre-packaged with our theme
		// http://codecanyon.net/item/ninja-forms-paypal-standard-payment-gateway/10047955/?ref=lumbermandesigns
		array(
			'name'     				=> 'Ninja Forms PayPal Standard Payment Gateway',
			'slug'     				=> 'ninja-forms-paypal-standard',
			'source'   				=> $plugins_paths . 'ninja-forms-paypal-standard.1.1.2.zip',
			'required' 				=> false,
			'version' 				=> '1.1.2',
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

		// Include 'Google Maps Add-On For Live Composer' premium plugin pre-packaged with our theme
		// http://codecanyon.net/item/google-maps-addon-for-live-composer/10653749/?ref=lumbermandesigns
		array(
			'name'     				=> 'Live Composer Add-On - Google Maps',
			'slug'     				=> 'sklc-addon-googlemaps',
			'source'   				=> $plugins_paths . 'sklc-addon-googlemaps.1.1.2.zip',
			'required' 				=> false,
			'version' 				=> '1.1.2',
			'force_activation' 	=> false,
			'force_deactivation' => false,
			'external_url' 		=> '',
		),

	);

	/**
	 * Array of configuration settings.
	 */
	$config = array(
		'domain'       		=> 'lbmn',         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '', // Default absolute path to pre-packaged plugins
		// 'parent_slug' 	=> 'themes.php', 				// Default parent menu slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> true,					   	// Automatically activate plugins after installation or not
		'message' 			   => '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       	=> __( 'Install Required Plugins', 'lbmn' ),
			'menu_title'                       	=> __( 'Install Plugins', 'lbmn' ),
			'installing'                       	=> __( 'Installing Plugin: %s', 'lbmn' ), // %1$s = plugin name
			'oops'                             	=> __( 'Something went wrong with the plugin API.', 'lbmn' ),
			'notice_can_install_required'     	=> _n_noop( 'This theme requires the following plugin (the plugin is already included, you need only to install/activate it): %1$s.', 'This theme requires the following plugins (the plugins are already included, you need only to install/activate them): %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'		=> _n_noop( 'This theme recommends the following plugin (the plugin is already included, you need only to install/activate it): %1$s.', 'This theme recommends the following plugins (the plugins are already included, you need only to install/activate them): %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  				=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    	=> _n_noop( 'The following required plugin is installed but inactive: %1$s.', 'The following required plugins are installed but inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'	=> _n_noop( 'The following recommended plugin is installed but inactive: %1$s.', 'The following recommended plugins are installed but inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 				=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 					=> _n_noop(
                    'The following premium plugin is ready to be updated: <br /> %1$s.',
                    'The following premium plugins are ready to be updated: <br /> %1$s.',
                    'tgmpa'
                ),
			'notice_cannot_update' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  		=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           	=> __( 'Return to Required Plugins Installer', 'lbmn' ),
			'plugin_activated'                 	=> __( 'Plugin activated successfully.', 'lbmn' ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'lbmn' ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	tgmpa( $plugins, $config );
}

/**
 * ----------------------------------------------------------------------
 * Output theme debug information in HTML including template file name.
 * Must be 'false' if you finished website development.
 */

define('LBMN_THEME_DEBUG', false);

/**
 * ----------------------------------------------------------------------
 * Alternative to define( 'SCRIPT_DEBUG', true ) in wp-config.php
 */

define('LBMN_SCRIPT_DEBUG', false);

// true – original theme JavaScripts outputted
// false - minimized theme JavaScripts outputted

/**
* ----------------------------------------------------------------------
* Theme debuging helpers
*/

function lbmn_debug($var) {
	echo '<!-- debug start --><pre>';
	print_r($var);
	echo '</pre> <!-- debug end -->';
}

/**
 * Send debug code to the Javascript console
 */
function lbmn_debug_console($data) {
	if(is_array($data) || is_object($data)) {
		echo("<script>console.info('PHP: ".json_encode($data)."');</script>");
	} else {
		echo("<script>console.info('PHP: ".$data."');</script>");
	}
}

function lbmn_debug_log( $msg, $title = '' ) {
   $msg = print_r($msg,true);
   $log = $title."  |  ".$msg."\n";
   error_log( $log);
}

if ( !defined('LBMN_THEME_CONFUGRATED') ) {
	if( get_option( LBMN_THEME_NAME . '_basic_config_done') ) {
		define ('LBMN_THEME_CONFUGRATED', true);
	} else {
		define ('LBMN_THEME_CONFUGRATED', false);
	}
}

/**
 * ----------------------------------------------------------------------
 * WPML integration
 */

add_action( 'current_screen', 'lbmn_wpml_integration' );
function lbmn_wpml_integration( $current_screen ) {

	if ( $current_screen->id == 'wpml-string-translation/menu/string-translation' ) {

		// Register single strings form the Customizer for WPML translation in WP > WPML > Strings Translation
		// @reference https://wpml.org/wpml-hook/wpml_register_string_for_translation/
		do_action( 'wpml_register_single_string', 'Theme Customizer', 'Notification panel (before header) – Message', get_theme_mod( 'lbmn_notificationpanel_message', LBMN_NOTIFICATIONPANEL_MESSAGE_DEFAULT )  );
		do_action( 'wpml_register_single_string', 'Theme Customizer', 'Notification panel (before header) – URL', get_theme_mod( 'lbmn_notificationpanel_buttonurl', LBMN_NOTIFICATIONPANEL_BUTTONURL_DEFAULT )  );

		do_action( 'wpml_register_single_string', 'Theme Customizer', 'Call to action (before footer) – Message', get_theme_mod( 'lbmn_calltoaction_message', LBMN_CALLTOACTION_MESSAGE_DEFAULT ) );
		do_action( 'wpml_register_single_string', 'Theme Customizer', 'Call to action (before footer) – URL', get_theme_mod( 'lbmn_calltoaction_url', LBMN_CALLTOACTION_URL_DEFAULT ) );
	}
}

/**
 * ----------------------------------------------------------------------
 * WPML integration - RTL Menu
 */

add_filter( 'mmm_container_class', 'mmm_container_class_rtl', 10, 2 );
function mmm_container_class_rtl( $value='', $predefined_classes ){

	$styling_classes = '';

	if ( apply_filters( 'wpml_is_rtl', NULL ) ) {
	    $styling_classes = 'language_direction-rtl';
	} else {
	    $styling_classes = 'language_direction-ltr';
	}

	return $styling_classes;
}

/**
 * ----------------------------------------------------------------------
 * Add lang code in body class
 */

function wpml_lang_body_class( $classes ) {

	if ( defined( "ICL_LANGUAGE_CODE" ) ) {
	    $classes[] = 'current_language_' . ICL_LANGUAGE_CODE;
	    return $classes;
    } else{
    	return $classes;
    }
}
add_filter( 'body_class','wpml_lang_body_class' );

/**
 * ----------------------------------------------------------------------
 * WPML integration - Ninja Forms
 */

add_action( 'current_screen', 'lbmn_wpml_integration_ninja_forms' );
function lbmn_wpml_integration_ninja_forms( $current_screen ) {

	if ( $current_screen->id == 'wpml-string-translation/menu/string-translation' ) {

		$all_fields = ninja_forms_get_all_fields();

		$i = 0;

		foreach ($all_fields as $value) {

			//Label Inside
			if ( $all_fields[$i]['data']['label_pos'] == 'inside' and $all_fields[$i]['data']['req'] == 1 ) {
				$settings = apply_filters( "ninja_forms_settings", get_option( "ninja_forms_settings" ) );
				do_action( 'wpml_register_single_string', 'Ninja Forms Plugin', 'Label Inside - ' . $all_fields[$i]['data']['label'] . ' ' . strip_tags($settings['req_field_symbol']), $all_fields[$i]['data']['label'] . ' ' . strip_tags($settings['req_field_symbol']) );
			}

			//Label
	   		do_action( 'wpml_register_single_string', 'Ninja Forms Plugin', 'Label - ' . $all_fields[$i]['data']['label'], $all_fields[$i]['data']['label'] );

	   		//Default label
	   		do_action( 'wpml_register_single_string', 'Ninja Forms Plugin', 'Placeholder - ' . $all_fields[$i]['data']['placeholder'], $all_fields[$i]['data']['placeholder'] );

	   		//Description Text
	   		do_action( 'wpml_register_single_string', 'Ninja Forms Plugin', 'Description Text - ' . $all_fields[$i]['data']['desc_text'], $all_fields[$i]['data']['desc_text'] );

	   		//MailChimp
	   		do_action( 'wpml_register_single_string', 'Ninja Forms Plugin', 'MailChimp - ' . $all_fields[$i]['data']['optin_mailchimp_checkbox_text'], $all_fields[$i]['data']['optin_mailchimp_checkbox_text'] );

	   		$i++;
		}

	}
}

function lbmn_wpml_integration_ninja_forms_fields( $data, $field_id ) {

	if ( ! empty( $data ) ) {

		// Label
		if ( isset($data['label']) ) {
			$data['label'] = apply_filters( 'wpml_translate_single_string', $data['label'], 'Ninja Forms Plugin', 'Label - ' . $data['label'] );
		}

		//Default label
		if ( isset($data['default_value']) ) {
			$data['default_value'] = apply_filters( 'wpml_translate_single_string', $data['default_value'], 'Ninja Forms Plugin', 'Default label - ' . $data['default_value'] );
		}

		//Placeholder
		if ( isset($data['placeholder']) ) {
			$data['placeholder'] = apply_filters( 'wpml_translate_single_string', $data['placeholder'], 'Ninja Forms Plugin', 'Placeholder - ' . $data['placeholder'] );
		}

		//Description Text
		if ( isset($data['desc_text']) ) {
			$data['desc_text'] = apply_filters( 'wpml_translate_single_string', $data['desc_text'], 'Ninja Forms Plugin', 'Description Text - ' . $data['desc_text'] );
		}

		//MailChimp
		if ( isset($data['optin_mailchimp_checkbox_text']) ) {
			$data['optin_mailchimp_checkbox_text'] = apply_filters( 'wpml_translate_single_string', $data['optin_mailchimp_checkbox_text'], 'Ninja Forms Plugin', 'MailChimp - ' . $data['optin_mailchimp_checkbox_text'] );
		}
	}

	return $data;

}
add_filter( 'ninja_forms_field', 'lbmn_wpml_integration_ninja_forms_fields', 10, 2 );
