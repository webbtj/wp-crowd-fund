<?php

class WPCrowdFund_FrontEnd_Process{

	public static function user_contribution(){
		if(empty($_POST))
			return false;

		if(isset($_POST['confirm-contribution']))
			return self::process_contribution();

		if(isset($_POST['cancel-contribution']))
			self::cancel_contribution();

		global $post;
		$post_id = $post->ID;

		$required_fields = array(	'wpcf-contribute-amount',
									'wpcf-contribute-perk',
									'wpcf-contribute-name',
									'wpcf-contribute-email',
									'wpcf-contribute-comments');
		foreach($required_fields as $required_field){
			if(!isset($_POST[$required_field]))
				return false;
		}

		if(!is_numeric($_POST['wpcf-contribute-perk']))
			return 'invalid-perk';

		$perk = wp_get_single_post($_POST['wpcf-contribute-perk']);
		if(!$perk->post_title)
			return 'invalid-perk';

		$perk_custom = get_post_custom($perk->ID);
		$perk_cost = $perk_custom['cost'][0];
		$perk_limit = $perk_custom['limit'][0];
		$perk_sold = $perk_custom['sold'][0];
		$perk_hold = count($perk_custom['hold']);

		if($perk_limit && $perk_sold+$perk_hold>=$perk_limit)
			return 'perk-soldout';

		$amount = WPCrowdFund_AdminValidator::settings_target($_POST['wpcf-contribute-amount']);

		if($perk_cost>$amount)
			return 'price-too-low';

		$name = $_POST['wpcf-contribute-name'] ? $_POST['wpcf-contribute-name'] : __('Anonymous', 'wp crowd fund');
		$email = $_POST['wpcf-contribute-email'];
		$comments = $_POST['wpcf-contribute-comments'];

		$backer['post_title'] = $name;
		$backer['post_content'] = $comments;
		$backer['post_type'] = 'wpcf-backer';
		$backer['post_status'] = 'draft';
		$backer['post_parent'] = $perk->ID;
		$backer = wp_insert_post($backer);
		update_post_meta($backer, 'email', $email);
		update_post_meta($backer, 'amount', $amount);

		include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contributor-fields.php'));
		if(is_array($wpcf_contributor_fields) && !empty($wpcf_contributor_fields)){
			foreach($wpcf_contributor_fields as $field => $label){
				if(isset($_POST['wpcf-contribute-' . $field]))
					update_post_meta($backer, $field, $_POST['wpcf-contribute-' . $field]);
			}
		}


		$_SESSION['backer'] = $backer;

		return $backer;
	}

	public static function error_message($type){
		$message = '';
		switch($type){
			case 'invalid-perk':
				$message = __('You have made an invalid selection in choosing a perk for your contribution.', 'wp crowd fund');
				break;
			case 'perk-soldout':
				$message = __('We\'re sorry, but the perk you have choosen is sold out.', 'wp crowd fund');
				break;
			case 'price-too-low':
				$message = __('We\'re sorry, but the perk you have choosen requires a larger contribution than what you have pledged. Please choose a different perk, or increase your contribution.', 'wp crowd fund');
				break;
		}
		return array('type'=>$type, 'message'=>$message);
	}

	public static function confirmation_page(){
		$perk = wp_get_single_post($_POST['wpcf-contribute-perk']);
		$perk_custom = get_post_custom($perk->ID);
		$perk_cost = $perk_custom['cost'][0];
		$perk_limit = $perk_custom['limit'][0];
		$perk_sold = $perk_custom['sold'][0];
		$perk_hold = count($perk_custom['hold']);
		$perk_sold += $perk_hold;

		add_post_meta($perk->ID, 'hold', strtotime('now'));
		$perk_title = $perk->post_title;
		$perk_description = $perk->post_content;
		$name = $_POST['wpcf-contribute-name'] ? $_POST['wpcf-contribute-name'] : __('Anonymous', 'wp crowd fund');
		$email = $_POST['wpcf-contribute-email'];
		$comments = $_POST['wpcf-contribute-comments'];
		$amount = WPCrowdFund_AdminValidator::settings_target($_POST['wpcf-contribute-amount']);
		include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contribute-confirmation-template.php'));
		return true;
	}

	public static function process_contribution(){
		//send them to paypal
		//this is just a stub for now
		self::complete_transaction();
		return true;
	}

	public static function cancel_contribution(){
		//get the backer
		$backer = wp_get_single_post($_SESSION['backer']);
		//delete backer meta
		//get perk
		$perk = wp_get_single_post($backer->post_parent);
		//get perk meta
		$perk_custom = get_post_custom($perk->ID);
		//remove last hold
		$hold = $perk_custom['hold'];
		sort($hold);
		$last = array_pop($hold);
		delete_post_meta($perk->ID, 'hold', $last);
		//delete backer
		wp_delete_post($_SESSION['backer'], true);
		//remove backer form session
		unset($_SESSION['backer']);
	}

	public static function complete_transaction(){
		//this would get called from paypal
		$backer['ID'] = $_SESSION['backer'];
		$backer['post_status'] = 'publish';
		$backer = wp_update_post($backer);
		$backer = wp_get_single_post($backer);
		$backer_custom = get_post_custom($backer->ID);
		$perk = wp_get_single_post($backer->post_parent);
		$perk_custom = get_post_custom($perk->ID);

		$sold = $perk_custom['sold'] ? $perk_custom['sold'][0] : 0;
		$sold++;
		update_post_meta($perk->ID, 'sold', $sold);

		$hold = $perk_custom['hold'];
		sort($hold);
		$last = array_pop($hold);
		delete_post_meta($perk->ID, 'hold', $last);

		$perk_custom = get_post_custom($perk->ID);

		global $post;
		$campaign_title = $post->post_title;
		$campaign_description = $post->post_content;
		$perk_title = $perk->post_title;
		$perk_description = $perk->post_content;
		$perk_sold = $perk_custom['sold'][0];
		$perk_hold = count($perk_custom['hold']);
		$perk_limit = $perk_custom['limit'][0];
		$perk_cost = $perk_custom['cost'][0];
		$backer_title = $backer->post_title;
		$backer_description = $backer->post_content;
		$backer_email = $backer_custom['email'][0];
		$backer_amount = $backer_custom['amount'][0];

		include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-thanks-template.php'));
	}
}