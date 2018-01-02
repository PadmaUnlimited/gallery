jQuery.noConflict();
(function($){
	jQuery(document).ready(function($){
	
		/* we change to body iframe to only have the media content. This prevent notices and other wp stuff to be displayed */
		var body_content = $('#wpbody-content .wrap').html();
		
		$('#wpbody-content').html('<div class="saving-loader"><p>Saving</p></div><div class="wrap">' + body_content + '</div>');
		
		$('.saving-loader').hide();
		
		
		/* we append the hidden input which will pass the value went the media is edited */
		$('#media-single-form').append('<input type="hidden" name="hwr_action" value="done_editing" />');
		
		
		/* we go a bit fancy and add a spinner on click */
		jQuery('.media-upload-form #save').live('click', function(){
			
			$('.wrap').fadeOut(500);
			$('.saving-loader').delay(500).fadeIn();
			
		});
		  	
	});
})(jQuery);