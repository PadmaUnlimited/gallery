<?php

class PadmaGalleryBlockStyling {

	public static function hooks() {
	
		if ( version_compare(PADMA_VERSION, '3.6', '<') ) {
						
			return padma_gallery_depreciate_hooks();
		
		}
			
		return array(
			/* all views */
			array(
				'id' => 'all-views',
				'name' => 'All Views',
				'selector' => '.block-type-padma-gallery',
				'properties' => array('')
			),
				array(
					'id' => 'block-container',
					'name' => 'Container',
					'selector' => '.block-type-padma-gallery .padma-gallery',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow'),
					'parent' => 'all-views'
				),
				array(
					'id' => 'block-before-',
					'name' => 'Before Block',
					'selector' => '.block-type-padma-gallery .padma-gallery .pur-block-before',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow'),
					'parent' => 'all-views'
				),
				array(
					'id' => 'block-title',
					'name' => 'Title',
					'selector' => '.block-type-padma-gallery .padma-gallery .pur-block-title',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow'),
					'parent' => 'all-views'
				),
					array(
						'id' => 'block-title-alt',
						'name' => 'Alt',
						'selector' => '.block-type-padma-gallery .padma-gallery .pur-block-title span',
						'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow'),
						'parent' => 'block-title'
					),
				array(
					'id' => 'block-content',
					'name' => 'Description',
					'selector' => '.block-type-padma-gallery .padma-gallery .pur-block-content',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow'),
					'parent' => 'all-views'
				),
				array(
					'id' => 'block-footer',
					'name' => 'Footer',
					'selector' => '.block-type-padma-gallery .padma-gallery .pur-block-footer',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow'),
					'parent' => 'all-views'
				),
				array(
					'id' => 'block-after',
					'name' => 'After Block',
					'selector' => '.block-type-padma-gallery .padma-gallery .pur-block-after',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow'),
					'parent' => 'all-views'
				),
				array(
					'id' => 'items-container',
					'name' => 'Items Container',
					'selector' => '.block-type-padma-gallery .padma-gallery .pur-album',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow'),
					'parent' => 'all-views'
				),
				array(
					'id' => 'readon-link',
					'name' => 'Readon Link',
					'selector' => '.block-type-padma-gallery .padma-gallery .readon-link a',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow'),
					'states' => array(
						'hover' => '.block-type-padma-gallery .padma-gallery .readon-link a:hover', 
						'active' => '.block-type-padma-gallery .padma-gallery .readon-link a:active'
					),
					'parent' => 'all-views'
				),
				array(
					'id' => 'image-container',
					'name' => 'Image',
					'selector' => '.block-type-padma-gallery .padma-gallery .item, .block-type-padma-gallery .padma-gallery .slider-item',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'padding', 'nudging', 'overflow', 'text-shadow'),
					'parent' => 'all-views'
				),
					array(
						'id' => 'image-wrap',
						'name' => 'Wrap',
						'selector' => '.block-type-padma-gallery .padma-gallery .image-wrap',
						'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'padding', 'nudging', 'overflow', 'text-shadow'),
						'parent' => 'image-container'
					),
				
					array(
						'id' => 'image-title',
						'name' => 'Title',
						'selector' => '.block-type-padma-gallery .padma-gallery .image-title',
						'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'text-shadow'),
						'parent' => 'image-container'
					),
						array(
							'id' => 'image-title-count',
							'name' => 'Count',
							'selector' => '.block-type-padma-gallery .padma-gallery .image-title .album-count',
							'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'text-shadow'),
							'parent' => 'image-title'
						),
					array(
						'id' => 'image-description',
						'name' => 'Description',
						'selector' => '.block-type-padma-gallery .padma-gallery .image-description',
						'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow'),
						'parent' => 'image-container'
					),
				array(
					'id' => 'overlay',
					'name' => 'Overlay',
					'selector' => '.block-type-padma-gallery .padma-gallery [class^="overlay"]',
					'properties' => array(''),
					'parent' => 'all-views'
				),
					array(
						'id' => 'overlay-container',
						'name' => 'Container',
						'selector' => '.block-type-padma-gallery .padma-gallery .overlay-wrap',
						'properties' => array('background', 'borders', 'rounded-corners', 'box-shadow'),
						'parent' => 'overlay'
					),
					array(
						'id' => 'overlay-title',
						'name' => 'Title',
						'selector' => '.block-type-padma-gallery .padma-gallery .overlay-title',
						'properties' => array('fonts', 'padding', 'text-shadow'),
						'parent' => 'overlay'
					),
					array(
						'id' => 'overlay-caption',
						'name' => 'Caption',
						'selector' => '.block-type-padma-gallery .padma-gallery .overlay-caption',
						'properties' => array('fonts', 'padding', 'text-shadow'),
						'parent' => 'overlay'
					),
					array(
						'id' => 'overlay-image',
						'name' => 'Image',
						'selector' => '.block-type-padma-gallery .padma-gallery .overlay-image',
						'properties' => array('background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging'),
						'parent' => 'overlay'
					),
			/* album view */
			array(
				'id' => 'album-view',
				'name' => 'Album View',
				'selector' => '.block-type-padma-gallery .padma-gallery [class^="album"]',
				'properties' => array('')
			),
				array(
					'id' => 'album-content-wrap',
					'name' => 'Album Content',
					'selector' => '.block-type-padma-gallery .padma-gallery .album-content-wrap',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow'),
					'parent' => 'album-view'
				),
				array(
					'id' => 'album-title',
					'name' => 'Album Title',
					'selector' => '.block-type-padma-gallery .padma-gallery .album-title',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow'),
					'parent' => 'album-view'
				),
				array(
					'id' => 'album-description',
					'name' => 'Album Description',
					'selector' => '.block-type-padma-gallery .padma-gallery .album-description',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow'),
					'parent' => 'album-view'
				),
				array(
					'id' => 'slider',
					'name' => 'Slider Layout',
					'selector' => '.block-type-padma-gallery .padma-gallery [class^="pager"]',
					'properties' => array(''),
					'parent' => 'album-view'
				),
					array(
						'id' => 'pagination-container',
						'name' => 'Pagination Container',
						'selector' => '.block-type-padma-gallery .padma-gallery .pager',
						'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow'),
						'parent' => 'slider'
					),
					array(
						'id' => 'pagination-thumb',
						'name' => 'Pagination Thumbnails',
						'selector' => '.block-type-padma-gallery .padma-gallery .pager-item',
						'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'padding', 'nudging', 'overflow', 'text-shadow'),
						'states' => array(
							'hover' => '.block-type-padma-gallery .padma-gallery .pager-item:hover', 
							'active' => '.block-type-padma-gallery .padma-gallery .pur-active-slide .pager-item, .block-type-padma-gallery .padma-gallery .pager-item.pur-active'
						),
						'parent' => 'slider'
					),
			/* media view */
			array(
				'id' => 'media-view',
				'name' => 'Media View',
				'selector' => '.block-type-padma-gallery .padma-gallery .media-view',
				'properties' => array('')
			),
				array(
					'id' => 'media-image-title',
					'name' => 'Image Title',
					'selector' => '.block-type-padma-gallery .padma-gallery .media-view .image-title',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'text-shadow'),
					'parent' => 'media-view'
				),
				array(
					'id' => 'media-image-description',
					'name' => 'Image Description',
					'selector' => '.block-type-padma-gallery .padma-gallery .media-view .image-description',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'text-shadow'),
					'parent' => 'media-view'
				),
				array(
					'id' => 'image-nav-btn',
					'name' => 'Next &amp; Previous',
					'selector' => '.block-type-padma-gallery .padma-gallery .image-nav a',
					'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow'),
					'states' => array(
						'hover' => '.block-type-padma-gallery .padma-gallery .image-nav a:hover', 
						'active' => '.block-type-padma-gallery .padma-gallery .image-nav a:active'
					),
					'parent' => 'media-view'
				)
		);
	
	}
	
	
	public static function defaults() {
		
		return array(
			'block-padma-gallery-block-container' => array(
				'properties' => array(
					'padding-top' => '20',
					'padding-right' => '0',
					'padding-bottom' => '50',
					'padding-left' => '0'
				)
			),
			'block-padma-gallery-block-title' => array(
				'properties' => array(
					'margin-top' => '0',
					'margin-bottom' => '10',
					'font-size' => '50',
					'color' => '555555'
				)
			),
			'block-padma-gallery-block-title-alt' => array(
				'properties' => array(
					'color' => '666666'
				)
			),
			'block-padma-gallery-block-content' => array(
				'properties' => array(
					'margin-bottom' => '20',
					'font-size' => '19',
					'color' => '777777',
					'line-height' => '160',
				)
			),
			'block-padma-gallery-items-container' => array(
				'properties' => array(
					'margin-bottom' => '20',
				)
			),
			'block-padma-gallery-album-title' => array(
				'properties' => array(
					'margin-bottom' => '10',
					'font-size' => '30',
					'line-height' =>' 100',
					'font-styling' => 'normal'
				)
			),
			'block-padma-gallery-album-description' => array(
				'properties' => array(
					'font-size' => '15'
				)
			),
			'block-padma-gallery-pagination-container' => array(
				'properties' => array(
					'margin-top' => '20',
					'margin-bottom' => '3',
					'padding-right' => '40',
					'padding-left' => '40',
				)
			),
			'block-padma-gallery-pagination-thumb' => array(
				'properties' => array(
					'padding-top' => '1',
					'padding-right' => '1',
					'padding-bottom' => '1',
					'padding-left' => '1',
					'border-color' => '#d4d4d4',
					'border-style' => 'solid',
					'border-top-width' => '1',
					'border-right-width' => '1',
					'border-bottom-width' => '1',
					'border-left-width' => '1'
				),
				'special-element-state' => array(
					'hover' => array(
						'border-color' => '#aaaaaa',
						'box-shadow-color' => '#bbbbbb',
						'box-shadow-horizontal-offset' => '0',
						'box-shadow-vertical-offset' => '0',
						'box-shadow-blur' => '4'
					),
					'active' => array(
						'border-color' => '#aaaaaa',
						'box-shadow-color' => '#bbbbbb',
						'box-shadow-horizontal-offset' => '0',
						'box-shadow-vertical-offset' => '0',
						'box-shadow-blur' => '4'
					)
				)
			),
			'block-padma-gallery-image-title' => array(
				'properties' => array(
					'background-color' => '#eeeeee',
					'margin-top' => '1',
					'margin-right' => '0',
					'margin-bottom' => '5',
					'margin-left' => '0',
					'margin-bottom' => '10',
					'padding-top' => '9',
					'padding-right' => '10',
					'padding-bottom' => '9',
					'padding-left' => '10',
					'font-size' => '14',
					'line-height' => '100',
					'font-styling' => 'normal'
				)
			),
			'block-padma-gallery-media-image-title' => array(
				'properties' => array(
					'background-color' => 'transparent',
					'margin-top' => '0',
					'margin-right' => '0',
					'margin-bottom' => '15',
					'margin-left' => '0',
					'padding-top' => '10',
					'padding-right' => '0',
					'padding-bottom' => '0',
					'padding-left' => '0',
					'font-size' => '25',
					'line-height' =>' 100',
					'font-styling' => 'normal'
				)
			),
			'block-padma-gallery-media-image-description' => array(
				'properties' => array(
					'margin-bottom' => '15',
					'line-height' => '140',
					'font-styling' => 'normal'
				)
			),
			'block-padma-gallery-image-title-count' => array(
				'properties' => array(
					'color' => '#888888',
					'margin-right' => '0',
					'margin-left' => '10',
					'font-styling' => 'normal'
				)
			),
			'block-padma-gallery-image-nav-btn' => array(
				'properties' => array(
					'background-color' => '#e6e6e6',
					'padding-top' => '4',
					'padding-right' => '8',
					'padding-bottom' => '6',
					'padding-left' => '8',
					'margin-left' => '10',
					'border-top-left-radius' => '4',
					'border-top-right-radius' => '4',
					'border-bottom-left-radius' => '4',
					'border-bottom-right-radius' => '4',
					'text-decoration' => 'none',
					'line-height' => '120'
				),
				'special-element-state' => array(
					'hover' => array(
						'background-color' => '#dddddd'
					),
					'active' => array(
						'background-color' => '#cccccc',
					)
				)
			),
			'block-padma-gallery-overlay-container' => array(
				'properties' => array(
					'background-color' => 'rgba(0,0,0,0.6)',
				)
			),
			'block-padma-gallery-overlay-title' => array(
				'properties' => array(
					'color' => '#f2f2f2',
					'padding-top' => '5',
					'padding-right' => '10',
					'padding-bottom' => '5',
					'padding-left' => '10',
					'line-height' => '140',
					'font-styling' => 'bold'
				)
			),
			'block-padma-gallery-overlay-caption' => array(
				'properties' => array(
					'color' => '#f2f2f2',
					'padding-top' => '5',
					'padding-right' => '10',
					'padding-bottom' => '5',
					'padding-left' => '10',
					'line-height' => '140'
				)
			),
			'block-padma-gallery-overlay-image' => array(
				'properties' => array(
					'background-image' => PADMA_GALLERY_URL . 'assets/images/magnet.png',
					'background-repeat' => 'no-repeat',
					'background-position' => 'center center'
				)
			),
			'block-padma-gallery-image-description' => array(
				'properties' => array(
					'margin-top' => '5',
					'margin-right' => '0',
					'margin-bottom' => '5',
					'margin-left' => '0',
					'line-height' => '140'
				)
			),
			'block-padma-gallery-readon-link' => array(
				'properties' => array(
					'background-color' => '#e6e6e6',
					'padding-top' => '4',
					'padding-right' => '8',
					'padding-bottom' => '4',
					'padding-left' => '8',
					'border-top-left-radius' => '4',
					'border-top-right-radius' => '4',
					'border-bottom-left-radius' => '4',
					'border-bottom-right-radius' => '4',
					'text-decoration' => 'none'
				),
				'special-element-state' => array(
					'hover' => array(
						'background-color' => '#dddddd'
					),
					'active' => array(
						'background-color' => '#cccccc',
					)
				)
			)
		
		);

	}

}
