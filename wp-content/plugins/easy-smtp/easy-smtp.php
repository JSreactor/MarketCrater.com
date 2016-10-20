<?php
/*
Plugin Name: Easy SMTP
Plugin URI: http://webbrand-mobile.com/easy-smtp.php
Description: Configure SMTP email address to be used for aoutgoing emails on your WordPress website.
Author: James Stamford <jstamford75@gmail.com>
Version: 1.0
License: GPLv2 or later
Author URI: http://webbrand-mobile.com
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $twdsmtpOptions;

if(!class_exists('TwdSmtpClass'))
{
	class TwdSmtpClass {
		
		private $TwdSmtpAdmin;
		private $TwdSmtp;
		
		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );			
			add_action('init', array(&$this, 'start_smtp_session'));
		}
		
		public function init()
		{
			$twdsmtpOptions = get_option("twd_smtp_options");
			self::setup_plugin_constants();
			self::include_plugin_files();
			
			add_filter('plugin_action_links',array(&$this, 'twd_smtp_page_settings_link'),10,2);
			
			$this->TwdSmtp = new TwdSmtp($twdsmtpOptions);
			
			if(class_exists('TwdSmtpAdmin')) :
				$this->TwdSmtpAdmin = new TwdSmtpAdmin($twdsmtpOptions);
			endif;
			
		}
		
		function start_smtp_session()
		{
			if( !session_id() )
				session_start();
		}
		
		/**
		* Setup plugin constants
		*/
		function setup_plugin_constants() {			

			// Plugin Directory URL
			if (!defined( 'TWD_SMTP_PLUGIN_URL' )) 
				define( 'TWD_SMTP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			
			// Plugin Directory Path
			if ( ! defined( 'TWD_SMTP_PLUGIN_DIR' ) )
				define( 'TWD_SMTP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		}
		
		/**
		* Include Plugin Files
		*/
		function include_plugin_files() {
		
			require_once( TWD_SMTP_PLUGIN_DIR . '/class.smtp.php' );
			if(is_admin()){
				require_once( TWD_SMTP_PLUGIN_DIR . '/class.smtp_admin.php' );
			}

		}
		
		/**
		* Plugin Link
		**/
		function twd_smtp_page_settings_link($action_links,$plugin_file){
		
			if($plugin_file==plugin_basename(__FILE__)){
				$twdsmtp_settings_page_link = '<a href="admin.php?page=' . dirname(plugin_basename(__FILE__)) . '">' . __("Settings") . '</a>';
				array_unshift($action_links,$twdsmtp_settings_page_link);
				
			}
			return $action_links;
		}
		
		
	}
}

if(class_exists('TwdSmtpClass'))
{
	$TwdSmtpClass = new TwdSmtpClass();
}
