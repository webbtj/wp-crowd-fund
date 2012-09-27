<ul class="wpcf-campaign-contribute-perks">
	<?php
		if(is_array($perks) && !empty($perks)){ 
			foreach($perks as $perk): ?>
			<li>
				<?php wpcf_contribute_perk_radio($perk); ?>
				<div class="wpcf-perk-amount"><?php wpcf_perk_amount($perk); ?></div>
				<div class="wpcf-perk-title"><?php wpcf_perk_title($perk); ?></div>
				<div class="wpcf-perk-remaining"><?php wpcf_perk_remaining($perk); ?></div>
				<?php if(wpcf_perk_soldout($perk)): ?>
					<div class="wpcf-perk-soldout"><?php echo __('Sold Out', 'wp crowd fund'); ?></div>
				<?php endif; ?>
			</li>
	<?php 
			endforeach; 
		}?>
</ul>