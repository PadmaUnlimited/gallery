<?php

class HeadwayGalleryBlockDisplay {

	function __construct($block) {

		$this->block     = $block;
		$this->page_info = self::page_info($block);
		$this->set       = HeadwayHwrGalleryBlockOptions::settings($this->block);
		$this->cond_set  = $this->cond_set();
		$this->columns   = $this->set_columns();
		$this->notice    = $this->notices();

	}


	function gallery_js() {

		$carousel = $slider = '';

		/* we set the slider */
		$slider_nav = $this->set['slider-nav'] ? 'true' : 'false';

		$animation_loop = $this->set['slider-enable-loop'] ? 'true' : 'false';
		$control_nav = ($this->set['slider-pager'] && $this->set['slider-enable-pager-thumbs'] == false) || ($this->set['slider-enable-pager-thumbs'] == true && $this->set['slider-pager-show-all'] == true) ? 'true' : 'false';
		$animate_height = $this->set['slider-height'] == 'animate' && $this->set['slider-direction'] != 'vertical' ? 'true' : 'false';
		$slideshow = $this->set['slider-enable-slideshow'] ? 'true' : 'false';
		$direction_nav = $this->set['slider-nav'] ? 'true' : 'false';
		$pager_direction_nav = $this->set['slider-pager-nav'] ? 'true' : 'false';
		$randomize = $this->set['slider-pager'] && $this->set['slider-enable-pager-thumbs'] == false ? 'true' : 'false';
		$sync = $this->set['slider-pager'] && $this->set['slider-enable-pager-thumbs'] ? 'sync: "#carousel-' . $this->block['id'] . '-" + i,' : '';

		/* our custom js */
		$js = 'jQuery.noConflict();
			(function ($){';

				$js .= 'jQuery(document).ready(function(){';

					/* we remove no-js class */
					$js .= ' $(".hwr-gallery").removeClass("no-js");';

				$js .= '});';


			/* we add the slider calls */
			if ( $this->set['layout'] == 'slider' ) {

				$js .= 'jQuery(window).load(function(){';

					$js .= '
						var i = 1;

						$(".slider-loading").remove();

						$(".hwr-album").each( function() {

						thumb_count = $(".pager-wrap").data("thumb-count");
						thumb_count_set = ' . $this->set['slider-thumb-count'] . ';

						pager_count = thumb_count < thumb_count_set ? thumb_count : thumb_count_set;

						pager_width = $(".carousel-item.pager").width();

					';

				if ( $this->set['slider-pager'] && $this->set['slider-enable-pager-thumbs'] && $this->set['slider-pager-show-all'] == false )
						$js .= '
							$("#carousel-' . $this->block['id'] . '-" + i).flexslider({
								namespace: "hwr-",
								directionNav: ' . $pager_direction_nav . ',
								animation: "slide",
								controlNav: false,
								animationLoop: ' . $animation_loop . ',
								slideshow: false,
								itemWidth: pager_width / pager_count,
								itemMargin: 0,
								maxItems: ' . $this->set['slider-thumb-count'] . ',
								asNavFor: "#slider-' . $this->block['id'] . '-" + i
							});
						';

				if ( $this->set['layout'] == 'slider' )
						$js .= '
							$("#slider-' . $this->block['id'] . '-" + i).flexslider({
								namespace: "hwr-",
								animation: "' . $this->set['slider-effect'] . '",
								easing: "' . $this->set['slider-easing'] .'",
								useCSS: false,
								direction: "' . $this->set['slider-direction'] .'",
								animationSpeed: ' . $this->set['slider-speed'] .',
								animationLoop: ' . $animation_loop . ',
								slideshow: ' . $slideshow .',
								slideshowSpeed: ' . $this->set['slider-slideshow-speed'] .',
								directionNav: ' . $direction_nav . ',
								controlNav: ' . $control_nav . ',
								smoothHeight: ' . $animate_height . ',
								manualControls: $("#pager-' . $this->block['id'] . '-" + i + " li, #pager-' . $this->block['id'] . '-" + i + " div"),
								' . $sync . '
							});
						';

					$js .= '
						i++;

						});
					';

				$js .= '});';

			}

		$js .= '})(jQuery);';


		if ( $this->is_visual_editor() )
			return;

		return $this->compress($js);

	}


	public static function page_info($block) {

		global $post;

		$block_data = HeadwayBlocksData::get_block($block);

		if ( version_compare('3.7', HEADWAY_VERSION, '<=') )
			$page_infos  = explode('||', $block_data['layout']);
		else
			$page_infos  = explode('-', $block_data['layout']);

		$page_layout = isset($page_infos[0]) ? $page_infos[0] : '' ;
		$post_type   = isset($page_infos[1]) ? $page_infos[1] : '' ;
		$page_id     = isset($page_infos[2]) ? $page_infos[2] : '' ;

		/* this is a fixed for the toolkit shortcode to call album using the shortcode param */
		if ( isset($post) ) {

			$post_type = $post->post_type ;
			$page_id   = $post->ID ;

		}

		return array(
			'page-layout' => $page_layout,
			'page-type' => $post_type,
			'page-id' => $page_id
		);

	}


	function view() {

		if ( $this->page_info['page-type'] == 'hwr_gallery' && !is_archive() )
			return 'album';

		elseif ( $this->page_info['page-type'] == 'attachment' )
			return 'media';

		else
			return $this->set['view'];

	}


	function cond_set() {

		$overlay_content = $this->set['overlay-content'];
		$overlay_effect = $this->set['overlay-effect'];

		if ( $this->set['layout'] == 'slider' ) {

			if ( $this->set['overlay-content'] == 'image' )
				$overlay_content = array('image');

			if ( $this->set['overlay-effect'] != 'top' || $this->set['overlay-effect'] != 'bottom' )
				$overlay_effect = 'bottom';

		} else if ( $this->set['overlay-content'] == 'image' ) {

			if ( $this->set['overlay-effect'] != 'fade' )
				$overlay_effect = 'fade';

		}

		return array(
			'overlay-content' => $overlay_content,
			'overlay-effect' => $overlay_effect
		);

	}


	function render($template, $params, $once = false) {

		extract($params);
		ob_start();

			if ( $once )
				include_once($template);

			else
				include($template);


	    return ob_get_clean();

	}


	function compress($buffer) {

		/* remove comments */
		$buffer = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $buffer);

		/* remove tabs, spaces, newlines, etc. */
		$buffer = preg_replace("/\s\s+/", "", $buffer);

		/* remove other spaces before/after ) */
		$buffer = preg_replace(array('(( )+\))','(\)( )+)'), ')', $buffer);

		return $buffer;

	}


	function is_visual_editor() {

		if ( headway_get('visual-editor-open') || HeadwayRoute::is_visual_editor_iframe() || headway_post('mode') == 'design' )
			return true;

		return false;

	}

	function more_link() {

		global $post;

		/* we set the more url */
		$get_more_link_url = get_post_meta( $post->ID, 'hwr_gallery_readon_link', true );

		if ( $this->page_info['page-type'] == 'hwr_gallery' && empty($get_more_link_url) )
			return false;

		return true;

	}


	function link($title, $content, $this_image = '', $custom_attr = '') {

		global $post;

		/* we set the more url */
		$get_meta_url = get_post_meta( $post->ID, 'hwr_gallery_readon_link', true );
		$get_url = get_permalink();

		/* we set the image link */
		if ( !empty($this_image) && $this->view() == 'album' ) {

			$get_meta_url = get_post_meta( $this_image['id'], '_hwr_custom_link', true );
			$get_url = add_query_arg(array('uploaded_to' => $post->ID), get_attachment_link($this_image['id']));

		}

		$auto_link = false;
		$custom_link = false;

		if ( !empty($this->set['link-behaviour']) ) {

			$auto_link = in_array('auto', $this->set['link-behaviour']);
			$custom_link = in_array('custom', $this->set['link-behaviour']);

		}

		if ( $auto_link && $custom_link )
			$link_url = $get_meta_url ? $get_meta_url : $get_url;

		elseif ( $auto_link )
			$link_url = $get_url;

		elseif ( $custom_link && $get_meta_url )
			$link_url = $get_meta_url;

		else
			return $content;

		$link_target = $this->set['link-target'] ? ' target="_blank" ' : '';

		return '<a href="' . $link_url . '" ' . $custom_attr . ' ' . $link_target . ' ' . $this->display_tags('link', 'title', $title) . '>' . $content . '</a>';

	}


	function fetch_content($content) {

		global $preview, $post;

		/* we build the output */
		$output = '';

		/* we check if the content has readmore */
		if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) && $this->more_link() ) {

			$content = explode($matches[0], $content, 2);

			$output = force_balance_tags($content[0]) . '<p class="readon-link">' . $this->link($this->set['readon-text'], $this->set['readon-text']) . '</p>';

		} else {

			$output = $content;

		}

		/* preview fix for javascript bug with foreign languages */
		if ( $preview )
			$output = preg_replace_callback('/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output);

		return $output;

	}


	function gallery_entry($post_id = '', $attachment = false) {

		global $post;

		if ( !empty($post_id) )
			$post = get_post($post_id);

		/* we get the featured image datas and build an array with it */
		$featured_img_entries = array();

		$featured_img_id = get_post_thumbnail_id( $post->ID );
		$get_featured_img = get_post( $featured_img_id );
		$featured_img_src = wp_get_attachment_image_src( $featured_img_id, 'full');
		$featured_img_alt = get_post_meta( $featured_img_id, '_wp_attachment_image_alt', true );
		$featured_img_title = $get_featured_img->post_title;
		$featured_img_title = preg_replace('/[^a-zA-Z0-9]/', ' ', ucwords(strtolower($featured_img_title)));
		$featured_img_caption = $get_featured_img->post_excerpt;
		$featured_img_description =  $get_featured_img->post_content;

		$featured_img_entries[] = array(
			'id' => $featured_img_id,
			'url' => $featured_img_src[0],
			'width' => $featured_img_src[1],
			'height' => $featured_img_src[2],
			'alt' => $featured_img_alt,
			'title' => $featured_img_alt,
			'caption' => $featured_img_caption,
			'description' => $featured_img_description
		);

		/* we get the gallery image */
		$album_img = $attachment ? array(0 => $post_id) : get_post_meta( $post->ID, 'hwr_gallery_image', true );

		/* we get the album images datas and build an array with it */
		$album_img_entries = array();

		if ( !empty($album_img) ) {

			foreach ( $album_img as $i => $album_img_id ) {

				$get_album_img = get_post( $album_img_id );
				$album_img_src = wp_get_attachment_image_src( $album_img_id, 'full' );

				if ( $album_img_src ) {
					$album_img_alt = get_post_meta( $album_img_id, '_wp_attachment_image_alt', true );
					$album_img_title = str_replace('"', "&quot;", $get_album_img->post_title);

					/* we prettify the title if enabled */
					if ( $this->set['img-title-prettifier'] )
						$album_img_title = preg_replace('/[-.!]/', ' ', ucwords(strtolower($album_img_title)));

					$album_img_caption = $get_album_img->post_excerpt;
					$album_img_description = $get_album_img->post_content;

					$album_img_entries[] = array(
						'id' => $album_img_id,
						'url' => $album_img_src[0],
						'width' => $album_img_src[1],
						'height' => $album_img_src[2],
						'alt' => $album_img_alt,
						'title' => $album_img_title,
						'caption' => $album_img_caption,
						'description' => $album_img_description,
					);

				}

			}

		}

		/* we modify the album img entries if a limit is set */
		if ( $this->set['enable-limit-images'] )
			$album_img_entries = array_slice($album_img_entries, 0 , $this->set['nbr-images'] );

		/* we order the images */
		if ( $this->set['enable-ordering'] && $this->set['order-images-by'] != 'entry' ) {

			if ( $this->set['order-images-by'] == 'rand' )
				shuffle($album_img_entries);
			else
				$album_img_entries = $this->msort($album_img_entries, $this->set['order-images-by'], $this->set['order-images']);

		}

		/* we build the post entries array */
		$entries = array(
			'post-id' => $post->ID,
			'title' => get_the_title(),
			'caption' => esc_html( get_post_meta( $post->ID, 'hwr_gallery_caption', true ) ),
			'description' => get_post_meta( $post->ID, 'hwr_gallery_description', true ),
			'featured-image' => $featured_img_entries,
			'album-images' => $album_img_entries
		);

		return $entries;

	}


	function msort($array, $sort_args, $order = 'desc', $sort_flags = SORT_REGULAR) {

        $mapping = array();

        foreach ( $array as $key => $value ) {

            $sort_key = '';

            if ( !is_array($sort_args) ) {

                $sort_key = $value[$sort_args];

            } else {

                foreach ($sort_args as $sort_args)
                    $sort_key .= $value[$sort_args];

            	$sort_flags = SORT_STRING;

            }

            $mapping[$key] = $sort_key;
        }

        if ( $order == 'asc' )
        	asort($mapping, $sort_flags);
        else
        	arsort($mapping, $sort_flags);


        $sorted = array();

        foreach ($mapping as $key => $vvalue)
            $sorted[] = $array[$key];

        return $sorted;

	}


	function gallery_effect($post_id = '', $image_title) {

		$overlay = $group = $lightbox = $lightbox_dim = '';

		$image_title = $this->set['lightbox-show-title'] ? 'title: ' . $image_title . ';' : '';

		/* we set the overlay */
		if ( $this->set['enable-overlay'] )
			$overlay = 'data-overlayer="
				effect: ' . $this->cond_set['overlay-effect'] . ';
				duration: ' . $this->set['overlay-speed'] . ';
				easing: ' . $this->set['overlay-easing'] . ';
				invert: ' . $this->set['overlay-invert'] . '
			"';

		/* we set the lightbox */
		if ( $this->set['lightbox-enable-resize'] )
			$lightbox_dim = '
				width: ' . $this->set['lightbox-width'] . 'px;
				height: ' . $this->set['lightbox-height'] . 'px';

		if ( $this->set['lightbox-enable-loop'] )
			$group = 'group: album ' . $this->block['id'] . $post_id . ';';

		if ( $this->set['enable-lightbox'] )
			$lightbox = 'data-lightbox="
				' . $group . '
				padding: 3px;
				titlePosition: ' . $this->set['lightbox-title-position'] . ';
				transitionIn: ' . $this->set['lightbox-open-effect'] . ';
				transitionOut: ' . $this->set['lightbox-close-effect'] . ';
				easingIn: ' . $this->set['lightbox-easingin'] . ';
				easingOut: ' . $this->set['lightbox-easingout'] . ';
				' . $image_title . $lightbox_dim . '
			"';

		return array(
			'overlay' => preg_replace("/\s\s+/", "", $overlay),
			'lightbox' => preg_replace("/\s\s+/", "", $lightbox)
		);

	}


	function set_columns() {

		$page_info = $this->page_info;

		if ( $this->set['layout'] == 'slider' || $page_info['page-type'] == 'attachment' )
			return 1;

		else
			return $this->set['grid-col'];

	}


	function wp_query() {

		/* we set wp content */
		global $post;

		$query_options = array();
		$query_options['post_type'] = 'hwr_gallery';

		if ( $this->page_info['page-type'] == 'hwr_gallery' ) {

			/* if $this->page_info['page-id'] is not set, it means we are on the gallery post type page. When then display a notice informing the users that they are setting the default style of all the children. */
			if ( empty($this->page_info['page-id']) && $this->is_visual_editor() ) {

				$query_options['posts_per_page'] = 1;

				echo $this->notice['single-view'];

			} elseif ( !empty($this->page_info['page-id']) && $this->is_visual_editor() ) {

				$query_options['page_id'] = $this->page_info['page-id'];

				echo $this->notice['single-view-item'];

			} else if ( !is_archive() ) {

				$query_options['page_id'] = $this->page_info['page-id'] != '' ? $this->page_info['page-id'] : $post->ID;

			}

		} elseif ( $this->page_info['page-type'] == 'attachment' ) {

			/* if $this->page_info['page-id'] is not set, it means we are on the gallery post type page. When then display a notice informing the users that they are setting the default style of all the children. */
			if ( empty($this->page_info['page-id']) && $this->is_visual_editor() ) {

				$query_options['posts_per_page'] = 1;

				echo $this->notice['media-view'];

			}

		} else {

			if ( $this->set['enable-ordering'] ) {

				$query_options['orderby'] = $this->set['order-by'];
				$query_options['order']   = $this->set['order'];

			}

			if ($this->set['enable-limit-albums'])
				$query_options['posts_per_page'] = $this->set['nbr-albums'];

			else
				$query_options['posts_per_page'] = -1;

			if ( $this->set['enable-filters'] ) {

				/* we build the taxonomy categories */
				$taxonomy_categories = $this->set['filters-categories'] ? array() : '';

				if ( $this->set['filters-enable-categories'] && $this->set['filters-categories'] ) {

					$taxonomy_categories['taxonomy'] = 'gallery_categories';
					$taxonomy_categories['field'] = 'id';
					$taxonomy_categories['terms'] = $this->set['filters-categories'];

					if ( $this->set['filters-categories-mode'] == 'exclude' )
						$taxonomy_categories['operator'] = 'NOT IN';

					$query_options['tax_query'] = array(
						'relation' => 'AND',
						$taxonomy_categories,

					);
				}

				/* we build the taxonomy tags */
				$taxonomy_tags = $this->set['filters-tags'] ? array() : '';

				if ( $this->set['filters-enable-tags'] && $this->set['filters-tags'] ) {

					$taxonomy_tags['taxonomy'] = 'gallery_tags';
					$taxonomy_tags['field'] = 'id';
					$taxonomy_tags['terms'] = $this->set['filters-tags'];

					if ( $this->set['filters-tags-mode'] == 'exclude' )
						$taxonomy_tags['operator'] = 'NOT IN';

					$query_options['tax_query'] = array(
						'relation' => 'AND',
						$taxonomy_tags

					);

				}

				if ( $this->set['filters-enable-wp-items'] && $this->set['filters-wp-items'] ) {

					if ( $this->set['filters-wp-items-mode'] == 'include' )
						$query_options['post__in'] = $this->set['filters-wp-items'];

					if ( $this->set['filters-wp-items-mode'] == 'exclude' )
						$query_options['post__not_in'] = $this->set['filters-wp-items'];

				}

			}

		}

		$this->query = new WP_Query($query_options);



	}


	function notices($entries = array()) {

		$album_title = '';
		$open        = '<div class="alert alert-yellow clear"><p>';
		$close       = '</p></div>';

		if ( !empty($entries) )
			 $album_title = $entries['title'];

		elseif ( !empty($this->page_info['page-id']) )
			 $album_title = get_the_title($this->page_info['page-id']);

		return array(
			'no-album' => $open . 'You do not have any album set at the moment, please add at least one Album in WordPress (via the Gallery post type).' . $close,
			'no-image' => $open . 'You do not have any image set for the album <strong>"' . $album_title . '"</strong> at the moment, please add at least one image in this album.' . $close,
			'single-view' => $open . 'Since you are on the Gallery post type, all the children of this post type will be displayed as single view if left empty. Therefore, the albums view and filters are disabled. The first post is displayed below this notice as an example.' . $close,
			'single-view-item' => $open . 'Since you are on <strong>"' . $album_title . '"</strong> page, only the <strong>"' . $album_title . '"</strong> album will be displayed. Therefore, the albums view and filters are disabled.' . $close,
			'media-view' => $open . 'Since you are on the Media post type, all the children of this post type will be displayed as single view if left empty. Therefore, only the appropriate options are available. The first post is displayed below this notice as an example.' . $close,
			'media-view-item' => $open . 'Since you are on Media post type page, the appropriate image will be displayed automatically. Therefore, only the appropriate options are available.' . $close,
			'slider-disabled' => '<div class="slider-notice"><p>Slider is disabled in Design mode. View the page to see it in action!</p></div>'
		);
	}


	function dimensions($images_count = 1) {

		if ( version_compare('3.5', HEADWAY_VERSION, '<=') ) {

			if ( version_compare('3.7', HEADWAY_VERSION, '<=') )
				$block_wrapper = HeadwayWrappersData::get_wrapper(headway_get('wrapper', $this->block));
			else
				$block_wrapper = HeadwayWrappers::get_wrapper(headway_get('wrapper', $this->block));

			$block_width = headway_get('fluid-grid', $block_wrapper) ? 2000 : HeadwayBlocksData::get_block_width($this->block);

		} else {

			$block_width = HeadwayBlocksData::get_block_width($this->block);

		}

		/* we calculate the columns width */
		$col_width_pct = (100 / $this->columns - $this->set['grid-col-spacing']) + ($this->set['grid-col-spacing'] / $this->columns);
		$col_width_px  = ($block_width * $col_width_pct) / 100;

		/* we set the thumb pager dimensions */
		$thumbs_pager_count      = $this->set['slider-pager-show-all'] ? $images_count : $this->set['slider-thumb-count'];
		$pager_width             = $block_width - $this->set['slider-pager-spacing'];
		$pager_col_width         = $pager_width / $thumbs_pager_count;
		$pager_thumb_spacing_pct = ($this->set['slider-thumb-spacing'] * 100) / $pager_width;
		$pager_col_width_pct 	 = (100 / $thumbs_pager_count - ($pager_thumb_spacing_pct * 2)) + (($pager_thumb_spacing_pct * 2) / $thumbs_pager_count);
		$pager_thumb_width       = $pager_col_width - $this->set['slider-thumb-spacing'];
		$pager_thumb_height      = $pager_thumb_width - (($pager_thumb_width * 10) / 100);
		$pager_force_center      = false;

		/* we set the pager with if the number of thumbs are less than what is set to be displayed */
		if ( $images_count <= $this->set['slider-thumb-count'] ) {

			$thumbs_pager_count = $images_count;
			$pager_force_center = true;

		}

		$pager_width = ceil(($thumbs_pager_count * $pager_col_width) + $this->set['slider-pager-spacing'] - $this->set['slider-pager-spacing']) . 'px';

		/* we crop the pager images if set */
		if ( $this->set['slider-enable-thumb-crop-height'] && $this->set['slider-thumb-crop-height'] < $pager_thumb_height )
			$pager_thumb_height = $this->set['slider-thumb-crop-height'];

		return array(
			'col-width-pct' => $col_width_px,
			'image-width' => ceil($col_width_px),
			'pager-width' => $pager_width,
			'pager-force-center' => $pager_force_center,
			'pager-col-width' => $pager_col_width,
			'pager-col-width-pct' => $pager_col_width_pct,
			'pager-thumb-width' => $pager_thumb_width,
			'pager-thumb-height' => $pager_thumb_height,
			'pager-thumb-spacing' => $pager_thumb_spacing_pct,
		);

	}


	function display_tags($markup, $tag, $content, $lightbox = false) {

		if ( $tag == 'title' && $this->set['img-show-title-tag'] == false )
			return null;

		elseif ( $this->set[$markup . '-show-' . $tag . '-tag'] )
			return $tag . '="' . $content . '"';

		return null;

	}


	function display_metas($this_image, $meta_count = '') {

		$description = $overlay = $overlay_content = $title_above_image = $title_below_image = "";

		/* we set title html */
		if ( $this->set['img-show-title'] && $this_image['title']) {

			if ( !empty($meta_count) )
				$meta_count = '<span class="album-count">' . $meta_count . $this->set['img-title-count-text'] . '</span>';

			$title = '<' . $this->set['img-title-type'] . ' class="image-title ' . $this->set['img-title-position'] . '">' . $this_image['title']  . $meta_count . '</' . $this->set['img-title-type'] . '>';

			if ( $this->set['enable-links'] && $this->set['enable-title-link'] )
				$title = $this->link($this_image['title'], $title, $this_image);

			$title_above_image = $this->set['img-title-position'] == 'above-image' ? $title : '';
			$title_below_image = $this->set['img-title-position'] == 'below-image' ? $title : '';

		}

		/* we set description html */
		if ( $this->set['img-show-description'] && $this_image['description'] )
			$description = '<div class="image-description">' . $this->fetch_content($this_image['description']) . '</div>';



		/* we set the overlay html */
		if ( $this->set['enable-overlay'] && !empty($this->cond_set['overlay-content']) ) {

			$overlay_img_class = in_array('image', $this->cond_set['overlay-content']) ? 'overlay-image' : '';

			/* we prepare the caption content */
			if ( in_array('title', $this->cond_set['overlay-content']) )
				$overlay_content .= '<' . $this->set['img-title-type'] . ' class="overlay-title">' . $this_image['title']  . '</' . $this->set['img-title-type'] . '>';

			if ( in_array('caption', $this->cond_set['overlay-content']) && !empty($this_image['caption']) )
				$overlay_content .= '<div class="overlay-caption">' . $this_image['caption'] . '</div>';


			/* we build the output */
			$overlay = '<div class="overlay overlay-wrap ' . $overlay_img_class . '">';

				$overlay .= $overlay_content;

			$overlay .= '</div>';

			if ( empty($overlay_content) && empty($overlay_img_class) ) {

				$overlay = '<div class="overlay overlay-wrap no-caption"></div>';

			}

		}

		return array(
			'title-above-image' => $title_above_image,
			'title-below-image' => $title_below_image,
			'description' => $description,
			'overlay' => $overlay
		);

	}


	function display_image($this_image, $overlay = '', $enable_crop, $auto_thumb = false, $auto_crop = '', $manuel_crop = '', $animate_height = '', $featured_image_url = '' ) {

		global $post;

		/* we get the block dimensions */
		$block_dim = $this->dimensions();

		/* we get the gallery effects */
		$effects = $this->gallery_effect($post->ID, $this_image['title']);

		if ( $enable_crop == false ) {

			$image_height = $this_image['height'];
			$max_height = ceil($image_height) . 'px';

		} elseif ( $animate_height ) {

			$image_height = $this_image['height'];
			$max_height = 'auto';

		} elseif ( $auto_thumb ) {

			$image_height = $block_dim['image-width'] - (($block_dim['image-width'] * 10) / 100);
			$max_height = ceil($image_height) . 'px';

		} elseif ( !empty($manuel_crop) ) {

			$image_height = $manuel_crop;
			$max_height = ceil($image_height) . 'px';

		} elseif ( !empty($auto_crop) ) {

			$image_height = $auto_crop;
			$max_height = ceil($image_height) . 'px';

		}

		$image_width = $this->set['img-enable-crop-width'] ? $block_dim['image-width'] : $this_image['width'];

		$image_url = empty($featured_image_url) ? $this_image['url'] : $featured_image_url;

		$resized_url = headway_resize_image($image_url, $image_width, $image_height, true);

		if ( $this->view() == 'media' ) {

			$resized_url = $image_url;
			$max_height = 'auto';

		}

		$get_meta_url = get_post_meta( $this_image['id'], '_hwr_custom_link', true );

		$lighbox_url = $this->set['lightbox-enable-resize'] ? headway_resize_image($this_image['url'], $this->set['lightbox-width'], $this->set['lightbox-height'], true) : $this_image['url'];

		if ( !empty($get_meta_url) && $this->set['enable-lightbox'] && $this->set['enable-image-link'] )
			$lighbox_url = $get_meta_url;

		/* we define if the image is wrapped in a link */
		$image_link = $this->set['enable-lightbox'] == true || $this->set['enable-image-link'] ? true : false;

		/* we set where the overlay effect should be */
		$overlay_on_wrap = $image_link ? '' : $effects['overlay'];
		$overlay_on_link = $image_link ? $effects['overlay'] : '';

		$image = '<img style="max-height: ' . $max_height . ';" src="' . $resized_url  . '" alt="' . $this_image['alt']  . '" ' . $this->display_tags('img', 'title', $this_image['title'], false) . ' />' . $overlay ;

		/* we build the output */
		echo '<div ' . $overlay_on_wrap . ' class="image-wrap">';

			if ( $this->set['enable-lightbox'] )
				echo '<a ' . $overlay_on_link . ' ' . $effects['lightbox'] . ' href="' . $lighbox_url . '" ' . $this->display_tags('link', 'title', $this_image['title'], true) . '>' . $image;

			elseif ( $this->set['enable-image-link'] && $this->page_info['page-type'] != 'attachment' )
				echo $this->link($this_image['title'], $image, $this_image, $overlay_on_link);

			else
				echo $image;

			if ( $this->set['enable-lightbox'] )
				echo '</a>';

		echo '</div>';

	}


	function display_pager_image($this_thumb, $image_count = '') {

		/* we get the block dimensions */
		$block_dim = $this->dimensions($image_count);

		/* we resize the album thumb */
		$resized_url = headway_resize_image($this_thumb['url'], ceil($block_dim['pager-thumb-width']), ceil($block_dim['pager-thumb-height']), true);

		return '<img class="thumb-image" src="' . $resized_url  . '" alt="' . $this_thumb['alt']  . '" ' . $this->display_tags('img', 'title', $this_thumb['title'], false) . ' />';

	}


	function display_row($i, $image_count) {

		$row = ceil($i / $this->columns);

		if ( $image_count <= $this->columns )
			return null;

		/* add the row and class */
		switch($i % $this->columns) {

			case $this->columns == 1 ? 0 : 1:
				if ( $i == 0 ) {
					echo '<div class="hwr-row hwr-row1 odd first clear">';
				} elseif ( $i == 1 ) {
					echo '<div class="hwr-row hwr-row1 odd first clear">';
				} else {
					$columns_alt_class = $row % 2 == 0 ? ' even' : ' odd';
					echo '</div><!-- row close --><div class="hwr-row hwr-row' . $row . $columns_alt_class . ' clear">';
				}
			break;

		}

	}


	function display_gallery() {

		global $post;

		$temp_post = $post;

		/* we initiate the query */
		$this->wp_query();

		echo '<div class="hwr-grid hwr-cols' . $this->columns . ' ' . $this->view() . '-view"><!-- grid open -->';

			if ( $this->page_info['page-type'] == 'attachment' )
				echo $this->render('views/media.php', array('block' => $this->block));

			elseif ( $this->page_info['page-type'] == 'hwr_gallery' && !is_archive() )
				echo $this->render('views/album.php', array('block' => $this->block));

			else
				echo $this->render('views/' . $this->view() . '.php', array('block' => $this->block));

		echo '</div><!-- grid close -->';

		/* we display a notice of there isn't any albums puplish */
		$count_posts = wp_count_posts( 'hwr_gallery' );
		$published_posts = $count_posts->publish;

		if ( $published_posts == 0 )
			echo $this->notice['no-album'];

		/* reset the global $post */
		$post = $temp_post;

		/* reset query to avoid conflict */
		wp_reset_query();

	}

}