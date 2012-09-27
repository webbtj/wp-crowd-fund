<?php

class PP_IPN{

	public static function autopost_form(){
		global $post;
		pre($post);
		pre($_POST);
		pre($_SESSION);
		$backer = wp_get_single_post($_SESSION['backer']);
		$backer_custom = get_post_custom($backer->ID);
		pre($backer);
		pre($backer_custom);
		$amount = $backer_custom['amount'][0];
		$backer_id = $backer->ID;
		$currency_code = 'CAD';
		$campaign = wp_get_single_post($backer->ancestors[1]);
		$title = $campaign->post_title;
		$url = get_permalink();
		//exit();
		?>
			<form name="wpcf-pp-ipn-form" id="wpcf-pp-ipn-form" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="business" value="sell_1266966509_biz@hotmail.com">
			<input type="hidden" name="item_name" value="Pursu.it backing for: <?php echo $title; ?>">
			<input type="hidden" name="item_number" value="">
			<input type="hidden" name="amount" value="<?php echo $amount; ?>">
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="return" value="<?php echo $url; ?>">
			<input type="hidden" name="no_note" value="1">
			<input type="hidden" name="currency_code" value="<?php echo $currency_code; ?>">

			<input type="hidden" name="on0" value="xid">
			<input type="hidden" name="os0" value="<?php echo $backer_id; ?>">

			<input name="the_submit" type="submit" style="display:none;">
			</form>
			<script type="text/javascript">
				//document.forms['wpcf-pp-ipn-form'].submit();
			</script>
		<?php
	}
}