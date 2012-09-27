jQuery(function($){
	$(document).ready(function(){

		if($('span#sample-permalink').text().split('/')[3] == "campaign"
			|| location.search == "?post_type=wpcf-campaign"){

			$('#wp-crowd-fund-giveback-add').click(function(){
				$div =  $('.wp-crowd-fund-giveback-region:last').clone();
				num = parseInt($div.attr('id').replace(/[^0-9]/g, '')) + 1;
				$div.attr('id', $div.attr('id').replace(/[0-9]+/g, num));

				$('input, textarea', $div).each(function(){
					$(this).attr('id', $(this).attr('id').replace(/[0-9]+/g, num));
					$(this).attr('name', $(this).attr('name').replace(/[0-9]+/g, num));
					$(this).val('');
				});

				$('label', $div).each(function(){
					$(this).attr('for', $(this).attr('for').replace(/[0-9]+/g, num));
				});

				$('.wp-crowd-fund-giveback-region:last').after($div);
			});

			$('#wp-crowd-fund-settings-date').datepicker();
		}
	});
});