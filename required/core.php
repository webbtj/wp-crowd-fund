<?php

function wpcf_perk_error($error, $echo=true){
	if(!isset($error['message']))
		return false;
	if($echo)
		echo $error['message'];
	else
		return $error['message'];
}

function __wpcf_contribute_text_input($id, $echo=true, $id_only=false, $class=false){
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
	}else{
		$value = isset($_POST[$id]) ? $_POST[$id] : '';
		$class = $class ? ' class="' . $class . '" ' : '';
		$output = '<input value="' . $value . '" type="text" name="' . $id . '" id="' . $id . '" ' . $class . ' />';
		if($echo)
			echo $output;
		else
			return $output;
	}
}

function __wpcf_contribute_textarea_input($id, $echo=true, $id_only=false, $class=false){
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
	}else{
		$value = isset($_POST[$id]) ? $_POST[$id] : '';
		$class = $class ? ' class="' . $class . '" ' : '';
		$output = '<textarea type="text" name="' . $id . '" id="' . $id . '" ' . $class . '>' . $value . '</textarea>';
		if($echo)
			echo $output;
		else
			return $output;
	}
}

function __wpcf_contribute_checkbox_input($id, $echo=true, $id_only=false, $class=false){
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
	}else{
		$class = $class ? ' class="' . $class . '" ' : '';
		$output = '<input type="checkbox" value="1" name="' . $id . '" id="' . $id . '" ' . $class . '>';
		if($echo)
			echo $output;
		else
			return $output;
	}
}

function _wpcf_contributor_field($type, $name, $echo=true, $id_only=false){
	global $wpcf_contributor_fields;
	if(!isset($wpcf_contributor_fields)){
		include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contributor-fields.php'));
	}
	$id = array_key_exists($name, $wpcf_contributor_fields) ? 'wpcf-contribute-' . $name : '';

	$class = '';
	$class .= $wpcf_contributor_fields[$name]['required'] ? ' wpcf-required ' : ' wpcf-not-required ';
	$class .=  $wpcf_contributor_fields[$name]['required_anonymous'] ? ' wpcf-required-anonymous ' : ' wpcf-not-required-anonymous ';
	$class = $class ? ' class="' . $class . '" ' : '';
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
		return;
	}elseif($id){
		$value = $_POST[$id] ? $_POST[$id] : '';
		$html = '';
		switch($type){
			case 'text':
				$html = '<input value="' . $value . '" ' . $class . ' type="text" name="' . $id . '" id="' . $id . '" />';
				break;
			case 'textarea':
				$html = '<textarea ' . $class . ' name="' . $id . '" id="' . $id . '">' . $value . '</textarea>';
				break;
			case 'label':
				$html = '<label for="'.$id.'">' . $wpcf_contributor_fields[$name]['label'] . '</label>';
				break;
		}
		if($echo)
			echo $html;
		else
			return $html;
	}
}

function _wpcf_url_params($url='', $params=array()){
	if(is_array($params) && !empty($params)){
		$url .= strpos($url, '?') ? '&' : '?';
		$first = true;
		foreach($params as $k => $v){
			$url .= $first ? '' : '&';
			$url .= urlencode($k) . '=' . urlencode($v);
			$first = false;
		}
	}
	return $url;
}

// returns the title of the "item" being passed to paypal
function wpcf_checkout_title($campaign, $perk, $backer, $backer_custom){
	//stub for now
	if($perk->ID == $campaign->ID){
		//perk and campaign are the same because if a backer has no-reward
		//the campaign is the parent and it gets passed around as the perk as well
		$perk->post_title = __('No Reward', 'wp crowd fund');
	}
	$string = 'Pursu.it Contribution for [campaign_title]';
	$search = array('[campaign_title]', '[perk_title]', '[backer_title]', '[backer_amount]');
	$replace = array($campaign->post_title, $perk->post_title, $backer->post_title, $backer_custom['amount'][0]);
	$string = str_replace($search, $replace, $string);
	return $string;
}

// returns the description of the "item" being passed to paypal
function wpcf_checkout_description($campaign, $perk, $backer, $backer_custom){
	//stub for now
	$string = 'Supporting [campaign_title] on Pursu.it with a contribution of [backer_amount], giveback: [perk_title]';
	$search = array('[campaign_title]', '[perk_title]', '[backer_title]', '[backer_amount]');
	$replace = array($campaign->post_title, $perk->post_title, $backer->post_title, $backer_custom['amount'][0]);
	$string = str_replace($search, $replace, $string);
	return $string;
}

// returns the curreny code of the "item" being passed to paypal
function wpcf_checkout_currency($campaign){
	//stub for now
	return 'CAD';
}

// returns paypal url
function wpcf_checkout_paypal_url($token){
	//stub for now
	$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . urlencode($token);
	return $url;
}