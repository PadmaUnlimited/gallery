!(function($) {

	"use strict";
	
	var pluginName = "btrOptions";

    var btrOptions = function( element, options ) {
		
		this.container = $(element);
        this.init();
        this.listen();
    
    }

    btrOptions.prototype = {
    
    	constructor: btrOptions,
        
        checkboxLabelToggle: function ( selector ) {
                	
        	selector.parent().find( 'input[type="checkbox"]' ).click();
            
        },
        repeaterAdd: function ( selector ) {
                	        							
        	$.ajax({
        		type: 'POST',
        		url: ajaxurl,
        		data: { 
        			option: selector.parent().data('option'),
        			count: selector.parent().find('div[class^="btr-repeater-group-"]').length,
        			action: 'butler_admin_fields_repeater'
        		},
        		beforeSend: function() {
        			
        			$('<span class="spinner"></span>').insertAfter(selector);
        										
        		},
        		success: function (html) {
        							
        			$(html).insertAfter(selector).hide().fadeIn();
        			
        			selector.next().find('[data-btr-option-type="select"] .btr-field, [data-btr-option-type="multiselect"] .btr-field').select2();
        			
        			selector.remove();
        			$('[data-btr-option-type="repeater"] .spinner').remove();
        			
        			//initFieldActivation();
        				
        		}
        	});
            
        },
        repeaterRemove: function ( selector ) {
                	        							
        	selector.closest('div[class^="btr-repeater-group-"]').slideUp(500, function() {
        	
        		$(this).remove();
        		
        	});
            
        },
        imageradio: function( selector ) {
        	
    		selector.closest('fieldset').find('label').removeClass('selected');
    		selector.closest('label').addClass('selected');
        	
        },
        slider: function( selector ) {
        	
        	var value = parseInt( selector.find('input.btr-slider-hidden').val() );
			
			var min = parseInt( selector.attr('slider_min') );
			var max = parseInt( selector.attr('slider_max') );
			var interval = parseInt( selector.attr('slider_interval') );
			
			/* this is a fix to make sure wp customize "data-customize-setting-link" fires properly */
			selector.find('input.btr-slider-hidden')
				.attr( 'type', 'text' )
				.hide();
			
			selector.slider({
				range: 'min',
				value: value,
				min: min,
				max: max,
				step: interval,
				slide: function( event, ui ) {
					
					/* Update visible output */
					$(this).parent().find('.btr-slider-value').text(ui.value);
					
					/* Update hidden input */
					$(this).find('input.btr-slider-hidden') 
						.val(ui.value)
						.keyup();

				}
			});

			/* Remove href attribute to keep status bar from showing */
			selector.find('.ui-slider-handle').removeAttr('href');
        	
        },
        activation: function( selector ) {
        	        		
        	if ( selector.is(":checked") ) {
        	
        		selector.parent()
        			.removeClass('deactivated')
        			.find('.btr-field, .btr-slider-value, .btr-slider-unit').removeAttr('disabled').css( 'opacity', 1 );
        			
        		if ( selector.parents('[data-btr-option-type]').data( 'btr-option-type' ) == 'slider' )
        			selector.parent().find('.btr-field').slider( "enable" );
        	
        	} else {
        	
        		selector.parent()
        			.addClass('deactivated')
        			.find('.btr-field, .btr-slider-value, .btr-slider-unit').attr('disabled', 'disabled').css( 'opacity', 0.5 );
        			        			
        		if ( selector.parents( '[data-btr-option-type]' ).data( 'btr-option-type' ) == 'slider' )
        			selector.parent().find('.btr-field').slider( "disable" );
        			
        	}
        	
        },
        readmore: function( selector ) {
                	
        	selector.parents('.btr-field-description').find('.btr-extended-content').slideToggle( 400, function() {
        	
        		if ( $(this).is(':visible') )
        			selector.text('Less...');
        		else
        			selector.text('More...');
        	
        	});
        	
        },
        sortable: function( selector ) {
                
        	selector.sortable({
        	    items: '[class^="btr-repeater-group-"]',
        	    handle: '.btr-toolbar a.dashicons-menu',
        	    placeholder: "btr-state-highlight",
        		cursor: 'move',
        		start: function(e, ui ){
        			ui.placeholder.height( $('[class^="btr-repeater-group-"]').outerHeight() - 6 );
        			ui.placeholder.width( $('[class^="btr-repeater-group-"]').outerWidth() - 6 );
        		}
        	      
        	});
        	    
        	selector.disableSelection();
        	
        },
        postbox: function( selector ) {
                
        	/* close postboxes that should be closed */
        	$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
        	
        	postboxes.add_postbox_toggles( selector.data( 'options-page' ) );
        	
        },
        init: function() {
        
        	var that = this;
        	
        	/* fire select 2 */
        	if ( $.isFunction( $.fn.select2 ) )
	        	this.container.find('[data-btr-option-type="select"] .btr-field, [data-btr-option-type="multiselect"] .btr-field').select2({
	        		placeholder: 'Make your selection'
	        	});
	        
	        /* fire ui slider */	
	        this.container.find('[data-btr-option-type="slider"] .btr-field').each( function() {
	        	        		
	        	that.slider( $(this) );
	        	
	        });
        	
        	/* fire sortable */
        	if ( this.container.find('[data-btr-option-type="repeater"]').length )
        		this.sortable( this.container.find('[data-btr-option-type="repeater"] > .btr-field-wrap') );
        	
        	/* add active imageradio */
        	this.container.find('[data-btr-option-type="imageradio"] input:checked:enabled').closest('label').addClass('selected');
        	
        	/* fire activation toggle */
        	this.container.find('input.activation').each( function() {
        		        		
        		that.activation( $(this) );
        		
        	});
        	
          	/* remove wp customize empty activation field type */
        	this.container.find('li.customize-control').each( function() {
        		        		
        		if ( !$.trim( $(this).html() ).length )
        			$(this).remove()
        		
        	});
        	
        	/* fire the postboxes */
        	if ( ( typeof postboxes != 'undefined' ) && this.container.attr( 'data-options-page' ) )
        		this.postbox( this.container );
        	        	
        },
        listen: function() {
        
        	var that = this;
        	
        	/* make checkbox legend toggling checkbox input on click */
        	this.container.on( 'click', '.checkbox-label', function( e ) {
        		        		
        		that.checkboxLabelToggle( $(this) );
        	
        	});
        	
        	/* add repeater group on click */
        	this.container.on( 'click', 'a.btr-repeat', function( e ) {
        	
        		e.preventDefault();
        		
       			that.repeaterAdd( $(this) );
        		
        	});
        	
        	/* remove repeater group on click */
        	this.container.on( 'click', '.btr-repeater-toolbar a.dashicons-post-trash', function( e ) {
        	
        		e.preventDefault();
        		
        		that.repeaterRemove( $(this) );
        		
        	});
        	
        	/* fire imageradio on click */
        	this.container.on( 'click', '[data-btr-option-type="imageradio"] label', function( e ) {
        	        		
        		that.imageradio( $(this) );
        		
        	});
        	
        	/* fire activation toggle on click */
        	this.container.on( 'click', 'input.activation', function() {
        		        		
        		that.activation( $(this) );
        		
        	});
        	
        	/* fire readmore on click */
        	this.container.on( 'click', '.btr-read-more', function( e ) {
        		
        		e.preventDefault();
        		
        		that.readmore( $(this) );
        		
        	});
        	
        	/* prevent dragging handle default on click */
        	this.container.on( 'click', '.btr-toolbar a.dashicons-menu', function(e) {
        	
        		e.preventDefault();
        		
        	});
        	
        	
            
        }
        
    };

    $.fn[ pluginName ] = function ( options ) {
    
		return this.each( function() {
		
			if ( !$.data( this, "plugin_" + pluginName ) )
				$.data( this, "plugin_" + pluginName, new btrOptions( this, options ) );

		});
    };
    
    /* fire the plugin */
    $(document).ready(function($) {
        
    	$('#edittag, #post-body, [data-options-page], #customize-controls').btrOptions();
    	
    });
       
	
})( window.jQuery );