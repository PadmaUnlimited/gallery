(function($){

	$(document).ready(function($) {
    	
    	/* Image Radio */
		$('.btr-imageradio input:checked:enabled').closest('label').addClass('selected');
		
		$('.btr-imageradio label').on('click', function(e) {
		    		
			$(this).closest('fieldset').find('label').removeClass('selected');
			$(this).closest('label').addClass('selected');
			
		});
		
		
		/* Sliders */
		$('.btr-slider .slider-field').each(function() {
			
			var value = parseInt($(this).parents('.btr-slider').find('input').val());
			
			var min = parseInt($(this).attr('slider_min'));
			var max = parseInt($(this).attr('slider_max'));
			var interval = parseInt($(this).attr('slider_interval'));
			
			/* this is a fix to make sure wp customize "data-customize-setting-link" fires properly */
			$(this).parents('.btr-slider').find('input')
				.attr( 'type', 'text' )
				.hide();
			
			$(this).slider({
				range: 'min',
				value: value,
				min: min,
				max: max,
				step: interval,
				slide: function( event, ui ) {
					
					/* Update visible output */
					$(this).parents('.btr-slider').find('span.slider-value').text(ui.value);
					
					/* Update hidden input */
					$(this).parents('.btr-slider').find('input') 
						.val(ui.value)
						.keyup();

				}
			});

			/* Remove href attribute to keep status bar from showing */
			$(this).find('.ui-slider-handle').removeAttr('href');
			
		});
		
		/* multicheckox/
		$('.btr-multicheckbox input.checkbox-field').each(function() {
			
			var dataCustomize = $(this).data('customize-setting-link');
			var id = $(this).attr('id');
			
			var parentId = $(this).parents('.customize-control').attr('id');
			
			$(this).parents('.customize-control').removeAttr('id');
			
			$(this).parents('.checkbox-label').attr('id', parentId + '_' + id);
									
			$(this).attr( 'data-customize-setting-link', dataCustomize + '_' + id);
			
			
		}); */
    	
	});
	
})(jQuery);


