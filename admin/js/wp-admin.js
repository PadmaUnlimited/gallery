jQuery.noConflict();
(function($){
	jQuery(document).ready(function($){

		var media_iframe;
		var gallery 	    = '.hwr-gallery-options';
		var thumb 		    = '.hwr-thumbnail';
		var thumbs 		    = '.hwr-thumbnails';
		var thumb_toolbar   = '.hwr-thumbnail-toolbar';
		var ui_sortable     = '.ui-sortable';
		var thickbox_height = ($(window).height() * 90) / 100;

		$('.hwr-upload-image').live('click', function( event ){

		  event.preventDefault();

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
		    multiple: true

		  });

		  /* we run a callback when an image is selected */
		  media_iframe.on( 'select', function() {

				var selection = media_iframe.state().get('selection');

				if( selection ) {

					var i = 0;

					selection.each(function(attachment){

						if( "thumbnail" in attachment.attributes.sizes )
							attachment_url = attachment.attributes.sizes[ 'thumbnail' ].url;

						else
							attachment_url = attachment.attributes.url;

				    	var image = {
					    	id : attachment.id,
					    	src : attachment_url
				    	};

						var tmpl = '<div class="hwr-thumbnail"><input class="hwr-image-value" type="hidden" name="hwr_gallery_image[]" value="' + attachment.id + '" /><div class="hwr-image-wrap"><img src="' + image.src + '" /></div><div class="hwr-thumbnail-toolbar"><ul><li><a href="#" class="hwr-drag hwr-btn">Drag</a></li><li><a href="#" class="hwr-edit hwr-btn">Edit</a></li><li><a href="#" class="hwr-remove hwr-btn">Remove</a></li></ul></div></div>';

						$(tmpl).appendTo(thumbs)
							.hide()
							.fadeIn('slow')
							.css({
								"height" : $('.hwr-thumbnails').data('thumb-h') + "px",
								"width" : $('.hwr-thumbnails').data('thumb-w') + "px"
							});

				    });


					update_count();


				}

		    });

		  /* we open the iframe */
		  media_iframe.open();

		});


		/* we update the thumb count */
		update_count = function() {

			var thumb_count = $(thumb).length;

			var thumb_count = ( thumb_count == 0) ? 'no image' : thumb_count;

			$(gallery).find('#hwr_gallery_count').attr( 'value', thumb_count );
			$(gallery).find('.hwr-thumbnail-count span').text( thumb_count );

			if (thumb_count == 'no image') {

				$('.hwr-no-thumbnail').show();
				$('.hwr-gallery-options .drag-notice').fadeOut();

			} else {

				$('.hwr-no-thumbnail').hide();
				$('.hwr-gallery-options .drag-notice').fadeIn();

			}

		}

		/* we show the thumbnail toolbar on hover. */
		$(thumb).live('mouseover', function(){

			$(this).find(thumb_toolbar).fadeIn('fast')
									   .animate({ opacity: 1 }, {queue: false, duration: 'fast'});

		}).live('mouseout',  function() {

		    $(this).find(thumb_toolbar).animate({ opacity: 0 }, {queue: false, duration: 'fast'});

		});


		/* we remove the thumbnail and update the count when removed link is clicked */
		$(thumb_toolbar).find('.hwr-remove').live('click', function(event ){

			event.preventDefault();

			$(this).closest(thumb).find('.hwr-image-value').attr( 'value', '' );

			$(this).closest(thumb).fadeOut( function(){

				$(this).remove();

				update_count();

			});


		 });


		/* we fire the edit iframe when edit link is clicked */
		$(thumb_toolbar).find('.hwr-edit').live('click', function( event ){

			event.preventDefault();

			id = $(this).closest(thumb).find('.hwr-image-value').attr( 'value');

			tb_show( 'Edit image' , hwr_admin_url + 'media.php?attachment_id=' + id + '&action=edit&hwr_media_editor=true&hwr_action=done_editing&type=image&TB_iframe=1&height=' + thickbox_height);

		 });


		/* we call the drag and drop function */
	  	if ($(ui_sortable).length) {

		  	$(ui_sortable).sortable({

		  	    handle: '.hwr-drag.hwr-btn',
				placeholder: "hwr-state-highlight",
				cursor: 'move',
				start: function(e, ui ){
					ui.placeholder.height($(thumb).outerHeight() - 6);
					ui.placeholder.width($(thumb).outerWidth() - 6);
				}

		  	});

		  	$(ui_sortable).disableSelection();

		 }


	  	/* we set the thickbox dynamic rezising */
	  	$('#TB_window').css("height", thickbox_height + "px");

	  	$(window).resize(function() {

	  		var thickbox_height = ($(window).height() * 90) / 100;

	  		$('iframe#TB_iframeContent').css("height", thickbox_height - 35 + "px");

	  		$('#TB_window').css("height", thickbox_height + "px");


	  	});


	  	/* we initiate the actions */
	  	update_count();

	});
})(jQuery);