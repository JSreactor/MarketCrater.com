<?php 
/*
	Plugin Name: Live Composer Add-On - Google Maps
	Plugin URI: http://www.codecanyon.com/user/SplendidCode/portfolio
	Description: Adds a google maps module to Live Composer page builder plugin.
	Author: Slobodan Kustrimovic
	Version: 1.1.2
	Author URI: http://www.codecanyon.com/user/SplendidCode
*/

define( 'SKLC_GMAPS_URL', plugin_dir_url( __FILE__ ) );
define( 'SKLC_GMAPS_ABS', dirname(__FILE__) );
define( 'SKLC_GMAPS_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );

// If LC is active
if ( defined( 'DS_LIVE_COMPOSER_URL' ) ) {

	include SKLC_GMAPS_ABS . '/inc/functions.php';
	include SKLC_GMAPS_ABS . '/inc/module.php';

}