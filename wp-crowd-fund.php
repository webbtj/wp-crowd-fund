<?php
/*
Plugin Name: WP Crowd Fund
Plugin URI: http://webb.tj/
Description: A crowd funding plugin for WordPress providing similar functionality to Kickstarter or indieGoGo. Offers both fixed and flexible funding campaigns. Fully configurable, payments processed with PayPal.
Version: 1.0
Author: TJ Webb
Author URI: http://webb.tj/
*/

//core functionality
require_once('required/constants.php');
require_once('required/posttype.php');
require_once('required/admin-fields.php');
require_once('required/admin-validator.php');
require_once('required/settings.php');
require_once('required/core.php');
require_once('required/api.php');
require_once('required/frontend.php');
require_once('required/frontend-process.php');
session_start();
//paypal integration
require_once('required/paypal/core.php');

function pre($a){
	echo '<pre>';
	print_r($a);
	echo '</pre>';
}

register_activation_hook(__FILE__, array('WPCrowdFund_Installer', 'activate'));
register_deactivation_hook(__FILE__, array('WPCrowdFund_Installer', 'deactivate'));
add_action('wpcf_cron', array('WPCrowdFund_Installer', 'cron'));

class WPCrowdFund_Installer{

	public static function activate(){
		wp_schedule_event(time(), 'hourly', 'wpcf_cron');
	}

	public static function cron(){
		global $wpdb;
		$now = strtotime('now');
		$wpdb->query( 
			$wpdb->prepare( 
				"
				DELETE FROM $wpdb->postmeta
				WHERE meta_value < %d
				AND meta_key = %s
				",
			        ($now - (2*60*60)), 'hold' 
		        )
		);
	}

	public static function deactivate(){
		wp_clear_scheduled_hook('wpcf_cron');
	}
}