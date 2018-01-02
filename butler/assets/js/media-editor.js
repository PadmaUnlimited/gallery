(function($){
	jQuery(document).ready(function($) {
			
		/* we change to body iframe to only have the media content. This prevent notices and other wp stuff to be displayed */
		var body_content = $('#wpbody-content .wrap').html();
		
		$('#wpbody-content').html('<div class="wrap">' + body_content + '</div>');
		
		$('body').append('<img class="btr-media-loader" src="' + btrMediaEditor.loaderUrl + '" />');
		
		$('.btr-media-loader').hide();
		
		$('#wpbody').fadeIn();
				
		/* we go a bit fancy and add a spinner on click */
		jQuery('.media-upload-form #save').live('click', function(){
			
			$('.wrap').fadeOut(500);
			$('.btr-media-loader').delay(500).fadeIn();
			
		});
		  	
	});
})(jQuery);