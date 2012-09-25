<form method="post">
	<label for="<?php wpcf_contribute_amount_input(true,true); ?>"><?php echo __('Contribution Amount', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_amount_input(); ?>
	<label for="<?php wpcf_contribute_perks_input(true,true); ?>"><?php echo __('Choose Your Perk', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_perks_input(); ?>
	<label for="<?php wpcf_contribute_name_input(true,true); ?>"><?php echo __('Name', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_name_input(); ?>
	<label for="<?php wpcf_contribute_email_input(true,true); ?>"><?php echo __('Email', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_email_input(); ?>
	<label for="<?php wpcf_contribute_comments_input(true,true); ?>"><?php echo __('Comments', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_comments_input(); ?>
	<input type="submit" value="<?php echo __('Contribute Now', 'wp crowd fund'); ?>" />
</form>