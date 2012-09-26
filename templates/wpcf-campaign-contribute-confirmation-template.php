<div id="perk-confirmation">
	<?php echo time() - (2*60*60); ?>
	<div id="perk-title"><?php echo $perk_title; ?></div>
	<div id="perk-cost"><?php echo $perk_cost; ?></div>
	<div id="perk-description"><?php echo $perk_description; ?></div>
	<div id="contribution-amount"><span><?php echo __('Your Contribution:', 'wp crowd fund'); ?> </span><?php echo $amount; ?></div>
	<div id="contribution-name"><span><?php echo __('Your Name:', 'wp crowd fund'); ?> </span><?php echo $name; ?></div>
	<div id="contribution-email"><span><?php echo __('Your Email:', 'wp crowd fund'); ?> </span><?php echo $email; ?></div>
	<div id="contribution-comments"><span><?php echo __('Your Comments:', 'wp crowd fund'); ?> </span><?php echo $comments; ?></div>
	<?php wpcf_confirmation_button(__('Confirm Contribution', 'wp crowd fund')); ?>
	<?php wpcf_cancel_contribution_button(__('Cancel Contribution', 'wp crowd fund')); ?>
</div>