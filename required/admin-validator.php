<?php

class WPCrowdFund_AdminValidator{

	public static function settings_target($value){
		$value = preg_replace('/[^a-zA-Z0-9\.]/' , '', $value);
		$value = floatval($value);
		$value = number_format($value, 2);
		return $value;
	}

	public static function settings_currency($value){
		if(!in_array($value, WPCrowdFund_Defaults::valid_currencies()))
			$value = WPCF_POST_DEFAULT_CURRENCY;
		return $value;
	}

	public static function settings_type($value){
		if(!in_array($value, WPCrowdFund_Defaults::valid_types()))
			$value = WPCF_POST_DEFAULT_TYPE;
		return $value;
	}

	public static function settings_date($value){
		return $value;
	}

	public static function givebacks($value){
		if(!array($value) || empty($value))
			return false;
		foreach($value as $order => &$giveback){
			$giveback['cost'] = preg_replace('/[^a-zA-Z0-9\.]/' , '', $giveback['cost']);
			$giveback['cost'] = floatval($giveback['cost']);
			if($giveback['cost']<1)
				$giveback['cost'] = 1;
			$giveback['cost'] = number_format($giveback['cost'], 2);
			$giveback['limit'] = preg_replace('/[^a-zA-Z0-9]/' , '', $giveback['limit']);
			$giveback['limit'] = intval($giveback['limit']);
			if($giveback['limit']<0)
				$giveback['limit']=0;
			if(!$giveback['title'])
				unset($value[$order]);
		}
		$value = array_values($value);
		return $value;
	}
}