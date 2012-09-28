<?php

add_action('wp', array('WPCrowdFund_FrontEnd_Process', 'process_contribution'));

class WPCrowdFund_FrontEnd_Process{

	public static function user_contribution(){
		if(empty($_POST))
			return false;

		if(isset($_POST['cancel-contribution']))
			self::cancel_contribution();

		if(isset($_POST['wpcf-edit-contribution']))
			return false;

		global $post;
		$post_id = $post->ID;

		$required_fields = array(	'wpcf-contribute-amount',
									'wpcf-contribute-perk',
									'wpcf-contribute-comments');
		foreach($required_fields as $required_field){
			if(!isset($_POST[$required_field]))
				return false;
		}

		if(!$_POST['wpcf-contribute-anonymous'] && (!$_POST['wpcf-contribute-name'] || !$_POST['wpcf-contribute-email'])){
			return 'name-email-missing';
		}

		if($_POST['wpcf-contribute-email'] && !is_email($_POST['wpcf-contribute-email'])){
			return 'invalid-email';
		}

		if(!is_numeric($_POST['wpcf-contribute-perk']) && $_POST['wpcf-contribute-perk'] != wpcf_no_reward_value())
			return 'invalid-perk';

		$perk = wp_get_single_post($_POST['wpcf-contribute-perk']);
		if(!$perk->post_title)
			return 'invalid-perk';

		include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contributor-fields.php'));
		if(is_array($wpcf_contributor_fields) && !empty($wpcf_contributor_fields)){
			if($_POST['wpcf-contribute-anonymous']){
				foreach($wpcf_contributor_fields as $id => $props){
					if(!$_POST['wpcf-contribute-' . $id] && $props['required_anonymous'])
						return 'missing-other-field';
				}
			}else{
				foreach($wpcf_contributor_fields as $id => $props){
					if(!$_POST['wpcf-contribute-' . $id] && $props['required'])
						return 'missing-other-field';
				}
			}
		}

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

		if(is_array($wpcf_contributor_fields) && !empty($wpcf_contributor_fields)){
			foreach($wpcf_contributor_fields as $field => $arr){
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
			case 'name-email-missing':
				$message = __('Please provide your name and email address, or choose to make an anonymous contribution', 'wp crowd fund');
				break;
			case 'invalid-email':
				$message = __('Please provide a valid email address, or choose to make an anonymous contribution', 'wp crowd fund');
				break;
			case 'missing-other-field':
				$message = __('You\'ve missed one or more required fields. Please review the contribution form.', 'wp crowd fund');
				break;
		}
		return array('type'=>$type, 'message'=>$message);
	}

	public static function paypal_message(){
		global $post;
		//perk and backer variables
		$backer = $backer_custom = $backer_title = false;
		$backer_description = $backer_email = $backer_amount = false;
		$perk = $perk_custom = $perk_cost = $perk_limit = false;
		$perk_sold = $perk_hold = $perk_title = $perk_description = false;
		if(isset($_SESSION['backer']) && is_numeric($_SESSION['backer'])){
			$backer = wp_get_single_post($_SESSION['backer']);
		}
		if($backer){
			$perk = wp_get_single_post($backer->post_parent);
			if($perk->ID == $post->ID)
				$perk = false;
			$backer_custom = get_post_custom($backer->ID);
			$backer_title = $backer->post_title;
			$backer_description = $backer->post_content;
			$backer_email = $backer_custom['email'][0];
			$backer_amount = $backer_custom['amount'][0];
		}
		if($perk){
			$perk_custom = get_post_custom($perk->ID);
			$perk_cost = $perk_custom['cost'][0];
			$perk_limit = $perk_custom['limit'][0];
			$perk_sold = $perk_custom['sold'][0];
			$perk_hold = count($perk_custom['hold']);
			$perk_sold += $perk_hold;
			$perk_title = $perk->post_title;
			$perk_description = $perk->post_content;
		}
		global $paypal_status;
		$file = null;
		switch($paypal_status){
			case 'perk-not-in-campaign':
				$file = 'wpcf-paypal-perk-not-in-campaign';
				break;
			case 'initializing-paypal':
				//do not display anything
				break;
			case 'no-paypal-token':
				$file = 'wpcf-paypal-no-paypal-token';
				break;
			case 'success':
				$file = 'wpcf-campaign-thanks-template';
				break;
			case 'could-not-complete':
				$file = 'wpcf-paypal-could-not-complete';
				break;
		}
		if($file){
			div(array('class' => $file));
			include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/' . $file . '.php'));
			div('/');			
		}
	}

	public static function confirmation_page(){
		if(is_numeric($_POST['wpcf-contribute-perk'])){
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
		}else{
			$no_perk = true;
		}
		$name = $_POST['wpcf-contribute-name'] ? $_POST['wpcf-contribute-name'] : __('Anonymous', 'wp crowd fund');
		$email = $_POST['wpcf-contribute-email'];
		$comments = $_POST['wpcf-contribute-comments'];
		$amount = WPCrowdFund_AdminValidator::settings_target($_POST['wpcf-contribute-amount']);
		div(array('class' => 'wpcf-campaign-contribute-confirmation-template'));
		include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contribute-confirmation-template.php'));
		div('/');	
		return true;
	}

	public static function process_contribution(){
		//send them to paypal
		//this is just a stub for now
		//PP_IPN::autopost_form();
		//TO DO IN HERE:
		/*
		1. define RETURNURL and CANCELURL (should be get_permalink() with a param added);
		2. Check if the cancel param was passed AND if referrer was from paypal,
			2.1 if it is canceled set a global, use that global within the context of 'the_content' to render a cancelation template
		3. Check if the return param was passed AND if the referrer was from paypal.
			3.1 

		*/
		session_start();
		if(isset($_POST['confirm-contribution']) && is_numeric($_SESSION['backer']) && $_SESSION['backer'] > 0){
			global $post;
			$base_url = get_permalink();
			$return_url = _wpcf_url_params($base_url, array('action'=>'return'));
			$cancel_url = _wpcf_url_params($base_url, array('action'=>'cancel'));

			$backer = wp_get_single_post($_SESSION['backer']);
			$backer_custom = get_post_custom($backer->ID);

			$perk = wp_get_single_post($backer->post_parent);
			$perk_custom = get_post_custom($perk->ID);

			$campaign = $post;
			$campaign_custom = get_post_custom($campaign->ID);

			//why perk->ID != campaign->ID? -- because if they choose no reward the backer's parent
			//becomes the campaign instad of the perk
			if($perk->post_parent != $campaign->ID && $perk->ID != $campaign->ID){
				global $paypal_status;
				$paypal_status = 'perk-not-in-campaign';
				return; //something went horribly wrong... ohhhh noooo (bruce)
			}

			$amount = $backer_custom['amount'][0];
			// GET A TOKEN
			$comm = new PPCommunicate();
			$response = $comm->request('SetExpressCheckout', array(
				'PAYMENTREQUEST_0_PAYMENTACTION'=>'Sale',
				'PAYMENTREQUEST_0_AMT'=>$backer_custom['amount'][0],
				'PAYMENTREQUEST_0_ITEMAMT'=>$backer_custom['amount'][0],
				'PAYMENTREQUEST_0_CURRENCYCODE'=>wpcf_checkout_currency($campaign) ,
				'L_PAYMENTREQUEST_0_NAME0'=>wpcf_checkout_title($campaign, $perk, $backer, $backer_custom) ,
				'L_PAYMENTREQUEST_0_NUMBER0'=>$backer->ID,
				'L_PAYMENTREQUEST_0_DESC0'=>wpcf_checkout_description($campaign, $perk, $backer, $backer_custom) ,
				'L_PAYMENTREQUEST_0_AMT0'=>$backer_custom['amount'][0],
				'L_PAYMENTREQUEST_0_QTY0'=>'1',
				'ALLOWNOTE'=>'0',
				'NOSHIPPING'=>'1',
				'RETURNURL'=> $return_url,
				'CANCELURL'=> $cancel_url
			));
			//IF WE GET A TOKEN, GO TO PAYPAL WITH IT
			if($response['TOKEN'] && ( strtolower($response['ACK'])=='success' || strtolower($response['ACK'])=='successwithwarning') ){
				global $paypal_status;
				$paypal_status = 'initializing-paypal';
				$_SESSION['TOKEN'] = $response['TOKEN'];
				header('Location: ' . wpcf_checkout_paypal_url($response['TOKEN']));
				exit();
			}else{
				global $paypal_status;
				$paypal_status = 'no-paypal-token';
				//sumptin phishy with pp, display a generic error (and by display I mean globalize a variable to be used within 'the_content')
			}
		// we just got a token and payer id back from paypal, so try and process it
		}elseif(isset($_GET['token']) && isset($_GET['PayerID']) && $_SESSION['TOKEN']==$_GET['token']){
			global $post;

			$backer = wp_get_single_post($_SESSION['backer']);
			$backer_custom = get_post_custom($backer->ID);

			$perk = wp_get_single_post($backer->post_parent);
			$perk_custom = get_post_custom($perk->ID);

			$campaign = $post;
			$campaign_custom = get_post_custom($campaign->ID);

			$comm = new PPCommunicate();
			$response = $comm->request('DoExpressCheckoutPayment', array(
				'TOKEN' => $_SESSION['TOKEN'],
				'PAYERID' => $_GET['PayerID'],
				'PAYMENTREQUEST_0_PAYMENTACTION'=>'Sale',
				'PAYMENTREQUEST_0_AMT'=>$backer_custom['amount'][0],
				'PAYMENTREQUEST_0_CURRENCYCODE'=>wpcf_checkout_currency($campaign) ,
				'IPADDRESS' => $_SERVER['SERVER_NAME']
			));

			if(strtolower($response['ACK'])=='success' || strtolower($response['ACK'])=='successwithwarning'){
				// we have confirmed payment! set a var to output a message
				self::complete_transaction();
				global $paypal_status;
				$paypal_status = 'success';
			}else{
				//log info from response
				//set var to output message (generic, your payment has not been processed deal, please contact blah blah blah)
				self::log_pp_error($campaign, $response, $token);
				self::cancel_contribution();
				global $paypal_status;
				$paypal_status = 'could-not-complete';
			}
		}
		return true;
	}

	public static function log_pp_error($campaign, $response, $token){
		$error = array('post_type' => 'wpcf-pp-error', 'post_title' => $token, 'post_status' => 'publish', 'post_parent' => $campaign->ID);
		$error = wp_insert_post($error);
		if($error && is_array($response) && !empty($response)){
			foreach($response as $k => $v){
				update_post_meta($error->ID, $k, $v);
			}
		}
	}

	public static function cancel_contribution(){
		//get the backer
		$backer = wp_get_single_post($_SESSION['backer']);
		//get perk
		$perk = wp_get_single_post($backer->post_parent);
		//get perk meta
		$perk_custom = get_post_custom($perk->ID);
		//remove last hold
		$hold = $perk_custom['hold'];
		if(!empty($hold)){
			sort($hold);
			$last = array_pop($hold);
			delete_post_meta($perk->ID, 'hold', $last);
		}
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
		if(!empty($hold)){
			sort($hold);
			$last = array_pop($hold);
			delete_post_meta($perk->ID, 'hold', $last);
		}

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
	}
}