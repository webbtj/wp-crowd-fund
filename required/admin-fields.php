<?php

class WPCrowdFund_AdminFields{

	public static function settings_target($value=WPCF_POST_DEFAULT_TARGET){ ?>
		<label for="wp-crowd-fund-settings-target"><?php echo __('Target', 'wp crowd fund'); ?></label>
		<input type="text" name="wp-crowd-fund-settings-target" id="wp-crowd-fund-settings-target" class="widefat" value="<?php echo $value; ?>" />
	<?php }

	public static function settings_currency($value=WPCF_POST_DEFAULT_CURRENCY){ ?>
		<label for="wp-crowd-fund-settings-currency"><?php echo __('Currency', 'wp crowd fund'); ?></label>
		<select name="wp-crowd-fund-settings-currency" id="wp-crowd-fund-settings-currency" class="widefat">
			<option <?php selected($value, 'cad', true); ?> value="cad">Canadian Dollars</option>
			<option <?php selected($value, 'usd', true); ?> value="usd">US Dollars</option>
		</select>
	<?php }

	public static function settings_date($value=WPCF_POST_DEFAULT_DATE){ ?>
		<label for="wp-crowd-fund-settings-date"><?php echo __('End Date', 'wp crowd fund'); ?></label>
		<input type="text" name="wp-crowd-fund-settings-date" id="wp-crowd-fund-settings-date" class="widefat" value="<?php echo $value; ?>" />
	<?php }

	public static function settings_type($value=WPCF_POST_DEFAULT_TYPE){ ?>
		<input <?php checked($value, 'fixed', true); ?> type="radio" name="wp-crowd-fund-settings-type" id="wp-crowd-fund-settings-type-fixed" value="fixed" />
		<label for="wp-crowd-fund-settings-type-fixed"><?php echo __('Fixed Funding', 'wp crowd fund'); ?></label>
		&nbsp;
		<input <?php checked($value, 'flexible', true); ?> type="radio" name="wp-crowd-fund-settings-type" id="wp-crowd-fund-settings-type-flexible" value="flexible" />
		<label for="wp-crowd-fund-settings-type-flexible"><?php echo __('Flexible Funding', 'wp crowd fund'); ?></label>
	<?php }

	public static function giveback_title($value='', $index=0, $sold=false){ ?>
		<label for="wp-crowd-fund-giveback-title-<?php echo $index; ?>"><?php echo __('Title', 'wp crowd fund'); ?></label>
		<input <?php disabled((bool)$sold, true); ?> type="text" name="wp-crowd-fund-giveback[<?php echo $index; ?>][title]" id="wp-crowd-fund-giveback-title-<?php echo $index; ?>" value="<?php echo $value; ?>" />
	<?php }

	public static function giveback_cost($value='', $index=0, $sold=false){ ?>
		<label for="wp-crowd-fund-giveback-cost-<?php echo $index; ?>"><?php echo __('Minimum Contribution', 'wp crowd fund'); ?></label>
		<input <?php disabled((bool)$sold, true); ?> type="text" name="wp-crowd-fund-giveback[<?php echo $index; ?>][cost]" id="wp-crowd-fund-giveback-cost-<?php echo $index; ?>" value="<?php echo $value; ?>" />
	<?php }

	public static function giveback_limit($value='', $index=0, $sold=false){ ?>
		<label for="wp-crowd-fund-giveback-limit-<?php echo $index; ?>"><?php echo __('Limited Quantity', 'wp crowd fund'); ?></label>
		<input <?php disabled((bool)$sold, true); ?> type="text" name="wp-crowd-fund-giveback[<?php echo $index; ?>][limit]" id="wp-crowd-fund-giveback-limit-<?php echo $index; ?>" value="<?php echo $value; ?>" />
	<?php }

	public static function giveback_description($value='', $index=0, $sold=false){ ?>
		<label for="wp-crowd-fund-giveback-description-<?php echo $index; ?>"><?php echo __('Description', 'wp crowd fund'); ?></label>
		<textarea <?php disabled((bool)$sold, true); ?> name="wp-crowd-fund-giveback[<?php echo $index; ?>][description]" id="wp-crowd-fund-giveback-description-<?php echo $index; ?>" class="widefat"><?php echo $value; ?></textarea>
	<?php }

	public static function giveback_add(){ ?>
		<input type="button" name="wp-crowd-fund-giveback-add" id="wp-crowd-fund-giveback-add" class="button" value="<?php echo __('Add Giveback', 'wp crowd fund'); ?>" />
	<?php }
}

if(!function_exists('br')){
	function br($c=1, $echo=true){
		if(!is_numeric($c) || $c<1)
			$c=1;
		$output = '';
		for($i=0; $i<$c; $i++)
			$output .= '<br>';
		if($echo)
			echo $output;
		else
			return $output;
	}
}

if(!function_exists('div')){
	function div($args=array()){
		if($args=='/'){
			echo '</div>';
			return;
		}
		$args = array_merge(
			array(
				'id' => '',
				'class' => ''
			), $args);
		extract($args);
		echo "<div id=\"$id\" class=\"$class\">";;
	}
}

if(!function_exists('hr')){
	function hr($class=false, $echo=true){
		$output = $class ? "<hr class=\"$class\">" : '<hr>';
		if($echo)
			echo $output;
		else
			return $output;
	}
}