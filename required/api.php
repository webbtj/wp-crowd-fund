<?php

function wpcf_backers($echo=true){
	$backers = 0;
	if($echo)
		echo $backers;
	else
		return $backers;
}

function wpcf_goal($echo=true){
	global $post;
	$goal = get_post_meta($post->ID, 'settings_target', true);
	if($echo)
		echo $goal;
	else
		return $goal;
}

function wpfc_contributed($echo=true){

}

function wpcf_days_left($echo=true){
	global $post;
	$date = get_post_meta($post->ID, 'settings_date', true);
	$date = new DateTime($date);
	$now = new DateTime('now');
	$days_left = $now->diff($date);
	$days_left = $days_left->format('%a');
	if($echo)
		echo $days_left;
	else
		return $days_left;
}

function wpcf_end_date($echo=true){
	global $post;
	$date = get_post_meta($post->ID, 'settings_date', true);
	$date = strtotime($date);
	$date = strftime('%h %e, %Y', $date);
	if($echo)
		echo $date;
	else
		return $date;
}

function wpcf_perks($format=true){
	global $post, $perks;
	if(!is_array($perks) || empty($perks)){
		$children = get_children(array(
			'post_parent' => $post->ID,
			'post_type' => 'wpcf-perk',
			'order' => 'ASC',
			'orderby' => 'meta_value_num',
			'meta_key' => 'order',
		));
		if(empty($children))
			return false;
		$perks = array();
		foreach($children as $child){
			$perk['id'] = $child->ID;
			$perk['title'] = $child->post_title;
			$perk['description'] = $child->post_content;
			$custom = get_post_custom($child->ID);
			$perk['cost'] = $custom['cost'][0];
			$perk['limit'] = $custom['limit'][0];
			$perk['sold'] = isset($custom['sold']) ? $custom['sold'][0] : 0;
			$perk['sold_out'] = false;
			$perk['remaining'] = __('Unlimited', 'wp crowd fund');
			if($perk['limit']){
				$perk['remaining'] = $perk['limit'] - $perk['sold'];
				if($perk['sold']>=$perk['limit'])
					$perk['sold_out'] = true;
			}
			$perks[] = $perk;
		}
		if(empty($perks))
			return false;
	}
	if(!$format)
		return $perks;
	include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-perks-template.php'));
}

function wpcf_perk_amount($perk){
	echo "\${$perk['cost']}";
}

function wpcf_perk_title($perk){
	echo $perk['title'];
}

function wpcf_perk_description($perk){
	echo $perk['description'];
}

function wpcf_perk_remaining($perk){
	if($perk['limit']==0)
		echo $perk['remaining'];
	else
		echo sprintf(__('%d of %d remaining', 'wp crowd fund'), $perk['remaining'], $perk['limit']);
}

function wpcf_perk_soldout($perk){
	return $perk['sold_out'];
}

function wpcf_perk_button_required_classes(){
	echo 'wpcf-perk-button ';
}

function wpcf_perk_button_required_id($perk){
	echo 'wpcf-perk-button-' . $perk['id'];
}

function wpcf_contribute_form(){
	global $post;
	$perks = wpcf_perks(false);
	include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contribute-template.php'));
}

function __wpcf_contribute_text_input($id, $echo=true, $id_only=false, $class=false){
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
	}else{
		$class = $class ? ' class="' . $class . '" ' : '';
		$output = '<input type="text" name="' . $id . '" id="' . $id . '" ' . $class . ' />';
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
		$class = $class ? ' class="' . $class . '" ' : '';
		$output = '<textarea type="text" name="' . $id . '" id="' . $id . '" ' . $class . '></textarea>';
		if($echo)
			echo $output;
		else
			return $output;
	}
}

function wpcf_contribute_amount_input($echo=true, $id_only=false, $class=false){
	$id = 'wpcf-contribute-amount';
	return __wpcf_contribute_text_input($id, $echo, $id_only, $class);
}

function wpcf_contribute_perks_input($echo=true, $id_only=false){
	$id = '';
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
	}else{
		global $post;
		$perks = wpcf_perks(false);
		include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contribute-perks-template.php'));
	}
}

function wpcf_contribute_name_input($echo=true, $id_only=false, $class=false){
	$id = 'wpcf-contribute-name';
	return __wpcf_contribute_text_input($id, $echo, $id_only, $class);
}

function wpcf_contribute_email_input($echo=true, $id_only=false, $class=false){
	$id = 'wpcf-contribute-email';
	return __wpcf_contribute_text_input($id, $echo, $id_only, $class);
}

function wpcf_contribute_comments_input($echo=true, $id_only=false, $class=false){
	$id = 'wpcf-contribute-comments';
	return __wpcf_contribute_textarea_input($id, $echo, $id_only, $class);
}

function wpcf_contribute_perk_radio($perk, $echo=true){
	$radio = '<input data-min-contribution="' . $perk['cost'] . '" type="radio" name="wpcf-contribute-perk" value="' . $perk['id'] . '" />';
	if(!$echo)
		return $radio;
	echo $radio;
}

function wpcf_perk_error($error, $echo=true){
	if(!isset($error['message']))
		return false;
	if($echo)
		echo $error['message'];
	else
		return $error['message'];
}

function wpcf_confirmation_button($button_text){
	?>
	<form method="post">
		<input type="hidden" name="confirm-contribution" value="confirm-contribution" />
		<input type="submit" value="<?php echo $button_text; ?>" />
	</form>
	<?php
}

function wpcf_contributor_field($name, $echo=true, $id_only=false, $label='', $type='text'){
	$id = 'wpcf-contribute-' . $name;
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
		return;
	}else{
		$html = '';
		switch($type){
			case 'textarea' : 
				break;
		}
	}
}