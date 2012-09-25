<?php

define('WPCF_POST_DEFAULT_TARGET', '');
define('WPCF_POST_DEFAULT_CURRENCY', 'cad');
define('WPCF_POST_DEFAULT_DATE', '');
define('WPCF_POST_DEFAULT_TYPE', 'fixed');

class WPCrowdFund_Defaults{

	public static function valid_currencies(){
		return array(
			'cad',
			'usd');
	}

	public static function valid_types(){
		return array(
			'fixed',
			'flexible');
	}
}