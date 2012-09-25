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
require_once('required/installer.php');
require_once('required/posttype.php');
require_once('required/admin-fields.php');
require_once('required/admin-validator.php');
require_once('required/settings.php');
require_once('required/crowdFund.php');
require_once('required/api.php');
require_once('required/frontend.php');
require_once('required/frontend-process.php');
session_start();
//paypal integration
//require_once('required/paypal/...');

function pre($a){
	echo '<pre>';
	print_r($a);
	echo '</pre>';
}