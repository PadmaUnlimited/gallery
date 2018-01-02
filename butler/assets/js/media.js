(function($){

	jQuery(document).ready(function($) {
		
		isGallery = function(element) {
				
			if ( element.parents('.btr-field-container').data('btr-option-type') == 'gallery' )
				return true;
			else
				return false;
		
		}
		
		
		var thumb_toolbar   = '.btr-toolbar';
		var thickbox_height = ($(window).height() * 90) / 100;
		
		$('.btr-upload-image').live('click', function( event ) {
			
			var media_iframe;
			
			event.preventDefault();
			
			var btn = $(this);
			var field = $(this).parents('.btr-field-container');
			var isMultiple = isGallery($(this));
			var tmpl = field.find('.btr-image-wrapper-tmpl');
			
							
			/* we re-open the iframe if it already exist */
			if ( media_iframe ) {
				
				media_iframe.open();
			
				return;
			
			}
			
			/* we build the media iframe. */
			media_iframe = wp.media.frames.media_iframe = wp.media({
				
				title: jQuery( this ).data( 'uploader_title' ),
				button: {
				  text: jQuery( this ).data( 'uploader_button_text' ),
				},
				multiple: isMultiple
			
			});
			
			/* we run a callback when an image is selected */
			media_iframe.on('select', function() {
			
				var selection = media_iframe.state().get('selection');
					        	
				if ( selection ) {
										
					selection.each( function(attachment) {
										
						if( "thumbnail" in attachment.attributes.sizes )
							attachment_url = attachment.attributes.sizes[ 'thumbnail' ].url;
							
						else
							attachment_url = attachment.attributes.url;
							
														
						if ( isMultiple ) {
												
							newTmpl = tmpl.clone().attr('class', 'btr-image-wrapper');
							newTmpl.find('.image-id')
								.removeAttr('data-name')
								.attr('name', tmpl.find('.image-id').data('name'));
							newTmpl.find('.image-id').attr('value', attachment.id);
							newTmpl.find('img').attr('src', attachment_url);
							
							field.find('.btr-gallery-wrapper').append(newTmpl);
								
						} else {
						
							field.find('.image-id').attr('value', attachment.id);
							
							btn.hide();
							
							field.find('.btr-field').attr('src', attachment_url);
							
							field.find('.btr-image-wrapper')
								.hide()
								.fadeIn();
							
							/* we remove the style to the toolbar to allow the hover CSS to work */
							field.find('.btr-media-toolbar').attr('style', '');
						
						}
											
				    });
					
				}
			
			});
			
			/* we open the iframe */
			media_iframe.open();
		
		});
		
				
		/* we remove the thumbnail and update the count when removed link is clicked */		  	
		$(thumb_toolbar).find('.dashicons-post-trash').live('click', function(event ) {
		
			event.preventDefault();
		
			$(this).closest('.btr-image-wrapper').fadeOut(500, function() {
			
				if ( isGallery($(this)) ) {
				
					$(this).remove();
				
				} else {
				
					$(this).closest('div [class*="field"]').find('.image-id').attr( 'value', '' );
					
					$(this).find('img').attr('src', '');
					
					$(this).closest('div [class*="field"]').find('.btr-upload-image').removeClass('hide').hide().fadeIn();
				
				}
				
			});
		  
		 });
		 
		
		/* we fire the edit iframe when edit link is clicked */
		$(thumb_toolbar).find('.dashicons-edit').live('click', function( event ) {
		
			event.preventDefault();
						
			id = $(this).parents('.btr-image-wrapper').find('.image-id').attr('value');
			
			tb_show( 'Edit image' , btrMedia.adminUrl + 'media.php?attachment_id=' + id + '&action=edit&btr_media_editor=true&btr_action=done_editing_media&type=image&TB_iframe=1&height=' + thickbox_height);
		  
		 });
			  	
	  	
	  	/* we set the thickbox dynamic rezising */
	  	$('#TB_window').css("height", thickbox_height + "px");
	  	
	  	$(window).resize(function() {
	  	
	  		var thickbox_height = ($(window).height() * 90) / 100;
	  		
	  		$('iframe#TB_iframeContent').css("height", thickbox_height - 35 + "px");
	  		
	  		$('#TB_window').css("height", thickbox_height + "px");
	  		
	  		
	  	});
	  	
	  	
	 	if ( $('.btr-gallery-wrapper').length ) {
	 	
	 		$('div.btr-gallery-wrapper').sortable({
	 		    handle: '.btr-toolbar .dashicons-menu',
	 		    placeholder: "btr-state-highlight",
	 			cursor: 'move',
	 			start: function(e, ui ){
	 				ui.placeholder.height($('.btr-image-wrapper').outerHeight() - 6);
	 				ui.placeholder.width($('.btr-image-wrapper').outerWidth() - 6);
	 			}
	 		});
	 		    
	 		$('.btr-gallery-wrapper').disableSelection();
	 		
	 	}
	  	
	});
	
})(jQuery);