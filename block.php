<?php

class PadmaGalleryBlock extends PadmaBlockAPI {

	public $id 					 = 'padma-gallery';
	public $name 				 = 'Gallery';
	public $options_class 		 = 'PadmaGalleryBlockOptions';
	public $core_block 			 = false;
	static public $block 		 = null;
	public $show_content_in_grid = false;
	public $allow_titles 		= false;
	public $categories 		= array('media');


	function init() {

		//$this->do_maintenance();

		if ( version_compare(PADMA_VERSION, '1.0.0', '>=') ) {

			add_action('padma_db_upgrade', array($this, 'merge_default_design_data'));
			add_action('padma_visual_editor_save', array($this, 'merge_default_design_data'));

		} else {

			add_action('init', array($this, 'set_options_defaults'));

		}

	}


	public static function enqueue_action() {

		wp_enqueue_style('padma-gallery-css', plugins_url(basename(dirname(__FILE__))) . '/assets/css/gallery.css');
		wp_enqueue_script('padma-gallery-js', plugins_url(basename(dirname(__FILE__))) . '/assets/js/gallery.min.js', array('jquery'));

	}


	function setup_elements() {

		$elements = PadmaGalleryBlockStyling::hooks();

		foreach ( $elements as $element )
			$this->register_block_element($element);

	}


	function set_options_defaults() {

		global $padma_default_element_data;

		if(is_array($padma_default_element_data))
			$padma_default_element_data = array_merge($padma_default_element_data, PadmaGalleryBlockStyling::defaults());


	}


	function merge_default_design_data() {

		return PadmaElementsData::merge_default_design_data(PadmaGalleryBlockStyling::defaults(), 'padma-gallery');

	}


	function do_maintenance() {

		$maintenance = padma_block_maintenance( $this->id );

		$maintenance->merge_default_elements( PadmaGalleryBlockStyling::defaults() );

	}


	public static function dynamic_css($block_id, $block) {

		$set 			 = PadmaGalleryBlockOptions::settings($block);
		$display_gallery = self::get_gallery_display($block);
		$columns 		 = $display_gallery->set_columns();
		$this_block 	 = '.padma-gallery-' . $block_id;

		$col_width = (100 / $columns - $set['grid-col-spacing']) + ($set['grid-col-spacing'] / $columns);

		$pager_spacing = $set['slider-pager-spacing'] / 2;

		$css = '
			' . $this_block . ' .pur-grid .pur-row { margin-bottom: ' . $set['grid-row-spacing'] . 'px; }
			' . $this_block . ' .item { margin-right: ' . $set['grid-col-spacing'] . '%; width: ' . $col_width . '%; }
			' . $this_block . ' .pur-album .pager { margin-right: ' . $pager_spacing  . 'px; margin-left: ' . $pager_spacing  . 'px; }
			' . $this_block . ' .carousel-item .pager-item { margin-right: ' . $set['slider-thumb-spacing'] / 2  . 'px; margin-left: ' .$set['slider-thumb-spacing'] / 2  . 'px; }
			';

		if ( $set['slider-effect'] == 'slide' && $set['slider-direction'] == 'vertical' )
			$css .= '
				' . $this_block . ' .slider-item .image-wrap,
				' . $this_block . ' .slider-item .image-wrap a { float: none!important; }
				';

		if ( $set['slider-pager-show-all'] )
			$css .= $this_block . ' .thumbs-item.pager { padding-left: 0; padding-right:0 ; }';

		/* slider direction nav */
		if ( $set['slider-nav-hover'] ) {

			$css .='
				' . $this_block . ' .slider-item .pur-next,
				' . $this_block . ' .slider-item .pur-prev { opacity: 0; -webkit-transition: all .75s ease; -moz-transition: all .75s ease; transition: all .75s ease; }
				' . $this_block . ' .slider-item:hover .pur-next,
				' . $this_block . ' .slider-item:hover .pur-prev { opacity: 0.8; filter: alpha(opacity=80); -webkit-transition: all .75s ease; -moz-transition: all .75s ease; transition: all .75s ease; }
				' . $this_block . ' .slider-item:hover .pur-next:hover, .padma-gallery .flexslider:hover .pur-prev:hover { opacity: 1; }
				' . $this_block . ' .slider-item:hover .pur-disabled { opacity: .3!important; filter: alpha(opacity=30); cursor: default; }
				';

		} else {

			$css .='
				' . $this_block . ' .slider-item .pur-next,
				' . $this_block . ' .slider-item .pur-prev { opacity: 0.8; filter: alpha(opacity=80); }
				' . $this_block . ' .slider-item .pur-disabled { opacity: .3!important; filter: alpha(opacity=80); cursor: default; }
				';

		}

		/* pager direction nav */
		if ( $set['slider-pager-nav-hover'] ) {

			$css .='
				' . $this_block . ' .carousel-item .pur-next,
				' . $this_block . ' .carousel-item .pur-prev { opacity: 0; -webkit-transition: all .75s ease; -moz-transition: all .75s ease; transition: all .75s ease; }
				' . $this_block . ' .carousel-item:hover .pur-next,
				' . $this_block . ' .carousel-item:hover .pur-prev { opacity: 0.8; filter: alpha(opacity=80); -webkit-transition: all .75s ease; -moz-transition: all .75s ease; transition: all .75s ease; }
				' . $this_block . ' .carousel-item:hover .pur-next:hover, .padma-gallery .flexslider:hover .pur-prev:hover { opacity: 1; }
				' . $this_block . ' .carousel-item:hover .pur-disabled { opacity: .3!important; filter: alpha(opacity=30); cursor: default; }
				';

		} else {

			$css .='
				' . $this_block . ' .carousel-item .pur-next,
				' . $this_block . ' .carousel-item .pur-prev { opacity: 0.8; filter: alpha(opacity=80); }
				' . $this_block . ' .carousel-item .pur-disabled { opacity: .3!important; filter: alpha(opacity=80); cursor: default; }
				';

		}

		/* we add the backgrounds */
		$css .= '
			.pur-direction-nav a,
			.padma-gallery .nav-item li,
			#lightbox-left,
			#lightbox-right,
			#lightbox-close { background-image: url(' . plugins_url( "/assets/images/sprite.png", __FILE__ ) . '); }
			#lightbox-loading,
			.slider-loading { background-image: url("' . plugins_url( "/assets/images/loader.gif", __FILE__ ) . '"); }
			';

		/* responsive */
		if ( PadmaResponsiveGrid::is_active() ) {

			$css .= '';
		}

		/* we apply a fix for hw tooltip in the iframe */
		if ( $display_gallery->is_visual_editor() )
			$css .= '.double-indent { display: none!important; }';


		return $css;

	}


	public static function get_gallery_display($block) {

		return new PadmaGalleryBlockDisplay($block);

	}


	public static function dynamic_js($block_id) {

		$block = PadmaBlocksData::get_block($block_id);

		/* if legacy exist, use it */
		if ( $block_id = butler_get( 'legacy_id', $block ) )
			$block['id'] = $block_id;

		/* compile js files */
		$display_gallery = self::get_gallery_display($block);

		return $display_gallery->gallery_js();

    }


	function content($block) {

		self::$block 		 = $block;
		$set 			 	 = PadmaGalleryBlockOptions::settings($block);
		$display_gallery 	 = self::get_gallery_display($block);

		?>

		<div id="padma-gallery-<?php echo $block['id']; ?>" class="no-js padma-gallery padma-gallery-<?php echo $block['id']; ?> clearfix">

			<?php if ( strip_tags($set['block-before']) ) : ?>
				<div class="pur-block-before">
					<?php echo $set['block-before']; ?>
				</div>
			<?php endif; ?>

			<?php if ( $set['block-title'] != '' ) : ?>
				<<?php echo $set['block-title-type']; ?> class="pur-block-title"><?php echo $set['block-title']; ?></<?php echo $set['block-title-type']; ?>>
			<?php endif; ?>

			<?php if ( strip_tags($set['block-content']) ) : ?>
				<div class="pur-block-content">
					<?php echo $set['block-content']; ?>
				</div>
			<?php endif; ?>

			<?php echo $display_gallery->display_gallery() ; ?>

			<?php if ( strip_tags($set['block-footer']) ) : ?>
				<div class="pur-block-footer">
					<?php echo $set['block-footer']; ?>
				</div>
			<?php endif; ?>

			<?php if ( strip_tags($set['block-after']) ) : ?>
				<div class="pur-block-after">
					<?php echo $set['block-after']; ?>
				</div>
			<?php endif; ?>

		</div>

		<?php

	}

}