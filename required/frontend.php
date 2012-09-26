<?php
add_filter('the_content', array('WPCrowdFund_FrontEnd', 'the_content'));
add_action('wp_enqueue_scripts', array('WPCrowdFund_FrontEnd', 'enqueue_scripts'));

class WPCrowdFund_FrontEnd{

	function the_content($content){
		global $post;
		if($post->post_type == 'wpcf-campaign'){
			$content = '';
			$display_confirmation = false;
			$contribution = WPCrowdFund_FrontEnd_Process::user_contribution();
			if($contribution){
				if($contribution===true){
					$display_confirmation = true;
				}elseif(is_numeric($contribution) && $contribution > 0){
					//do confirmation
					$display_confirmation = WPCrowdFund_FrontEnd_Process::confirmation_page();
				}elseif(is_string($contribution)){
					//do error message
					$perk_error = WPCrowdFund_FrontEnd_Process::error_message($contribution);
				}
			}
			if(!$display_confirmation)
				include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-single-template.php'));
		}
		return $content;
	}

	function enqueue_scripts(){
		wp_enqueue_script('wpcf-front-end', plugins_url('js/wp-crowd-fund.js', dirname(__FILE__)), array('jquery'));
	}
}

function wpcf_template_include($fn){
	$original = $fn;
	$fn = explode('/', $fn);
	$fn = array_pop($fn);
	$themed_version = get_theme_root() . '/' . get_template() . '/' . $fn;
	if(file_exists($themed_version))
		return $themed_version;
	return $original;
}