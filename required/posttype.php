<?php

add_action('init', array('WPCrowdFund_Admin', 'register_post_types'));
add_action('add_meta_boxes', array('WPCrowdFund_Admin', 'add_meta_boxes'));
add_action('save_post', array('WPCrowdFund_Admin', 'save_post'), 40);
add_action('admin_enqueue_scripts', array('WPCrowdFund_Admin', 'admin_enqueue_scripts'));

class WPCrowdFund_Admin{

	public static function register_post_types(){
		register_post_type('wpcf-campaign',array(
			'label' => __('Campaigns', 'wp crowd fund'),
			'labels' => array(
				'name' => __('Campaigns', 'wp crowd fund'),
				'singular_name' => __('Campaign', 'wp crowd fund'),
				'add_new' => __('Create Campaign', 'wp crowd fund'),
				'edit_item' => __('Edit Campaign', 'wp crowd fund'),
				'add_new_item' => __('Add New Campaign', 'wp crowd fund'),
				'edit_item' => __('Edit Campaign', 'wp crowd fund'),
				'new_item' => __('New Campaign', 'wp crowd fund'),
				'view_item' => __('View Campaign', 'wp crowd fund'),
				'search_items' => __('Search Campaigns', 'wp crowd fund'),
				'not_found' => __('No Campaigns Found', 'wp crowd fund'),
			),
			'description' => __('A crowd-funding campaign.', 'wp crowd fund'),
			'public' => true,
			'_builtin' =>  false,
			'supports' => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
				'comments',
			),
			'rewrite' => array('slug' => 'campaign')
			//'menu_icon' =>  plugins_url('admin/images/project.png', __FILE__),
		));
		register_taxonomy_for_object_type('tag', 'wpcf-campaign');
		
		register_post_type('wpcf-perk',array(
			'label' => __('Give Back', 'wp crowd fund'),
			'description' => __('A give back for contributing to a crowd-funding campaign.', 'wp crowd fund'),
			'public' => false,
		));
		
		register_post_type('wpcf-backer',array(
			'label' => __('Backer', 'wp crowd fund'),
			'description' => __('A person who has contributed to a crowd-funding campaign.', 'wp crowd fund'),
			'public' => false,
		));

		register_post_type('wpcf-pp-error',array(
			'label' => __('Error', 'wp crowd fund'),
			'description' => __('An error log record of a PayPal processing error.', 'wp crowd fund'),
			'public' => false,
		));
	}

	public static function add_meta_boxes(){
		add_meta_box( 
			'campaign_givebacks',
			__('Rewards', 'wp crowd fund'),
			array('WPCrowdFund_Admin', 'mb_campaign_givebacks'),
			'wpcf-campaign',
			'advanced',
			'high'
		);
		
		add_meta_box(
			'campaign_settings',
			__('Settings', 'wp crowd fund'),
			array('WPCrowdFund_Admin', 'mb_campaign_settings'),
			'wpcf-campaign',
			'side'
		);
		
		add_meta_box(
			'campaign_funding',
			__('Funding', 'wp crowd fund'),
			array('WPCrowdFund_Admin', 'mb_campaign_funding'),
			'wpcf-campaign',
			'advanced',
			'high'
		);
	}

	public static function mb_campaign_givebacks(){
		WPCrowdFund_Installer::cron();
		wp_nonce_field(basename(__FILE__), 'WPCrowdFund_Admin_Givebacks');
		global $post;
		$saved_givebacks = get_children(array(
			'post_parent' => $post->ID,
			'post_type' => 'wpcf-perk',
			'order' => 'ASC',
			'orderby' => 'meta_value_num',
			'meta_key' => 'order',
		));
		$givebacks = array();
		$i = 0;
		if(!empty($saved_givebacks)){
			foreach($saved_givebacks as $giveback){
				$givebacks[] = array(
					'title' => $giveback->post_title,
					'description' => $giveback->post_content,
					'cost' => get_post_meta($giveback->ID, 'cost', true),
					'limit' => get_post_meta($giveback->ID, 'limit', true),
					'sold' => get_post_meta($giveback->ID, 'sold', true),
					'index' => $i);
				$i++;
			}
		}
		$givebacks[] = array(
			'title' => '',
			'description' => '',
			'cost' => '',
			'limit' => '',
			'index' => $i);
		foreach($givebacks as $giveback){
			$class = 'wp-crowd-fund-giveback-region';
			$class .= $giveback['sold'] ? ' giveback-sold' : '';
			div(array(
				'id' => 'wp-crowd-fund-giveback-region-' . $giveback['index'],
				'class' =>  $class));
			WPCrowdFund_AdminFields::giveback_title($giveback['title'], $giveback['index'], $giveback['sold']);
			br();
			WPCrowdFund_AdminFields::giveback_cost($giveback['cost'], $giveback['index'], $giveback['sold']);
			br();
			WPCrowdFund_AdminFields::giveback_limit($giveback['limit'], $giveback['index'], $giveback['sold']);
			br();
			WPCrowdFund_AdminFields::giveback_description($giveback['description'], $giveback['index'], $giveback['sold']);
			$of_sold = $giveback['limit'] ? $giveback['limit'] : __('Unlimited', 'wp crowd-fund');
			if($giveback['sold']){
				$sold = sprintf(__('%d of %s sold', 'wp crowd fund'), $giveback['sold'], $of_sold);
				br();
				echo $sold;
			}
			hr();
			div('/');
		}

		div();
		WPCrowdFund_AdminFields::giveback_add();
		div('/');
	}

	public static function mb_campaign_settings(){
		global $post;
		$settings = array_merge(
			array(
				'settings_target' => WPCF_POST_DEFAULT_TARGET,
				'settings_currency' => WPCF_POST_DEFAULT_CURRENCY,
				'settings_date' => WPCF_POST_DEFAULT_DATE,
				'settings_type' => WPCF_POST_DEFAULT_TYPE
			), get_post_custom($post->ID)
		);
		extract($settings);
		wp_nonce_field(basename(__FILE__), 'WPCrowdFund_Admin_Settings');
		br();
		WPCrowdFund_AdminFields::settings_target($settings_target[0]);
		br(2);
		WPCrowdFund_AdminFields::settings_currency($settings_currency[0]);
		br(2);
		WPCrowdFund_AdminFields::settings_date($settings_date[0]);
		br(2);
		WPCrowdFund_AdminFields::settings_type($settings_type[0]);
	}

	public static function mb_campaign_funding(){
		$saved_givebacks = get_children(array(
			'post_parent' => $post->ID,
			'post_type' => 'wpcf-perk',
			'order' => 'ASC',
			'orderby' => 'meta_value_num',
			'meta_key' => 'order',
		));
		if(!empty($saved_givebacks)){
			foreach($saved_givebacks as $giveback){
				$saved_backers = get_children(array(
					'post_parent' => $giveback->ID,
					'post_type' => 'wpcf-backer'
				));
				if(!empty($saved_backers)){
					foreach($saved_backers as $backer){
						$backer_custom = get_post_custom($backer->ID);
						$backer_email = $backer_custom['email'][0];
						$backer_amount = $backer_custom['amount'][0];
						div();
							div();
							echo $backer->post_title;
							div('/');
							div();
							echo $backer_amount;
							div('/');
							div();
							echo $giveback->post_title;
							div('/');
							br(2);
						div('/');
					}
				}
			}
		}
	}

	public static function save_post($post_id){
		global $post;
		if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)  return;
		if(empty($post) || $post->ID != $post_id) return;
		if($post->post_type != 'wpcf-campaign') return;
		$post_type = get_post_type_object($post->post_type);
		if(!current_user_can($post_type->cap->edit_post, $post_id)) return;
		if(@$_REQUEST['action'] == 'trash') return;
		if(!wp_verify_nonce($_POST['WPCrowdFund_Admin_Settings'], basename(__FILE__))) return $post_id;
		self::save_settings($post_id);
		
		if(!wp_verify_nonce($_POST['WPCrowdFund_Admin_Givebacks'], basename(__FILE__))) return $post_id;
		self::save_givebacks($post_id);
	}

	public static function save_settings($post_id){
		$target = WPCrowdFund_AdminValidator::settings_target($_POST['wp-crowd-fund-settings-target']);
		$currency = WPCrowdFund_AdminValidator::settings_currency($_POST['wp-crowd-fund-settings-currency']);
		$date = WPCrowdFund_AdminValidator::settings_date($_POST['wp-crowd-fund-settings-date']);
		$type = WPCrowdFund_AdminValidator::settings_type($_POST['wp-crowd-fund-settings-type']);

		update_post_meta($post_id, 'settings_target', $target);
		update_post_meta($post_id, 'settings_currency', $currency);
		update_post_meta($post_id, 'settings_date', $date);
		update_post_meta($post_id, 'settings_type', $type);
	}

	public static function save_givebacks($post_id){
		$givebacks = WPCrowdFund_AdminValidator::givebacks($_POST['wp-crowd-fund-giveback']);
		if(!$givebacks)
			return $givebacks;

		$old_givebacks = get_children(array(
			'post_parent' => $post_id,
			'post_type' => 'wpcf-perk',
			'order' => 'ASC',
			'orderby' => 'meta_value_num',
			'meta_key' => 'order',
		));
		//pre($old_givebacks);
		$save_givebacks = array();
		if(!empty($old_givebacks)){
			foreach($old_givebacks as $giveback){
				$custom = get_post_custom($giveback->ID);
				$save_givebacks[$custom['order'][0]] = array(	'ID' => $giveback->ID,
																'title' => $giveback->post_title,
																'description' => $giveback->post_content,
																'order' => $custom['order'][0],
																'limit' => $custom['limit'][0],
																'cost' => $custom['cost'][0],
																'sold' => $custom['sold'][0]);
			}
		}

		foreach($givebacks as $order_index => $giveback){
			$save_givebacks[$order_index]['title'] = $giveback['title'];
			$save_givebacks[$order_index]['cost'] = $giveback['cost'];
			$save_givebacks[$order_index]['limit'] = $giveback['limit'];
			$save_givebacks[$order_index]['description'] = $giveback['description'];
			$save_givebacks[$order_index]['save'] = true;
		}

		foreach($save_givebacks as $order_index => $giveback){
			if(!$giveback['sold']){
				$post = array();
				$post['post_title'] = $giveback['title'];
				$post['post_content'] = $giveback['description'];
				$post['post_status'] = 'publish';
				$post['post_parent'] = $post_id;
				$post['post_type'] = 'wpcf-perk';
				if($giveback['ID']) $post['ID'] = $giveback['ID'];
				if($giveback['ID'] && !$giveback['save']){
					wp_delete_post($giveback['ID'], true);
				}else{
					$giveback_id = wp_insert_post($post);
					update_post_meta($giveback_id, 'cost', $giveback['cost']);
					update_post_meta($giveback_id, 'limit', $giveback['limit']);
					update_post_meta($giveback_id, 'order', $order_index);
				}
			}
		}
	}

	public static function admin_enqueue_scripts(){
		wp_enqueue_script('wpcf-admin', plugins_url('js/admin.js', dirname(__FILE__)), array('jquery'));
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery.ui.theme', plugins_url('/css/jquery-ui-1.8.23.custom.css', dirname(__FILE__)));
	}
}