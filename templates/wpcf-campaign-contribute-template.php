<form method="post">
	<label for="<?php wpcf_contribute_amount_input(true,true); ?>"><?php echo __('Contribution Amount', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_amount_input(); ?>
	<label for="<?php wpcf_contribute_perks_input(true,true); ?>"><?php echo __('Choose Your Perk', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_perks_input(); ?>
	<label for="<?php wpcf_contribute_anonymous_checkbox(true,true); ?>"><?php echo __('I would like to make my contribution anonymously', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_anonymous_checkbox(); ?>
	<label for="<?php wpcf_contribute_name_input(true,true); ?>"><?php echo __('Name', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_name_input(); ?>
	<label for="<?php wpcf_contribute_email_input(true,true); ?>"><?php echo __('Email', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_email_input(); ?>
	<label for="<?php wpcf_contribute_comments_input(true,true); ?>"><?php echo __('Comments', 'wp crowd fund'); ?></label>
	<?php wpcf_contribute_comments_input(); ?>

	<?php wpcf_contributor_label('address1'); ?>
	<?php wpcf_contributor_text_field('address1'); ?>

	<?php wpcf_contributor_label('address2'); ?>
	<?php wpcf_contributor_text_field('address2'); ?>

	<?php wpcf_contributor_label('city'); ?>
	<?php wpcf_contributor_text_field('city'); ?>

	<?php wpcf_contributor_label('province'); ?>
	<?php wpcf_contributor_text_field('province'); ?>

	<?php wpcf_contributor_label('country'); ?>
	<?php wpcf_contributor_text_field('country'); ?>

	<?php wpcf_contributor_label('postal_code'); ?>
	<?php wpcf_contributor_text_field('postal_code'); ?>

	<input type="submit" value="<?php echo __('Contribute Now', 'wp crowd fund'); ?>" />
</form>