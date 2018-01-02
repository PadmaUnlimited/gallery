/* gallery block admin js
*****************************************************/
;(function($) {

	/* this function is where we are going to right all custom js for the block */
	hwr_gallery_js = function(id) {

		var api = hwr.blockOptionsApi;

		/* we modify the view and dipslay notices */
		if ( api.getPageLayout() == 'hwr_gallery' ) {

			api.update_select(api.getInput(id, 'view'), 'album', 'album');
			api.select_status(api.getInput(id, 'view'), ['albums', 'media']);

			/* album post type notice */
			if ( api.getPageId() ) {

				var album_title = $("#content").contents().find(".album-title").text();

				if ( album_title != '')
					var notice_content = 'Since you are on <strong>"' + album_title + '"</strong> page, only the <strong>"' + album_title + '"</strong> album will be displayed. Therefore, the albums view and filters are disabled.';

				else
					var notice_content = 'Since you are on Gallery post type page, the appropriate album will be displayed automatically. Therefore, the albums view and filters are disabled.';

			} else {

				var notice_content = 'Since you are on the Gallery post type, all the children of this post type will be displayed as single view if left empty. Therefore, the albums view and filters are disabled. The first post is displayed below this notice as an example.';

			}

			api.notice(id, 'setup', notice_content, true);

		} else if ( api.getPageLayout() == 'attachment' ){

			api.update_select(api.getInput(id, 'view'),'media', 'media');
			api.select_status(api.getInput(id, 'view'), ['albums', 'album']);

			/* media post type notice */
			if ( api.getPageId() ) {

				var notice_content = 'Since you are on Media post type page, the appropriate image will be displayed automatically. Therefore, only the appropriate options are available.';

			} else {

				var notice_content = 'Since you are on the Media post type, all the children of this post type will be displayed as single view if left empty. Therefore, only the appropriate options are available. The first post is displayed below this notice as an example.';

			}

			api.notice(id, 'setup', notice_content, true);

		} else {

			api.select_status(api.getInput(id, 'view'), 'media');
			api.select_status(api.getInput(id, 'view'), ['albums', 'album'], true);

		}


		/* we modify the layout */
		if ( api.getInput(id, 'view').val() != 'album' ) {

			api.update_select(api.getInput(id, 'layout'), 'grid', 'grid');
			api.select_status(api.getInput(id, 'layout'), 'slider');

		} else {

			api.select_status(api.getInput(id, 'layout'), 'slider', true);

		}


		/* we modify the overlay content and overlay effects according */
		if ( api.getInput(id, 'layout').val() == 'slider' ) {

			/* we start by removing all attribute which could cause problems */
			api.select_status(api.getInput(id, 'overlay-effect'), ['fade', 'top', 'right', 'bottom', 'left'], true);

			/* we modify overlay content accordingly */
			api.update_select(api.getInput(id, 'overlay-content'), 'image', 'title', true);
			api.select_status(api.getInput(id, 'overlay-content'), 'image');

			/* we modify overlay effects accordingly */
			api.update_select(api.getInput(id, 'overlay-effect'), ['top', 'bottom'], 'bottom');
			api.select_status(api.getInput(id, 'overlay-effect'), ['fade', 'right', 'left']);

			/* we modify slider height accordingly */
			if ( api.getInput(id, 'slider-effect').val() == 'slide' && api.getInput(id, 'slider-direction').val() == 'vertical' ) {

				api.update_select(api.getInput(id, 'slider-height'), 'animate', 'auto', true);
				api.select_status(api.getInput(id, 'slider-height'), 'animate');

			} else {

				api.select_status(api.getInput(id, 'slider-height'), 'animate', true);

			}

		} else if ( api.getInput(id, 'overlay-content').val() == 'image' ){

			/* we modify overlay effects accordingly */
			api.update_select(api.getInput(id, 'overlay-effect'), 'fade', 'fade');
			api.select_status(api.getInput(id, 'overlay-effect'), ['top', 'right', 'bottom', 'left']);

		} else {

			/* we remove all disabled attribute */
			api.select_status(api.getInput(id, 'overlay-content'), 'image', true);
			api.select_status(api.getInput(id, 'overlay-effect'), ['fade', 'top', 'right', 'bottom', 'left'], true);

		}

	}


})(jQuery);