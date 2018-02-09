<?php

class PadmaGalleryBlockOptions extends PadmaBlockOptionsAPI {	
	
	public $tabs 		     = array();
	public $inputs 		     = array();
	public $tab_notices      = array();
	public $open_js_callback = array();
	
	public static function easing_effect($block) {
	
		/* we build the easing array */
		$easing_group = array( 'Quad', 'Cubic', 'Quart', 'Quint', 'Sine', 'Expo', 'Circ', 'Elastic', 'Back', 'Bounce' );
		
		$easing_effect = array();
		
		$easing_effect['swing'] = 'Swing';
			
		foreach ( $easing_group as $key => $value ) {
		
			$easing_in = 'ease In ' .  $value;
			$easing_out = 'ease Out ' .  $value;
			$easing_inout = 'ease In Out ' .  $value;
			
			$easing_effect[str_replace(' ', '', $easing_in)] = ucfirst($easing_in);
			$easing_effect[str_replace(' ', '', $easing_out)] = ucfirst($easing_out);
			$easing_effect[str_replace(' ', '', $easing_inout)] = ucfirst($easing_inout);
		
		}
		
		return $easing_effect;
	
	}
	
	
	public static function get_all_inputs($block) {
	
		return array(
			'setup'    => self::setup($block),
			'media'    => self::media($block),
			'grid'     => self::grid($block),
			'slider'   => self::slider($block),
			'lightbox' => self::lightbox($block),
			'overlay'  => self::overlay($block),
			'links'    => self::links($block),
			'filters'  => self::filters($block),
			'ordering' => self::ordering($block),
			'content'  => self::content($block)
		);
		
	}
	
	
	public static function settings($block = "") {
	
		$options = self::get_all_inputs($block);
		
		foreach ( $options as $key => $value ) {
		
			foreach ( $value as $options ) {
			
				if ( !isset($options['default']) )
					$options['default'] = '';
				
				$get_hw_setting = PadmaGalleryBlock::get_setting($block, $options['name'], $options['default']);
				
				if ( $options['type'] == 'wysiwyg' )
					$settings[$options['name']] = html_entity_decode($get_hw_setting, ENT_QUOTES, 'UTF-8');
				
				else	
					$settings[$options['name']] = $get_hw_setting;
				
			}
		
		}
		
		return $settings;
		
	}
	

	function modify_arguments($args = false) {

		$block = PadmaBlocksData::get_block($args['block_id']);		
		
		/* we add the notices to each tabs */
		$inputs = self::get_all_inputs($block);
		
		foreach ( $inputs as $key => $value )
			$inputs[$key][$key . '-notice'] = self::tab_notice($key . '-notice');
			
		$this->tabs = array(
			'setup' => "Setup",
			'media' => "Media",
			'grid' => "Grid",
			'slider' => "Slider",
			'lightbox' => "Lightbox",
			'overlay' => "Overlay",
			'links' => "Links",
			'filters' => "Filters",
			'ordering' => "Ordering",
			'content' => "Content"
		);
		
		$this->inputs = $inputs;
				
		$js_callback = '
			pur.blockOptionsApi.loadStyle( "' . PADMA_GALLERY_URL . 'admin/css/admin.css" );
			pur.blockOptionsApi.loadScript( "' . PADMA_GALLERY_URL . 'admin/js/admin.js", function() {

				padma_gallery_js("' . $args['block_id'] . '");
				pur.blockOptionsApi.updateOptions("padma_gallery_js", "' . $args['block_id'] . '");

			} );
		';

		$this->open_js_callback = padma_load_block_options_assets( $args['block_id'], $js_callback );

	}
	
	
	public static function tab_notice($id) {
	
		return array(
			'name' => $id,
			'type' => 'raw-html',
			'html' => '<div class="pur-admin-notice"><a href="#" class="pur-close">x</a><p class="show-once"></p></div>'
		);
				
	}
	
	
	public static function header($id, $label) {
	
		return array(
			'name' => $id,
			'type' => 'raw-html',
			'html' => '<h3 class="pur-admin-header">' . $label . '</h3>'
		);
			
			
	}
	
	
	public static function wrapper($id, $open = false) {
	
		if ( $open )
			return array(
				'name' => 'wrap-' . $id . '-open',
				'type' => 'raw-html',
				'html' => '<div class="wrapper ' . $id . '">'
			);
		else
			return array(
				'name' => 'wrap-' . $id . '-close',
				'type' => 'raw-html',
				'html' => '</div>'
			);
			
	}
	
		
	public static function setup($block) {
	
		global $post;
		
		$page_info = PadmaGalleryBlockDisplay::page_info($block);
		
		if (  $page_info['page-type'] == 'padma_gallery' )
			$ablum_hide = array(
				'#sub-tab-media',
				'#input-readon-text', 
				'#input-enable-filters', 
				'#input-enable-ordering',
				'#input-img-enable-title-count',
				'#input-img-title-count-text'
			);
			
		else
			$ablum_hide = array(
				'#sub-tab-media',
				'#input-img-enable-title-count',
				'#input-img-title-count-text'
			);
									
		$settings = array(
			'open-wrap-view' => self::wrapper('view', true),
				'heading-view' => self::header('heading-view', 'View &amp; Layout'),
					'view' => array(
						'type' => 'select',
						'name' => 'view',
						'label' => 'View',
						'default' => 'album',
						'options' => array(
							'albums' => 'Albums',
							'album' => 'Album',
							'media' => 'Media'
						),
						'toggle' => array(
							'albums' => array(
								'hide' => array(
									'#sub-tab-media',
									'.wrapper.album-show-title'
								),
								'show' => array(
									'#input-img-enable-title-count',
									'#input-img-title-count-text'
								)
							),
							'album' => array(
								'hide' => $ablum_hide,
								'show' => array(
									'.wrapper.album-show-title'
								)
								
							),
							'media' => array(
								'show' => array(
									'#sub-tab-media',
									'.wrapper.img-show-title'
								),
								'hide' => array(
									'#sub-tab-grid',
									'#sub-tab-links',
									'#sub-tab-filters',
									'#input-enable-links',
									'#input-enable-lightbox',
									'#input-enable-filters',
									'#input-enable-ordering',
									'#input-layout',
									'#input-img-enable-title-count',
									'#input-img-title-count-text',
									'.wrapper.album-show-title',
									'.wrapper.nbr-images'
								),
								
							)
						),
						'tooltip' => 'Select whether to display a list of albums, or a specific album.'
					),
					'layout' => array(
						'type' => 'select',
						'name' => 'layout',
						'label' => 'Layout',
						'default' => 'grid',
						'options' => array(
							'grid' => 'Grid',
							'slider' => 'Slider'
						),
						'toggle' => array(
							'grid' => array(
								'show' => array(
									'#sub-tab-grid',
									'#input-enable-title-link',
									'.wrapper.img-show-title'
								),
								'hide' => array(
									'#sub-tab-slider'
								)
							),
							'slider' => array(
								'show' => array(
									'#sub-tab-slider'
								),
								'hide' => array(
									'#sub-tab-grid',
									'#input-enable-title-link',
									( $page_info['page-type'] == 'attachment' ? '' : '.wrapper.img-show-title' )
								)
							)
						),
						'tooltip' => 'Set whether the layout should be a Grid or a Slider.'
					),
			'close-wrap-view' => self::wrapper('view'),	
								
			'open-wrap-enable-overlay' => self::wrapper('enable-overlay', true),
				'heading-enable-overlay' => self::header('heading-enable-overlay', 'Features'),
					'enable-overlay' => array(
						'type' => 'checkbox',
						'name' => 'enable-overlay',
						'label' => 'Overlay',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#sub-tab-overlay'
								)
							),
							'false' => array(
								'hide' => array(
									'#sub-tab-overlay'
								)
							)
						),
						'tooltip' => 'Set whether the hover overlay on images should be enabled.'
					),
					'enable-lightbox' => array(
						'type' => 'checkbox',
						'name' => 'enable-lightbox',
						'label' => 'Lightbox',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#sub-tab-lightbox'
								)
							),
							'false' => array(
								'hide' => array(
									'#sub-tab-lightbox'
								)
							)
						),
						'tooltip' => 'Enable the full size lightbox preview for images in your Gallery.'
					),
					'enable-filters' => array(
						'type' => 'checkbox',
						'name' => 'enable-filters',
						'label' => 'Filters',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#sub-tab-filters'
								)
							),
							'false' => array(
								'hide' => array(
									'#sub-tab-filters'
								)
							)
						),
						'tooltip' => 'Set whether the Gallery filters should be applied or not.'
					),
					'enable-ordering' => array(
						'type' => 'checkbox',
						'name' => 'enable-ordering',
						'label' => 'Ordering',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#sub-tab-ordering'
								)
							),
							'false' => array(
								'hide' => array(
									'#sub-tab-ordering'
								)
							)
						),
						'tooltip' => 'Set whether the Gallery odering should be applied or not.'
					),
					'enable-links' => array(
						'type' => 'checkbox',
						'name' => 'enable-links',
						'label' => 'Links',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#sub-tab-links'
								)
							),
							'false' => array(
								'hide' => array(
									'#sub-tab-links'
								)
							)
						),
						'tooltip' => 'Set whether the Gallery links should be enabled.'
					),
			'close-wrap-enable-overlay' => self::wrapper('enable-overlay'),	
				
			'open-wrap-nbr-images' => self::wrapper('nbr-images', true),
				'heading-limit-images' => self::header('heading-limit-images', 'Limit'),
					'enable-limit-images' => array(
						'type' => 'checkbox',
						'name' => 'enable-limit-images',
						'label' => 'Limit Images',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-nbr-images'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-nbr-images'
								)
							)
						),
						'tooltip' => 'Set whether you want to limit the images displayed in your Gallery.'
					),
					'nbr-images' => array(
						'type' => 'integer',
						'name' => 'nbr-images',
						'label' => 'Number Of Images',
						'default' => 10,
						'tooltip' => 'Set the maximum number of images to display.'
					),
			'close-wrap-nbr-images' => self::wrapper('nbr-images')
		);
		
		
		return $settings;
	
	}
	
	
	public static function media($block) {
	
				
		$settings = array(
			'open-wrap-img-nav' => self::wrapper('img-nav', true),
				'heading-img-nav' => self::header('heading-img-nav', 'Image Navigation'),
					'img-nav' => array(
						'type' => 'checkbox',
						'name' => 'img-nav',
						'label' => 'Next &amp; Previous Navigation',
						'default' => true,
						'tooltip' => 'Set whether the next &amp; previous navigation should be displayed.'
					),
					'img-nav-previous-text' => array(
						'type' => 'text',
						'name' => 'img-nav-previous-text',
						'label' => 'Previous Button Text',
						'default' => 'Previous',
						'tooltip' => 'Set the previous button text.'
					),
					'img-nav-next-text' => array(
						'type' => 'text',
						'name' => 'img-nav-next-text',
						'label' => 'Next Button Text',
						'default' => 'Next',
						'tooltip' => 'Set the next button text.'
					),
			'close-wrap-img-nav' => self::wrapper('img-nav')	
		);
		
		
		return $settings;
	
	}
	
	
	public static function grid($block) {
				
		$settings = array(
			'open-wrap-grid-col' => self::wrapper('grid-col', true),
				'heading-grid-col' => self::header('heading-grid-col', 'Grid'),
					'grid-col' => array(
						'type' => 'select',
						'name' => 'grid-col',
						'label' => 'Columns',
						'default' => 3,
						'options' => array(
							'1' => '1',
							'2' => '2',
							'3' => '3',
							'4' => '4',
							'5' => '5',
							'6' => '6',
							'7' => '7',
							'8' => '8'
						),
						'tooltip' => 'Set the number of columns the Grid should contain.'
					),
					'grid-col-spacing' => array(
						'type' => 'slider',
						'name' => 'grid-col-spacing',
						'label' => 'Columns Spacing',
						'default' => 3,
						'slider-min' => 0,
						'slider-max' => 25,
						'slider-interval' => 1,
						'unit' => '%',
						'tooltip' => 'Set the amount of spacing between your Gallery columns.'
					),
					'grid-row-spacing' => array(
						'type' => 'slider',
						'name' => 'grid-row-spacing',
						'label' => 'Rows Spacing',
						'default' => 10,
						'slider-min' => 0,
						'slider-max' => 500,
						'slider-interval' => 1,
						'unit' => 'px',
						'tooltip' => "Set the rows spacing in pixels."
					),
			'close-wrap-grid-col' => self::wrapper('grid-col'),
			
			'open-wrap-img-enable-crop-height' => self::wrapper('img-enable-crop-height', true),
				'heading-img-enable-crop-height' => self::header('heading-img-enable-crop-height', 'Resize'),
					'img-enable-crop-height' => array(
						'type' => 'checkbox',
						'name' => 'img-enable-crop-height',
						'label' => 'Crop Image Vertically',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-img-crop-method'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-img-crop-method',
									'#input-img-crop-height'
								)
							)
						),
						'tooltip' => 'Set whether the image should be vertically cropped.'
					),
					'img-crop-method' => array(
						'type' => 'select',
						'name' => 'img-crop-method',
						'label' => 'Image Height Method',
						'default' => 'auto-thumb',
						'options' => array(
							'auto-thumb' => 'Auto Thumbnail',
							'auto-crop' => 'Auto Crop',
							'crop' => 'Manual Crop'
						),
						'toggle' => array(
							'auto-thumb' => array(
								'hide' => array(
									'#input-img-crop-height'
								)
							),
							'auto-crop' => array(
								'hide' => array(
									'#input-img-crop-height'
								)
							),
							'crop' => array(
								'show' => array(
									'#input-img-crop-height'
								)
							)
						),
						'tooltip' => 'The auto thumbnail will resize the width and height based on the available space in the grid column. The auto crop will get the height of the smallest image and use that as the height for all images in the album. The manual crop method allows you to specify a height to crop the images by.'
					),
					'img-crop-height' => array(
						'type' => 'slider',
						'name' => 'img-crop-height',
						'label' => 'Image Height',
						'default' => 200,
						'slider-min' => 50,
						'slider-max' => 1500,
						'slider-interval' => 1,
						'unit' => 'px',
						'tooltip' => 'Set the height of your image.'
					),
					'img-enable-crop-width' => array(
						'type' => 'checkbox',
						'name' => 'img-enable-crop-width',
						'label' => 'Crop Image Horizontally',
						'default' => true,
						'tooltip' => 'Set whether the image should be horizontally cropped.'
					),
			'close-wrap-img-enable-crop-height' => self::wrapper('img-enable-crop-height')
		);
		
		
		return $settings;
	
	}
	
	
	public static function slider($block) {
			
		$settings = array(
			'open-wrap-slider-nav' => self::wrapper('slider-nav', true),
				'heading-slider-nav' => self::header('heading-slider-nav', 'Main Slider'),
					'slider-nav' => array(
						'type' => 'checkbox',
						'name' => 'slider-nav',
						'label' => 'Next &amp; Previous Navigation',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-slider-nav-hover'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-slider-nav-hover'
								)
							)
						),
						'tooltip' => 'Set whether you would like to display the slider next/previous navigation.'
					),
					'slider-nav-hover' => array(
						'type' => 'checkbox',
						'name' => 'slider-nav-hover',
						'label' => 'Show Only On Hover',
						'default' => false,
						'tooltip' => 'Set whether to only show the navigation when you move your mouse over the main image.'
					),
					'slider-enable-loop' => array(
						'type' => 'checkbox',
						'name' => 'slider-enable-loop',
						'label' => 'Loop',
						'default' => true,
						'tooltip' => 'Should the Slider be set to a continuous loop?'
					),
					'slider-enable-slideshow' => array(
						'type' => 'checkbox',
						'name' => 'slider-enable-slideshow',
						'label' => 'Slideshow',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-slider-slideshow-speed'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-slider-slideshow-speed'
								)
							)
						),
						'tooltip' => 'Set whether you want the Slider to act like a slideshow and play automatically.'
					),
					'slider-slideshow-speed' => array(
						'type' => 'slider',
						'name' => 'slider-slideshow-speed',
						'label' => 'Slideshow Speed',
						'default' => 7000,
						'slider-min' => 2000,
						'slider-max' => 20000,
						'slider-interval' => 500,
						'unit' => 'ms',
						'tooltip' => 'Set the speed for the pause between slides.'
					),
			'close-wrap-slider-nav' => self::wrapper('slider-nav'),
			
			'open-wrap-slider-height' => self::wrapper('slider-height', true),
				'heading-slider-height' => self::header('heading-slider-height', 'Resize'),
					'slider-height' => array(
						'type' => 'select',
						'name' => 'slider-height',
						'label' => 'Slider Height Method',
						'default' => 'auto',
						'options' => array(
							'auto' => 'Auto Crop',
							'crop' => 'Manual Crop',
							'animate' => 'Animate',
						),
						'toggle' => array(
							'auto' => array(
								'hide' => array(
									'#input-slider-crop-height'
								)
							),
							'crop' => array(
								'show' => array(
									'#input-slider-crop-height'
								)
							),
							'animate' => array(
								'hide' => array(
									'#input-slider-crop-height'
								)
							)
						),
						'tooltip' => 'Set whether to automatically crop the image, based on the height of the shortest image in the album or manually crop, where you set a specific height to crop to; or animate between the various image heights.'
					),
					'slider-crop-height' => array(
						'type' => 'slider',
						'name' => 'slider-crop-height',
						'label' => 'Slider Height',
						'default' => 200,
						'slider-min' => 50,
						'slider-max' => 1500,
						'slider-interval' => 1,
						'unit' => 'px',
						'tooltip' => 'Set the height of your slider.'
					),
			'close-wrap-img-enable-crop-height' => self::wrapper('img-enable-crop-height'),
			
			'open-wrap-slider-effect' => self::wrapper('slider-effect', true),
				'heading-slider-effect' => self::header('heading-slider-effect', 'Effects'),
					'slider-effect' => array(
						'type' => 'select',
						'name' => 'slider-effect',
						'label' => 'Effect',
						'default' => 'fade',
						'options' => array(
							'fade' => 'Fade',
							'slide' => 'Slide'
						),
						'toggle' => array(
							'fade' => array(
								'hide' => array(
									'#input-slider-direction'
								)
							),
							'slide' => array(
								'show' => array(
									'#input-slider-direction'
								)
							)
						),
						'tooltip' => 'Set the effect to use for your album slider.'
					),
					'slider-direction' => array(
						'type' => 'select',
						'name' => 'slider-direction',
						'label' => 'Direction',
						'default' => 'horizontal',
						'options' => array(
							'horizontal' => 'Horizontal',
							'vertical' => 'Vertical'
						),
						'tooltip' => 'Set the direction for your album slider.'
					),
					'slider-speed' => array(
						'type' => 'slider',
						'name' => 'slider-speed',
						'label' => 'Slider Speed',
						'default' => 300,
						'slider-min' => 150,
						'slider-max' => 5000,
						'slider-interval' => 50,
						'unit' => 'ms',
						'tooltip' => 'Set the speed of your album slider.'
					),
					'slider-easing' => array(
						'type' => 'select',
						'name' => 'slider-easing',
						'label' => 'Easing',
						'default' => 'swing',
						'options' => self::easing_effect($block),
						'tooltip' => 'Set whether the album slider should use the easing effect.'
					),
			'close-wrap-slider-effect' => self::wrapper('slider-effect'),
			
			'open-wrap-slider-pager' => self::wrapper('slider-pager', true),
				'heading-slider-pager' => self::header('heading-slider-pager', 'Pagination'),
					'slider-pager' => array(
						'type' => 'checkbox',
						'name' => 'slider-pager',
						'label' => 'Pagination',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-slider-enable-pager-thumbs'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-slider-enable-pager-thumbs'
								)
							)
						),
						'tooltip' => 'Set whether the slider pagination should be enabled.'
					),
					'slider-enable-pager-thumbs' => array(
						'type' => 'checkbox',
						'name' => 'slider-enable-pager-thumbs',
						'label' => 'Use Thumbnails',
						'default' => false,
						'toggle' => array(
							'false' => array(
								'hide' => array(
									'.wrapper.slider-pager-show-all'
								)
							),
							'true' => array(
								'show' => array(
									'.wrapper.slider-pager-show-all',
									'#input-slider-pager-show-all' /* used to fire that input toggle */
								)
							)
						),
						'tooltip' => 'Set whether to use thumbnails for the slider pagination.'
					),
			'close-wrap-slider-pager' => self::wrapper('slider-pager'),
			
			'open-wrap-slider-pager-show-all' => self::wrapper('slider-pager-show-all', true),
				'heading-slider-pager-show-all' => self::header('heading-slider-pager-show-all', 'Pagination Thumbnails'),
					'slider-pager-show-all' => array(
						'type' => 'checkbox',
						'name' => 'slider-pager-show-all',
						'label' => 'Show All Thumbnails ',
						'default' => false,
						'toggle' => array(
							'false' => array(
								'show' => array(
									'#input-slider-thumb-count',
									'#input-slider-pager-nav',
									'#input-slider-pager-nav-hover'
								)
							),
							'true' => array(
								'hide' => array(
									'#input-slider-thumb-count',
									'#input-slider-pager-nav',
									'#input-slider-pager-nav-hover'
								)
							)
						),
						'tooltip' => 'Set whether all pager thumbnails should be displayed. If yes, the carousel navigation will automatically be enabled, allowing you to scroll horizontally between the thumbnails. If disabled, only the number of thumbnails you specify will be displayed.'
					),
					'slider-thumb-count' => array(
						'type' => 'slider',
						'name' => 'slider-thumb-count',
						'label' => 'Number Of Thumbnails',
						'default' => 4,
						'slider-min' => 2,
						'slider-max' => 10,
						'slider-interval' => 1,
						'tooltip' => 'Set the number of thumbnails to use for your slider pagination.'
					),
					'slider-pager-spacing' => array(
						'type' => 'slider',
						'name' => 'slider-pager-spacing',
						'label' => 'Container Spacing',
						'default' => 0,
						'slider-min' => 0,
						'slider-max' => 500,
						'slider-interval' => 2,
						'unit' => 'px',
						'tooltip' => 'Set the left and right spacing of the slider thumbnail container.'
					),
					'slider-thumb-spacing' => array(
						'type' => 'slider',
						'name' => 'slider-thumb-spacing',
						'label' => 'Thumbnails Spacing',
						'default' => 6,
						'slider-min' => 0,
						'slider-max' => 100,
						'slider-interval' => 2,
						'unit' => 'px',
						'tooltip' => 'Set the amount of spacing between the slider pagination thumbnails.'
					),
					'slider-enable-thumb-crop-height' => array(
						'type' => 'checkbox',
						'name' => 'slider-enable-thumb-crop-height',
						'label' => 'Crop Thumbnail Vertically',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-slider-thumb-crop-height'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-slider-thumb-crop-height'
								)
							)
						),
						'tooltip' => ''
					),
					'slider-thumb-crop-height' => array(
						'type' => 'slider',
						'name' => 'slider-thumb-crop-height',
						'label' => 'Thumbnail Height',
						'default' => 50,
						'slider-min' => 5,
						'slider-max' => 500,
						'slider-interval' => 1,
						'unit' => 'px',
						'tooltip' => ''
					),
					'slider-pager-nav' => array(
						'type' => 'checkbox',
						'name' => 'slider-pager-nav',
						'label' => 'Next &amp; Previous Navigation',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-slider-pager-nav-hover'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-slider-pager-nav-hover'
								)
							)
						),
						'tooltip' => 'Set whether you would like to display the next/previous navigation.'
					),
					'slider-pager-nav-hover' => array(
						'type' => 'checkbox',
						'name' => 'slider-pager-nav-hover',
						'label' => 'Show Only On Hover ',
						'default' => false,
						'tooltip' => ''
					),
				'close-wrap-slider-thumb-count' => self::wrapper('slider-thumb-count')
		);
		
		
		return $settings;
	
	}
		
	
	public static function lightbox($block) {
				
		$settings = array(
			'open-wrap-lightbox-enable-loop' => self::wrapper('lightbox-enable-loop', true),
				'heading-lightbox-enable-loop' => self::header('heading-lightbox-enable-loop', 'Lightbox'),
					'lightbox-enable-loop' => array(
						'type' => 'checkbox',
						'name' => 'lightbox-enable-loop',
						'label' => 'Loop',
						'default' => true,
						'tooltip' => 'Set whether the enable the lightbox image loop. This means that your users will be able to navigation all the Gallery images without needing to open and close the lightbox full image view. '
					),
					'lightbox-show-title' => array(
						'type' => 'checkbox',
						'name' => 'lightbox-show-title',
						'label' => 'Title',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-lightbox-title-position'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-lightbox-title-position'
								)
							)
						),
						'tooltip' => 'Set whether the lightbox title should be displayed.'
					),
					'lightbox-title-position' => array(
						'type' => 'select',
						'name' => 'lightbox-title-position',
						'label' => 'Title Position',
						'default' => 'over',
						'options' => array(
							'over' => 'Over Image',
							'float' => 'Below Image'
						),
						'tooltip' => 'Set the position of the image title in the full size lightbox preview.'
					),
			'close-wrap-lightbox-enable-loop' => self::wrapper('lightbox-enable-loop'),
			
			'open-wrap-lightbox-enable-resize' => self::wrapper('lightbox-enable-resize', true),
				'heading-lightbox-enable-resize' => self::header('heading-lightbox-enable-resize', 'Resize'),
					'lightbox-enable-resize' => array(
						'type' => 'checkbox',
						'name' => 'lightbox-enable-resize',
						'label' => 'Resize Lightbox',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-lightbox-height',
									'#input-lightbox-width'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-lightbox-height',
									'#input-lightbox-width'
								)
							)
						),
						'tooltip' => 'Set the light box dimensions. If set to false, the lightbox size will be calculated automatically.'
					),
					'lightbox-height' => array(
						'type' => 'slider',
						'name' => 'lightbox-height',
						'label' => 'Height',
						'default' => 1000,
						'slider-min' => 100,
						'slider-max' => 2000,
						'slider-interval' => 10,
						'unit' => 'px',
						'tooltip' => 'Set the height of your lightbox.'
					),
					'lightbox-width' => array(
						'type' => 'slider',
						'name' => 'lightbox-width',
						'label' => 'Width',
						'default' => 1000,
						'slider-min' => 100,
						'slider-max' => 2000,
						'slider-interval' => 10,
						'unit' => 'px',
						'tooltip' => 'Set the width of your lightbox.'
					),
			'close-wrap-img-enable-crop-height' => self::wrapper('img-enable-crop-height'),
			
			'open-wrap-lightbox-open-effect' => self::wrapper('lightbox-open-effect', true),
				'heading-lightbox-open-effect' => self::header('heading-lightbox-open-effect', 'Effects'),
					'lightbox-open-effect' => array(
						'type' => 'select',
						'name' => 'lightbox-open-effect',
						'label' => 'Open Effect',
						'default' => 'elastic',
						'options' => array(
							'fade' => 'Fade',
							'elastic' => 'Elastic',
							'none' => 'None'
						),
						'tooltip' => ''
					),
					'lightbox-close-effect' => array(
						'type' => 'select',
						'name' => 'lightbox-close-effect',
						'label' => 'Close Effect',
						'default' => 'elastic',
						'options' => array(
							'fade' => 'Fade',
							'elastic' => 'Elastic',
							'none' => 'None'
						),
						'tooltip' => 'Set the effect to use for the lightbox closing.'
					),
					'lightbox-easingin' => array(
						'type' => 'select',
						'name' => 'lightbox-easingin',
						'label' => 'Easing In',
						'default' => 'swing',
						'options' => self::easing_effect($block),
						'tooltip' => 'Set whether the easing-in effect be enabled for the lightbox.'
					),
					'lightbox-easingout' => array(
						'type' => 'select',
						'name' => 'lightbox-easingout',
						'label' => 'Easing Out',
						'default' => 'swing',
						'options' => self::easing_effect($block),
						'tooltip' => 'Set whether the easing-out effect be enabled for the lightbox.'
					),
			'close-wrap-lightbox-title-position' => self::wrapper('lightbox-title-position')
		);
		
		return $settings;
	
	}
	
	
	public static function overlay($block) {	
		
		$settings = array(
			'open-wrap-overlay-content' => self::wrapper('overlay-content', true),
				'heading-overlay-content' => self::header('heading-overlay-content', 'Overlay'),
					'overlay-content' => array(
						'type' => 'multi-select',
						'name' => 'overlay-content',
						'label' => 'Overlay Content',
						'default' => array('image'),
						'options' => array(
							'title'	=> 'Title',
							'caption' => 'Caption',
							'image'	=> 'Image'
						),
						'tooltip' => 'Set whether to display the thumnail title or caption in the hover overlay. The image is set in Mode->Caption Container->Background->Image'
					),
			'close-wrap-overlay-content' => self::wrapper('overlay-content'),
			
			'open-wrap-overlay-effect' => self::wrapper('overlay-effect', true),
				'heading-overlay-effect' => self::header('heading-overlay-effect', 'Effects'),
					'overlay-effect' => array(
						'type' => 'select',
						'name' => 'overlay-effect',
						'label' => 'Effect',
						'default' => 'bottom',
						'options' => array(
							'fade' => 'Fade',
							'top' => 'Slide From Top',
							'right' => 'Slide From Right',
							'bottom' => 'Slide From Bottom',
							'left' => 'Slide From Left'
						),
						'tooltip' => 'Set the overlay effect for your album images.'
					),
					'overlay-speed' => array(
						'type' => 'slider',
						'name' => 'overlay-speed',
						'label' => 'Speed',
						'default' => 300,
						'slider-min' => 150,
						'slider-max' => 2000,
						'slider-interval' => 1,
						'unit' => 'ms',
						'tooltip' => 'Set the speed at which the hover overlay is displayed.'
					),
					'overlay-easing' => array(
						'type' => 'select',
						'name' => 'overlay-easing',
						'label' => 'Easing',
						'default' => 'easeOutQuad',
						'options' => self::easing_effect($block),
						'tooltip' => 'Should the easing effect be enabled when hovering over individual image?'
					),
					'overlay-invert' => array(
						'type' => 'checkbox',
						'name' => 'overlay-invert',
						'label' => 'Invert Effect',
						'default' => false,
						'tooltip' => 'Set whether to invert the overlay when moving your mouse over one of the album image.'
					),
			'close-wrap-overlay-effect' => self::wrapper('overlay-effect')
		);
		
		
		return $settings;
	
	}
	
	
	public static function links($block) {
	
		$settings = array(
			'open-wrap-readon-text' => self::wrapper('readon-text', true),
				'heading-readon-text' => self::header('heading-readon-text', 'Links'),
					'readon-text' => array(
						'type' => 'text',
						'name' => 'readon-text',
						'label' => 'Readon Text',
						'default' => 'View Album…',
						'tooltip' => 'Set the readon text for your Gallery.'
					),
					'enable-title-link' => array(
						'type' => 'checkbox',
						'name' => 'enable-title-link',
						'label' => 'Link Title',
						'default' => false,
						'tooltip' => 'Set whether the Gallery title should be linked?'
					),
					'enable-image-link' => array(
						'type' => 'checkbox',
						'name' => 'enable-image-link',
						'label' => 'Link Image',
						'default' => true,
						'tooltip' => 'Set whether the images should be linked.'
					),
					'link-target' => array(
						'type' => 'checkbox',
						'name' => 'link-target',
						'label' => 'Open In New Window',
						'default' => false,
						'tooltip' => 'Set whether the read on link should open in a new window.'
					),
					'link-behaviour' => array(
						'type' => 'multi-select',
						'name' => 'link-behaviour',
						'label' => 'Link Behaviour',
						'default' => array('auto', 'custom'),
						'options' => array(
							'auto' => 'Link To Item',
							'custom' => 'Custom Link'
						),
						'tooltip' => 'If Link To Item is selected, it will automatically link to the album item from albums view or link to the image from album view. If Custom Link is selected, it will use the custom link set in the Wordpress ablum or image if not left empty. If both options are selected, it will use the custom link if set or fallback on the item link if custom link is left empty.'
					),
			'close-wrap-readon-text' => self::wrapper('readon-text'),
			
			'open-wrap-link-show-title-tag' => self::wrapper('link-show-title-tag', true),
				'heading-link-show-title-tag' => self::header('heading-link-show-title-tag', 'HTML Tags'),
					'link-show-title-tag' => array(
						'type' => 'checkbox',
						'name' => 'link-show-title-tag',
						'label' => 'Title Tag',
						'default' => true,
						'tooltip' => 'Set whether the link title tag should be added.'
					),
			'close-wrap-link-show-title-tag' => self::wrapper('link-show-title-tag')
		);
		
		
		return $settings;
	
	}
	
	
	public static function filters($block) {
	
		/* we build the category option for the multiselect */		
		$category_options 	     = array();
		$categories_select_query = get_terms('gallery_categories');
		
		foreach ( $categories_select_query as $category )
			$category_options[$category->term_id] = $category->name;
		
		/* we build the tag option for the multiselect */		
		$tag_options	  = array();
		$tag_select_query = get_terms('gallery_tags');
		
		foreach ( $tag_select_query as $tag )
			$tag_options[$tag->term_id] = $tag->name;
			
		/* we build the item option for the multiselect */		
		$item_options = array();
		
		$args = array(
		    'posts_per_page' => -1,
		    'post_type' => 'padma_gallery',
		    'post_status' => 'publish',
		    'suppress_filters' => true );
		    
		$item_select_query = get_posts( $args );
		
		foreach ( $item_select_query as $item )
			$item_options[$item->ID] = $item->post_title;
		
		$settings = array(
			'open-wrap-limit-albums' => self::wrapper('limit-album', true),
				'heading-limit-albums' => self::header('heading-limit-albums', 'Limit'),
					'enable-limit-albums' => array(
						'type' => 'checkbox',
						'name' => 'enable-limit-albums',
						'label' => 'Limit Albums',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-nbr-albums'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-nbr-albums'
								)
							)
						),
						'tooltip' => 'Set whether you want to limit the number of albums albums displayed.'
					),
					'nbr-albums' => array(
						'type' => 'integer',
						'name' => 'nbr-albums',
						'label' => 'Number Of Albums',
						'default' => 10,
						'tooltip' => 'Set the maximum number of albums to display.'
					),
			'close-wrap-nbr-images' => self::wrapper('nbr-images'),
			
			'open-wrap-filters-enable-categories' => self::wrapper('filters-enable-categories', true),
				'heading-filters-enable-categories' => self::header('heading-filters-enable-categories', 'Filters'),
					'filters-enable-categories' => array(
						'type' => 'checkbox',
						'name' => 'filters-enable-categories',
						'label' => 'By Category',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-filters-categories',
									'#input-filters-categories-mode'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-filters-categories',
									'#input-filters-categories-mode'
								)
							)
						),
						'tooltip' => 'Set whether the filter category should be enabled.'
					),
					'filters-categories' => array(
						'type' => 'multi-select',
						'name' => 'filters-categories',
						'label' => 'Categories',
						'default' => null,
						'options' => $category_options,
						'tooltip' => 'Set which categories you would like to filter.'
					),
					'filters-categories-mode' => array(
						'type' => 'select',
						'name' => 'filters-categories-mode',
						'label' => 'Categories Mode',
						'default' => 'include',
						'options' => array(
							'include' => 'Include',
							'exclude' => 'Exclude'
						),
						'tooltip' => 'Set whether you would like to include or exclude the selected categories.'
					),
					'filters-enable-tags' => array(
						'type' => 'checkbox',
						'name' => 'filters-enable-tags',
						'label' => 'By Tags',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-filters-tags',
									'#input-filters-tags-mode'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-filters-tags',
									'#input-filters-tags-mode'
								)
							)
						),
						'tooltip' => 'Set whether the filter tags should be enabled.'
					),
					'filters-tags' => array(
						'type' => 'multi-select',
						'name' => 'filters-tags',
						'label' => 'Tags',
						'default' => null,
						'options' => $tag_options,
						'tooltip' => 'Set which tags you would like to filter.'
					),
					'filters-tags-mode' => array(
						'type' => 'select',
						'name' => 'filters-tags-mode',
						'label' => 'Tags Mode',
						'default' => 'include',
						'options' => array(
							'include' => 'Include',
							'exclude' => 'Exclude'
						),
						'tooltip' => 'Set whether you would like to include or exclude the selected tags.'
					),
					'filters-enable-wp-items' => array(
						'type' => 'checkbox',
						'name' => 'filters-enable-wp-items',
						'label' => 'By Albums',
						'default' => false,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-filters-wp-items',
									'#input-filters-wp-items-mode'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-filters-wp-items',
									'#input-filters-wp-items-mode'
								)
							)
						),
						'tooltip' => 'Set whether the filter album should be enabled.'
					),
					'filters-wp-items' => array(
						'type' => 'multi-select',
						'name' => 'filters-wp-items',
						'label' => 'Albums',
						'default' => null,
						'options' => $item_options,
						'tooltip' => 'Set which album you would like to filter.'
					),
					'filters-wp-items-mode' => array(
						'type' => 'select',
						'name' => 'filters-wp-items-mode',
						'label' => 'Albums Mode',
						'default' => 'include',
						'options' => array(
							'include' => 'Include',
							'exclude' => 'Exclude'
						),
						'tooltip' => 'Set whether you would like to include or exclude the selected albums.'
					),
			'close-wrap-enable-filters' => self::wrapper('enable-filters')
		);
		
		
		return $settings;
	
	
	}
	
	public static function ordering($block) {
	
		$settings = array(
			'open-wrap-heading-order-by' => self::wrapper('heading-order-by', true),
				'heading-order-by' => self::header('heading-order-by', 'Ordering Albums'),
					'order-by' => array(
						'type' => 'select',
						'name' => 'order-by',
						'label' => 'Order By',
						'default' => 'id',
						'options' => array(
							'date' => 'Date',
							'title' => 'Title',
							'id' => 'ID',
							'rand' => 'Random'
						),
						'tooltip' => 'Set your albums ordering.'
					),
					'order' => array(
						'type' => 'select',
						'name' => 'order',
						'label' => 'Order',
						'default' => 'desc',
						'options' => array(
							'desc' => 'Descending',
							'asc' => 'Ascending',
						),
						'tooltip' => 'Set whether the ordering should be Descending or Ascending.'
					),
			'close-wrap-heading-order-by' => self::wrapper('heading-order-by'),
			
			'open-wrap-heading-order-images-by' => self::wrapper('heading-order-images-by', true),
				'heading-order-images-by' => self::header('heading-order-images-by', 'Ordering Images'),
					'order-images-by' => array(
						'type' => 'select',
						'name' => 'order-images-by',
						'label' => 'Order By',
						'default' => 'entry',
						'options' => array(
							'entry' => 'Manual',
							'title' => 'Title',
							'id' => 'ID',
							'rand' => 'Random'
						),
						'tooltip' => 'Set your images ordering. If ordering is set to Manual, images will be displayed in the order you have them set in your album.'
					),
					'order-images' => array(
						'type' => 'select',
						'name' => 'order-images',
						'label' => 'Order',
						'default' => 'desc',
						'options' => array(
							'desc' => 'Descending',
							'asc' => 'Ascending',
						),
						'tooltip' => 'Set whether the ordering should be Descending or Ascending.'
					),
			'close-wrap-heading-order-images-by' => self::wrapper('heading-order-images-by')
		);
		
		
		return $settings;
	
	}
	
	
	public static function content($block) {
	
		$wysiwyg = version_compare('3.2.5', PADMA_VERSION, '>') ? 'textarea' : 'wysiwyg';

		$settings = array(
			'open-wrap-block-before' => self::wrapper('block-before', true),
				'heading-block-before' => self::header('heading-block-before', 'Block Content'),
					'block-before' => array(
						'type' => $wysiwyg,
						'name' => 'block-before',
						'label' => 'Before Block',
						'default' => '',
						'tooltip' => 'Add your own custom html here and it will be outputted before the block content.'
					),
					'block-title' => array(
						'type' => 'text',
						'name' => 'block-title',
						'label' => 'Block Title',
						'default' => '',
						'tooltip' => 'Add a name for this gallery block.'
					),
					'block-title-type' => array(
						'type' => 'select',
						'name' => 'block-title-type',
						'label' => 'Block Title Type',
						'default' => 'h1',
						'options' => array(
							'h1' => 'h1',
							'h2' => 'h2',
							'h3' => 'h3',
							'h4' => 'h4',
							'h5' => 'h5'
						),
						'tooltip' => 'Choose which level of Heading should be used for the block name.'
					),
					'block-content' => array(
						'type' => $wysiwyg,
						'name' => 'block-content',
						'label' => 'Block Description',
						'default' => '',
						'tooltip' => 'Add a description for this gallery block.'
					),
					'block-footer' => array(
						'type' => $wysiwyg,
						'name' => 'block-footer',
						'label' => 'Block Footer',
						'default' => '',
						'tooltip' => 'Add a footer for this gallery block.'
					),
					'block-after' => array(
						'type' => $wysiwyg,
						'name' => 'block-after',
						'label' => 'After Block',
						'default' => '',
						'tooltip' => 'Add your own custom html here and it will be outputted after the block content.'
					),
			'close-wrap-block-before' => self::wrapper('block-before'),
			
			'open-wrap-album-show-title' => self::wrapper('album-show-title', true),
				'heading-album-show-title' => self::header('heading-album-show-title', 'Album Content'),
					'album-show-title' => array(
						'type' => 'checkbox',
						'name' => 'album-show-title',
						'label' => 'Album Title',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-album-title-type'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-album-title-type'
								)
							)
						),
						'tooltip' => 'Set a title for your Gallery.'
					),
					'album-title-type' => array(
						'type' => 'select',
						'name' => 'album-title-type',
						'label' => 'Title Markup',
						'default' => 'h2',
						'options' => array(
							'h1' => 'h1',
							'h2' => 'h2',
							'h3' => 'h3',
							'h4' => 'h4',
							'h5' => 'h5'
						),
						'tooltip' => 'Choose which heading type should be used for the Gallery title.'
					),
					'album-show-description' => array(
						'type' => 'checkbox',
						'name' => 'album-show-description',
						'label' => 'Album Description',
						'default' => true,
						'tooltip' => 'Set whether the album description should be displayed.'
					),
			'close-wrap-album-show-title' => self::wrapper('album-show-title'),
			
			'open-wrap-img-show-title' => self::wrapper('img-show-title', true),
				'heading-img-show-title' => self::header('heading-img-show-title', 'Image Content'),
					'img-show-title' => array(
						'type' => 'checkbox',
						'name' => 'img-show-title',
						'label' => 'Image Title',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-img-title-type',
									'#input-img-title-position'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-img-title-type',
									'#input-img-title-position'
								)
							)
						),
						'tooltip' => 'Set whether the image title should be displayed.'
					),
					'img-title-type' => array(
						'type' => 'select',
						'name' => 'img-title-type',
						'label' => 'Title Markup',
						'default' => 'h3',
						'options' => array(
							'h1' => 'h1',
							'h2' => 'h2',
							'h3' => 'h3',
							'h4' => 'h4',
							'h5' => 'h5'
						),
						'tooltip' => 'Choose which heading type should be used for the image title.'
					),
					'img-title-position' => array(
						'type' => 'select',
						'name' => 'img-title-position',
						'label' => 'Title Position',
						'default' => 'below-image',
						'options' => array(
							'above-image' => 'Above Image',
							'below-image' => 'Below Image'
						),
						'tooltip' => 'Set the position of the image title.'
					),
					'img-title-prettifier' => array(
						'type' => 'checkbox',
						'name' => 'img-title-prettifier',
						'label' => 'Prettify Title',
						'default' => true,
						'tooltip' => 'Set whether you would like to prettify the image title. If enabled, the words first letters will be capitalized and the dashes and dots will be replaced by a space.'
					),
					'img-show-description' => array(
						'type' => 'checkbox',
						'name' => 'img-show-description',
						'label' => 'Image Description',
						'default' => false,
						'tooltip' => 'Set whether the image description should be displayed.'
					),
					'img-enable-title-count' => array(
						'type' => 'checkbox',
						'name' => 'img-enable-title-count',
						'label' => 'Enable Image Count',
						'default' => true,
						'toggle' => array(
							'true' => array(
								'show' => array(
									'#input-img-title-count-text'
								)
							),
							'false' => array(
								'hide' => array(
									'#input-img-title-count-text'
								)
							)
						),
						'tooltip' => 'Set whether the number of image should be displayed.'
					),
					'img-title-count-text' => array(
						'type' => 'text',
						'name' => 'img-title-count-text',
						'label' => 'Image Count Suffix',
						'default' => ' images',
						
						'tooltip' => 'Set the text which will be displayed after the image count.'
					),
			'close-wrap-img-show-title' => self::wrapper('img-show-title'),
			
			'open-wrap-img-show-title-tag' => self::wrapper('img-show-title-tag', true),
				'heading-img-show-title-tag' => self::header('heading-img-show-title-tag', 'HTML Tags'),
					'img-show-title-tag' => array(
						'type' => 'checkbox',
						'name' => 'img-show-title-tag',
						'label' => 'Image Title Tag',
						'default' => true,
						'tooltip' => 'Set whether the image title tag should be added.'
					),
			'close-wrap-img-show-title-tag' => self::wrapper('img-show-title-tag')
		);
				
		return $settings;
	
	}
	

}