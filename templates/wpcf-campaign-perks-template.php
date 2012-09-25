<ul class="wpcf-campaign-perks">
	<?php foreach($perks as $perk): ?>
		<li>
			<div class="wpcf-perk-amount"><?php wpcf_perk_amount($perk); ?></div>
			<div class="wpcf-perk-title"><?php wpcf_perk_title($perk); ?></div>
			<div class="wpcf-perk-description"><?php wpcf_perk_description($perk); ?></div>
			<div class="wpcf-perk-remaining"><?php wpcf_perk_remaining($perk); ?></div>
			<div class="wpcf-perk-contribute"><input type="button"
				class="<?php wpcf_perk_button_required_classes(); echo wpcf_perk_soldout($perk) ? 'wpcf-perk-soldout' : 'wpcf-perk-contribute'; ?> "
				value="<?php echo wpcf_perk_soldout($perk) ? __('Sold Out', 'wp crowd fund') : __('Contribute Now', 'wp crowd fund'); ?> "
				id="<?php wpcf_perk_button_required_id($perk); ?>" /></div>
		</li>
	<?php endforeach; ?>
</ul>