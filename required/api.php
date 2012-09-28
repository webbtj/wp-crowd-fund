<?php

/**
  * Campaign Tags
  * These tags provide info for a given campaign, all asume the global $post is populated with a
  * campaign (to be used within "the loop"). All except an boolean parameter (defaulted to true).
  * When this paramater is true, the tag will echo the results, passing it false will cause the
  * tag to return the results. All campaign tags return the same value they would echo except
  * wpcf_perks which will render results using a template when passed true, or return an array
  * when passed false.
  **/

// the number of backers for a campaign
function wpcf_backers($echo=true){
	$backers = 0;
	$perks = wpcf_perks(false);
	if(is_array($perks)){
		$perk_ids = array();
		foreach($perks as $perk){
			$perk_ids[] = $perk['id'];
		}
		global $post;
		$perk_ids[] = $post->ID;
		$backers = array();
		if(!empty($perk_ids)){
			foreach($perk_ids as $pid){
				$backers += get_children(array(
					'post_parent' => $pid,
					'post_type' => 'wpcf-backer',
					'post_status' => 'publish',
				));
			}
		}
		$backers = count($backers);
	}
	if($echo)
		echo $backers;
	else
		return $backers;
}

// the campaign's goal
function wpcf_goal($echo=true){
	global $post;
	$goal = get_post_meta($post->ID, 'settings_target', true);
	if($echo)
		echo $goal;
	else
		return $goal;
}

// the amount contributed to the campaign (money)
function wpcf_contributed($echo=true, $percent=false){
	$contributed = 0;

	$perks = wpcf_perks(false);
	global $post;
	if(is_array($perks)){
		$perk_ids = array();
		foreach($perks as $perk){
			$perk_ids[] = $perk['id'];
		}
		$perk_ids[] = $post->ID;
		$backers = array();
		if(!empty($perk_ids)){
			foreach($perk_ids as $pid){
				$backers += get_children(array(
					'post_parent' => $pid,
					'post_type' => 'wpcf-backer',
					'post_status' => 'publish',
				));
			}
		}
		if(!empty($backers)){
			foreach($backers as $backer){
				$backer_contributed = get_post_meta($backer->ID, 'amount', true);
				$contributed += $backer_contributed;
			}
		}
	}
	if($percent){
		$goal = (float) str_replace(',', '', wpcf_goal(false));
		if(!$contributed){
		}elseif(!$goal && $contributed){
			$contributed = 100;
		}else{
			$contributed = ceil(($contributed / $goal) * 100);
			if($contributed>100)
				$contributed=100;
		}
	}
	if($echo)
		echo $contributed;
	else
		return $contributed;
}

// days left in the campaign
function wpcf_days_left($echo=true){
	global $post;
	$date = get_post_meta($post->ID, 'settings_date', true);
	$date = strtotime($date);
	$now = strtotime('now');
	$seconds_left = $date - $now;
	$minutes_left = $seconds_left / 60;
	$hours_left = $minutes_left / 60;
	$days_left = ceil($hours_left / 24);
	if($echo)
		echo $days_left;
	else
		return $days_left;
}

// the last day of the campaign
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

// the perks for the campaign
function wpcf_perks($format=true){
	global $post, $perks;
	$perks = array();
	//if(!is_array($perks) || empty($perks)){
		$children = get_children(array(
			'post_parent' => $post->ID,
			'post_type' => 'wpcf-perk',
			'post_status' => 'publish',
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
			$perk['sold'] += count($custom['hold']);
			$perk['sold_out'] = false;
			$perk['remaining'] = __('Unlimited', 'wp crowd fund');
			if($perk['limit']){
				$perk['remaining'] = $perk['limit'] - $perk['sold'];
				if($perk['sold']>=$perk['limit'])
					$perk['sold_out'] = true;
			}
			$perks[] = $perk;
		}
		usort($perks, "costsort");		
		if(empty($perks))
			return false;
	//}
	if(!$format)
		return $perks;
	div(array('class' => 'wpcf-campaign-perks-template'));
	include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-perks-template.php'));
	div('/');
}

/**
  * Perk Functions
  * These functions are all passed a perk and either return or echo information about the perk
  **/

// the amount the perk costs
function wpcf_perk_amount($perk){
	echo "\${$perk['cost']}";
}

// the perk title
function wpcf_perk_title($perk){
	echo $perk['title'];
}

// the perk description
function wpcf_perk_description($perk){
	echo $perk['description'];
}

// the number of perks sold
function wpcf_perk_sold($perk){
	echo $perk['sold'];
}

// the number remaing (or "unlimited")
function wpcf_perk_remaining($perk){
	if($perk['limit']==0)
		echo $perk['remaining'];
	else
		echo sprintf(__('%d of %d remaining', 'wp crowd fund'), $perk['remaining'], $perk['limit']);
}

// boolean - is the perk sold out?
function wpcf_perk_soldout($perk){
	return $perk['sold_out'];
}

// order perks by cost
function costsort($a, $b){
  if ($a['cost'] == $b['cost'])
    return 0;
  return ($a < $b) ? -1 : 1;
}

/** Contribute Buttons
  * These functions are used in the templates is assigning required classes and ids to certain buttons.
  * These functions should be used as the are in the example templates, so as not to break the javascript functionality.
  **/

// the class for the buttons you click to contribute and choose a certain perk
// used in templates/wpcf-campaign-perks-template.php
function wpcf_perk_button_required_classes(){
	echo 'wpcf-perk-button ';
}

// the ids for the buttons you click to contribute and choose a certain perk
// used in templates/wpcf-campaign-perks-template.php
function wpcf_perk_button_required_id($perk){
	echo 'wpcf-perk-button-' . $perk['id'];
}

/** Form Functions
  * These functions are used in creating form elements for users to complete the contribution form.
  **/

// the form itself, check the included template file to see how fields are implemented
function wpcf_contribute_form(){
	global $post;
	$perks = wpcf_perks(false);
	div(array('class' => 'wpcf-campaign-contribute-template'));
	include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contribute-template.php'));
	div('/');
}

// the input to allow a user to choose their perk
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
		div(array('class' => 'wpcf-campaign-contribute-perks-template'));
		include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contribute-perks-template.php'));
		div('/');
	}
}

// the amount (dollar value) of the backer's contribution
function wpcf_contribute_amount_input($echo=true, $id_only=false, $class=false){
	$id = 'wpcf-contribute-amount';
	return __wpcf_contribute_text_input($id, $echo, $id_only, $class);
}

// the backer's name
function wpcf_contribute_name_input($echo=true, $id_only=false, $class=false){
	$id = 'wpcf-contribute-name';
	return __wpcf_contribute_text_input($id, $echo, $id_only, $class);
}

// the backer's email
function wpcf_contribute_email_input($echo=true, $id_only=false, $class=false){
	$id = 'wpcf-contribute-email';
	return __wpcf_contribute_text_input($id, $echo, $id_only, $class);
}

// the backer's comments
function wpcf_contribute_comments_input($echo=true, $id_only=false, $class=false){
	$id = 'wpcf-contribute-comments';
	return __wpcf_contribute_textarea_input($id, $echo, $id_only, $class);
}

function wpcf_contribute_anonymous_checkbox($echo=true, $id_only=false, $class=false){	
	$id = 'wpcf-contribute-anonymous';
	return __wpcf_contribute_checkbox_input($id, $echo, $id_only, $class);
}

// an individual radio button for a perk selection
function wpcf_contribute_perk_radio($perk, $echo=true){
	$radio = '<input ' . checked($_POST['wpcf-contribute-perk'], $perk['id'], false) . ' data-min-contribution="' . str_replace(',', '', $perk['cost']) . '" type="radio" name="wpcf-contribute-perk" value="' . $perk['id'] . '" />';
	if(!$echo)
		return $radio;
	echo $radio;
}

// value used to identify a no-reward contribution
function wpcf_no_reward_value(){
	return 'no';
}

// a button to edit a contribution from the confirmation screen
function wpcf_edit_contribution_button($text){
	echo '<form method="post">';
	if(!empty($_POST)){
		foreach($_POST as $k => $v){
			if(is_string($v)){
				echo '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
			}
		}
	}
	echo '<input type="hidden" name="wpcf-edit-contribution" value="wpcf-edit-contribution" />';
	echo '<input type="submit" value="' . $text . '" />';
	echo '</form>';
}

// returns a boolean of whether or not the current contribution form is an edit or not
function wpcf_is_edit_contribution(){
	return isset($_POST['wpcf-edit-contribution']);
}

// genereic fields used to capture additional informaion from a backer (like address)
// extra fields are defined in templates/wpcf-campaign-contributor-fields.php
function wpcf_contributor_text_field($name, $echo=true, $id_only=false){
	return _wpcf_contributor_field('text', $name, $echo, $id_only);
}

function wpcf_contributor_textarea($name, $echo=true, $id_only=false){
	return _wpcf_contributor_field('textarea', $name, $echo, $id_only);
}

function wpcf_contributor_label($name, $echo=true, $id_only=false){
	return _wpcf_contributor_field('label', $name, $echo, $id_only);
}

// the confirmation button for the confirmation page (templates/wpcf-campaign-contribute-confirmation-template.php)
function wpcf_confirmation_button($button_text){
	?>
	<form method="post">
		<input type="submit" name="confirm-contribution" class="confirm-contribution" value="<?php echo $button_text; ?>" />
	</form>
	<?php
}

// the cancel button for the confirmation page (templates/wpcf-campaign-contribute-confirmation-template.php)
function wpcf_cancel_contribution_button($button_text){
	?>
	<form method="post">
		<input type="submit" name="cancel-contribution" class="cancel-contribution" value="<?php echo $button_text; ?>" />
	</form>
	<?php
}