/* gallery block admin js
*****************************************************/
;(function($) {

	$(document).ready(function(){

		/* we prevent the elements header and subheader to be clickable */
		$(document).on( 'click', 'body', function(){

			 $(".element-header, .element-sub-header").parent().parent()
			 	.removeClass("sub-element")
			 	.addClass('element-group');

		});

		$(document).on( 'click', 'a.pur-save', function(e) {

			e.preventDefault();

			$("#save-button").trigger("click");

		});

		$(document).on( 'click', 'a.pur-reload', function(e) {

			e.preventDefault();

			location.reload();

		});

	});


	pur.blockOptionsApi = {

		version: '1.0.0',

		versionCompare:function(v1, v2, operator) {

		    operator = operator == '=' ? '==' : operator;

		    var v1parts = v1.split('.'), v2parts = v2.split('.');
		    var maxLen = Math.max(v1parts.length, v2parts.length);
		    var part1, part2;
		    var cmp = 0;

		    for ( var i = 0; i < maxLen && !cmp; i++ ) {

		        part1 = parseInt(v1parts[i], 10) || 0;
		        part2 = parseInt(v2parts[i], 10) || 0;

		        if( part1 < part2 )
		            cmp = 1;

		        if( part1 > part2 )
		            cmp = -1;
		    }

		    return eval('0' + operator + cmp);

		},

		loadStyle:function(url) {

			if ( pur.assetsLoaded[url] == true )
				return false;

			var link = document.createElement("link");

		    link.type = "text/css";
		    link.rel = "stylesheet";
		    link.href = url;

		    document.getElementsByTagName("head")[0].appendChild(link);

		    pur.assetsLoaded[url] = true;

		},

		loadScript:function(url, success) {

			if ( pur.assetsLoaded[url] == true )
				return success();

			$.getScript(url, success);

		    pur.assetsLoaded[url] = true;

		},

		select_status:function(selector, target, enable) {

			var array = $.isArray(target) ? target : [target];

			$.each( array, function( key, value ) {

				if ( enable )
					selector.children('option[value="' + value + '"]').removeAttr('disabled');

				else
					selector.children('option[value="' + value + '"]').attr('disabled','disabled');

			});

		},

		update_select:function(selector, checker, new_value, condition) {

			var array = $.isArray(checker) ? checker : [checker];

			if ( condition ) {

				if ( $.inArray(selector.val(), array) == 0 )
					selector.val(new_value).change();

			} else {

				if ( $.inArray(selector.val(), array) == -1 )
					selector.val(new_value).change();

			}

		},

		updateOptions:function(function_name, blockId) {

			$('.input [id^="input-' + blockId + '"]').each( function(key, value) {

				/* works for all selects */
			 	 $(value).bind('change', function(){

			 	 	eval(function_name + "(blockId)");

			 	 });

			 	 /* works for all input besides slider */
		 	 	 $(value).bind('keyup blur', function(){


			 	 	eval(function_name + "(blockId)");

		 	 	 });

			 	 /* works for slider since in is specific to the ui slider */
			 	 $(value).parent().bind('slidechange', function(){

			 	 	eval(function_name + "(blockId)");

			 	 });

			 	 /* works for checkbox and multiselect */
			 	 $(value).parent().parent().bind('click', function(){

			 	 	eval(function_name + "(blockId)");

			 	 });

			});

		},

		inputToggle:function(input, val) {

			/* we have to delay the action a bit to be sure hw js toggle run first */
			setTimeout( function () {

				handleInputToggle( input, val );

			}, 05 );

		},

		selectorToObject:function(elements) {

			$.each( elements, function( key, value ) {

				elements[key] = $(value);

			});

			return elements;

		},


		getInput:function(blockId, optionId) {

			return $('#input-' + blockId + '-' + optionId);

		},

		getOption:function(blockId, optionId) {

			return $('#block-' + blockId + '-tab #input-' + optionId);

		},

		getTab:function(blockId, tab) {

			return $('#block-' + blockId + '-tab #sub-tab-' + tab);

		},

		getWrapper:function(blockId, wrapperId) {

			return $('#block-' + blockId + '-tab .wrapper.' + wrapperId);

		},

		notice:function(block_id, tab, content, show_once) {

			var notice = $('#block-' + block_id + '-tab #sub-tab-' + tab + '-content').find('.pur-admin-notice');

			show_notice = function(notice) {

				notice.find('p').html(content);
				notice.fadeIn();
				notice.find('p').removeClass('show-once');

			}

			if ( show_once ) {

				if ( notice.find('p').hasClass('show-once') )
					show_notice(notice);

			} else {

				show_notice(notice);


			}

			notice.find("a.pur-close").click(function() {

				event.preventDefault();

				$(this).parent().fadeOut();

			});

		},

		closeNotice:function(block_id, tab) {

			var notice = $('#block-' + block_id + '-tab #sub-tab-' + tab + '-content').find('.pur-admin-notice');

			notice.fadeOut();

		},

		saveVe: function() {

			event.preventDefault();

			$("#save-button").trigger("click");


		},

		reloadVe: function() {
			event.preventDefault();
			location.reload();
		},

		getUrlParam:function(key) {

			var result = new RegExp(key + "=([^&]*)", "i").exec($(location).attr('href'));

			return result && result[1] || "";

		},

		getLayoutParam:function() {

			if ( this.versionCompare( pur.PadmaVersion, '3.8', '>=' ) )
				return decodeURIComponent( this.getUrlParam('ve-layout') ).split('||');
			else if ( this.versionCompare( pur.PadmaVersion, '3.7', '>=' ) )
				return this.getUrlParam('layout').split('||');
			else
				return this.getUrlParam('layout').split('-');

		},

		getPageType:function() {

			var page_type = this.getLayoutParam();

			return page_type[0];

		},

		getPageLayout:function() {

			var page_layout = this.getLayoutParam();

			if ( typeof page_layout[1] == "undefined")
				return false;

			return page_layout[1];

		},

		getPageId:function() {

			var page_id = this.getLayoutParam();

			if ( typeof page_id[2] == "undefined")
				return false;

			return page_id[2];

		},

	};


})(jQuery);