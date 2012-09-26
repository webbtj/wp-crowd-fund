jQuery(function($){
	$(document).ready(function(){
		//when a perk button is clicked:
		// 1. get the ID number,
		// 2. get the minimum cost of the perk with that ID number
		// 3. plug that cost into the amount text field
		// 4. call disable perks function
		$('.wpcf-perk-button').click(function(){
			if(!$(this).hasClass('wpcf-perk-soldout')){
				perk_id = $(this).attr('id').replace(/[^0-9]/g, '');
				min_amount = $('input[type=radio][name="wpcf-contribute-perk"][value='+perk_id+']').prop('checked', true).data('min-contribution');
				$('#wpcf-contribute-amount').val(min_amount);
				disable_perks_on_price();
			}
		});

		//on key down, only allow numeric values
		$('#wpcf-contribute-amount').keydown(function(e){
			ret = false;
			if(e.keyCode >= 48 && e.keyCode <= 57) // 0 - 9
				ret = true;
			else if(e.keyCode >= 96 && e.keyCode <= 105) // 0 - 9 (numpad)
				ret = true;
			else if(e.keyCode == 110 || e.keyCode == 190) // decimal/period
				ret = true;
			else if(e.keyCode == 8 || e.keyCode == 46) // backspace / delete
				ret = true;
			return ret;
		//on key up call the disable perks function
		}).keyup(function(){disable_perks_on_price()});

		$('#wpcf-contribute-anonymous').click(function(){
			if($(this).is(':checked')){
				$('#wpcf-contribute-name, #wpcf-contribute-email, .wpcf-not-required-anonymous').prop('disabled', true);
			}else{
				$('#wpcf-contribute-name, #wpcf-contribute-email, .wpcf-not-required-anonymous').prop('disabled', false);
			}
		});

		//disable all perks who "min-contribution" data attribute is below the value in the amount field
		//if a perk that was previously selected is below the value in the amount field, it will be un-selected
		function disable_perks_on_price(){
			input_amount = $('#wpcf-contribute-amount').val();
			if(isNaN(input_amount))
				input_amount = 0;
			if(!isNaN(input_amount)){
				$('input[type=radio][name="wpcf-contribute-perk"]').each(function(){
					if(parseFloat($(this).data('min-contribution')) > parseFloat(input_amount) || input_amount==0){
						$(this).prop('checked', false).prop('disabled', true);
					}else{
						$(this).prop('disabled', false);
					}
				});
			}
		}
	});
});